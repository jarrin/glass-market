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
$payment_amount = 0;
$payment_months = 0;
$is_trial_upgrade = false;

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

    // Check if this was a trial upgrade
    $stmt = $pdo->prepare("SELECT COUNT(*) as trial_count FROM user_subscriptions WHERE user_id = :user_id AND is_trial = 1");
    $stmt->execute(['user_id' => $user_id]);
    $is_trial_upgrade = $stmt->fetch(PDO::FETCH_ASSOC)['trial_count'] > 0;

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
        $payment_amount = $pendingPayment['amount'];
        $payment_months = $pendingPayment['months'];
        error_log("Found payment ID: $payment_id for user: $user_id");

        $payment = $mollie->getPayment($payment_id);

        if ($payment) {
            if ($payment->isPaid()) {
                $status = 'success';

                // Update payment status in database
                $stmt = $pdo->prepare("UPDATE mollie_payments SET status = 'paid', paid_at = NOW() WHERE payment_id = :payment_id");
                $stmt->execute(['payment_id' => $payment_id]);

                // Extend user subscription
                $stmt = $pdo->prepare("SELECT months, user_id, amount FROM mollie_payments WHERE payment_id = :payment_id");
                $stmt->execute(['payment_id' => $payment_id]);
                $paymentData = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($paymentData) {
                    $months = $paymentData['months'];
                    $userId = $paymentData['user_id'];

                    // Check if subscription exists
                    $stmt = $pdo->prepare("SELECT end_date, is_trial FROM user_subscriptions WHERE user_id = :user_id AND is_active = 1");
                    $stmt->execute(['user_id' => $userId]);
                    $subscription = $stmt->fetch(PDO::FETCH_ASSOC);

                    if ($subscription && $subscription['end_date'] >= date('Y-m-d')) {
                        // Extend existing subscription
                        $newEndDate = date('Y-m-d', strtotime($subscription['end_date'] . " +{$months} months"));
                        $stmt = $pdo->prepare("UPDATE user_subscriptions SET end_date = :end_date, is_trial = 0, updated_at = NOW() WHERE user_id = :user_id");
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

                    // Send confirmation email
                    try {
                        require_once __DIR__ . '/../../../app/Services/RustMailer.php';
                        $mailer = new App\Services\RustMailer();

                        // Get user details
                        $stmt = $pdo->prepare("SELECT name, email FROM users WHERE id = :user_id");
                        $stmt->execute(['user_id' => $userId]);
                        $user = $stmt->fetch(PDO::FETCH_ASSOC);

                        if ($user) {
                            $mailer->sendSubscriptionEmail(
                                $user['email'],
                                $user['name'],
                                [
                                    'plan_name' => $months == 1 ? 'Monthly Plan' : 'Annual Plan',
                                    'amount' => number_format($paymentData['amount'], 2),
                                    'currency' => 'EUR',
                                    'start_date' => $startDate,
                                    'end_date' => $newEndDate ?? $endDate,
                                    'is_trial' => false,
                                ]
                            );
                        }
                    } catch (Exception $e) {
                        error_log("Failed to send subscription email: " . $e->getMessage());
                    }

                    // Log success
                    error_log("Payment $payment_id successfully processed for user $userId");
                }

            } elseif ($payment->isFailed()) {
                $status = 'failed';

                // Update payment status
                $stmt = $pdo->prepare("UPDATE mollie_payments SET status = 'failed', updated_at = NOW() WHERE payment_id = :payment_id");
                $stmt->execute(['payment_id' => $payment_id]);

                error_log("Payment $payment_id failed for user $user_id");

            } elseif ($payment->isCanceled()) {
                $status = 'canceled';

                // Update payment status
                $stmt = $pdo->prepare("UPDATE mollie_payments SET status = 'canceled', updated_at = NOW() WHERE payment_id = :payment_id");
                $stmt->execute(['payment_id' => $payment_id]);

                error_log("Payment $payment_id canceled by user $user_id");

            } elseif ($payment->isExpired()) {
                $status = 'expired';

                // Update payment status
                $stmt = $pdo->prepare("UPDATE mollie_payments SET status = 'expired', updated_at = NOW() WHERE payment_id = :payment_id");
                $stmt->execute(['payment_id' => $payment_id]);

                error_log("Payment $payment_id expired for user $user_id");

            } else {
                // Payment is still open/pending
                $status = 'processing';
                error_log("Payment $payment_id still pending for user $user_id");
            }
        } else {
            $status = 'error';
            error_log("Could not get payment $payment_id from Mollie");
        }
    } else {
        $status = 'error';
        error_log("No payment found for user $user_id");
    }

} catch (Exception $e) {
    $status = 'error';
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
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Helvetica Neue', Arial, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            background: #f5f7fa;
        }

        /* Status-specific backgrounds */
        body.success { background: linear-gradient(135deg, #e8f5e9 0%, #f1f8e9 100%); }
        body.failed { background: linear-gradient(135deg, #ffebee 0%, #fce4ec 100%); }
        body.canceled { background: linear-gradient(135deg, #fff9e6 0%, #fffaf0 100%); }
        body.expired { background: linear-gradient(135deg, #ffeef0 0%, #fff5f7 100%); }
        body.processing { background: linear-gradient(135deg, #e3f2fd 0%, #e1f5fe 100%); }
        body.error { background: linear-gradient(135deg, #ffe0e0 0%, #fff0f0 100%); }

        .container {
            max-width: 600px;
            width: 100%;
            animation: slideUp 0.6s cubic-bezier(0.16, 1, 0.3, 1);
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .card {
            background: white;
            border-radius: 24px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1), 0 8px 24px rgba(0, 0, 0, 0.06);
            overflow: hidden;
        }

        .card-header {
            padding: 48px 40px 32px;
            text-align: center;
            position: relative;
        }

        .icon-wrapper {
            width: 96px;
            height: 96px;
            margin: 0 auto 24px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            animation: iconPop 0.6s cubic-bezier(0.16, 1, 0.3, 1) 0.2s backwards;
        }

        @keyframes iconPop {
            0% {
                opacity: 0;
                transform: scale(0.3);
            }
            50% {
                transform: scale(1.1);
            }
            100% {
                opacity: 1;
                transform: scale(1);
            }
        }

        .icon-wrapper::before {
            content: '';
            position: absolute;
            width: 100%;
            height: 100%;
            border-radius: 50%;
            animation: pulse 2s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
                opacity: 0.3;
            }
            50% {
                transform: scale(1.1);
                opacity: 0.1;
            }
        }

        /* Status-specific icon styling */
        body.success .icon-wrapper { background: linear-gradient(135deg, #66bb6a, #4caf50); }
        body.success .icon-wrapper::before { background: #4caf50; }
        body.failed .icon-wrapper { background: linear-gradient(135deg, #ef5350, #e53935); }
        body.failed .icon-wrapper::before { background: #e53935; }
        body.canceled .icon-wrapper { background: linear-gradient(135deg, #ffa726, #ff9800); }
        body.canceled .icon-wrapper::before { background: #ff9800; }
        body.expired .icon-wrapper { background: linear-gradient(135deg, #ec407a, #e91e63); }
        body.expired .icon-wrapper::before { background: #e91e63; }
        body.processing .icon-wrapper { background: linear-gradient(135deg, #42a5f5, #2196f3); }
        body.processing .icon-wrapper::before { background: #2196f3; }
        body.error .icon-wrapper { background: linear-gradient(135deg, #ef5350, #e53935); }
        body.error .icon-wrapper::before { background: #e53935; }

        .icon-wrapper svg {
            width: 52px;
            height: 52px;
            color: white;
            position: relative;
            z-index: 1;
        }

        body.processing .icon-wrapper svg {
            animation: rotate 2s linear infinite;
        }

        @keyframes rotate {
            to { transform: rotate(360deg); }
        }

        h1 {
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 12px;
            color: #1a1a1a;
            letter-spacing: -0.02em;
        }

        .subtitle {
            font-size: 16px;
            color: #666;
            line-height: 1.6;
            margin-bottom: 8px;
        }

        .card-body {
            padding: 0 40px 48px;
        }

        .info-box {
            background: #f8f9fa;
            border-radius: 16px;
            padding: 24px;
            margin-bottom: 24px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #e0e0e0;
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-label {
            font-size: 14px;
            color: #666;
            font-weight: 500;
        }

        .info-value {
            font-size: 14px;
            color: #1a1a1a;
            font-weight: 600;
        }

        .message-box {
            background: #fff8e1;
            border-left: 4px solid #ffc107;
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 24px;
        }

        body.success .message-box {
            background: #e8f5e9;
            border-left-color: #4caf50;
        }

        body.failed .message-box,
        body.error .message-box {
            background: #ffebee;
            border-left-color: #f44336;
        }

        body.canceled .message-box {
            background: #fff8e1;
            border-left-color: #ff9800;
        }

        body.expired .message-box {
            background: #fce4ec;
            border-left-color: #e91e63;
        }

        .message-box h3 {
            font-size: 16px;
            margin-bottom: 8px;
            color: #1a1a1a;
        }

        .message-box p {
            font-size: 14px;
            color: #555;
            line-height: 1.6;
            margin: 0;
        }

        .button-group {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .btn {
            display: block;
            padding: 16px 32px;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            text-align: center;
            text-decoration: none;
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            border: none;
            cursor: pointer;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            box-shadow: 0 4px 16px rgba(102, 126, 234, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(102, 126, 234, 0.4);
        }

        .btn-secondary {
            background: white;
            color: #667eea;
            border: 2px solid #e0e0e0;
        }

        .btn-secondary:hover {
            border-color: #667eea;
            background: #f8f9fa;
        }

        .btn-danger {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
            box-shadow: 0 4px 16px rgba(245, 87, 108, 0.3);
        }

        .btn-danger:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(245, 87, 108, 0.4);
        }

        .support-text {
            text-align: center;
            margin-top: 24px;
            padding-top: 24px;
            border-top: 1px solid #e0e0e0;
            font-size: 13px;
            color: #999;
        }

        .support-text a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
        }

        .support-text a:hover {
            text-decoration: underline;
        }

        @media (max-width: 640px) {
            .container {
                padding: 0 16px;
            }

            .card-header,
            .card-body {
                padding-left: 24px;
                padding-right: 24px;
            }

            h1 {
                font-size: 24px;
            }

            .icon-wrapper {
                width: 80px;
                height: 80px;
            }

            .icon-wrapper svg {
                width: 44px;
                height: 44px;
            }
        }
    </style>
</head>
<body class="<?php echo $status; ?>">
    <div class="container">
        <div class="card">
            <div class="card-header">
                <div class="icon-wrapper">
                    <?php
                    switch($status) {
                        case 'success':
                            echo '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>';
                            break;
                        case 'failed':
                        case 'error':
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
                        case 'processing':
                            echo 'Processing Payment...';
                            break;
                        default:
                            echo 'Something Went Wrong';
                    }
                    ?>
                </h1>

                <p class="subtitle">
                    <?php
                    switch($status) {
                        case 'success':
                            echo 'Your subscription has been activated successfully.';
                            break;
                        case 'failed':
                            echo 'We couldn\'t process your payment.';
                            break;
                        case 'canceled':
                            echo 'You canceled the payment process.';
                            break;
                        case 'expired':
                            echo 'The payment session has expired.';
                            break;
                        case 'processing':
                            echo 'Please wait while we confirm your payment.';
                            break;
                        default:
                            echo 'An unexpected error occurred.';
                    }
                    ?>
                </p>
            </div>

            <div class="card-body">
                <?php if ($payment_amount > 0 && in_array($status, ['success', 'failed', 'canceled', 'expired'])): ?>
                <div class="info-box">
                    <div class="info-row">
                        <span class="info-label">Plan</span>
                        <span class="info-value"><?php echo $payment_months == 1 ? 'Monthly' : 'Annual'; ?> Subscription</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Amount</span>
                        <span class="info-value">€<?php echo number_format($payment_amount, 2); ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Duration</span>
                        <span class="info-value"><?php echo $payment_months; ?> month<?php echo $payment_months > 1 ? 's' : ''; ?></span>
                    </div>
                    <?php if ($is_trial_upgrade): ?>
                    <div class="info-row">
                        <span class="info-label">Type</span>
                        <span class="info-value">Trial Upgrade</span>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endif; ?>

                <div class="message-box">
                    <?php
                    switch($status) {
                        case 'success':
                            echo '<h3>🎉 Welcome to Premium!</h3>';
                            echo '<p>Your account has been upgraded. You now have full access to all Glass Market features. A confirmation email has been sent to your inbox.</p>';
                            break;

                        case 'failed':
                            echo '<h3>Payment Could Not Be Completed</h3>';
                            echo '<p>Your payment was declined. This could be due to insufficient funds, an expired card, or your bank declining the transaction. Please check your payment details and try again.</p>';
                            break;

                        case 'canceled':
                            echo '<h3>Payment Canceled</h3>';
                            if ($is_trial_upgrade) {
                                echo '<p>Your trial upgrade was canceled. Don\'t worry - your free trial is still active! You can upgrade anytime before your trial expires.</p>';
                            } else {
                                echo '<p>You canceled the payment process. No charges were made to your account. You can try again whenever you\'re ready.</p>';
                            }
                            break;

                        case 'expired':
                            echo '<h3>Session Timed Out</h3>';
                            echo '<p>Your payment session expired due to inactivity. No charges were made. Please create a new payment to continue with your subscription.</p>';
                            break;

                        case 'processing':
                            echo '<h3>Still Processing...</h3>';
                            echo '<p>Your payment is being verified by our payment processor. This usually takes a few seconds. The page will automatically refresh.</p>';
                            break;

                        default:
                            echo '<h3>Unexpected Error</h3>';
                            echo '<p>An unexpected error occurred while processing your payment. Our team has been notified. Please try again or contact support if the issue persists.</p>';
                    }
                    ?>
                </div>

                <div class="button-group">
                    <?php if ($status === 'success'): ?>
                        <a href="/glass-market/public/index.php" class="btn btn-primary">Continue to Homepage</a>
                        <a href="/glass-market/resources/views/profile.php?tab=subscription" class="btn btn-secondary">View Subscription</a>

                    <?php elseif ($status === 'processing'): ?>
                        <a href="?user_id=<?php echo $user_id; ?>" class="btn btn-primary" onclick="setTimeout(() => location.reload(), 3000); return false;">Refresh Status</a>
                        <p style="text-align: center; font-size: 14px; color: #666; margin-top: 12px;">Auto-refreshing in 3 seconds...</p>
                        <script>
                            setTimeout(() => location.reload(), 3000);
                        </script>

                    <?php elseif (in_array($status, ['failed', 'canceled', 'expired'])): ?>
                        <a href="/glass-market/resources/views/pricing.php" class="btn btn-primary">Try Again</a>
                        <a href="/glass-market/resources/views/profile.php?tab=subscription" class="btn btn-secondary">View Subscription</a>

                    <?php else: ?>
                        <a href="/glass-market/resources/views/pricing.php" class="btn btn-primary">View Plans</a>
                        <a href="/glass-market/public/index.php" class="btn btn-secondary">Go Home</a>
                    <?php endif; ?>
                </div>

                <div class="support-text">
                    Need help? <a href="mailto:support@glassmarket.com">Contact Support</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
