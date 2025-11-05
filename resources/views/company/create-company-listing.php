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

// Get user's company
$company = null;
try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $stmt = $pdo->prepare('SELECT * FROM companies WHERE owner_user_id = :user_id');
    $stmt->execute(['user_id' => $user_id]);
    $company = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$company) {
        $_SESSION['company_error'] = 'You need to create a company first before creating company listings.';
        header('Location: ' . VIEWS_URL . '/company/create-company.php');
        exit;
    }
} catch (PDOException $e) {
    $error_message = 'Database error: ' . $e->getMessage();
}

// Handle listing creation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_listing'])) {
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
            
            // Insert new listing with company_id
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
                    recycled,
                    tested,
                    storage_location,
                    currency,
                    published,
                    image_path,
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
                    :recycled,
                    :tested,
                    :storage_location,
                    :currency,
                    :published,
                    :image_path,
                    NOW()
                )
            ');
            
            $stmt->execute([
                'company_id' => $company['id'],
                'user_id' => $user_id,
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
                'image_path' => 'image.png' // Default placeholder
            ]);
            
            $listing_id = $pdo->lastInsertId();
            
            // Handle multiple image uploads
            if (isset($_FILES['product_images']) && !empty($_FILES['product_images']['name'][0])) {
                $upload_dir = __DIR__ . '/../../../public/uploads/listings/';
                
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0755, true);
                }
                
                $total_images = count($_FILES['product_images']['name']);
                
                if ($total_images > 20) {
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
                            $is_main = ($key == 0) ? 1 : 0;
                            
                            $stmt = $pdo->prepare('
                                INSERT INTO listing_images (listing_id, image_path, is_main, display_order)
                                VALUES (:listing_id, :image_path, :is_main, :display_order)
                            ');
                            $stmt->execute([
                                'listing_id' => $listing_id,
                                'image_path' => $image_path,
                                'is_main' => $is_main,
                                'display_order' => $key
                            ]);
                        }
                    }
                }
            }
            
            $pdo->commit();
            
            $_SESSION['listing_success'] = 'Company listing created successfully!';
            header('Location: ' . VIEWS_URL . '/profile.php?tab=company');
            exit;
            
        } catch (Exception $e) {
            $pdo->rollBack();
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
    <title>Create Company Listing - Glass Market</title>
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
            flex-wrap: wrap;
            gap: 16px;
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

        .company-badge {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-transform: uppercase;
            letter-spacing: 0.5px;
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
            -webkit-overflow-scrolling: touch;
            scrollbar-width: none;
        }
        
        .tabs-header::-webkit-scrollbar {
            display: none;
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

        .button-group {
            display: flex;
            gap: 12px;
            margin-top: 32px;
        }

        .upload-zone {
            border: 3px dashed var(--profile-border);
            border-radius: 16px;
            padding: 48px 24px;
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

        .input-group {
            display: grid;
            grid-template-columns: 1fr auto;
            gap: 12px;
        }

        #preview-container {
            margin-top: 16px;
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
            gap: 16px;
        }

        .preview-item {
            position: relative;
            aspect-ratio: 1;
            border-radius: 12px;
            overflow: hidden;
            border: 3px solid #e5e7eb;
            background: #f9fafb;
        }

        .preview-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .preview-badge {
            position: absolute;
            top: 8px;
            left: 8px;
            background: var(--profile-primary);
            color: white;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 700;
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
            margin-bottom: 32px;
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
    <?php include __DIR__ . '/../../../includes/navbar.php'; ?>

    <main class="edit-container">
        <div class="page-header">
            <div class="page-header-top">
                <h1 class="page-title">
                    Create Company Listing
                    <span class="company-badge">🏢 <?php echo htmlspecialchars($company['name']); ?></span>
                </h1>
                <a href="<?php echo VIEWS_URL; ?>/profile.php?tab=company" class="back-link">
                    ← Back to Company
                </a>
            </div>
            <p class="page-subtitle">Create a new listing for your company</p>
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

        <form method="POST" enctype="multipart/form-data">
            <div class="tabs-container">
                <div class="tabs-header">
                    <button type="button" class="tab-button active" onclick="switchTab('details')">
                        📝 Listing Details
                    </button>
                    <button type="button" class="tab-button" onclick="switchTab('images')">
                        📸 Product Images
                    </button>
                </div>

                <!-- Tab: Listing Details -->
                <div id="tab-details" class="tab-content active">
                    <!-- Status Toggle -->
                    <div class="status-toggle">
                        <div class="status-toggle-label">
                            <strong>Listing Status</strong>
                            <span id="status-text">Draft - Save and publish later</span>
                        </div>
                        <label class="toggle-switch">
                            <input type="checkbox" name="published" value="1" onchange="updateStatusText(this)">
                            <span class="toggle-slider"></span>
                        </label>
                    </div>

                    <div class="edit-grid">
                        <div class="form-group">
                            <label class="form-label" for="side">
                                Listing Type <span class="required">*</span>
                            </label>
                            <select id="side" name="side" class="form-select" required>
                                <option value="WTS">Want To Sell (WTS)</option>
                                <option value="WTB">Want To Buy (WTB)</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="glass_type">
                                Type of Glass <span class="required">*</span>
                            </label>
                            <select id="glass_type" name="glass_type" class="form-select" onchange="toggleOtherGlassType(this)" required>
                                <option value="">Select glass type...</option>
                                <option value="Clear Cullet">Clear Cullet</option>
                                <option value="Green Cullet">Green Cullet</option>
                                <option value="Brown Cullet">Brown Cullet</option>
                                <option value="Amber Cullet">Amber Cullet</option>
                                <option value="Mixed Cullet">Mixed Cullet</option>
                                <option value="Flint Cullet">Flint Cullet</option>
                                <option value="other">Other (specify)</option>
                            </select>
                        </div>

                        <div class="form-group" id="glass_type_other_container" style="display: none;">
                            <label class="form-label" for="glass_type_other">Specify Glass Type</label>
                            <input type="text" id="glass_type_other" name="glass_type_other" class="form-input" placeholder="Enter custom glass type">
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="glass_title">
                                Listing Title <span class="required">*</span>
                            </label>
                            <input type="text" id="glass_title" name="glass_title" class="form-input" placeholder="e.g., Premium Green Cullet - High Quality" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="glass_tons">
                                Quantity (in tons) <span class="required">*</span>
                            </label>
                            <input type="number" id="glass_tons" name="glass_tons" class="form-input" placeholder="0.00" step="0.01" min="0.01" required>
                            <small class="form-hint">Specify the total weight in tons</small>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="recycled">Recycled Status</label>
                            <select id="recycled" name="recycled" class="form-select">
                                <option value="unknown">Unknown</option>
                                <option value="recycled">Recycled</option>
                                <option value="not_recycled">Not Recycled</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="tested">Testing Status</label>
                            <select id="tested" name="tested" class="form-select">
                                <option value="unknown">Unknown</option>
                                <option value="tested">Tested</option>
                                <option value="untested">Untested</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="storage_location">Storage Location</label>
                            <input type="text" id="storage_location" name="storage_location" class="form-input" placeholder="e.g., Rotterdam warehouse, Dock 5">
                            <small class="form-hint">Where is the glass currently stored?</small>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="price_text">Price</label>
                            <div class="input-group">
                                <input type="text" id="price_text" name="price_text" class="form-input" placeholder="e.g., €120/ton CIF or Negotiable">
                                <select id="currency" name="currency" class="form-select" style="width: 120px;">
                                    <option value="EUR">EUR (€)</option>
                                    <option value="USD">USD ($)</option>
                                    <option value="GBP">GBP (£)</option>
                                    <option value="CNY">CNY (¥)</option>
                                </select>
                            </div>
                            <small class="form-hint">Enter price or leave blank for negotiation</small>
                        </div>

                        <div class="form-group" style="grid-column: 1 / -1;">
                            <label class="form-label" for="glass_description">Quality Notes / Description</label>
                            <textarea id="glass_description" name="glass_description" class="form-textarea" rows="5" placeholder="Describe the glass quality, condition, source, etc..."></textarea>
                            <small class="form-hint">Add any quality notes or additional information</small>
                        </div>
                    </div>

                    <div class="button-group">
                        <button type="submit" name="create_listing" class="btn btn-primary">
                            ✨ Create Listing
                        </button>
                        <a href="<?php echo VIEWS_URL; ?>/profile.php?tab=company" class="btn btn-secondary">
                            Cancel
                        </a>
                    </div>
                </div>

                <!-- Tab: Images -->
                <div id="tab-images" class="tab-content">
                    <div class="upload-zone" onclick="document.getElementById('product_images').click()">
                        <div class="upload-zone-icon">📸</div>
                        <div class="upload-zone-text">Click to upload product images</div>
                        <div class="upload-zone-hint">JPG, PNG, or WEBP (max 5MB each, up to 20 images)</div>
                        <input type="file" id="product_images" name="product_images[]" accept="image/jpeg,image/jpg,image/png,image/webp" multiple style="display: none;" onchange="previewImages(this)">
                    </div>

                    <div id="preview-container"></div>

                    <div class="button-group">
                        <button type="submit" name="create_listing" class="btn btn-primary" style="width: 100%;">
                            ✨ Create Listing with Images
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </main>

    <?php include __DIR__ . '/../../../includes/footer.php'; ?>

    <script>
    function updateStatusText(checkbox) {
        const statusText = document.getElementById('status-text');
        if (checkbox.checked) {
            statusText.textContent = 'Published - Visible to buyers immediately';
        } else {
            statusText.textContent = 'Draft - Save and publish later';
        }
    }

    function switchTab(tabName) {
        document.querySelectorAll('.tab-content').forEach(content => {
            content.classList.remove('active');
        });
        
        document.querySelectorAll('.tab-button').forEach(button => {
            button.classList.remove('active');
        });
        
        const selectedTab = document.getElementById('tab-' + tabName);
        if (selectedTab) {
            selectedTab.classList.add('active');
        }
        
        const clickedButton = Array.from(document.querySelectorAll('.tab-button')).find(btn => {
            return btn.textContent.includes(tabName === 'details' ? 'Listing Details' : 'Product Images');
        });
        if (clickedButton) {
            clickedButton.classList.add('active');
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
                    preview.className = 'preview-item';
                    
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    
                    const badge = document.createElement('div');
                    badge.className = 'preview-badge';
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
</body>
</html>
