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

if (!$user_id) {
    die('Error: User ID not found in session');
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

try {
    $db_host = '127.0.0.1';
    $db_name = 'glass_market';
    $db_user = 'root';
    $db_pass = '';
    
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $mollie = new MolliePayment();
    
    if (!$mollie->isConfigured()) {
        die('Error: Mollie is not configured. Please check your .env file for MOLLIE_TEST_API_KEY.');
    }
    
    $result = $mollie->createSubscriptionPayment($user_id, $months, $pdo);
    
    if (is_array($result) && isset($result['error'])) {
        // Show detailed error
        die('Payment Error: ' . htmlspecialchars($result['error']) . '<br><br>
             <a href="/glass-market/resources/views/pricing.php">← Back to Pricing</a>');
    }
    
    if ($result) {
        // Redirect to Mollie checkout
        header('Location: ' . $result);
        exit;
    } else {
        die('Failed to create payment. Please try again or contact support.<br><br>
             <a href="/glass-market/resources/views/pricing.php">← Back to Pricing</a>');
    }
    
} catch (Exception $e) {
    die('Error: ' . htmlspecialchars($e->getMessage()) . '<br><br>
         <a href="/glass-market/resources/views/pricing.php">← Back to Pricing</a>');
}
