-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 03, 2025 at 11:10 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `glass_market`
--

-- --------------------------------------------------------

--
-- Table structure for table `broadcasts`
--

CREATE TABLE `broadcasts` (
  `id` bigint(20) NOT NULL,
  `listing_id` bigint(20) DEFAULT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `body` text DEFAULT NULL,
  `from_email` varchar(255) DEFAULT NULL,
  `reply_to_email` varchar(255) DEFAULT NULL,
  `recipients_count` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `sent_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `capacities`
--

CREATE TABLE `capacities` (
  `id` bigint(20) NOT NULL,
  `location_id` bigint(20) DEFAULT NULL,
  `date_recorded` date DEFAULT curdate(),
  `weekly_capacity_tons` decimal(12,2) DEFAULT NULL,
  `notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `capacities`
--

INSERT INTO `capacities` (`id`, `location_id`, `date_recorded`, `weekly_capacity_tons`, `notes`) VALUES
(1, 1, '2025-10-01', 500.00, 'Normal capacity'),
(2, 2, '2025-10-01', 300.00, 'Normal capacity'),
(3, 3, '2025-10-01', 150.00, 'Normal capacity');

-- --------------------------------------------------------

--
-- Table structure for table `companies`
--

CREATE TABLE `companies` (
  `id` bigint(20) NOT NULL,
  `name` varchar(255) NOT NULL,
  `company_type` varchar(50) NOT NULL,
  `website` varchar(255) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `companies`
--

INSERT INTO `companies` (`id`, `name`, `company_type`, `website`, `phone`, `created_at`) VALUES
(1, 'GlassRecycle BV', 'Glass Recycle Plant', 'https://grb.example', '+31 10 123 4567', '2025-10-15 11:48:18'),
(2, 'GlassFactory NL', 'Glass Factory', 'https://gfnl.example', '+31 20 987 6543', '2025-10-15 11:48:18'),
(3, 'CollectionCo BE', 'Collection Company', 'https://ccbe.example', '+32 2 555 1234', '2025-10-15 11:48:18'),
(4, 'testaccount', 'Other', NULL, NULL, '2025-10-31 10:14:41');

-- --------------------------------------------------------

--
-- Table structure for table `company_types`
--

CREATE TABLE `company_types` (
  `type` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `company_types`
--

INSERT INTO `company_types` (`type`) VALUES
('Collection Company'),
('Glass Factory'),
('Glass Recycle Plant'),
('Other'),
('Trader');

-- --------------------------------------------------------

--
-- Table structure for table `contracts`
--

CREATE TABLE `contracts` (
  `id` bigint(20) NOT NULL,
  `grp_company_id` bigint(20) DEFAULT NULL,
  `gf_company_id` bigint(20) DEFAULT NULL,
  `grp_location_id` bigint(20) DEFAULT NULL,
  `gf_location_id` bigint(20) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `weekly_quantity_tons` decimal(12,2) DEFAULT NULL,
  `price_text` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `contracts`
--

INSERT INTO `contracts` (`id`, `grp_company_id`, `gf_company_id`, `grp_location_id`, `gf_location_id`, `start_date`, `end_date`, `weekly_quantity_tons`, `price_text`, `created_at`) VALUES
(1, 1, 2, 1, 2, '2025-01-01', '2025-12-31', 200.00, '€115/ton CIF', '2025-10-15 11:48:18');

-- --------------------------------------------------------

--
-- Table structure for table `currency_iso`
--

CREATE TABLE `currency_iso` (
  `currency` varchar(3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `currency_iso`
--

INSERT INTO `currency_iso` (`currency`) VALUES
('CNY'),
('EUR'),
('GBP'),
('JPY'),
('USD');

-- --------------------------------------------------------

--
-- Table structure for table `listings`
--

CREATE TABLE `listings` (
  `id` bigint(20) NOT NULL,
  `location_id` bigint(20) DEFAULT NULL,
  `company_id` bigint(20) DEFAULT NULL,
  `side` varchar(10) NOT NULL,
  `glass_type` varchar(255) NOT NULL,
  `glass_type_other` varchar(255) DEFAULT NULL,
  `quantity_tons` decimal(12,2) DEFAULT NULL,
  `quantity_note` varchar(255) DEFAULT NULL,
  `recycled` varchar(20) DEFAULT 'unknown',
  `tested` varchar(20) DEFAULT 'unknown',
  `storage_location` varchar(255) DEFAULT NULL,
  `price_text` varchar(255) DEFAULT NULL,
  `currency` varchar(3) DEFAULT 'EUR',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `valid_until` date DEFAULT NULL,
  `published` tinyint(1) DEFAULT 1,
  `quality_notes` text DEFAULT NULL,
  `image_path` varchar(500) DEFAULT NULL,
  `accepted_by_contract` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `listings`
--

INSERT INTO `listings` (`id`, `location_id`, `company_id`, `side`, `glass_type`, `glass_type_other`, `quantity_tons`, `quantity_note`, `recycled`, `tested`, `storage_location`, `price_text`, `currency`, `created_at`, `valid_until`, `published`, `quality_notes`, `image_path`, `accepted_by_contract`) VALUES
(1, 1, 1, 'WTS', 'Clear Cullet', NULL, 250.00, NULL, 'recycled', 'tested', 'Rotterdam yard', '€120/ton CIF', 'EUR', '2025-10-15 11:48:18', NULL, 1, 'Low Fe content', NULL, 0),
(2, 2, 2, 'WTB', 'Brown Cullet', NULL, 150.00, NULL, 'recycled', 'tested', 'Amsterdam warehouse', '€110/ton CIF', 'EUR', '2025-10-15 11:48:18', NULL, 1, 'High purity required', NULL, 0),
(3, 3, 3, 'WTS', 'Mixed Cullet', NULL, 100.00, NULL, 'not_recycled', 'untested', 'Brussels yard', '€80/ton EXW', 'EUR', '2025-10-15 11:48:18', NULL, 1, 'Unsorted mix', NULL, 0),
(4, NULL, 4, 'WTS', 'Brown Glass', NULL, 432545.00, 'asdfasdf!', 'unknown', 'unknown', NULL, '', 'EUR', '2025-10-31 10:16:48', NULL, 1, 'rtwert', 'uploads/listings/listing_1761905808_69048c908d4b3.jpeg', 0);

-- --------------------------------------------------------

--
-- Table structure for table `listing_sides`
--

CREATE TABLE `listing_sides` (
  `side` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `listing_sides`
--

INSERT INTO `listing_sides` (`side`) VALUES
('WTB'),
('WTS');

-- --------------------------------------------------------

--
-- Table structure for table `locations`
--

CREATE TABLE `locations` (
  `id` bigint(20) NOT NULL,
  `company_id` bigint(20) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `address_line1` varchar(255) DEFAULT NULL,
  `address_line2` varchar(255) DEFAULT NULL,
  `postal_code` varchar(20) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `region` varchar(100) DEFAULT NULL,
  `country_code` char(2) DEFAULT NULL,
  `contact_email_broadcast` varchar(255) DEFAULT NULL,
  `contact_email_personal` varchar(255) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `locations`
--

INSERT INTO `locations` (`id`, `company_id`, `name`, `address_line1`, `address_line2`, `postal_code`, `city`, `region`, `country_code`, `contact_email_broadcast`, `contact_email_personal`, `phone`, `created_at`) VALUES
(1, 1, 'Rotterdam Plant', 'Harbour 1', NULL, '3011AA', 'Rotterdam', NULL, 'NL', 'broadcast@grb.example', 'sales@grb.example', NULL, '2025-10-15 11:48:18'),
(2, 2, 'Amsterdam Factory', 'Factory Street 10', NULL, '1000AA', 'Amsterdam', NULL, 'NL', 'broadcast@gfnl.example', 'person@gfnl.example', NULL, '2025-10-15 11:48:18'),
(3, 3, 'Brussels Collection', 'Glass Road 5', NULL, '1000', 'Brussels', NULL, 'BE', 'broadcast@ccbe.example', 'person@ccbe.example', NULL, '2025-10-15 11:48:18');

-- --------------------------------------------------------

--
-- Table structure for table `maintenance_events`
--

CREATE TABLE `maintenance_events` (
  `id` bigint(20) NOT NULL,
  `location_id` bigint(20) DEFAULT NULL,
  `start_datetime` datetime DEFAULT NULL,
  `end_datetime` datetime DEFAULT NULL,
  `reason` text DEFAULT NULL,
  `planned` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `maintenance_events`
--

INSERT INTO `maintenance_events` (`id`, `location_id`, `start_datetime`, `end_datetime`, `reason`, `planned`, `created_at`) VALUES
(1, 1, '2025-12-24 08:00:00', '2025-12-26 18:00:00', 'Christmas shutdown', 1, '2025-10-15 11:48:18');

-- --------------------------------------------------------

--
-- Table structure for table `mollie_payments`
--

CREATE TABLE `mollie_payments` (
  `id` bigint(20) NOT NULL,
  `user_id` bigint(20) NOT NULL,
  `payment_id` varchar(255) NOT NULL COMMENT 'Mollie payment ID',
  `amount` decimal(10,2) NOT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'open' COMMENT 'open, paid, failed, canceled, expired',
  `months` int(11) NOT NULL DEFAULT 1 COMMENT 'Number of months purchased',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `paid_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `mollie_payments`
--

INSERT INTO `mollie_payments` (`id`, `user_id`, `payment_id`, `amount`, `status`, `months`, `created_at`, `updated_at`, `paid_at`) VALUES
(1, 10, 'tr_Uw2jPryC4LCoRCeP82LGJ', 9.99, 'open', 1, '2025-10-31 09:09:16', NULL, NULL),
(2, 10, 'tr_8gyXjjsWQ3Zhn2XaC2LGJ', 9.99, 'open', 1, '2025-10-31 09:10:00', NULL, NULL),
(3, 10, 'tr_L2Fa4kHzTS6UtpynC2LGJ', 9.99, 'paid', 1, '2025-10-31 09:10:02', '2025-10-31 09:22:15', '2025-10-31 09:22:15'),
(4, 11, 'tr_HoaNUqcvAgr6cSzER5LGJ', 9.99, 'paid', 1, '2025-10-31 09:41:27', '2025-10-31 09:42:51', '2025-10-31 09:42:51');

-- --------------------------------------------------------

--
-- Table structure for table `pages`
--

CREATE TABLE `pages` (
  `id` bigint(20) NOT NULL,
  `slug` varchar(100) NOT NULL COMMENT 'URL-friendly identifier (e.g., about-us, contact)',
  `title` varchar(255) NOT NULL COMMENT 'Page title',
  `meta_description` text DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pages`
--

INSERT INTO `pages` (`id`, `slug`, `title`, `meta_description`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'about-us', 'About Us', 'Learn more about Glass Market and our mission', 1, '2025-10-31 10:45:31', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `page_content`
--

CREATE TABLE `page_content` (
  `id` bigint(20) NOT NULL,
  `section_id` bigint(20) NOT NULL,
  `content_value` longtext DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` bigint(20) DEFAULT NULL COMMENT 'User ID who last updated'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `page_content`
--

INSERT INTO `page_content` (`id`, `section_id`, `content_value`, `updated_at`, `updated_by`) VALUES
(1, 2, 'Connecting the global glass recycling industry with innovative marketplace solutions', '2025-10-31 11:21:43', 4),
(2, 1, 'About Glass Market!', '2025-10-31 11:21:43', 4),
(3, 4, 'Glass Market is dedicated to creating a sustainable future by facilitating the efficient exchange of recycled glass materials. We connect glass recycling plants, factories, and collection companies worldwide, making it easier to source and supply quality glass cullet.', '2025-10-31 11:21:43', 4),
(4, 3, 'Our Mission', '2025-10-31 11:21:43', 4),
(5, 10, 'Glass Market is built by a passionate team of industry experts, developers, and environmental advocates. We bring together decades of experience in glass recycling, supply chain management, and digital marketplace innovation.', '2025-10-31 11:21:43', 4),
(6, 9, 'Our Team', '2025-10-31 11:21:43', 4),
(7, 8, 'Sustainability: We prioritize environmental responsibility in everything we do.\r\nTransparency: We believe in open, honest communication and fair pricing.\r\nInnovation: We continuously improve our platform to serve our community better.\r\nQuality: We maintain high standards for all listings and transactions.', '2025-10-31 11:21:43', 4),
(8, 7, 'Our Values', '2025-10-31 11:21:43', 4),
(9, 6, 'We envision a world where glass recycling is seamless, transparent, and accessible to all stakeholders in the industry. By providing a centralized platform, we aim to reduce waste, lower costs, and support environmental sustainability.', '2025-10-31 11:21:43', 4),
(10, 5, 'Our Vision', '2025-10-31 11:21:43', 4),
(16, 34, 'Request a Demo', '2025-10-31 11:21:43', 4),
(17, 35, '/demo', '2025-10-31 11:21:43', 4),
(18, 36, 'Talk to Operations', '2025-10-31 11:21:43', 4),
(19, 37, 'mailto:hello@glassmarket.com', '2025-10-31 11:21:43', 4),
(20, 33, 'Whether you handle production cullet...', '2025-10-31 11:21:43', 4),
(21, 32, 'Join the Circular Glass Movement', '2025-10-31 11:21:43', 4),
(22, 12, 'From Rotterdam to Singapore, Glass Market keeps premium cullet moving...', '2025-10-31 11:21:43', 4),
(23, 11, 'About Glass Market', '2025-10-31 11:21:43', 4),
(24, 13, 'Explore Marketplace', '2025-10-31 11:21:43', 4),
(25, 14, '/glass-market/resources/views/browse.php', '2025-10-31 11:21:43', 4),
(28, 18, 'Tons of glass traded', '2025-10-31 11:21:43', 4),
(29, 17, '432K', '2025-10-31 11:21:43', 4),
(30, 20, 'Active partner locations', '2025-10-31 11:21:43', 4),
(31, 19, '68', '2025-10-31 11:21:43', 4),
(32, 22, 'Average listing approval', '2025-10-31 11:21:43', 4),
(33, 21, '24 hrs', '2025-10-31 11:21:43', 4),
(34, 24, 'Fulfilment satisfaction rate', '2025-10-31 11:21:43', 4),
(35, 23, '98%', '2025-10-31 11:21:43', 4),
(36, 25, 'We design technology and partnerships...', '2025-10-31 11:21:43', 4),
(37, 27, 'Live logistics, pricing transparency...', '2025-10-31 11:21:43', 4),
(38, 26, 'Clarity First', '2025-10-31 11:21:43', 4),
(39, 29, 'We vet every supplier and logistics partner...', '2025-10-31 11:21:43', 4),
(40, 28, 'Reliable Partnerships', '2025-10-31 11:21:43', 4),
(41, 31, 'Our roadmap is focused on reducing waste...', '2025-10-31 11:21:43', 4),
(42, 30, 'Sustainable Growth', '2025-10-31 11:21:43', 4);

-- --------------------------------------------------------

--
-- Table structure for table `page_sections`
--

CREATE TABLE `page_sections` (
  `id` bigint(20) NOT NULL,
  `page_id` bigint(20) NOT NULL,
  `section_key` varchar(100) NOT NULL COMMENT 'Unique identifier for this section (e.g., hero_title, mission_text)',
  `section_type` varchar(50) NOT NULL DEFAULT 'text' COMMENT 'text, textarea, image, html, url',
  `section_label` varchar(255) NOT NULL COMMENT 'Human-readable label for admin UI',
  `display_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `page_sections`
--

INSERT INTO `page_sections` (`id`, `page_id`, `section_key`, `section_type`, `section_label`, `display_order`, `created_at`) VALUES
(1, 1, 'hero_title', 'text', 'Hero Section - Main Title', 2, '2025-10-31 10:45:32'),
(2, 1, 'hero_subtitle', 'textarea', 'Hero Section - Subtitle', 3, '2025-10-31 10:45:32'),
(3, 1, 'mission_title', 'text', 'Mission Section - Title', 15, '2025-10-31 10:45:32'),
(4, 1, 'mission_text', 'textarea', 'Mission Section - Description', 16, '2025-10-31 10:45:32'),
(5, 1, 'vision_title', 'text', 'Vision Section - Title', 17, '2025-10-31 10:45:32'),
(6, 1, 'vision_text', 'textarea', 'Vision Section - Description', 18, '2025-10-31 10:45:32'),
(7, 1, 'values_title', 'text', 'Values Section - Title', 19, '2025-10-31 10:45:32'),
(8, 1, 'values_text', 'textarea', 'Values Section - Description', 8, '2025-10-31 10:45:32'),
(9, 1, 'team_title', 'text', 'Team Section - Title', 27, '2025-10-31 10:45:32'),
(10, 1, 'team_text', 'textarea', 'Team Section - Description', 28, '2025-10-31 10:45:32'),
(11, 1, 'hero_kicker', 'text', 'Hero Section - Kicker', 1, '2025-10-31 11:04:37'),
(12, 1, 'hero_description', 'textarea', 'Hero Section - Description', 4, '2025-10-31 11:04:37'),
(13, 1, 'hero_primary_label', 'text', 'Hero Section - Primary Button Label', 5, '2025-10-31 11:04:37'),
(14, 1, 'hero_primary_url', 'text', 'Hero Section - Primary Button URL', 6, '2025-10-31 11:04:37'),
(17, 1, 'stats_1_value', 'text', 'Stats Card 1 - Value', 7, '2025-10-31 11:04:37'),
(18, 1, 'stats_1_label', 'text', 'Stats Card 1 - Label', 8, '2025-10-31 11:04:37'),
(19, 1, 'stats_2_value', 'text', 'Stats Card 2 - Value', 9, '2025-10-31 11:04:37'),
(20, 1, 'stats_2_label', 'text', 'Stats Card 2 - Label', 10, '2025-10-31 11:04:37'),
(21, 1, 'stats_3_value', 'text', 'Stats Card 3 - Value', 11, '2025-10-31 11:04:37'),
(22, 1, 'stats_3_label', 'text', 'Stats Card 3 - Label', 12, '2025-10-31 11:04:37'),
(23, 1, 'stats_4_value', 'text', 'Stats Card 4 - Value', 13, '2025-10-31 11:04:37'),
(24, 1, 'stats_4_label', 'text', 'Stats Card 4 - Label', 14, '2025-10-31 11:04:37'),
(25, 1, 'values_intro', 'textarea', 'Values Section - Intro', 20, '2025-10-31 11:04:37'),
(26, 1, 'values_item_1_title', 'text', 'Values Card 1 - Title', 21, '2025-10-31 11:04:37'),
(27, 1, 'values_item_1_text', 'textarea', 'Values Card 1 - Description', 22, '2025-10-31 11:04:37'),
(28, 1, 'values_item_2_title', 'text', 'Values Card 2 - Title', 23, '2025-10-31 11:04:37'),
(29, 1, 'values_item_2_text', 'textarea', 'Values Card 2 - Description', 24, '2025-10-31 11:04:37'),
(30, 1, 'values_item_3_title', 'text', 'Values Card 3 - Title', 25, '2025-10-31 11:04:37'),
(31, 1, 'values_item_3_text', 'textarea', 'Values Card 3 - Description', 26, '2025-10-31 11:04:37'),
(32, 1, 'cta_title', 'text', 'CTA Section - Title', 29, '2025-10-31 11:04:37'),
(33, 1, 'cta_text', 'textarea', 'CTA Section - Description', 30, '2025-10-31 11:04:37'),
(34, 1, 'cta_primary_label', 'text', 'CTA Section - Primary Button Label', 31, '2025-10-31 11:04:37'),
(35, 1, 'cta_primary_url', 'text', 'CTA Section - Primary Button URL', 32, '2025-10-31 11:04:37'),
(36, 1, 'cta_secondary_label', 'text', 'CTA Section - Secondary Button Label', 33, '2025-10-31 11:04:37'),
(37, 1, 'cta_secondary_url', 'text', 'CTA Section - Secondary Button URL', 34, '2025-10-31 11:04:37');

-- --------------------------------------------------------

--
-- Table structure for table `payment_cards`
--

CREATE TABLE `payment_cards` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `card_last4` varchar(4) NOT NULL,
  `card_brand` varchar(20) DEFAULT NULL,
  `card_holder` varchar(255) NOT NULL,
  `card_expiry` varchar(7) NOT NULL,
  `is_default` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `payment_cards`
--

INSERT INTO `payment_cards` (`id`, `user_id`, `card_last4`, `card_brand`, `card_holder`, `card_expiry`, `is_default`, `created_at`) VALUES
(1, 5, '3456', 'Visa', 'Cornelis wim poort', '05/50', 1, '2025-10-28 13:12:55');

-- --------------------------------------------------------

--
-- Table structure for table `payment_errors`
--

CREATE TABLE `payment_errors` (
  `id` bigint(20) NOT NULL,
  `user_id` bigint(20) DEFAULT NULL,
  `plan` varchar(50) DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `error_message` text NOT NULL,
  `error_context` text DEFAULT NULL COMMENT 'JSON with request details',
  `payment_id` varchar(255) DEFAULT NULL COMMENT 'Mollie payment ID if created',
  `request_data` text DEFAULT NULL COMMENT 'JSON of the request that caused error',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `recycled_statuses`
--

CREATE TABLE `recycled_statuses` (
  `status` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `recycled_statuses`
--

INSERT INTO `recycled_statuses` (`status`) VALUES
('not_recycled'),
('recycled'),
('unknown');

-- --------------------------------------------------------

--
-- Table structure for table `subscriptions`
--

CREATE TABLE `subscriptions` (
  `id` bigint(20) NOT NULL,
  `location_id` bigint(20) NOT NULL,
  `start_date` date NOT NULL,
  `duration_years` int(11) NOT NULL DEFAULT 1,
  `active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `subscriptions`
--

INSERT INTO `subscriptions` (`id`, `location_id`, `start_date`, `duration_years`, `active`, `created_at`) VALUES
(1, 1, '2025-01-01', 1, 1, '2025-10-15 11:48:18'),
(2, 2, '2025-02-01', 1, 1, '2025-10-15 11:48:18'),
(3, 3, '2025-03-01', 1, 1, '2025-10-15 11:48:18');

-- --------------------------------------------------------

--
-- Stand-in structure for view `subscription_expiry`
-- (See below for the actual view)
--
CREATE TABLE `subscription_expiry` (
`id` bigint(20)
,`location_id` bigint(20)
,`start_date` date
,`duration_years` int(11)
,`active` tinyint(1)
,`created_at` timestamp
,`expiry_date` date
,`paid_from_date` date
);

-- --------------------------------------------------------

--
-- Table structure for table `tested_statuses`
--

CREATE TABLE `tested_statuses` (
  `status` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tested_statuses`
--

INSERT INTO `tested_statuses` (`status`) VALUES
('tested'),
('unknown'),
('untested');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) NOT NULL,
  `company_id` bigint(20) DEFAULT NULL,
  `created_by` bigint(20) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `avatar` varchar(500) DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `company_name` varchar(255) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `roles` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`roles`)),
  `is_admin` tinyint(1) DEFAULT 0,
  `is_approved` tinyint(1) DEFAULT 0,
  `approved_at` timestamp NULL DEFAULT NULL,
  `approved_by` bigint(20) DEFAULT NULL,
  `last_login` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `company_id`, `created_by`, `email`, `avatar`, `email_verified_at`, `password`, `remember_token`, `name`, `company_name`, `phone`, `roles`, `is_admin`, `is_approved`, `approved_at`, `approved_by`, `last_login`, `created_at`, `updated_at`) VALUES
(4, NULL, NULL, 'admin@glassmarket.com', NULL, '2025-10-27 13:08:53', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, 'Admin', NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, '2025-10-27 12:29:44', '2025-10-29 14:40:57'),
(5, NULL, NULL, 'colinpoort@hotmail.com', '/glass-market/public/uploads/avatars/avatar_5_1761660214.jpg', '2025-10-27 13:14:36', '$2y$10$3le7iqImsFG85PwGuK60i.KcZpRP0wxk9MoHF3iBFXjpRx3oaJxpq', '30804072962d6ccea488d67d676f56a61fda774d49489c2114982b317eef78c9', 'Cornelis Wim Poort', 'CP Company', NULL, NULL, 0, 0, NULL, NULL, NULL, '2025-10-27 13:13:58', '2025-10-28 14:03:34'),
(6, NULL, NULL, 'gijs@gmail.com', NULL, '2025-10-27 13:33:45', '$2y$10$qiL225fIXPba/gq8Z/mZwOGOaXBiYU8lmIjQzBMy4vn6mZ4X0fWoa', NULL, 'Gijsje Radijsje', NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, '2025-10-27 13:33:06', '2025-10-27 13:33:45'),
(7, NULL, NULL, 'Kaj@gmail.com', NULL, '2025-10-27 14:04:47', '$2y$10$e3IsWujghleCxhlVwmlOwOph2Ijlq3gOfOMc8EcTxIUJVkZL6wpdq', NULL, 'Kaj', NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, '2025-10-27 13:56:22', '2025-10-27 14:04:47'),
(9, NULL, NULL, 'colinpoort12@hotmail.com', NULL, NULL, '$2y$10$5LuxQblwc2OqwTw7tzQU7eDA3g1QV4L/.3rn6XsQSn5301dSLpcQ.', NULL, 'Colin', NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, '2025-10-28 14:15:57', '2025-10-29 13:40:57'),
(10, NULL, NULL, 'test_sub@testsub.com', NULL, '2025-10-29 14:07:18', '$2y$10$qYcQxm8kHYBDjXc2Skh05erADJ5uxwub0DOHWojfcgYLEVfiKHwOi', NULL, 'test_sub', 'test_sub', NULL, NULL, 0, 0, NULL, NULL, NULL, '2025-10-29 14:06:07', '2025-10-29 14:07:18'),
(11, 4, NULL, 'testaccount@gmail.com', '/glass-market/public/uploads/avatars/avatar_11_1761905779.jpg', '2025-10-31 09:40:18', '$2y$10$9SuQyKcOKfwc5v5Nxep.tOeqwCztfV3ikKI/cQHFRq2Sx.9IHj.h.', NULL, 'testaccount', 'testaccount', NULL, NULL, 0, 0, NULL, NULL, NULL, '2025-10-31 09:39:51', '2025-10-31 10:16:19');

-- --------------------------------------------------------

--
-- Table structure for table `user_emails`
--

CREATE TABLE `user_emails` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `email_address` varchar(255) NOT NULL,
  `email_type` varchar(50) NOT NULL,
  `email_label` varchar(100) DEFAULT NULL,
  `is_verified` tinyint(1) DEFAULT 0,
  `is_primary` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_emails`
--

INSERT INTO `user_emails` (`id`, `user_id`, `email_address`, `email_type`, `email_label`, `is_verified`, `is_primary`, `created_at`) VALUES
(2, 5, 'CpNotif@hotmail.com', 'notifications', 'Notifications email', 1, 0, '2025-10-28 13:21:02');

-- --------------------------------------------------------

--
-- Table structure for table `user_locations`
--

CREATE TABLE `user_locations` (
  `user_id` bigint(20) NOT NULL,
  `location_id` bigint(20) NOT NULL,
  `can_edit` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_subscriptions`
--

CREATE TABLE `user_subscriptions` (
  `id` bigint(20) NOT NULL,
  `user_id` bigint(20) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `is_trial` tinyint(1) DEFAULT 1,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_subscriptions`
--

INSERT INTO `user_subscriptions` (`id`, `user_id`, `start_date`, `end_date`, `is_trial`, `is_active`, `created_at`, `updated_at`) VALUES
(5, 10, '2025-10-31', '2025-12-01', 0, 1, '2025-10-31 09:22:15', NULL),
(7, 11, '2025-10-31', '2025-12-01', 0, 1, '2025-10-31 09:42:51', NULL);

-- --------------------------------------------------------

--
-- Structure for view `subscription_expiry`
--
DROP TABLE IF EXISTS `subscription_expiry`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `subscription_expiry`  AS SELECT `s`.`id` AS `id`, `s`.`location_id` AS `location_id`, `s`.`start_date` AS `start_date`, `s`.`duration_years` AS `duration_years`, `s`.`active` AS `active`, `s`.`created_at` AS `created_at`, `s`.`start_date`+ interval 3 month + interval `s`.`duration_years` year AS `expiry_date`, `s`.`start_date`+ interval 3 month AS `paid_from_date` FROM `subscriptions` AS `s` ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `broadcasts`
--
ALTER TABLE `broadcasts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `listing_id` (`listing_id`);

--
-- Indexes for table `capacities`
--
ALTER TABLE `capacities`
  ADD PRIMARY KEY (`id`),
  ADD KEY `location_id` (`location_id`);

--
-- Indexes for table `companies`
--
ALTER TABLE `companies`
  ADD PRIMARY KEY (`id`),
  ADD KEY `company_type` (`company_type`);

--
-- Indexes for table `company_types`
--
ALTER TABLE `company_types`
  ADD PRIMARY KEY (`type`);

--
-- Indexes for table `contracts`
--
ALTER TABLE `contracts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `grp_company_id` (`grp_company_id`),
  ADD KEY `gf_company_id` (`gf_company_id`),
  ADD KEY `grp_location_id` (`grp_location_id`),
  ADD KEY `gf_location_id` (`gf_location_id`);

--
-- Indexes for table `currency_iso`
--
ALTER TABLE `currency_iso`
  ADD PRIMARY KEY (`currency`);

--
-- Indexes for table `listings`
--
ALTER TABLE `listings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `location_id` (`location_id`),
  ADD KEY `company_id` (`company_id`),
  ADD KEY `side` (`side`),
  ADD KEY `recycled` (`recycled`),
  ADD KEY `tested` (`tested`),
  ADD KEY `currency` (`currency`);
ALTER TABLE `listings` ADD FULLTEXT KEY `ft_search` (`glass_type`,`glass_type_other`,`storage_location`,`price_text`,`quality_notes`);

--
-- Indexes for table `listing_sides`
--
ALTER TABLE `listing_sides`
  ADD PRIMARY KEY (`side`);

--
-- Indexes for table `locations`
--
ALTER TABLE `locations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `company_id` (`company_id`);

--
-- Indexes for table `maintenance_events`
--
ALTER TABLE `maintenance_events`
  ADD PRIMARY KEY (`id`),
  ADD KEY `location_id` (`location_id`);

--
-- Indexes for table `mollie_payments`
--
ALTER TABLE `mollie_payments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `payment_id` (`payment_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `status` (`status`);

--
-- Indexes for table `pages`
--
ALTER TABLE `pages`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `idx_page_slug` (`slug`);

--
-- Indexes for table `page_content`
--
ALTER TABLE `page_content`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `section_id` (`section_id`),
  ADD KEY `updated_by` (`updated_by`);

--
-- Indexes for table `page_sections`
--
ALTER TABLE `page_sections`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `page_section_unique` (`page_id`,`section_key`),
  ADD KEY `page_id` (`page_id`),
  ADD KEY `idx_section_order` (`page_id`,`display_order`);

--
-- Indexes for table `payment_cards`
--
ALTER TABLE `payment_cards`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `payment_errors`
--
ALTER TABLE `payment_errors`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `created_at` (`created_at`);

--
-- Indexes for table `recycled_statuses`
--
ALTER TABLE `recycled_statuses`
  ADD PRIMARY KEY (`status`);

--
-- Indexes for table `subscriptions`
--
ALTER TABLE `subscriptions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `location_id` (`location_id`);

--
-- Indexes for table `tested_statuses`
--
ALTER TABLE `tested_statuses`
  ADD PRIMARY KEY (`status`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `company_id` (`company_id`);

--
-- Indexes for table `user_emails`
--
ALTER TABLE `user_emails`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_locations`
--
ALTER TABLE `user_locations`
  ADD PRIMARY KEY (`user_id`,`location_id`),
  ADD KEY `location_id` (`location_id`);

--
-- Indexes for table `user_subscriptions`
--
ALTER TABLE `user_subscriptions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `broadcasts`
--
ALTER TABLE `broadcasts`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `capacities`
--
ALTER TABLE `capacities`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `companies`
--
ALTER TABLE `companies`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `contracts`
--
ALTER TABLE `contracts`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `listings`
--
ALTER TABLE `listings`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `locations`
--
ALTER TABLE `locations`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `maintenance_events`
--
ALTER TABLE `maintenance_events`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `mollie_payments`
--
ALTER TABLE `mollie_payments`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `pages`
--
ALTER TABLE `pages`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `page_content`
--
ALTER TABLE `page_content`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT for table `page_sections`
--
ALTER TABLE `page_sections`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `payment_cards`
--
ALTER TABLE `payment_cards`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `payment_errors`
--
ALTER TABLE `payment_errors`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `subscriptions`
--
ALTER TABLE `subscriptions`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `user_emails`
--
ALTER TABLE `user_emails`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `user_subscriptions`
--
ALTER TABLE `user_subscriptions`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `broadcasts`
--
ALTER TABLE `broadcasts`
  ADD CONSTRAINT `broadcasts_ibfk_1` FOREIGN KEY (`listing_id`) REFERENCES `listings` (`id`);

--
-- Constraints for table `capacities`
--
ALTER TABLE `capacities`
  ADD CONSTRAINT `capacities_ibfk_1` FOREIGN KEY (`location_id`) REFERENCES `locations` (`id`);

--
-- Constraints for table `companies`
--
ALTER TABLE `companies`
  ADD CONSTRAINT `companies_ibfk_1` FOREIGN KEY (`company_type`) REFERENCES `company_types` (`type`);

--
-- Constraints for table `contracts`
--
ALTER TABLE `contracts`
  ADD CONSTRAINT `contracts_ibfk_1` FOREIGN KEY (`grp_company_id`) REFERENCES `companies` (`id`),
  ADD CONSTRAINT `contracts_ibfk_2` FOREIGN KEY (`gf_company_id`) REFERENCES `companies` (`id`),
  ADD CONSTRAINT `contracts_ibfk_3` FOREIGN KEY (`grp_location_id`) REFERENCES `locations` (`id`),
  ADD CONSTRAINT `contracts_ibfk_4` FOREIGN KEY (`gf_location_id`) REFERENCES `locations` (`id`);

--
-- Constraints for table `listings`
--
ALTER TABLE `listings`
  ADD CONSTRAINT `listings_ibfk_1` FOREIGN KEY (`location_id`) REFERENCES `locations` (`id`),
  ADD CONSTRAINT `listings_ibfk_2` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`),
  ADD CONSTRAINT `listings_ibfk_3` FOREIGN KEY (`side`) REFERENCES `listing_sides` (`side`),
  ADD CONSTRAINT `listings_ibfk_4` FOREIGN KEY (`recycled`) REFERENCES `recycled_statuses` (`status`),
  ADD CONSTRAINT `listings_ibfk_5` FOREIGN KEY (`tested`) REFERENCES `tested_statuses` (`status`),
  ADD CONSTRAINT `listings_ibfk_6` FOREIGN KEY (`currency`) REFERENCES `currency_iso` (`currency`);

--
-- Constraints for table `locations`
--
ALTER TABLE `locations`
  ADD CONSTRAINT `locations_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `maintenance_events`
--
ALTER TABLE `maintenance_events`
  ADD CONSTRAINT `maintenance_events_ibfk_1` FOREIGN KEY (`location_id`) REFERENCES `locations` (`id`);

--
-- Constraints for table `mollie_payments`
--
ALTER TABLE `mollie_payments`
  ADD CONSTRAINT `mollie_payments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `page_content`
--
ALTER TABLE `page_content`
  ADD CONSTRAINT `page_content_ibfk_1` FOREIGN KEY (`section_id`) REFERENCES `page_sections` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `page_content_ibfk_2` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `page_sections`
--
ALTER TABLE `page_sections`
  ADD CONSTRAINT `page_sections_ibfk_1` FOREIGN KEY (`page_id`) REFERENCES `pages` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `payment_errors`
--
ALTER TABLE `payment_errors`
  ADD CONSTRAINT `payment_errors_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `subscriptions`
--
ALTER TABLE `subscriptions`
  ADD CONSTRAINT `subscriptions_ibfk_1` FOREIGN KEY (`location_id`) REFERENCES `locations` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `user_locations`
--
ALTER TABLE `user_locations`
  ADD CONSTRAINT `user_locations_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_locations_ibfk_2` FOREIGN KEY (`location_id`) REFERENCES `locations` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_subscriptions`
--
ALTER TABLE `user_subscriptions`
  ADD CONSTRAINT `user_subscriptions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
