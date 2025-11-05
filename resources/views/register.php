<?php
session_start();

require_once __DIR__ . '/../../config.php';

// Check if already logged in
$is_logged_in = isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true;
$user_name = $is_logged_in ? ($_SESSION['user_name'] ?? 'User') : '';
$user_email = $is_logged_in ? ($_SESSION['user_email'] ?? '') : '';
$user_avatar = $is_logged_in ? ($_SESSION['user_avatar'] ?? '') : '';

// Database connection
$db_host = '127.0.0.1';
$db_name = 'glass_market';
$db_user = 'root';
$db_pass = '';

$error_message = '';
$success_message = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $company_name = trim($_POST['company_name'] ?? ''); // Optional
    $address = trim($_POST['address'] ?? ''); // Optional
    $city = trim($_POST['city'] ?? ''); // Optional
    $country = trim($_POST['country'] ?? ''); // Optional
    $notification_email = trim($_POST['notification_email'] ?? '');
    $communication_email = trim($_POST['communication_email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Validation - only required fields: name, emails, password
    if (empty($name) || empty($notification_email) || empty($communication_email) || 
        empty($password) || empty($confirm_password)) {
        $error_message = 'Please fill in all required fields (name, emails, and password).';
    } elseif (!filter_var($notification_email, FILTER_VALIDATE_EMAIL)) {
        $error_message = 'Please enter a valid notification email address.';
    } elseif (!filter_var($communication_email, FILTER_VALIDATE_EMAIL)) {
        $error_message = 'Please enter a valid communication email address.';
    } elseif (strlen($password) < 8) {
        $error_message = 'Password must be at least 8 characters long.';
    } elseif ($password !== $confirm_password) {
        $error_message = 'Passwords do not match.';
    } else {
        try {
            $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Check if email already exists
            $stmt = $pdo->prepare('SELECT id FROM users WHERE email = :email OR email = :comm_email LIMIT 1');
            $stmt->execute(['email' => $notification_email, 'comm_email' => $communication_email]);

            if ($stmt->fetch()) {
                $error_message = 'This email is already registered.';
            } else {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                // Add company_name column if it doesn't exist
                try {
                    $pdo->exec("ALTER TABLE users ADD COLUMN company_name VARCHAR(255) NULL AFTER name");
                } catch (PDOException $e) {
                    // Column already exists, ignore error
                }

                // Insert user with email_verified_at set to NOW() (auto-verified for regular users)
                $stmt = $pdo->prepare('
                    INSERT INTO users (name, company_name, email, password, email_verified_at)
                    VALUES (:name, :company_name, :email, :password, NOW())
                ');

                $stmt->execute([
                    'name' => $name,
                    'company_name' => !empty($company_name) ? $company_name : null,
                    'email' => $notification_email,
                    'password' => $hashed_password,
                ]);

                // Get the newly created user ID
                $user_id = $pdo->lastInsertId();

                // If company details provided, create a company record
                if (!empty($company_name)) {
                    try {
                        // Check if companies table exists and create company
                        $stmt = $pdo->prepare('
                            INSERT INTO companies (name, address, city, country, contact_email, created_at, updated_at)
                            VALUES (:name, :address, :city, :country, :email, NOW(), NOW())
                        ');
                        
                        $stmt->execute([
                            'name' => $company_name,
                            'address' => !empty($address) ? $address : null,
                            'city' => !empty($city) ? $city : null,
                            'country' => !empty($country) ? $country : null,
                            'email' => $communication_email,
                        ]);
                        
                        $company_id = $pdo->lastInsertId();
                        
                        // Link user to company
                        $stmt = $pdo->prepare('UPDATE users SET company_id = :company_id WHERE id = :user_id');
                        $stmt->execute([
                            'company_id' => $company_id,
                            'user_id' => $user_id
                        ]);
                    } catch (PDOException $e) {
                        // Company creation failed, but user is still created
                        error_log("Company creation failed: " . $e->getMessage());
                    }
                }

                // Create 3-month free trial subscription
                require_once __DIR__ . '/../../database/classes/subscriptions.php';
                $subscription_created = Subscription::createTrialSubscription($pdo, $user_id);

                if ($subscription_created) {
                    $success_message = 'Registration successful! You have been granted a 3-month free trial. You can now log in to your account.';
                } else {
                    $success_message = 'Registration successful! However, there was an issue creating your trial subscription. Please contact support.';
                    error_log("Trial subscription creation failed for user_id: $user_id");
                }
            }
        } catch (PDOException $e) {
            $error_message = 'Registration failed: ' . $e->getMessage();
            error_log("Registration error: " . $e->getMessage());
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Glass Market</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: #f5f5f5;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .register-container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            width: 100%;
            max-width: 520px;
            overflow: hidden;
            border: 1px solid #e0e0e0;
        }

        .register-header {
            padding: 32px 32px 20px;
            border-bottom: 2px solid #f5f5f5;
            background: #fafafa;
        }

        .register-header h1 {
            font-size: 20px;
            font-weight: 800;
            margin-bottom: 10px;
            color: #000;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .register-header .subtitle {
            font-size: 13px;
            color: #555;
            background: white;
            padding: 10px 16px;
            border-radius: 6px;
            text-align: center;
            border: 1px solid #e0e0e0;
        }

        .section-title {
            font-size: 12px;
            font-weight: 800;
            color: #000;
            margin: 28px 0 16px;
            letter-spacing: 0.8px;
            text-transform: uppercase;
            padding-bottom: 8px;
            border-bottom: 2px solid #000;
        }

        .register-body {
            padding: 32px;
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
            margin-bottom: 18px;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            margin-bottom: 16px;
        }

        .form-group label {
            display: block;
            font-size: 12px;
            font-weight: 600;
            color: #222;
            margin-bottom: 8px;
        }

        .form-group label .required {
            color: #e53e3e;
        }

        .form-group .helper-text {
            font-size: 10px;
            color: #999;
            margin-top: 2px;
        }

        .form-group input {
            width: 100%;
            padding: 12px 14px;
            font-size: 14px;
            border: 1.5px solid #ddd;
            border-radius: 6px;
            transition: all 0.2s ease;
            outline: none;
            background: #fafafa;
        }

        .form-group input:focus {
            border-color: #000;
            background: white;
            box-shadow: 0 0 0 3px rgba(0, 0, 0, 0.08);
        }

        .form-group input::placeholder {
            color: #bbb;
        }

        .form-actions {
            display: flex;
            gap: 12px;
            margin-top: 24px;
        }

        .btn {
            padding: 12px 24px;
            font-size: 13px;
            font-weight: 600;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.2s ease;
            border: none;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .btn-primary {
            flex: 1;
            background: #000;
            color: white;
            font-weight: 700;
        }

        .btn-primary:hover {
            background: #333;
        }

        .btn-secondary {
            padding: 12px 24px;
            background: transparent;
            color: #666;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .btn-secondary:hover {
            color: #000;
        }

        .register-footer {
            text-align: center;
            padding: 20px 32px 28px;
            font-size: 12px;
            color: #888;
            background: #fafafa;
            border-top: 2px solid #f5f5f5;
        }

        @media (max-width: 600px) {
            .form-row {
                grid-template-columns: 1fr;
            }

            .form-actions {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="register-header">
            <h1>Create Account</h1>
            <div class="subtitle">First 3 months FREE, then subscription required</div>
        </div>

        <div class="register-body">
            <?php if ($is_logged_in): ?>
                <!-- Already Logged In View -->
                <div style="text-align: center; padding: 20px 0;">
                    <div style="width: 80px; height: 80px; margin: 0 auto 20px; border-radius: 50%; background: #f0f0f0; display: flex; align-items: center; justify-content: center; overflow: hidden; border: 3px solid #000;">
                        <?php if (!empty($user_avatar)): ?>
                            <img src="<?php echo htmlspecialchars($user_avatar); ?>" alt="Profile" style="width: 100%; height: 100%; object-fit: cover;">
                        <?php else: ?>
                            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#666" stroke-width="2">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                <circle cx="12" cy="7" r="4"></circle>
                            </svg>
                        <?php endif; ?>
                    </div>
                    <h2 style="font-size: 18px; font-weight: 700; margin-bottom: 8px; color: #000;"><?php echo htmlspecialchars($user_name); ?></h2>
                    <p style="font-size: 13px; color: #666; margin-bottom: 24px;"><?php echo htmlspecialchars($user_email); ?></p>
                    <div style="display: flex; gap: 12px; flex-direction: column;">
                        <a href="<?php echo PUBLIC_URL; ?>/index.php" class="btn btn-primary" style="display: block; text-decoration: none; text-align: center;">Go to Home</a>
                        <a href="<?php echo VIEWS_URL; ?>/logout.php" class="btn btn-secondary" style="display: block; text-decoration: none; text-align: center; border: 1.5px solid #ddd;">Logout</a>
                    </div>
                </div>
            <?php else: ?>
                <!-- Registration Form -->
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

                <form method="POST" action="">
                <!-- Personal Information -->
                <div class="section-title">Personal Information</div>

                <div class="form-group">
                    <label for="name">Full name <span class="required">*</span></label>
                    <input
                        type="text"
                        id="name"
                        name="name"
                        value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>"
                        placeholder="Full name..."
                        required
                        autofocus
                    >
                </div>

                <div class="form-group">
                    <label for="company_name">Company name <span style="color: #999; font-size: 11px;">(Optional)</span></label>
                    <input
                        type="text"
                        id="company_name"
                        name="company_name"
                        value="<?php echo htmlspecialchars($_POST['company_name'] ?? ''); ?>"
                        placeholder="Company name (optional)..."
                    >
                </div>

                <div class="form-group">
                    <label for="address">Address <span style="color: #999; font-size: 11px;">(Optional)</span></label>
                    <input
                        type="text"
                        id="address"
                        name="address"
                        value="<?php echo htmlspecialchars($_POST['address'] ?? ''); ?>"
                        placeholder="Address (optional)"
                    >
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="city">City <span style="color: #999; font-size: 11px;">(Optional)</span></label>
                        <input
                            type="text"
                            id="city"
                            name="city"
                            value="<?php echo htmlspecialchars($_POST['city'] ?? ''); ?>"
                            placeholder="City (optional)"
                        >
                    </div>
                    <div class="form-group">
                        <label for="country">Country <span style="color: #999; font-size: 11px;">(Optional)</span></label>
                        <input
                            type="text"
                            id="country"
                            name="country"
                            value="<?php echo htmlspecialchars($_POST['country'] ?? ''); ?>"
                            placeholder="Country (optional)"
                        >
                    </div>
                </div>

                <!-- Email Addresses -->
                <div class="section-title">Email Addresses</div>

                <div class="form-group">
                    <label for="notification_email">Notification Email <span class="required">*</span></label>
                    <div class="helper-text">For new listing alerts</div>
                    <input
                        type="email"
                        id="notification_email"
                        name="notification_email"
                        value="<?php echo htmlspecialchars($_POST['notification_email'] ?? ''); ?>"
                        placeholder="Notification Email"
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="communication_email">Communication Email <span class="required">*</span></label>
                    <div class="helper-text">For team-wide messages</div>
                    <input
                        type="email"
                        id="communication_email"
                        name="communication_email"
                        value="<?php echo htmlspecialchars($_POST['communication_email'] ?? ''); ?>"
                        placeholder="Communication Email"
                        required
                    >
                </div>

                <!-- Security -->
                <div class="section-title">Security</div>

                <div class="form-group">
                    <label for="password">Password <span class="required">*</span></label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="Password"
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="confirm_password">Confirm Password <span class="required">*</span></label>
                    <input
                        type="password"
                        id="confirm_password"
                        name="confirm_password"
                        placeholder="Confirm Password"
                        required
                    >
                </div>

                <!-- Form Actions -->
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        Create an Account
                    </button>
                    <button type="button" class="btn btn-secondary" onclick="window.location.href='<?php echo PUBLIC_URL; ?>/index.php'">Cancel</button>
                </div>
            </form>
            <?php endif; ?>
        </div>

        <?php if (!$is_logged_in): ?>
        <div class="register-footer">
            Account requires approval before activation
        </div>
        <?php endif; ?>
    </div>
</body>
</html>
