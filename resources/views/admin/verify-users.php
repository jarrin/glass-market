<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

// Database connection
$db_host = '127.0.0.1';
$db_name = 'glass_market';
$db_user = 'root';
$db_pass = '';

$success_message = '';
$error_message = '';

// Initialize variables
$pending_users = [];
$verified_users = [];
$pending_count = 0;
$total_users = 0;

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Check if email_verified_at column exists
    $columns = $pdo->query("SHOW COLUMNS FROM users LIKE 'email_verified_at'")->fetch();
    
    if (!$columns) {
        $error_message = "Database setup incomplete. Please run the following SQL command in phpMyAdmin:<br><br>
        <code>ALTER TABLE users ADD COLUMN email_verified_at TIMESTAMP NULL DEFAULT NULL AFTER email;</code><br><br>
        Or run the SQL file: <code>database/add_email_verified_column.sql</code>";
    } else {
        // Handle approval/rejection
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user_id = $_POST['user_id'] ?? 0;
            $action = $_POST['action'] ?? '';
            
            if ($action === 'approve') {
                $stmt = $pdo->prepare("UPDATE users SET email_verified_at = NOW() WHERE id = :id");
                $stmt->execute(['id' => $user_id]);
                
                // Ensure user has a trial subscription
                require_once __DIR__ . '/../../../database/classes/subscriptions.php';
                if (!Subscription::hasActiveSubscription($pdo, $user_id)) {
                    Subscription::createTrialSubscription($pdo, $user_id);
                }
                
                $success_message = 'User has been approved successfully and granted 3-month trial!';
            } elseif ($action === 'reject' || $action === 'delete') {
                $stmt = $pdo->prepare("DELETE FROM users WHERE id = :id AND email != 'admin@glassmarket.com'");
                $stmt->execute(['id' => $user_id]);
                $success_message = $action === 'reject' ? 'User registration has been rejected and removed.' : 'User has been deleted successfully.';
            }
        }
        
        // Get pending users (not verified, exclude admin)
        $pending_users = $pdo->query("
            SELECT id, name, email, created_at 
            FROM users 
            WHERE email_verified_at IS NULL 
            AND email != 'admin@glassmarket.com'
            ORDER BY created_at DESC
        ")->fetchAll(PDO::FETCH_ASSOC);
        
        // Get verified users (exclude admin)
        $verified_users = $pdo->query("
            SELECT id, name, email, email_verified_at, created_at 
            FROM users 
            WHERE email_verified_at IS NOT NULL 
            AND email != 'admin@glassmarket.com'
            ORDER BY email_verified_at DESC 
            LIMIT 10
        ")->fetchAll(PDO::FETCH_ASSOC);
        
        $pending_count = count($pending_users);
        $total_users = $pdo->query("SELECT COUNT(*) FROM users WHERE email != 'admin@glassmarket.com'")->fetchColumn();
    }
    
} catch (PDOException $e) {
    $error_message = "Database error: " . $e->getMessage();
}

