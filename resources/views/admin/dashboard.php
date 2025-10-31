<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Admin guard - ensures only admins can access
require_once __DIR__ . '/../../../includes/admin-guard.php';

// Load config
$config_path = dirname(dirname(dirname(__DIR__))) . '/config.php';
if (file_exists($config_path)) {
    require_once $config_path;
}

if (false) {
    exit;
}

// Database connection
$db_host = '127.0.0.1';
$db_name = 'glass_market';
$db_user = 'root';
$db_pass = '';

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Get statistics
    $total_listings = $pdo->query("SELECT COUNT(*) FROM listings")->fetchColumn();
    $total_companies = $pdo->query("SELECT COUNT(*) FROM companies")->fetchColumn();
    $total_users = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
    $active_subscriptions = $pdo->query("SELECT COUNT(*) FROM subscriptions WHERE active = 1")->fetchColumn();
    
    // Get recent listings
    $recent_listings = $pdo->query("
        SELECT l.*, c.name as company_name, loc.name as location_name 
        FROM listings l 
        LEFT JOIN companies c ON l.company_id = c.id 
        LEFT JOIN locations loc ON l.location_id = loc.id 
        ORDER BY l.created_at DESC 
        LIMIT 5
    ")->fetchAll(PDO::FETCH_ASSOC);
    
    // Get recent users
    $recent_users = $pdo->query("
        SELECT * FROM users 
        WHERE email != 'admin@glassmarket.com'
        ORDER BY created_at DESC 
        LIMIT 5
    ")->fetchAll(PDO::FETCH_ASSOC);
    
    // Get company types distribution
    $company_types = $pdo->query("
        SELECT ct.type, COUNT(c.id) as count 
        FROM company_types ct 
        LEFT JOIN companies c ON ct.type = c.company_type 
        GROUP BY ct.type
    ")->fetchAll(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    $error = "Database error: " . $e->getMessage();
}

$admin_name = $_SESSION['user_name'] ?? 'Admin';
$admin_email = $_SESSION['user_email'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Glass Market</title>
    <link rel="stylesheet" href="/glass-market/public/css/admin-dashboard.css">
</head>
<body>
    <?php if (isset($error)): ?>
        <div style="background: #ff4444; color: white; padding: 15px; margin: 0; text-align: center; font-weight: bold;">
            ⚠️ <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>
    
    <!-- Header -->
    <div class="header">
        <div class="header-content">
            <div class="logo-section">
                <div class="logo">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="white">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 01.75-.75h3a.75.75 0 01.75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349m-16.5 11.65V9.35m0 0a3.001 3.001 0 003.75-.615A2.993 2.993 0 009.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 002.25 1.016c.896 0 1.7-.393 2.25-1.016a3.001 3.001 0 003.75.614m-16.5 0a3.004 3.004 0 01-.621-4.72L4.318 3.44A1.5 1.5 0 015.378 3h13.243a1.5 1.5 0 011.06.44l1.19 1.189a3 3 0 01-.621 4.72m-13.5 8.65h3.75a.75.75 0 00.75-.75V13.5a.75.75 0 00-.75-.75H6.75a.75.75 0 00-.75.75v3.75c0 .415.336.75.75.75z" />
                    </svg>
                </div>
                <div class="logo-text">
                    <h1>Glass Market</h1>
                    <p>Admin Dashboard</p>
                </div>
            </div>
            <div class="header-actions">
                <div class="user-profile">
                    <div class="user-avatar">
                        <?php echo strtoupper(substr($admin_name, 0, 1)); ?>
                    </div>
                    <div class="user-info">
                        <div class="name"><?php echo htmlspecialchars($admin_name); ?></div>
                        <div class="email"><?php echo htmlspecialchars($admin_email); ?></div>
                    </div>
                </div>
                <a href="<?php echo VIEWS_URL; ?>/logout.php" class="btn-logout">Logout</a>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container">
        <!-- Welcome Banner -->
        <div class="welcome-banner">
            <h2>Welcome back, <?php echo htmlspecialchars(explode(' ', $admin_name)[0]); ?>!</h2>
            <p>Here's what's happening with your glass market today</p>
        </div>

        <!-- Stats Grid -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-header">
                    <div>
                        <div class="stat-title">Total Listings</div>
                        <div class="stat-value"><?php echo number_format($total_listings ?? 0); ?></div>
                        <span class="stat-change positive">Active</span>
                    </div>
                    <div class="stat-icon purple">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="white" width="28" height="28">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-header">
                    <div>
                        <div class="stat-title">Total Companies</div>
                        <div class="stat-value"><?php echo number_format($total_companies ?? 0); ?></div>
                        <span class="stat-change neutral">Registered</span>
                    </div>
                    <div class="stat-icon blue">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="white" width="28" height="28">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008z" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-header">
                    <div>
                        <div class="stat-title">Total Users</div>
                        <div class="stat-value"><?php echo number_format($total_users ?? 0); ?></div>
                        <span class="stat-change positive">Members</span>
                    </div>
                    <div class="stat-icon green">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="white" width="28" height="28">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-header">
                    <div>
                        <div class="stat-title">Active Subscriptions</div>
                        <div class="stat-value"><?php echo number_format($active_subscriptions ?? 0); ?></div>
                        <span class="stat-change positive">Active</span>
                    </div>
                    <div class="stat-icon orange">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="white" width="28" height="28">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 01-1.043 3.296 3.745 3.745 0 01-3.296 1.043A3.745 3.745 0 0112 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 01-3.296-1.043 3.745 3.745 0 01-1.043-3.296A3.745 3.745 0 013 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 011.043-3.296 3.746 3.746 0 013.296-1.043A3.746 3.746 0 0112 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 013.296 1.043 3.746 3.746 0 011.043 3.296A3.745 3.745 0 0121 12z" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content Grid -->
        <div class="content-grid">
            <!-- Recent Listings -->
            <div class="card">
                <div class="card-header">
                    <h3>Recent Listings</h3>
                    <a href="recent-listings.php">View All →</a>
                </div>
                <?php if (!empty($recent_listings)): ?>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Company</th>
                                <th>Location</th>
                                <th>Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recent_listings as $listing): ?>
                                <tr>
                                    <td><strong>#<?php echo $listing['id']; ?></strong></td>
                                    <td><?php echo htmlspecialchars($listing['company_name'] ?? 'N/A'); ?></td>
                                    <td><?php echo htmlspecialchars($listing['location_name'] ?? 'N/A'); ?></td>
                                    <td><?php echo date('M d, Y', strtotime($listing['created_at'])); ?></td>
                                    <td><span class="badge success">Active</span></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="empty-state">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                        </svg>
                        <p>No listings yet</p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Company Types Distribution -->
            <div class="card">
                <div class="card-header">
                    <h3>Company Types</h3>
                </div>
                <?php if (!empty($company_types)): ?>
                    <div class="chart-container">
                        <?php 
                        $max_count = max(array_column($company_types, 'count'));
                        foreach ($company_types as $type): 
                            $percentage = $max_count > 0 ? ($type['count'] / $max_count) * 100 : 0;
                        ?>
                            <div class="chart-bar-wrapper">
                                <div class="chart-bar-label">
                                    <span><?php echo htmlspecialchars($type['type']); ?></span>
                                    <span><?php echo $type['count']; ?></span>
                                </div>
                                <div class="chart-bar">
                                    <div class="chart-bar-fill" style="width: <?php echo $percentage; ?>%"></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <p>No company data available</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Recent Users -->
        <div class="card">
            <div class="card-header">
                <h3>Recent Users</h3>
                <a href="verify-users.php">Verify Users →</a>
            </div>
            <?php if (!empty($recent_users)): ?>
                <?php foreach ($recent_users as $user): ?>
                    <div class="list-item">
                        <div class="list-item-info">
                            <h4><?php echo htmlspecialchars($user['name']); ?></h4>
                            <p><?php echo htmlspecialchars($user['email']); ?> • Joined <?php echo date('M d, Y', strtotime($user['created_at'])); ?></p>
                        </div>
                        <div class="list-item-value">
                            <?php if ($user['email_verified_at']): ?>
                                <span class="badge success">Verified</span>
                            <?php else: ?>
                                <span class="badge warning">Pending</span>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="empty-state">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <p>No users yet</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Quick Actions -->
        <div class="card" style="margin-top: 24px;">
            <div class="card-header">
                <h3>Quick Actions</h3>
            </div>
            <div class="quick-actions">
                <a href="listings.php" class="action-btn">
                    <div class="action-btn-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" width="24" height="24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z" />
                        </svg>
                    </div>
                    <span>Manage Listings</span>
                </a>
                <a href="manage-users.php" class="action-btn">
                    <div class="action-btn-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" width="24" height="24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                        </svg>
                    </div>
                    <span>Manage Users</span>
                </a>
                <a href="register.php" class="action-btn">
                    <div class="action-btn-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" width="24" height="24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zM4 19.235v-.11a6.375 6.375 0 0112.75 0v.109A12.318 12.318 0 0110.374 21c-2.331 0-4.512-.645-6.374-1.766z" />
                        </svg>
                    </div>
                    <span>Register Customer</span>
                </a>
                <a href="verify-users.php" class="action-btn">
                    <div class="action-btn-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" width="24" height="24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 01-1.043 3.296 3.745 3.745 0 01-3.296 1.043A3.745 3.745 0 0112 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 01-3.296-1.043 3.745 3.745 0 01-1.043-3.296A3.745 3.745 0 013 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 011.043-3.296 3.746 3.746 0 013.296-1.043A3.746 3.746 0 0112 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 013.296 1.043 3.746 3.746 0 011.043 3.296A3.745 3.745 0 0121 12z" />
                        </svg>
                    </div>
                    <span>Verify Customers</span>
                </a>
                <a href="payment-errors.php" class="action-btn" style="border: 2px solid #dc2626;">
                    <div class="action-btn-icon" style="background: #dc2626;">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" width="24" height="24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                        </svg>
                    </div>
                    <span style="color: #dc2626; font-weight: 700;">Payment Errors</span>
                </a>
            </div>
        </div>
    </div>

</body>
</html>
