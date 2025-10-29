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
     * @return bool Success status
     */
    public static function createTrialSubscription($pdo, $user_id)
    {
        try {
            $start_date = date('Y-m-d');
            $end_date = date('Y-m-d', strtotime('+3 months'));
            
            $stmt = $pdo->prepare("
                INSERT INTO user_subscriptions (user_id, start_date, end_date, is_trial, is_active)
                VALUES (:user_id, :start_date, :end_date, 1, 1)
            ");
            
            return $stmt->execute([
                'user_id' => $user_id,
                'start_date' => $start_date,
                'end_date' => $end_date
            ]);
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