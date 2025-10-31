<?php
/**
 * Subscription Status Checker
 * Returns subscription status for the logged-in user
 */

if (!isset($_SESSION)) {
    session_start();
}

$subscription_status = [
    'has_access' => false,
    'is_expired' => false,
    'is_trial' => false,
    'days_remaining' => 0,
    'end_date' => null,
    'show_notification' => false,
    'notification_type' => null, // 'expired', 'expiring_soon', 'no_subscription'
];

// Don't check subscription for admins
if (isset($_SESSION['is_admin']) && ($_SESSION['is_admin'] === true || $_SESSION['is_admin'] == 1)) {
    $subscription_status['has_access'] = true;
    $subscription_status['show_notification'] = false;
    return;
}

// Check if user is logged in
if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
    // User not logged in - show friendly signup prompt
    $subscription_status['show_notification'] = true;
    $subscription_status['notification_type'] = 'not_logged_in';
    return;
}

// User is logged in - check subscription
if (isset($_SESSION['user_id'])) {
    
    // Database connection
    $db_host = '127.0.0.1';
    $db_name = 'glass_market';
    $db_user = 'root';
    $db_pass = '';
    
    try {
        $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Get user's subscription status
        $stmt = $pdo->prepare("
            SELECT 
                us.start_date,
                us.end_date,
                us.is_trial,
                us.is_active,
                DATEDIFF(us.end_date, CURDATE()) as days_remaining,
                CASE 
                    WHEN us.end_date >= CURDATE() AND us.is_active = 1 THEN 'active'
                    WHEN us.end_date < CURDATE() THEN 'expired'
                    ELSE 'none'
                END as status
            FROM user_subscriptions us
            WHERE us.user_id = :user_id
            ORDER BY us.end_date DESC
            LIMIT 1
        ");
        $stmt->execute(['user_id' => $_SESSION['user_id']]);
        $sub = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($sub) {
            $subscription_status['end_date'] = $sub['end_date'];
            $subscription_status['is_trial'] = (bool)$sub['is_trial'];
            $subscription_status['days_remaining'] = (int)$sub['days_remaining'];
            
            if ($sub['status'] === 'active') {
                $subscription_status['has_access'] = true;
                
                // Show warning if expiring soon (within 7 days)
                // But only if not dismissed in last 24 hours
                if ($subscription_status['days_remaining'] <= 7 && $subscription_status['days_remaining'] > 0) {
                    $dismissed_at = $_SESSION['notification_dismissed_at'] ?? 0;
                    if (time() - $dismissed_at > 86400) { // 24 hours
                        $subscription_status['show_notification'] = true;
                        $subscription_status['notification_type'] = 'expiring_soon';
                    }
                }
            } elseif ($sub['status'] === 'expired') {
                $subscription_status['is_expired'] = true;
                $subscription_status['show_notification'] = true;
                $subscription_status['notification_type'] = 'expired';
            }
        } else {
            // No subscription found
            $subscription_status['show_notification'] = true;
            $subscription_status['notification_type'] = 'no_subscription';
        }
        
    } catch (PDOException $e) {
        error_log('Subscription check error: ' . $e->getMessage());
    }
}
