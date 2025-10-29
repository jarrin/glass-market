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

// Check if user is admin
// if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
//     header('Location: ' . PUBLIC_URL . '/index.php');
//     exit;
// }

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
    $active_subscriptions = $pdo->query("SELECT COUNT(*) FROM subscriptions WHERE subscription_expiry_id = 1")->fetchColumn();
    
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
        ORDER BY created_at DESC 
        LIMIT 5
    ")->fetchAll(PDO::FETCH_ASSOC);
    
    // Get company types distribution
    $company_types = $pdo->query("
        SELECT ct.type, COUNT(c.id) as count 
        FROM company_types ct 
        LEFT JOIN companies c ON ct.id = c.company_type_id 
        GROUP BY ct.id, ct.type
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

        .logo-section {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .logo {
            width: 48px;
            height: 48px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .logo svg {
            width: 28px;
            height: 28px;
        }

        .logo-text h1 {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 2px;
        }

        .logo-text p {
            font-size: 13px;
            opacity: 0.9;
        }

        .header-actions {
            display: flex;
            gap: 20px;
            align-items: center;
        }

        .user-profile {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 8px 16px;
            background: rgba(255, 255, 255, 0.15);
            border-radius: 50px;
            backdrop-filter: blur(10px);
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: white;
            color: #2a2623;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 16px;
        }

        .user-info {
            text-align: left;
        }

        .user-info .name {
            font-weight: 600;
            font-size: 14px;
        }

        .user-info .email {
            font-size: 12px;
            opacity: 0.8;
        }

        .btn-logout {
            padding: 10px 24px;
            background: rgba(255, 255, 255, 0.2);
            color: white;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }

        .btn-logout:hover {
            background: rgba(255, 255, 255, 0.3);
            border-color: rgba(255, 255, 255, 0.5);
            transform: translateY(-2px);
        }

        /* Container */
        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 32px;
        }

        /* Welcome Banner */
        .welcome-banner {
            background: #201b15;
            color: white;
            padding: 48px;
            border-radius: 8px;
            margin-bottom: 32px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
            position: relative;
            overflow: hidden;
        }

        .welcome-banner::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -10%;
            width: 500px;
            height: 500px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 50%;
        }

        .welcome-banner h2 {
            font-size: 36px;
            margin-bottom: 8px;
            position: relative;
            z-index: 1;
            font-weight: 800;
        }

        .welcome-banner p {
            font-size: 18px;
            opacity: 0.95;
            position: relative;
            z-index: 1;
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 24px;
            margin-bottom: 32px;
        }

        .stat-card {
            background: white;
            padding: 28px;
            border-radius: 8px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
            transition: all 0.2s ease;
            border: 1px solid rgba(0, 0, 0, 0.06);
        }

        .stat-card:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
        }

        .stat-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 20px;
        }

        .stat-icon {
            width: 56px;
            height: 56px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }

        .stat-icon.purple {
            background: #2a2623;
            box-shadow: 0 4px 8px rgba(42, 38, 35, 0.15);
        }

        .stat-icon.blue {
            background: #3b342f;
            box-shadow: 0 4px 8px rgba(59, 52, 47, 0.15);
        }

        .stat-icon.green {
            background: #6b5f56;
            box-shadow: 0 4px 8px rgba(107, 95, 86, 0.15);
        }

        .stat-icon.orange {
            background: #8c8278;
            box-shadow: 0 4px 8px rgba(140, 130, 120, 0.15);
        }

        .stat-title {
            font-size: 14px;
            color: #6b5f56;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
        }

        .stat-value {
            font-size: 42px;
            font-weight: 800;
            color: #1f1a17;
            line-height: 1;
        }

        .stat-change {
            display: inline-block;
            margin-top: 12px;
            font-size: 13px;
            padding: 4px 12px;
            border-radius: 20px;
            font-weight: 600;
        }

        .stat-change.positive {
            background: #efe7dc;
            color: #2a2623;
        }

        .stat-change.neutral {
            background: #f2ede5;
            color: #6b5f56;
        }

        /* Content Grid */
        .content-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 24px;
            margin-bottom: 32px;
        }

        @media (max-width: 1024px) {
            .content-grid {
                grid-template-columns: 1fr;
            }
        }

        .card {
            background: white;
            padding: 28px;
            border-radius: 8px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
            border: 1px solid rgba(0, 0, 0, 0.06);
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
            padding-bottom: 16px;
            border-bottom: 1px solid rgba(0, 0, 0, 0.06);
        }

        .card-header h3 {
            font-size: 20px;
            font-weight: 800;
            color: #1f1a17;
        }

        .card-header a {
            font-size: 14px;
            color: #2a2623;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.2s;
        }

        .card-header a:hover {
            text-decoration: underline;
        }

        /* Table */
        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table thead {
            background: #faf6ef;
        }

        .table th {
            padding: 12px 16px;
            text-align: left;
            font-size: 12px;
            font-weight: 700;
            color: #6b5f56;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .table td {
            padding: 16px;
            border-top: 1px solid rgba(0, 0, 0, 0.06);
            font-size: 14px;
        }

        .table tr:hover {
            background: #faf6ef;
        }

        .badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .badge.success {
            background: #efe7dc;
            color: #2a2623;
        }

        .badge.warning {
            background: #f2ede5;
            color: #6b5f56;
        }

        .badge.info {
            background: #faf6ef;
            color: #3b342f;
        }

        /* List */
        .list-item {
            padding: 16px;
            border-bottom: 1px solid rgba(0, 0, 0, 0.06);
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: background 0.2s;
        }

        .list-item:hover {
            background: #faf6ef;
        }

        .list-item:last-child {
            border-bottom: none;
        }

        .list-item-info h4 {
            font-size: 15px;
            font-weight: 700;
            color: #1f1a17;
            margin-bottom: 4px;
        }

        .list-item-info p {
            font-size: 13px;
            color: #6b5f56;
        }

        .list-item-value {
            font-size: 18px;
            font-weight: 700;
            color: #2a2623;
        }

        /* Chart Section */
        .chart-container {
            margin-top: 20px;
        }

        .chart-bar-wrapper {
            margin-bottom: 16px;
        }

        .chart-bar-label {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            font-size: 14px;
        }

        .chart-bar-label span:first-child {
            font-weight: 600;
            color: #1f1a17;
        }

        .chart-bar-label span:last-child {
            color: #6b5f56;
        }

        .chart-bar {
            height: 12px;
            background: #f2ede5;
            border-radius: 10px;
            overflow: hidden;
        }

        .chart-bar-fill {
            height: 100%;
            background: #2a2623;
            border-radius: 10px;
            transition: width 1s ease;
        }

        /* Quick Actions */
        .quick-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
        }

        .action-btn {
            padding: 20px;
            background: #faf6ef;
            border: 1px solid rgba(0, 0, 0, 0.06);
            border-radius: 8px;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s ease;
            text-decoration: none;
            color: #1f1a17;
            display: block;
        }

        .action-btn:hover {
            background: #f2ede5;
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
        }

        .action-btn-icon {
            width: 48px;
            height: 48px;
            margin: 0 auto 12px;
            background: #2a2623;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 24px;
        }

        .action-btn span {
            display: block;
            font-weight: 600;
            font-size: 14px;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: #6b5f56;
        }

        .empty-state svg {
            width: 64px;
            height: 64px;
            margin: 0 auto 16px;
            opacity: 0.5;
        }

        .empty-state p {
            font-size: 14px;
        }
    </style>
