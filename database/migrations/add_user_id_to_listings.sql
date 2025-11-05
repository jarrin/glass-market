-- Add user_id column to listings table to support personal listings
-- This allows users to create listings without a company

ALTER TABLE `listings` 
ADD COLUMN `user_id` bigint(20) DEFAULT NULL AFTER `company_id`,
ADD KEY `idx_listings_user` (`user_id`);

-- Add foreign key constraint
ALTER TABLE `listings`
ADD CONSTRAINT `fk_listings_user` 
FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

-- Update existing listings to set user_id based on company ownership
UPDATE listings l
INNER JOIN companies c ON l.company_id = c.id
SET l.user_id = c.owner_user_id
WHERE l.company_id IS NOT NULL;
