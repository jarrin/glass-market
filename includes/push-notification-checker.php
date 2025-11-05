<?php
/**
 * Push Notification Checker
 * Checks for pending push notifications for logged-in user
 * Include this in navbar or footer
 */

if (!isset($_SESSION['user_id'])) {
    return;
}

$user_id = $_SESSION['user_id'];
?>

<script>
// Check for new push notifications every 30 seconds
let lastNotificationCheck = 0;

function checkPushNotifications() {
    if (!document.hidden && ('Notification' in window) && Notification.permission === 'granted') {
        fetch('<?php echo BASE_URL; ?>/includes/get-push-notifications.php')
            .then(response => response.json())
            .then(data => {
                if (data.notifications && data.notifications.length > 0) {
                    data.notifications.forEach(notification => {
                        showPushNotification(notification);
                    });
                }
            })
            .catch(error => console.error('Error checking notifications:', error));
    }
}

function showPushNotification(notification) {
    const n = new Notification(notification.title, {
        body: notification.body,
        icon: '<?php echo BASE_URL; ?>/public/images/logo.png',
        badge: '<?php echo BASE_URL; ?>/public/images/badge.png',
        tag: 'glass-market-' + notification.id,
        requireInteraction: false
    });
    
    n.onclick = function(event) {
        event.preventDefault();
        window.open(notification.url, '_blank');
        n.close();
        
        // Mark as read
        fetch('<?php echo BASE_URL; ?>/includes/mark-notification-read.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({notification_id: notification.id})
        });
    };
    
    // Auto close after 5 seconds
    setTimeout(() => n.close(), 5000);
}

// Check immediately on load
document.addEventListener('DOMContentLoaded', function() {
    if ('Notification' in window && Notification.permission === 'granted') {
        checkPushNotifications();
    }
});

// Check every 30 seconds
setInterval(checkPushNotifications, 30000);

// Check when page becomes visible
document.addEventListener('visibilitychange', function() {
    if (!document.hidden) {
        checkPushNotifications();
    }
});
</script>
