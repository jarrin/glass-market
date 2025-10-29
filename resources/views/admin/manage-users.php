<?php
session_start();

// Load config
$config_path = dirname(dirname(dirname(__DIR__))) . '/config.php';
if (file_exists($config_path)) {
    require_once $config_path;
}

// Require admin authentication
if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
    header('Location: ' . VIEWS_URL . '/login.php');
    exit;
}

// Database connection
$db_host = '127.0.0.1';
$db_name = 'glass_market';
$db_user = 'root';
$db_pass = '';

$success_message = '';
$error_message = '';
$users = [];
$total_users = 0;
$total_trial = 0;
$total_paid = 0;
$total_expired = 0;

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Load subscription class
    require_once __DIR__ . '/../../../database/classes/subscriptions.php';
    
    // Handle actions
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Debug logging
        error_log("Manage Users - POST Request received");
        error_log("POST data: " . print_r($_POST, true));
        
        $action = $_POST['action'] ?? '';
        
        // Bulk actions
        if ($action === 'bulk_action' && !empty($_POST['bulk_users'])) {
            $user_ids = $_POST['bulk_users'];
            $bulk_action = $_POST['bulk_action_type'] ?? '';
            $count = 0;
            
            foreach ($user_ids as $user_id) {
                if ($bulk_action === 'apply_trial') {
                    if (!Subscription::hasActiveSubscription($pdo, $user_id)) {
                        if (Subscription::createTrialSubscription($pdo, $user_id)) {
                            $count++;
                        }
                    }
                } elseif ($bulk_action === 'remove_trial') {
                    $stmt = $pdo->prepare("DELETE FROM user_subscriptions WHERE user_id = :id AND is_trial = 1");
                    if ($stmt->execute(['id' => $user_id])) {
                        $count++;
                    }
                } elseif ($bulk_action === 'unverify') {
                    $stmt = $pdo->prepare("UPDATE users SET email_verified_at = NULL WHERE id = :id");
                    if ($stmt->execute(['id' => $user_id])) {
                        $count++;
                    }
                } elseif ($bulk_action === 'verify') {
                    $stmt = $pdo->prepare("UPDATE users SET email_verified_at = NOW() WHERE id = :id");
                    if ($stmt->execute(['id' => $user_id])) {
                        $count++;
                    }
                } elseif ($bulk_action === 'delete') {
                    $stmt = $pdo->prepare("DELETE FROM users WHERE id = :id");
                    if ($stmt->execute(['id' => $user_id])) {
                        $count++;
                    }
                }
            }
            $_SESSION['success_message'] = "Bulk action completed: {$count} user(s) affected.";
            header('Location: ' . $_SERVER['PHP_SELF']);
            exit;
        }
        // Single actions
        else {
            $user_id = $_POST['user_id'] ?? 0;
            
            if ($action === 'apply_trial') {
                if (!Subscription::hasActiveSubscription($pdo, $user_id)) {
                    if (Subscription::createTrialSubscription($pdo, $user_id)) {
                        $_SESSION['success_message'] = 'Trial subscription applied successfully!';
                        header('Location: ' . $_SERVER['PHP_SELF']);
                        exit;
                    } else {
                        $error_message = 'Failed to apply trial subscription.';
                    }
                } else {
                    $error_message = 'User already has an active subscription.';
                }
            } elseif ($action === 'remove_trial') {
                $stmt = $pdo->prepare("DELETE FROM user_subscriptions WHERE user_id = :id AND is_trial = 1");
                if ($stmt->execute(['id' => $user_id])) {
                    $_SESSION['success_message'] = 'Trial subscription removed successfully.';
                    header('Location: ' . $_SERVER['PHP_SELF']);
                    exit;
                } else {
                    $error_message = 'Failed to remove trial subscription.';
                }
            } elseif ($action === 'unverify_user') {
                $stmt = $pdo->prepare("UPDATE users SET email_verified_at = NULL WHERE id = :id");
                if ($stmt->execute(['id' => $user_id])) {
                    $_SESSION['success_message'] = 'User unverified successfully.';
                    header('Location: ' . $_SERVER['PHP_SELF']);
                    exit;
                } else {
                    $error_message = 'Failed to unverify user.';
                }
            } elseif ($action === 'verify_user') {
                $stmt = $pdo->prepare("UPDATE users SET email_verified_at = NOW() WHERE id = :id");
                if ($stmt->execute(['id' => $user_id])) {
                    $_SESSION['success_message'] = 'User verified successfully.';
                    header('Location: ' . $_SERVER['PHP_SELF']);
                    exit;
                } else {
                    $error_message = 'Failed to verify user.';
                }
            } elseif ($action === 'delete_user') {
                $stmt = $pdo->prepare("DELETE FROM users WHERE id = :id");
                if ($stmt->execute(['id' => $user_id])) {
                    $_SESSION['success_message'] = 'User deleted successfully.';
                    header('Location: ' . $_SERVER['PHP_SELF']);
                    exit;
                } else {
                    $error_message = 'Failed to delete user.';
                }
            }
        }
    }
    
    // Check for session messages
    if (isset($_SESSION['success_message'])) {
        $success_message = $_SESSION['success_message'];
        unset($_SESSION['success_message']);
    }
    
    // Get filter and search parameters
    $search = $_GET['search'] ?? '';
    $filter_status = $_GET['status'] ?? '';
    $filter_subscription = $_GET['subscription'] ?? '';
    
    // Build query with filters
    $where = ["u.email != 'admin@glassmarket.com'"];
    $params = [];
    
    // Search filter
    if (!empty($search)) {
        $where[] = "(u.name LIKE :search OR u.email LIKE :search)";
        $params['search'] = "%{$search}%";
    }
    
    // Status filter
    if ($filter_status === 'verified') {
        $where[] = "u.email_verified_at IS NOT NULL";
    } elseif ($filter_status === 'pending') {
        $where[] = "u.email_verified_at IS NULL";
    }
    
    $where_clause = implode(' AND ', $where);
    
    // Get all users with subscription info
    $stmt = $pdo->prepare("
        SELECT 
            u.id,
            u.name,
            u.email,
            u.company_name,
            u.email_verified_at,
            u.created_at,
            us.id as sub_id,
            us.start_date as sub_start,
            us.end_date as sub_end,
            us.is_trial,
            us.is_active as sub_active,
            CASE 
                WHEN us.end_date >= CURDATE() AND us.is_active = 1 THEN 'active'
                WHEN us.end_date < CURDATE() THEN 'expired'
                ELSE 'none'
            END as subscription_status
        FROM users u
        LEFT JOIN user_subscriptions us ON u.id = us.user_id
        WHERE {$where_clause}
        ORDER BY u.created_at DESC
    ");
    $stmt->execute($params);
    $all_users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Apply subscription filter
    $users = [];
    foreach ($all_users as $user) {
        if (!empty($filter_subscription)) {
            if ($filter_subscription === 'trial' && $user['subscription_status'] === 'active' && $user['is_trial']) {
                $users[] = $user;
            } elseif ($filter_subscription === 'paid' && $user['subscription_status'] === 'active' && !$user['is_trial']) {
                $users[] = $user;
            } elseif ($filter_subscription === 'expired' && $user['subscription_status'] === 'expired') {
                $users[] = $user;
            } elseif ($filter_subscription === 'none' && $user['subscription_status'] === 'none') {
                $users[] = $user;
            }
        } else {
            $users[] = $user;
        }
    }
    
    $total_users = count($users);
    
    // Count statistics
    foreach ($users as $user) {
        if ($user['subscription_status'] === 'active') {
            if ($user['is_trial']) {
                $total_trial++;
            } else {
                $total_paid++;
            }
        } elseif ($user['subscription_status'] === 'expired') {
            $total_expired++;
        }
    }
    
} catch (PDOException $e) {
    $error_message = "Database error: " . $e->getMessage();
}

