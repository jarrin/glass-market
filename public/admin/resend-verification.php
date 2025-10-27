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
    $email = trim($_POST['email'] ?? '');
    
    if (empty($email)) {
        $error_message = 'Please enter your email address.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = 'Please enter a valid email address.';
    } else {
        try {
            $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Find user
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
            $stmt->execute(['email' => $email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user) {
                if (!empty($user['email_verified_at'])) {
                    $error_message = 'This account is already verified. You can login now.';
                } else {
                    // Generate new verification token
                    $verification_token = bin2hex(random_bytes(32));
                    
                    $stmt = $pdo->prepare("UPDATE users SET remember_token = :token WHERE id = :id");
                    $stmt->execute(['token' => $verification_token, 'id' => $user['id']]);
                    
                    // In a real application, send email here
                    $success_message = 'Verification link has been sent to your email.';
                    
                    // For demo purposes, show the link
                    $_SESSION['demo_verification_link'] = 'verify-account.php?token=' . $verification_token . '&email=' . urlencode($email);
                }
            } else {
                // For security, don't reveal if email exists or not
                $success_message = 'If an account exists with this email, a verification link has been sent.';
            }
        } catch (PDOException $e) {
            $error_message = 'An error occurred. Please try again.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resend Verification - Glass Market</title>
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

        .resend-container {
            background: white;
            border-radius: 8px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
            width: 100%;
            max-width: 440px;
            overflow: hidden;
            border: 1px solid rgba(0, 0, 0, 0.06);
        }

        .resend-header {
            background: #201b15;
            padding: 40px 30px;
            text-align: center;
            color: white;
        }

        .resend-header h1 {
            font-size: 28px;
            font-weight: 800;
            margin-bottom: 8px;
            letter-spacing: 0.02em;
        }

        .resend-header p {
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

        .resend-body {
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

        .demo-link {
            margin-top: 12px;
            padding: 10px;
            background: #fffbeb;
            border: 1px solid #fcd34d;
            border-radius: 6px;
            font-size: 12px;
            word-break: break-all;
        }

        .demo-link strong {
            display: block;
            margin-bottom: 6px;
            color: #92400e;
        }

        .demo-link a {
            color: #1e40af;
        }

        .form-group {
            margin-bottom: 24px;
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

        .btn-resend {
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
        }

        .btn-resend:hover {
            background: #161311;
        }

        .resend-footer {
            text-align: center;
            padding: 20px 30px 30px;
            font-size: 14px;
            color: #6b7280;
        }

        .resend-footer a {
            color: #2a2623;
            text-decoration: none;
            font-weight: 500;
        }

        .resend-footer a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="resend-container">
        <div class="resend-header">
            <div class="icon-wrapper">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                </svg>
            </div>
            <h1>Resend Verification</h1>
            <p>Enter your email to receive a new verification link</p>
        </div>

        <div class="resend-body">
            <?php if ($error_message): ?>
                <div class="alert alert-danger">
                    <?php echo htmlspecialchars($error_message); ?>
                </div>
            <?php endif; ?>

            <?php if ($success_message): ?>
                <div class="alert alert-success">
                    <?php echo htmlspecialchars($success_message); ?>
                    <?php if (isset($_SESSION['demo_verification_link'])): ?>
                        <div class="demo-link">
                            <strong>Demo: Click this link to verify</strong>
                            <a href="<?php echo htmlspecialchars($_SESSION['demo_verification_link']); ?>">
                                <?php echo htmlspecialchars($_SESSION['demo_verification_link']); ?>
                            </a>
                        </div>
                        <?php unset($_SESSION['demo_verification_link']); ?>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                        placeholder="your@email.com"
                        required 
                        autofocus
                    >
                </div>

                <button type="submit" class="btn-resend">
                    Resend Verification Link
                </button>
            </form>
        </div>

        <div class="resend-footer">
            <a href="login.php">Back to Login</a> • <a href="register.php">Register</a>
        </div>
    </div>
</body>
</html>
