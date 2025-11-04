-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 04, 2025 at 12:22 PM
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
(4, 'testaccount', 'Other', NULL, NULL, '2025-10-31 10:14:41'),
(5, 'test_sub', 'Other', NULL, NULL, '2025-11-03 12:45:22');

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
(4, NULL, 4, 'WTS', 'Brown Glass', NULL, 432545.00, 'asdfasdf!', 'unknown', 'unknown', NULL, '', 'EUR', '2025-10-31 10:16:48', NULL, 1, 'rtwert', 'uploads/listings/listing_1761905808_69048c908d4b3.jpeg', 0),
(5, NULL, 5, 'WTS', 'Green Glass', NULL, 324.00, 'ifjbnwjri', 'unknown', 'unknown', NULL, NULL, 'EUR', '2025-11-03 12:45:22', NULL, 1, 'asdf', 'uploads/listings/listing_1762173922_6908a3e2cb7ea.png', 0),
(6, NULL, 5, 'WTS', 'Green Glass', NULL, 234.00, 'asdfasdf', 'unknown', 'unknown', NULL, NULL, 'EUR', '2025-11-03 12:48:53', NULL, 0, 'erwt', 'uploads/listings/listing_1762174133_6908a4b57b97e.png', 0);

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
(4, 11, 'tr_HoaNUqcvAgr6cSzER5LGJ', 9.99, 'paid', 1, '2025-10-31 09:41:27', '2025-10-31 09:42:51', '2025-10-31 09:42:51'),
(5, 10, 'tr_HwHjPAt8YXQsH2AotGUGJ', 9.99, 'open', 1, '2025-11-03 13:42:41', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `pages`
--

CREATE TABLE `pages` (
  `id` int(11) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `title` varchar(255) NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pages`
--

INSERT INTO `pages` (`id`, `slug`, `title`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'terms', 'Terms of Service', 1, '2025-11-03 14:10:34', '2025-11-03 14:10:34'),
(2, 'privacy', 'Privacy Policy', 1, '2025-11-03 15:27:47', '2025-11-03 15:27:47'),
(3, 'about-us', 'About Us', 1, '2025-11-04 10:21:36', '2025-11-04 10:21:36');

-- --------------------------------------------------------

--
-- Table structure for table `page_content`
--

CREATE TABLE `page_content` (
  `id` int(11) NOT NULL,
  `section_id` int(11) NOT NULL,
  `content_value` text NOT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `page_content`
--

INSERT INTO `page_content` (`id`, `section_id`, `content_value`, `updated_by`, `created_at`, `updated_at`) VALUES
(1, 1, 'Terms of Service!', 4, '2025-11-03 14:10:34', '2025-11-04 09:27:12'),
(2, 2, 'Agreement for using Glass Market platform', 4, '2025-11-03 14:10:34', '2025-11-04 09:27:12'),
(3, 3, 'November 3, 2025', 4, '2025-11-03 14:10:34', '2025-11-04 09:27:12'),
(4, 4, 'Welcome to Glass Market. By accessing or using our platform, you agree to be bound by these Terms of Service (\"Terms\"). Please read them carefully.', 4, '2025-11-03 14:10:34', '2025-11-04 09:27:12'),
(5, 5, 'Important: These Terms contain a mandatory arbitration provision and class action waiver. Please review Section 14 carefully.', 4, '2025-11-03 14:10:34', '2025-11-04 09:27:12'),
(6, 6, '1. Acceptance of Terms', 4, '2025-11-03 14:10:34', '2025-11-04 09:27:12'),
(7, 7, 'By creating an account, accessing, or using Glass Market, you agree to:', 4, '2025-11-03 14:10:34', '2025-11-04 09:27:12'),
(8, 8, 'Comply with these Terms and all applicable laws', 4, '2025-11-03 14:10:34', '2025-11-04 09:27:12'),
(9, 9, 'Our Privacy Policy', 4, '2025-11-03 14:10:34', '2025-11-04 09:27:12'),
(10, 10, 'Our Seller Guidelines (if selling)', 4, '2025-11-03 14:10:34', '2025-11-04 09:27:12'),
(11, 11, 'Any additional policies and guidelines posted on our platform', 4, '2025-11-03 14:10:34', '2025-11-04 09:27:12'),
(12, 12, 'If you do not agree to these Terms, you may not use our services.', 4, '2025-11-03 14:10:34', '2025-11-04 09:27:12'),
(13, 13, '2. Eligibility', 4, '2025-11-03 14:10:34', '2025-11-04 09:27:12'),
(14, 14, 'To use Glass Market, you must:', 4, '2025-11-03 14:10:34', '2025-11-04 09:27:12'),
(15, 15, 'Be at least 18 years old', 4, '2025-11-03 14:10:34', '2025-11-04 09:27:12'),
(16, 16, 'Have the legal capacity to enter into binding contracts', 4, '2025-11-03 14:10:34', '2025-11-04 09:27:12'),
(17, 17, 'Not be prohibited from using our services under applicable law', 4, '2025-11-03 14:10:34', '2025-11-04 09:27:12'),
(18, 18, 'Provide accurate and complete registration information', 4, '2025-11-03 14:10:34', '2025-11-04 09:27:12'),
(19, 19, 'Maintain the security of your account credentials', 4, '2025-11-03 14:10:34', '2025-11-04 09:27:12'),
(20, 20, '3. Account Responsibilities', 4, '2025-11-03 14:10:34', '2025-11-04 09:27:12'),
(21, 21, 'You are responsible for:', 4, '2025-11-03 14:10:34', '2025-11-04 09:27:12'),
(22, 22, 'All activity that occurs under your account', 4, '2025-11-03 14:10:34', '2025-11-04 09:27:12'),
(23, 23, 'Maintaining the confidentiality of your password', 4, '2025-11-03 14:10:34', '2025-11-04 09:27:12'),
(24, 24, 'Notifying us immediately of any unauthorized use', 4, '2025-11-03 14:10:34', '2025-11-04 09:27:12'),
(25, 25, 'Providing truthful and accurate information', 4, '2025-11-03 14:10:34', '2025-11-04 09:27:12'),
(26, 26, 'Updating your information to keep it current', 4, '2025-11-03 14:10:34', '2025-11-04 09:27:12'),
(27, 27, 'You may NOT:', 4, '2025-11-03 14:10:34', '2025-11-04 09:27:12'),
(28, 28, 'Share your account with others', 4, '2025-11-03 14:10:34', '2025-11-04 09:27:12'),
(29, 29, 'Create multiple accounts to circumvent restrictions', 4, '2025-11-03 14:10:34', '2025-11-04 09:27:12'),
(30, 30, 'Impersonate another person or entity', 4, '2025-11-03 14:10:34', '2025-11-04 09:27:12'),
(31, 31, 'Use automated tools to access our platform (bots, scrapers)', 4, '2025-11-03 14:10:34', '2025-11-04 09:27:12'),
(32, 32, 'Sell, transfer, or rent your account to others', 4, '2025-11-03 14:10:34', '2025-11-04 09:27:12'),
(33, 33, '4. Platform Usage', 4, '2025-11-03 14:10:34', '2025-11-04 09:27:12'),
(34, 34, 'Permitted Use', 4, '2025-11-03 14:10:34', '2025-11-04 09:27:12'),
(35, 35, 'You may use Glass Market to:', 4, '2025-11-03 14:10:34', '2025-11-04 09:27:12'),
(36, 36, 'Browse and purchase glass products', 4, '2025-11-03 14:10:34', '2025-11-04 09:27:12'),
(37, 37, 'List and sell glass products (if approved as a seller)', 4, '2025-11-03 14:10:34', '2025-11-04 09:27:12'),
(38, 38, 'Communicate with other users for legitimate transactions', 4, '2025-11-03 14:10:34', '2025-11-04 09:27:12'),
(39, 39, 'Access features and tools provided by our platform', 4, '2025-11-03 14:10:34', '2025-11-04 09:27:12'),
(40, 40, 'Prohibited Activities', 4, '2025-11-03 14:10:34', '2025-11-04 09:27:12'),
(41, 41, 'You may NOT:', 4, '2025-11-03 14:10:34', '2025-11-04 09:27:12'),
(42, 42, 'Violate any laws or regulations', 4, '2025-11-03 14:10:34', '2025-11-04 09:27:12'),
(43, 43, 'Infringe on intellectual property rights', 4, '2025-11-03 14:10:34', '2025-11-04 09:27:12'),
(44, 44, 'Post false, misleading, or deceptive content', 4, '2025-11-03 14:10:34', '2025-11-04 09:27:12'),
(45, 45, 'Engage in fraudulent transactions', 4, '2025-11-03 14:10:34', '2025-11-04 09:27:12'),
(46, 46, 'Harass, threaten, or abuse other users', 4, '2025-11-03 14:10:34', '2025-11-04 09:27:12'),
(47, 47, 'Spam or send unsolicited communications', 4, '2025-11-03 14:10:34', '2025-11-04 09:27:12'),
(48, 48, 'Attempt to circumvent fees or payments', 4, '2025-11-03 14:10:34', '2025-11-04 09:27:12'),
(49, 49, 'Interfere with platform operation or security', 4, '2025-11-03 14:10:34', '2025-11-04 09:27:12'),
(50, 50, 'Transmit viruses or malicious code', 4, '2025-11-03 14:10:34', '2025-11-04 09:27:12'),
(51, 51, 'Collect user data without permission', 4, '2025-11-03 14:10:34', '2025-11-04 09:27:12'),
(52, 52, 'Complete transactions off-platform to avoid fees', 4, '2025-11-03 14:10:34', '2025-11-04 09:27:12'),
(53, 53, '16. Contact Information', 4, '2025-11-03 14:10:34', '2025-11-04 09:27:12'),
(54, 54, 'Questions about these Terms? Contact us:', 4, '2025-11-03 14:10:34', '2025-11-04 09:27:12'),
(55, 55, 'legal@glassmarket.com', 4, '2025-11-03 14:10:34', '2025-11-04 09:27:12'),
(56, 56, '1-800-GLASS-123', 4, '2025-11-03 14:10:34', '2025-11-04 09:27:12'),
(57, 57, 'Glass Market Legal Department\r\n123 Glass Street\r\nNew York, NY 10001', 4, '2025-11-03 14:10:34', '2025-11-04 09:27:12'),
(58, 58, 'Thank you for using Glass Market! We\'re committed to providing a safe, transparent marketplace for glass buyers and sellers.', 4, '2025-11-03 14:10:34', '2025-11-04 09:27:12'),
(59, 59, 'Privacy Policy', NULL, '2025-11-03 15:27:47', '2025-11-03 15:27:47'),
(60, 60, 'How we collect, use, and protect your information', NULL, '2025-11-03 15:27:47', '2025-11-03 15:27:47'),
(61, 61, 'November 3, 2025', NULL, '2025-11-03 15:27:47', '2025-11-03 15:27:47'),
(62, 62, 'At Glass Market, we take your privacy seriously. This Privacy Policy explains how we collect, use, disclose, and safeguard your information when you use our platform.', NULL, '2025-11-03 15:27:47', '2025-11-03 15:27:47'),
(63, 63, 'Your Rights: You have the right to access, correct, or delete your personal information at any time. Contact us at privacy@glassmarket.com for assistance.', NULL, '2025-11-03 15:27:47', '2025-11-03 15:27:47'),
(64, 64, '1. Information We Collect', NULL, '2025-11-03 15:27:47', '2025-11-03 15:27:47'),
(65, 65, 'Information You Provide', NULL, '2025-11-03 15:27:47', '2025-11-03 15:27:47'),
(66, 66, 'We collect information you voluntarily provide when using our services:', NULL, '2025-11-03 15:27:47', '2025-11-03 15:27:47'),
(67, 67, 'Account Information: Name, email address, phone number, password', NULL, '2025-11-03 15:27:47', '2025-11-03 15:27:47'),
(68, 68, 'Profile Information: Profile photo, bio, business name, location', NULL, '2025-11-03 15:27:47', '2025-11-03 15:27:47'),
(69, 69, 'Payment Information: Credit card details, billing address, payment history', NULL, '2025-11-03 15:27:47', '2025-11-03 15:27:47'),
(70, 70, 'Listing Information: Product descriptions, photos, pricing', NULL, '2025-11-03 15:27:47', '2025-11-03 15:27:47'),
(71, 71, 'Communication: Messages, reviews, support inquiries', NULL, '2025-11-03 15:27:47', '2025-11-03 15:27:47'),
(72, 72, 'Verification Data: Government ID, business documents (for sellers)', NULL, '2025-11-03 15:27:47', '2025-11-03 15:27:47'),
(73, 73, 'Automatically Collected Information', NULL, '2025-11-03 15:27:47', '2025-11-03 15:27:47'),
(74, 74, 'When you use our platform, we automatically collect:', NULL, '2025-11-03 15:27:47', '2025-11-03 15:27:47'),
(75, 75, 'Device Information: IP address, browser type, operating system', NULL, '2025-11-03 15:27:47', '2025-11-03 15:27:47'),
(76, 76, 'Usage Data: Pages viewed, time spent, clicks, search queries', NULL, '2025-11-03 15:27:47', '2025-11-03 15:27:47'),
(77, 77, 'Location Data: Approximate location based on IP address', NULL, '2025-11-03 15:27:47', '2025-11-03 15:27:47'),
(78, 78, 'Cookies & Tracking: Session data, preferences, analytics', NULL, '2025-11-03 15:27:47', '2025-11-03 15:27:47'),
(79, 79, '2. How We Use Your Information', NULL, '2025-11-03 15:27:47', '2025-11-03 15:27:47'),
(80, 80, 'We use your information to:', NULL, '2025-11-03 15:27:47', '2025-11-03 15:27:47'),
(81, 81, 'Provide Services: Process transactions, facilitate buying and selling', NULL, '2025-11-03 15:27:47', '2025-11-03 15:27:47'),
(82, 82, 'Account Management: Create and manage your account', NULL, '2025-11-03 15:27:47', '2025-11-03 15:27:47'),
(83, 83, 'Communication: Send order updates, notifications, and support responses', NULL, '2025-11-03 15:27:47', '2025-11-03 15:27:47'),
(84, 84, 'Improve Platform: Analyze usage to enhance features and user experience', NULL, '2025-11-03 15:27:47', '2025-11-03 15:27:47'),
(85, 85, 'Security: Detect fraud, prevent abuse, and protect user safety', NULL, '2025-11-03 15:27:47', '2025-11-03 15:27:47'),
(86, 86, 'Marketing: Send promotional emails (you can opt out anytime)', NULL, '2025-11-03 15:27:47', '2025-11-03 15:27:47'),
(87, 87, 'Legal Compliance: Meet legal obligations and enforce our terms', NULL, '2025-11-03 15:27:47', '2025-11-03 15:27:47'),
(88, 88, 'Personalization: Customize content and recommendations', NULL, '2025-11-03 15:27:47', '2025-11-03 15:27:47'),
(89, 89, '3. Information Sharing', NULL, '2025-11-03 15:27:47', '2025-11-03 15:27:47'),
(90, 90, 'We Share Your Information With:', NULL, '2025-11-03 15:27:47', '2025-11-03 15:27:47'),
(91, 91, 'Other Users: Buyers and sellers see necessary transaction information', NULL, '2025-11-03 15:27:47', '2025-11-03 15:27:47'),
(92, 92, 'Service Providers: Payment processors, shipping carriers, email services', NULL, '2025-11-03 15:27:47', '2025-11-03 15:27:47'),
(93, 93, 'Business Partners: Marketing partners (only with your consent)', NULL, '2025-11-03 15:27:47', '2025-11-03 15:27:47'),
(94, 94, 'Legal Authorities: When required by law or to protect rights', NULL, '2025-11-03 15:27:47', '2025-11-03 15:27:47'),
(95, 95, 'Business Transfers: In case of merger, acquisition, or sale', NULL, '2025-11-03 15:27:47', '2025-11-03 15:27:47'),
(96, 96, 'We Do NOT:', NULL, '2025-11-03 15:27:47', '2025-11-03 15:27:47'),
(97, 97, 'Sell your personal information to third parties', NULL, '2025-11-03 15:27:47', '2025-11-03 15:27:47'),
(98, 98, 'Share your data for unrelated purposes without consent', NULL, '2025-11-03 15:27:47', '2025-11-03 15:27:47'),
(99, 99, 'Disclose your payment details to other users', NULL, '2025-11-03 15:27:47', '2025-11-03 15:27:47'),
(100, 100, '4. Data Security', NULL, '2025-11-03 15:27:47', '2025-11-03 15:27:47'),
(101, 101, 'We implement security measures to protect your information:', NULL, '2025-11-03 15:27:47', '2025-11-03 15:27:47'),
(102, 102, 'Encryption: SSL/TLS encryption for data transmission', NULL, '2025-11-03 15:27:47', '2025-11-03 15:27:47'),
(103, 103, 'Secure Storage: Encrypted databases and secure servers', NULL, '2025-11-03 15:27:47', '2025-11-03 15:27:47'),
(104, 104, 'Access Controls: Limited employee access to personal data', NULL, '2025-11-03 15:27:47', '2025-11-03 15:27:47'),
(105, 105, 'Regular Audits: Security assessments and vulnerability testing', NULL, '2025-11-03 15:27:47', '2025-11-03 15:27:47'),
(106, 106, 'Payment Security: PCI-DSS compliant payment processing', NULL, '2025-11-03 15:27:47', '2025-11-03 15:27:47'),
(107, 107, 'However, no method of transmission over the internet is 100% secure. While we strive to protect your data, we cannot guarantee absolute security.', NULL, '2025-11-03 15:27:47', '2025-11-03 15:27:47'),
(108, 108, '5. Your Privacy Rights', NULL, '2025-11-03 15:27:47', '2025-11-03 15:27:47'),
(109, 109, 'You Have the Right To:', NULL, '2025-11-03 15:27:47', '2025-11-03 15:27:47'),
(110, 110, 'Access: Request a copy of your personal information', NULL, '2025-11-03 15:27:47', '2025-11-03 15:27:47'),
(111, 111, 'Correction: Update or correct inaccurate information', NULL, '2025-11-03 15:27:47', '2025-11-03 15:27:47'),
(112, 112, 'Deletion: Request deletion of your account and data', NULL, '2025-11-03 15:27:47', '2025-11-03 15:27:47'),
(113, 113, 'Opt-Out: Unsubscribe from marketing emails', NULL, '2025-11-03 15:27:47', '2025-11-03 15:27:47'),
(114, 114, 'Data Portability: Receive your data in a portable format', NULL, '2025-11-03 15:27:47', '2025-11-03 15:27:47'),
(115, 115, 'Object: Object to certain processing of your data', NULL, '2025-11-03 15:27:47', '2025-11-03 15:27:47'),
(116, 116, 'Withdraw Consent: Revoke consent where applicable', NULL, '2025-11-03 15:27:47', '2025-11-03 15:27:47'),
(117, 117, 'To exercise these rights, contact us at privacy@glassmarket.com or through your account settings.', NULL, '2025-11-03 15:27:47', '2025-11-03 15:27:47'),
(118, 118, '15. Contact Us', NULL, '2025-11-03 15:27:47', '2025-11-03 15:27:47'),
(119, 119, 'If you have questions about this Privacy Policy or our privacy practices, please contact us:', NULL, '2025-11-03 15:27:47', '2025-11-03 15:27:47'),
(120, 120, 'privacy@glassmarket.com', NULL, '2025-11-03 15:27:47', '2025-11-03 15:27:47'),
(121, 121, '1-800-GLASS-123', NULL, '2025-11-03 15:27:47', '2025-11-03 15:27:47'),
(122, 122, 'Glass Market, 123 Glass Street, New York, NY 10001', NULL, '2025-11-03 15:27:47', '2025-11-03 15:27:47'),
(123, 123, 'dpo@glassmarket.com', NULL, '2025-11-03 15:27:47', '2025-11-03 15:27:47'),
(189, 219, 'Start Selling', 4, '2025-11-04 10:21:37', '2025-11-04 10:21:48'),
(190, 220, '../selling-page.php', 4, '2025-11-04 10:21:37', '2025-11-04 10:21:48'),
(191, 221, 'Browse Listings', 4, '2025-11-04 10:21:37', '2025-11-04 10:21:48'),
(192, 222, '../browse.php', 4, '2025-11-04 10:21:37', '2025-11-04 10:21:48'),
(193, 218, 'Whether you sell, ship, or source cullet, Glass Market is your always-on operations partner. Start trading today and be part of the sustainable glass revolution.', 4, '2025-11-04 10:21:37', '2025-11-04 10:21:48'),
(194, 217, 'Join the Circular Glass Movement', 4, '2025-11-04 10:21:37', '2025-11-04 10:21:48'),
(195, 192, 'Glass Market is the leading B2B marketplace for glass cullet trading, connecting recyclers, processors, and manufacturers worldwide.', 4, '2025-11-04 10:21:37', '2025-11-04 10:21:48'),
(196, 189, 'ABOUT US!', 4, '2025-11-04 10:21:37', '2025-11-04 10:21:48'),
(197, 193, 'Explore Collection', 4, '2025-11-04 10:21:37', '2025-11-04 10:21:48'),
(198, 194, '../browse.php', 4, '2025-11-04 10:21:37', '2025-11-04 10:21:48'),
(199, 195, 'Contact Us', 4, '2025-11-04 10:21:37', '2025-11-04 10:21:48'),
(200, 196, '../../public/contact.php', 4, '2025-11-04 10:21:37', '2025-11-04 10:21:48'),
(201, 191, 'Connecting the global glass recycling industry', 4, '2025-11-04 10:21:37', '2025-11-04 10:21:48'),
(202, 190, 'About Glass Market', 4, '2025-11-04 10:21:37', '2025-11-04 10:21:48'),
(203, 204, 'To create a sustainable circular economy for glass by connecting recyclers, processors, and manufacturers through our innovative B2B marketplace platform.', 4, '2025-11-04 10:21:37', '2025-11-04 10:21:48'),
(204, 203, 'Our Mission', 4, '2025-11-04 10:21:37', '2025-11-04 10:21:48'),
(205, 198, 'Tons Traded', 4, '2025-11-04 10:21:37', '2025-11-04 10:21:48'),
(206, 197, '10,000+', 4, '2025-11-04 10:21:37', '2025-11-04 10:21:48'),
(207, 200, 'Active Partners', 4, '2025-11-04 10:21:37', '2025-11-04 10:21:48'),
(208, 199, '50+', 4, '2025-11-04 10:21:37', '2025-11-04 10:21:48'),
(209, 202, 'Countries Served', 4, '2025-11-04 10:21:37', '2025-11-04 10:21:48'),
(210, 201, '25', 4, '2025-11-04 10:21:37', '2025-11-04 10:21:48'),
(211, 216, 'Glass Market is powered by a passionate team of industry experts, sustainability advocates, and technology innovators dedicated to transforming the glass recycling industry.', 4, '2025-11-04 10:21:37', '2025-11-04 10:21:48'),
(212, 215, 'Our Team', 4, '2025-11-04 10:21:37', '2025-11-04 10:21:48'),
(213, 208, 'We are guided by principles that drive sustainable growth and meaningful partnerships.', 4, '2025-11-04 10:21:37', '2025-11-04 10:21:48'),
(214, 210, 'We prioritize environmental impact in every decision, promoting circular economy principles and reducing glass waste worldwide.', 4, '2025-11-04 10:21:37', '2025-11-04 10:21:48'),
(215, 209, 'Sustainability First', 4, '2025-11-04 10:21:37', '2025-11-04 10:21:48'),
(216, 212, 'We build lasting relationships through honest communication, fair practices, and reliable service to all our partners.', 4, '2025-11-04 10:21:37', '2025-11-04 10:21:48'),
(217, 211, 'Trust & Transparency', 4, '2025-11-04 10:21:37', '2025-11-04 10:21:48'),
(218, 214, 'We continuously improve our platform and services to provide the best glass trading experience in the industry.', 4, '2025-11-04 10:21:37', '2025-11-04 10:21:48'),
(219, 213, 'Innovation & Quality', 4, '2025-11-04 10:21:37', '2025-11-04 10:21:48'),
(220, 207, 'Our Values', 4, '2025-11-04 10:21:37', '2025-11-04 10:21:48'),
(221, 206, 'A world where every piece of glass is recycled and reused, reducing waste and environmental impact while supporting businesses globally.', 4, '2025-11-04 10:21:37', '2025-11-04 10:21:48'),
(222, 205, 'Our Vision', 4, '2025-11-04 10:21:37', '2025-11-04 10:21:48');

-- --------------------------------------------------------

--
-- Table structure for table `page_sections`
--

CREATE TABLE `page_sections` (
  `id` int(11) NOT NULL,
  `page_id` int(11) NOT NULL,
  `section_key` varchar(100) NOT NULL,
  `section_label` varchar(255) NOT NULL,
  `section_type` varchar(50) DEFAULT 'text',
  `section_group` varchar(100) DEFAULT NULL,
  `display_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `page_sections`
--

INSERT INTO `page_sections` (`id`, `page_id`, `section_key`, `section_label`, `section_type`, `section_group`, `display_order`, `created_at`, `updated_at`) VALUES
(1, 1, 'hero_title', 'Hero Title', 'text', 'hero', 1, '2025-11-03 14:10:34', '2025-11-03 14:10:34'),
(2, 1, 'hero_subtitle', 'Hero Subtitle', 'text', 'hero', 2, '2025-11-03 14:10:34', '2025-11-03 14:10:34'),
(3, 1, 'last_updated', 'Last Updated Date', 'text', 'hero', 3, '2025-11-03 14:10:34', '2025-11-03 14:10:34'),
(4, 1, 'intro_text', 'Introduction Text', 'textarea', 'introduction', 4, '2025-11-03 14:10:34', '2025-11-03 14:10:34'),
(5, 1, 'warning_text', 'Warning Box Text', 'textarea', 'introduction', 5, '2025-11-03 14:10:34', '2025-11-03 14:10:34'),
(6, 1, 'section_1_title', 'Section 1 Title', 'text', 'section_1', 6, '2025-11-03 14:10:34', '2025-11-03 14:10:34'),
(7, 1, 'section_1_intro', 'Section 1 Introduction', 'textarea', 'section_1', 7, '2025-11-03 14:10:34', '2025-11-03 14:10:34'),
(8, 1, 'section_1_point_1', 'Agreement Point 1', 'text', 'section_1', 8, '2025-11-03 14:10:34', '2025-11-03 14:10:34'),
(9, 1, 'section_1_point_2', 'Agreement Point 2', 'text', 'section_1', 9, '2025-11-03 14:10:34', '2025-11-03 14:10:34'),
(10, 1, 'section_1_point_3', 'Agreement Point 3', 'text', 'section_1', 10, '2025-11-03 14:10:34', '2025-11-03 14:10:34'),
(11, 1, 'section_1_point_4', 'Agreement Point 4', 'text', 'section_1', 11, '2025-11-03 14:10:34', '2025-11-03 14:10:34'),
(12, 1, 'section_1_closing', 'Section 1 Closing', 'textarea', 'section_1', 12, '2025-11-03 14:10:34', '2025-11-03 14:10:34'),
(13, 1, 'section_2_title', 'Section 2 Title', 'text', 'section_2', 13, '2025-11-03 14:10:34', '2025-11-03 14:10:34'),
(14, 1, 'section_2_intro', 'Section 2 Introduction', 'textarea', 'section_2', 14, '2025-11-03 14:10:34', '2025-11-03 14:10:34'),
(15, 1, 'section_2_point_1', 'Eligibility Point 1', 'text', 'section_2', 15, '2025-11-03 14:10:34', '2025-11-03 14:10:34'),
(16, 1, 'section_2_point_2', 'Eligibility Point 2', 'text', 'section_2', 16, '2025-11-03 14:10:34', '2025-11-03 14:10:34'),
(17, 1, 'section_2_point_3', 'Eligibility Point 3', 'text', 'section_2', 17, '2025-11-03 14:10:34', '2025-11-03 14:10:34'),
(18, 1, 'section_2_point_4', 'Eligibility Point 4', 'text', 'section_2', 18, '2025-11-03 14:10:34', '2025-11-03 14:10:34'),
(19, 1, 'section_2_point_5', 'Eligibility Point 5', 'text', 'section_2', 19, '2025-11-03 14:10:34', '2025-11-03 14:10:34'),
(20, 1, 'section_3_title', 'Section 3 Title', 'text', 'section_3', 20, '2025-11-03 14:10:34', '2025-11-03 14:10:34'),
(21, 1, 'section_3_subtitle_1', 'Responsibilities Subtitle', 'text', 'section_3', 21, '2025-11-03 14:10:34', '2025-11-03 14:10:34'),
(22, 1, 'section_3_resp_1', 'Responsibility 1', 'text', 'section_3', 22, '2025-11-03 14:10:34', '2025-11-03 14:10:34'),
(23, 1, 'section_3_resp_2', 'Responsibility 2', 'text', 'section_3', 23, '2025-11-03 14:10:34', '2025-11-03 14:10:34'),
(24, 1, 'section_3_resp_3', 'Responsibility 3', 'text', 'section_3', 24, '2025-11-03 14:10:34', '2025-11-03 14:10:34'),
(25, 1, 'section_3_resp_4', 'Responsibility 4', 'text', 'section_3', 25, '2025-11-03 14:10:34', '2025-11-03 14:10:34'),
(26, 1, 'section_3_resp_5', 'Responsibility 5', 'text', 'section_3', 26, '2025-11-03 14:10:34', '2025-11-03 14:10:34'),
(27, 1, 'section_3_subtitle_2', 'Prohibitions Subtitle', 'text', 'section_3', 27, '2025-11-03 14:10:34', '2025-11-03 14:10:34'),
(28, 1, 'section_3_prohibit_1', 'Prohibition 1', 'text', 'section_3', 28, '2025-11-03 14:10:34', '2025-11-03 14:10:34'),
(29, 1, 'section_3_prohibit_2', 'Prohibition 2', 'text', 'section_3', 29, '2025-11-03 14:10:34', '2025-11-03 14:10:34'),
(30, 1, 'section_3_prohibit_3', 'Prohibition 3', 'text', 'section_3', 30, '2025-11-03 14:10:34', '2025-11-03 14:10:34'),
(31, 1, 'section_3_prohibit_4', 'Prohibition 4', 'text', 'section_3', 31, '2025-11-03 14:10:34', '2025-11-03 14:10:34'),
(32, 1, 'section_3_prohibit_5', 'Prohibition 5', 'text', 'section_3', 32, '2025-11-03 14:10:34', '2025-11-03 14:10:34'),
(33, 1, 'section_4_title', 'Section 4 Title', 'text', 'section_4', 33, '2025-11-03 14:10:34', '2025-11-03 14:10:34'),
(34, 1, 'section_4_subtitle_1', 'Permitted Use Subtitle', 'text', 'section_4', 34, '2025-11-03 14:10:34', '2025-11-03 14:10:34'),
(35, 1, 'section_4_intro_1', 'Permitted Use Introduction', 'textarea', 'section_4', 35, '2025-11-03 14:10:34', '2025-11-03 14:10:34'),
(36, 1, 'section_4_permitted_1', 'Permitted Activity 1', 'text', 'section_4', 36, '2025-11-03 14:10:34', '2025-11-03 14:10:34'),
(37, 1, 'section_4_permitted_2', 'Permitted Activity 2', 'text', 'section_4', 37, '2025-11-03 14:10:34', '2025-11-03 14:10:34'),
(38, 1, 'section_4_permitted_3', 'Permitted Activity 3', 'text', 'section_4', 38, '2025-11-03 14:10:34', '2025-11-03 14:10:34'),
(39, 1, 'section_4_permitted_4', 'Permitted Activity 4', 'text', 'section_4', 39, '2025-11-03 14:10:34', '2025-11-03 14:10:34'),
(40, 1, 'section_4_subtitle_2', 'Prohibited Activities Subtitle', 'text', 'section_4', 40, '2025-11-03 14:10:34', '2025-11-03 14:10:34'),
(41, 1, 'section_4_intro_2', 'Prohibited Activities Introduction', 'textarea', 'section_4', 41, '2025-11-03 14:10:34', '2025-11-03 14:10:34'),
(42, 1, 'section_4_prohibited_1', 'Prohibited Activity 1', 'text', 'section_4', 42, '2025-11-03 14:10:34', '2025-11-03 14:10:34'),
(43, 1, 'section_4_prohibited_2', 'Prohibited Activity 2', 'text', 'section_4', 43, '2025-11-03 14:10:34', '2025-11-03 14:10:34'),
(44, 1, 'section_4_prohibited_3', 'Prohibited Activity 3', 'text', 'section_4', 44, '2025-11-03 14:10:34', '2025-11-03 14:10:34'),
(45, 1, 'section_4_prohibited_4', 'Prohibited Activity 4', 'text', 'section_4', 45, '2025-11-03 14:10:34', '2025-11-03 14:10:34'),
(46, 1, 'section_4_prohibited_5', 'Prohibited Activity 5', 'text', 'section_4', 46, '2025-11-03 14:10:34', '2025-11-03 14:10:34'),
(47, 1, 'section_4_prohibited_6', 'Prohibited Activity 6', 'text', 'section_4', 47, '2025-11-03 14:10:34', '2025-11-03 14:10:34'),
(48, 1, 'section_4_prohibited_7', 'Prohibited Activity 7', 'text', 'section_4', 48, '2025-11-03 14:10:34', '2025-11-03 14:10:34'),
(49, 1, 'section_4_prohibited_8', 'Prohibited Activity 8', 'text', 'section_4', 49, '2025-11-03 14:10:34', '2025-11-03 14:10:34'),
(50, 1, 'section_4_prohibited_9', 'Prohibited Activity 9', 'text', 'section_4', 50, '2025-11-03 14:10:34', '2025-11-03 14:10:34'),
(51, 1, 'section_4_prohibited_10', 'Prohibited Activity 10', 'text', 'section_4', 51, '2025-11-03 14:10:34', '2025-11-03 14:10:34'),
(52, 1, 'section_4_prohibited_11', 'Prohibited Activity 11', 'text', 'section_4', 52, '2025-11-03 14:10:34', '2025-11-03 14:10:34'),
(53, 1, 'contact_title', 'Contact Section Title', 'text', 'contact', 100, '2025-11-03 14:10:34', '2025-11-03 14:10:34'),
(54, 1, 'contact_intro', 'Contact Introduction', 'textarea', 'contact', 101, '2025-11-03 14:10:34', '2025-11-03 14:10:34'),
(55, 1, 'contact_email', 'Contact Email', 'text', 'contact', 102, '2025-11-03 14:10:34', '2025-11-03 14:10:34'),
(56, 1, 'contact_phone', 'Contact Phone', 'text', 'contact', 103, '2025-11-03 14:10:34', '2025-11-03 14:10:34'),
(57, 1, 'contact_address', 'Contact Address', 'textarea', 'contact', 104, '2025-11-03 14:10:34', '2025-11-03 14:10:34'),
(58, 1, 'closing_message', 'Closing Message', 'textarea', 'contact', 105, '2025-11-03 14:10:34', '2025-11-03 14:10:34'),
(59, 2, 'hero_title', 'Hero Title', 'text', 'hero', 1, '2025-11-03 15:27:47', '2025-11-03 15:27:47'),
(60, 2, 'hero_subtitle', 'Hero Subtitle', 'text', 'hero', 2, '2025-11-03 15:27:47', '2025-11-03 15:27:47'),
(61, 2, 'last_updated', 'Last Updated Date', 'text', 'hero', 3, '2025-11-03 15:27:47', '2025-11-03 15:27:47'),
(62, 2, 'intro_text', 'Introduction Text', 'textarea', 'introduction', 4, '2025-11-03 15:27:47', '2025-11-03 15:27:47'),
(63, 2, 'info_box_text', 'Info Box Text', 'textarea', 'introduction', 5, '2025-11-03 15:27:47', '2025-11-03 15:27:47'),
(64, 2, 'section_1_title', 'Section 1 Title', 'text', 'section_1', 6, '2025-11-03 15:27:47', '2025-11-03 15:27:47'),
(65, 2, 'section_1_subtitle_1', 'Information You Provide Subtitle', 'text', 'section_1', 7, '2025-11-03 15:27:47', '2025-11-03 15:27:47'),
(66, 2, 'section_1_intro_1', 'Information You Provide Intro', 'textarea', 'section_1', 8, '2025-11-03 15:27:47', '2025-11-03 15:27:47'),
(67, 2, 'section_1_info_1', 'Account Information', 'text', 'section_1', 9, '2025-11-03 15:27:47', '2025-11-03 15:27:47'),
(68, 2, 'section_1_info_2', 'Profile Information', 'text', 'section_1', 10, '2025-11-03 15:27:47', '2025-11-03 15:27:47'),
(69, 2, 'section_1_info_3', 'Payment Information', 'text', 'section_1', 11, '2025-11-03 15:27:47', '2025-11-03 15:27:47'),
(70, 2, 'section_1_info_4', 'Listing Information', 'text', 'section_1', 12, '2025-11-03 15:27:47', '2025-11-03 15:27:47'),
(71, 2, 'section_1_info_5', 'Communication', 'text', 'section_1', 13, '2025-11-03 15:27:47', '2025-11-03 15:27:47'),
(72, 2, 'section_1_info_6', 'Verification Data', 'text', 'section_1', 14, '2025-11-03 15:27:47', '2025-11-03 15:27:47'),
(73, 2, 'section_1_subtitle_2', 'Automatically Collected Subtitle', 'text', 'section_1', 15, '2025-11-03 15:27:47', '2025-11-03 15:27:47'),
(74, 2, 'section_1_intro_2', 'Automatically Collected Intro', 'textarea', 'section_1', 16, '2025-11-03 15:27:47', '2025-11-03 15:27:47'),
(75, 2, 'section_1_auto_1', 'Device Information', 'text', 'section_1', 17, '2025-11-03 15:27:47', '2025-11-03 15:27:47'),
(76, 2, 'section_1_auto_2', 'Usage Data', 'text', 'section_1', 18, '2025-11-03 15:27:47', '2025-11-03 15:27:47'),
(77, 2, 'section_1_auto_3', 'Location Data', 'text', 'section_1', 19, '2025-11-03 15:27:47', '2025-11-03 15:27:47'),
(78, 2, 'section_1_auto_4', 'Cookies & Tracking', 'text', 'section_1', 20, '2025-11-03 15:27:47', '2025-11-03 15:27:47'),
(79, 2, 'section_2_title', 'Section 2 Title', 'text', 'section_2', 21, '2025-11-03 15:27:47', '2025-11-03 15:27:47'),
(80, 2, 'section_2_intro', 'Section 2 Introduction', 'textarea', 'section_2', 22, '2025-11-03 15:27:47', '2025-11-03 15:27:47'),
(81, 2, 'section_2_use_1', 'Provide Services', 'text', 'section_2', 23, '2025-11-03 15:27:47', '2025-11-03 15:27:47'),
(82, 2, 'section_2_use_2', 'Account Management', 'text', 'section_2', 24, '2025-11-03 15:27:47', '2025-11-03 15:27:47'),
(83, 2, 'section_2_use_3', 'Communication', 'text', 'section_2', 25, '2025-11-03 15:27:47', '2025-11-03 15:27:47'),
(84, 2, 'section_2_use_4', 'Improve Platform', 'text', 'section_2', 26, '2025-11-03 15:27:47', '2025-11-03 15:27:47'),
(85, 2, 'section_2_use_5', 'Security', 'text', 'section_2', 27, '2025-11-03 15:27:47', '2025-11-03 15:27:47'),
(86, 2, 'section_2_use_6', 'Marketing', 'text', 'section_2', 28, '2025-11-03 15:27:47', '2025-11-03 15:27:47'),
(87, 2, 'section_2_use_7', 'Legal Compliance', 'text', 'section_2', 29, '2025-11-03 15:27:47', '2025-11-03 15:27:47'),
(88, 2, 'section_2_use_8', 'Personalization', 'text', 'section_2', 30, '2025-11-03 15:27:47', '2025-11-03 15:27:47'),
(89, 2, 'section_3_title', 'Section 3 Title', 'text', 'section_3', 31, '2025-11-03 15:27:47', '2025-11-03 15:27:47'),
(90, 2, 'section_3_subtitle_1', 'We Share Subtitle', 'text', 'section_3', 32, '2025-11-03 15:27:47', '2025-11-03 15:27:47'),
(91, 2, 'section_3_share_1', 'Other Users', 'text', 'section_3', 33, '2025-11-03 15:27:47', '2025-11-03 15:27:47'),
(92, 2, 'section_3_share_2', 'Service Providers', 'text', 'section_3', 34, '2025-11-03 15:27:47', '2025-11-03 15:27:47'),
(93, 2, 'section_3_share_3', 'Business Partners', 'text', 'section_3', 35, '2025-11-03 15:27:47', '2025-11-03 15:27:47'),
(94, 2, 'section_3_share_4', 'Legal Authorities', 'text', 'section_3', 36, '2025-11-03 15:27:47', '2025-11-03 15:27:47'),
(95, 2, 'section_3_share_5', 'Business Transfers', 'text', 'section_3', 37, '2025-11-03 15:27:47', '2025-11-03 15:27:47'),
(96, 2, 'section_3_subtitle_2', 'We Do NOT Subtitle', 'text', 'section_3', 38, '2025-11-03 15:27:47', '2025-11-03 15:27:47'),
(97, 2, 'section_3_not_1', 'Not Sell Data', 'text', 'section_3', 39, '2025-11-03 15:27:47', '2025-11-03 15:27:47'),
(98, 2, 'section_3_not_2', 'Not Share Unrelated', 'text', 'section_3', 40, '2025-11-03 15:27:47', '2025-11-03 15:27:47'),
(99, 2, 'section_3_not_3', 'Not Disclose Payment', 'text', 'section_3', 41, '2025-11-03 15:27:47', '2025-11-03 15:27:47'),
(100, 2, 'section_4_title', 'Section 4 Title', 'text', 'section_4', 42, '2025-11-03 15:27:47', '2025-11-03 15:27:47'),
(101, 2, 'section_4_intro', 'Section 4 Introduction', 'textarea', 'section_4', 43, '2025-11-03 15:27:47', '2025-11-03 15:27:47'),
(102, 2, 'section_4_security_1', 'Encryption', 'text', 'section_4', 44, '2025-11-03 15:27:47', '2025-11-03 15:27:47'),
(103, 2, 'section_4_security_2', 'Secure Storage', 'text', 'section_4', 45, '2025-11-03 15:27:47', '2025-11-03 15:27:47'),
(104, 2, 'section_4_security_3', 'Access Controls', 'text', 'section_4', 46, '2025-11-03 15:27:47', '2025-11-03 15:27:47'),
(105, 2, 'section_4_security_4', 'Regular Audits', 'text', 'section_4', 47, '2025-11-03 15:27:47', '2025-11-03 15:27:47'),
(106, 2, 'section_4_security_5', 'Payment Security', 'text', 'section_4', 48, '2025-11-03 15:27:47', '2025-11-03 15:27:47'),
(107, 2, 'section_4_disclaimer', 'Security Disclaimer', 'textarea', 'section_4', 49, '2025-11-03 15:27:47', '2025-11-03 15:27:47'),
(108, 2, 'section_5_title', 'Section 5 Title', 'text', 'section_5', 50, '2025-11-03 15:27:47', '2025-11-03 15:27:47'),
(109, 2, 'section_5_subtitle', 'Rights Subtitle', 'text', 'section_5', 51, '2025-11-03 15:27:47', '2025-11-03 15:27:47'),
(110, 2, 'section_5_right_1', 'Access', 'text', 'section_5', 52, '2025-11-03 15:27:47', '2025-11-03 15:27:47'),
(111, 2, 'section_5_right_2', 'Correction', 'text', 'section_5', 53, '2025-11-03 15:27:47', '2025-11-03 15:27:47'),
(112, 2, 'section_5_right_3', 'Deletion', 'text', 'section_5', 54, '2025-11-03 15:27:47', '2025-11-03 15:27:47'),
(113, 2, 'section_5_right_4', 'Opt-Out', 'text', 'section_5', 55, '2025-11-03 15:27:47', '2025-11-03 15:27:47'),
(114, 2, 'section_5_right_5', 'Data Portability', 'text', 'section_5', 56, '2025-11-03 15:27:47', '2025-11-03 15:27:47'),
(115, 2, 'section_5_right_6', 'Object', 'text', 'section_5', 57, '2025-11-03 15:27:47', '2025-11-03 15:27:47'),
(116, 2, 'section_5_right_7', 'Withdraw Consent', 'text', 'section_5', 58, '2025-11-03 15:27:47', '2025-11-03 15:27:47'),
(117, 2, 'section_5_contact', 'Exercise Rights Text', 'textarea', 'section_5', 59, '2025-11-03 15:27:47', '2025-11-03 15:27:47'),
(118, 2, 'contact_title', 'Contact Section Title', 'text', 'contact', 100, '2025-11-03 15:27:47', '2025-11-03 15:27:47'),
(119, 2, 'contact_intro', 'Contact Introduction', 'textarea', 'contact', 101, '2025-11-03 15:27:47', '2025-11-03 15:27:47'),
(120, 2, 'contact_email', 'Contact Email', 'text', 'contact', 102, '2025-11-03 15:27:47', '2025-11-03 15:27:47'),
(121, 2, 'contact_phone', 'Contact Phone', 'text', 'contact', 103, '2025-11-03 15:27:47', '2025-11-03 15:27:47'),
(122, 2, 'contact_address', 'Contact Address', 'text', 'contact', 104, '2025-11-03 15:27:47', '2025-11-03 15:27:47'),
(123, 2, 'contact_dpo', 'Data Protection Officer Email', 'text', 'contact', 105, '2025-11-03 15:27:47', '2025-11-03 15:27:47'),
(189, 3, 'hero_kicker', 'Hero Kicker Text', 'text', 'hero', 1, '2025-11-04 10:21:37', '2025-11-04 10:21:37'),
(190, 3, 'hero_title', 'Hero Title', 'text', 'hero', 2, '2025-11-04 10:21:37', '2025-11-04 10:21:37'),
(191, 3, 'hero_subtitle', 'Hero Subtitle', 'textarea', 'hero', 3, '2025-11-04 10:21:37', '2025-11-04 10:21:37'),
(192, 3, 'hero_description', 'Hero Description', 'textarea', 'hero', 4, '2025-11-04 10:21:37', '2025-11-04 10:21:37'),
(193, 3, 'hero_primary_label', 'Primary Button Label', 'text', 'hero', 5, '2025-11-04 10:21:37', '2025-11-04 10:21:37'),
(194, 3, 'hero_primary_url', 'Primary Button URL', 'text', 'hero', 6, '2025-11-04 10:21:37', '2025-11-04 10:21:37'),
(195, 3, 'hero_secondary_label', 'Secondary Button Label', 'text', 'hero', 7, '2025-11-04 10:21:37', '2025-11-04 10:21:37'),
(196, 3, 'hero_secondary_url', 'Secondary Button URL', 'text', 'hero', 8, '2025-11-04 10:21:37', '2025-11-04 10:21:37'),
(197, 3, 'stats_1_value', 'Stat 1 Value', 'text', 'stats', 10, '2025-11-04 10:21:37', '2025-11-04 10:21:37'),
(198, 3, 'stats_1_label', 'Stat 1 Label', 'text', 'stats', 11, '2025-11-04 10:21:37', '2025-11-04 10:21:37'),
(199, 3, 'stats_2_value', 'Stat 2 Value', 'text', 'stats', 12, '2025-11-04 10:21:37', '2025-11-04 10:21:37'),
(200, 3, 'stats_2_label', 'Stat 2 Label', 'text', 'stats', 13, '2025-11-04 10:21:37', '2025-11-04 10:21:37'),
(201, 3, 'stats_3_value', 'Stat 3 Value', 'text', 'stats', 14, '2025-11-04 10:21:37', '2025-11-04 10:21:37'),
(202, 3, 'stats_3_label', 'Stat 3 Label', 'text', 'stats', 15, '2025-11-04 10:21:37', '2025-11-04 10:21:37'),
(203, 3, 'mission_title', 'Mission Title', 'text', 'mission_vision', 20, '2025-11-04 10:21:37', '2025-11-04 10:21:37'),
(204, 3, 'mission_text', 'Mission Text', 'textarea', 'mission_vision', 21, '2025-11-04 10:21:37', '2025-11-04 10:21:37'),
(205, 3, 'vision_title', 'Vision Title', 'text', 'mission_vision', 22, '2025-11-04 10:21:37', '2025-11-04 10:21:37'),
(206, 3, 'vision_text', 'Vision Text', 'textarea', 'mission_vision', 23, '2025-11-04 10:21:37', '2025-11-04 10:21:37'),
(207, 3, 'values_title', 'Values Section Title', 'text', 'values', 30, '2025-11-04 10:21:37', '2025-11-04 10:21:37'),
(208, 3, 'values_intro', 'Values Introduction', 'textarea', 'values', 31, '2025-11-04 10:21:37', '2025-11-04 10:21:37'),
(209, 3, 'values_item_1_title', 'Value 1 Title', 'text', 'values', 32, '2025-11-04 10:21:37', '2025-11-04 10:21:37'),
(210, 3, 'values_item_1_text', 'Value 1 Text', 'textarea', 'values', 33, '2025-11-04 10:21:37', '2025-11-04 10:21:37'),
(211, 3, 'values_item_2_title', 'Value 2 Title', 'text', 'values', 34, '2025-11-04 10:21:37', '2025-11-04 10:21:37'),
(212, 3, 'values_item_2_text', 'Value 2 Text', 'textarea', 'values', 35, '2025-11-04 10:21:37', '2025-11-04 10:21:37'),
(213, 3, 'values_item_3_title', 'Value 3 Title', 'text', 'values', 36, '2025-11-04 10:21:37', '2025-11-04 10:21:37'),
(214, 3, 'values_item_3_text', 'Value 3 Text', 'textarea', 'values', 37, '2025-11-04 10:21:37', '2025-11-04 10:21:37'),
(215, 3, 'team_title', 'Team Section Title', 'text', 'team', 40, '2025-11-04 10:21:37', '2025-11-04 10:21:37'),
(216, 3, 'team_text', 'Team Description', 'textarea', 'team', 41, '2025-11-04 10:21:37', '2025-11-04 10:21:37'),
(217, 3, 'cta_title', 'CTA Title', 'text', 'cta', 50, '2025-11-04 10:21:37', '2025-11-04 10:21:37'),
(218, 3, 'cta_text', 'CTA Text', 'textarea', 'cta', 51, '2025-11-04 10:21:37', '2025-11-04 10:21:37'),
(219, 3, 'cta_primary_label', 'CTA Primary Button Label', 'text', 'cta', 52, '2025-11-04 10:21:37', '2025-11-04 10:21:37'),
(220, 3, 'cta_primary_url', 'CTA Primary Button URL', 'text', 'cta', 53, '2025-11-04 10:21:37', '2025-11-04 10:21:37'),
(221, 3, 'cta_secondary_label', 'CTA Secondary Button Label', 'text', 'cta', 54, '2025-11-04 10:21:37', '2025-11-04 10:21:37'),
(222, 3, 'cta_secondary_url', 'CTA Secondary Button URL', 'text', 'cta', 55, '2025-11-04 10:21:37', '2025-11-04 10:21:37');

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
(10, 5, NULL, 'test_sub@testsub.com', NULL, '2025-10-29 14:07:18', '$2y$10$qYcQxm8kHYBDjXc2Skh05erADJ5uxwub0DOHWojfcgYLEVfiKHwOi', NULL, 'test_sub', 'test_sub', NULL, NULL, 0, 0, NULL, NULL, NULL, '2025-10-29 14:06:07', '2025-11-03 12:45:22'),
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
  ADD KEY `idx_slug` (`slug`),
  ADD KEY `idx_active` (`is_active`);

--
-- Indexes for table `page_content`
--
ALTER TABLE `page_content`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_content` (`section_id`),
  ADD KEY `idx_section` (`section_id`);

--
-- Indexes for table `page_sections`
--
ALTER TABLE `page_sections`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_section` (`page_id`,`section_key`),
  ADD KEY `idx_page_order` (`page_id`,`display_order`);

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
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `contracts`
--
ALTER TABLE `contracts`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `listings`
--
ALTER TABLE `listings`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

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
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `pages`
--
ALTER TABLE `pages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `page_content`
--
ALTER TABLE `page_content`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=252;

--
-- AUTO_INCREMENT for table `page_sections`
--
ALTER TABLE `page_sections`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=223;

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
  ADD CONSTRAINT `page_content_ibfk_1` FOREIGN KEY (`section_id`) REFERENCES `page_sections` (`id`) ON DELETE CASCADE;

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
