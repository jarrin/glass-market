<?php
/**
 * Subscription Notification Modal
 * Beautiful centered modal with blur overlay for expired/expiring subscriptions
 */

// Include subscription check if not already included
if (!isset($subscription_status)) {
    require_once __DIR__ . '/subscription-check.php';
}

// Only show notification if needed
if (!$subscription_status['show_notification']) {
    return;
}

// Don't show notification on certain pages (users need access to these!)
$current_page = basename($_SERVER['PHP_SELF']);
$excluded_pages = [
    'pricing.php',        // Let users see pricing
    'login.php',          // Already on login
    'register.php',       // Already registering
    'logout.php',         // Logging out
    'mollie-return.php',  // Payment return page
    'mollie-webhook.php', // Payment webhook
];

// Also exclude all admin pages
$current_uri = $_SERVER['REQUEST_URI'] ?? '';
if (in_array($current_page, $excluded_pages) || strpos($current_uri, '/admin/') !== false) {
    return;
}

// Prepare notification content based on type
$notification_data = [];

switch ($subscription_status['notification_type']) {
    case 'expired':
        $notification_data = [
            'title' => 'Subscription Expired',
            'message' => 'Your ' . ($subscription_status['is_trial'] ? 'trial period' : 'subscription') . ' ended on ' . date('F d, Y', strtotime($subscription_status['end_date'])) . '. Renew your subscription to continue accessing Glass Market premium features.',
            'icon' => null,
            'color' => 'error',
            'primary_button' => [
                'text' => 'Renew Subscription',
                'link' => '/glass-market/resources/views/pricing.php'
            ],
            'secondary_button' => [
                'text' => 'View Plans',
                'link' => '/glass-market/resources/views/pricing.php'
            ]
        ];
        break;
        
    case 'expiring_soon':
        $days = $subscription_status['days_remaining'];
        $notification_data = [
            'title' => 'Subscription Expiring Soon',
            'message' => 'Your subscription will expire in ' . $days . ' day' . ($days != 1 ? 's' : '') . ' on ' . date('F d, Y', strtotime($subscription_status['end_date'])) . '. Renew now to ensure uninterrupted access to your account.',
            'icon' => null,
            'color' => 'warning',
            'primary_button' => [
                'text' => 'Renew Now',
                'link' => '/glass-market/resources/views/pricing.php'
            ],
            'secondary_button' => [
                'text' => 'Remind Me Later',
                'link' => '#',
                'dismiss' => true
            ]
        ];
        break;
        
    case 'no_subscription':
        $notification_data = [
            'title' => 'Subscription Required',
            'message' => 'Access to Glass Market requires an active subscription. Start with a complimentary 3-month trial to explore our platform and connect with artisans worldwide.',
            'icon' => null,
            'color' => 'info',
            'primary_button' => [
                'text' => 'View Plans',
                'link' => '/glass-market/resources/views/pricing.php'
            ],
            'secondary_button' => [
                'text' => 'Learn More',
                'link' => '/glass-market/resources/views/about.php',
                'dismiss' => true
            ]
        ];
        break;
        
    case 'not_logged_in':
        $notification_data = [
            'title' => 'Welcome to Glass Market',
            'message' => 'Join our community of glass artisans and collectors. Sign up today to get a free 3-month trial and access thousands of unique glass art pieces from around the world.',
            'icon' => null,
            'color' => 'welcome',
            'primary_button' => [
                'text' => 'Sign Up Free',
                'link' => '/glass-market/resources/views/register.php'
            ],
            'secondary_button' => [
                'text' => 'Login',
                'link' => '/glass-market/resources/views/login.php'
            ]
        ];
        break;
}
?>

