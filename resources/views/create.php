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

// Get user's company ID (optional - can be null for personal listings)
$company_id = null;
try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $stmt = $pdo->prepare('SELECT company_id FROM users WHERE id = :user_id');
    $stmt->execute(['user_id' => $user_id]);
    $user_data = $stmt->fetch(PDO::FETCH_ASSOC);
    $company_id = $user_data['company_id'] ?? null;
    
    // Company is optional - listings can be personal (company_id = NULL) or company-based
} catch (PDOException $e) {
    $error_message = 'Database error: ' . $e->getMessage();
}

// Handle listing creation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_listing'])) {
    $title = trim($_POST['glass_title'] ?? '');
    $glass_type = trim($_POST['glass_type'] ?? '');
    $tons = $_POST['glass_tons'] ?? '';
    $description = trim($_POST['glass_description'] ?? '');
    $side = $_POST['side'] ?? 'WTS';
    $price_text = trim($_POST['price_text'] ?? '');
    $recycled = $_POST['recycled'] ?? 'unknown';
    $tested = $_POST['tested'] ?? 'unknown';
    $published = isset($_POST['publish']) ? 1 : 0;
    
    if (empty($title) || empty($glass_type) || empty($tons)) {
        $error_message = 'Title, glass type and tonnage are required.';
    } elseif (!is_numeric($tons) || $tons <= 0) {
        $error_message = 'Please enter a valid tonnage.';
    } else {
        try {
            $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            $pdo->beginTransaction();
            
            $image_path = 'image.png'; // Default image - kept for backwards compatibility
            
            // Map glass type to proper format
            $glass_type_mapped = ucfirst($glass_type) . ' Glass';
            
            // Insert new listing
            $stmt = $pdo->prepare('
                INSERT INTO listings (
                    company_id,
                    user_id,
                    side,
                    glass_type,
                    quantity_tons,
                    quantity_note,
                    quality_notes,
                    price_text,
                    image_path,
                    recycled,
                    tested,
                    published,
                    currency,
                    created_at
                ) VALUES (
                    :company_id,
                    :user_id,
                    :side,
                    :glass_type,
                    :quantity_tons,
                    :quantity_note,
                    :quality_notes,
                    :price_text,
                    :image_path,
                    :recycled,
                    :tested,
                    :published,
                    :currency,
                    NOW()
                )
            ');
            
            $stmt->execute([
                'company_id' => $company_id,
                'user_id' => $user_id,
                'side' => $side,
                'glass_type' => $glass_type_mapped,
                'quantity_tons' => $tons,
                'quantity_note' => $title,
                'quality_notes' => $description,
                'price_text' => $price_text,
                'image_path' => $image_path,
                'recycled' => $recycled,
                'tested' => $tested,
                'published' => $published,
                'currency' => 'EUR'
            ]);
            
            $listing_id = $pdo->lastInsertId();
            
            // Handle multiple image uploads
            if (isset($_FILES['product_images']) && !empty($_FILES['product_images']['name'][0])) {
                $upload_dir = __DIR__ . '/../../public/uploads/listings/';
                
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0755, true);
                }
                
                $uploaded_count = 0;
                foreach ($_FILES['product_images']['name'] as $key => $filename) {
                    if ($uploaded_count >= 20) break; // Max 20 images
                    
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
                            $image_path_db = 'uploads/listings/' . $new_filename;
                            $is_main = ($uploaded_count == 0) ? 1 : 0; // First image is main
                            
                            $stmt = $pdo->prepare('
                                INSERT INTO listing_images (listing_id, image_path, is_main, display_order)
                                VALUES (:listing_id, :image_path, :is_main, :display_order)
                            ');
                            $stmt->execute([
                                'listing_id' => $listing_id,
                                'image_path' => $image_path_db,
                                'is_main' => $is_main,
                                'display_order' => $uploaded_count
                            ]);
                            
                            $uploaded_count++;
                        }
                    }
                }
            }
            
            $pdo->commit();
            
            $_SESSION['listing_success' ] = 'Listing created successfully!';
            header('Location: ' . VIEWS_URL . '/profile.php?tab=listings');
            exit;
        } catch (PDOException $e) {
            if (isset($pdo)) {
                $pdo->rollBack();
            }
            $error_message = 'Failed to create listing: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Listing - Glass Market</title>
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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            border-radius: 16px;
            margin: 100px 0 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .page-header h1 {
            margin: 0;
            font-size: 28px;
        }

        .section {
            background: white;
            padding: 40px;
            border-radius: 16px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            margin-bottom: 30px;
        }

        .section h2 {
            margin: 0 0 24px;
            font-size: 20px;
            font-weight: 600;
            color: #1f2937;
        }

        .form-group {
            margin-bottom: 24px;
        }

        .form-group label {
            display: block;
            font-weight: 600;
            color: #374151;
            margin-bottom: 8px;
            font-size: 14px;
        }

        .form-group input[type="text"],
        .form-group input[type="number"],
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e5e7eb;
            border-radius: 10px;
            font-size: 15px;
            transition: all 0.2s;
        }

        .form-group input:focus,
        .form-group textarea:focus,
        .form-group select:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .form-group textarea {
            min-height: 120px;
            resize: vertical;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .radio-group {
            display: flex;
            gap: 20px;
            margin-top: 8px;
        }

        .radio-group label {
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 500;
            cursor: pointer;
        }

        .radio-group input[type="radio"] {
            width: 18px;
            height: 18px;
            cursor: pointer;
        }

        .file-upload {
            border: 2px dashed #d1d5db;
            border-radius: 10px;
            padding: 30px;
            text-align: center;
            background: #f9fafb;
            cursor: pointer;
            transition: all 0.2s;
        }

        .file-upload:hover {
            border-color: #667eea;
            background: #f3f4f6;
        }

        .file-upload input[type="file"] {
            display: none;
        }

        .btn {
            padding: 14px 28px;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
            display: inline-block;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }

        .btn-secondary {
            background: #f3f4f6;
            color: #374151;
        }

        .btn-secondary:hover {
            background: #e5e7eb;
        }

        .alert {
            padding: 16px;
            border-radius: 12px;
            margin-bottom: 24px;
            font-size: 14px;
        }

        .alert-error {
            background: #fee2e2;
            border: 1px solid #fca5a5;
            color: #991b1b;
        }

        .alert-success {
            background: #d1fae5;
            border: 1px solid #6ee7b7;
            color: #065f46;
        }

        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-top: 20px;
            padding: 16px;
            background: #f0fdf4;
            border-radius: 10px;
            border: 2px solid #bbf7d0;
        }

        .checkbox-group input[type="checkbox"] {
            width: 20px;
            height: 20px;
            cursor: pointer;
        }

        .checkbox-group label {
            font-weight: 600;
            color: #166534;
            margin: 0;
            cursor: pointer;
        }

        @media (max-width: 768px) {
            .form-row {
                grid-template-columns: 1fr;
            }

            .page-header {
                flex-direction: column;
                gap: 16px;
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <?php include __DIR__ . '/../../includes/navbar.php'; ?>

    <div class="container">
        <div class="page-header">
            <h1>➕ Create New Listing</h1>
            <a href="<?php echo VIEWS_URL; ?>/profile.php?tab=listings" class="btn btn-secondary">Back to My Listings</a>
        </div>

        <?php if ($error_message): ?>
            <div class="alert alert-error">
                ❌ <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>

        <?php if ($success_message): ?>
            <div class="alert alert-success">
                ✅ <?php echo htmlspecialchars($success_message); ?>
            </div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <div class="section">
                <h2>📦 Basic Information</h2>
                
                <div class="form-group">
                    <label for="glass_title">Listing Title *</label>
                    <input 
                        type="text" 
                        id="glass_title" 
                        name="glass_title" 
                        placeholder="e.g., High-quality clear glass cullet"
                        required
                    >
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="glass_type">Glass Type *</label>
                        <select id="glass_type" name="glass_type" required>
                            <option value="">Select glass type...</option>
                            <option value="clear">Clear Glass</option>
                            <option value="green">Green Glass</option>
                            <option value="brown">Brown Glass</option>
                            <option value="blue">Blue Glass</option>
                            <option value="amber">Amber Glass</option>
                            <option value="other">Other Glass</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="glass_tons">Quantity (tons) *</label>
                        <input 
                            type="number" 
                            id="glass_tons" 
                            name="glass_tons" 
                            placeholder="e.g., 100"
                            step="0.01"
                            min="0.01"
                            required
                        >
                    </div>
                </div>

                <div class="form-group">
                    <label>Listing Type *</label>
                    <div class="radio-group">
                        <label>
                            <input type="radio" name="side" value="WTS" checked>
                            Want to Sell (WTS)
                        </label>
                        <label>
                            <input type="radio" name="side" value="WTB">
                            Want to Buy (WTB)
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label for="price_text">Price (optional)</label>
                    <input 
                        type="text" 
                        id="price_text" 
                        name="price_text" 
                        placeholder="e.g., €150/ton or Contact for price"
                    >
                </div>
            </div>

            <div class="section">
                <h2>📋 Details</h2>
                
                <div class="form-group">
                    <label for="glass_description">Description</label>
                    <textarea 
                        id="glass_description" 
                        name="glass_description" 
                        placeholder="Provide details about the glass quality, sorting, contamination, etc."
                    ></textarea>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="recycled">Recycled Status</label>
                        <select id="recycled" name="recycled">
                            <option value="unknown">Unknown</option>
                            <option value="recycled">Recycled</option>
                            <option value="not_recycled">Not Recycled</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="tested">Tested Status</label>
                        <select id="tested" name="tested">
                            <option value="unknown">Unknown</option>
                            <option value="tested">Tested</option>
                            <option value="untested">Untested</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="section">
                <h2>📸 Product Images</h2>
                
                <div class="form-group">
                    <label for="product_images">Upload Images (up to 20 images)</label>
                    <div class="file-upload" onclick="document.getElementById('product_images').click()">
                        <div style="font-size: 48px; margin-bottom: 12px;">📷</div>
                        <p style="margin: 0; font-weight: 600; color: #374151;">Click to upload images</p>
                        <p style="margin: 4px 0 0; font-size: 13px; color: #6b7280;">JPG, PNG, or WEBP (max 5MB each, up to 20 images)</p>
                        <input type="file" id="product_images" name="product_images[]" accept="image/jpeg,image/jpg,image/png,image/webp" multiple onchange="previewProductImages(this)">
                    </div>
                    <div id="image-preview-container" style="margin-top: 16px; display: grid; grid-template-columns: repeat(auto-fill, minmax(100px, 1fr)); gap: 12px;"></div>
                </div>
            </div>

            <div class="section">
                <div class="checkbox-group">
                    <input type="checkbox" id="publish" name="publish" checked>
                    <label for="publish">✅ Publish listing immediately (uncheck to save as draft)</label>
                </div>

                <div style="margin-top: 24px; display: flex; gap: 12px;">
                    <button type="submit" name="create_listing" class="btn btn-primary">Create Listing</button>
                    <a href="<?php echo VIEWS_URL; ?>/profile.php?tab=listings" class="btn btn-secondary">Cancel</a>
                </div>
            </div>
        </form>
    </div>

    <script>
    function previewProductImages(input) {
        const container = document.getElementById('image-preview-container');
        container.innerHTML = '';
        
        if (input.files && input.files.length > 0) {
            if (input.files.length > 20) {
                alert('Maximum 20 images allowed. Only the first 20 will be uploaded.');
            }
            
            const filesToProcess = Math.min(input.files.length, 20);
            
            for (let i = 0; i < filesToProcess; i++) {
                const file = input.files[i];
                
                if (file.size > 5 * 1024 * 1024) {
                    alert(`File "${file.name}" is too large. Maximum size is 5MB.`);
                    continue;
                }
                
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.createElement('div');
                    preview.style.cssText = 'position: relative; aspect-ratio: 1; border-radius: 8px; overflow: hidden; border: 2px solid #e5e7eb;';
                    
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.style.cssText = 'width: 100%; height: 100%; object-fit: cover;';
                    
                    const badge = document.createElement('div');
                    badge.style.cssText = 'position: absolute; top: 4px; left: 4px; background: #2f6df5; color: white; padding: 2px 8px; border-radius: 4px; font-size: 10px; font-weight: 700;';
                    badge.textContent = i === 0 ? 'MAIN' : `#${i + 1}`;
                    
                    preview.appendChild(img);
                    preview.appendChild(badge);
                    container.appendChild(preview);
                };
                reader.readAsDataURL(file);
            }
        }
    }
    </script>

    <?php include __DIR__ . '/../../includes/footer.php'; ?>
</body>
</html>