</head>
<body>
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
                    <a href="#">View All →</a>
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
                <a href="#">Manage Users →</a>
            </div>
            <?php if (!empty($recent_users)): ?>
                <?php foreach ($recent_users as $user): ?>
                    <div class="list-item">
                        <div class="list-item-info">
                            <h4><?php echo htmlspecialchars($user['name']); ?></h4>
                            <p><?php echo htmlspecialchars($user['email']); ?> • Joined <?php echo date('M d, Y', strtotime($user['created_at'])); ?></p>
                        </div>
                        <div class="list-item-value">
                            <span class="badge info">Active</span>
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
                <a href="#" class="action-btn">
                    <div class="action-btn-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" width="24" height="24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                        </svg>
                    </div>
                    <span>Manage Users</span>
                </a>
                <a href="#" class="action-btn">
                    <div class="action-btn-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" width="24" height="24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008z" />
                        </svg>
                    </div>
                    <span>Manage Companies</span>
                </a>
                <a href="#" class="action-btn">
                    <div class="action-btn-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" width="24" height="24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                    <span>Settings</span>
                </a>
                <a href="#" class="action-btn">
                    <div class="action-btn-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" width="24" height="24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />
                        </svg>
                    </div>
                    <span>Analytics</span>
                </a>
                <a href="#" class="action-btn">
                    <div class="action-btn-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" width="24" height="24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                        </svg>
                    </div>
                    <span>Messages</span>
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
            </div>
        </div>
    </div>

</body>
</html>