$admin_name = $_SESSION['admin_user_name'] ?? 'Admin';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Users - Glass Market Admin</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: ui-serif, Georgia, 'Times New Roman', Times, serif;
            background: #f3eee6;
            color: #1f1a17;
        }

        /* Header */
        .header {
            background: #201b15;
            color: white;
            padding: 0;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .header-content {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px 32px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo-section h1 {
            font-size: 24px;
            font-weight: 700;
        }

        .header-actions a {
            padding: 10px 24px;
            background: rgba(255, 255, 255, 0.2);
            color: white;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 8px;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .header-actions a:hover {
            background: rgba(255, 255, 255, 0.3);
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 32px;
        }

        .page-header {
            margin-bottom: 32px;
        }

        .page-header h2 {
            font-size: 36px;
            font-weight: 800;
            margin-bottom: 8px;
        }

        .page-header p {
            color: #6b5f56;
            font-size: 16px;
        }

        .stats-bar {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 32px;
        }

        .stat-box {
            background: white;
            padding: 20px;
            border-radius: 8px;
            border: 1px solid rgba(0, 0, 0, 0.06);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
        }

        .stat-box .label {
            font-size: 12px;
            color: #6b5f56;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
        }

        .stat-box .value {
            font-size: 32px;
            font-weight: 800;
            color: #1f1a17;
        }

        .alert {
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 24px;
            font-size: 14px;
        }

        .alert-success {
            background: #d1fae5;
            color: #065f46;
            border: 1px solid #6ee7b7;
        }

        .alert-danger {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        .alert code {
            background: rgba(0, 0, 0, 0.1);
            padding: 2px 6px;
            border-radius: 4px;
            font-family: 'Courier New', monospace;
            font-size: 13px;
            display: block;
            margin: 8px 0;
            padding: 12px;
        }

        .section {
            background: white;
            padding: 32px;
            border-radius: 8px;
            margin-bottom: 24px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
            border: 1px solid rgba(0, 0, 0, 0.06);
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
            padding-bottom: 16px;
            border-bottom: 1px solid rgba(0, 0, 0, 0.06);
        }

        .section-header h3 {
            font-size: 24px;
            font-weight: 800;
        }

        .badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .badge.warning {
            background: #fed7aa;
            color: #92400e;
        }

        .badge.success {
            background: #d1fae5;
            color: #065f46;
        }

        .user-table {
            width: 100%;
            border-collapse: collapse;
        }

        .user-table thead {
            background: #faf6ef;
        }

        .user-table th {
            padding: 12px 16px;
            text-align: left;
            font-size: 12px;
            font-weight: 700;
            color: #6b5f56;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .user-table td {
            padding: 16px;
            border-top: 1px solid rgba(0, 0, 0, 0.06);
        }

        .user-table tr:hover {
            background: #faf6ef;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #2a2623;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 16px;
        }

        .user-details h4 {
            font-size: 15px;
            font-weight: 700;
            margin-bottom: 2px;
        }

        .user-details p {
            font-size: 13px;
            color: #6b5f56;
        }

        .action-buttons {
            display: flex;
            gap: 8px;
        }

        .btn {
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            border: none;
            transition: all 0.2s ease;
            text-decoration: none;
            display: inline-block;
        }

        .btn-approve {
            background: #d1fae5;
            color: #065f46;
        }

        .btn-approve:hover {
            background: #a7f3d0;
        }

        .btn-reject {
            background: #fee2e2;
            color: #991b1b;
        }

        .btn-reject:hover {
            background: #fecaca;
        }

        .btn-delete {
            background: #fca5a5;
            color: #7f1d1d;
            padding: 8px 16px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 13px;
            font-weight: 600;
            transition: all 0.2s ease;
        }

        .btn-delete:hover {
            background: #f87171;
            color: white;
            transform: translateY(-1px);
        }

        .btn-view {
            background: #faf6ef;
            color: #2a2623;
            border: 1px solid rgba(0, 0, 0, 0.06);
        }

        .btn-view:hover {
            background: #f2ede5;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #6b5f56;
        }

        .empty-state svg {
            width: 64px;
            height: 64px;
            margin: 0 auto 16px;
            opacity: 0.5;
            stroke: currentColor;
        }

        .empty-state h4 {
            font-size: 18px;
            margin-bottom: 8px;
            color: #1f1a17;
        }

        .empty-state p {
            font-size: 14px;
        }

        form {
            display: inline;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="header-content">
            <div class="logo-section">
                <h1>User Verification</h1>
            </div>
            <div class="header-actions">
                <a href="dashboard.php">← Back to Dashboard</a>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container">
        <div class="page-header">
            <h2>Verify Customer Accounts</h2>
            <p>Review and approve new customer registrations for website access</p>
        </div>

        <!-- Stats Bar -->
        <div class="stats-bar">
            <div class="stat-box">
                <div class="label">Pending Approval</div>
                <div class="value"><?php echo $pending_count; ?></div>
            </div>
            <div class="stat-box">
                <div class="label">Total Customers</div>
                <div class="value"><?php echo $total_users; ?></div>
            </div>
            <div class="stat-box">
                <div class="label">Verified Customers</div>
                <div class="value"><?php echo $total_users - $pending_count; ?></div>
            </div>
        </div>

        <!-- Alerts -->
        <?php if ($success_message): ?>
            <div class="alert alert-success">
                <?php echo htmlspecialchars($success_message); ?>
            </div>
        <?php endif; ?>

        <?php if ($error_message): ?>
            <div class="alert alert-danger">
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>

        <!-- Pending Users Section -->
        <div class="section">
            <div class="section-header">
                <h3>Pending Registrations</h3>
                <span class="badge warning"><?php echo $pending_count; ?> Pending</span>
            </div>

            <?php if (!empty($pending_users)): ?>
                <table class="user-table">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Email</th>
                            <th>Registration Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pending_users as $user): ?>
                            <tr>
                                <td>
                                    <div class="user-info">
                                        <div class="user-avatar">
                                            <?php echo strtoupper(substr($user['name'], 0, 1)); ?>
                                        </div>
                                        <div class="user-details">
                                            <h4><?php echo htmlspecialchars($user['name']); ?></h4>
                                            <p>ID: #<?php echo $user['id']; ?></p>
                                        </div>
                                    </div>
                                </td>
                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                <td><?php echo date('M d, Y \a\t H:i', strtotime($user['created_at'])); ?></td>
                                <td>
                                    <div class="action-buttons">
                                        <form method="POST" onsubmit="return confirm('Approve this user?');">
                                            <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                            <input type="hidden" name="action" value="approve">
                                            <button type="submit" class="btn btn-approve">✓ Approve</button>
                                        </form>
                                        <form method="POST" onsubmit="return confirm('Reject and delete this user? This action cannot be undone.');">
                                            <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                            <input type="hidden" name="action" value="reject">
                                            <button type="submit" class="btn btn-reject">✗ Reject</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="empty-state">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <h4>No Pending Users</h4>
                    <p>All user registrations have been reviewed</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Recently Verified Users -->
        <div class="section">
            <div class="section-header">
                <h3>Recently Verified</h3>
                <span class="badge success">Last 10</span>
            </div>

            <?php if (!empty($verified_users)): ?>
                <table class="user-table">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Email</th>
                            <th>Verified On</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($verified_users as $user): ?>
                            <tr>
                                <td>
                                    <div class="user-info">
                                        <div class="user-avatar">
                                            <?php echo strtoupper(substr($user['name'], 0, 1)); ?>
                                        </div>
                                        <div class="user-details">
                                            <h4><?php echo htmlspecialchars($user['name']); ?></h4>
                                            <p>ID: #<?php echo $user['id']; ?></p>
                                        </div>
                                    </div>
                                </td>
                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                <td><?php echo date('M d, Y \a\t H:i', strtotime($user['email_verified_at'])); ?></td>
                                <td><span class="badge success">Verified</span></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="empty-state">
                    <p>No verified users yet</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