<!-- Subscription Notification Styles -->
<style>
    .subscription-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.7);
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
        z-index: 99999;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
        animation: fadeIn 0.3s ease;
    }
    
    @keyframes fadeIn {
        from {
            opacity: 0;
        }
        to {
            opacity: 1;
        }
    }
    
    .subscription-modal {
        background: white;
        border-radius: 12px;
        max-width: 560px;
        width: 100%;
        padding: 48px;
        box-shadow: 0 25px 50px rgba(0, 0, 0, 0.25);
        animation: slideUp 0.3s ease;
        position: relative;
        border-top: 4px solid #1f2937;
    }
    
    @keyframes slideUp {
        from {
            transform: translateY(20px);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }
    
    .subscription-modal-icon {
        width: 64px;
        height: 64px;
        margin: 0 auto 24px;
        background: #f3f4f6;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 32px;
    }
    
    .subscription-modal-title {
        font-size: 24px;
        font-weight: 600;
        text-align: center;
        margin-bottom: 12px;
        color: #111827;
        letter-spacing: -0.02em;
    }
    
    .subscription-modal-message {
        font-size: 15px;
        line-height: 1.7;
        text-align: center;
        color: #4b5563;
        margin-bottom: 32px;
        font-weight: 400;
    }
    
    .subscription-modal-buttons {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }
    
    .subscription-btn {
        padding: 16px 32px;
        border: none;
        border-radius: 12px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        text-decoration: none;
        text-align: center;
        transition: all 0.2s;
        display: inline-block;
    }
    
    .subscription-btn-primary {
        background: #111827;
        color: white;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
        font-weight: 600;
    }
    
    .subscription-btn-primary:hover {
        background: #000000;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
    }
    
    .subscription-btn-secondary {
        background: transparent;
        color: #6b7280;
        border: 1.5px solid #d1d5db;
        font-weight: 500;
    }
    
    .subscription-btn-secondary:hover {
        background: #f9fafb;
        border-color: #9ca3af;
        color: #374151;
    }
    
    .subscription-modal.error {
        border-top-color: #dc2626;
    }
    
    .subscription-modal.warning {
        border-top-color: #f59e0b;
    }
    
    .subscription-modal.info {
        border-top-color: #2563eb;
    }
    
    .subscription-modal.welcome {
        border-top-color: #059669;
    }
    
    .subscription-modal.error .subscription-modal-icon {
        background: #fee2e2;
        color: #dc2626;
    }
    
    .subscription-modal.warning .subscription-modal-icon {
        background: #fef3c7;
        color: #f59e0b;
    }
    
    .subscription-modal.info .subscription-modal-icon {
        background: #dbeafe;
        color: #2563eb;
    }
    
    .subscription-modal.welcome .subscription-modal-icon {
        background: #d1fae5;
        color: #059669;
    }
    
    /* Prevent body scroll when modal is open */
    body.subscription-modal-open {
        overflow: hidden;
    }
    
    /* Mobile responsive */
    @media (max-width: 640px) {
        .subscription-modal {
            padding: 32px 24px;
        }
        
        .subscription-modal-icon {
            font-size: 48px;
        }
        
        .subscription-modal-title {
            font-size: 24px;
        }
        
        .subscription-modal-message {
            font-size: 14px;
        }
        
        .subscription-btn {
            padding: 14px 24px;
            font-size: 15px;
        }
    }
</style>

<!-- Subscription Notification Modal -->
<div class="subscription-overlay" id="subscriptionOverlay">
    <div class="subscription-modal <?php echo $notification_data['color']; ?>">
        <?php if ($notification_data['color'] === 'error'): ?>
            <div class="subscription-modal-icon">!</div>
        <?php elseif ($notification_data['color'] === 'warning'): ?>
            <div class="subscription-modal-icon">⚠</div>
        <?php elseif ($notification_data['color'] === 'welcome'): ?>
            <div class="subscription-modal-icon">👋</div>
        <?php else: ?>
            <div class="subscription-modal-icon">i</div>
        <?php endif; ?>
        <h2 class="subscription-modal-title">
            <?php echo htmlspecialchars($notification_data['title']); ?>
        </h2>
        <p class="subscription-modal-message">
            <?php echo htmlspecialchars($notification_data['message']); ?>
        </p>
        <div class="subscription-modal-buttons">
            <a href="<?php echo htmlspecialchars($notification_data['primary_button']['link']); ?>" 
               class="subscription-btn subscription-btn-primary">
                <?php echo htmlspecialchars($notification_data['primary_button']['text']); ?>
            </a>
            
            <?php if (isset($notification_data['secondary_button']['dismiss']) && $notification_data['secondary_button']['dismiss']): ?>
                <button onclick="dismissNotification()" 
                        class="subscription-btn subscription-btn-secondary">
                    <?php echo htmlspecialchars($notification_data['secondary_button']['text']); ?>
                </button>
            <?php else: ?>
                <a href="<?php echo htmlspecialchars($notification_data['secondary_button']['link']); ?>" 
                   class="subscription-btn subscription-btn-secondary">
                    <?php echo htmlspecialchars($notification_data['secondary_button']['text']); ?>
                </a>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    // Add class to body to prevent scrolling
    document.body.classList.add('subscription-modal-open');
    
    // Dismiss notification function
    function dismissNotification() {
        const overlay = document.getElementById('subscriptionOverlay');
        overlay.style.animation = 'fadeOut 0.3s ease';
        
        setTimeout(() => {
            overlay.remove();
            document.body.classList.remove('subscription-modal-open');
        }, 300);
        
        // Store dismissal in session (for "remind me later")
        fetch('/glass-market/includes/dismiss-notification.php', {
            method: 'POST'
        });
    }
    
    // Add CSS for fade out animation
    const style = document.createElement('style');
    style.textContent = `
        @keyframes fadeOut {
            from { opacity: 1; }
            to { opacity: 0; }
        }
    `;
    document.head.appendChild(style);
</script>
