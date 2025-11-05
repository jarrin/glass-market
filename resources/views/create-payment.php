<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
    $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
    header('Location: /glass-market/resources/views/login.php');
    exit;
}

// Get plan from URL
$plan = $_GET['plan'] ?? 'monthly';
$user_id = $_SESSION['user_id'] ?? 0;
$upgrade_from_trial = isset($_GET['upgrade_from_trial']) && $_GET['upgrade_from_trial'] == '1';

if (!$user_id) {
    die('Error: User ID not found in session');
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
    die('
        <h2>You already have an active subscription</h2>
        <p>You are currently subscribed until ' . date('F d, Y', strtotime($existing_subscription['end_date'])) . '</p>
        <p>
            <a href="/glass-market/resources/views/profile.php?tab=subscription">Manage Subscription</a> |
            <a href="/glass-market/public/index.php">Go Home</a>
        </p>
    ');
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
        die('Invalid plan selected');
}

// If free trial, just create subscription directly
if ($amount == 0) {
    try {
        $db_host = '127.0.0.1';
        $db_name = 'glass_market';
        $db_user = 'root';
        $db_pass = '';
        
        $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Create free trial subscription
        $start_date = date('Y-m-d H:i:s');
        $end_date = date('Y-m-d H:i:s', strtotime('+3 months'));
        
        // Check if subscription already exists
        $stmt = $pdo->prepare("SELECT id FROM user_subscriptions WHERE user_id = :user_id");
        $stmt->execute(['user_id' => $user_id]);
        
        if ($stmt->fetch()) {
            // Update existing
            $stmt = $pdo->prepare("
                UPDATE user_subscriptions 
                SET start_date = :start_date, end_date = :end_date, is_trial = 1, is_active = 1
                WHERE user_id = :user_id
            ");
        } else {
            // Insert new
            $stmt = $pdo->prepare("
                INSERT INTO user_subscriptions (user_id, start_date, end_date, is_trial, is_active)
                VALUES (:user_id, :start_date, :end_date, 1, 1)
            ");
        }
        
        $stmt->execute([
            'user_id' => $user_id,
            'start_date' => $start_date,
            'end_date' => $end_date
        ]);
        
        // Redirect to success page
        header('Location: /glass-market/public/index.php?trial_activated=1');
        exit;
        
    } catch (PDOException $e) {
        die('Database error: ' . $e->getMessage());
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
        die('Error: ' . $error . '<br><br>
             <a href="/glass-market/resources/views/pricing.php">← Back to Pricing</a>');
    }

    $result = $mollie->createSubscriptionPayment($user_id, $months, $pdo);

    if (is_array($result) && isset($result['error'])) {
        // Log the error
        logPaymentError($pdo, $user_id, $plan, $amount, $result['error'], [
            'months' => $months,
            'description' => $description
        ]);

        // Show user-friendly error
        die('Payment Error: ' . htmlspecialchars($result['error']) . '<br><br>
             This error has been logged and our team will review it.<br><br>
             <a href="/glass-market/resources/views/pricing.php">← Back to Pricing</a>');
    }

    if ($result) {
        // Redirect to Mollie checkout
        header('Location: ' . $result);
        exit;
    } else {
        $error = 'Failed to create payment. Unknown error occurred.';
        logPaymentError($pdo, $user_id, $plan, $amount, $error);
        die('Failed to create payment. Please try again or contact support.<br><br>
             <a href="/glass-market/resources/views/pricing.php">← Back to Pricing</a>');
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

    die('Error: ' . htmlspecialchars($e->getMessage()) . '<br><br>
         This error has been logged. Please try again or contact support.<br><br>
         <a href="/glass-market/resources/views/pricing.php">← Back to Pricing</a>');
}
