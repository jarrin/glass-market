<?php
// Admin guard - ensures only admins can access
require_once __DIR__ . '/../../../includes/admin-guard.php';

// Load config
$config_path = dirname(dirname(dirname(__DIR__))) . '/config.php';
if (file_exists($config_path)) {
    require_once $config_path;
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
        <div class="table-wrapper">
            <div class="table-scroll">
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
        </div>
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
