<?php
/**
 * Notification Preferences Tab
 * Allows users to manage email and push notifications
 */
?>
<div class="tab-panel" id="tab-notifications">
    <h2 class="section-title">Notification Preferences</h2>

    <?php if (!$subscription_status['has_access']): ?>
        <?php include __DIR__ . '/../components/subscription-required-message.php'; ?>
    <?php else: ?>

    <p style="font-size: 14px; color: #6b7280; margin-bottom: 24px;">
        Control what notifications you receive and how you want to be notified.
    </p>

    <?php if ($success_message && isset($_POST['update_notifications'])): ?>
        <div class="alert alert-success">
            <?php echo htmlspecialchars($success_message); ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="">
        <!-- Email Notifications Section -->
        <div style="background: white; padding: 32px; border-radius: 16px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); margin-bottom: 24px;">
            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 24px;">
                <div style="width: 48px; height: 48px; background: #dbeafe; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 24px;">
                    📧
                </div>
                <div>
                    <h3 style="margin: 0; font-size: 18px; font-weight: 600;">Email Notifications</h3>
                    <p style="margin: 4px 0 0; font-size: 13px; color: #6b7280;">Receive updates via email</p>
                </div>
            </div>

            <div style="display: grid; gap: 16px;">
                <!-- New Listings -->
                <label style="
                    display: flex;
                    align-items: center;
                    justify-content: space-between;
                    padding: 16px;
                    background: #f9fafb;
                    border-radius: 10px;
                    cursor: pointer;
                    transition: background 0.2s;
                " onmouseover="this.style.background='#f3f4f6'" onmouseout="this.style.background='#f9fafb'">
                    <div style="flex: 1;">
                        <div style="font-weight: 600; font-size: 15px; color: #1f2937; margin-bottom: 4px;">
                            New Listings
                        </div>
                        <div style="font-size: 13px; color: #6b7280;">
                            Get notified when new glass listings are posted
                        </div>
                    </div>
                    <div class="toggle-switch">
                        <input 
                            type="checkbox" 
                            name="notify_new_listings" 
                            id="notify_new_listings" 
                            value="1"
                            <?php echo (!empty($user['notify_new_listings']) ? 'checked' : ''); ?>
                            style="display: none;"
                        >
                        <div class="toggle-slider"></div>
                    </div>
                </label>

                <!-- Price Changes -->
                <label style="
                    display: flex;
                    align-items: center;
                    justify-content: space-between;
                    padding: 16px;
                    background: #f9fafb;
                    border-radius: 10px;
                    cursor: pointer;
                    transition: background 0.2s;
                " onmouseover="this.style.background='#f3f4f6'" onmouseout="this.style.background='#f9fafb'">
                    <div style="flex: 1;">
                        <div style="font-weight: 600; font-size: 15px; color: #1f2937; margin-bottom: 4px;">
                            Account Updates
                        </div>
                        <div style="font-size: 13px; color: #6b7280;">
                            Subscription renewals, payment confirmations
                        </div>
                    </div>
                    <div class="toggle-switch">
                        <input 
                            type="checkbox" 
                            name="notify_account_updates" 
                            id="notify_account_updates" 
                            value="1"
                            <?php echo (!empty($user['notify_account_updates']) ? 'checked' : ''); ?>
                            style="display: none;"
                        >
                        <div class="toggle-slider"></div>
                    </div>
                </label>
            </div>
        </div>

        <!-- Push Notifications Section -->
        <div style="background: white; padding: 32px; border-radius: 16px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); margin-bottom: 24px;">
            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 24px;">
                <div style="width: 48px; height: 48px; background: #fef3c7; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 24px;">
                    🔔
                </div>
                <div>
                    <h3 style="margin: 0; font-size: 18px; font-weight: 600;">Push Notifications</h3>
                    <p style="margin: 4px 0 0; font-size: 13px; color: #6b7280;">Real-time browser notifications</p>
                </div>
            </div>

            <div id="push-notification-status" style="margin-bottom: 16px;">
                <!-- Will be populated by JavaScript -->
            </div>

            <div style="display: grid; gap: 16px;">
                <!-- Instant Alerts -->
                <label style="
                    display: flex;
                    align-items: center;
                    justify-content: space-between;
                    padding: 16px;
                    background: #f9fafb;
                    border-radius: 10px;
                    cursor: pointer;
                    transition: background 0.2s;
                " onmouseover="this.style.background='#f3f4f6'" onmouseout="this.style.background='#f9fafb'">
                    <div style="flex: 1;">
                        <div style="font-weight: 600; font-size: 15px; color: #1f2937; margin-bottom: 4px;">
                            Instant Listing Alerts
                        </div>
                        <div style="font-size: 13px; color: #6b7280;">
                            Get instant push notifications for new listings
                        </div>
                    </div>
                    <div class="toggle-switch">
                        <input 
                            type="checkbox" 
                            name="push_new_listings" 
                            id="push_new_listings" 
                            value="1"
                            <?php echo (!empty($user['push_new_listings']) ? 'checked' : ''); ?>
                            style="display: none;"
                        >
                        <div class="toggle-slider"></div>
                    </div>
                </label>
            </div>

            <button 
                type="button" 
                id="enable-push-button"
                style="
                    width: 100%;
                    padding: 12px 24px;
                    background: #f59e0b;
                    color: white;
                    border: none;
                    border-radius: 8px;
                    font-size: 14px;
                    font-weight: 600;
                    cursor: pointer;
                    margin-top: 16px;
                    display: none;
                "
            >
                🔔 Enable Browser Notifications
            </button>
        </div>

        <!-- Save Button -->
        <button 
            type="submit" 
            name="update_notifications"
            style="
                width: 100%;
                max-width: 300px;
                padding: 14px 24px;
                background: #2f6df5;
                color: white;
                border: none;
                border-radius: 10px;
                font-size: 16px;
                font-weight: 600;
                cursor: pointer;
                transition: background 0.2s;
            "
            onmouseover="this.style.background='#1e4dd8'"
            onmouseout="this.style.background='#2f6df5'"
        >
            Save Preferences
        </button>
    </form>
