<?php

class Subscription
{
    public function __construct(
        private string $subscription_id,
        private string $email,
        private string $status,
        private string $start_date,
        private string $created_at,
        private string $updated_at
    ) {}

    public function saveUserSubscription()
    {
        
    }

    /**
     * Create a 3-month free trial subscription for a new user
     * @param PDO $pdo Database connection
     * @param int $user_id User ID
     * @param string|null $user_email Optional: user email to send confirmation
     * @param string|null $user_name Optional: user name for email
     * @return bool Success status
     */
    public static function createTrialSubscription($pdo, $user_id, $user_email = null, $user_name = null)
    {
        try {
            $start_date = date('Y-m-d');
            $end_date = date('Y-m-d', strtotime('+3 months'));
            
            $stmt = $pdo->prepare("
                INSERT INTO user_subscriptions (user_id, start_date, end_date, is_trial, is_active)
                VALUES (:user_id, :start_date, :end_date, 1, 1)
            ");
            
            $success = $stmt->execute([
                'user_id' => $user_id,
                'start_date' => $start_date,
                'end_date' => $end_date
            ]);
            
            // Send trial subscription email if email provided
            if ($success && $user_email && $user_name) {
                try {
                    require_once __DIR__ . '/../../app/Services/RustMailer.php';
                    $mailer = new App\Services\RustMailer();
                    $emailResult = $mailer->sendSubscriptionEmail(
                        $user_email,
                        $user_name,
                        [
                            'plan_name' => '3-Month Free Trial',
                            'amount' => '0.00',
                            'currency' => 'EUR',
                            'start_date' => $start_date,
                            'end_date' => $end_date,
                            'is_trial' => true,
                        ]
                    );
                    
                    if (!$emailResult['success']) {
                        error_log("Trial subscription email failed: " . $emailResult['message']);
                    }
                } catch (Exception $e) {
                    error_log("Trial subscription email exception: " . $e->getMessage());
                }
            }
            
            return $success;
        } catch (PDOException $e) {
            error_log("Failed to create trial subscription: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Check if user has an active subscription
     * @param PDO $pdo Database connection
     * @param int $user_id User ID
     * @return bool Has active subscription
     */
    public static function hasActiveSubscription($pdo, $user_id)
    {
        try {
            $stmt = $pdo->prepare("
                SELECT COUNT(*) 
                FROM user_subscriptions 
                WHERE user_id = :user_id 
                AND is_active = 1 
                AND end_date >= CURDATE()
            ");
            $stmt->execute(['user_id' => $user_id]);
            return $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            error_log("Failed to check subscription: " . $e->getMessage());
            return false;
        }
    }
}