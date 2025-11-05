<?php
session_start();

// Session flow management - 10 minute timeout
$session_timeout = 600; // 10 minutes in seconds

// Check if user has a session
if (isset($_SESSION['pricing_flow_started'])) {
    // Check if session has expired (10 minutes of inactivity)
    if (time() - $_SESSION['pricing_flow_started'] > $session_timeout) {
        // Session expired - start fresh
        unset($_SESSION['pricing_flow_started']);
        unset($_SESSION['selected_plan']);
        $_SESSION['flow_restarted'] = true;
    } else {
        // Update last activity time
        $_SESSION['pricing_flow_started'] = time();
    }
} else {
    // Start new session flow
    $_SESSION['pricing_flow_started'] = time();
}

require_once __DIR__ . '/../../config.php';

// Get user info if logged in
$user_name = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : null;
$is_logged_in = isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true;
$user_id = $_SESSION['user_id'] ?? null;

// Check current subscription status
$current_subscription = null;
$has_active_subscription = false;
$is_trial = false;

if ($is_logged_in && $user_id) {
    try {
        $db_host = '127.0.0.1';
        $db_name = 'glass_market';
        $db_user = 'root';
        $db_pass = '';
        
        $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Check for active subscription
        $stmt = $pdo->prepare("
            SELECT * FROM user_subscriptions 
            WHERE user_id = :user_id 
            AND is_active = 1 
            AND end_date > NOW()
            LIMIT 1
        ");
        $stmt->execute(['user_id' => $user_id]);
        $current_subscription = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($current_subscription) {
            $has_active_subscription = true;
            $is_trial = (bool)$current_subscription['is_trial'];
        }
    } catch (PDOException $e) {
        error_log("Error checking subscription: " . $e->getMessage());
    }
}

// Handle plan selection
if (isset($_GET['plan'])) {
    $_SESSION['selected_plan'] = $_GET['plan'];
    $_SESSION['pricing_flow_started'] = time(); // Update activity
}

$selected_plan = $_SESSION['selected_plan'] ?? null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pricing - Glass Market</title>
    <link rel="stylesheet" href="<?php echo CSS_URL; ?>/app.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #f9fafb;
            color: #111827;
        }

        .pricing-header {
            background: white;
            border-bottom: 1px solid #e5e7eb;
            padding: 80px 0 60px;
            text-align: center;
        }

        .pricing-header h1 {
            font-size: 48px;
            font-weight: 700;
            margin-bottom: 16px;
            color: #111827;
            letter-spacing: -0.02em;
        }

        .pricing-header p {
            font-size: 20px;
            color: #6b7280;
            max-width: 600px;
            margin: 0 auto;
            line-height: 1.6;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 24px;
        }

        .pricing-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 32px;
            margin: 60px auto;
            max-width: 1100px;
        }

        .pricing-card {
            background: white;
            border: 2px solid #e5e7eb;
            border-radius: 16px;
            padding: 40px 32px;
            position: relative;
            transition: all 0.3s;
        }

        .pricing-card:hover {
            border-color: #111827;
            transform: translateY(-4px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }

        .pricing-card.featured {
            border-color: #111827;
            border-width: 3px;
            transform: scale(1.05);
        }

        .pricing-card.featured::before {
            content: 'MOST POPULAR';
            position: absolute;
            top: -12px;
            left: 50%;
            transform: translateX(-50%);
            background: #111827;
            color: white;
            padding: 6px 20px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.5px;
        }

        .plan-name {
            font-size: 14px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #6b7280;
            margin-bottom: 12px;
        }

        .plan-price {
            font-size: 48px;
            font-weight: 700;
            color: #111827;
            margin-bottom: 8px;
            display: flex;
            align-items: baseline;
            gap: 8px;
        }

        .plan-price span {
            font-size: 20px;
            color: #6b7280;
            font-weight: 500;
        }

        .plan-description {
            font-size: 15px;
            color: #6b7280;
            line-height: 1.6;
            margin-bottom: 32px;
            min-height: 48px;
        }

        .plan-features {
            list-style: none;
            margin-bottom: 32px;
        }

        .plan-features li {
            padding: 12px 0;
            color: #374151;
            font-size: 15px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .plan-features li::before {
            content: '✓';
            color: #059669;
            font-weight: 700;
            font-size: 18px;
        }

        .plan-button {
            width: 100%;
            padding: 16px 32px;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            text-align: center;
            transition: all 0.2s;
        }

        .plan-button-primary {
            background: #111827;
            color: white;
        }

        .plan-button-primary:hover {
            background: #000000;
            transform: translateY(-2px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15);
        }

        .plan-button-secondary {
            background: transparent;
            color: #111827;
            border: 2px solid #d1d5db;
        }

        .plan-button-secondary:hover {
            background: #f9fafb;
            border-color: #111827;
        }

        .faq-section {
            background: white;
            padding: 80px 0;
            margin-top: 60px;
        }

        .faq-section h2 {
            font-size: 36px;
            font-weight: 700;
            text-align: center;
            margin-bottom: 48px;
            color: #111827;
        }

        .faq-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 32px;
            max-width: 900px;
            margin: 0 auto;
        }

        .faq-item h3 {
            font-size: 18px;
            font-weight: 600;
            color: #111827;
            margin-bottom: 8px;
        }

        .faq-item p {
            font-size: 15px;
            color: #6b7280;
            line-height: 1.7;
        }

        .session-info {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: white;
            border: 1px solid #e5e7eb;
            padding: 12px 16px;
            border-radius: 8px;
            font-size: 12px;
            color: #6b7280;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            z-index: 1000;
        }

        .session-info.restarted {
            background: #fef3c7;
            border-color: #fbbf24;
            color: #92400e;
        }

        @media (max-width: 768px) {
            .pricing-header h1 {
                font-size: 36px;
            }

            .pricing-header p {
                font-size: 16px;
            }

            .pricing-grid {
                grid-template-columns: 1fr;
                gap: 24px;
            }

            .pricing-card.featured {
                transform: scale(1);
            }

            .faq-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <?php include __DIR__ . '/../../includes/navbar.php'; ?>

    <?php if ($has_active_subscription): ?>
    <!-- Current Subscription Status Banner -->
    <div style="background: <?php echo $is_trial ? '#fef3c7' : '#d1fae5'; ?>; border-bottom: 1px solid <?php echo $is_trial ? '#fbbf24' : '#6ee7b7'; ?>; padding: 16px 0; text-align: center;">
        <div class="container">
            <?php if ($is_trial): ?>
                <p style="margin: 0; font-size: 15px; color: #92400e;">
                    ⏰ <strong>You're on a Free Trial</strong> - Active until <?php echo date('F d, Y', strtotime($current_subscription['end_date'])); ?>
                    <a href="<?php echo VIEWS_URL; ?>/profile.php?tab=subscription" style="color: #92400e; text-decoration: underline; margin-left: 12px;">View Details</a>
                </p>
            <?php else: ?>
                <p style="margin: 0; font-size: 15px; color: #065f46;">
                    ✓ <strong>You have an active subscription</strong> - Renews on <?php echo date('F d, Y', strtotime($current_subscription['end_date'])); ?>
                    <a href="<?php echo VIEWS_URL; ?>/profile.php?tab=subscription" style="color: #065f46; text-decoration: underline; margin-left: 12px;">Manage Subscription</a>
                </p>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- Pricing Header -->
    <div class="pricing-header">
        <div class="container">
            <h1>Choose Your Plan</h1>
            <p>Flexible pricing for artisans and collectors. Start with a free trial, no credit card required.</p>
        </div>
    </div>

    <!-- Pricing Plans -->
    <div class="container">
        <div class="pricing-grid">
            <!-- Trial Plan -->
            <div class="pricing-card">
                <div class="plan-name">Free Trial</div>
                <div class="plan-price">
                    €0
                    <span>/3 months</span>
                </div>
                <div class="plan-description">
                    Perfect to get started and explore the platform
                </div>
                <ul class="plan-features">
                    <li>Full platform access</li>
                    <li>Browse all listings</li>
                    <li>Connect with sellers</li>
                    <li>Email support</li>
                </ul>
                <?php if ($has_active_subscription && !$is_trial): ?>
                    <!-- Already has paid subscription -->
                    <button class="plan-button plan-button-secondary" disabled style="opacity: 0.5; cursor: not-allowed;">
                        Already Subscribed
                    </button>
                <?php elseif ($has_active_subscription && $is_trial): ?>
                    <!-- Has trial, can't get another trial -->
                    <button class="plan-button plan-button-secondary" disabled style="opacity: 0.5; cursor: not-allowed;">
                        Already on Trial
                    </button>
                <?php elseif ($is_logged_in): ?>
                    <a href="/glass-market/resources/views/create-payment.php?plan=trial" class="plan-button plan-button-secondary">
                        Start Free Trial
                    </a>
                <?php else: ?>
                    <a href="/glass-market/resources/views/register.php?plan=trial" class="plan-button plan-button-secondary">
                        Start Free Trial
                    </a>
                <?php endif; ?>
            </div>

            <!-- Monthly Plan -->
            <div class="pricing-card featured">
                <div class="plan-name">Monthly</div>
                <div class="plan-price">
                    €9.99
                    <span>/month</span>
                </div>
                <div class="plan-description">
                    Best for active buyers and sellers
                </div>
                <ul class="plan-features">
                    <li>Everything in Trial</li>
                    <li>Unlimited listings</li>
                    <li>Priority support</li>
                    <li>Featured placement</li>
                    <li>No commitment</li>
                </ul>
                <?php if ($has_active_subscription && !$is_trial): ?>
                    <!-- Already has paid subscription -->
                    <a href="<?php echo VIEWS_URL; ?>/profile.php?tab=subscription" class="plan-button plan-button-secondary">
                        Manage Subscription
                    </a>
                <?php elseif ($is_logged_in): ?>
                    <?php if ($is_trial): ?>
                        <a href="/glass-market/resources/views/create-payment.php?plan=monthly&upgrade_from_trial=1" class="plan-button plan-button-primary">
                            Upgrade from Trial
                        </a>
                    <?php else: ?>
                        <a href="/glass-market/resources/views/create-payment.php?plan=monthly" class="plan-button plan-button-primary">
                            Subscribe Now
                        </a>
                    <?php endif; ?>
                <?php else: ?>
                    <a href="/glass-market/resources/views/register.php?plan=monthly" class="plan-button plan-button-primary">
                        Get Started
                    </a>
                <?php endif; ?>
            </div>

            <!-- Annual Plan -->
            <div class="pricing-card">
                <div class="plan-name">Annual</div>
                <div class="plan-price">
                    €99
                    <span>/year</span>
                </div>
                <div class="plan-description">
                    Save 17% with annual billing
                </div>
                <ul class="plan-features">
                    <li>Everything in Monthly</li>
                    <li>Custom branding</li>
                    <li>Dedicated support</li>
                </ul>
                <?php if ($has_active_subscription && !$is_trial): ?>
                    <!-- Already has paid subscription -->
                    <a href="<?php echo VIEWS_URL; ?>/profile.php?tab=subscription" class="plan-button plan-button-secondary">
                        Manage Subscription
                    </a>
                <?php elseif ($is_logged_in): ?>
                    <?php if ($is_trial): ?>
                        <a href="/glass-market/resources/views/create-payment.php?plan=annual&upgrade_from_trial=1" class="plan-button plan-button-secondary">
                            Upgrade from Trial
                        </a>
                    <?php else: ?>
                        <a href="/glass-market/resources/views/create-payment.php?plan=annual" class="plan-button plan-button-secondary">
                            Subscribe Now
                        </a>
                    <?php endif; ?>
                <?php else: ?>
                    <a href="/glass-market/resources/views/register.php?plan=annual" class="plan-button plan-button-secondary">
                        Get Started
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- FAQ Section -->
    <div class="faq-section">
        <div class="container">
            <h2>Frequently Asked Questions</h2>
            <div class="faq-grid">
                <div class="faq-item">
                    <h3>How does the free trial work?</h3>
                    <p>Get complete access to Glass Market for 3 months, absolutely free. No credit card required. You can upgrade anytime.</p>
                </div>
                <div class="faq-item">
                    <h3>Can I change plans?</h3>
                    <p>Yes, you can upgrade or downgrade your plan at any time. Changes take effect immediately with prorated billing.</p>
                </div>
                <div class="faq-item">
                    <h3>What payment methods do you accept?</h3>
                    <p>We accept all major credit cards, debit cards, and various local payment methods through our secure payment processor Mollie.</p>
                </div>
                <div class="faq-item">
                    <h3>Can I cancel anytime?</h3>
                    <p>Absolutely. Cancel your subscription at any time from your account settings. No questions asked, no cancellation fees.</p>
                </div>
            </div>
        </div>
    </div>

    <?php include __DIR__ . '/../../includes/footer.php'; ?>

    <!-- Session Info (for testing) -->
    <?php if (isset($_SESSION['flow_restarted']) && $_SESSION['flow_restarted']): ?>
        <div class="session-info restarted" id="sessionInfo">
            ⚠️ Session expired (10 min). Flow restarted.
        </div>
        <?php unset($_SESSION['flow_restarted']); ?>
        <script>
            setTimeout(() => {
                const info = document.getElementById('sessionInfo');
                if (info) {
                    info.style.opacity = '0';
                    setTimeout(() => info.remove(), 300);
                }
            }, 5000);
        </script>
    <?php elseif (isset($_SESSION['pricing_flow_started'])): ?>
        <div class="session-info" id="sessionInfo">
            ✓ Session active (expires in <?php echo floor(($session_timeout - (time() - $_SESSION['pricing_flow_started'])) / 60); ?> min)
        </div>
        <script>
            setTimeout(() => {
                const info = document.getElementById('sessionInfo');
                if (info) {
                    info.style.opacity = '0';
                    setTimeout(() => info.remove(), 300);
                }
            }, 3000);
        </script>
    <?php endif; ?>

    <script>
        // Update session activity on user interaction
        let activityTimeout;
        
        function updateActivity() {
            clearTimeout(activityTimeout);
            activityTimeout = setTimeout(() => {
                // Ping server to keep session alive
                fetch('/glass-market/includes/update-session.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify({flow: 'pricing'})
                });
            }, 1000);
        }

        // Track user activity
        ['click', 'scroll', 'mousemove', 'keypress'].forEach(event => {
            document.addEventListener(event, updateActivity, {passive: true});
        });
    </script>
</body>
</html>
