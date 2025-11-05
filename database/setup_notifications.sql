-- ================================================
-- Notification System Database Setup
-- Run this to add notification support to your database
-- ================================================

-- Add notification preference columns to users table
ALTER TABLE users ADD COLUMN IF NOT EXISTS notify_new_listings TINYINT(1) DEFAULT 1 COMMENT 'Email notification for new listings';
ALTER TABLE users ADD COLUMN IF NOT EXISTS notify_account_updates TINYINT(1) DEFAULT 1 COMMENT 'Email notification for account updates';
ALTER TABLE users ADD COLUMN IF NOT EXISTS notify_newsletter TINYINT(1) DEFAULT 0 COMMENT 'Receive newsletter emails';
ALTER TABLE users ADD COLUMN IF NOT EXISTS push_new_listings TINYINT(1) DEFAULT 0 COMMENT 'Push notification for new listings';
ALTER TABLE users ADD COLUMN IF NOT EXISTS push_messages TINYINT(1) DEFAULT 0 COMMENT 'Push notification for messages';

-- Create push_notifications table
CREATE TABLE IF NOT EXISTS push_notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    body TEXT,
    url VARCHAR(500),
    is_read TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_user_id (user_id),
    INDEX idx_is_read (is_read),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ================================================
-- Verification Queries
-- ================================================

-- Check if columns were added
SELECT 
    COLUMN_NAME, 
    DATA_TYPE, 
    COLUMN_DEFAULT, 
    IS_NULLABLE
FROM 
    INFORMATION_SCHEMA.COLUMNS
WHERE 
    TABLE_SCHEMA = 'glass_market' 
    AND TABLE_NAME = 'users'
    AND COLUMN_NAME LIKE 'notify_%' OR COLUMN_NAME LIKE 'push_%';

-- Check push_notifications table structure
DESCRIBE push_notifications;

-- ================================================
-- Test Data (Optional)
-- ================================================

-- Enable notifications for user ID 1
UPDATE users SET 
    notify_new_listings = 1,
    notify_account_updates = 1,
    push_new_listings = 1
WHERE id = 1;

-- Insert a test push notification
-- INSERT INTO push_notifications (user_id, title, body, url) 
-- VALUES (1, 'Test Notification', 'This is a test push notification', 'http://localhost/glass-market');

-- ================================================
-- Cleanup Queries (if needed)
-- ================================================

-- Remove old notifications (older than 7 days)
-- DELETE FROM push_notifications WHERE created_at < DATE_SUB(NOW(), INTERVAL 7 DAY);

-- Reset all notification preferences
-- UPDATE users SET 
--     notify_new_listings = 1,
--     notify_account_updates = 1,
--     notify_newsletter = 0,
--     push_new_listings = 0,
--     push_messages = 0;
