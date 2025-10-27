-- Add missing columns to users table
-- Run this SQL in phpMyAdmin or MySQL command line
-- Note: These will give errors if columns already exist - that's okay, just means they're already there

-- Add updated_at column
ALTER TABLE `users` 
ADD COLUMN `updated_at` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP AFTER `created_at`;

-- Add remember_token column
ALTER TABLE `users` 
ADD COLUMN `remember_token` VARCHAR(100) NULL DEFAULT NULL AFTER `password`;

-- Verify the changes
DESCRIBE `users`;
