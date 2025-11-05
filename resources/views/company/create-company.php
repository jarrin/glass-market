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

// Check if user already has a company
try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $stmt = $pdo->prepare('SELECT id FROM companies WHERE owner_user_id = :user_id');
    $stmt->execute(['user_id' => $user_id]);
    $existing_company = $stmt->fetch();
    
    if ($existing_company) {
        $_SESSION['company_error'] = 'You already have a company. You can edit it from your profile.';
        header('Location: ' . VIEWS_URL . '/profile.php?tab=company');
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

// Handle company creation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_company'])) {
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
            $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            $stmt = $pdo->prepare('
                INSERT INTO companies (
                    name, company_type, description, address_line1, address_line2,
                    city, postal_code, country, phone, website, owner_user_id, created_at
                ) VALUES (
                    :name, :company_type, :description, :address_line1, :address_line2,
                    :city, :postal_code, :country, :phone, :website, :owner_user_id, NOW()
                )
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
            
            $_SESSION['company_success'] = 'Company created successfully!';
            header('Location: ' . VIEWS_URL . '/profile.php?tab=company');
            exit;
            
        } catch (PDOException $e) {
            $error_message = 'Failed to create company: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Company - Glass Market</title>
    <link rel="stylesheet" href="<?php echo CSS_URL; ?>/app.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 40px 20px;
        }

        .container {
            max-width: 700px;
            margin: 0 auto;
        }

        .company-card {
            background: white;
            border-radius: 24px;
            padding: 48px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }

        .page-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .page-header .icon {
            font-size: 72px;
            margin-bottom: 16px;
        }

        .page-header h1 {
            font-size: 32px;
            font-weight: 800;
            margin: 0 0 12px;
            color: #1f2937;
        }

        .page-header p {
            font-size: 15px;
            color: #6b7280;
            margin: 0;
            line-height: 1.6;
        }

        .form-section {
            margin-bottom: 32px;
        }

        .form-section-title {
            font-size: 18px;
            font-weight: 700;
            color: #374151;
            margin: 0 0 20px;
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
            min-height: 100px;
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
            width: 100%;
            padding: 16px 24px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.6);
        }

        .btn-secondary {
            display: inline-block;
            padding: 12px 24px;
            background: #f3f4f6;
            color: #374151;
            border: none;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            text-align: center;
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

        @media (max-width: 768px) {
            .company-card {
                padding: 32px 24px;
            }

            .form-row {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <?php include __DIR__ . '/../../../includes/navbar.php'; ?>

    <div class="container">
        <div class="company-card">
            <div class="page-header">
                <div class="icon">🏢</div>
                <h1>Create Your Company</h1>
                <p>Set up your company profile to create listings, build your brand, and appear in the Sellers directory.</p>
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
                            value="<?php echo isset($_POST['company_name']) ? htmlspecialchars($_POST['company_name']) : ''; ?>"
                            placeholder="Enter your company name"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label>
                            Company Type <span class="required">*</span>
                        </label>
                        <select name="company_type" required>
                            <option value="">Select company type...</option>
                            <?php foreach ($company_types as $type): ?>
                                <option value="<?php echo htmlspecialchars($type); ?>" <?php echo (isset($_POST['company_type']) && $_POST['company_type'] === $type) ? 'selected' : ''; ?>>
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
                        ><?php echo isset($_POST['description']) ? htmlspecialchars($_POST['description']) : ''; ?></textarea>
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
                            value="<?php echo isset($_POST['address_line1']) ? htmlspecialchars($_POST['address_line1']) : ''; ?>"
                            placeholder="Street address"
                        >
                    </div>

                    <div class="form-group">
                        <label>Address Line 2</label>
                        <input 
                            type="text" 
                            name="address_line2" 
                            value="<?php echo isset($_POST['address_line2']) ? htmlspecialchars($_POST['address_line2']) : ''; ?>"
                            placeholder="Apartment, suite, unit, building, floor, etc."
                        >
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>City</label>
                            <input 
                                type="text" 
                                name="city" 
                                value="<?php echo isset($_POST['city']) ? htmlspecialchars($_POST['city']) : ''; ?>"
                                placeholder="City"
                            >
                        </div>

                        <div class="form-group">
                            <label>Postal Code</label>
                            <input 
                                type="text" 
                                name="postal_code" 
                                value="<?php echo isset($_POST['postal_code']) ? htmlspecialchars($_POST['postal_code']) : ''; ?>"
                                placeholder="Postal code"
                            >
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Country</label>
                        <input 
                            type="text" 
                            name="country" 
                            value="<?php echo isset($_POST['country']) ? htmlspecialchars($_POST['country']) : ''; ?>"
                            placeholder="Country"
                        >
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Phone Number</label>
                            <input 
                                type="tel" 
                                name="phone" 
                                value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>"
                                placeholder="+1 (555) 123-4567"
                            >
                        </div>

                        <div class="form-group">
                            <label>Website</label>
                            <input 
                                type="url" 
                                name="website" 
                                value="<?php echo isset($_POST['website']) ? htmlspecialchars($_POST['website']) : ''; ?>"
                                placeholder="https://www.example.com"
                            >
                        </div>
                    </div>
                </div>

                <div class="button-group">
                    <button type="submit" name="create_company" class="btn-primary" style="flex: 1;">
                        🚀 Create Company
                    </button>
                    <a href="<?php echo VIEWS_URL; ?>/profile.php?tab=company" class="btn-secondary">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>

    <?php include __DIR__ . '/../../../includes/footer.php'; ?>
</body>
</html>
