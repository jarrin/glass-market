<?php
// Overview Tab Content
// Expects: $user, $user_listings_count, $user_subscriptions, $company
?>
<div class="tab-panel active" id="tab-overview">
    <h2 class="section-title">Account Overview</h2>
    
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 24px; margin-bottom: 32px;">
        <!-- Account Status Card -->
        <div style="background: white; padding: 24px; border-radius: 16px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 16px;">
                <div style="width: 48px; height: 48px; background: #dbeafe; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 24px;">
                    ✅
                </div>
                <div>
                    <h3 style="margin: 0; font-size: 16px; font-weight: 600;">Account Status</h3>
                    <p style="margin: 4px 0 0; font-size: 13px; color: #6b7280;">Active since <?php echo $user['created_at'] ? date('M Y', strtotime($user['created_at'])) : 'Recently'; ?></p>
                </div>
            </div>
            <div style="background: #f0fdf4; padding: 12px; border-radius: 8px; border-left: 4px solid #22c55e;">
                <div style="font-size: 13px; color: #166534;">
                    <strong>Verified Account</strong><br>
                    Email: <?php echo htmlspecialchars($user['email']); ?>
                </div>
            </div>
        </div>

        <!-- Subscription Status Card -->
        <div style="background: white; padding: 24px; border-radius: 16px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 16px;">
                <div style="width: 48px; height: 48px; background: #fef3c7; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 24px;">
                    ⭐
                </div>
                <div>
                    <h3 style="margin: 0; font-size: 16px; font-weight: 600;">Subscription</h3>
                    <p style="margin: 4px 0 0; font-size: 13px; color: #6b7280;"><?php echo count($user_subscriptions); ?> active plan(s)</p>
                </div>
            </div>
            <?php if (!empty($user_subscriptions)): ?>
                <?php 
                    $active_sub = null;
                    foreach ($user_subscriptions as $sub) {
                        if ($sub['is_active'] == 1) {
                            $active_sub = $sub;
                            break;
                        }
                    }
                ?>
                <?php if ($active_sub): ?>
                    <?php 
                        $end_date = new DateTime($active_sub['end_date']);
                        $now = new DateTime();
                        $days_left = $now->diff($end_date)->days;
                    ?>
                    <div style="background: #fffbeb; padding: 12px; border-radius: 8px; border-left: 4px solid #f59e0b;">
                        <div style="font-size: 13px; color: #92400e;">
                            <strong><?php echo $active_sub['is_trial'] ? 'Free Trial' : 'Premium Plan'; ?></strong><br>
                            <?php echo $days_left; ?> days remaining
                        </div>
                    </div>
                <?php else: ?>
                    <div style="background: #f3f4f6; padding: 12px; border-radius: 8px;">
                        <div style="font-size: 13px; color: #6b7280;">No active subscription</div>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <div style="background: #f3f4f6; padding: 12px; border-radius: 8px;">
                    <div style="font-size: 13px; color: #6b7280;">
                        No subscription yet<br>
                        <a href="<?php echo VIEWS_URL; ?>/subscription.php" style="color: #2f6df5; text-decoration: none; font-weight: 600;">Start free trial →</a>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Activity Card -->
        <div style="background: white; padding: 24px; border-radius: 16px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 16px;">
                <div style="width: 48px; height: 48px; background: #e0e7ff; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 24px;">
                    📊
                </div>
                <div>
                    <h3 style="margin: 0; font-size: 16px; font-weight: 600;">Your Activity</h3>
                    <p style="margin: 4px 0 0; font-size: 13px; color: #6b7280;">Marketplace presence</p>
                </div>
            </div>
            <div style="display: grid; gap: 8px;">
                <div style="display: flex; justify-content: space-between; padding: 8px 12px; background: #f9fafb; border-radius: 6px;">
                    <span style="font-size: 13px; color: #6b7280;">Total Listings:</span>
                    <strong style="font-size: 13px; color: #1f2937;"><?php echo $user_listings_count; ?></strong>
                </div>
                <?php if ($company): ?>
                <div style="display: flex; justify-content: space-between; padding: 8px 12px; background: #f9fafb; border-radius: 6px;">
                    <span style="font-size: 13px; color: #6b7280;">Company:</span>
                    <strong style="font-size: 13px; color: #1f2937;"><?php echo htmlspecialchars($company['name']); ?></strong>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
