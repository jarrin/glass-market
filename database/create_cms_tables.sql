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
(@about_page_id, 'hero_kicker', 'text', 'Hero Section - Kicker', 1),
(@about_page_id, 'hero_title', 'text', 'Hero Section - Main Title', 2),
(@about_page_id, 'hero_subtitle', 'textarea', 'Hero Section - Subtitle', 3),
(@about_page_id, 'hero_description', 'textarea', 'Hero Section - Description', 4),
(@about_page_id, 'hero_primary_label', 'text', 'Hero Section - Primary Button Label', 5),
(@about_page_id, 'hero_primary_url', 'text', 'Hero Section - Primary Button URL', 6),
(@about_page_id, 'hero_secondary_label', 'text', 'Hero Section - Secondary Button Label', 7),
(@about_page_id, 'hero_secondary_url', 'text', 'Hero Section - Secondary Button URL', 8),
(@about_page_id, 'stats_1_value', 'text', 'Stats Card 1 - Value', 9),
(@about_page_id, 'stats_1_label', 'text', 'Stats Card 1 - Label', 10),
(@about_page_id, 'stats_2_value', 'text', 'Stats Card 2 - Value', 11),
(@about_page_id, 'stats_2_label', 'text', 'Stats Card 2 - Label', 12),
(@about_page_id, 'stats_3_value', 'text', 'Stats Card 3 - Value', 13),
(@about_page_id, 'stats_3_label', 'text', 'Stats Card 3 - Label', 14),
(@about_page_id, 'stats_4_value', 'text', 'Stats Card 4 - Value', 15),
(@about_page_id, 'stats_4_label', 'text', 'Stats Card 4 - Label', 16),
(@about_page_id, 'mission_title', 'text', 'Mission Section - Title', 17),
(@about_page_id, 'mission_text', 'textarea', 'Mission Section - Description', 18),
(@about_page_id, 'vision_title', 'text', 'Vision Section - Title', 19),
(@about_page_id, 'vision_text', 'textarea', 'Vision Section - Description', 20),
(@about_page_id, 'values_title', 'text', 'Values Section - Title', 21),
(@about_page_id, 'values_intro', 'textarea', 'Values Section - Intro', 22),
(@about_page_id, 'values_item_1_title', 'text', 'Values Card 1 - Title', 23),
(@about_page_id, 'values_item_1_text', 'textarea', 'Values Card 1 - Description', 24),
(@about_page_id, 'values_item_2_title', 'text', 'Values Card 2 - Title', 25),
(@about_page_id, 'values_item_2_text', 'textarea', 'Values Card 2 - Description', 26),
(@about_page_id, 'values_item_3_title', 'text', 'Values Card 3 - Title', 27),
(@about_page_id, 'values_item_3_text', 'textarea', 'Values Card 3 - Description', 28),
(@about_page_id, 'team_title', 'text', 'Team Section - Title', 29),
(@about_page_id, 'team_text', 'textarea', 'Team Section - Description', 30),
(@about_page_id, 'cta_title', 'text', 'CTA Section - Title', 31),
(@about_page_id, 'cta_text', 'textarea', 'CTA Section - Description', 32),
(@about_page_id, 'cta_primary_label', 'text', 'CTA Section - Primary Button Label', 33),
(@about_page_id, 'cta_primary_url', 'text', 'CTA Section - Primary Button URL', 34),
(@about_page_id, 'cta_secondary_label', 'text', 'CTA Section - Secondary Button Label', 35),
(@about_page_id, 'cta_secondary_url', 'text', 'CTA Section - Secondary Button URL', 36);

-- Insert default content for About Us sections
INSERT INTO `page_content` (`section_id`, `content_value`) 
SELECT id, CASE section_key
  WHEN 'hero_kicker' THEN 'About Glass Market'
  WHEN 'hero_title' THEN 'Reimagining Glass Recycling for the Modern World'
  WHEN 'hero_subtitle' THEN 'We connect glass recyclers, factories, and collection partners with a marketplace built for clarity, trust, and speed.'
  WHEN 'hero_description' THEN 'From Rotterdam to Singapore, Glass Market keeps premium cullet moving. Discover reliable supply, verified partners, and transparent pricing in one curated platform.'
  WHEN 'hero_primary_label' THEN 'Explore Marketplace'
  WHEN 'hero_primary_url' THEN '/browse'
  WHEN 'hero_secondary_label' THEN ''
  WHEN 'hero_secondary_url' THEN ''
  WHEN 'stats_1_value' THEN '432K'
  WHEN 'stats_1_label' THEN 'Tons of glass traded'
  WHEN 'stats_2_value' THEN '68'
  WHEN 'stats_2_label' THEN 'Active partner locations'
  WHEN 'stats_3_value' THEN '24 hrs'
  WHEN 'stats_3_label' THEN 'Average listing approval'
  WHEN 'stats_4_value' THEN '98%'
  WHEN 'stats_4_label' THEN 'Fulfilment satisfaction rate'
  WHEN 'mission_title' THEN 'Our Mission'
  WHEN 'mission_text' THEN 'Glass Market accelerates the circular economy by helping recycling plants place high-grade cullet where it is needed most. We build tools that shorten lead times, improve quality assurance, and keep production lines supplied with confidence.'
  WHEN 'vision_title' THEN 'Our Vision'
  WHEN 'vision_text' THEN 'A fully traceable, data-rich glass ecosystem where every shard is recovered, certified, and returned to industry. By unifying stakeholders on a single platform, we make sustainability measurable and commercially viable.'
  WHEN 'values_title' THEN 'Principles We Operate By'
  WHEN 'values_intro' THEN 'We design technology and partnerships that make the glass value chain resilient. Every decision is guided by a shared responsibility to the planet and the people who keep materials in motion.'
  WHEN 'values_item_1_title' THEN 'Clarity First'
  WHEN 'values_item_1_text' THEN 'Live logistics, pricing transparency, and certified quality data keep buyers and sellers aligned from enquiry to delivery.'
  WHEN 'values_item_2_title' THEN 'Reliable Partnerships'
  WHEN 'values_item_2_text' THEN 'We vet every supplier and logistics partner to make sure glass arrives exactly as promised—no surprises, no delays.'
  WHEN 'values_item_3_title' THEN 'Sustainable Growth'
  WHEN 'values_item_3_text' THEN 'Our roadmap is focused on reducing waste, lowering emissions, and helping partners invest in greener operations.'
  WHEN 'team_title' THEN 'Dedicated Industry Specialists'
  WHEN 'team_text' THEN 'Our Amsterdam-based core team blends decades of glass recycling, supply chain, and product experience. We stay close to the yards, factories, and ports that rely on us every day.'
  WHEN 'cta_title' THEN 'Join the Circular Glass Movement'
  WHEN 'cta_text' THEN 'Whether you handle production cullet, collection streams, or manufacturing demand, Glass Market builds tools tailored to your business.'
  WHEN 'cta_primary_label' THEN 'Request a Demo'
  WHEN 'cta_primary_url' THEN '/demo'
  WHEN 'cta_secondary_label' THEN 'Talk to Operations'
  WHEN 'cta_secondary_url' THEN 'mailto:hello@glassmarket.com'
END
FROM `page_sections`
WHERE `page_id` = @about_page_id;

-- Create index for faster content retrieval
CREATE INDEX idx_page_slug ON pages(slug);
CREATE INDEX idx_section_order ON page_sections(page_id, display_order);
