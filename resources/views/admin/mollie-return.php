<?php
session_start();

// Database connection
$db_host = '127.0.0.1';
$db_name = 'glass_market';
$db_user = 'root';
$db_pass = '';

$status = 'processing';
$message = 'Processing your payment...';
$payment_id = $_GET['payment_id'] ?? '';
$user_id = $_GET['user_id'] ?? 0;

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Load composer autoload
    require_once __DIR__ . '/../../../vendor/autoload.php';
    require_once __DIR__ . '/../../../database/classes/mollie.php';
    require_once __DIR__ . '/../../../database/classes/subscriptions.php';
    
    $mollie = new MolliePayment();
    
    if ($payment_id) {
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
                    }
                }
                
            } elseif ($payment->isFailed()) {
                $status = 'failed';
                $message = '❌ Payment failed. Please try again.';
            } elseif ($payment->isCanceled()) {
                $status = 'canceled';
                $message = '⚠️ Payment was canceled.';
            } elseif ($payment->isExpired()) {
                $status = 'expired';
                $message = '⏰ Payment expired. Please create a new payment.';
            }
        }
    }
    
} catch (Exception $e) {
    $status = 'error';
    $message = 'An error occurred: ' . $e->getMessage();
    error_log('Mollie Return Error: ' . $e->getMessage());
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
            <a href="dashboard.php" class="btn">Go to Dashboard</a>
        <?php else: ?>
            <a href="sandbox.php" class="btn">Back to Sandbox</a>
        <?php endif; ?>
    </div>
</body>
</html>
