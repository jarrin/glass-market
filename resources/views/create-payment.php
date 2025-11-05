<?php
session_start();
require_once __DIR__ . '/../../config.php';

/**
 * Display a professional error/message page
 */
function showMessagePage($title, $message, $type = 'error', $actions = []) {
    $iconColor = $type === 'error' ? '#991b1b' : ($type === 'warning' ? '#f59e0b' : '#16a34a');
    $bgColor = $type === 'error' ? '#fef2f2' : ($type === 'warning' ? '#fffbeb' : '#f0fdf4');
    $borderColor = $type === 'error' ? '#fecaca' : ($type === 'warning' ? '#fde68a' : '#bbf7d0');
    
    $icon = $type === 'error' 
        ? '<path stroke-linecap="round" stroke-linejoin="round" d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />'
        : ($type === 'warning' 
            ? '<path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />'
            : '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />');
    
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?php echo htmlspecialchars($title); ?> - Glass Market</title>
        <style>
            * { margin: 0; padding: 0; box-sizing: border-box; }
            body {
                font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
                background: #f5f5f5;
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 20px;
            }
            .message-container {
                background: white;
                border-radius: 12px;
                box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
                width: 100%;
                max-width: 520px;
                overflow: hidden;
                border: 1px solid #e0e0e0;
            }
            .message-header {
                padding: 32px;
                background: #fafafa;
                border-bottom: 2px solid #f5f5f5;
                text-align: center;
            }
            .icon-wrapper {
                width: 80px;
                height: 80px;
                margin: 0 auto 20px;
                border-radius: 50%;
                background: <?php echo $bgColor; ?>;
                border: 3px solid <?php echo $borderColor; ?>;
                display: flex;
                align-items: center;
                justify-content: center;
            }
            .icon-wrapper svg {
                width: 48px;
                height: 48px;
                stroke: <?php echo $iconColor; ?>;
            }
            .message-header h1 {
                font-size: 24px;
                font-weight: 800;
                color: #000;
                margin-bottom: 8px;
                text-transform: uppercase;
                letter-spacing: 0.5px;
            }
            .message-body {
                padding: 32px;
            }
            .message-content {
                text-align: center;
                margin-bottom: 28px;
            }
            .message-content p {
                font-size: 15px;
                line-height: 1.6;
                color: #555;
                margin-bottom: 12px;
            }
            .action-buttons {
                display: flex;
                flex-direction: column;
                gap: 12px;
            }
            .btn {
                padding: 14px 24px;
                font-size: 14px;
                font-weight: 600;
                border-radius: 6px;
                cursor: pointer;
                text-decoration: none;
                text-align: center;
                transition: all 0.2s ease;
                display: block;
                text-transform: uppercase;
                letter-spacing: 0.5px;
            }
            .btn-primary {
                background: #000;
                color: white;
                border: 2px solid #000;
            }
            .btn-primary:hover {
                background: #333;
                border-color: #333;
            }
            .btn-secondary {
                background: transparent;
                color: #000;
                border: 2px solid #ddd;
            }
            .btn-secondary:hover {
                border-color: #000;
                background: #fafafa;
            }
            .message-footer {
                background: #fafafa;
                border-top: 2px solid #f5f5f5;
                padding: 20px 32px;
                text-align: center;
                font-size: 13px;
                color: #888;
            }
        </style>
    </head>
    <body>
        <div class="message-container">
            <div class="message-header">
                <div class="icon-wrapper">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2">
                        <?php echo $icon; ?>
                    </svg>
                </div>
                <h1><?php echo htmlspecialchars($title); ?></h1>
            </div>
            
            <div class="message-body">
                <div class="message-content">
                    <?php echo $message; ?>
                </div>
                
                <div class="action-buttons">
                    <?php 
                    $isPrimary = true;
                    foreach ($actions as $label => $url): 
                    ?>
                        <a href="<?php echo htmlspecialchars($url); ?>" class="btn <?php echo $isPrimary ? 'btn-primary' : 'btn-secondary'; ?>">
                            <?php echo htmlspecialchars($label); ?>
                        </a>
                    <?php 
                        $isPrimary = false;
                    endforeach; 
                    ?>
                </div>
            </div>
            
            <div class="message-footer">
                Need help? Contact support at support@glassmarket.com
            </div>
        </div>
    </body>
    </html>
    <?php
    exit;
}

// Check if user is logged in
if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
    $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
    header('Location: ' . VIEWS_URL . '/login.php');
    exit;
}

