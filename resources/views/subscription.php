<?php
session_start();

require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../database/classes/subscriptions.php';

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

$user_id = $_SESSION['user_id'] ?? null;

// Handle trial subscription activation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['activate_trial'])) {
    try {
        $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Check if user already has a subscription
        $stmt = $pdo->prepare('SELECT COUNT(*) as count FROM user_subscriptions WHERE user_id = :user_id');
        $stmt->execute(['user_id' => $user_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result['count'] > 0) {
            $error_message = 'You already have a subscription!';
        } else {
            if (Subscription::createTrialSubscription($pdo, $user_id)) {
                $_SESSION['subscription_success'] = 'Free trial activated! You have 3 months of premium access.';
                header('Location: ' . VIEWS_URL . '/profile.php');
                exit;
            } else {
                $error_message = 'Failed to activate trial. Please try again.';
            }
        }
    } catch (PDOException $e) {
        $error_message = 'Database error: ' . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subscription - Glass Market</title>
    <link rel="stylesheet" href="<?php echo CSS_URL; ?>/styles.css">
    <style>
        :root {
            --primary-color: #2f6df5;
            --text-color: #1d1d1f;
            --muted-color: #6e6e73;
            --bg-color: #f5f5f7;
            --card-bg: #ffffff;
            --border-color: #d2d2d7;
        }

        body {
            font-family: "SF Pro Display", "SF Pro Text", -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            background: var(--bg-color);
            color: var(--text-color);
            margin: 0;
            line-height: 1.6;
        }

        .subscription-container {
            max-width: 1000px;
            margin: 100px auto 60px;
            padding: 0 32px;
        }

        .subscription-header {
            text-align: center;
            margin-bottom: 60px;
        }

        .subscription-header h1 {
            font-size: 48px;
            font-weight: 700;
            margin: 0 0 16px;
            color: var(--text-color);
        }

        .subscription-header p {
            font-size: 20px;
            color: var(--muted-color);
            margin: 0;
        }

        .pricing-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 32px;
            margin-bottom: 60px;
        }

        .pricing-card {
            background: var(--card-bg);
            border: 2px solid var(--border-color);
            border-radius: 20px;
            padding: 40px;
            text-align: center;
            transition: all 0.3s ease;
        }

        .pricing-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            border-color: var(--primary-color);
        }

        .pricing-card.featured {
            border-color: var(--primary-color);
            border-width: 3px;
            position: relative;
        }

        .pricing-card.featured::before {
            content: "BEST VALUE";
            position: absolute;
            top: -12px;
            left: 50%;
            transform: translateX(-50%);
            background: var(--primary-color);
            color: white;
            padding: 4px 16px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.5px;
        }

        .plan-name {
            font-size: 24px;
            font-weight: 600;
            margin: 0 0 16px;
            color: var(--text-color);
        }

        .plan-price {
            font-size: 56px;
            font-weight: 700;
            margin: 0 0 8px;
            color: var(--primary-color);
        }

        .plan-price small {
            font-size: 18px;
            font-weight: 500;
            color: var(--muted-color);
        }

        .plan-description {
            font-size: 15px;
            color: var(--muted-color);
            margin: 0 0 32px;
        }

        .plan-features {
            list-style: none;
            padding: 0;
            margin: 0 0 32px;
            text-align: left;
        }

        .plan-features li {
            padding: 12px 0;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 15px;
        }

        .plan-features li:last-child {
            border-bottom: none;
        }

        .plan-features svg {
            flex-shrink: 0;
            color: var(--primary-color);
        }

        .btn {
            display: inline-block;
            padding: 14px 32px;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s ease;
            border: none;
            cursor: pointer;
        }

        .btn-primary {
            background: var(--primary-color);
            color: white;
        }

        .btn-primary:hover {
            background: #1e4dd8;
            transform: scale(1.05);
        }

        .btn-secondary {
            background: transparent;
            color: var(--primary-color);
            border: 2px solid var(--primary-color);
        }

        .btn-secondary:hover {
            background: var(--primary-color);
            color: white;
        }

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
            .subscription-header h1 {
                font-size: 36px;
            }

            .pricing-cards {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <?php include __DIR__ . '/../../includes/navbar.php'; ?>

    <div class="subscription-container">
        <div class="subscription-header">
            <h1>Choose Your Plan</h1>
            <p>Get premium access to all features of Glass Market</p>
        </div>

        <?php if ($error_message): ?>
            <div class="alert alert-error">
                <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>

        <?php if ($success_message): ?>
            <div class="alert alert-success">
                <?php echo htmlspecialchars($success_message); ?>
            </div>
        <?php endif; ?>

        <div class="pricing-cards">
            <!-- Free Trial -->
            <div class="pricing-card featured">
                <h2 class="plan-name">Free Trial</h2>
                <div class="plan-price">
                    €0
                    <small>/3 months</small>
                </div>
                <p class="plan-description">Try all premium features free for 3 months</p>
                
                <ul class="plan-features">
                    <li>
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                        Unlimited listings
                    </li>
                    <li>
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                        Priority support
                    </li>
                    <li>
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                        Advanced analytics
                    </li>
                    <li>
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                        No credit card required
                    </li>
                </ul>

                <form method="POST" action="">
                    <button type="submit" name="activate_trial" class="btn btn-primary">
                        Start Free Trial
                    </button>
                </form>
            </div>

            <!-- Premium Plan -->
            <div class="pricing-card">
                <h2 class="plan-name">Premium</h2>
                <div class="plan-price">
                    €9.99
                    <small>/month</small>
                </div>
                <p class="plan-description">Full access to all Glass Market features</p>
                
                <ul class="plan-features">
                    <li>
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                        Unlimited listings
                    </li>
                    <li>
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                        Priority support
                    </li>
                    <li>
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                        Advanced analytics
                    </li>
                    <li>
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                        Monthly billing
                    </li>
                </ul>

                <a href="#" class="btn btn-secondary">
                    Coming Soon
                </a>
                <p style="margin-top: 12px; font-size: 12px; color: var(--muted-color);">
                    Mollie payment integration in progress
                </p>
            </div>
        </div>

        <div style="text-align: center; color: var(--muted-color); font-size: 14px;">
            <p>✓ Cancel anytime • ✓ No hidden fees • ✓ Secure payment via Mollie</p>
        </div>
    </div>
</body>
</html>
