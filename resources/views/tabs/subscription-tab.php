<?php
// Subscription Tab Content
// Expects: $user_subscriptions, $success_message, $error_message, $subscription_error
?>
<div class="tab-panel" id="tab-subscription">
    <h2 class="section-title">My Subscriptions</h2>
    
    <?php if ($success_message && isset($_POST['cancel_subscription'])): ?>
        <div class="alert alert-success">
            <?php echo htmlspecialchars($success_message); ?>
        </div>
    <?php endif; ?>
    
    <?php if ($error_message && isset($_POST['cancel_subscription'])): ?>
        <div class="alert alert-error">
            <?php echo htmlspecialchars($error_message); ?>
        </div>
    <?php endif; ?>

    <p style="font-size: 14px; color: var(--profile-muted); margin-bottom: 24px;">
        Manage your active subscriptions and payment plans
    </p>
    
    <?php if (!empty($user_subscriptions) && count($user_subscriptions) > 0): ?>
        <!-- Display subscriptions -->
        <?php foreach ($user_subscriptions as $subscription): ?>
            <?php include __DIR__ . '/../components/subscription-card.php'; ?>
        <?php endforeach; ?>
        
        <!-- Info Box -->
        <div style="
            background: #fffbeb;
            border: 1px solid #fbbf24;
            border-radius: 12px;
            padding: 16px 20px;
            margin-top: 24px;
        ">
            <div style="display: flex; gap: 12px;">
                <span style="font-size: 20px;">ℹ️</span>
                <div style="font-size: 14px; color: #92400e;">
                    <strong>Cancellation Policy:</strong> When you cancel a subscription, you'll retain access until the end date. 
                    No refunds will be issued for the current billing period.
                </div>
            </div>
        </div>
    <?php else: ?>
        <!-- Empty State -->
        <div style="
            text-align: center;
            padding: 60px 20px;
            background: #f9fafb;
            border-radius: 16px;
            border: 2px dashed #d1d5db;
        ">
            <div style="font-size: 64px; margin-bottom: 16px;">📦</div>
            <h3 style="margin: 0 0 8px; font-size: 20px; font-weight: 600; color: #1f2937;">
                No Active Subscriptions
            </h3>
            <p style="margin: 0 0 24px; font-size: 14px; color: #6b7280;">
                You don't have any subscriptions yet. Start with a free trial!
            </p>
            <a 
                href="<?php echo VIEWS_URL; ?>/subscription.php" 
                style="
                    display: inline-block;
                    padding: 12px 24px;
                    background: #2f6df5;
                    color: white;
                    text-decoration: none;
                    border-radius: 10px;
                    font-weight: 600;
                    font-size: 14px;
                    transition: background 0.2s;
                "
                onmouseover="this.style.background='#1e4dd8'"
                onmouseout="this.style.background='#2f6df5'"
            >
                Subscribe Now
            </a>
        </div>
    <?php endif; ?>
</div>