// Get plan from URL
$plan = $_GET['plan'] ?? 'monthly';
$user_id = $_SESSION['user_id'] ?? 0;
$upgrade_from_trial = isset($_GET['upgrade_from_trial']) && $_GET['upgrade_from_trial'] == '1';

if (!$user_id) {
    showMessagePage(
        'Session Error',
        '<p>Your session has expired. Please log in again to continue.</p>',
        'error',
        ['Login' => VIEWS_URL . '/login.php', 'Go Home' => PUBLIC_URL . '/index.php']
    );
}

// Database connection
$db_host = '127.0.0.1';
$db_name = 'glass_market';
$db_user = 'root';
$db_pass = '';

$pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Check if user already has an active paid subscription
$stmt = $pdo->prepare("
    SELECT * FROM user_subscriptions 
    WHERE user_id = :user_id 
    AND is_active = 1 
    AND end_date > NOW()
    LIMIT 1
");
$stmt->execute(['user_id' => $user_id]);
$existing_subscription = $stmt->fetch(PDO::FETCH_ASSOC);

// If user has an active PAID subscription (not trial), don't allow new subscription
if ($existing_subscription && !$existing_subscription['is_trial']) {
    showMessagePage(
        'Active Subscription',
        '<p>You already have an active subscription.</p><p>Your subscription is valid until <strong>' . date('F d, Y', strtotime($existing_subscription['end_date'])) . '</strong></p>',
        'warning',
        ['Manage Subscription' => VIEWS_URL . '/profile.php?tab=subscription', 'Go Home' => PUBLIC_URL . '/index.php']
    );
}

// Determine months and amount based on plan
switch ($plan) {
    case 'trial':
        $months = 3;
        $amount = 0; // Free trial
        $description = "Glass Market - 3 Month Free Trial";
        break;
    case 'monthly':
        $months = 1;
        $amount = 9.99;
        $description = "Glass Market - Monthly Subscription";
        break;
    case 'annual':
        $months = 12;
        $amount = 99.00;
        $description = "Glass Market - Annual Subscription";
        break;
    default:
        showMessagePage(
            'Invalid Plan',
            '<p>The selected subscription plan is not valid.</p><p>Please choose a valid plan from our pricing page.</p>',
            'error',
            ['View Pricing' => VIEWS_URL . '/pricing.php', 'Go Home' => PUBLIC_URL . '/index.php']
        );
}

// If free trial, check eligibility and create subscription
if ($amount == 0) {
    try {
        $db_host = '127.0.0.1';
        $db_name = 'glass_market';
        $db_user = 'root';
        $db_pass = '';
        
        $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Check if user has EVER had a trial subscription
        require_once __DIR__ . '/../../database/classes/subscriptions.php';
        if (Subscription::hasEverHadTrial($pdo, $user_id)) {
            showMessagePage(
                'Trial Already Used',
                '<p>You have already used your 3-month free trial.</p><p><strong>Important:</strong> Free trials can only be activated once per account.</p><p>Please choose one of our paid plans to continue enjoying Glass Market.</p>',
                'warning',
                ['View Paid Plans' => VIEWS_URL . '/pricing.php', 'Go Home' => PUBLIC_URL . '/index.php']
            );
        }
        
        // Create free trial subscription (this will double-check eligibility)
        $trialCreated = Subscription::createTrialSubscription($pdo, $user_id);
        
        if ($trialCreated) {
            // Redirect to success page
            header('Location: ' . PUBLIC_URL . '/index.php?trial_activated=1');
            exit;
        } else {
            showMessagePage(
                'Activation Failed',
                '<p>Unable to activate your free trial.</p><p>This may be because you have already used your trial subscription.</p><p>Please contact support if you believe this is an error.</p>',
                'error',
                ['View Paid Plans' => VIEWS_URL . '/pricing.php', 'Contact Support' => PUBLIC_URL . '/contact.php', 'Go Home' => PUBLIC_URL . '/index.php']
            );
        }
        
    } catch (PDOException $e) {
        error_log('Trial activation database error: ' . $e->getMessage());
        showMessagePage(
            'System Error',
            '<p>A system error occurred while processing your request.</p><p>Our team has been notified and will investigate the issue.</p><p>Please try again later or contact support.</p>',
            'error',
            ['Try Again' => VIEWS_URL . '/pricing.php', 'Contact Support' => PUBLIC_URL . '/contact.php', 'Go Home' => PUBLIC_URL . '/index.php']
        );
    }
}

// For paid plans, create Mollie payment
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../database/classes/mollie.php';

// If upgrading from trial, cancel the trial subscription
if ($upgrade_from_trial && $existing_subscription && $existing_subscription['is_trial']) {
    try {
        $stmt = $pdo->prepare("
            UPDATE user_subscriptions 
            SET is_active = 0, 
                end_date = NOW(),
                updated_at = NOW()
            WHERE user_id = :user_id AND is_trial = 1
        ");
        $stmt->execute(['user_id' => $user_id]);
        error_log("Trial subscription cancelled for user_id: $user_id - upgrading to paid plan");
    } catch (PDOException $e) {
        error_log("Error cancelling trial: " . $e->getMessage());
    }
}

/**
 * Log payment error to database
 */
function logPaymentError($pdo, $user_id, $plan, $amount, $error_message, $request_data = null, $payment_id = null) {
    try {
        $stmt = $pdo->prepare("
            INSERT INTO payment_errors (user_id, plan, amount, error_message, error_context, payment_id, request_data, created_at)
            VALUES (:user_id, :plan, :amount, :error_message, :error_context, :payment_id, :request_data, NOW())
        ");

        $error_context = json_encode([
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown',
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? 'Unknown',
            'timestamp' => date('Y-m-d H:i:s'),
            'session_id' => session_id()
        ]);

        $stmt->execute([
            'user_id' => $user_id,
            'plan' => $plan,
            'amount' => $amount,
            'error_message' => $error_message,
            'error_context' => $error_context,
            'payment_id' => $payment_id,
            'request_data' => $request_data ? json_encode($request_data) : null
        ]);

        return $pdo->lastInsertId();
    } catch (Exception $e) {
        error_log('Failed to log payment error: ' . $e->getMessage());
        return false;
    }
}

try {
    $db_host = '127.0.0.1';
    $db_name = 'glass_market';
    $db_user = 'root';
    $db_pass = '';

    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $mollie = new MolliePayment();

    if (!$mollie->isConfigured()) {
        $error = 'Mollie is not configured. Please check your .env file for MOLLIE_TEST_API_KEY.';
        logPaymentError($pdo, $user_id, $plan, $amount, $error);
        showMessagePage(
            'Payment System Error',
            '<p>The payment system is currently not available.</p><p>Our team has been notified and will resolve this issue shortly.</p><p>Please try again later or contact support.</p>',
            'error',
            ['Contact Support' => PUBLIC_URL . '/contact.php', 'View Pricing' => VIEWS_URL . '/pricing.php', 'Go Home' => PUBLIC_URL . '/index.php']
        );
    }

    $result = $mollie->createSubscriptionPayment($user_id, $months, $pdo);

    if (is_array($result) && isset($result['error'])) {
        // Log the error
        logPaymentError($pdo, $user_id, $plan, $amount, $result['error'], [
            'months' => $months,
            'description' => $description
        ]);

        // Show user-friendly error
        showMessagePage(
            'Payment Error',
            '<p>We encountered an error while setting up your payment.</p><p>The issue has been logged and our team will review it.</p><p>Please try again or contact support for assistance.</p>',
            'error',
            ['Try Again' => VIEWS_URL . '/pricing.php', 'Contact Support' => PUBLIC_URL . '/contact.php', 'Go Home' => PUBLIC_URL . '/index.php']
        );
    }

    if ($result) {
        // Redirect to Mollie checkout
        header('Location: ' . $result);
        exit;
    } else {
        $error = 'Failed to create payment. Unknown error occurred.';
        logPaymentError($pdo, $user_id, $plan, $amount, $error);
        showMessagePage(
            'Payment Failed',
            '<p>Unable to create your payment.</p><p>An unknown error occurred. Our team has been notified.</p><p>Please try again or contact support for assistance.</p>',
            'error',
            ['Try Again' => VIEWS_URL . '/pricing.php', 'Contact Support' => PUBLIC_URL . '/contact.php', 'Go Home' => PUBLIC_URL . '/index.php']
        );
    }

} catch (Exception $e) {
    // Log the exception
    if (isset($pdo)) {
        logPaymentError($pdo, $user_id, $plan, $amount, $e->getMessage(), [
            'exception_type' => get_class($e),
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ]);
    }

    showMessagePage(
        'System Error',
        '<p>A system error occurred while processing your payment.</p><p>The error has been logged and our team will investigate.</p><p>Please try again later or contact support for assistance.</p>',
        'error',
        ['Try Again' => VIEWS_URL . '/pricing.php', 'Contact Support' => PUBLIC_URL . '/contact.php', 'Go Home' => PUBLIC_URL . '/index.php']
    );
}
