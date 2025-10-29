-- Add image_path column to listings table
ALTER TABLE `listings` ADD COLUMN `image_path` VARCHAR(500) DEFAULT NULL AFTER `quality_notes`;

-- Create uploads directory structure (run manually via PHP or mkdir)
-- Directory: c:\xampp-3\htdocs\glass-market\public\uploads\listings\
