<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

// Check if user is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

// Check if user is admin
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header('Location: login.php');
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
    
    // Pagination
    $per_page = 20;
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $offset = ($page - 1) * $per_page;
    
    // Get total count
    $total_listings = $pdo->query("SELECT COUNT(*) FROM listings")->fetchColumn();
    $total_pages = ceil($total_listings / $per_page);
    
    // Get listings with company and location info
    $listings = $pdo->prepare("
        SELECT 
            l.*,
            c.name as company_name,
            c.company_type,
            loc.name as location_name,
            loc.city,
            loc.country_code
        FROM listings l 
        LEFT JOIN companies c ON l.company_id = c.id 
        LEFT JOIN locations loc ON l.location_id = loc.id 
        ORDER BY l.created_at DESC 
        LIMIT :limit OFFSET :offset
    ");
    $listings->bindValue(':limit', $per_page, PDO::PARAM_INT);
    $listings->bindValue(':offset', $offset, PDO::PARAM_INT);
    $listings->execute();
    $listings = $listings->fetchAll(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    $error = "Database error: " . $e->getMessage();
}

$admin_name = $_SESSION['admin_user_name'] ?? 'Admin';
$admin_email = $_SESSION['admin_user_email'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recent Listings - Glass Market Admin</title>
    <link rel="stylesheet" href="/glass-market/public/css/admin-dashboard.css">
    <style>
        .listing-card {
            background: white;
            padding: 24px;
            border-radius: 8px;
            margin-bottom: 16px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            border: 1px solid rgba(0, 0, 0, 0.06);
            transition: all 0.2s ease;
        }
        
        .listing-card:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            transform: translateY(-2px);
        }
        
        .listing-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 16px;
            padding-bottom: 16px;
            border-bottom: 1px solid rgba(0, 0, 0, 0.06);
        }
        
        .listing-title {
            font-size: 20px;
            font-weight: 700;
            color: #1f1a17;
            margin-bottom: 4px;
        }
        
        .listing-meta {
            font-size: 14px;
            color: #6b5f56;
        }
        
        .listing-side-badge {
            padding: 6px 16px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .listing-side-badge.wts {
            background: #efe7dc;
            color: #2a2623;
        }
        
        .listing-side-badge.wtb {
            background: #e3f2fd;
            color: #1565c0;
        }
        
        .listing-body {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 16px;
            margin-bottom: 16px;
        }
        
        .listing-detail {
            display: flex;
            flex-direction: column;
        }
        
        .listing-detail-label {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #6b5f56;
            font-weight: 600;
            margin-bottom: 4px;
        }
        
        .listing-detail-value {
            font-size: 15px;
            color: #1f1a17;
            font-weight: 500;
        }
        
        .listing-footer {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }
        
        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
            margin-top: 32px;
            padding: 24px;
        }
        
        .pagination a,
        .pagination span {
            padding: 8px 16px;
            background: white;
            border: 1px solid rgba(0, 0, 0, 0.1);
            border-radius: 6px;
            text-decoration: none;
            color: #2a2623;
            font-weight: 600;
            transition: all 0.2s ease;
        }
        
        .pagination a:hover {
            background: #2a2623;
            color: white;
            transform: translateY(-1px);
        }
        
        .pagination .active {
            background: #2a2623;
            color: white;
        }
        
        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            background: white;
            border: 1px solid rgba(0, 0, 0, 0.1);
            border-radius: 6px;
            text-decoration: none;
            color: #2a2623;
            font-weight: 600;
            transition: all 0.2s ease;
            margin-bottom: 24px;
        }
        
        .back-link:hover {
            background: #2a2623;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        
        .stats-bar {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }
        
        .stats-bar h2 {
            font-size: 20px;
            color: #1f1a17;
        }
        
        .stats-bar .count {
            font-size: 16px;
            color: #6b5f56;
        }
    </style>
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
                <a href="logout.php" class="btn-logout">Logout</a>
            </div>
        </div>
    </div>

    <div class="container">
        <a href="dashboard.php" class="back-link">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" width="20" height="20">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
            </svg>
            Back to Dashboard
        </a>

        <div class="stats-bar">
            <h2>All Listings</h2>
            <div class="count">Total: <?php echo number_format($total_listings); ?> listings • Page <?php echo $page; ?> of <?php echo $total_pages; ?></div>
        </div>

        <?php if (!empty($listings)): ?>
            <?php foreach ($listings as $listing): ?>
                <div class="listing-card">
                    <div class="listing-header">
                        <div>
                            <div class="listing-title"><?php echo htmlspecialchars($listing['glass_type']); ?></div>
                            <div class="listing-meta">
                                <?php echo htmlspecialchars($listing['company_name'] ?? 'Unknown Company'); ?>
                                <?php if ($listing['location_name']): ?>
                                    • <?php echo htmlspecialchars($listing['location_name']); ?>
                                <?php endif; ?>
                                <?php if ($listing['city']): ?>
                                    • <?php echo htmlspecialchars($listing['city']); ?>
                                <?php endif; ?>
                            </div>
                        </div>
                        <span class="listing-side-badge <?php echo strtolower($listing['side']); ?>">
                            <?php echo htmlspecialchars($listing['side']); ?>
                        </span>
                    </div>
                    
                    <div class="listing-body">
                        <div class="listing-detail">
                            <span class="listing-detail-label">Quantity</span>
                            <span class="listing-detail-value">
                                <?php echo $listing['quantity_tons'] ? number_format($listing['quantity_tons'], 2) . ' tons' : 'N/A'; ?>
                            </span>
                        </div>
                        
                        <div class="listing-detail">
                            <span class="listing-detail-label">Price</span>
                            <span class="listing-detail-value">
                                <?php echo htmlspecialchars($listing['price_text'] ?? 'Contact for price'); ?>
                            </span>
                        </div>
                        
                        <div class="listing-detail">
                            <span class="listing-detail-label">Storage Location</span>
                            <span class="listing-detail-value">
                                <?php echo htmlspecialchars($listing['storage_location'] ?? 'N/A'); ?>
                            </span>
                        </div>
                        
                        <div class="listing-detail">
                            <span class="listing-detail-label">Posted</span>
                            <span class="listing-detail-value">
                                <?php echo date('M d, Y', strtotime($listing['created_at'])); ?>
                            </span>
                        </div>
                    </div>
                    
                    <div class="listing-footer">
                        <span class="badge <?php echo $listing['recycled'] === 'recycled' ? 'success' : 'warning'; ?>">
                            <?php echo ucfirst($listing['recycled']); ?>
                        </span>
                        <span class="badge <?php echo $listing['tested'] === 'tested' ? 'success' : 'info'; ?>">
                            <?php echo ucfirst($listing['tested']); ?>
                        </span>
                        <?php if ($listing['published']): ?>
                            <span class="badge success">Published</span>
                        <?php else: ?>
                            <span class="badge warning">Draft</span>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
            
            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
                <div class="pagination">
                    <?php if ($page > 1): ?>
                        <a href="?page=<?php echo $page - 1; ?>">← Previous</a>
                    <?php endif; ?>
                    
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <?php if ($i == $page): ?>
                            <span class="active"><?php echo $i; ?></span>
                        <?php else: ?>
                            <a href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                        <?php endif; ?>
                    <?php endfor; ?>
                    
                    <?php if ($page < $total_pages): ?>
                        <a href="?page=<?php echo $page + 1; ?>">Next →</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <div class="card">
                <div class="empty-state">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                    </svg>
                    <p>No listings found</p>
                </div>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
