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
                    website = :website
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
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding-top: 80px;
        }

        .container {
            max-width: 1000px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .page-header {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(20px);
            border-radius: 24px;
            padding: 48px;
            margin-bottom: 32px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
            display: flex;
            align-items: center;
            gap: 32px;
            border: 1px solid rgba(255, 255, 255, 0.5);
        }

        .company-avatar {
            width: 120px;
            height: 120px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 56px;
            color: white;
            font-weight: 800;
            flex-shrink: 0;
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
        }

        .company-header-info {
            flex: 1;
        }

        .company-header-info h1 {
            font-size: 40px;
            font-weight: 900;
            margin: 0 0 8px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .company-header-info p {
            font-size: 16px;
            color: #6b7280;
            margin: 0;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-bottom: 32px;
        }

        .stat-card {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(20px);
            padding: 32px;
            border-radius: 20px;
            text-align: center;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.5);
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 50px rgba(0, 0, 0, 0.15);
        }

        .stat-value {
            font-size: 48px;
            font-weight: 900;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin: 0 0 8px;
        }

        .stat-label {
            font-size: 14px;
            color: #6b7280;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 0;
        }

        .company-card {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(20px);
            border-radius: 24px;
            padding: 48px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
            margin-bottom: 24px;
            border: 1px solid rgba(255, 255, 255, 0.5);
        }

        .form-section {
            margin-bottom: 48px;
        }

        .form-section:last-child {
            margin-bottom: 0;
        }

        .form-section-title {
            font-size: 24px;
            font-weight: 800;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin: 0 0 28px;
            padding-bottom: 16px;
            border-bottom: 3px solid #f3f4f6;
        }

        .form-group {
            margin-bottom: 24px;
        }

        .form-group label {
            display: block;
            font-size: 15px;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 10px;
        }

        .form-group label .required {
            color: #ef4444;
            font-weight: 900;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 14px 18px;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            font-size: 15px;
            color: #1f2937;
            transition: all 0.3s ease;
            font-family: inherit;
            background: white;
        }

        .form-group input:hover,
        .form-group select:hover,
        .form-group textarea:hover {
            border-color: #d1d5db;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.15);
        }

        .form-group textarea {
            resize: vertical;
            min-height: 140px;
            line-height: 1.6;
        }

        .form-group small {
            display: block;
            font-size: 13px;
            color: #6b7280;
            margin-top: 8px;
            font-style: italic;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
        }

        .form-row-triple {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 20px;
        }

        .alert {
            padding: 18px 24px;
            border-radius: 16px;
            margin-bottom: 28px;
            font-size: 15px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .alert-error {
            background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
            color: #991b1b;
            border: 2px solid #fca5a5;
        }

        .alert-success {
            background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
            color: #065f46;
            border: 2px solid #6ee7b7;
        }

        .btn-primary {
            padding: 16px 36px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 14px;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 8px 24px rgba(102, 126, 234, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 32px rgba(102, 126, 234, 0.4);
        }

        .btn-primary:active {
            transform: translateY(0);
        }

        .btn-secondary {
            display: inline-block;
            padding: 16px 36px;
            background: white;
            color: #374151;
            border: 2px solid #e5e7eb;
            border-radius: 14px;
            font-size: 16px;
            font-weight: 700;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-secondary:hover {
            background: #f9fafb;
            border-color: #d1d5db;
            transform: translateY(-2px);
        }

        .button-group {
            display: flex;
            gap: 16px;
            margin-top: 40px;
        }

        @media (max-width: 768px) {
            body {
                padding-top: 60px;
            }

            .container {
                margin: 20px auto;
            }

            .page-header {
                flex-direction: column;
                text-align: center;
                padding: 32px 24px;
            }

            .company-avatar {
                width: 100px;
                height: 100px;
                font-size: 48px;
            }

            .company-header-info h1 {
                font-size: 32px;
            }

            .company-card {
                padding: 28px 20px;
            }

            .form-row,
            .form-row-triple {
                grid-template-columns: 1fr;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .button-group {
                flex-direction: column;
            }

            .btn-primary,
            .btn-secondary {
                width: 100%;
                text-align: center;
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
