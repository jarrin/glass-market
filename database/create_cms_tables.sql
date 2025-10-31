-- CMS Tables for Page Content Management
-- Run this in phpMyAdmin to create the content management system

-- Table to store different pages
CREATE TABLE IF NOT EXISTS `pages` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `slug` varchar(100) NOT NULL COMMENT 'URL-friendly identifier (e.g., about-us, contact)',
  `title` varchar(255) NOT NULL COMMENT 'Page title',
  `meta_description` text DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table to store content sections for each page
CREATE TABLE IF NOT EXISTS `page_sections` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `page_id` bigint(20) NOT NULL,
  `section_key` varchar(100) NOT NULL COMMENT 'Unique identifier for this section (e.g., hero_title, mission_text)',
  `section_type` varchar(50) NOT NULL DEFAULT 'text' COMMENT 'text, textarea, image, html, url',
  `section_label` varchar(255) NOT NULL COMMENT 'Human-readable label for admin UI',
  `display_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `page_section_unique` (`page_id`, `section_key`),
  KEY `page_id` (`page_id`),
  CONSTRAINT `page_sections_ibfk_1` FOREIGN KEY (`page_id`) REFERENCES `pages` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table to store the actual content values
CREATE TABLE IF NOT EXISTS `page_content` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `section_id` bigint(20) NOT NULL,
  `content_value` longtext DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` bigint(20) DEFAULT NULL COMMENT 'User ID who last updated',
  PRIMARY KEY (`id`),
  UNIQUE KEY `section_id` (`section_id`),
  KEY `updated_by` (`updated_by`),
  CONSTRAINT `page_content_ibfk_1` FOREIGN KEY (`section_id`) REFERENCES `page_sections` (`id`) ON DELETE CASCADE,
  CONSTRAINT `page_content_ibfk_2` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default "About Us" page
INSERT INTO `pages` (`slug`, `title`, `meta_description`, `is_active`) VALUES
('about-us', 'About Us', 'Learn more about Glass Market and our mission', 1);

-- Get the page ID for about-us (will be 1 if fresh install)
SET @about_page_id = LAST_INSERT_ID();

-- Insert sections for About Us page
INSERT INTO `page_sections` (`page_id`, `section_key`, `section_type`, `section_label`, `display_order`) VALUES
(@about_page_id, 'hero_title', 'text', 'Hero Section - Main Title', 1),
(@about_page_id, 'hero_subtitle', 'textarea', 'Hero Section - Subtitle', 2),
(@about_page_id, 'mission_title', 'text', 'Mission Section - Title', 3),
(@about_page_id, 'mission_text', 'textarea', 'Mission Section - Description', 4),
(@about_page_id, 'vision_title', 'text', 'Vision Section - Title', 5),
(@about_page_id, 'vision_text', 'textarea', 'Vision Section - Description', 6),
(@about_page_id, 'values_title', 'text', 'Values Section - Title', 7),
(@about_page_id, 'values_text', 'textarea', 'Values Section - Description', 8),
(@about_page_id, 'team_title', 'text', 'Team Section - Title', 9),
(@about_page_id, 'team_text', 'textarea', 'Team Section - Description', 10);

-- Insert default content for About Us sections
INSERT INTO `page_content` (`section_id`, `content_value`) 
SELECT id, CASE section_key
    WHEN 'hero_title' THEN 'About Glass Market'
    WHEN 'hero_subtitle' THEN 'Connecting the global glass recycling industry with innovative marketplace solutions'
    WHEN 'mission_title' THEN 'Our Mission'
    WHEN 'mission_text' THEN 'Glass Market is dedicated to creating a sustainable future by facilitating the efficient exchange of recycled glass materials. We connect glass recycling plants, factories, and collection companies worldwide, making it easier to source and supply quality glass cullet.'
    WHEN 'vision_title' THEN 'Our Vision'
    WHEN 'vision_text' THEN 'We envision a world where glass recycling is seamless, transparent, and accessible to all stakeholders in the industry. By providing a centralized platform, we aim to reduce waste, lower costs, and support environmental sustainability.'
    WHEN 'values_title' THEN 'Our Values'
    WHEN 'values_text' THEN 'Sustainability: We prioritize environmental responsibility in everything we do.\nTransparency: We believe in open, honest communication and fair pricing.\nInnovation: We continuously improve our platform to serve our community better.\nQuality: We maintain high standards for all listings and transactions.'
    WHEN 'team_title' THEN 'Our Team'
    WHEN 'team_text' THEN 'Glass Market is built by a passionate team of industry experts, developers, and environmental advocates. We bring together decades of experience in glass recycling, supply chain management, and digital marketplace innovation.'
END
FROM `page_sections`
WHERE `page_id` = @about_page_id;

-- Create index for faster content retrieval
CREATE INDEX idx_page_slug ON pages(slug);
CREATE INDEX idx_section_order ON page_sections(page_id, display_order);
