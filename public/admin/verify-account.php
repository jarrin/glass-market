<?php
session_start();

// Database connection
$db_host = '127.0.0.1';
$db_name = 'glass_market';
$db_user = 'root';
$db_pass = '';

$verification_status = 'pending'; // pending, success, error
$message = '';
$email = $_GET['email'] ?? '';
$token = $_GET['token'] ?? '';

// Handle verification
if (!empty($token) && !empty($email)) {
    try {
        $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Find user with matching token and email
        $stmt = $pdo->prepare("
            SELECT id, name, email, email_verified_at 
            FROM users 
            WHERE email = :email AND remember_token = :token
        ");
        $stmt->execute(['email' => $email, 'token' => $token]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user) {
            if ($user['email_verified_at']) {
                $verification_status = 'already_verified';
                $message = 'Your account has already been verified. You can now login.';
            } else {
                // Verify the account
                $stmt = $pdo->prepare("
                    UPDATE users 
                    SET email_verified_at = NOW() 
                    WHERE id = :id
                ");
                $stmt->execute(['id' => $user['id']]);
                
                $verification_status = 'success';
                $message = 'Your account has been successfully verified!';
            }
        } else {
            $verification_status = 'error';
            $message = 'Invalid verification link. Please try again or contact support.';
        }
    } catch (PDOException $e) {
        $verification_status = 'error';
        $message = 'Verification failed: ' . $e->getMessage();
    }
} else {
    $verification_status = 'error';
    $message = 'Invalid verification link.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Account - Glass Market</title>
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

        .verify-container {
            background: white;
            border-radius: 8px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
            width: 100%;
            max-width: 520px;
            overflow: hidden;
            border: 1px solid rgba(0, 0, 0, 0.06);
            text-align: center;
        }

        .verify-body {
            padding: 60px 40px;
        }

        .icon-wrapper {
            width: 96px;
            height: 96px;
            margin: 0 auto 24px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .icon-wrapper.pending {
            background: #fef3c7;
        }

        .icon-wrapper.success {
            background: #d1fae5;
        }

        .icon-wrapper.error {
            background: #fecaca;
        }

        .icon-wrapper.already_verified {
            background: #dbeafe;
        }

        .icon-wrapper svg {
            width: 48px;
            height: 48px;
        }

        .icon-wrapper.pending svg {
            stroke: #d97706;
        }

        .icon-wrapper.success svg {
            stroke: #059669;
        }

        .icon-wrapper.error svg {
            stroke: #dc2626;
        }

        .icon-wrapper.already_verified svg {
            stroke: #2563eb;
        }

        h1 {
            font-size: 32px;
            font-weight: 800;
            color: #1f1a17;
            margin-bottom: 12px;
            letter-spacing: 0.02em;
        }

        .message {
            font-size: 16px;
            color: #6b5f56;
            line-height: 1.6;
            margin-bottom: 32px;
        }

        .email-display {
            background: #faf6ef;
            padding: 12px 20px;
            border-radius: 8px;
            font-size: 14px;
            color: #2a2623;
            margin-bottom: 32px;
            font-weight: 600;
        }

        .action-buttons {
            display: flex;
            gap: 12px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn {
            padding: 14px 28px;
            font-size: 15px;
            font-weight: 600;
            border-radius: 8px;
            text-decoration: none;
            display: inline-block;
            transition: all 0.2s ease;
            cursor: pointer;
            border: none;
        }

        .btn-primary {
            background: #2a2623;
            color: white;
        }

        .btn-primary:hover {
            background: #161311;
        }

        .btn-secondary {
            background: transparent;
            color: #2a2623;
            border: 2px solid #2a2623;
        }

        .btn-secondary:hover {
            background: #faf6ef;
        }

        .verify-footer {
            padding: 24px;
            background: #faf6ef;
            border-top: 1px solid rgba(0, 0, 0, 0.06);
            font-size: 13px;
            color: #6b5f56;
        }

        .verify-footer a {
            color: #2a2623;
            text-decoration: none;
            font-weight: 600;
        }

        .verify-footer a:hover {
            text-decoration: underline;
        }

        @keyframes checkmark {
            0% {
                stroke-dashoffset: 100;
            }
            100% {
                stroke-dashoffset: 0;
            }
        }

        .checkmark {
            stroke-dasharray: 100;
            stroke-dashoffset: 100;
            animation: checkmark 0.6s ease-in-out 0.3s forwards;
        }
    </style>
</head>
<body>
    <div class="verify-container">
        <div class="verify-body">
            <!-- Pending Status -->
            <?php if ($verification_status === 'pending'): ?>
                <div class="icon-wrapper pending">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h1>Verify Your Account</h1>
                <p class="message">We've sent a verification link to your email. Please check your inbox and click the link to verify your account.</p>
                <?php if ($email): ?>
                    <div class="email-display"><?php echo htmlspecialchars($email); ?></div>
                <?php endif; ?>

            <!-- Success Status -->
            <?php elseif ($verification_status === 'success'): ?>
                <div class="icon-wrapper success">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path class="checkmark" stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h1>Account Verified!</h1>
                <p class="message"><?php echo htmlspecialchars($message); ?> You can now login to your account.</p>
                <?php if ($email): ?>
                    <div class="email-display"><?php echo htmlspecialchars($email); ?></div>
                <?php endif; ?>
                <div class="action-buttons">
                    <a href="login.php" class="btn btn-primary">Go to Login</a>
                </div>

            <!-- Already Verified -->
            <?php elseif ($verification_status === 'already_verified'): ?>
                <div class="icon-wrapper already_verified">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
                    </svg>
                </div>
                <h1>Already Verified</h1>
                <p class="message"><?php echo htmlspecialchars($message); ?></p>
                <div class="action-buttons">
                    <a href="login.php" class="btn btn-primary">Go to Login</a>
                </div>

            <!-- Error Status -->
            <?php else: ?>
                <div class="icon-wrapper error">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                    </svg>
                </div>
                <h1>Verification Failed</h1>
                <p class="message"><?php echo htmlspecialchars($message); ?></p>
                <div class="action-buttons">
                    <a href="register.php" class="btn btn-secondary">Register Again</a>
                    <a href="login.php" class="btn btn-primary">Go to Login</a>
                </div>
            <?php endif; ?>
        </div>

        <div class="verify-footer">
            Need help? <a href="#">Contact Support</a> • <a href="/">Back to Home</a>
        </div>
    </div>
</body>
</html>
