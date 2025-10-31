<?php
session_start();

require_once __DIR__ . '/../../config.php';

// Require authentication
if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
    header('Location: ' . VIEWS_URL . '/login.php');
    exit;
}

// Database credentials
$db_host = '127.0.0.1';
$db_name = 'glass_market';
$db_user = 'root';
$db_pass = '';

$error_message = '';
$success_message = '';

// Get user info
$user_id = $_SESSION['user_id'] ?? null;

// Handle listing deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_listing'])) {
    $listing_id = $_POST['listing_id'] ?? 0;
    
    try {
        $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Verify the listing belongs to the user's company
        $stmt = $pdo->prepare('
            SELECT l.id FROM listings l
            LEFT JOIN companies c ON l.company_id = c.id
            LEFT JOIN users u ON c.id = u.company_id
            WHERE l.id = :listing_id AND u.id = :user_id
        ');
        $stmt->execute(['listing_id' => $listing_id, 'user_id' => $user_id]);
        
        if ($stmt->fetch()) {
            $stmt = $pdo->prepare('DELETE FROM listings WHERE id = :id');
            $stmt->execute(['id' => $listing_id]);
            $_SESSION['listing_success'] = 'Listing deleted successfully!';
        } else {
            $_SESSION['listing_error'] = 'You do not have permission to delete this listing.';
        }
    } catch (PDOException $e) {
        $_SESSION['listing_error'] = 'Failed to delete listing: ' . $e->getMessage();
    }
    
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

// Handle listing publish/unpublish toggle
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['toggle_publish'])) {
    $listing_id = $_POST['listing_id'] ?? 0;
    $new_status = $_POST['new_status'] ?? 0;
    
    try {
        $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Verify the listing belongs to the user's company
        $stmt = $pdo->prepare('
            SELECT l.id FROM listings l
            LEFT JOIN companies c ON l.company_id = c.id
            LEFT JOIN users u ON c.id = u.company_id
            WHERE l.id = :listing_id AND u.id = :user_id
        ');
        $stmt->execute(['listing_id' => $listing_id, 'user_id' => $user_id]);
        
        if ($stmt->fetch()) {
            $stmt = $pdo->prepare('UPDATE listings SET published = :status WHERE id = :id');
            $stmt->execute(['status' => $new_status, 'id' => $listing_id]);
            
            $status_text = $new_status == 1 ? 'published' : 'unpublished';
            $_SESSION['listing_success'] = "Listing {$status_text} successfully!";
        } else {
            $_SESSION['listing_error'] = 'You do not have permission to modify this listing.';
        }
    } catch (PDOException $e) {
        $_SESSION['listing_error'] = 'Failed to update listing: ' . $e->getMessage();
    }
    
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

// Get messages from session and clear them
if (isset($_SESSION['listing_success'])) {
    $success_message = $_SESSION['listing_success'];
    unset($_SESSION['listing_success']);
}
if (isset($_SESSION['listing_error'])) {
    $error_message = $_SESSION['listing_error'];
    unset($_SESSION['listing_error']);
}

// Load user's listings
$my_listings = [];
$total_listings = 0;
$published_count = 0;
$pending_count = 0;

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Get all listings for the user's company
    $stmt = $pdo->prepare('
        SELECT l.*, c.name as company_name
        FROM listings l
        LEFT JOIN companies c ON l.company_id = c.id
        LEFT JOIN users u ON c.id = u.company_id
        WHERE u.id = :user_id
        ORDER BY l.created_at DESC
    ');
    $stmt->execute(['user_id' => $user_id]);
    $my_listings = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Calculate stats
    $total_listings = count($my_listings);
    foreach ($my_listings as $listing) {
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
    <title>My Listings - Glass Market</title>
    <link rel="stylesheet" href="<?php echo CSS_URL; ?>/app.css">
    <style>
        body {
            background: #f5f5f5;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 20px;
        }

        .page-header {
            background: #000;
            color: white;
            padding: 30px 40px;
            border-radius: 12px;
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .page-header h1 {
            font-size: 28px;
            font-weight: 800;
            margin: 0;
        }

        .section {
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
            text-decoration: none;
            display: inline-block;
        }

        .btn-edit {
            background: #2a2623;
            color: white;
        }

        .btn-edit:hover {
            background: #4a4643;
        }

        .btn-delete {
            background: #5a4a42;
            color: white;
        }

        .btn-delete:hover {
            background: #7a5a52;
        }

        .btn-publish {
            background: #6b6460;
            color: white;
        }

        .btn-publish:hover {
            background: #8b8480;
        }

        .btn-unpublish {
            background: #9a8a80;
            color: white;
        }

        .btn-unpublish:hover {
            background: #baaaa0;
        }

        .btn-back {
            background: #6b7280;
            color: white;
        }

        .btn-back:hover {
            background: #4b5563;
        }

        .btn-create {
            background: #000;
            color: white;
        }

        .btn-create:hover {
            background: #333;
        }

        .empty-state {
            text-align: center;
            padding: 60px 40px;
            color: #999;
        }

        .empty-state svg {
            width: 80px;
            height: 80px;
            margin-bottom: 20px;
            color: #ddd;
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
    <?php include __DIR__ . '/../../includes/navbar.php'; ?>
    <?php include __DIR__ . '/../../includes/subscription-notification.php'; ?>

    <main style="padding-top: 80px;">
        <div class="container">
            <div class="page-header">
                <h1>📦 My Listings</h1>
                <div>
                    <a href="<?php echo VIEWS_URL; ?>/profile.php" class="btn btn-back">Back to Profile</a>
                </div>
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
                    <div class="stat-label">Pending</div>
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
            <div class="section">
                <div class="section-title">All Your Listings (<?php echo $total_listings; ?>)</div>
                
                <?php if (count($my_listings) > 0): ?>
                    <?php foreach ($my_listings as $listing): ?>
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
                                        <strong>Company:</strong> <?php echo htmlspecialchars($listing['company_name'] ?? 'N/A'); ?>
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
                                <a href="<?php echo VIEWS_URL; ?>/edit-listing.php?id=<?php echo $listing['id']; ?>" class="btn btn-edit">
                                    ✏️ Edit
                                </a>
                                
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="listing_id" value="<?php echo $listing['id']; ?>">
                                    <input type="hidden" name="new_status" value="<?php echo $listing['published'] == 1 ? 0 : 1; ?>">
                                    <?php if ($listing['published'] == 0): ?>
                                        <button type="submit" name="toggle_publish" class="btn btn-publish" 
                                                onclick="return confirm('Publish this listing?')">
                                            ✓ Publish
                                        </button>
                                    <?php else: ?>
                                        <button type="submit" name="toggle_publish" class="btn btn-unpublish"
                                                onclick="return confirm('Unpublish this listing?')">
                                            ⏸ Unpublish
                                        </button>
                                    <?php endif; ?>
                                    
                                    <button type="submit" name="delete_listing" class="btn btn-delete"
                                            onclick="return confirm('Are you sure you want to delete this listing? This cannot be undone.')">
                                        🗑️ Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="empty-state">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                            <line x1="9" y1="9" x2="15" y2="15"></line>
                            <line x1="15" y1="9" x2="9" y2="15"></line>
                        </svg>
                        <h3 style="margin-bottom: 12px; color: #666;">No listings yet</h3>
                        <p style="margin-bottom: 24px;">Create your first listing to get started!</p>
                        <a href="<?php echo VIEWS_URL; ?>/create-listing.php" class="btn btn-create">Create Your First Listing</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <?php include __DIR__ . '/../../includes/footer.php'; ?>
</body>
</html>
