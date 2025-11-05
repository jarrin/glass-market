<?php
// Subscription Card Component
// Displays a single subscription card
// Expects $subscription array to be passed in

$is_active = $subscription['is_active'] == 1;
$is_trial = $subscription['is_trial'] == 1;
$start_date = new DateTime($subscription['start_date']);
$end_date = new DateTime($subscription['end_date']);
$now = new DateTime();
$days_remaining = $now->diff($end_date)->days;
$is_expired = $now > $end_date;
?>

<div class="subscription-card" style="
    background: <?php echo $is_active ? '#f0fdf4' : '#ffffff'; ?>;
    border: 2px solid <?php echo $is_active ? '#22c55e' : '#e5e7eb'; ?>;
    border-radius: 16px;
    padding: 24px;
    margin-bottom: 16px;
">
    <!-- Header -->
    <div style="display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 20px;">
        <div style="display: flex; align-items: center; gap: 12px;">
            <div style="
                width: 48px;
                height: 48px;
                background: <?php echo $is_active ? '#dcfce7' : '#f3f4f6'; ?>;
                border-radius: 12px;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 24px;
            ">
                <?php echo $is_trial ? '🎁' : '⭐'; ?>
            </div>
            <div>
                <h3 style="margin: 0; font-size: 18px; font-weight: 600; color: #1f2937;">
                    Subscription #<?php echo $subscription['id']; ?>
                </h3>
                <p style="margin: 4px 0 0; font-size: 14px; color: #6b7280;">
                    <?php echo $is_trial ? 'Free Trial' : 'Premium Plan'; ?>
                </p>
            </div>
        </div>
        
        <div style="
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            background: <?php echo $is_active && !$is_expired ? '#dcfce7' : '#fef2f2'; ?>;
            color: <?php echo $is_active && !$is_expired ? '#16a34a' : '#dc2626'; ?>;
        ">
            <?php echo $is_active && !$is_expired ? '✓ Active' : '✗ Inactive'; ?>
        </div>
    </div>
    
    <!-- Details Grid -->
    <div style="
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 16px;
        margin-bottom: 20px;
        padding: 20px;
        background: white;
        border-radius: 12px;
    ">
        <div>
            <div style="font-size: 12px; color: #6b7280; margin-bottom: 4px;">Start Date</div>
            <div style="font-size: 14px; font-weight: 600; color: #1f2937;">
                <?php echo $start_date->format('M d, Y'); ?>
            </div>
        </div>
        
        <div>
            <div style="font-size: 12px; color: #6b7280; margin-bottom: 4px;">End Date</div>
            <div style="font-size: 14px; font-weight: 600; color: #1f2937;">
                <?php echo $end_date->format('M d, Y'); ?>
            </div>
        </div>
        
        <div>
            <div style="font-size: 12px; color: #6b7280; margin-bottom: 4px;">Days Remaining</div>
            <div style="font-size: 14px; font-weight: 600; color: <?php echo $days_remaining < 7 ? '#dc2626' : '#16a34a'; ?>;">
                <?php echo $is_expired ? '0 (Expired)' : $days_remaining; ?>
            </div>
        </div>
        
        <div>
            <div style="font-size: 12px; color: #6b7280; margin-bottom: 4px;">Type</div>
            <div style="font-size: 14px; font-weight: 600; color: #1f2937;">
                <?php echo $is_trial ? 'Trial' : 'Paid'; ?>
            </div>
        </div>
    </div>
    
    <!-- Actions -->
    <?php if ($is_active && !$is_expired): ?>
    <div style="display: flex; gap: 12px;">
        <form method="POST" style="margin: 0;" onsubmit="return confirm('Are you sure you want to cancel this subscription?');">
            <input type="hidden" name="subscription_id" value="<?php echo $subscription['id']; ?>">
            <button 
                type="submit" 
                name="cancel_subscription"
                style="
                    padding: 10px 20px;
                    background: #ef4444;
                    color: white;
                    border: none;
                    border-radius: 8px;
                    font-size: 14px;
                    font-weight: 600;
                    cursor: pointer;
                    transition: background 0.2s;
                "
                onmouseover="this.style.background='#dc2626'"
                onmouseout="this.style.background='#ef4444'"
            >
                Cancel Subscription
            </button>
        </form>
    </div>
    <?php endif; ?>
</div>
