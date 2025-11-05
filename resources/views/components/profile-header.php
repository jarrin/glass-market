<?php
// Profile Header Component
// Displays user avatar, name, and stats
?>
<div class="profile-header">
    <div class="profile-avatar">
        <?php if (!empty($user['avatar'])): ?>
            <img src="<?php echo PUBLIC_URL . '/' . htmlspecialchars($user['avatar']); ?>" alt="<?php echo htmlspecialchars($user['name']); ?>">
        <?php else: ?>
            <div class="avatar-placeholder">
                <?php echo strtoupper(substr($user['name'], 0, 1)); ?>
            </div>
        <?php endif; ?>
    </div>
    
    <div class="profile-info">
        <h1 class="profile-name"><?php echo htmlspecialchars($user['name']); ?></h1>
        <p class="profile-email"><?php echo htmlspecialchars($user['email']); ?></p>
        <?php if (!empty($user['company_name'])): ?>
            <p class="profile-company">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="3" width="18" height="18" rx="2"></rect>
                </svg>
                <?php echo htmlspecialchars($user['company_name']); ?>
            </p>
        <?php endif; ?>
    </div>
    
    <div class="profile-stats">
        <div class="stat-item">
            <div class="stat-value"><?php echo $user_listings_count; ?></div>
            <div class="stat-label">Listings</div>
        </div>
        <div class="stat-item">
            <div class="stat-value"><?php echo count($user_subscriptions); ?></div>
            <div class="stat-label">Subscriptions</div>
        </div>
        <?php if ($user['created_at']): ?>
        <div class="stat-item">
            <div class="stat-value"><?php echo date('M Y', strtotime($user['created_at'])); ?></div>
            <div class="stat-label">Member Since</div>
        </div>
        <?php endif; ?>
    </div>
</div>
