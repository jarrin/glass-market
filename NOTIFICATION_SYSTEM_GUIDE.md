# Email and Push Notification System

## Overview
Complete email and push notification system for Glass Market with user preferences.

## Features Implemented

### 1. **Email Notifications**
- ✅ Welcome email on registration
- ✅ New listing notifications
- ✅ Email verification (if needed)
- ✅ Styled email templates with responsive design

### 2. **Push Notifications**
- ✅ Browser push notifications for new listings
- ✅ Real-time notification checking (every 30 seconds)
- ✅ User preference controls
- ✅ Notification permission management

### 3. **User Preferences**
New tab in Profile page where users can control:
- Email notifications for new listings
- Email for account updates
- Newsletter subscription
- Push notifications for instant alerts
- Push notifications for messages

## Files Created

### Email Service
- `app/Services/EmailService.php` - Main email service class

### Notification System
- `resources/views/tabs/notifications-tab.php` - User preferences UI
- `resources/views/handlers/notification-handler.php` - Saves preferences
- `includes/notify-new-listing.php` - Sends notifications when listing is created
- `includes/push-notification-checker.php` - JavaScript to check for push notifications
- `includes/get-push-notifications.php` - API to get pending notifications
- `includes/mark-notification-read.php` - API to mark notification as read

## Database Changes

### New Columns in `users` table:
```sql
ALTER TABLE users ADD COLUMN notify_new_listings TINYINT(1) DEFAULT 1;
ALTER TABLE users ADD COLUMN notify_account_updates TINYINT(1) DEFAULT 1;
ALTER TABLE users ADD COLUMN notify_newsletter TINYINT(1) DEFAULT 0;
ALTER TABLE users ADD COLUMN push_new_listings TINYINT(1) DEFAULT 0;
ALTER TABLE users ADD COLUMN push_messages TINYINT(1) DEFAULT 0;
```

### New Table: `push_notifications`
```sql
CREATE TABLE push_notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    body TEXT,
    url VARCHAR(500),
    is_read TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_user_id (user_id),
    INDEX idx_is_read (is_read)
);
```

## Usage

### 1. Send Welcome Email (Automatic)
When a user registers, they automatically receive a welcome email:
```php
// Already integrated in register.php
$emailService = new EmailService();
$emailService->sendWelcomeEmail($user_email, $user_name);
```

### 2. Notify Users of New Listing
When someone creates a listing, call this function:
```php
require_once 'includes/notify-new-listing.php';
notifyUsersOfNewListing($listing_id);
```

### 3. User Manages Preferences
Users go to Profile → Notifications tab to:
- Toggle email notifications on/off
- Enable/disable push notifications
- Choose what type of notifications they want

### 4. Push Notifications
Add this to your navbar or footer to enable push checking:
```php
<?php include 'includes/push-notification-checker.php'; ?>
```

## Integration Points

### In Add Listing Page
After successfully creating a listing, add:
```php
// Notify users about new listing
require_once __DIR__ . '/../includes/notify-new-listing.php';
notifyUsersOfNewListing($listing_id);
```

### In Navbar (Already Done)
```php
<?php include __DIR__ . '/../includes/push-notification-checker.php'; ?>
```

## Email Configuration

### Using PHP mail() (Default)
Works out of the box with XAMPP, but emails might go to spam.

### Using SMTP (Recommended for Production)
1. Install PHPMailer: `composer require phpmailer/phpmailer`
2. Update `EmailService.php`:
```php
private $use_smtp = true;
// Add SMTP credentials in sendViaSMTP() method
```

## Testing

### Test Welcome Email
1. Register a new user
2. Check `storage/logs/emails.log` for email log
3. Check your email inbox (might be in spam)

### Test Push Notifications
1. Go to Profile → Notifications
2. Click "Enable Browser Notifications"
3. Toggle "Instant Listing Alerts" ON
4. Create a new listing (or have someone else do it)
5. You should receive a browser notification

### Test Email Preferences
1. Go to Profile → Notifications
2. Toggle "New Listings" OFF
3. Save preferences
4. New listings won't send you emails anymore

## Email Templates
All emails use a professional template with:
- Glass Market branding
- Responsive design
- Call-to-action buttons
- Footer with links

## Troubleshooting

### Emails not sending
- Check `storage/logs/emails.log`
- Verify XAMPP has sendmail configured
- Consider using SMTP instead

### Push notifications not working
- Check browser permission (must be "Allow")
- Verify user has "push_new_listings" enabled
- Check browser console for errors
- Make sure `push_notifications` table exists

### User preferences not saving
- Check database has new columns (auto-created on first save)
- Verify form is submitting correctly
- Check error logs

## Future Enhancements
- [ ] Add message notifications
- [ ] Add favorite listing notifications
- [ ] Add weekly digest emails
- [ ] Add SMS notifications (Twilio integration)
- [ ] Add notification history page
- [ ] Add notification sound preferences
