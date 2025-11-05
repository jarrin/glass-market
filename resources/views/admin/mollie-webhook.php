<?php
/**
 * Mollie Webhook Handler
 * Processes payment status updates from Mollie
 */

// Database connection
$db_host = '127.0.0.1';
$db_name = 'glass_market';
$db_user = 'root';
$db_pass = '';

// Get payment ID from POST data
$payment_id = $_POST['id'] ?? '';

if (!$payment_id) {
    http_response_code(400);
    exit('No payment ID provided');
}

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Load composer autoload
    require_once __DIR__ . '/../../../vendor/autoload.php';
    require_once __DIR__ . '/../../../database/classes/mollie.php';
    
    $mollie = new MolliePayment();
    $payment = $mollie->getPayment($payment_id);
    
    if ($payment) {
        // Update payment status in database
        $stmt = $pdo->prepare("
            UPDATE mollie_payments 
            SET status = :status, updated_at = NOW()
            WHERE payment_id = :payment_id
        ");
        $stmt->execute([
            'status' => $payment->status,
            'payment_id' => $payment_id
        ]);
        
        // If payment is successful, extend subscription
        if ($payment->isPaid()) {
            $stmt = $pdo->prepare("SELECT months, user_id, amount, currency FROM mollie_payments WHERE payment_id = :payment_id");
            $stmt->execute(['payment_id' => $payment_id]);
            $paymentData = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($paymentData) {
                $months = $paymentData['months'];
                $userId = $paymentData['user_id'];
                $amount = $paymentData['amount'] ?? $payment->amount->value;
                $currency = $paymentData['currency'] ?? $payment->amount->currency;
                
                // Get user details for email
                $stmt = $pdo->prepare("SELECT name, email FROM users WHERE id = :user_id");
                $stmt->execute(['user_id' => $userId]);
                $userData = $stmt->fetch(PDO::FETCH_ASSOC);
                
                // Check if subscription exists
                $stmt = $pdo->prepare("SELECT end_date FROM user_subscriptions WHERE user_id = :user_id AND is_active = 1");
                $stmt->execute(['user_id' => $userId]);
                $subscription = $stmt->fetch(PDO::FETCH_ASSOC);
                
                $newEndDate = null;
                
                if ($subscription && $subscription['end_date'] >= date('Y-m-d')) {
                    // Extend existing subscription
                    $newEndDate = date('Y-m-d', strtotime($subscription['end_date'] . " +{$months} months"));
                    $stmt = $pdo->prepare("UPDATE user_subscriptions SET end_date = :end_date, is_trial = 0 WHERE user_id = :user_id");
                    $stmt->execute(['end_date' => $newEndDate, 'user_id' => $userId]);
                } else {
                    // Create new subscription
                    $startDate = date('Y-m-d');
                    $newEndDate = date('Y-m-d', strtotime("+{$months} months"));
                    
                    $stmt = $pdo->prepare("
                        INSERT INTO user_subscriptions (user_id, start_date, end_date, is_trial, is_active)
                        VALUES (:user_id, :start_date, :end_date, 0, 1)
                        ON DUPLICATE KEY UPDATE 
                        start_date = :start_date, 
                        end_date = :end_date, 
                        is_trial = 0, 
                        is_active = 1
                    ");
                    $stmt->execute([
                        'user_id' => $userId,
                        'start_date' => $startDate,
                        'end_date' => $newEndDate
                    ]);
                }
                
                // Mark payment as paid
                $stmt = $pdo->prepare("UPDATE mollie_payments SET paid_at = NOW() WHERE payment_id = :payment_id");
                $stmt->execute(['payment_id' => $payment_id]);
                
                error_log("Subscription extended for user {$userId} by {$months} months");
                
                // Send payment receipt and subscription confirmation emails
                if ($userData) {
                    try {
                        require_once __DIR__ . '/../../../app/Services/RustMailer.php';
                        $mailer = new App\Services\RustMailer();
                        
                        // Send payment receipt
                        $receiptResult = $mailer->sendPaymentReceipt(
                            $userData['email'],
                            $userData['name'],
                            [
                                'payment_id' => $payment_id,
                                'amount' => $amount,
                                'currency' => $currency,
                                'description' => "{$months}-Month Subscription",
                                'date' => date('Y-m-d H:i:s'),
                                'method' => ucfirst($payment->method ?? 'card'),
                                'status' => 'paid',
                            ]
                        );
                        
                        if (!$receiptResult['success']) {
                            error_log("Payment receipt email failed: " . $receiptResult['message']);
                        }
                        
                        // Send subscription confirmation
                        if ($newEndDate) {
                            $subscriptionResult = $mailer->sendSubscriptionEmail(
                                $userData['email'],
                                $userData['name'],
                                [
                                    'plan_name' => "{$months}-Month Subscription",
                                    'amount' => $amount,
                                    'currency' => $currency,
                                    'start_date' => date('Y-m-d'),
                                    'end_date' => $newEndDate,
                                    'is_trial' => false,
                                ]
                            );
                            
                            if (!$subscriptionResult['success']) {
                                error_log("Subscription email failed: " . $subscriptionResult['message']);
                            }
                        }
                    } catch (Exception $e) {
                        error_log("Email notification exception: " . $e->getMessage());
                    }
                }
            }
        }
        
        http_response_code(200);
        exit('Webhook processed successfully');
    } else {
        http_response_code(404);
        exit('Payment not found');
    }
    
} catch (Exception $e) {
    error_log('Mollie Webhook Error: ' . $e->getMessage());
    http_response_code(500);
    exit('Internal server error');
}
