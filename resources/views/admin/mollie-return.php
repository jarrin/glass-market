<?php
session_start();

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);

// Database connection
$db_host = '127.0.0.1';
$db_name = 'glass_market';
$db_user = 'root';
$db_pass = '';

$status = 'processing';
$message = 'Processing your payment...';
$user_id = $_GET['user_id'] ?? 0;

// Log all GET parameters for debugging
error_log('Mollie Return - GET params: ' . print_r($_GET, true));

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Load composer autoload
    require_once __DIR__ . '/../../../vendor/autoload.php';
    require_once __DIR__ . '/../../../database/classes/mollie.php';

    $mollie = new MolliePayment();

    if (!$user_id) {
        throw new Exception('No user ID provided in return URL');
    }

    // Get the most recent pending payment for this user
    $stmt = $pdo->prepare("
        SELECT payment_id, months, amount, status
        FROM mollie_payments
        WHERE user_id = :user_id
        AND status = 'open'
        ORDER BY created_at DESC
        LIMIT 1
    ");
    $stmt->execute(['user_id' => $user_id]);
    $pendingPayment = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$pendingPayment) {
        // Try to find any recent payment (in case status was already updated)
        $stmt = $pdo->prepare("
            SELECT payment_id, months, amount, status
            FROM mollie_payments
            WHERE user_id = :user_id
            ORDER BY created_at DESC
            LIMIT 1
        ");
        $stmt->execute(['user_id' => $user_id]);
        $pendingPayment = $stmt->fetch(PDO::FETCH_ASSOC);
    }

    if ($pendingPayment) {
        $payment_id = $pendingPayment['payment_id'];
        error_log("Found payment ID: $payment_id for user: $user_id");

        $payment = $mollie->getPayment($payment_id);
        
        if ($payment) {
            if ($payment->isPaid()) {
                $status = 'success';
                $message = '✅ Payment successful! Your subscription has been activated.';
                
                // Update payment status in database
                $stmt = $pdo->prepare("UPDATE mollie_payments SET status = 'paid', paid_at = NOW() WHERE payment_id = :payment_id");
                $stmt->execute(['payment_id' => $payment_id]);
                
                // Extend user subscription
                $stmt = $pdo->prepare("SELECT months, user_id FROM mollie_payments WHERE payment_id = :payment_id");
                $stmt->execute(['payment_id' => $payment_id]);
                $paymentData = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($paymentData) {
                    $months = $paymentData['months'];
                    $userId = $paymentData['user_id'];
                    
                    // Check if subscription exists
                    $stmt = $pdo->prepare("SELECT end_date FROM user_subscriptions WHERE user_id = :user_id AND is_active = 1");
                    $stmt->execute(['user_id' => $userId]);
                    $subscription = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    if ($subscription && $subscription['end_date'] >= date('Y-m-d')) {
                        // Extend existing subscription
                        $newEndDate = date('Y-m-d', strtotime($subscription['end_date'] . " +{$months} months"));
                        $stmt = $pdo->prepare("UPDATE user_subscriptions SET end_date = :end_date, is_trial = 0 WHERE user_id = :user_id");
                        $stmt->execute(['end_date' => $newEndDate, 'user_id' => $userId]);
                    } else {
                        // Create new subscription or replace expired one
                        $stmt = $pdo->prepare("DELETE FROM user_subscriptions WHERE user_id = :user_id");
                        $stmt->execute(['user_id' => $userId]);
                        
                        $startDate = date('Y-m-d');
                        $endDate = date('Y-m-d', strtotime("+{$months} months"));
                        
                        $stmt = $pdo->prepare("
                            INSERT INTO user_subscriptions (user_id, start_date, end_date, is_trial, is_active)
                            VALUES (:user_id, :start_date, :end_date, 0, 1)
                        ");
                        $stmt->execute([
                            'user_id' => $userId,
                            'start_date' => $startDate,
                            'end_date' => $endDate
                        ]);

                        error_log("Created new subscription for user $userId: $startDate to $endDate");
                    }

                    // Log success
                    error_log("Payment $payment_id successfully processed for user $userId");
                }

            } elseif ($payment->isFailed()) {
                $status = 'failed';
                $message = '❌ Payment failed. Please try again.';
                error_log("Payment $payment_id failed for user $user_id");
            } elseif ($payment->isCanceled()) {
                $status = 'canceled';
                $message = '⚠️ Payment was canceled.';
                error_log("Payment $payment_id canceled by user $user_id");
            } elseif ($payment->isExpired()) {
                $status = 'expired';
                $message = '⏰ Payment expired. Please create a new payment.';
                error_log("Payment $payment_id expired for user $user_id");
            } else {
                // Payment is still open/pending
                $status = 'processing';
                $message = 'Payment is still being processed. Please wait...';
                error_log("Payment $payment_id still pending for user $user_id");
            }
        } else {
            $status = 'error';
            $message = 'Could not retrieve payment information from Mollie.';
            error_log("Could not get payment $payment_id from Mollie");
        }
    } else {
        $status = 'error';
        $message = 'No payment found for this user. Please try again.';
        error_log("No payment found for user $user_id");
    }

} catch (Exception $e) {
    $status = 'error';
    $message = 'An error occurred: ' . $e->getMessage();
    error_log('Mollie Return Error: ' . $e->getMessage());
    error_log('Stack trace: ' . $e->getTraceAsString());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Result - Glass Market</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Helvetica Neue', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            transition: background 0.6s ease;
        }

        /* Dynamic backgrounds based on status */
        body.success { background: linear-gradient(135deg, #d6f5e0 0%, #e6f9ed 100%); }
        body.failed { background: linear-gradient(135deg, #fee2e2 0%, #fef2f2 100%); }
        body.canceled { background: linear-gradient(135deg, #fff4d6 0%, #fffbeb 100%); }
        body.expired { background: linear-gradient(135deg, #fde2e4 0%, #fef3f4 100%); }
        body.processing { background: linear-gradient(135deg, #e8f4fd 0%, #f0f9ff 100%); }

        .card {
            background: white;
            padding: 56px 48px;
            border-radius: 32px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.12), 0 4px 16px rgba(0,0,0,0.08);
            text-align: center;
            max-width: 520px;
            width: 100%;
            animation: scaleIn 0.5s cubic-bezier(0.16, 1, 0.3, 1);
        }

        @keyframes scaleIn {
            from {
                opacity: 0;
                transform: scale(0.9) translateY(20px);
            }
            to {
                opacity: 1;
                transform: scale(1) translateY(0);
            }
        }

        .icon-container {
            width: 96px;
            height: 96px;
            margin: 0 auto 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            animation: bounceIn 0.6s cubic-bezier(0.16, 1, 0.3, 1) 0.2s backwards;
        }

        @keyframes bounceIn {
            from {
                opacity: 0;
                transform: scale(0.3);
            }
            50% {
                transform: scale(1.05);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        .icon-container.success { background: linear-gradient(135deg, #d6f5e0, #b8ecc7); }
        .icon-container.failed { background: linear-gradient(135deg, #fee2e2, #fecaca); }
        .icon-container.canceled { background: linear-gradient(135deg, #fff4d6, #fde68a); }
        .icon-container.expired { background: linear-gradient(135deg, #fde2e4, #fecdd3); }
        .icon-container.processing { background: linear-gradient(135deg, #e8f4fd, #bfdbfe); }

        .icon-container svg {
            width: 52px;
            height: 52px;
        }

        .icon-container.success svg { color: #0d6832; }
        .icon-container.failed svg { color: #991b1b; }
        .icon-container.canceled svg { color: #8e6a00; }
        .icon-container.expired svg { color: #9f1239; }
        .icon-container.processing svg { color: #0071e3; animation: spin 2s linear infinite; }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        h1 {
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 16px;
            color: #1d1d1f;
            letter-spacing: -0.02em;
        }

        p {
            font-size: 17px;
            color: #86868b;
            margin-bottom: 40px;
            line-height: 1.6;
            letter-spacing: -0.01em;
        }

        .btn {
            display: inline-block;
            padding: 16px 40px;
            background: #0071e3;
            color: white;
            text-decoration: none;
            border-radius: 24px;
            font-weight: 600;
            font-size: 16px;
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            letter-spacing: -0.01em;
            box-shadow: 0 4px 12px rgba(0, 113, 227, 0.3);
        }

        .btn:hover {
            background: #0077ed;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0, 113, 227, 0.4);
        }

        .btn:active {
            transform: translateY(0);
        }

        .refresh-link {
            display: inline-block;
            margin-top: 24px;
            padding: 12px 24px;
            background: rgba(0, 113, 227, 0.1);
            color: #0071e3;
            text-decoration: none;
            border-radius: 20px;
            font-weight: 500;
            font-size: 15px;
            transition: all 0.2s ease;
        }

        .refresh-link:hover {
            background: rgba(0, 113, 227, 0.15);
        }

        @media (max-width: 640px) {
            .card {
                padding: 40px 28px;
                border-radius: 24px;
            }

            h1 {
                font-size: 28px;
            }

            p {
                font-size: 16px;
            }

            .icon-container {
                width: 80px;
                height: 80px;
                margin-bottom: 24px;
            }

            .icon-container svg {
                width: 44px;
                height: 44px;
            }
        }
    </style>
</head>
<body class="<?php echo $status; ?>">
    <div class="card">
        <div class="icon-container <?php echo $status; ?>">
            <?php
            switch($status) {
                case 'success':
                    echo '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>';
                    break;
                case 'failed':
                    echo '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>';
                    break;
                case 'canceled':
                    echo '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>';
                    break;
                case 'expired':
                    echo '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>';
                    break;
                default:
                    echo '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99"/></svg>';
            }
            ?>
        </div>
        <h1>
            <?php
            switch($status) {
                case 'success':
                    echo 'Payment Successful!';
                    break;
                case 'failed':
                    echo 'Payment Failed';
                    break;
                case 'canceled':
                    echo 'Payment Canceled';
                    break;
                case 'expired':
                    echo 'Payment Expired';
                    break;
                default:
                    echo 'Processing Payment...';
            }
            ?>
        </h1>
        <p><?php echo htmlspecialchars($message); ?></p>

        <?php if ($status === 'success'): ?>
            <a href="/glass-market/public/index.php" class="btn">Continue to Homepage</a>
        <?php elseif ($status === 'processing'): ?>
            <a href="?user_id=<?php echo $user_id; ?>" class="refresh-link">↻ Refresh Status</a>
        <?php else: ?>
            <a href="/glass-market/resources/views/pricing.php" class="btn">Back to Pricing</a>
        <?php endif; ?>
    </div>
</body>
</html>
