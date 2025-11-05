<?php
// Subscription Handler
// Handles subscription cancellation
// Expects: $user array, database connection settings

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['cancel_subscription'])) {
    return;
}

$subscription_id = $_POST['subscription_id'] ?? 0;

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Verify the subscription belongs to this user before canceling
    $stmt = $pdo->prepare('
        UPDATE user_subscriptions 
        SET is_active = 0, updated_at = NOW() 
        WHERE id = :id AND user_id = :user_id
    ');
    
    $stmt->execute([
        'id' => $subscription_id,
        'user_id' => $user['id']
    ]);
    
    if ($stmt->rowCount() > 0) {
        $success_message = 'Subscription cancelled successfully. You will have access until the end date.';
        
        // Reload subscriptions
        $stmt = $pdo->prepare('SELECT * FROM user_subscriptions WHERE user_id = :user_id ORDER BY created_at DESC');
        $stmt->execute(['user_id' => $user['id']]);
        $user_subscriptions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        $error_message = 'Subscription not found or already cancelled.';
    }
} catch (PDOException $e) {
    $error_message = 'Failed to cancel subscription: ' . $e->getMessage();
}
