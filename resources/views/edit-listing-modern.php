<?php
session_start();

require_once __DIR__ . '/../../config.php';

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
try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Get listing and verify ownership
    $stmt = $pdo->prepare('
        SELECT l.*, c.name as company_name
        FROM listings l
        LEFT JOIN companies c ON l.company_id = c.id
        WHERE l.id = :listing_id AND l.user_id = :user_id
    ');
    $stmt->execute(['listing_id' => $listing_id, 'user_id' => $user_id]);
    $listing = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$listing) {
        $_SESSION['listing_error'] = 'Listing not found or you do not have permission to edit it.';
        header('Location: ' . VIEWS_URL . '/profile.php?tab=listings');
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
    
    // If no images in new table but old image_path exists, migrate it
    if (empty($listing_images) && !empty($listing['image_path']) && $listing['image_path'] !== 'image.png') {
        $stmt = $pdo->prepare('
            INSERT INTO listing_images (listing_id, image_path, is_main, display_order)
            VALUES (:listing_id, :image_path, 1, 0)
        ');
        $stmt->execute([
            'listing_id' => $listing_id,
            'image_path' => $listing['image_path']
        ]);
        
        // Reload images
        $stmt = $pdo->prepare('
            SELECT * FROM listing_images 
            WHERE listing_id = :listing_id 
            ORDER BY is_main DESC, display_order ASC
        ');
        $stmt->execute(['listing_id' => $listing_id]);
        $listing_images = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
} catch (PDOException $e) {
    $_SESSION['listing_error'] = 'Failed to load listing: ' . $e->getMessage();
    header('Location: ' . VIEWS_URL . '/profile.php?tab=listings');
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
                $upload_dir = __DIR__ . '/../../public/uploads/listings/';
                
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
                            continue; // Skip files over 5MB
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
            
            // Use Post/Redirect/Get pattern to prevent form resubmission
            $_SESSION['listing_success'] = 'Listing updated successfully!';
            header('Location: ' . VIEWS_URL . '/edit-listing-modern.php?id=' . $listing_id);
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
        $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Verify ownership and get image path
        $stmt = $pdo->prepare('
            SELECT li.* FROM listing_images li
            JOIN listings l ON li.listing_id = l.id
            WHERE li.id = :image_id AND l.user_id = :user_id
        ');
        $stmt->execute(['image_id' => $image_id, 'user_id' => $user_id]);
        $image = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($image) {
            // Delete file
            $file_path = __DIR__ . '/../../public/' . $image['image_path'];
            if (file_exists($file_path)) {
                unlink($file_path);
            }
            
            // Delete from database
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
        $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Verify ownership
        $stmt = $pdo->prepare('
            SELECT li.listing_id FROM listing_images li
            JOIN listings l ON li.listing_id = l.id
            WHERE li.id = :image_id AND l.user_id = :user_id
        ');
        $stmt->execute(['image_id' => $image_id, 'user_id' => $user_id]);
        $image = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($image) {
            // Remove main from all images in this listing
            $stmt = $pdo->prepare('UPDATE listing_images SET is_main = 0 WHERE listing_id = :listing_id');
            $stmt->execute(['listing_id' => $image['listing_id']]);
            
            // Set new main image
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
    // Handle error silently
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Listing - Glass Market</title>
    <link rel="stylesheet" href="<?php echo CSS_URL; ?>/app.css">
    <style>
        :root {
            --profile-primary: #2f6df5;
            --profile-text: #1d1d1f;
            --profile-muted: #6e6e73;
            --profile-bg: #f5f5f7;
            --profile-card-bg: #ffffff;
            --profile-border: rgba(15, 23, 42, 0.08);
        }

        body {
            background: var(--profile-bg);
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            color: var(--profile-text);
            margin: 0;
            padding: 0;
        }

        .edit-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 100px 32px 60px;
        }

        .page-header {
            margin-bottom: 32px;
        }

        .page-header-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 16px;
        }

        .page-title {
            font-size: 42px;
            font-weight: 700;
            margin: 0;
            color: var(--profile-text);
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .status-badge {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-badge.published {
            background: #10b981;
            color: white;
        }

        .status-badge.draft {
            background: #f59e0b;
            color: white;
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: var(--profile-primary);
            text-decoration: none;
            font-weight: 600;
            font-size: 15px;
            transition: opacity 0.2s;
        }

        .back-link:hover {
            opacity: 0.7;
        }

        .page-subtitle {
            font-size: 17px;
            color: var(--profile-muted);
            margin: 0;
        }

        /* Tab System */
        .tabs-container {
            background: var(--profile-card-bg);
            border-radius: 16px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }

        .tabs-header {
            display: flex;
            border-bottom: 2px solid var(--profile-border);
            background: #fafafa;
            padding: 0 24px;
            overflow-x: auto;
            overflow-y: hidden;
            -webkit-overflow-scrolling: touch;
            scrollbar-width: none; /* Firefox */
        }
        
        .tabs-header::-webkit-scrollbar {
            display: none; /* Chrome, Safari, Opera */
        }

        .tab-button {
            padding: 20px 28px;
            font-size: 15px;
            font-weight: 600;
            color: var(--profile-muted);
            background: transparent;
            border: none;
            border-bottom: 3px solid transparent;
            cursor: pointer;
            transition: all 0.2s;
            white-space: nowrap;
            position: relative;
            top: 2px;
            flex-shrink: 0;
        }

        .tab-button:hover {
            color: var(--profile-text);
            background: rgba(47, 109, 245, 0.05);
        }

        .tab-button.active {
            color: var(--profile-primary);
            border-bottom-color: var(--profile-primary);
            background: white;
        }

        .tab-content {
            display: none;
            padding: 32px;
            animation: fadeIn 0.3s ease;
        }

        .tab-content.active {
            display: block;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .edit-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 32px;
        }

        @media (max-width: 968px) {
            .edit-grid {
                grid-template-columns: 1fr;
            }
            
            .tabs-header {
                overflow-x: auto;
            }
        }

        .card {
            background: var(--profile-card-bg);
            border-radius: 16px;
            padding: 32px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }

        .card-title {
            font-size: 20px;
            font-weight: 700;
            margin: 0 0 24px 0;
            color: var(--profile-text);
        }

        .form-group {
            margin-bottom: 24px;
        }

        .form-label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            color: var(--profile-text);
            margin-bottom: 8px;
        }

        .form-label .required {
            color: #ef4444;
        }

        .form-input,
        .form-select,
        .form-textarea {
            width: 100%;
            padding: 12px 16px;
            font-size: 15px;
            border: 2px solid var(--profile-border);
            border-radius: 10px;
            background: var(--profile-bg);
            transition: all 0.2s ease;
            outline: none;
            font-family: inherit;
            box-sizing: border-box;
        }

        .form-input:focus,
        .form-select:focus,
        .form-textarea:focus {
            border-color: var(--profile-primary);
            background: white;
        }

        .form-textarea {
            resize: vertical;
            min-height: 120px;
        }

        .form-hint {
            font-size: 13px;
            color: var(--profile-muted);
            margin-top: 6px;
            display: block;
        }

        .alert {
            padding: 16px 20px;
            border-radius: 12px;
            margin-bottom: 24px;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .alert-danger {
            background: #fef2f2;
            color: #991b1b;
            border: 2px solid #fecaca;
        }

        .alert-success {
            background: #f0fdf4;
            color: #166534;
            border: 2px solid #bbf7d0;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 14px 28px;
            font-size: 15px;
            font-weight: 600;
            border-radius: 10px;
            cursor: pointer;
            border: none;
            transition: all 0.2s ease;
            text-decoration: none;
            font-family: inherit;
        }

        .btn-primary {
            background: var(--profile-primary);
            color: #fff;
        }

        .btn-primary:hover {
            background: #1e5ce6;
            transform: translateY(-1px);
        }

        .btn-secondary {
            background: var(--profile-muted);
            color: white;
        }

        .btn-secondary:hover {
            background: #52525b;
        }

        .btn-danger {
            background: #ef4444;
            color: white;
        }

        .btn-danger:hover {
            background: #dc2626;
        }

        .button-group {
            display: flex;
            gap: 12px;
            margin-top: 32px;
        }

        /* Image Gallery Styles */
        .image-gallery {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
            gap: 16px;
            margin-top: 16px;
        }

        .image-item {
            position: relative;
            aspect-ratio: 1;
            border-radius: 12px;
            overflow: hidden;
            border: 3px solid #e5e7eb;
            transition: all 0.3s ease;
            cursor: pointer;
            background: #f9fafb;
        }

        .image-item:hover {
            border-color: #cbd5e1;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .image-item.main {
            border-color: var(--profile-primary);
            box-shadow: 0 0 0 4px rgba(47, 109, 245, 0.1);
            background: linear-gradient(135deg, rgba(47, 109, 245, 0.05) 0%, rgba(47, 109, 245, 0.02) 100%);
        }
        
        .image-item.main:hover {
            border-color: #1e5ce6;
            box-shadow: 0 0 0 4px rgba(47, 109, 245, 0.15), 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .image-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .image-item-badge {
            position: absolute;
            top: 8px;
            left: 8px;
            background: var(--profile-primary);
            color: white;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            box-shadow: 0 2px 8px rgba(47, 109, 245, 0.3);
            z-index: 2;
        }

        .image-item-actions {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: linear-gradient(to top, rgba(0,0,0,0.9) 0%, rgba(0,0,0,0.7) 70%, transparent 100%);
            padding: 40px 8px 8px;
            display: flex;
            gap: 6px;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .image-item:hover .image-item-actions {
            opacity: 1;
        }

        .image-action-btn {
            flex: 1;
            background: white;
            border: none;
            padding: 8px 6px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 11px;
            font-weight: 700;
            transition: all 0.2s;
            color: #1f2937;
        }

        .image-action-btn:hover {
            transform: scale(1.05);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
        }

        .image-action-btn.delete {
            background: #ef4444;
            color: white;
        }
        
        .image-action-btn.delete:hover {
            background: #dc2626;
        }

        .upload-zone {
            border: 3px dashed var(--profile-border);
            border-radius: 16px;
            padding: 40px 20px;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s;
            background: var(--profile-bg);
        }

        .upload-zone:hover {
            border-color: var(--profile-primary);
            background: rgba(47, 109, 245, 0.03);
        }

        .upload-zone-icon {
            font-size: 48px;
            margin-bottom: 12px;
        }

        .upload-zone-text {
            font-size: 15px;
            font-weight: 600;
            color: var(--profile-text);
            margin-bottom: 4px;
        }

        .upload-zone-hint {
            font-size: 13px;
            color: var(--profile-muted);
        }

        .image-count {
            font-size: 13px;
            color: var(--profile-muted);
            margin-bottom: 12px;
        }

        .danger-zone {
            margin-top: 40px;
            padding-top: 32px;
            border-top: 2px solid #fecaca;
        }

        .danger-zone-title {
            font-size: 18px;
            font-weight: 700;
            color: #dc2626;
            margin: 0 0 12px 0;
        }

        .danger-zone-text {
            font-size: 14px;
            color: var(--profile-muted);
            margin-bottom: 16px;
        }

        .input-group {
            display: grid;
            grid-template-columns: 1fr auto;
            gap: 12px;
        }

        /* New Image UI Styles */
        .images-container {
            max-width: 1000px;
            margin: 0 auto;
        }

        .images-header {
            margin-bottom: 32px;
            padding-bottom: 24px;
            border-bottom: 2px solid var(--profile-border);
        }

        .images-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
            gap: 16px;
            margin-bottom: 32px;
        }

        .image-card {
            background: white;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            overflow: hidden;
            transition: all 0.2s ease;
        }

        .image-card:hover {
            border-color: #cbd5e1;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }

        .image-card.is-main {
            border-color: var(--profile-primary);
            box-shadow: 0 0 0 3px rgba(47, 109, 245, 0.1);
        }

        .image-card.is-main:hover {
            box-shadow: 0 0 0 3px rgba(47, 109, 245, 0.15), 0 4px 12px rgba(0, 0, 0, 0.08);
        }

        .image-card-img {
            position: relative;
            aspect-ratio: 1;
            overflow: hidden;
            background: #f9fafb;
        }

        .image-card-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .main-badge {
            position: absolute;
            top: 8px;
            left: 8px;
            background: var(--profile-primary);
            color: white;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 4px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
        }

        .image-card-actions {
            padding: 8px;
            display: flex;
            gap: 6px;
            background: #fafafa;
        }

        .image-card-btn {
            flex: 1;
            padding: 8px 12px;
            font-size: 12px;
            font-weight: 600;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            background: white;
            color: #374151;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }

        .image-card-btn:hover {
            background: #f9fafb;
            border-color: #9ca3af;
        }

        .image-card-btn.delete-btn {
            background: #fef2f2;
            border-color: #fecaca;
            color: #dc2626;
        }

        .image-card-btn.delete-btn:hover {
            background: #fee2e2;
            border-color: #fca5a5;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: var(--profile-muted);
        }

        .empty-state svg {
            margin-bottom: 16px;
            opacity: 0.3;
        }

        .empty-state h4 {
            font-size: 18px;
            font-weight: 600;
            margin: 0 0 8px 0;
            color: var(--profile-text);
        }

        .empty-state p {
            margin: 0;
            font-size: 14px;
        }

        .upload-section {
            margin-top: 32px;
            padding-top: 32px;
            border-top: 2px solid var(--profile-border);
        }

        .upload-box {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 12px;
            padding: 48px 24px;
            border: 3px dashed #d1d5db;
            border-radius: 16px;
            background: #fafafa;
            cursor: pointer;
            transition: all 0.2s;
            text-align: center;
        }

        .upload-box:hover {
            border-color: var(--profile-primary);
            background: rgba(47, 109, 245, 0.03);
        }

        .upload-box svg {
            color: var(--profile-muted);
        }

        .upload-box strong {
            font-size: 15px;
            color: var(--profile-text);
        }

        .upload-box span {
            font-size: 13px;
            color: var(--profile-muted);
        }

        #preview-container {
            margin-top: 16px;
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
            gap: 16px;
        }

        /* Status Toggle */
        .status-toggle {
            background: #f9fafb;
            border: 2px solid var(--profile-border);
            border-radius: 12px;
            padding: 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .status-toggle-label {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .status-toggle-label strong {
            font-size: 15px;
            color: var(--profile-text);
        }

        .status-toggle-label span {
            font-size: 13px;
            color: var(--profile-muted);
        }

        .toggle-switch {
            position: relative;
            width: 56px;
            height: 32px;
        }

        .toggle-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .toggle-slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #cbd5e1;
            border-radius: 32px;
            transition: 0.3s;
        }

        .toggle-slider:before {
            position: absolute;
            content: "";
            height: 24px;
            width: 24px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            border-radius: 50%;
            transition: 0.3s;
        }

        .toggle-switch input:checked + .toggle-slider {
            background-color: #22c55e;
        }

        .toggle-switch input:checked + .toggle-slider:before {
            transform: translateX(24px);
        }
    </style>
</head>
<body>
    <?php include __DIR__ . '/../../includes/navbar.php'; ?>
    <?php include __DIR__ . '/../../includes/subscription-notification.php'; ?>

    <!-- Toast Container -->
    <div id="toast-container" style="position: fixed; top: 100px; right: 20px; z-index: 99999;"></div>

    <main class="edit-container">
        <div class="page-header">
            <div class="page-header-top">
                <h1 class="page-title">
                    Edit Listing
                    <span class="status-badge <?php echo $listing['published'] == 1 ? 'published' : 'draft'; ?>">
                        <?php echo $listing['published'] == 1 ? 'Published' : 'Draft'; ?>
                    </span>
                </h1>
                <a href="<?php echo VIEWS_URL; ?>/profile.php?tab=listings" class="back-link">
                    ← Back to My Listings
                </a>
            </div>
            <p class="page-subtitle">Update your listing details and manage product images</p>
        </div>

        <?php if ($error_message): ?>
            <div class="alert alert-danger">
                <span style="font-size: 20px;">⚠️</span>
                <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>

        <?php if ($success_message): ?>
            <div class="alert alert-success">
                <span style="font-size: 20px;">✓</span>
                <?php echo htmlspecialchars($success_message); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="" enctype="multipart/form-data">
            <!-- Tab System -->
            <div class="tabs-container">
                <div class="tabs-header">
                    <button type="button" class="tab-button active" onclick="switchTab('details')">
                        📝 Listing Details
                    </button>
                    <button type="button" class="tab-button" onclick="switchTab('images')">
                        📸 Product Images
                    </button>
                    <button type="button" class="tab-button" onclick="switchTab('danger')">
                        �️ Delete Listing
                    </button>
                </div>

                <!-- Tab: Listing Details -->
                <div id="tab-details" class="tab-content active">
                    <!-- Status Toggle -->
                    <div class="status-toggle" style="margin-bottom: 32px;">
                        <div class="status-toggle-label">
                            <strong>Listing Status</strong>
                            <span id="status-text"><?php echo $listing['published'] == 1 ? 'Published - Visible to buyers' : 'Draft - Hidden from buyers'; ?></span>
                        </div>
                        <label class="toggle-switch">
                            <input type="checkbox" name="published" value="1" 
                                   <?php echo $listing['published'] == 1 ? 'checked' : ''; ?>
                                   onchange="updateStatusText(this)">
                            <span class="toggle-slider"></span>
                        </label>
                    </div>

                    <div class="edit-grid">

                        <div class="form-group">
                            <label class="form-label" for="side">
                                Listing Type <span class="required">*</span>
                            </label>
                            <select id="side" name="side" class="form-select" required>
                                <option value="WTS" <?php echo ($listing['side'] ?? '') === 'WTS' ? 'selected' : ''; ?>>Want To Sell (WTS)</option>
                                <option value="WTB" <?php echo ($listing['side'] ?? '') === 'WTB' ? 'selected' : ''; ?>>Want To Buy (WTB)</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="glass_type">
                                Type of Glass <span class="required">*</span>
                            </label>
                            <select id="glass_type" name="glass_type" class="form-select" onchange="toggleOtherGlassType(this)" required>
                                <option value="">Select glass type...</option>
                                <?php
                                    $glass_types = ['Clear Cullet', 'Green Cullet', 'Brown Cullet', 'Amber Cullet', 'Mixed Cullet', 'Flint Cullet'];
                                    $current_type = str_replace(' Glass', '', $listing['glass_type'] ?? '');
                                    $is_other = !in_array($current_type, $glass_types) && !empty($current_type);
                                    
                                    foreach ($glass_types as $type) {
                                        $selected = ($current_type === $type) ? 'selected' : '';
                                        echo "<option value=\"$type\" $selected>$type</option>";
                                    }
                                    
                                    $other_selected = $is_other ? 'selected' : '';
                                    echo "<option value=\"other\" $other_selected>Other (specify)</option>";
                                ?>
                            </select>
                        </div>

                        <div class="form-group" id="glass_type_other_container" style="<?php echo $is_other ? '' : 'display: none;'; ?>">
                            <label class="form-label" for="glass_type_other">Specify Glass Type</label>
                            <input
                                type="text"
                                id="glass_type_other"
                                name="glass_type_other"
                                class="form-input"
                                value="<?php echo $is_other ? htmlspecialchars($current_type) : ''; ?>"
                                placeholder="Enter custom glass type"
                            >
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="glass_title">
                                Listing Title <span class="required">*</span>
                            </label>
                            <input
                                type="text"
                                id="glass_title"
                                name="glass_title"
                                class="form-input"
                                value="<?php echo htmlspecialchars($listing['quantity_note'] ?? ''); ?>"
                                placeholder="e.g., Premium Green Cullet - High Quality"
                                required
                            >
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="glass_tons">
                                Quantity (in tons) <span class="required">*</span>
                            </label>
                            <input
                                type="number"
                                id="glass_tons"
                                name="glass_tons"
                                class="form-input"
                                value="<?php echo htmlspecialchars($listing['quantity_tons'] ?? ''); ?>"
                                placeholder="0.00"
                                step="0.01"
                                min="0"
                                required
                            >
                            <small class="form-hint">Specify the total weight in tons</small>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="recycled">Recycled Status</label>
                            <select id="recycled" name="recycled" class="form-select" required>
                                <option value="recycled" <?php echo ($listing['recycled'] ?? 'unknown') === 'recycled' ? 'selected' : ''; ?>>Recycled</option>
                                <option value="not_recycled" <?php echo ($listing['recycled'] ?? 'unknown') === 'not_recycled' ? 'selected' : ''; ?>>Not Recycled</option>
                                <option value="unknown" <?php echo ($listing['recycled'] ?? 'unknown') === 'unknown' ? 'selected' : ''; ?>>Unknown</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="tested">Testing Status</label>
                            <select id="tested" name="tested" class="form-select" required>
                                <option value="tested" <?php echo ($listing['tested'] ?? 'unknown') === 'tested' ? 'selected' : ''; ?>>Tested</option>
                                <option value="untested" <?php echo ($listing['tested'] ?? 'unknown') === 'untested' ? 'selected' : ''; ?>>Untested</option>
                                <option value="unknown" <?php echo ($listing['tested'] ?? 'unknown') === 'unknown' ? 'selected' : ''; ?>>Unknown</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="storage_location">Storage Location</label>
                            <input
                                type="text"
                                id="storage_location"
                                name="storage_location"
                                class="form-input"
                                value="<?php echo htmlspecialchars($listing['storage_location'] ?? ''); ?>"
                                placeholder="e.g., Rotterdam warehouse, Dock 5"
                            >
                            <small class="form-hint">Where is the glass currently stored?</small>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="price_text">Price</label>
                            <div class="input-group">
                                <input
                                    type="text"
                                    id="price_text"
                                    name="price_text"
                                    class="form-input"
                                    value="<?php echo htmlspecialchars($listing['price_text'] ?? ''); ?>"
                                    placeholder="e.g., €120/ton CIF or Negotiable"
                                >
                                <select id="currency" name="currency" class="form-select" style="width: 120px;">
                                    <option value="EUR" <?php echo ($listing['currency'] ?? 'EUR') === 'EUR' ? 'selected' : ''; ?>>EUR (€)</option>
                                    <option value="USD" <?php echo ($listing['currency'] ?? 'EUR') === 'USD' ? 'selected' : ''; ?>>USD ($)</option>
                                    <option value="GBP" <?php echo ($listing['currency'] ?? 'EUR') === 'GBP' ? 'selected' : ''; ?>>GBP (£)</option>
                                    <option value="CNY" <?php echo ($listing['currency'] ?? 'EUR') === 'CNY' ? 'selected' : ''; ?>>CNY (¥)</option>
                                </select>
                            </div>
                            <small class="form-hint">Enter price or leave blank for negotiation</small>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="glass_description">Quality Notes / Description</label>
                            <textarea
                                id="glass_description"
                                name="glass_description"
                                class="form-textarea"
                                placeholder="Describe the glass quality, condition, source, etc..."
                            ><?php echo htmlspecialchars($listing['quality_notes'] ?? ''); ?></textarea>
                            <small class="form-hint">Add any quality notes or additional information</small>
                        </div>
                    </div>

                    <!-- Save Button -->
                    <div class="button-group">
                        <button type="submit" name="update_listing" class="btn btn-primary">
                            💾 Save Changes
                        </button>
                    </div>
                </div>
                <!-- Tab: Images -->
                <div id="tab-images" class="tab-content">
                    <div class="images-container">
                        <div class="images-header">
                            <div>
                                <h3 style="margin: 0 0 8px 0; font-size: 20px; font-weight: 700;">Product Images</h3>
                                <p style="margin: 0; color: var(--profile-muted); font-size: 14px;">
                                    <?php echo count($listing_images); ?> of 20 images • Main image appears first in search
                                </p>
                            </div>
                        </div>

                        <!-- Current Images -->
                        <?php if (!empty($listing_images)): ?>
                            <div class="images-grid">
                                <?php foreach ($listing_images as $img): ?>
                                    <div class="image-card <?php echo $img['is_main'] ? 'is-main' : ''; ?>" data-image-id="<?php echo $img['id']; ?>">
                                        <div class="image-card-img">
                                            <img src="<?php echo PUBLIC_URL . '/' . $img['image_path']; ?>" alt="Product image">
                                            <?php if ($img['is_main']): ?>
                                                <div class="main-badge">
                                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor">
                                                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                                    </svg>
                                                    Main
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="image-card-actions">
                                            <?php if (!$img['is_main']): ?>
                                                <button type="button" class="image-card-btn" onclick="setMainImage(<?php echo $img['id']; ?>)">
                                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                                    </svg>
                                                    Set Main
                                                </button>
                                            <?php endif; ?>
                                            <button type="button" class="image-card-btn delete-btn" onclick="deleteImage(<?php echo $img['id']; ?>)">
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path d="M3 6h18M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2"/>
                                                </svg>
                                                Delete
                                            </button>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="empty-state">
                                <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                                    <circle cx="8.5" cy="8.5" r="1.5"/>
                                    <path d="M21 15l-5-5L5 21"/>
                                </svg>
                                <h4>No images yet</h4>
                                <p>Upload at least one image to showcase your product</p>
                            </div>
                        <?php endif; ?>

                        <!-- Upload New Images -->
                        <?php if (count($listing_images) < 20): ?>
                            <div class="upload-section">
                                <label for="product_images" class="upload-box">
                                    <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                        <path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4M17 8l-5-5-5 5M12 3v12"/>
                                    </svg>
                                    <strong>Click to upload images</strong>
                                    <span>JPG, PNG, WebP (Max 5MB each)</span>
                                    <input
                                        type="file"
                                        id="product_images"
                                        name="product_images[]"
                                        accept="image/jpeg,image/jpg,image/png,image/webp"
                                        multiple
                                        style="display: none;"
                                        onchange="previewImages(this)"
                                    >
                                </label>
                                <div id="preview-container"></div>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-danger">
                                Maximum 20 images reached. Delete some images to upload new ones.
                            </div>
                        <?php endif; ?>

                        <!-- Save Button -->
                        <div class="button-group">
                            <button type="submit" name="update_listing" class="btn btn-primary" style="width: 100%;">
                                💾 Save Changes
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Tab: Danger Zone -->
                <div id="tab-danger" class="tab-content">
                    <div style="max-width: 600px; margin: 0 auto;">
                        <div style="background: #fef2f2; border-left: 4px solid #ef4444; padding: 20px; border-radius: 12px;">
                            <h3 style="font-size: 24px; font-weight: 700; color: #dc2626; margin: 0 0 16px 0;">
                                🗑️ Delete Listing
                            </h3>
                            <p style="font-size: 15px; color: #7f1d1d; margin-bottom: 20px; line-height: 1.6;">
                                Deleting this listing will permanently remove it from Glass Market. This action <strong>cannot be undone</strong>.
                            </p>
                            <p style="font-size: 14px; color: #991b1b; margin-bottom: 24px;">
                                <strong>What will be deleted:</strong><br>
                                • The listing and all its details<br>
                                • All uploaded product images<br>
                                • Saved listings from other users<br>
                                • Any associated data
                            </p>
                        </div>

                        <div style="margin-top: 24px; padding: 20px; background: white; border-radius: 12px; border: 2px solid #fecaca;">
                            <p style="font-size: 14px; color: #6b7280; margin-bottom: 16px;">
                                Are you absolutely sure? This action is permanent and cannot be reversed.
                            </p>
                            <form method="POST" action="<?php echo VIEWS_URL; ?>/profile.php?tab=listings" 
                                  onsubmit="return confirm('⚠️ FINAL WARNING ⚠️\n\nAre you absolutely sure you want to permanently delete this listing?\n\nThis action CANNOT be undone!\n\nClick OK to delete forever, or Cancel to keep the listing.');"
                                  style="display: inline;">
                                <input type="hidden" name="delete_listing" value="1">
                                <input type="hidden" name="listing_id" value="<?php echo $listing_id; ?>">
                                <button type="submit" class="btn btn-danger" style="width: 100%; padding: 16px; font-size: 16px;">
                                    �️ Delete Listing Permanently
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

            </div>
        </form>
    </main>

    <?php include __DIR__ . '/../../includes/footer.php'; ?>

    <script>
    // Update Status Text
    function updateStatusText(checkbox) {
        const statusText = document.getElementById('status-text');
        if (checkbox.checked) {
            statusText.textContent = 'Published - Visible to buyers';
        } else {
            statusText.textContent = 'Draft - Hidden from buyers';
        }
    }

    // Tab Switching Function
    function switchTab(tabName) {
        // Hide all tab contents
        document.querySelectorAll('.tab-content').forEach(content => {
            content.classList.remove('active');
        });
        
        // Remove active class from all tab buttons
        document.querySelectorAll('.tab-button').forEach(button => {
            button.classList.remove('active');
        });
        
        // Show selected tab content
        const selectedTab = document.getElementById('tab-' + tabName);
        if (selectedTab) {
            selectedTab.classList.add('active');
        }
        
        // Add active class to clicked button
        const clickedButton = Array.from(document.querySelectorAll('.tab-button')).find(btn => {
            return btn.textContent.includes(tabName === 'details' ? 'Listing Details' : 
                   tabName === 'images' ? 'Product Images' : 'Delete Listing');
        });
        if (clickedButton) {
            clickedButton.classList.add('active');
        }
        
        // Scroll to top of tabs
        const tabsContainer = document.querySelector('.tabs-container');
        if (tabsContainer) {
            window.scrollTo({
                top: tabsContainer.offsetTop - 100,
                behavior: 'smooth'
            });
        }
    }
    
    function toggleOtherGlassType(select) {
        const container = document.getElementById('glass_type_other_container');
        const input = document.getElementById('glass_type_other');
        
        if (select.value === 'other') {
            container.style.display = 'block';
            input.required = true;
        } else {
            container.style.display = 'none';
            input.required = false;
            input.value = '';
        }
    }

    function previewImages(input) {
        const container = document.getElementById('preview-container');
        container.innerHTML = '';
        
        if (input.files) {
            const fileCount = input.files.length;
            const currentCount = document.querySelectorAll('.image-item').length;
            
            if (currentCount + fileCount > 20) {
                alert(`You can only upload ${20 - currentCount} more images. Maximum is 20 images per listing.`);
                input.value = '';
                return;
            }
            
            Array.from(input.files).forEach((file, index) => {
                if (file.size > 5 * 1024 * 1024) {
                    alert(`File "${file.name}" is too large. Maximum size is 5MB.`);
                    return;
                }
                
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.createElement('div');
                    preview.style.cssText = 'margin-top: 8px; padding: 12px; background: #f0fdf4; border-radius: 8px; font-size: 13px; color: #166534;';
                    preview.textContent = `✓ ${file.name} ready to upload`;
                    container.appendChild(preview);
                };
                reader.readAsDataURL(file);
            });
        }
    }

    async function deleteImage(imageId) {
        if (!confirm('Are you sure you want to delete this image?\n\nThis action cannot be undone.')) {
            return;
        }
        
        // Show loading state
        const imageItem = document.querySelector(`[data-image-id="${imageId}"]`);
        if (imageItem) {
            imageItem.style.opacity = '0.5';
            imageItem.style.pointerEvents = 'none';
        }
        
        try {
            const formData = new FormData();
            formData.append('delete_image', '1');
            formData.append('image_id', imageId);
            
            const response = await fetch(window.location.href, {
                method: 'POST',
                body: formData
            });
            
            const result = await response.json();
            
            if (result.success) {
                showToast('Image deleted successfully!', 'success');
                // Use replace to prevent form resubmission
                setTimeout(() => {
                    window.location.replace(window.location.pathname + '?id=<?php echo $listing_id; ?>&tab=images');
                }, 500);
            } else {
                showToast('Error: ' + (result.error || 'Failed to delete image'), 'error');
                if (imageItem) {
                    imageItem.style.opacity = '1';
                    imageItem.style.pointerEvents = 'auto';
                }
            }
        } catch (error) {
            showToast('Error deleting image: ' + error.message, 'error');
            if (imageItem) {
                imageItem.style.opacity = '1';
                imageItem.style.pointerEvents = 'auto';
            }
        }
    }

    async function setMainImage(imageId) {
        // Show loading state
        const allItems = document.querySelectorAll('.image-item');
        allItems.forEach(item => {
            item.style.pointerEvents = 'none';
            item.style.opacity = '0.6';
        });
        
        try {
            const formData = new FormData();
            formData.append('set_main_image', '1');
            formData.append('image_id', imageId);
            
            const response = await fetch(window.location.href, {
                method: 'POST',
                body: formData
            });
            
            const result = await response.json();
            
            if (result.success) {
                showToast('Main image updated!', 'success');
                // Use replace to prevent form resubmission
                setTimeout(() => {
                    window.location.replace(window.location.pathname + '?id=<?php echo $listing_id; ?>&tab=images');
                }, 500);
            } else {
                showToast('Error: ' + (result.error || 'Failed to set main image'), 'error');
                allItems.forEach(item => {
                    item.style.pointerEvents = 'auto';
                    item.style.opacity = '1';
                });
            }
        } catch (error) {
            showToast('Error setting main image: ' + error.message, 'error');
            allItems.forEach(item => {
                item.style.pointerEvents = 'auto';
                item.style.opacity = '1';
            });
        }
    }
    
    // Toast Notification System
    function showToast(message, type = 'success') {
        const container = document.getElementById('toast-container');
        const toast = document.createElement('div');
        
        const icons = {
            success: '✓',
            error: '✕',
            info: 'ℹ'
        };
        
        const colors = {
            success: '#10b981',
            error: '#ef4444',
            info: '#3b82f6'
        };
        
        toast.style.cssText = `
            background: white;
            border-left: 4px solid ${colors[type] || colors.info};
            padding: 16px 20px;
            margin-bottom: 10px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            display: flex;
            align-items: center;
            gap: 12px;
            min-width: 300px;
            max-width: 400px;
            animation: slideIn 0.3s ease-out;
        `;
        
        toast.innerHTML = `
            <span style="
                background: ${colors[type] || colors.info};
                color: white;
                width: 24px;
                height: 24px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                font-weight: bold;
                flex-shrink: 0;
            ">${icons[type] || icons.info}</span>
            <span style="flex: 1; color: #1f2937; font-size: 14px;">${message}</span>
        `;
        
        container.appendChild(toast);
        
        setTimeout(() => {
            toast.style.animation = 'slideOut 0.3s ease-in';
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }
    
    // Add animation styles
    const style = document.createElement('style');
    style.textContent = `
        @keyframes slideIn {
            from {
                transform: translateX(400px);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        
        @keyframes slideOut {
            from {
                transform: translateX(0);
                opacity: 1;
            }
            to {
                transform: translateX(400px);
                opacity: 0;
            }
        }
    `;
    document.head.appendChild(style);

    // Check URL for tab parameter and switch to it
    window.addEventListener('DOMContentLoaded', function() {
        const urlParams = new URLSearchParams(window.location.search);
        const tab = urlParams.get('tab');
        if (tab) {
            switchTab(tab);
        }

        // Clear file input to prevent accidental resubmission
        const fileInput = document.getElementById('product_images');
        if (fileInput) {
            fileInput.value = '';
        }
        const previewContainer = document.getElementById('preview-container');
        if (previewContainer) {
            previewContainer.innerHTML = '';
        }

        // Remove duplicate image cards (safety measure)
        const imageCards = document.querySelectorAll('.image-card');
        const seenIds = new Set();
        imageCards.forEach(card => {
            const imageId = card.getAttribute('data-image-id');
            if (seenIds.has(imageId)) {
                card.remove();
            } else {
                seenIds.add(imageId);
            }
        });
    });
    </script>
</body>
</html>
