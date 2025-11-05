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
            
            $image_path = 'image.png'; // Default image
            
            // Handle image upload
            if (isset($_FILES['glass_image']) && $_FILES['glass_image']['error'] === UPLOAD_ERR_OK) {
                $upload_dir = __DIR__ . '/../../public/uploads/listings/';
                
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0755, true);
                }
                
                $file_extension = strtolower(pathinfo($_FILES['glass_image']['name'], PATHINFO_EXTENSION));
                $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                
                if (in_array($file_extension, $allowed_extensions)) {
                    $new_filename = 'listing_' . time() . '_' . uniqid() . '.' . $file_extension;
                    $upload_path = $upload_dir . $new_filename;
                    
                    if (move_uploaded_file($_FILES['glass_image']['tmp_name'], $upload_path)) {
                        $image_path = 'uploads/listings/' . $new_filename;
                    }
                }
            }
            
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
            
            $_SESSION['listing_success'] = 'Listing created successfully!';
            header('Location: ' . VIEWS_URL . '/profile.php?tab=listings');
            exit;
        } catch (PDOException $e) {
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
                <h2>📸 Image</h2>
                
                <div class="form-group">
                    <label for="glass_image">Upload Image (optional)</label>
                    <div class="file-upload" onclick="document.getElementById('glass_image').click()">
                        <div style="font-size: 48px; margin-bottom: 12px;">📷</div>
                        <p style="margin: 0; font-weight: 600; color: #374151;">Click to upload image</p>
                        <p style="margin: 4px 0 0; font-size: 13px; color: #6b7280;">JPG, PNG, GIF, or WEBP (max 10MB)</p>
                        <input type="file" id="glass_image" name="glass_image" accept="image/*">
                    </div>
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

    <?php include __DIR__ . '/../../includes/footer.php'; ?>
</body>
</html>
