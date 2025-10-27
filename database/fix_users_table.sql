-- Quick fix for users table
-- Copy and paste these commands into phpMyAdmin SQL tab

-- Add updated_at column
ALTER TABLE `users` ADD COLUMN `updated_at` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP AFTER `created_at`;

-- Add remember_token column  
ALTER TABLE `users` ADD COLUMN `remember_token` VARCHAR(100) NULL DEFAULT NULL AFTER `password`;
