<?php
session_start();
require_once __DIR__ . '/../../config.php';

header('Content-Type: application/json');

// Check authentication
if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Please login to save listings']);
    exit;
}

$user_id = $_SESSION['user_id'];

// Database credentials
$db_host = '127.0.0.1';
$db_name = 'glass_market';
$db_user = 'root';
$db_pass = '';

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Get listing_id from request
    $input = json_decode(file_get_contents('php://input'), true);
    $listing_id = $input['listing_id'] ?? null;
    
    if (!$listing_id || !is_numeric($listing_id)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid listing ID']);
        exit;
    }
    
    // Check if listing exists
    $stmt = $pdo->prepare('SELECT id FROM listings WHERE id = ?');
    $stmt->execute([$listing_id]);
    if (!$stmt->fetch()) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Listing not found']);
        exit;
    }
    
    // Check if already saved
    $stmt = $pdo->prepare('SELECT id FROM saved_listings WHERE user_id = ? AND listing_id = ?');
    $stmt->execute([$user_id, $listing_id]);
    $existing = $stmt->fetch();
    
    if ($existing) {
        // Remove from saved
        $stmt = $pdo->prepare('DELETE FROM saved_listings WHERE user_id = ? AND listing_id = ?');
        $stmt->execute([$user_id, $listing_id]);
        echo json_encode(['success' => true, 'saved' => false, 'message' => 'Listing removed from saved']);
    } else {
        // Add to saved
        $stmt = $pdo->prepare('INSERT INTO saved_listings (user_id, listing_id) VALUES (?, ?)');
        $stmt->execute([$user_id, $listing_id]);
        echo json_encode(['success' => true, 'saved' => true, 'message' => 'Listing saved successfully']);
    }
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