</div>

<style>
/* Toggle Switch Styles */
.toggle-switch {
    position: relative;
    width: 50px;
    height: 28px;
    flex-shrink: 0;
}

.toggle-slider {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: #d1d5db;
    border-radius: 14px;
    transition: background 0.3s;
    cursor: pointer;
}

.toggle-slider:before {
    content: '';
    position: absolute;
    height: 22px;
    width: 22px;
    left: 3px;
    bottom: 3px;
    background: white;
    border-radius: 50%;
    transition: transform 0.3s;
}

input[type="checkbox"]:checked + .toggle-slider {
    background: #22c55e;
}

input[type="checkbox"]:checked + .toggle-slider:before {
    transform: translateX(22px);
}
</style>

<script>
// Toggle switch functionality
document.addEventListener('DOMContentLoaded', function() {
    const toggles = document.querySelectorAll('.toggle-switch');
    
    toggles.forEach(toggle => {
        const input = toggle.querySelector('input[type="checkbox"]');
        const slider = toggle.querySelector('.toggle-slider');
        
        slider.addEventListener('click', function() {
            input.checked = !input.checked;
        });
    });

    // Check push notification permission
    checkPushNotificationStatus();
    
    // Enable push notifications button
    document.getElementById('enable-push-button')?.addEventListener('click', requestPushPermission);
});

function checkPushNotificationStatus() {
    const statusDiv = document.getElementById('push-notification-status');
    const enableButton = document.getElementById('enable-push-button');
    
    if (!('Notification' in window)) {
        statusDiv.innerHTML = `
            <div style="padding: 12px; background: #fef2f2; border: 1px solid #fecaca; border-radius: 8px; font-size: 13px; color: #991b1b;">
                ❌ Your browser doesn't support push notifications
            </div>
        `;
        return;
    }
    
    const permission = Notification.permission;
    
    if (permission === 'granted') {
        statusDiv.innerHTML = `
            <div style="padding: 12px; background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 8px; font-size: 13px; color: #166534;">
                ✅ Push notifications are enabled
            </div>
        `;
    } else if (permission === 'denied') {
        statusDiv.innerHTML = `
            <div style="padding: 12px; background: #fef2f2; border: 1px solid #fecaca; border-radius: 8px; font-size: 13px; color: #991b1b;">
                ❌ Push notifications are blocked. Please enable them in your browser settings.
            </div>
        `;
    } else {
        statusDiv.innerHTML = `
            <div style="padding: 12px; background: #fffbeb; border: 1px solid #fbbf24; border-radius: 8px; font-size: 13px; color: #92400e;">
                ⚠️ Push notifications are not enabled yet
            </div>
        `;
        enableButton.style.display = 'block';
    }
}

function requestPushPermission() {
    if (!('Notification' in window)) {
        alert('Your browser doesn\'t support notifications');
        return;
    }
    
    Notification.requestPermission().then(function(permission) {
        if (permission === 'granted') {
            new Notification('Glass Market', {
                body: '🎉 Push notifications enabled! You\'ll now receive instant alerts.',
                icon: '/public/images/logo.png'
            });
            checkPushNotificationStatus();
        }
    });
}
</script>
