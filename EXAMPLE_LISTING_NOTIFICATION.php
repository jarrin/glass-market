<?php
/**
 * EXAMPLE: How to send notifications when creating a listing
 * 
 * Add this code to your add-listing.php or wherever listings are created
 */

// After successfully inserting a listing into the database:

// Example from your listing creation code:
// $stmt = $pdo->prepare('INSERT INTO listings (...) VALUES (...)');
// $stmt->execute([...]);
// $listing_id = $pdo->lastInsertId();

// Then add these lines:

try {
    // Send email and push notifications to users who opted in
    require_once __DIR__ . '/../includes/notify-new-listing.php';
    notifyUsersOfNewListing($listing_id);
    
    // Optional: Log the notification
    error_log("Sent notifications for new listing ID: $listing_id");
} catch (Exception $e) {
    // Don't fail the listing creation if notifications fail
    error_log("Failed to send notifications: " . $e->getMessage());
}

// Continue with your success message
$success_message = 'Listing created successfully! Users with notifications enabled will be alerted.';

?>

<!-- FULL EXAMPLE INTEGRATION -->

<?php
session_start();
require_once __DIR__ . '/../config.php';

// ... your authentication and form validation code ...

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_listing'])) {
    // Get form data
    $title = trim($_POST['title'] ?? '');
    $glass_type = trim($_POST['glass_type'] ?? '');
    $tons = $_POST['tons'] ?? '';
    $storage_location = trim($_POST['storage_location'] ?? '');
    // ... more fields ...
    
    try {
        $pdo = new PDO("mysql:host=127.0.0.1;dbname=glass_market", 'root', '');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Insert listing
        $stmt = $pdo->prepare('
            INSERT INTO listings (title, glass_type, tons, storage_location, side, created_at)
            VALUES (:title, :glass_type, :tons, :storage_location, :side, NOW())
        ');
        
        $stmt->execute([
            'title' => $title,
            'glass_type' => $glass_type,
            'tons' => $tons,
            'storage_location' => $storage_location,
            'side' => 'WTS',
        ]);
        
        $listing_id = $pdo->lastInsertId();
        
        // ✅ SEND NOTIFICATIONS HERE ✅
        try {
            require_once __DIR__ . '/../includes/notify-new-listing.php';
            notifyUsersOfNewListing($listing_id);
        } catch (Exception $e) {
            error_log("Notification error: " . $e->getMessage());
        }
        
        $success_message = "Listing created! Subscribers have been notified.";
        
    } catch (PDOException $e) {
        $error_message = "Error: " . $e->getMessage();
    }
}
?>
