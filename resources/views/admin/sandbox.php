<?php
// Admin guard - ensures only admins can access
require_once __DIR__ . '/../../../includes/admin-guard.php';

// Database connection
$db_host = '127.0.0.1';
$db_name = 'glass_market';
$db_user = 'root';
$db_pass = '';

$success_message = '';
$error_message = '';
$mollie_status = 'Not installed';

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Load composer autoload (for Mollie library)
    $autoloadPath = __DIR__ . '/../../../vendor/autoload.php';
    if (file_exists($autoloadPath)) {
        require_once $autoloadPath;
    }
    
    // Load Mollie class
    require_once __DIR__ . '/../../../database/classes/mollie.php';
    require_once __DIR__ . '/../../../database/classes/subscriptions.php';
    
    $mollie = new MolliePayment();
    
    // Get debug info
    $debug_info = $mollie->getDebugInfo();
    
    // Check if Mollie is configured
    if ($mollie->isConfigured()) {
        $mollie_status = '✅ Configured (' . $mollie->getApiKey() . ')';
    } else {
        $mollie_status = '❌ Not configured - Check .env file';
    }
    
    // Handle form submissions
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $action = $_POST['action'] ?? '';
        
        // Change user creation date
        if ($action === 'change_date') {
            $user_id = $_POST['user_id'] ?? 0;
            $new_date = $_POST['new_date'] ?? '';
            
            if ($user_id && $new_date) {
                $stmt = $pdo->prepare("UPDATE users SET created_at = :new_date WHERE id = :id");
                if ($stmt->execute(['new_date' => $new_date, 'id' => $user_id])) {
                    $success_message = "User creation date updated to {$new_date}";
                } else {
                    $error_message = 'Failed to update creation date';
                }
            }
        }
        
        // Change subscription date
        elseif ($action === 'change_subscription') {
            $user_id = $_POST['user_id'] ?? 0;
            $start_date = $_POST['start_date'] ?? '';
            $end_date = $_POST['end_date'] ?? '';
            
            if ($user_id && $start_date && $end_date) {
                // Check if subscription exists
                $stmt = $pdo->prepare("SELECT id FROM user_subscriptions WHERE user_id = :user_id");
                $stmt->execute(['user_id' => $user_id]);
                $subscription = $stmt->fetch();
                
                if ($subscription) {
                    // Update existing subscription
                    $stmt = $pdo->prepare("
                        UPDATE user_subscriptions 
                        SET start_date = :start_date, end_date = :end_date, is_active = 1
                        WHERE user_id = :user_id
                    ");
                    $stmt->execute([
                        'start_date' => $start_date,
                        'end_date' => $end_date,
                        'user_id' => $user_id
                    ]);
                    $success_message = "Subscription dates updated for user";
                } else {
                    // Create new subscription
                    $stmt = $pdo->prepare("
                        INSERT INTO user_subscriptions (user_id, start_date, end_date, is_trial, is_active)
                        VALUES (:user_id, :start_date, :end_date, 1, 1)
                    ");
                    $stmt->execute([
                        'user_id' => $user_id,
                        'start_date' => $start_date,
                        'end_date' => $end_date
                    ]);
                    $success_message = "Trial subscription created for user";
                }
            }
        }
        
        // Test payment creation
        elseif ($action === 'test_payment') {
            $user_id = $_POST['user_id'] ?? 0;
            $months = $_POST['months'] ?? 1;
            
            if (!$user_id) {
                $error_message = 'Invalid user ID';
            } elseif (!$mollie->isConfigured()) {
                $error_message = 'Mollie not configured. Check .env file for API keys.';
            } else {
                $result = $mollie->createSubscriptionPayment($user_id, $months, $pdo);
                if (is_array($result) && isset($result['error'])) {
                    // Error returned with message
                    $error_message = 'Payment Error: ' . $result['error'];
                } elseif ($result) {
                    // Success - redirect to Mollie payment page
                    header('Location: ' . $result);
                    exit;
                } else {
                    $error_message = 'Failed to create payment. Check PHP error log at C:\\xampp\\php\\logs\\php_error_log';
                }
            }
        }
        
        // Reset to defaults
        elseif ($action === 'reset_user') {
            $user_id = $_POST['user_id'] ?? 0;
            
            if ($user_id) {
                // Reset user creation date to now
                $stmt = $pdo->prepare("UPDATE users SET created_at = NOW() WHERE id = :id");
                $stmt->execute(['id' => $user_id]);
                
                // Delete subscription
                $stmt = $pdo->prepare("DELETE FROM user_subscriptions WHERE user_id = :id");
                $stmt->execute(['id' => $user_id]);
                
                $success_message = "User reset to defaults (no subscription, current date)";
            }
        }
    }
    
    // Get all users for testing
    $users = $pdo->query("
        SELECT 
            u.id,
            u.name,
            u.email,
            u.created_at,
            us.start_date,
            us.end_date,
            us.is_trial,
            us.is_active,
            CASE 
                WHEN us.end_date >= CURDATE() AND us.is_active = 1 THEN 'Active'
                WHEN us.end_date < CURDATE() THEN 'Expired'
                ELSE 'None'
            END as subscription_status,
            DATEDIFF(us.end_date, CURDATE()) as days_remaining
        FROM users u
        LEFT JOIN user_subscriptions us ON u.id = us.user_id
        WHERE u.email != 'admin@glassmarket.com'
        ORDER BY u.id ASC
    ")->fetchAll(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    $error_message = "Database error: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sandbox Testing - Glass Market Admin</title>
    <link rel="stylesheet" href="/glass-market/public/css/admin-dashboard.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #f3f4f6;
            color: #1f2937;
        }

        .header {
            background: #1f2937;
            color: white;
            padding: 20px 0;
            margin-bottom: 32px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .header-content {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 32px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header h1 {
            font-size: 24px;
            font-weight: 700;
        }

        .header a {
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            background: rgba(255,255,255,0.1);
            border-radius: 6px;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.2s;
        }

        .header a:hover {
            background: rgba(255,255,255,0.2);
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 32px;
        }

        .alert {
            padding: 16px 20px;
            border-radius: 8px;
            margin-bottom: 24px;
            font-size: 14px;
            font-weight: 500;
        }

        .alert-success {
            background: #d1fae5;
            color: #065f46;
            border: 1px solid #6ee7b7;
        }

        .alert-danger {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fca5a5;
        }

        .alert-warning {
            background: #fef3c7;
            color: #92400e;
            border: 1px solid #fcd34d;
        }

        .info-box {
            background: white;
            padding: 24px;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
            margin-bottom: 24px;
        }

        .info-box h2 {
            font-size: 20px;
            margin-bottom: 16px;
            color: #111827;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 16px;
            margin-bottom: 16px;
        }

        .info-item {
            padding: 12px;
            background: #f9fafb;
            border-radius: 6px;
        }

        .info-item strong {
            display: block;
            font-size: 12px;
            color: #6b7280;
            text-transform: uppercase;
            margin-bottom: 4px;
        }

        .info-item span {
            font-size: 16px;
            color: #111827;
            font-weight: 600;
        }

        .user-card {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 16px;
        }

        .user-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 16px;
            padding-bottom: 16px;
            border-bottom: 1px solid #e5e7eb;
        }

        .user-info h3 {
            font-size: 18px;
            color: #111827;
            margin-bottom: 4px;
        }

        .user-info p {
            font-size: 14px;
            color: #6b7280;
        }

        .badge {
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
        }

        .badge-active {
            background: #d1fae5;
            color: #065f46;
        }

        .badge-expired {
            background: #fee2e2;
            color: #991b1b;
        }

        .badge-none {
            background: #e5e7eb;
            color: #374151;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 16px;
        }

        .form-section {
            background: #f9fafb;
            padding: 16px;
            border-radius: 6px;
        }

        .form-section h4 {
            font-size: 14px;
            color: #374151;
            margin-bottom: 12px;
            text-transform: uppercase;
            font-weight: 700;
        }

        .form-group {
            margin-bottom: 12px;
        }

        .form-group label {
            display: block;
            font-size: 13px;
            color: #6b7280;
            margin-bottom: 4px;
            font-weight: 500;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 8px 12px;
            border: 1.5px solid #d1d5db;
            border-radius: 6px;
            font-size: 14px;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #000;
        }

        .btn {
            padding: 8px 16px;
            border: none;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-primary {
            background: #2563eb;
            color: white;
        }

        .btn-primary:hover {
            background: #1d4ed8;
        }

        .btn-success {
            background: #059669;
            color: white;
        }

        .btn-success:hover {
            background: #047857;
        }

        .btn-danger {
            background: #dc2626;
            color: white;
        }

        .btn-danger:hover {
            background: #b91c1c;
        }

        .btn-warning {
            background: #f59e0b;
            color: white;
        }

        .btn-warning:hover {
            background: #d97706;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="header-content">
            <h1>🧪 Sandbox Testing</h1>
            <a href="dashboard.php">← Back to Dashboard</a>
        </div>
    </div>

    <div class="container">
        <!-- Alerts -->
        <?php if ($success_message): ?>
            <div class="alert alert-success">
                <?php echo htmlspecialchars($success_message); ?>
            </div>
        <?php endif; ?>

        <?php if ($error_message): ?>
            <div class="alert alert-danger">
                <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>

        <!-- Mollie Status -->
        <div class="info-box">
            <h2>Mollie Integration Status</h2>
            <div class="info-grid">
                <div class="info-item">
                    <strong>Status</strong>
                    <span><?php echo $mollie_status; ?></span>
                </div>
                <div class="info-item">
                    <strong>Test Mode</strong>
                    <span>✅ Enabled</span>
                </div>
                <div class="info-item">
                    <strong>Library Status</strong>
                    <span><?php echo class_exists('\Mollie\Api\MollieApiClient') ? '✅ Installed' : '❌ Not installed'; ?></span>
                </div>
            </div>
            
            <?php if (!class_exists('\Mollie\Api\MollieApiClient')): ?>
                <div class="alert alert-warning" style="margin-top: 16px;">
                    <strong>⚠️ Mollie library not installed!</strong><br>
                    Run: <code>composer install</code> in your project root
                </div>
            <?php endif; ?>
            
            <?php if (!$mollie->isConfigured()): ?>
                <!-- Debug Information -->
                <div style="margin-top: 20px; padding: 16px; background: #fff3cd; border: 2px solid #ffc107; border-radius: 8px;">
                    <h3 style="margin: 0 0 12px 0; color: #856404;">🔍 Debug Information</h3>
                    <div style="font-size: 13px; font-family: monospace; color: #333;">
                        <p><strong>.env Path:</strong> <?php echo htmlspecialchars($debug_info['env_path']); ?></p>
                        <p><strong>.env Exists:</strong> <?php echo $debug_info['env_exists'] ? '✅ YES' : '❌ NO'; ?></p>
                        <?php if ($debug_info['env_real_path']): ?>
                            <p><strong>.env Real Path:</strong> <?php echo htmlspecialchars($debug_info['env_real_path']); ?></p>
                        <?php endif; ?>
                        <p><strong>API Key Set:</strong> <?php echo $debug_info['api_key_set'] ? '✅ YES' : '❌ NO'; ?></p>
                        <p><strong>API Key Preview:</strong> <?php echo htmlspecialchars($debug_info['api_key_preview']); ?></p>
                        <p><strong>Profile ID:</strong> <?php echo htmlspecialchars($debug_info['profile_id']); ?></p>
                        <p><strong>Current File:</strong> <?php echo htmlspecialchars($debug_info['current_file']); ?></p>
                        <p><strong>Working Directory:</strong> <?php echo htmlspecialchars($debug_info['working_directory']); ?></p>
                        
                        <?php if (!empty($debug_info['alternative_paths'])): ?>
                            <hr style="margin: 12px 0; border: none; border-top: 1px solid #ccc;">
                            <p><strong>Alternative Paths Checked:</strong></p>
                            <ul style="margin: 8px 0; padding-left: 20px;">
                                <?php foreach ($debug_info['alternative_paths'] as $alt): ?>
                                    <li>
                                        <?php echo $alt['exists'] ? '✅' : '❌'; ?> 
                                        <?php echo htmlspecialchars($alt['path']); ?>
                                        <?php if ($alt['exists']): ?>
                                            <br>&nbsp;&nbsp;&nbsp;<small>→ <?php echo htmlspecialchars($alt['real_path']); ?></small>
                                        <?php endif; ?>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                        
                        <hr style="margin: 12px 0; border: none; border-top: 1px solid #ccc;">
                        <p style="color: #856404;"><strong>💡 Tip:</strong> Check PHP error log at <code>C:\xampp\php\logs\php_error_log</code> for detailed logs.</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <div class="info-box">
            <h2>📘 Sandbox Instructions</h2>
            <ul style="line-height: 2; color: #374151;">
                <li><strong>Change User Creation Date:</strong> Test if trial expiration logic works correctly</li>
                <li><strong>Modify Subscription Dates:</strong> Set custom start/end dates for testing</li>
                <li><strong>Test Payments:</strong> Create real Mollie test payments (sandbox mode)</li>
                <li><strong>Reset User:</strong> Clear subscription and reset to defaults</li>
            </ul>
        </div>

        <!-- User Cards -->
        <h2 style="margin-bottom: 20px; font-size: 20px;">Test Users</h2>
        
        <?php foreach ($users as $user): ?>
            <div class="user-card">
                <div class="user-header">
                    <div class="user-info">
                        <h3><?php echo htmlspecialchars($user['name']); ?></h3>
                        <p><?php echo htmlspecialchars($user['email']); ?></p>
                        <p style="font-size: 12px; margin-top: 4px;">
                            Created: <?php echo date('M d, Y H:i', strtotime($user['created_at'])); ?>
                        </p>
                    </div>
                    <div>
                        <?php if ($user['subscription_status'] === 'Active'): ?>
                            <span class="badge badge-active">
                                Active (<?php echo $user['days_remaining']; ?> days left)
                            </span>
                        <?php elseif ($user['subscription_status'] === 'Expired'): ?>
                            <span class="badge badge-expired">Expired</span>
                        <?php else: ?>
                            <span class="badge badge-none">No Subscription</span>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="form-grid">
                    <!-- Change Creation Date -->
                    <div class="form-section">
                        <h4>📅 Change Creation Date</h4>
                        <form method="POST">
                            <input type="hidden" name="action" value="change_date">
                            <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                            <div class="form-group">
                                <label>New Date</label>
                                <input type="datetime-local" name="new_date" value="<?php echo date('Y-m-d\TH:i', strtotime($user['created_at'])); ?>" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Update Date</button>
                        </form>
                    </div>

                    <!-- Change Subscription -->
                    <div class="form-section">
                        <h4>📆 Modify Subscription</h4>
                        <form method="POST">
                            <input type="hidden" name="action" value="change_subscription">
                            <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                            <div class="form-group">
                                <label>Start Date</label>
                                <input type="date" name="start_date" value="<?php echo $user['start_date'] ?? date('Y-m-d'); ?>" required>
                            </div>
                            <div class="form-group">
                                <label>End Date</label>
                                <input type="date" name="end_date" value="<?php echo $user['end_date'] ?? date('Y-m-d', strtotime('+3 months')); ?>" required>
                            </div>
                            <button type="submit" class="btn btn-success">Update Subscription</button>
                        </form>
                    </div>

                    <!-- Test Payment -->
                    <div class="form-section">
                        <h4>💳 Test Mollie Payment</h4>
                        <form method="POST">
                            <input type="hidden" name="action" value="test_payment">
                            <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                            <div class="form-group">
                                <label>Months to Purchase</label>
                                <select name="months" required>
                                    <option value="1">1 Month (€9.99)</option>
                                    <option value="3">3 Months (€29.97)</option>
                                    <option value="6">6 Months (€59.94)</option>
                                    <option value="12">12 Months (€119.88)</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-warning" <?php echo !$mollie->isConfigured() ? 'disabled' : ''; ?>>
                                Create Test Payment
                            </button>
                        </form>
                    </div>

                    <!-- Reset User -->
                    <div class="form-section">
                        <h4>🔄 Reset to Defaults</h4>
                        <form method="POST" onsubmit="return confirm('Reset this user to defaults?')">
                            <input type="hidden" name="action" value="reset_user">
                            <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                            <p style="font-size: 13px; color: #6b7280; margin-bottom: 12px;">
                                Removes subscription and resets creation date to now
                            </p>
                            <button type="submit" class="btn btn-danger">Reset User</button>
                        </form>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>

        <?php if (empty($users)): ?>
            <div class="alert alert-warning">
                No test users found. Register some users first!
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
