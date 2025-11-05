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

// Handle listing update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_listing'])) {
    $title = trim($_POST['glass_title'] ?? '');
    $glass_type = trim($_POST['glass_type'] ?? '');
    $glass_type_other = trim($_POST['glass_type_other'] ?? '');
    $tons = $_POST['glass_tons'] ?? '';
    $description = trim($_POST['glass_description'] ?? '');
    $side = $_POST['side'] ?? 'WTS';
    $price_text = trim($_POST['price_text'] ?? '');
    $recycled = $_POST['recycled'] ?? 'unknown';
    $tested = $_POST['tested'] ?? 'unknown';
    $storage_location = trim($_POST['storage_location'] ?? '');
    $currency = $_POST['currency'] ?? 'EUR';
    $published = isset($_POST['published']) ? 1 : 0;
    
    // Handle "other" glass type
    if ($glass_type === 'other' && !empty($glass_type_other)) {
        $glass_type = $glass_type_other;
    }
    
    if (empty($title) || empty($glass_type) || empty($tons)) {
        $error_message = 'Title, glass type and tonnage are required.';
    } elseif (!is_numeric($tons) || $tons <= 0) {
        $error_message = 'Please enter a valid tonnage.';
    } else {
        try {
            $pdo->beginTransaction();
            
            // Handle multiple image uploads
            if (isset($_FILES['product_images']) && !empty($_FILES['product_images']['name'][0])) {
                $upload_dir = __DIR__ . '/../../../public/uploads/listings/';
                
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0755, true);
                }
                
                $total_images = count($_FILES['product_images']['name']);
                $current_image_count = count($listing_images);
                
                if ($current_image_count + $total_images > 20) {
                    throw new Exception('Maximum 20 images allowed per listing');
                }
                
                foreach ($_FILES['product_images']['name'] as $key => $filename) {
                    if ($_FILES['product_images']['error'][$key] === UPLOAD_ERR_OK) {
                        $file_extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                        $allowed_extensions = ['jpg', 'jpeg', 'png', 'webp'];
                        
                        if (!in_array($file_extension, $allowed_extensions)) {
                            continue;
                        }
                        
                        if ($_FILES['product_images']['size'][$key] > 5 * 1024 * 1024) {
                            continue;
                        }
                        
                        $new_filename = 'listing_' . $listing_id . '_' . time() . '_' . $key . '.' . $file_extension;
                        $upload_path = $upload_dir . $new_filename;
                        
                        if (move_uploaded_file($_FILES['product_images']['tmp_name'][$key], $upload_path)) {
                            $image_path = 'uploads/listings/' . $new_filename;
                            $is_main = ($current_image_count == 0 && $key == 0) ? 1 : 0;
                            
                            $stmt = $pdo->prepare('
                                INSERT INTO listing_images (listing_id, image_path, is_main, display_order)
                                VALUES (:listing_id, :image_path, :is_main, :display_order)
                            ');
                            $stmt->execute([
                                'listing_id' => $listing_id,
                                'image_path' => $image_path,
                                'is_main' => $is_main,
                                'display_order' => $current_image_count + $key
                            ]);
                        }
                    }
                }
            }
            
            // Update listing
            $stmt = $pdo->prepare('
                UPDATE listings SET
                    side = :side,
                    glass_type = :glass_type,
                    quantity_tons = :quantity_tons,
                    quantity_note = :quantity_note,
                    quality_notes = :quality_notes,
                    price_text = :price_text,
                    recycled = :recycled,
                    tested = :tested,
                    storage_location = :storage_location,
                    currency = :currency,
                    published = :published
                WHERE id = :id
            ');
            
            $stmt->execute([
                'side' => $side,
                'glass_type' => $glass_type,
                'quantity_tons' => $tons,
                'quantity_note' => $title,
                'quality_notes' => $description,
                'price_text' => $price_text,
                'recycled' => $recycled,
                'tested' => $tested,
                'storage_location' => $storage_location,
                'currency' => $currency,
                'published' => $published,
                'id' => $listing_id
            ]);
            
            $pdo->commit();
            
            $_SESSION['listing_success'] = 'Company listing updated successfully!';
            header('Location: ' . VIEWS_URL . '/company/edit-company-listing.php?id=' . $listing_id);
            exit;
        } catch (Exception $e) {
            $pdo->rollBack();
            $error_message = 'Failed to update listing: ' . $e->getMessage();
        }
    }
}

// Check for session success message
if (isset($_SESSION['listing_success'])) {
    $success_message = $_SESSION['listing_success'];
    unset($_SESSION['listing_success']);
}

// Handle image deletion via AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_image'])) {
    header('Content-Type: application/json');
    $image_id = $_POST['image_id'] ?? 0;
    
    try {
        // Verify ownership
        $stmt = $pdo->prepare('
            SELECT li.* FROM listing_images li
            JOIN listings l ON li.listing_id = l.id
            WHERE li.id = :image_id AND l.user_id = :user_id AND l.company_id = :company_id
        ');
        $stmt->execute([
            'image_id' => $image_id,
            'user_id' => $user_id,
            'company_id' => $company['id']
        ]);
        $image = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($image) {
            $file_path = __DIR__ . '/../../../public/' . $image['image_path'];
            if (file_exists($file_path)) {
                unlink($file_path);
            }
            
            $stmt = $pdo->prepare('DELETE FROM listing_images WHERE id = :id');
            $stmt->execute(['id' => $image_id]);
            
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Image not found']);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    exit;
}

// Handle set main image via AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['set_main_image'])) {
    header('Content-Type: application/json');
    $image_id = $_POST['image_id'] ?? 0;
    
    try {
        // Verify ownership
        $stmt = $pdo->prepare('
            SELECT li.listing_id FROM listing_images li
            JOIN listings l ON li.listing_id = l.id
            WHERE li.id = :image_id AND l.user_id = :user_id AND l.company_id = :company_id
        ');
        $stmt->execute([
            'image_id' => $image_id,
            'user_id' => $user_id,
            'company_id' => $company['id']
        ]);
        $image = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($image) {
            $stmt = $pdo->prepare('UPDATE listing_images SET is_main = 0 WHERE listing_id = :listing_id');
            $stmt->execute(['listing_id' => $image['listing_id']]);
            
            $stmt = $pdo->prepare('UPDATE listing_images SET is_main = 1 WHERE id = :id');
            $stmt->execute(['id' => $image_id]);
            
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Image not found']);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    exit;
}

// Reload images after operations
try {
    $stmt = $pdo->prepare('
        SELECT * FROM listing_images 
        WHERE listing_id = :listing_id 
        ORDER BY is_main DESC, display_order ASC
    ');
    $stmt->execute(['listing_id' => $listing_id]);
    $listing_images = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Silent fail
}

// Get glass types for dropdown
$glass_types = [
    'Clear/Flint', 'Green', 'Amber/Brown', 'Blue', 'Mixed Cullet',
    'Float Glass', 'Tempered Glass', 'Laminated Glass', 'Borosilicate'
];

$current_type = $listing['glass_type'] ?? '';
$is_other = !in_array($current_type, $glass_types) && !empty($current_type);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Company Listing - Glass Market</title>
    <link rel="stylesheet" href="<?php echo CSS_URL; ?>/app.css">