$admin_name = $_SESSION['user_name'] ?? 'Admin';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - Glass Market Admin</title>
    <link rel="stylesheet" href="/glass-market/public/css/admin-dashboard.css">
    <!-- Manage Users CSS Files -->
    <link rel="stylesheet" href="css/manage-users-base.css">
    <link rel="stylesheet" href="css/manage-users-header.css">
    <link rel="stylesheet" href="css/manage-users-stats.css">
    <link rel="stylesheet" href="css/manage-users-search.css">
    <link rel="stylesheet" href="css/manage-users-bulk.css">
    <link rel="stylesheet" href="css/manage-users-table.css">
    <link rel="stylesheet" href="css/manage-users-responsive.css">
</head>
<body>
    <!-- Temporarily removed inline styles - now using external CSS -->
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .user-table thead {
            background: #f8f9fa;
        }

        .user-table th {
            padding: 16px;
            text-align: left;
            font-weight: 700;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #666;
            border-bottom: 2px solid #e0e0e0;
        }

        .user-table td {
            padding: 16px;
            border-bottom: 1px solid #f0f0f0;
        }

        .user-table tbody tr:hover {
            background: #f8f9fa;
        }

        .badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .badge.success {
            background: #d1fae5;
            color: #065f46;
        }

        .badge.warning {
            background: #fef3c7;
            color: #92400e;
        }

        .badge.danger {
            background: #fee2e2;
            color: #991b1b;
        }

        .badge.secondary {
            background: #e5e7eb;
            color: #374151;
        }

        .action-buttons {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .btn {
            padding: 7px 14px;
            border-radius: 5px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            border: 1px solid transparent;
            transition: all 0.2s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            letter-spacing: 0.3px;
        }

        .btn:hover {
            opacity: 0.9;
        }

        .btn-primary {
            background: #2563eb;
            color: white;
            border-color: #2563eb;
        }

        .btn-primary:hover {
            background: #1d4ed8;
        }

        .btn-success {
            background: #059669;
            color: white;
            border-color: #059669;
        }

        .btn-success:hover {
            background: #047857;
        }

        .btn-warning {
            background: #f59e0b;
            color: white;
            border-color: #f59e0b;
        }

        .btn-warning:hover {
            background: #d97706;
        }

        .btn-danger {
            background: #dc2626;
            color: white;
            border-color: #dc2626;
        }

        .btn-danger:hover {
            background: #b91c1c;
        }

        .btn-sm {
            padding: 6px 12px;
            font-size: 11px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 32px;
        }

        .stat-card {
            background: white;
            padding: 24px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .stat-card .label {
            font-size: 12px;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
        }

        .stat-card .value {
            font-size: 32px;
            font-weight: 700;
            color: #000;
        }

        .alert {
            padding: 16px;
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

        .page-header {
            margin-bottom: 32px;
        }

        .page-header h1 {
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .page-header p {
            color: #666;
            font-size: 16px;
        }

        .user-info {
            display: flex;
            flex-direction: column;
        }

        .user-info .name {
            font-weight: 600;
            color: #000;
            margin-bottom: 4px;
        }

        .user-info .email {
            font-size: 13px;
            color: #666;
        }

        .subscription-info {
            font-size: 13px;
        }

        .subscription-info .date {
            color: #666;
        }

        form {
            display: inline;
        }

        .search-filter-bar {
            background: #ffffff;
            padding: 24px;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            margin-bottom: 24px;
            display: flex;
            gap: 16px;
            align-items: center;
            flex-wrap: wrap;
        }

        .search-box {
            flex: 1;
            min-width: 300px;
            position: relative;
        }

        .search-box input {
            width: 100%;
            padding: 12px 48px 12px 16px;
            border: 1.5px solid #d1d5db;
            border-radius: 6px;
            font-size: 14px;
            transition: all 0.2s;
            font-weight: 400;
            background: #f9fafb;
        }

        .search-box input::placeholder {
            color: #9ca3af;
            font-weight: 400;
        }

        .search-box input:focus {
            outline: none;
            border-color: #000;
            background: #fff;
        }

        .search-icon {
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #999;
            font-size: 18px;
            pointer-events: none;
        }

        .filter-group {
            display: flex;
            gap: 12px;
            align-items: center;
        }

        .filter-group select {
            padding: 12px 36px 12px 16px;
            border: 1.5px solid #d1d5db;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 500;
            background: #f9fafb;
            cursor: pointer;
            transition: all 0.2s;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%23666' d='M6 9L1 4h10z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 12px center;
        }

        .filter-group select:hover {
            border-color: #9ca3af;
            background: #fff;
        }

        .filter-group select:focus {
            outline: none;
            border-color: #000;
            background: #fff;
        }

        .bulk-actions-bar {
            background: #f3f4f6;
            padding: 16px 20px;
            border-radius: 6px;
            border: 1px solid #e5e7eb;
            margin-bottom: 20px;
            display: none;
            align-items: center;
            gap: 16px;
            animation: slideDown 0.2s ease;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .bulk-actions-bar.active {
            display: flex;
        }

        .bulk-actions-bar .count {
            font-weight: 600;
            color: #374151;
            font-size: 14px;
            padding: 6px 12px;
            background: #e5e7eb;
            border-radius: 4px;
        }

        .bulk-actions-bar select {
            padding: 10px 36px 10px 16px;
            border: 1.5px solid #d1d5db;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 500;
            appearance: none;
            background: white;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%23666' d='M6 9L1 4h10z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 12px center;
            cursor: pointer;
            transition: border-color 0.2s;
        }

        .bulk-actions-bar select:focus {
            outline: none;
            border-color: #000;
        }

        .bulk-actions-bar button {
            padding: 10px 20px;
            background: #000;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s;
        }

        .bulk-actions-bar button:hover {
            background: #1f2937;
        }

        .bulk-actions-bar button:last-child {
            background: #6b7280;
            color: white;
        }

        .bulk-actions-bar button:last-child:hover {
            background: #4b5563;
        }

        input[type="checkbox"] {
            width: 18px;
            height: 18px;
            cursor: pointer;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 32px;
        }

        .header {
            background: #000;
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
            margin: 0;
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
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="header-content">
            <h1>Manage Users - Glass Market</h1>
            <a href="dashboard.php">← Back to Dashboard</a>
        </div>
    </div>

    <div class="container">
        <div class="page-header">
            <h1>Manage Users</h1>
            <p>View and manage all user accounts and subscriptions</p>
        </div>

        <!-- Statistics -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="label">Total Users</div>
                <div class="value"><?php echo $total_users; ?></div>
            </div>
            <div class="stat-card">
                <div class="label">Active Trials</div>
                <div class="value"><?php echo $total_trial; ?></div>
            </div>
            <div class="stat-card">
                <div class="label">Paid Subscriptions</div>
                <div class="value"><?php echo $total_paid; ?></div>
            </div>
            <div class="stat-card">
                <div class="label">Expired</div>
                <div class="value"><?php echo $total_expired; ?></div>
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
                <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>

        <!-- Search and Filter Bar -->
        <div class="search-filter-bar">
            <div class="search-box">
                <form method="GET" action="">
                    <input type="text" name="search" placeholder="Search by name or email..." value="<?php echo htmlspecialchars($search); ?>" onchange="this.form.submit()">
                    <span class="search-icon">🔍</span>
                    <input type="hidden" name="status" value="<?php echo htmlspecialchars($filter_status); ?>">
                    <input type="hidden" name="subscription" value="<?php echo htmlspecialchars($filter_subscription); ?>">
                </form>
            </div>
            <div class="filter-group">
                <form method="GET" action="" style="display: flex; gap: 12px;">
                    <select name="status" onchange="this.form.submit()">
                        <option value="">All Status</option>
                        <option value="verified" <?php echo $filter_status === 'verified' ? 'selected' : ''; ?>>Verified</option>
                        <option value="pending" <?php echo $filter_status === 'pending' ? 'selected' : ''; ?>>Pending</option>
                    </select>
                    <select name="subscription" onchange="this.form.submit()">
                        <option value="">All Subscriptions</option>
                        <option value="trial" <?php echo $filter_subscription === 'trial' ? 'selected' : ''; ?>>Trial</option>
                        <option value="paid" <?php echo $filter_subscription === 'paid' ? 'selected' : ''; ?>>Paid</option>
                        <option value="expired" <?php echo $filter_subscription === 'expired' ? 'selected' : ''; ?>>Expired</option>
                        <option value="none" <?php echo $filter_subscription === 'none' ? 'selected' : ''; ?>>No Subscription</option>
                    </select>
                    <input type="hidden" name="search" value="<?php echo htmlspecialchars($search); ?>">
                </form>
            </div>
        </div>

        <!-- Bulk Actions Bar -->
        <div class="bulk-actions-bar" id="bulkActionsBar">
            <form method="POST" id="bulkForm" onsubmit="confirmBulkAction(event); return false;" style="display: contents;">
                <input type="hidden" name="action" value="bulk_action">
                <span class="count"><span id="selectedCount">0</span> selected</span>
                <select name="bulk_action_type" id="bulkActionType">
                    <option value="">Choose action...</option>
                    <option value="apply_trial">✓ Apply Trial</option>
                    <option value="remove_trial">✗ Remove Trial</option>
                    <option value="verify">✓ Verify Users</option>
                    <option value="unverify">⊘ Unverify Users</option>
                    <option value="delete">🗑 Delete Users</option>
                </select>
                <button type="submit">Apply</button>
                <button type="button" onclick="clearSelection()" style="background: #6c757d;">Clear</button>
            </form>
        </div>

        <!-- Users Table -->
        <table class="user-table">
            <thead>
                <tr>
                    <th style="width: 40px;"><input type="checkbox" id="selectAll" onclick="toggleSelectAll()"></th>
                    <th>User</th>
                    <th>Company</th>
                    <th>Verification</th>
                    <th>Subscription</th>
                    <th>Access Until</th>
                    <th>Registered</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($users)): ?>
                    <tr>
                        <td colspan="8" style="text-align: center; padding: 40px; color: #666;">
                            No users found
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td>
                                <input type="checkbox" name="bulk_users[]" value="<?php echo $user['id']; ?>" class="user-checkbox" onchange="updateBulkBar()">
                            </td>
                            <td>
                                <div class="user-info">
                                    <span class="name"><?php echo htmlspecialchars($user['name']); ?></span>
                                    <span class="email"><?php echo htmlspecialchars($user['email']); ?></span>
                                </div>
                            </td>
                            <td><?php echo htmlspecialchars($user['company_name'] ?? 'N/A'); ?></td>
                            <td>
                                <?php if ($user['email_verified_at']): ?>
                                    <span class="badge success">Verified</span>
                                <?php else: ?>
                                    <span class="badge warning">Pending</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($user['subscription_status'] === 'active'): ?>
                                    <?php if ($user['is_trial']): ?>
                                        <span class="badge warning">Trial</span>
                                    <?php else: ?>
                                        <span class="badge success">Paid</span>
                                    <?php endif; ?>
                                <?php elseif ($user['subscription_status'] === 'expired'): ?>
                                    <span class="badge danger">Expired</span>
                                <?php else: ?>
                                    <span class="badge secondary">None</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="subscription-info">
                                    <?php if ($user['subscription_status'] === 'active' && $user['sub_end']): ?>
                                        <span class="date"><?php echo date('M d, Y', strtotime($user['sub_end'])); ?></span>
                                    <?php elseif ($user['subscription_status'] === 'expired'): ?>
                                        <span style="color: #dc3545; font-weight: 600;">Expired</span>
                                    <?php else: ?>
                                        <span style="color: #999;">No Access</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td>
                                <span class="date"><?php echo date('M d, Y', strtotime($user['created_at'])); ?></span>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <?php if ($user['email_verified_at']): ?>
                                        <form method="POST" style="display: inline;" onsubmit="console.log('Unverify form submitted for user <?php echo $user['id']; ?>'); return true;">
                                            <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                            <input type="hidden" name="action" value="unverify_user">
                                            <button type="submit" class="btn btn-warning btn-sm" onclick="console.log('Unverify button clicked'); return confirm('Unverify this user? They will need to be verified again.');">
                                                ⊘ Unverify
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                            <input type="hidden" name="action" value="verify_user">
                                            <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('Verify this user?')">
                                                ✓ Verify
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                    <?php if ($user['subscription_status'] === 'active' && $user['is_trial']): ?>
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                            <input type="hidden" name="action" value="remove_trial">
                                            <button type="submit" class="btn btn-warning btn-sm" onclick="return confirm('Remove trial subscription? This will revoke their access.')">
                                                ✗ Remove Trial
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                    <?php if ($user['subscription_status'] !== 'active'): ?>
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                            <input type="hidden" name="action" value="apply_trial">
                                            <button type="submit" class="btn btn-primary btn-sm" onclick="return confirm('Apply 3-month trial to this user?')">
                                                + Trial
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                        <input type="hidden" name="action" value="delete_user">
                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Delete this user? This cannot be undone.')">
                                            🗑 Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <script>
        function updateBulkBar() {
            const checkboxes = document.querySelectorAll('.user-checkbox:checked');
            const count = checkboxes.length;
            const bulkBar = document.getElementById('bulkActionsBar');
            const selectedCount = document.getElementById('selectedCount');
            
            selectedCount.textContent = count;
            
            if (count > 0) {
                bulkBar.classList.add('active');
            } else {
                bulkBar.classList.remove('active');
            }
        }

        function toggleSelectAll() {
            const selectAll = document.getElementById('selectAll');
            const checkboxes = document.querySelectorAll('.user-checkbox');
            
            checkboxes.forEach(checkbox => {
                checkbox.checked = selectAll.checked;
            });
            
            updateBulkBar();
        }

        function clearSelection() {
            const checkboxes = document.querySelectorAll('.user-checkbox');
            const selectAll = document.getElementById('selectAll');
            
            checkboxes.forEach(checkbox => {
                checkbox.checked = false;
            });
            selectAll.checked = false;
            
            updateBulkBar();
        }

        function confirmBulkAction(event) {
            event.preventDefault();
            
            const form = document.getElementById('bulkForm');
            const actionType = document.getElementById('bulkActionType').value;
            const checkboxes = document.querySelectorAll('.user-checkbox:checked');
            const count = checkboxes.length;
            
            if (!actionType) {
                alert('Please select an action');
                return false;
            }
            
            if (count === 0) {
                alert('Please select at least one user');
                return false;
            }
            
            let message = '';
            if (actionType === 'apply_trial') {
                message = `Apply 3-month trial to ${count} user(s)?`;
            } else if (actionType === 'remove_trial') {
                message = `Remove trial subscription from ${count} user(s)? This will revoke their access.`;
            } else if (actionType === 'verify') {
                message = `Verify ${count} user(s)?`;
            } else if (actionType === 'unverify') {
                message = `Unverify ${count} user(s)? They will need to be verified again.`;
            } else if (actionType === 'delete') {
                message = `DELETE ${count} user(s)? This action CANNOT be undone!`;
            }
            
            if (confirm(message)) {
                // Add checked user IDs to the form
                checkboxes.forEach(checkbox => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'bulk_users[]';
                    input.value = checkbox.value;
                    form.appendChild(input);
                });
                
                form.submit();
            }
            
            return false;
        }
    </script>
</body>
</html>
