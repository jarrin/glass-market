<?php
session_start();

// Database connection
require_once __DIR__ . '/../../config.php';

// Redirect if already logged in
if (isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true) {
    header('Location: ' . PUBLIC_URL . '/index.php');
    exit;
}

// Database credentials
$db_host = '127.0.0.1';
$db_name = 'glass_market';
$db_user = 'root';
$db_pass = '';

$error_message = '';
$success_message = '';

// Check for registration success message
if (isset($_SESSION['registration_success'])) {
    $success_message = $_SESSION['registration_success'];
    unset($_SESSION['registration_success']);
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']);
    
    if (empty($email) || empty($password)) {
        $error_message = 'Please enter both email and password.';
    } else {
        try {
            $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Check user credentials
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
            $stmt->execute(['email' => $email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user && password_verify($password, $user['password'])) {
                // Block admin from logging in here - must use admin login
                if ($user['email'] === 'admin@glassmarket.com') {
                    $error_message = 'Admin accounts must use the admin login portal.';
                } elseif (empty($user['email_verified_at'])) {
                    // Check if account is verified
                    $error_message = 'The admin still needs to verify your email';
                } else {
                    // Login successful - regular user only
                    $_SESSION['user_logged_in'] = true;
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_name'] = $user['name'];
                    $_SESSION['user_email'] = $user['email'];
                    $_SESSION['user_avatar'] = $user['avatar'] ?? '';
                    
                    // Handle remember me
                    if ($remember) {
                        setcookie('remember_user', $user['email'], time() + (86400 * 30), '/');
                    }
                    
                    // Redirect to home page
                    header('Location: ' . PUBLIC_URL . '/index.php');
                    exit;
                }
            } else {
                $error_message = 'Invalid email or password.';
            }
        } catch (PDOException $e) {
            $error_message = 'Database connection failed. Please try again later.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Glass Market</title>
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

        .auth-container {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            width: 100%;
            max-width: 480px;
            border: 1px solid #e0e0e0;
            overflow: hidden;
        }

        .auth-header {
            padding: 32px 32px 20px;
            background: #fafafa;
            border-bottom: 2px solid #f5f5f5;
        }

        .auth-header h1 {
            font-size: 22px;
            font-weight: 800;
            color: #000;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 10px;
        }

        .auth-header .subtitle {
            font-size: 13px;
            color: #555;
            background: #fff;
            padding: 10px 16px;
            border-radius: 6px;
            border: 1px solid #e0e0e0;
            text-align: center;
        }

        .auth-body {
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

        .form-group label {
            display: block;
            font-size: 12px;
            font-weight: 600;
            color: #222;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .form-group label .required {
            color: #e53e3e;
        }

        .form-group input {
            width: 100%;
            padding: 12px 14px;
            font-size: 14px;
            border: 1.5px solid #ddd;
            border-radius: 6px;
            background: #fafafa;
            transition: all 0.2s ease;
            outline: none;
        }

        .form-group input:focus {
            border-color: #000;
            background: #fff;
            box-shadow: 0 0 0 3px rgba(0, 0, 0, 0.08);
        }

        .form-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 18px;
            font-size: 13px;
            color: #555;
        }

        .remember-me {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            font-size: 13px;
        }

        .remember-me input[type="checkbox"] {
            width: 16px;
            height: 16px;
            accent-color: #111;
            cursor: pointer;
        }

        .forgot-link {
            color: #111;
            font-weight: 600;
            text-decoration: none;
        }

        .forgot-link:hover {
            text-decoration: underline;
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
            border: none;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: all 0.2s ease;
        }

        .btn-primary {
            flex: 1;
            background: #000;
            color: #fff;
        }

        .btn-primary:hover {
            background: #333;
        }

        .btn-secondary {
            background: transparent;
            color: #666;
        }

        .btn-secondary:hover {
            color: #000;
        }

        .auth-footer {
            background: #fafafa;
            border-top: 2px solid #f5f5f5;
            padding: 20px 32px 28px;
            text-align: center;
            font-size: 13px;
            color: #555;
        }

        .auth-footer a {
            color: #111;
            font-weight: 600;
            text-decoration: none;
        }

        .auth-footer a:hover {
            text-decoration: underline;
        }

        @media (max-width: 520px) {
            .form-actions {
                flex-direction: column;
            }

            .btn {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="auth-header">
            <h1>Welcome Back</h1>
            <div class="subtitle">Sign in to your Glass Market account</div>
        </div>

        <div class="auth-body">
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
                <div class="form-group">
                    <label for="email">Email Address <span class="required">*</span></label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                        placeholder="you@example.com"
                        required
                        autofocus
                    >
                </div>

                <div class="form-group">
                    <label for="password">Password <span class="required">*</span></label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="Enter your password"
                        required
                    >
                </div>

                <div class="form-meta">
                    <label class="remember-me">
                        <input type="checkbox" name="remember">
                        <span>Remember me</span>
                    </label>
                    <a href="#" class="forgot-link">Forgot password?</a>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Sign In</button>
                    <button type="button" class="btn btn-secondary" onclick="window.location.href='<?php echo PUBLIC_URL; ?>/index.php'">Cancel</button>
                </div>
            </form>
        </div>

        <div class="auth-footer">
            Don't have an account? <a href="register.php">Create one now</a>
        </div>
    </div>
</body>
</html>
