<?php
session_start();

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
    $company_name = trim($_POST['company_name'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $city = trim($_POST['city'] ?? '');
    $country = trim($_POST['country'] ?? '');
    $notification_email = trim($_POST['notification_email'] ?? '');
    $communication_email = trim($_POST['communication_email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    // Validation
    if (empty($name) || empty($company_name) || empty($address) || empty($city) || empty($country) || 
        empty($notification_email) || empty($communication_email) || empty($password) || empty($confirm_password)) {
        $error_message = 'All fields are required.';
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
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email OR email = :comm_email");
            $stmt->execute(['email' => $notification_email, 'comm_email' => $communication_email]);
            
            if ($stmt->fetch()) {
                $error_message = 'This email is already registered.';
            } else {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                
                // Insert user with email_verified_at as NULL (pending admin approval)
                // Note: Adjust column names based on your actual database schema
                $stmt = $pdo->prepare("
                    INSERT INTO users (name, email, password, email_verified_at) 
                    VALUES (:name, :email, :password, NULL)
                ");
                
                $stmt->execute([
                    'name' => $name,
                    'email' => $notification_email,
                    'password' => $hashed_password
                ]);
                
                // Set success message
                $success_message = 'Registration successful! Your account is pending admin approval. You will be able to access the platform once an administrator verifies your account.';
            }
        } catch (PDOException $e) {
            $error_message = 'Registration failed: ' . $e->getMessage();
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

        .credit-card-section {
            background: #fafafa;
            border: 1.5px solid #e0e0e0;
            border-radius: 8px;
            padding: 20px;
            margin: 16px 0;
        }

        .card-row {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr;
            gap: 12px;
        }

        .card-icon {
            display: flex;
            gap: 8px;
            margin-bottom: 12px;
        }

        .card-icon svg {
            width: 32px;
            height: 20px;
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
            border: none;
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
    </style>
    <script>
        // Format card number with spaces
        document.addEventListener('DOMContentLoaded', function() {
            const cardNumber = document.getElementById('card_number');
            const cardExpiry = document.getElementById('card_expiry');
            
            if (cardNumber) {
                cardNumber.addEventListener('input', function(e) {
                    let value = e.target.value.replace(/\s/g, '');
                    let formattedValue = value.match(/.{1,4}/g)?.join(' ') || value;
                    e.target.value = formattedValue;
                });
            }
            
            if (cardExpiry) {
                cardExpiry.addEventListener('input', function(e) {
                    let value = e.target.value.replace(/\D/g, '');
                    if (value.length >= 2) {
                        value = value.slice(0, 2) + '/' + value.slice(2, 4);
                    }
                    e.target.value = value;
                });
            }
        });
    </script>
</head>
<body>
    <div class="register-container">
        <div class="register-header">
            <h1>Create Account</h1>
            <div class="subtitle">First 3 months FREE, then subscription required</div>
        </div>

        <div class="register-body">
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
                    <label for="company_name">Company name <span class="required">*</span></label>
                    <input 
                        type="text" 
                        id="company_name" 
                        name="company_name" 
                        value="<?php echo htmlspecialchars($_POST['company_name'] ?? ''); ?>"
                        placeholder="Company name..."
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="address">Address <span class="required">*</span></label>
                    <input 
                        type="text" 
                        id="address" 
                        name="address" 
                        value="<?php echo htmlspecialchars($_POST['address'] ?? ''); ?>"
                        placeholder="Address"
                        required
                    >
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="city">City <span class="required">*</span></label>
                        <input 
                            type="text" 
                            id="city" 
                            name="city" 
                            value="<?php echo htmlspecialchars($_POST['city'] ?? ''); ?>"
                            placeholder="City"
                            required
                        >
                    </div>
                    <div class="form-group">
                        <label for="country">Country <span class="required">*</span></label>
                        <input 
                            type="text" 
                            id="country" 
                            name="country" 
                            value="<?php echo htmlspecialchars($_POST['country'] ?? ''); ?>"
                            placeholder="Country"
                            required
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

                <!-- Payment Method -->
                <div class="section-title">Payment Method</div>
                
                <div class="credit-card-section">
                    <div class="card-icon">
                        <!-- Visa -->
                        <svg viewBox="0 0 48 32" fill="none">
                            <rect width="48" height="32" rx="4" fill="#1434CB"/>
                            <path d="M21.5 20.5L19.5 11.5H17L19.5 20.5H21.5ZM28 11.8L26.2 16.5L25.8 14.5L24.8 11.8C24.7 11.6 24.5 11.5 24.3 11.5H20.5L20.4 11.8C21.5 12 22.5 12.5 23.3 13.2L25.8 20.5H28.2L31.5 11.5H29.2L28 11.8ZM34 20.5L35.5 11.5H33.2L31.7 20.5H34ZM15.5 11.5L13 20.5H15.3L17.8 11.5H15.5Z" fill="white"/>
                        </svg>
                        <!-- Mastercard -->
                        <svg viewBox="0 0 48 32" fill="none">
                            <rect width="48" height="32" rx="4" fill="#EB001B"/>
                            <circle cx="19" cy="16" r="8" fill="#FF5F00"/>
                            <circle cx="29" cy="16" r="8" fill="#F79E1B"/>
                        </svg>
                    </div>
                    
                    <div class="form-group">
                        <label for="card_number">Card Number <span class="required">*</span></label>
                        <input 
                            type="text" 
                            id="card_number" 
                            name="card_number" 
                            placeholder="1234 5678 9012 3456"
                            maxlength="19"
                            pattern="[0-9\s]*"
                        >
                    </div>

                    <div class="form-group">
                        <label for="card_name">Cardholder Name <span class="required">*</span></label>
                        <input 
                            type="text" 
                            id="card_name" 
                            name="card_name" 
                            placeholder="JOHN DOE"
                            style="text-transform: uppercase;"
                        >
                    </div>

                    <div class="card-row">
                        <div class="form-group">
                            <label for="card_expiry">Expiry Date <span class="required">*</span></label>
                            <input 
                                type="text" 
                                id="card_expiry" 
                                name="card_expiry" 
                                placeholder="MM/YY"
                                maxlength="5"
                                pattern="[0-9/]*"
                            >
                        </div>
                        <div class="form-group">
                            <label for="card_cvv">CVV <span class="required">*</span></label>
                            <input 
                                type="text" 
                                id="card_cvv" 
                                name="card_cvv" 
                                placeholder="123"
                                maxlength="4"
                                pattern="[0-9]*"
                            >
                        </div>
                        <div class="form-group">
                            <label for="card_zip">ZIP Code <span class="required">*</span></label>
                            <input 
                                type="text" 
                                id="card_zip" 
                                name="card_zip" 
                                placeholder="12345"
                                maxlength="10"
                            >
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        Create an Account
                    </button>
                    <button type="button" class="btn btn-secondary" onclick="window.location.href='login.php'">
                        Cancel
                    </button>
                </div>
            </form>
        </div>

        <div class="register-footer">
            Account required approval before activation
        </div>
    </div>
</body>
</html>
