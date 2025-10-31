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
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .card {
            background: white;
            padding: 48px;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            text-align: center;
            max-width: 500px;
            width: 100%;
        }

        .icon {
            font-size: 64px;
            margin-bottom: 24px;
        }

        h1 {
            font-size: 28px;
            margin-bottom: 16px;
            color: #111827;
        }

        p {
            font-size: 16px;
            color: #6b7280;
            margin-bottom: 32px;
            line-height: 1.6;
        }

        .btn {
            display: inline-block;
            padding: 14px 32px;
            background: #2563eb;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 16px;
            transition: all 0.2s;
        }

        .btn:hover {
            background: #1d4ed8;
            transform: translateY(-2px);
        }

        .success { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
        .failed { background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); }
        .canceled { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); }
    </style>
</head>
<body class="<?php echo $status; ?>">
    <div class="card">
        <div class="icon">
            <?php
            switch($status) {
                case 'success':
                    echo '✅';
                    break;
                case 'failed':
                    echo '❌';
                    break;
                case 'canceled':
                    echo '⚠️';
                    break;
                case 'expired':
                    echo '⏰';
                    break;
                default:
                    echo '⏳';
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
            <a href="/glass-market/public/index.php" class="btn">Go to Homepage</a>
        <?php elseif ($status === 'processing'): ?>
            <p style="margin-top: 20px; font-size: 14px;">
                <a href="?user_id=<?php echo $user_id; ?>" style="color: #3b82f6;">Refresh to check status</a>
            </p>
        <?php else: ?>
            <a href="/glass-market/resources/views/pricing.php" class="btn">Back to Pricing</a>
        <?php endif; ?>
    </div>
</body>
</html>
