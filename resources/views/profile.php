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

// Check for session error messages (from redirects)
if (isset($_SESSION['profile_error'])) {
    $error_message = $_SESSION['profile_error'];
    unset($_SESSION['profile_error']);
}

// Check for session success messages (from redirects)
if (isset($_SESSION['profile_success'])) {
    $success_message = $_SESSION['profile_success'];
    unset($_SESSION['profile_success']);
}

// Load user data (sets: $user, $company, $user_listings_count, $user_subscriptions, $user_cards)
require_once __DIR__ . '/loaders/user-data-loader.php';

// Handle form submissions
require_once __DIR__ . '/handlers/profile-update-handler.php';
require_once __DIR__ . '/handlers/subscription-handler.php';
require_once __DIR__ . '/handlers/notification-handler.php';

// Handle listing publish/unpublish toggle from listings tab
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
        $stmt->execute(['listing_id' => $listing_id, 'user_id' => $user['id']]);
        
        if ($stmt->fetch()) {
            $stmt = $pdo->prepare('UPDATE listings SET published = :status WHERE id = :id');
            $stmt->execute(['status' => $new_status, 'id' => $listing_id]);
            
            $status_text = $new_status == 1 ? 'published' : 'unpublished';
            $success_message = "Listing {$status_text} successfully!";
        } else {
            $error_message = 'You do not have permission to modify this listing.';
        }
    } catch (PDOException $e) {
        $error_message = 'Failed to update listing: ' . $e->getMessage();
    }
    
    // Redirect to prevent form resubmission
    header('Location: ' . VIEWS_URL . '/profile.php?tab=listings');
    exit;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - Glass Market</title>
    <link rel="stylesheet" href="<?php echo CSS_URL; ?>/styles.css">
    <style>
        /* Inline critical styles */
        :root {
            --profile-primary: #2f6df5;
            --profile-text: #1d1d1f;
            --profile-muted: #6e6e73;
            --profile-bg: #f5f5f7;
            --profile-card-bg: #ffffff;
            --profile-border: #d2d2d7;
        }

        body {
            font-family: "SF Pro Display", -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            background: var(--profile-bg);
            color: var(--profile-text);
            margin: 0;
            line-height: 1.6;
        }

        .profile-container {
            max-width: 1200px;
            margin: 100px auto 60px;
            padding: 0 32px;
        }

        /* Profile Header Styles */
        .profile-header {
            display: flex;
            align-items: center;
            gap: 32px;
            background: var(--profile-card-bg);
            padding: 40px;
            border-radius: 20px;
            margin-bottom: 40px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        }

        .profile-avatar img,
        .avatar-placeholder {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
        }

        .avatar-placeholder {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 48px;
            font-weight: 700;
        }

        .profile-name {
            margin: 0 0 8px;
            font-size: 32px;
            font-weight: 700;
        }

        .profile-email {
            margin: 0 0 8px;
            font-size: 16px;
            color: var(--profile-muted);
        }

        .profile-company {
            margin: 0;
            font-size: 14px;
            color: var(--profile-muted);
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .profile-stats {
            display: flex;
            gap: 32px;
            margin-left: auto;
        }

        .stat-item {
            text-align: center;
        }

        .stat-value {
            font-size: 28px;
            font-weight: 700;
            color: var(--profile-primary);
        }

        .stat-label {
            font-size: 12px;
            color: var(--profile-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Tabs */
        .profile-tabs {
            display: flex;
            gap: 8px;
            border-bottom: 2px solid var(--profile-border);
            margin-bottom: 32px;
            overflow-x: auto;
        }

        .tab-button {
            padding: 14px 24px;
            background: transparent;
            border: none;
            border-bottom: 3px solid transparent;
            font-size: 15px;
            font-weight: 600;
            color: var(--profile-muted);
            cursor: pointer;
            transition: all 0.2s;
            white-space: nowrap;
        }

        .tab-button:hover {
            color: var(--profile-text);
        }

        .tab-button.active {
            color: var(--profile-primary);
            border-bottom-color: var(--profile-primary);
        }

        .tab-panel {
            display: none;
        }

        .tab-panel.active {
            display: block;
        }

        .section-title {
            font-size: 24px;
            font-weight: 700;
            margin: 0 0 24px;
        }

        /* Alerts */
        .alert {
            padding: 16px 20px;
            border-radius: 12px;
            margin-bottom: 24px;
            font-size: 14px;
        }

        .alert-success {
            background: #f0fdf4;
            color: #166534;
            border: 1px solid #bbf7d0;
        }

        .alert-error {
            background: #fef2f2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        @media (max-width: 768px) {
            .profile-header {
                flex-direction: column;
                text-align: center;
            }

            .profile-stats {
                margin-left: 0;
                width: 100%;
                justify-content: space-around;
            }
        }
    </style>
</head>
<body>
    <?php include __DIR__ . '/../../includes/navbar.php'; ?>

    <div class="profile-container">
        <?php if ($success_message): ?>
            <div class="alert alert-success">
                <?php echo htmlspecialchars($success_message); ?>
            </div>
        <?php endif; ?>

        <?php if ($error_message): ?>
            <div class="alert alert-error">
                <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>

        <!-- Profile Header -->
        <?php include __DIR__ . '/components/profile-header.php'; ?>

        <!-- Tabs Navigation -->
        <div class="profile-tabs">
            <button class="tab-button active" data-tab="overview">Overview</button>
            <button class="tab-button" data-tab="listings">My Listings</button>
            <button class="tab-button" data-tab="saved">Saved</button>
            <button class="tab-button" data-tab="company">Company</button>
            <button class="tab-button" data-tab="edit">Edit Profile</button>
            <button class="tab-button" data-tab="subscription">Subscriptions</button>
            <button class="tab-button" data-tab="notifications">Notifications</button>
        </div>

        <!-- Tab Panels -->
        
        <!-- Overview Tab -->
        <?php include __DIR__ . '/tabs/overview-tab.php'; ?>

        <!-- Listings Tab -->
        <?php include __DIR__ . '/tabs/listings-tab.php'; ?>

        <!-- Saved Tab -->
        <?php include __DIR__ . '/tabs/saved-tab.php'; ?>

        <!-- Company Tab -->
        <?php include __DIR__ . '/tabs/company-tab.php'; ?>

        <!-- Edit Profile Tab -->
        <?php include __DIR__ . '/tabs/edit-tab.php'; ?>

        <!-- Subscription Tab -->
        <?php include __DIR__ . '/tabs/subscription-tab.php'; ?>

        <!-- Notifications Tab -->
        <?php include __DIR__ . '/tabs/notifications-tab.php'; ?>
    </div>

    <!-- Tab Switching Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tabButtons = document.querySelectorAll('.tab-button');
            const tabPanels = document.querySelectorAll('.tab-panel');

            // Check for tab parameter in URL
            const urlParams = new URLSearchParams(window.location.search);
            const tabParam = urlParams.get('tab');
            
            // Function to switch tabs
            function switchToTab(targetTab) {
                // Remove active class from all buttons and panels
                tabButtons.forEach(btn => btn.classList.remove('active'));
                tabPanels.forEach(panel => panel.classList.remove('active'));

                // Add active class to target button and panel
                const targetButton = document.querySelector(`[data-tab="${targetTab}"]`);
                const targetPanel = document.getElementById('tab-' + targetTab);
                
                if (targetButton && targetPanel) {
                    targetButton.classList.add('active');
                    targetPanel.classList.add('active');
                    targetPanel.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                }
            }

            // If tab parameter exists in URL, switch to that tab
            if (tabParam) {
                switchToTab(tabParam);
            }

            tabButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    const targetTab = button.getAttribute('data-tab');
                    switchToTab(targetTab);
                });
            });
        });
    </script>
</body>
</html>
