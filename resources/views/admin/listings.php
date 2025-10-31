<?php
session_start();

// Load config
$config_path = dirname(dirname(dirname(__DIR__))) . '/config.php';
if (file_exists($config_path)) {
    require_once $config_path;
} else {
    die('Config file not found at: ' . $config_path);
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

// Database credentials
$db_host = '127.0.0.1';
$db_name = 'glass_market';
$db_user = 'root';
$db_pass = '';

$error_message = '';
$success_message = '';

// Handle listing approval
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['approve_listing'])) {
    $listing_id = $_POST['listing_id'] ?? 0;
    
    try {
        $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $stmt = $pdo->prepare('UPDATE listings SET published = 1 WHERE id = :id');
        $stmt->execute(['id' => $listing_id]);
        
        $_SESSION['admin_success'] = 'Listing approved and published successfully!';
    } catch (PDOException $e) {
        $_SESSION['admin_error'] = 'Failed to approve listing: ' . $e->getMessage();
    }
    
    // Redirect to prevent form resubmission
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

// Handle listing rejection/deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && (isset($_POST['reject_listing']) || isset($_POST['delete_listing']))) {
    $listing_id = $_POST['listing_id'] ?? 0;
    
    try {
        $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $stmt = $pdo->prepare('DELETE FROM listings WHERE id = :id');
        $stmt->execute(['id' => $listing_id]);
        
        $_SESSION['admin_success'] = 'Listing deleted successfully!';
    } catch (PDOException $e) {
        $_SESSION['admin_error'] = 'Failed to delete listing: ' . $e->getMessage();
    }
    
    // Redirect to prevent form resubmission
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

// Get messages from session and clear them
if (isset($_SESSION['admin_success'])) {
    $success_message = $_SESSION['admin_success'];
    unset($_SESSION['admin_success']);
}
if (isset($_SESSION['admin_error'])) {
    $error_message = $_SESSION['admin_error'];
    unset($_SESSION['admin_error']);
}

// Load all listings
$all_listings = [];
$total_listings = 0;
$published_count = 0;
$pending_count = 0;

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Get all listings with company info
    $stmt = $pdo->prepare('
        SELECT l.*, c.name as company_name, u.name as user_name, u.email as user_email
        FROM listings l
        LEFT JOIN companies c ON l.company_id = c.id
        LEFT JOIN users u ON c.id = u.company_id
        ORDER BY l.created_at DESC
    ');
    $stmt->execute();
    $all_listings = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Calculate stats
    $total_listings = count($all_listings);
    foreach ($all_listings as $listing) {
        if ($listing['published'] == 1) {
            $published_count++;
        } else {
            $pending_count++;
        }
    }
    
} catch (PDOException $e) {
    $error_message = 'Failed to load listings: ' . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Manage Listings - Glass Market</title>
    <link rel="stylesheet" href="<?php echo CSS_URL; ?>/app.css">
    <style>
        body {
            background: #f5f5f5;
        }

        .admin-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 20px;
        }

        .admin-header {
            background: #000;
            color: white;
            padding: 30px 40px;
            border-radius: 12px;
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .admin-header h1 {
            font-size: 28px;
            font-weight: 800;
            margin: 0;
        }

        .admin-section {
            background: white;
            border-radius: 12px;
            padding: 32px;
            margin-bottom: 30px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }

        .section-title {
            font-size: 20px;
            font-weight: 800;
            color: #000;
            margin-bottom: 20px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .alert {
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .alert-danger {
            background: #fef2f2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        .alert-success {
            background: #f0fdf4;
            color: #166534;
            border: 1px solid #bbf7d0;
        }

        .listing-card {
            background: #fafafa;
            border: 1.5px solid #e0e0e0;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 16px;
        }

        .listing-header {
            display: flex;
            gap: 16px;
            align-items: start;
            margin-bottom: 12px;
        }

        .listing-thumbnail {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border-radius: 6px;
            border: 1px solid #e0e0e0;
            flex-shrink: 0;
        }

        .listing-content {
            flex: 1;
            min-width: 0;
        }

        .listing-title {
            font-size: 16px;
            font-weight: 700;
            color: #000;
            margin-bottom: 8px;
        }

        .listing-meta {
            font-size: 13px;
            color: #666;
            margin-bottom: 4px;
        }

        .listing-description {
            font-size: 13px;
            color: #444;
            margin: 12px 0;
            padding: 12px;
            background: white;
            border-radius: 6px;
        }

        .badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .badge-pending {
            background: #fef3c7;
            color: #92400e;
        }

        .badge-published {
            background: #d1fae5;
            color: #065f46;
        }

        .btn {
            padding: 10px 20px;
            font-size: 13px;
            font-weight: 600;
            border-radius: 4px;
            cursor: pointer;
            border: none;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: all 0.2s ease;
            margin-right: 8px;
        }

        .btn-approve {
            background: #10b981;
            color: white;
        }

        .btn-approve:hover {
            background: #059669;
        }

        .btn-reject {
            background: #ef4444;
            color: white;
        }

        .btn-reject:hover {
            background: #dc2626;
        }

        .btn-back {
            background: #6b7280;
            color: white;
            text-decoration: none;
            display: inline-block;
        }

        .btn-back:hover {
            background: #4b5563;
        }

        .empty-state {
            text-align: center;
            padding: 40px;
            color: #999;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            padding: 24px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            text-align: center;
        }

        .stat-number {
            font-size: 36px;
            font-weight: 800;
            color: #000;
            margin-bottom: 8px;
        }

        .stat-label {
            font-size: 12px;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <main style="padding-top: 40px;">
        <div class="admin-container">
            <div class="admin-header">
                <h1>🛡️ Admin - Manage Listings</h1>
                <a href="<?php echo VIEWS_URL; ?>/admin/dashboard.php" class="btn btn-back">Back to dashboard</a>
            </div>

            <!-- Stats -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-number"><?php echo $total_listings; ?></div>
                    <div class="stat-label">Total Listings</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?php echo $published_count; ?></div>
                    <div class="stat-label">Published</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?php echo $pending_count; ?></div>
                    <div class="stat-label">Pending Approval</div>
                </div>
            </div>

            <?php if ($error_message): ?>
                <div class="alert alert-danger">
                    <?php echo htmlspecialchars($error_message); ?>
                </div>
            <?php endif; ?>

            <?php if ($success_message): ?>
                <div class="alert alert-success">
                    <?php echo htmlspecialchars($success_message); ?>
                </div>
            <?php endif; ?>

            <!-- All Listings -->
            <div class="admin-section">
                <div class="section-title">📋 All Listings (<?php echo $total_listings; ?>)</div>
                
                <?php if (count($all_listings) > 0): ?>
                    <?php foreach ($all_listings as $listing): ?>
                        <?php
                            // Determine image URL
                            $imageUrl = "https://picsum.photos/seed/glass{$listing['id']}/400/400";
                            if (!empty($listing['image_path'])) {
                                $imageUrl = PUBLIC_URL . '/' . $listing['image_path'];
                            }
                        ?>
                        <div class="listing-card">
                            <div class="listing-header">
                                <img src="<?php echo htmlspecialchars($imageUrl, ENT_QUOTES, 'UTF-8'); ?>" 
                                     alt="<?php echo htmlspecialchars($listing['quantity_note'] ?? 'Product', ENT_QUOTES, 'UTF-8'); ?>" 
                                     class="listing-thumbnail">
                                <div class="listing-content">
                                    <div class="listing-title">
                                        <?php echo htmlspecialchars($listing['quantity_note'] ?? 'Untitled Listing'); ?>
                                        <?php if ($listing['published'] == 1): ?>
                                            <span class="badge badge-published">Published</span>
                                        <?php else: ?>
                                            <span class="badge badge-pending">Pending</span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="listing-meta">
                                        <strong>Type:</strong> <?php echo htmlspecialchars($listing['glass_type']); ?> | 
                                        <strong>Quantity:</strong> <?php echo htmlspecialchars($listing['quantity_tons']); ?> tons | 
                                        <strong>Side:</strong> <?php echo htmlspecialchars($listing['side']); ?>
                                    </div>
                                    <div class="listing-meta">
                                        <strong>Company:</strong> <?php echo htmlspecialchars($listing['company_name'] ?? 'N/A'); ?> | 
                                        <strong>User:</strong> <?php echo htmlspecialchars($listing['user_name'] ?? 'N/A'); ?> (<?php echo htmlspecialchars($listing['user_email'] ?? 'N/A'); ?>)
                                    </div>
                                    <div class="listing-meta">
                                        <strong>Created:</strong> <?php echo date('F j, Y \a\t g:i A', strtotime($listing['created_at'])); ?>
                                    </div>
                                    
                                    <?php if (!empty($listing['quality_notes'])): ?>
                                        <div class="listing-description">
                                            <strong>Quality Notes:</strong><br>
                                            <?php echo nl2br(htmlspecialchars($listing['quality_notes'])); ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <div style="margin-top: 16px; padding-top: 16px; border-top: 1px solid #e0e0e0;">
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="listing_id" value="<?php echo $listing['id']; ?>">
                                    <?php if ($listing['published'] == 0): ?>
                                        <button type="submit" name="approve_listing" class="btn btn-approve" 
                                                onclick="return confirm('Approve this listing and make it live?')">
                                            ✓ Approve & Publish
                                        </button>
                                    <?php endif; ?>
                                    <button type="submit" name="delete_listing" class="btn btn-reject"
                                            onclick="return confirm('Are you sure you want to delete this listing? This cannot be undone.')">
                                        🗑️ Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="empty-state">
                        <p>No listings found.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>
</body>
</html>
