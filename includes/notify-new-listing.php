<?php
/**
 * Send Notifications for New Listing
 * Called when a new listing is created
 */

require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../app/Services/RustMailer.php';

use App\Services\RustMailer;

function notifyUsersOfNewListing($listing_id) {
    // Database connection
    $db_host = '127.0.0.1';
    $db_name = 'glass_market';
    $db_user = 'root';
    $db_pass = '';
    
    try {
        $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Get listing details
        $stmt = $pdo->prepare('SELECT * FROM listings WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $listing_id]);
        $listing = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$listing) {
            return false;
        }
        
        // Get all users who want to be notified of new listings
        $stmt = $pdo->prepare('
            SELECT id, name, email 
            FROM users 
            WHERE notify_new_listings = 1 
            AND email_verified_at IS NOT NULL
            AND id != :listing_owner_id
        ');
        
        // Get listing owner ID (you'll need to add this logic based on your structure)
        $listing_owner_id = 0; // Replace with actual owner ID
        $stmt->execute(['listing_owner_id' => $listing_owner_id]);
        $users_to_notify = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Send email notifications using Rust Mailer
        $mailer = new RustMailer();
        $sent_count = 0;
        
        foreach ($users_to_notify as $user) {
            $listingTitle = ($listing['glass_type'] ?? 'Glass') . ' - ' . ($listing['quantity_tons'] ?? '0') . ' tons';
            $listingUrl = 'http://localhost/glass-market/resources/views/listing-detail.php?id=' . $listing_id;
            
            $result = $mailer->sendListingNotification(
                $user['email'],
                $listingTitle,
                $listingUrl
            );
            
            if ($result['success']) {
                $sent_count++;
            }
        }
        
        // Log notification
        error_log("Sent {$sent_count} email notifications for listing #{$listing_id}");
        
        // Send push notifications to users with push enabled
        sendPushNotifications($pdo, $listing, $listing_owner_id);
        
        return $sent_count;
        
    } catch (PDOException $e) {
        error_log("Error sending notifications: " . $e->getMessage());
        return false;
    }
}

function sendPushNotifications($pdo, $listing, $listing_owner_id) {
    try {
        // Get users with push notifications enabled
        $stmt = $pdo->prepare('
            SELECT id, name, email 
            FROM users 
            WHERE push_new_listings = 1 
            AND id != :listing_owner_id
        ');
        $stmt->execute(['listing_owner_id' => $listing_owner_id]);
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Store push notifications in database for delivery via JavaScript
        foreach ($users as $user) {
            $stmt = $pdo->prepare('
                INSERT INTO push_notifications (user_id, title, body, url, created_at)
                VALUES (:user_id, :title, :body, :url, NOW())
            ');
            
            $stmt->execute([
                'user_id' => $user['id'],
                'title' => '🔔 New Listing: ' . $listing['title'],
                'body' => ($listing['glass_type'] ?? 'Glass') . ' - ' . ($listing['tons'] ?? '') . ' tons',
                'url' => BASE_URL . '/listing.php?id=' . $listing['id']
            ]);
        }
        
        return true;
    } catch (PDOException $e) {
        // Table might not exist yet
        createPushNotificationsTable($pdo);
        return false;
    }
}

function createPushNotificationsTable($pdo) {
    try {
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS push_notifications (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                title VARCHAR(255) NOT NULL,
                body TEXT,
                url VARCHAR(500),
                is_read TINYINT(1) DEFAULT 0,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_user_id (user_id),
                INDEX idx_is_read (is_read)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");
    } catch (PDOException $e) {
        error_log("Error creating push_notifications table: " . $e->getMessage());
    }
}
