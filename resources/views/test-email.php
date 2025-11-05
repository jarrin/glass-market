<?php
session_start();
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../app/Services/RustMailer.php';

use App\Services\RustMailer;

// Require authentication
if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
    header('Location: ' . VIEWS_URL . '/login.php');
    exit;
}

$message = '';
$error = '';

// Handle test email send
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_test'])) {
    $test_email = trim($_POST['test_email'] ?? '');
    $test_name = trim($_POST['test_name'] ?? 'Test User');
    $notification_type = $_POST['notification_type'] ?? 'simple';
    
    if (empty($test_email) || !filter_var($test_email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address';
    } else {
        try {
            $mailer = new RustMailer();
            
            switch ($notification_type) {
                case 'simple':
                    $result = $mailer->send(
                        $test_email,
                        'Test Email from Glass Market',
                        '<h2>Hello from Glass Market! 👋</h2><p>This is a test email to verify your email notifications are working correctly.</p>',
                        true,
                        $test_name
                    );
                    break;
                    
                case 'welcome':
                    $result = $mailer->sendWelcomeEmail(
                        $test_email,
                        $test_name
                    );
                    break;
                    
                case 'new_listing':
                    $result = $mailer->sendListingNotification(
                        $test_email,
                        'Clear Glass - 10.5 tons',
                        'http://localhost/glass-market/resources/views/listing-detail.php?id=999'
                    );
                    break;
                    
                case 'password_changed':
                    $result = $mailer->sendPasswordChangedEmail(
                        $test_email,
                        $test_name
                    );
                    break;
                    
                default:
                    $error = 'Invalid notification type';
                    $result = ['success' => false];
            }
            
            if ($result['success']) {
                $message = "✅ Test email sent successfully to $test_email using Rust mailer!";
            } else {
                $error = "❌ Failed to send email: " . ($result['message'] ?? 'Unknown error');
            }
        } catch (Exception $e) {
            $error = "❌ Error: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Email Notifications - Glass Market</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #f5f7fa;
            padding: 40px 20px;
        }
        
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            border-radius: 16px;
            padding: 40px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }
        
        h1 {
            font-size: 28px;
            margin-bottom: 10px;
            color: #1f2937;
        }
        
        .subtitle {
            color: #6b7280;
            margin-bottom: 30px;
        }
        
        .alert {
            padding: 16px 20px;
            border-radius: 8px;
            margin-bottom: 24px;
            font-size: 14px;
        }
        
        .alert-success {
            background: #d1fae5;
            color: #065f46;
            border: 1px solid #6ee7b7;
        }
        
        .alert-error {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fca5a5;
        }
        
        .form-group {
            margin-bottom: 24px;
        }
        
        label {
            display: block;
            font-weight: 600;
            margin-bottom: 8px;
            color: #374151;
            font-size: 14px;
        }
        
        input[type="email"],
        select {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            font-size: 14px;
            transition: border-color 0.2s;
        }
        
        input[type="email"]:focus,
        select:focus {
            outline: none;
            border-color: #2f6df5;
            box-shadow: 0 0 0 3px rgba(47, 109, 245, 0.1);
        }
        
        button {
            width: 100%;
            padding: 14px 24px;
            background: #2f6df5;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s;
        }
        
        button:hover {
            background: #1e5ad7;
        }
        
        .info-box {
            background: #eff6ff;
            border-left: 4px solid #3b82f6;
            padding: 16px;
            border-radius: 8px;
            margin-bottom: 24px;
            font-size: 14px;
            color: #1e40af;
        }
        
        .back-link {
            display: inline-block;
            color: #2f6df5;
            text-decoration: none;
            font-size: 14px;
            margin-bottom: 20px;
        }
        
        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="<?php echo VIEWS_URL; ?>/profile.php?tab=notifications" class="back-link">
            ← Back to Notifications
        </a>
        
        <h1>📧 Test Email Notifications</h1>
        <p class="subtitle">Send test emails to verify your notification settings</p>
        
        <?php if ($message): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <div class="info-box">
            💡 <strong>Using Rust Mailer:</strong> This test page uses the high-performance Rust email service. 
            Make sure your Gmail credentials are configured in the .env file (GMAIL_FROM_EMAIL and GOOGLE_APP_SECRET).
        </div>
        
        <form method="POST">
            <div class="form-group">
                <label for="test_email">Email Address</label>
                <input 
                    type="email" 
                    id="test_email" 
                    name="test_email" 
                    placeholder="Enter email to receive test notification"
                    required
                >
            </div>
            
            <div class="form-group">
                <label for="test_name">Recipient Name</label>
                <input 
                    type="text" 
                    id="test_name" 
                    name="test_name" 
                    placeholder="Enter recipient name"
                    value="Test User"
                    required
                >
            </div>
            
            <div class="form-group">
                <label for="notification_type">Notification Type</label>
                <select id="notification_type" name="notification_type" required>
                    <option value="simple">Simple Test Email</option>
                    <option value="welcome">Welcome Email</option>
                    <option value="new_listing">New Listing Notification</option>
                    <option value="password_changed">Password Changed Alert</option>
                </select>
            </div>
            
            <button type="submit" name="send_test">
                🚀 Send Test Email (via Rust Mailer)
            </button>
        </form>
    </div>
</body>
</html>
