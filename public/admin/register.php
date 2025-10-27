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
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    // Validation
    if (empty($name) || empty($email) || empty($password) || empty($confirm_password)) {
        $error_message = 'All fields are required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = 'Please enter a valid email address.';
    } elseif (strlen($password) < 8) {
        $error_message = 'Password must be at least 8 characters long.';
    } elseif ($password !== $confirm_password) {
        $error_message = 'Passwords do not match.';
    } else {
        try {
            $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Check if email already exists
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email");
            $stmt->execute(['email' => $email]);
            
            if ($stmt->fetch()) {
                $error_message = 'This email is already registered.';
            } else {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                
                // Insert user with email_verified_at as NULL (pending admin approval)
                $stmt = $pdo->prepare("
                    INSERT INTO users (name, email, password, email_verified_at) 
                    VALUES (:name, :email, :password, NULL)
                ");
                
                $stmt->execute([
                    'name' => $name,
                    'email' => $email,
                    'password' => $hashed_password
                ]);
                
                // Set success message
                $success_message = 'Registration successful! Your account is pending admin approval. You will be able to login once an administrator verifies your account.';
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
            font-family: ui-serif, Georgia, 'Times New Roman', Times, serif;
            background: #f3eee6;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .register-container {
            background: white;
            border-radius: 8px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
            width: 100%;
            max-width: 480px;
            overflow: hidden;
            border: 1px solid rgba(0, 0, 0, 0.06);
        }

        .register-header {
            background: #201b15;
            padding: 40px 30px;
            text-align: center;
            color: white;
        }

        .register-header h1 {
            font-size: 28px;
            font-weight: 800;
            margin-bottom: 8px;
            letter-spacing: 0.02em;
        }

        .register-header p {
            font-size: 14px;
            opacity: 0.9;
        }

        .icon-wrapper {
            width: 64px;
            height: 64px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
        }

        .icon-wrapper svg {
            width: 32px;
            height: 32px;
            stroke: white;
        }

        .register-body {
            padding: 40px 30px;
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
            font-size: 14px;
            font-weight: 500;
            color: #374151;
            margin-bottom: 8px;
        }

        .form-group input {
            width: 100%;
            padding: 12px 16px;
            font-size: 15px;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            transition: all 0.3s ease;
            outline: none;
        }

        .form-group input:focus {
            border-color: #2a2623;
            box-shadow: 0 0 0 3px rgba(42, 38, 35, 0.08);
        }

        .form-group input.error {
            border-color: #ef4444;
        }

        .password-requirements {
            font-size: 12px;
            color: #6b5f56;
            margin-top: 6px;
        }

        .btn-register {
            width: 100%;
            padding: 14px;
            font-size: 16px;
            font-weight: 600;
            color: white;
            background: #2a2623;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background 0.2s ease;
            margin-top: 10px;
        }

        .btn-register:hover {
            background: #161311;
        }

        .btn-register:active {
            background: #0f0c0a;
        }

        .register-footer {
            text-align: center;
            padding: 20px 30px 30px;
            font-size: 14px;
            color: #6b7280;
        }

        .register-footer a {
            color: #2a2623;
            text-decoration: none;
            font-weight: 500;
        }

        .register-footer a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="register-header">
            <div class="icon-wrapper">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zM4 19.235v-.11a6.375 6.375 0 0112.75 0v.109A12.318 12.318 0 0110.374 21c-2.331 0-4.512-.645-6.374-1.766z" />
                </svg>
            </div>
            <h1>Create Account</h1>
            <p>Register for Glass Market Customer Access</p>
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
                <div class="form-group">
                    <label for="name">Full Name</label>
                    <input 
                        type="text" 
                        id="name" 
                        name="name" 
                        value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>"
                        placeholder="John Doe"
                        required 
                        autofocus
                    >
                </div>

                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                        placeholder="john@example.com"
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        placeholder="Create a strong password"
                        required
                    >
                    <div class="password-requirements">
                        Must be at least 8 characters long
                    </div>
                </div>

                <div class="form-group">
                    <label for="confirm_password">Confirm Password</label>
                    <input 
                        type="password" 
                        id="confirm_password" 
                        name="confirm_password" 
                        placeholder="Re-enter your password"
                        required
                    >
                </div>

                <button type="submit" class="btn-register">
                    Create Account
                </button>
            </form>
        </div>

        <div class="register-footer">
            Already have an account? <a href="login.php">Sign In</a>
        </div>
    </div>
</body>
</html>
