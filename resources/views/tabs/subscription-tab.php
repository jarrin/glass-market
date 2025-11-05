<?php
// Subscription Tab Content
// Expects: $user_subscriptions, $success_message, $error_message, $subscription_error
?>
<div class="tab-panel" id="tab-subscription">
    <h2 class="section-title">My Subscriptions</h2>
    
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

    <p style="font-size: 14px; color: var(--profile-muted); margin-bottom: 24px;">
        Manage your active subscriptions and payment plans
    </p>
    
    <?php if (!empty($user_subscriptions) && count($user_subscriptions) > 0): ?>
        <!-- TEST BUTTON: Expire Subscription -->
        <div style="
            background: linear-gradient(135deg, #fff7ed 0%, #fed7aa 100%);
            border: 2px solid #fb923c;
            border-radius: 12px;
            padding: 16px 20px;
            margin-bottom: 24px;
        ">
            <div style="display: flex; justify-content: space-between; align-items: center; gap: 16px;">
                <div style="flex: 1;">
                    <div style="font-weight: 700; font-size: 14px; color: #9a3412; margin-bottom: 4px;">
                        🧪 Test Mode
                    </div>
                    <div style="font-size: 13px; color: #92400e;">
                        Instantly expire your subscription for testing access control
                    </div>
                </div>
                <form method="POST" action="<?php echo VIEWS_URL; ?>/handlers/expire-subscription-test.php" style="margin: 0;">
                    <button
                        type="submit"
                        name="expire_subscription_test"
                        onclick="return confirm('⚠️ This will expire your subscription immediately for testing. Continue?')"
                        style="
                            padding: 10px 20px;
                            background: #ea580c;
                            color: white;
                            border: none;
                            border-radius: 8px;
                            font-weight: 600;
                            font-size: 13px;
                            cursor: pointer;
                            white-space: nowrap;
                            transition: all 0.2s;
                        "
                        onmouseover="this.style.background='#c2410c'"
                        onmouseout="this.style.background='#ea580c'"
                    >
                        Expire Subscription
                    </button>
                </form>
            </div>
        </div>

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
