<?php
session_start();

require_once __DIR__ . '/../../../config.php';

// Require authentication
if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
    header('Location: ' . VIEWS_URL . '/login.php');
    exit;
}

// Database credentials
$db_host = '127.0.0.1';
$db_name = 'glass_market';
$db_user = 'root';
$db_pass = '';

$error_message = '';
$success_message = '';

// Get user info
$user_id = $_SESSION['user_id'] ?? null;
$listing_id = $_GET['id'] ?? 0;

// Load the listing
$listing = null;
$listing_images = [];
$company = null;

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Get user's company first
    $stmt = $pdo->prepare('SELECT * FROM companies WHERE owner_user_id = :user_id');
    $stmt->execute(['user_id' => $user_id]);
    $company = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$company) {
        $_SESSION['listing_error'] = 'You need a company to edit company listings.';
        header('Location: ' . VIEWS_URL . '/profile.php?tab=company');
        exit;
    }
    
    // Get listing and verify it belongs to user's company
    $stmt = $pdo->prepare('
        SELECT l.*, c.name as company_name
        FROM listings l
        LEFT JOIN companies c ON l.company_id = c.id
        WHERE l.id = :listing_id AND l.user_id = :user_id AND l.company_id = :company_id
    ');
    $stmt->execute([
        'listing_id' => $listing_id, 
        'user_id' => $user_id,
        'company_id' => $company['id']
    ]);
    $listing = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$listing) {
        $_SESSION['listing_error'] = 'Company listing not found or you do not have permission to edit it.';
        header('Location: ' . VIEWS_URL . '/profile.php?tab=company');
        exit;
    }
    
    // Get all images for this listing
    $stmt = $pdo->prepare('
        SELECT * FROM listing_images 
        WHERE listing_id = :listing_id 
        ORDER BY is_main DESC, display_order ASC
    ');
    $stmt->execute(['listing_id' => $listing_id]);
    $listing_images = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    $_SESSION['listing_error'] = 'Failed to load listing: ' . $e->getMessage();
    header('Location: ' . VIEWS_URL . '/profile.php?tab=company');
    exit;
}
