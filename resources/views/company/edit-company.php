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

// Load company
$company = null;
try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $stmt = $pdo->prepare('SELECT * FROM companies WHERE owner_user_id = :user_id');
    $stmt->execute(['user_id' => $user_id]);
    $company = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$company) {
        $_SESSION['company_error'] = 'Company not found. Please create one first.';
        header('Location: ' . VIEWS_URL . '/company/create-company.php');
        exit;
    }
} catch (PDOException $e) {
    $error_message = 'Database error: ' . $e->getMessage();
}

// Get company types
try {
    $stmt = $pdo->query('SELECT type FROM company_types ORDER BY type');
    $company_types = $stmt->fetchAll(PDO::FETCH_COLUMN);
} catch (PDOException $e) {
    $company_types = ['Glass Recycle Plant', 'Glass Factory', 'Collection Company', 'Trader', 'Other'];
}

// Handle company update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_company'])) {
    $company_name = trim($_POST['company_name'] ?? '');
    $company_type = trim($_POST['company_type'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $address_line1 = trim($_POST['address_line1'] ?? '');
    $address_line2 = trim($_POST['address_line2'] ?? '');
    $city = trim($_POST['city'] ?? '');
    $postal_code = trim($_POST['postal_code'] ?? '');
    $country = trim($_POST['country'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $website = trim($_POST['website'] ?? '');
    
    if (empty($company_name)) {
        $error_message = 'Company name is required.';
    } elseif (empty($company_type)) {
        $error_message = 'Company type is required.';
    } else {
        try {
            $stmt = $pdo->prepare('
                UPDATE companies SET
                    name = :name,
                    company_type = :company_type,
                    description = :description,
                    address_line1 = :address_line1,
                    address_line2 = :address_line2,
                    city = :city,
                    postal_code = :postal_code,
                    country = :country,
                    phone = :phone,
                    website = :website,
                    updated_at = NOW()
                WHERE owner_user_id = :owner_user_id
            ');
            
            $stmt->execute([
                'name' => $company_name,
                'company_type' => $company_type,
                'description' => $description,
                'address_line1' => $address_line1,
                'address_line2' => $address_line2,
                'city' => $city,
                'postal_code' => $postal_code,
                'country' => $country,
                'phone' => $phone,
                'website' => $website,
                'owner_user_id' => $user_id
            ]);
            
            $success_message = 'Company updated successfully!';
            
            // Reload company data
            $stmt = $pdo->prepare('SELECT * FROM companies WHERE owner_user_id = :user_id');
            $stmt->execute(['user_id' => $user_id]);
            $company = $stmt->fetch(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            $error_message = 'Failed to update company: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Company - Glass Market</title>
    <link rel="stylesheet" href="<?php echo CSS_URL; ?>/app.css">
    <style>
        body {
            background: #f9fafb;
            min-height: 100vh;
        }

        .container {
            max-width: 900px;
            margin: 0 auto;
            padding: 40px 20px;
        }

        .page-header {
            background: linear-gradient(135deg, #1f2937 0%, #374151 100%);
            color: white;
            padding: 40px;
            border-radius: 16px;
            margin-bottom: 32px;
            display: flex;
            align-items: center;
            gap: 24px;
        }

        .company-avatar {
            width: 100px;
            height: 100px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 48px;
            color: white;
            font-weight: 700;
            flex-shrink: 0;
        }

        .company-header-info {
            flex: 1;
        }

        .company-header-info h1 {
            font-size: 32px;
            font-weight: 800;
            margin: 0 0 8px;
        }

        .company-header-info p {
            font-size: 14px;
            opacity: 0.8;
            margin: 0;
        }

        .company-card {
            background: white;
            border-radius: 16px;
            padding: 40px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            margin-bottom: 24px;
        }

        .form-section {
            margin-bottom: 36px;
        }

        .form-section:last-child {
            margin-bottom: 0;
        }

        .form-section-title {
            font-size: 20px;
            font-weight: 700;
            color: #1f2937;
            margin: 0 0 24px;
            padding-bottom: 12px;
            border-bottom: 2px solid #e5e7eb;
        }

        .form-group {
            margin-bottom: 24px;
        }

        .form-group label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 8px;
        }

        .form-group label .required {
            color: #ef4444;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #d1d5db;
            border-radius: 10px;
            font-size: 15px;
            color: #1f2937;
            transition: all 0.2s ease;
            font-family: inherit;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .form-group textarea {
            resize: vertical;
            min-height: 120px;
        }

        .form-group small {
            display: block;
            font-size: 13px;
            color: #6b7280;
            margin-top: 6px;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .form-row-triple {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 16px;
        }

        .alert {
            padding: 16px 20px;
            border-radius: 12px;
            margin-bottom: 24px;
            font-size: 14px;
            font-weight: 500;
        }

        .alert-error {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fca5a5;
        }

        .alert-success {
            background: #d1fae5;
            color: #065f46;
            border: 1px solid #6ee7b7;
        }

        .btn-primary {
            padding: 14px 28px;
            background: #667eea;
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .btn-primary:hover {
            background: #5568d3;
            transform: translateY(-1px);
        }

        .btn-secondary {
            display: inline-block;
            padding: 14px 28px;
            background: #f3f4f6;
            color: #374151;
            border: none;
            border-radius: 10px;
            font-size: 15px;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .btn-secondary:hover {
            background: #e5e7eb;
        }

        .button-group {
            display: flex;
            gap: 12px;
            margin-top: 32px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-bottom: 32px;
        }

        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 24px;
            border-radius: 12px;
            color: white;
            text-align: center;
        }

        .stat-value {
            font-size: 36px;
            font-weight: 800;
            margin: 0 0 4px;
        }

        .stat-label {
            font-size: 13px;
            opacity: 0.9;
            margin: 0;
        }

        @media (max-width: 768px) {
            .company-card {
                padding: 24px;
            }

            .page-header {
                flex-direction: column;
                text-align: center;
            }

            .form-row,
            .form-row-triple {
                grid-template-columns: 1fr;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <?php include __DIR__ . '/../../../includes/navbar.php'; ?>

    <div class="container">
        <div class="page-header">
            <div class="company-avatar">
                <?php echo strtoupper(substr($company['name'], 0, 1)); ?>
            </div>
            <div class="company-header-info">
                <h1><?php echo htmlspecialchars($company['name']); ?></h1>
                <p>Member since <?php echo isset($company['created_at']) ? date('F Y', strtotime($company['created_at'])) : 'N/A'; ?></p>
            </div>
        </div>

        <?php
        // Get company stats
        try {
            $stmt = $pdo->prepare('SELECT COUNT(*) as listing_count FROM listings WHERE company_id = :company_id');
            $stmt->execute(['company_id' => $company['id']]);
            $total_listings = $stmt->fetch()['listing_count'];
            
            $stmt = $pdo->prepare('SELECT COUNT(*) as published_count FROM listings WHERE company_id = :company_id AND published = 1');
            $stmt->execute(['company_id' => $company['id']]);
            $published_listings = $stmt->fetch()['published_count'];
            
            $draft_listings = $total_listings - $published_listings;
        } catch (PDOException $e) {
            $total_listings = 0;
            $published_listings = 0;
            $draft_listings = 0;
        }
        ?>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-value"><?php echo $total_listings; ?></div>
                <div class="stat-label">Total Listings</div>
            </div>
            <div class="stat-card">
                <div class="stat-value"><?php echo $published_listings; ?></div>
                <div class="stat-label">Published</div>
            </div>
            <div class="stat-card">
                <div class="stat-value"><?php echo $draft_listings; ?></div>
                <div class="stat-label">Drafts</div>
            </div>
        </div>

        <div class="company-card">
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

            <form method="POST">
                <!-- Basic Information -->
                <div class="form-section">
                    <h2 class="form-section-title">Basic Information</h2>
                    
                    <div class="form-group">
                        <label>
                            Company Name <span class="required">*</span>
                        </label>
                        <input 
                            type="text" 
                            name="company_name" 
                            value="<?php echo htmlspecialchars($company['name']); ?>"
                            placeholder="Enter your company name"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label>
                            Company Type <span class="required">*</span>
                        </label>
                        <select name="company_type" required>
                            <?php foreach ($company_types as $type): ?>
                                <option value="<?php echo htmlspecialchars($type); ?>" <?php echo ($company['company_type'] ?? '') === $type ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($type); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Company Description</label>
                        <textarea 
                            name="description" 
                            placeholder="Tell potential customers about your company, your expertise, and what sets you apart..."
                        ><?php echo htmlspecialchars($company['description'] ?? ''); ?></textarea>
                        <small>Help buyers understand what makes your company unique</small>
                    </div>
                </div>

                <!-- Contact & Location -->
                <div class="form-section">
                    <h2 class="form-section-title">Contact & Location</h2>
                    
                    <div class="form-group">
                        <label>Address Line 1</label>
                        <input 
                            type="text" 
                            name="address_line1" 
                            value="<?php echo htmlspecialchars($company['address_line1'] ?? ''); ?>"
                            placeholder="Street address"
                        >
                    </div>

                    <div class="form-group">
                        <label>Address Line 2</label>
                        <input 
                            type="text" 
                            name="address_line2" 
                            value="<?php echo htmlspecialchars($company['address_line2'] ?? ''); ?>"
                            placeholder="Apartment, suite, unit, building, floor, etc."
                        >
                    </div>

                    <div class="form-row-triple">
                        <div class="form-group">
                            <label>City</label>
                            <input 
                                type="text" 
                                name="city" 
                                value="<?php echo htmlspecialchars($company['city'] ?? ''); ?>"
                                placeholder="City"
                            >
                        </div>

                        <div class="form-group">
                            <label>Postal Code</label>
                            <input 
                                type="text" 
                                name="postal_code" 
                                value="<?php echo htmlspecialchars($company['postal_code'] ?? ''); ?>"
                                placeholder="Postal code"
                            >
                        </div>

                        <div class="form-group">
                            <label>Country</label>
                            <input 
                                type="text" 
                                name="country" 
                                value="<?php echo htmlspecialchars($company['country'] ?? ''); ?>"
                                placeholder="Country"
                            >
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Phone Number</label>
                            <input 
                                type="tel" 
                                name="phone" 
                                value="<?php echo htmlspecialchars($company['phone'] ?? ''); ?>"
                                placeholder="+1 (555) 123-4567"
                            >
                        </div>

                        <div class="form-group">
                            <label>Website</label>
                            <input 
                                type="url" 
                                name="website" 
                                value="<?php echo htmlspecialchars($company['website'] ?? ''); ?>"
                                placeholder="https://www.example.com"
                            >
                        </div>
                    </div>
                </div>

                <div class="button-group">
                    <button type="submit" name="update_company" class="btn-primary">
                        💾 Save Changes
                    </button>
                    <a href="<?php echo VIEWS_URL; ?>/profile.php?tab=company" class="btn-secondary">
                        Back to Profile
                    </a>
                </div>
            </form>
        </div>
    </div>

    <?php include __DIR__ . '/../../../includes/footer.php'; ?>
</body>
</html>
