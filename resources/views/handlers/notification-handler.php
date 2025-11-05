<?php
/**
 * Notification Handler
 * Handles notification preferences updates
 */

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['update_notifications'])) {
    return;
}

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Add notification preference columns if they don't exist
    $columns_to_add = [
        'notify_new_listings' => 'TINYINT(1) DEFAULT 1',
        'notify_account_updates' => 'TINYINT(1) DEFAULT 1',
        'notify_newsletter' => 'TINYINT(1) DEFAULT 0',
        'push_new_listings' => 'TINYINT(1) DEFAULT 0',
        'push_messages' => 'TINYINT(1) DEFAULT 0',
    ];
    
    foreach ($columns_to_add as $column => $type) {
        try {
            $pdo->exec("ALTER TABLE users ADD COLUMN {$column} {$type} AFTER email_verified_at");
        } catch (PDOException $e) {
            // Column already exists, continue
        }
    }
    
    // Update notification preferences
    $stmt = $pdo->prepare('
        UPDATE users 
        SET 
            notify_new_listings = :notify_new_listings,
            notify_account_updates = :notify_account_updates,
            notify_newsletter = :notify_newsletter,
            push_new_listings = :push_new_listings,
            push_messages = :push_messages,
            updated_at = NOW()
        WHERE id = :user_id
    ');
    
    $stmt->execute([
        'notify_new_listings' => isset($_POST['notify_new_listings']) ? 1 : 0,
        'notify_account_updates' => isset($_POST['notify_account_updates']) ? 1 : 0,
        'notify_newsletter' => isset($_POST['notify_newsletter']) ? 1 : 0,
        'push_new_listings' => isset($_POST['push_new_listings']) ? 1 : 0,
        'push_messages' => isset($_POST['push_messages']) ? 1 : 0,
        'user_id' => $user['id']
    ]);
    
    $success_message = 'Notification preferences updated successfully!';
    
    // Reload user data
    $stmt = $pdo->prepare('SELECT * FROM users WHERE id = :id LIMIT 1');
    $stmt->execute(['id' => $user['id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    $error_message = 'Failed to update preferences: ' . $e->getMessage();
}
