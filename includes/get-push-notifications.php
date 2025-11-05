<?php
/**
 * Get Push Notifications API
 * Returns unread push notifications for the current user
 */

session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['notifications' => []]);
    exit;
}

$user_id = $_SESSION['user_id'];

// Database connection
$db_host = '127.0.0.1';
$db_name = 'glass_market';
$db_user = 'root';
$db_pass = '';

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Get unread notifications
    $stmt = $pdo->prepare('
        SELECT id, title, body, url, created_at
        FROM push_notifications
        WHERE user_id = :user_id 
        AND is_read = 0
        AND created_at >= DATE_SUB(NOW(), INTERVAL 1 HOUR)
        ORDER BY created_at DESC
        LIMIT 5
    ');
    
    $stmt->execute(['user_id' => $user_id]);
    $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Mark as read
    if (!empty($notifications)) {
        $ids = array_column($notifications, 'id');
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $stmt = $pdo->prepare("UPDATE push_notifications SET is_read = 1 WHERE id IN ($placeholders)");
        $stmt->execute($ids);
    }
    
    echo json_encode(['notifications' => $notifications]);
    
} catch (PDOException $e) {
    echo json_encode(['notifications' => [], 'error' => $e->getMessage()]);
}
