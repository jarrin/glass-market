<?php
/**
 * Change Password Page
 * Allows users to change their password with confirmation email
 */

session_start();
require_once __DIR__ . '/../../includes/db_connect.php';
require_once __DIR__ . '/../../app/Services/RustMailer.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ' . VIEWS_URL . '/login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$success_message = '';
$error_message = '';

// Handle password change form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Validation
    if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
        $error_message = 'All fields are required.';
    } elseif ($new_password !== $confirm_password) {
        $error_message = 'New passwords do not match.';
    } elseif (strlen($new_password) < 8) {
        $error_message = 'New password must be at least 8 characters long.';
    } else {
        try {
            // Get user's current password hash and email
            $stmt = $pdo->prepare("SELECT password, email, name FROM users WHERE id = ?");
            $stmt->execute([$user_id]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user) {
                $error_message = 'User not found.';
            } elseif (!password_verify($current_password, $user['password'])) {
                $error_message = 'Current password is incorrect.';
            } else {
                // Hash new password
                $new_password_hash = password_hash($new_password, PASSWORD_BCRYPT);

                // Update password
                $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
                $stmt->execute([$new_password_hash, $user_id]);

                // Send confirmation email
                try {
                    $mailer = new \App\Services\RustMailer();
                    $emailResult = $mailer->sendPasswordChangedEmail(
                        $user['email'],
                        $user['name']
                    );

                    if ($emailResult['success']) {
                        $success_message = 'Password changed successfully! A confirmation email has been sent.';
                    } else {
                        $success_message = 'Password changed successfully! (Email notification failed: ' . $emailResult['message'] . ')';
                    }
                } catch (Exception $e) {
                    $success_message = 'Password changed successfully! (Email notification could not be sent)';
                }

                // Clear form values on success
                $current_password = '';
                $new_password = '';
                $confirm_password = '';
            }
        } catch (PDOException $e) {
            error_log("Password change error: " . $e->getMessage());
            $error_message = 'An error occurred. Please try again.';
        }
    }
}

// Get user info for display
try {
    $stmt = $pdo->prepare("SELECT name, email FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Error fetching user: " . $e->getMessage());
    header('Location: ' . VIEWS_URL . '/profile.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password - Glass Market</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            max-width: 500px;
            width: 100%;
            padding: 40px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .header h1 {
            font-size: 28px;
            color: #1f2937;
            margin-bottom: 8px;
        }

        .header p {
            color: #6b7280;
            font-size: 14px;
        }

        .alert {
            padding: 16px;
            border-radius: 12px;
            margin-bottom: 24px;
            font-size: 14px;
        }

        .alert-success {
            background: #d1fae5;
            border: 1px solid #6ee7b7;
            color: #065f46;
        }

        .alert-error {
            background: #fee2e2;
            border: 1px solid #fca5a5;
            color: #991b1b;
        }

        .form-group {
            margin-bottom: 24px;
        }

        .form-group label {
            display: block;
            font-weight: 600;
            color: #374151;
            margin-bottom: 8px;
            font-size: 14px;
        }

        .form-group input {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e5e7eb;
            border-radius: 10px;
            font-size: 15px;
            transition: all 0.2s;
        }

        .form-group input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .password-requirements {
            font-size: 12px;
            color: #6b7280;
            margin-top: 6px;
        }

        .btn {
            width: 100%;
            padding: 14px;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            margin-bottom: 12px;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }

        .btn-secondary {
            background: #f3f4f6;
            color: #374151;
        }

        .btn-secondary:hover {
            background: #e5e7eb;
        }

        .user-info {
            background: #f9fafb;
            padding: 16px;
            border-radius: 10px;
            margin-bottom: 24px;
            font-size: 14px;
        }

        .user-info strong {
            color: #374151;
        }

        .user-info span {
            color: #6b7280;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔐 Change Password</h1>
            <p>Update your account password</p>
        </div>

        <div class="user-info">
            <strong>Account:</strong> <span><?php echo htmlspecialchars($user['email']); ?></span>
        </div>

        <?php if ($success_message): ?>
            <div class="alert alert-success">
                ✅ <?php echo htmlspecialchars($success_message); ?>
            </div>
        <?php endif; ?>

        <?php if ($error_message): ?>
            <div class="alert alert-error">
                ❌ <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label for="current_password">Current Password</label>
                <input 
                    type="password" 
                    id="current_password" 
                    name="current_password" 
                    required
                    autocomplete="current-password"
                >
            </div>

            <div class="form-group">
                <label for="new_password">New Password</label>
                <input 
                    type="password" 
                    id="new_password" 
                    name="new_password" 
                    required
                    minlength="8"
                    autocomplete="new-password"
                >
                <div class="password-requirements">
                    Must be at least 8 characters long
                </div>
            </div>

            <div class="form-group">
                <label for="confirm_password">Confirm New Password</label>
                <input 
                    type="password" 
                    id="confirm_password" 
                    name="confirm_password" 
                    required
                    minlength="8"
                    autocomplete="new-password"
                >
            </div>

            <button type="submit" class="btn btn-primary">
                Update Password
            </button>

            <a href="<?php echo VIEWS_URL; ?>/profile.php" class="btn btn-secondary">
                Cancel
            </a>
        </form>
    </div>
</body>
</html>
