-- Add cover_image column to companies table
-- This allows companies to upload custom cover images

ALTER TABLE `companies` 
ADD COLUMN `cover_image` VARCHAR(500) NULL DEFAULT NULL AFTER `logo`;

-- Update existing companies to use fallback image path
-- UPDATE `companies` SET `cover_image` = 'uploads/default/fallback_company.png';
