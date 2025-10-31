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
try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Get listing and verify ownership
    $stmt = $pdo->prepare('
        SELECT l.*, c.name as company_name
        FROM listings l
        LEFT JOIN companies c ON l.company_id = c.id
        LEFT JOIN users u ON c.id = u.company_id
        WHERE l.id = :listing_id AND u.id = :user_id
    ');
    $stmt->execute(['listing_id' => $listing_id, 'user_id' => $user_id]);
    $listing = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$listing) {
        $_SESSION['listing_error'] = 'Listing not found or you do not have permission to edit it.';
        header('Location: ' . VIEWS_URL . '/my-listings.php');
        exit;
    }
} catch (PDOException $e) {
    $_SESSION['listing_error'] = 'Failed to load listing: ' . $e->getMessage();
    header('Location: ' . VIEWS_URL . '/my-listings.php');
    exit;
}

// Handle listing update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_listing'])) {
    $title = trim($_POST['glass_title'] ?? '');
    $glass_type = trim($_POST['glass_type'] ?? '');
    $tons = $_POST['glass_tons'] ?? '';
    $description = trim($_POST['glass_description'] ?? '');
    $side = $_POST['side'] ?? 'WTS';
    $price_text = trim($_POST['price_text'] ?? '');
    
    if (empty($title) || empty($glass_type) || empty($tons)) {
        $error_message = 'Title, glass type and tonnage are required.';
    } elseif (!is_numeric($tons) || $tons <= 0) {
        $error_message = 'Please enter a valid tonnage.';
    } else {
        try {
            $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            $image_path = $listing['image_path']; // Keep existing image by default
            
            // Handle image upload
            if (isset($_FILES['glass_image']) && $_FILES['glass_image']['error'] === UPLOAD_ERR_OK) {
                $upload_dir = __DIR__ . '/../../public/uploads/listings/';
                
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0755, true);
                }
                
                $file_extension = strtolower(pathinfo($_FILES['glass_image']['name'], PATHINFO_EXTENSION));
                $allowed_extensions = ['jpg', 'jpeg', 'png', 'webp'];
                
                if (in_array($file_extension, $allowed_extensions)) {
                    $new_filename = 'listing_' . $listing_id . '_' . time() . '.' . $file_extension;
                    $upload_path = $upload_dir . $new_filename;
                    
                    if (move_uploaded_file($_FILES['glass_image']['tmp_name'], $upload_path)) {
                        $image_path = 'uploads/listings/' . $new_filename;
                    }
                }
            }
            
            // Map glass type to proper format
            $glass_type_mapped = ucfirst($glass_type) . ' Glass';
            
            // Update listing
            $stmt = $pdo->prepare('
                UPDATE listings SET
                    side = :side,
                    glass_type = :glass_type,
                    quantity_tons = :quantity_tons,
                    quantity_note = :quantity_note,
                    quality_notes = :quality_notes,
                    price_text = :price_text,
                    image_path = :image_path
                WHERE id = :id
            ');
            
            $stmt->execute([
                'side' => $side,
                'glass_type' => $glass_type_mapped,
                'quantity_tons' => $tons,
                'quantity_note' => $title,
                'quality_notes' => $description,
                'price_text' => $price_text,
                'image_path' => $image_path,
                'id' => $listing_id
            ]);
            
            $_SESSION['listing_success'] = 'Listing updated successfully!';
            header('Location: ' . VIEWS_URL . '/my-listings.php');
            exit;
        } catch (PDOException $e) {
            $error_message = 'Failed to update listing: ' . $e->getMessage();
        }
    }
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
        body {
            background: #f5f5f5;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 40px 20px;
        }

        .page-header {
            background: #000;
            color: white;
            padding: 30px 40px;
            border-radius: 12px;
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .page-header h1 {
            font-size: 28px;
            font-weight: 800;
            margin: 0;
        }

        .section {
            background: white;
            border-radius: 12px;
            padding: 32px;
            margin-bottom: 30px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }

        .alert {
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .alert-danger {
            background: #fef2f2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        .alert-success {
            background: #f0fdf4;
            color: #166534;
            border: 1px solid #bbf7d0;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-size: 12px;
            font-weight: 600;
            color: #222;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 12px 14px;
            font-size: 14px;
            border: 1.5px solid #ddd;
            border-radius: 6px;
            background: #fafafa;
            transition: all 0.2s ease;
            outline: none;
            font-family: inherit;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            border-color: #000;
            background: #fff;
            box-shadow: 0 0 0 3px rgba(0, 0, 0, 0.08);
        }

        .form-group textarea {
            resize: vertical;
        }

        .form-group small {
            font-size: 11px;
            color: #999;
            display: block;
            margin-top: 6px;
        }

        .btn {
            padding: 12px 24px;
            font-size: 13px;
            font-weight: 600;
            border-radius: 4px;
            cursor: pointer;
            border: none;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: all 0.2s ease;
            text-decoration: none;
            display: inline-block;
            margin-right: 8px;
        }

        .btn-primary {
            background: #000;
            color: #fff;
        }

        .btn-primary:hover {
            background: #333;
        }

        .btn-secondary {
            background: #6b7280;
            color: white;
        }

        .btn-secondary:hover {
            background: #4b5563;
        }

        .current-image {
            max-width: 200px;
            border-radius: 8px;
            border: 1px solid #ddd;
            margin-top: 12px;
        }

        .info-box {
            background: #fffbeb;
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-size: 12px;
            color: #92400e;
        }
    </style>
</head>
<body>
    <?php include __DIR__ . '/../../includes/navbar.php'; ?>
    <?php include __DIR__ . '/../../includes/subscription-notification.php'; ?>

    <main style="padding-top: 80px;">
        <div class="container">
            <div class="page-header">
                <h1>✏️ Edit Listing</h1>
                <a href="<?php echo VIEWS_URL; ?>/my-listings.php" class="btn btn-secondary">Back to My Listings</a>
            </div>

            <div class="section">
                <?php if ($error_message): ?>
                    <div class="alert alert-danger">
                        <?php echo htmlspecialchars($error_message); ?>
                    </div>
                <?php endif; ?>

                <?php if ($success_message): ?>
                    <div class="alert alert-success">
                        <?php echo htmlspecialchars($success_message); ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="glass_title">Listing Title</label>
                        <input
                            type="text"
                            id="glass_title"
                            name="glass_title"
                            value="<?php echo htmlspecialchars($listing['quantity_note'] ?? ''); ?>"
                            placeholder="e.g., Premium Green Glass - High Quality"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label for="side">Listing Type</label>
                        <select id="side" name="side" required>
                            <option value="WTS" <?php echo ($listing['side'] ?? '') === 'WTS' ? 'selected' : ''; ?>>Want To Sell (WTS)</option>
                            <option value="WTB" <?php echo ($listing['side'] ?? '') === 'WTB' ? 'selected' : ''; ?>>Want To Buy (WTB)</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="glass_type">Glass Type</label>
                        <select id="glass_type" name="glass_type" required>
                            <option value="">Select glass type...</option>
                            <?php
                                $types = ['green', 'white', 'brown', 'clear', 'mixed'];
                                $current_type = strtolower(str_replace(' Glass', '', $listing['glass_type'] ?? ''));
                                
                                foreach ($types as $type) {
                                    $selected = ($current_type === $type) ? 'selected' : '';
                                    echo "<option value=\"$type\" $selected>" . ucfirst($type) . " Glass</option>";
                                }
                            ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="glass_tons">Tonnage (Tons)</label>
                        <input
                            type="number"
                            id="glass_tons"
                            name="glass_tons"
                            value="<?php echo htmlspecialchars($listing['quantity_tons'] ?? ''); ?>"
                            placeholder="0.00"
                            step="0.01"
                            min="0"
                            required
                        >
                        <small>Specify the total weight in tons</small>
                    </div>

                    <div class="form-group">
                        <label for="glass_description">Quality Notes / Description</label>
                        <textarea
                            id="glass_description"
                            name="glass_description"
                            rows="5"
                            placeholder="Describe the glass quality, condition, source, etc..."
                        ><?php echo htmlspecialchars($listing['quality_notes'] ?? ''); ?></textarea>
                        <small>Optional - Add any quality notes or additional information</small>
                    </div>

                    <div class="form-group">
                        <label for="glass_image">Product Image</label>
                        <?php if (!empty($listing['image_path'])): ?>
                            <div style="margin-bottom: 12px;">
                                <p style="font-size: 13px; color: #666; margin-bottom: 8px;">Current image:</p>
                                <img src="<?php echo htmlspecialchars(PUBLIC_URL . '/' . $listing['image_path']); ?>" 
                                     alt="Current listing image" 
                                     class="current-image">
                            </div>
                        <?php endif; ?>
                        <input
                            type="file"
                            id="glass_image"
                            name="glass_image"
                            accept="image/jpeg,image/jpg,image/png,image/webp"
                        >
                        <small>Upload a new photo to replace the current one (JPG, PNG, or WebP - Max 5MB)</small>
                    </div>

                    <div class="info-box">
                        💡 <strong>Note:</strong> Changes will be saved immediately. Make sure all information is accurate before saving.
                    </div>

                    <div style="margin-top: 24px; display: flex; gap: 12px;">
                        <button type="submit" name="update_listing" class="btn btn-primary">Save Changes</button>
                        <a href="<?php echo VIEWS_URL; ?>/my-listings.php" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <?php include __DIR__ . '/../../includes/footer.php'; ?>
</body>
</html>
