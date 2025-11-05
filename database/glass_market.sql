-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 05, 2025 at 05:57 PM
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

-- --------------------------------------------------------

--
-- Table structure for table `companies`
--

CREATE TABLE `companies` (
  `id` bigint(20) NOT NULL,
  `name` varchar(255) NOT NULL,
  `logo` varchar(500) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `address_line1` varchar(255) DEFAULT NULL,
  `address_line2` varchar(255) DEFAULT NULL,
  `postal_code` varchar(20) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `country` varchar(100) DEFAULT NULL,
  `owner_user_id` bigint(20) DEFAULT NULL,
  `is_verified` tinyint(1) DEFAULT 0,
  `verified_at` timestamp NULL DEFAULT NULL,
  `company_type` varchar(50) NOT NULL,
  `website` varchar(255) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `companies`
--

INSERT INTO `companies` (`id`, `name`, `logo`, `description`, `address_line1`, `address_line2`, `postal_code`, `city`, `country`, `owner_user_id`, `is_verified`, `verified_at`, `company_type`, `website`, `phone`, `created_at`) VALUES
(1, 'Glass Market Verified', NULL, 'Official Glass Market verified listings', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 'Trader', NULL, NULL, '2025-11-05 10:09:15'),
(2, 'Trial Company 1', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 'Glass Recycle Plant', NULL, NULL, '2025-11-05 10:09:15'),
(3, 'Trial Company 2', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 'Trader', NULL, NULL, '2025-11-05 10:09:16'),
(4, 'Trial Company 3', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 'Glass Recycle Plant', NULL, NULL, '2025-11-05 10:09:16'),
(5, 'Trial Company 4', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 'Collection Company', NULL, NULL, '2025-11-05 10:09:17'),
(6, 'Trial Company 5', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 'Glass Recycle Plant', NULL, NULL, '2025-11-05 10:09:17'),
(7, 'Trial Company 6', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 'Glass Factory', NULL, NULL, '2025-11-05 10:09:17'),
(8, 'Trial Company 7', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 'Glass Recycle Plant', NULL, NULL, '2025-11-05 10:09:18'),
(9, 'Trial Company 8', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 'Glass Recycle Plant', NULL, NULL, '2025-11-05 10:09:18'),
(10, 'Trial Company 9', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 'Glass Factory', NULL, NULL, '2025-11-05 10:09:19'),
(11, 'Trial Company 10', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 'Glass Recycle Plant', NULL, NULL, '2025-11-05 10:09:19'),
(12, 'Trial Company 11', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 'Glass Factory', NULL, NULL, '2025-11-05 10:09:20'),
(13, 'Trial Company 12', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 'Glass Factory', NULL, NULL, '2025-11-05 10:09:20'),
(14, 'Trial Company 13', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 'Trader', NULL, NULL, '2025-11-05 10:09:20'),
(15, 'Trial Company 14', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 'Glass Recycle Plant', NULL, NULL, '2025-11-05 10:09:21'),
(16, 'Trial Company 15', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 'Collection Company', NULL, NULL, '2025-11-05 10:09:21'),
(17, 'Trial Company 16', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 'Collection Company', NULL, NULL, '2025-11-05 10:09:22'),
(18, 'Trial Company 17', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 'Glass Recycle Plant', NULL, NULL, '2025-11-05 10:09:22'),
(19, 'Trial Company 18', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 'Glass Recycle Plant', NULL, NULL, '2025-11-05 10:09:22'),
(20, 'Trial Company 19', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 'Glass Recycle Plant', NULL, NULL, '2025-11-05 10:09:23'),
(21, 'Trial Company 20', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 'Collection Company', NULL, NULL, '2025-11-05 10:09:23'),
(22, 'Trial Company 21', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 'Glass Factory', NULL, NULL, '2025-11-05 10:09:24'),
(23, 'Trial Company 22', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 'Collection Company', NULL, NULL, '2025-11-05 10:09:24'),
(24, 'Trial Company 23', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 'Collection Company', NULL, NULL, '2025-11-05 10:09:24'),
(25, 'Trial Company 24', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 'Trader', NULL, NULL, '2025-11-05 10:09:25'),
(26, 'Trial Company 25', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 'Glass Factory', NULL, NULL, '2025-11-05 10:09:25'),
(27, 'Trial Company 26', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 'Glass Recycle Plant', NULL, NULL, '2025-11-05 10:09:26'),
(28, 'Trial Company 27', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 'Glass Recycle Plant', NULL, NULL, '2025-11-05 10:09:26'),
(29, 'Trial Company 28', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 'Glass Factory', NULL, NULL, '2025-11-05 10:09:27'),
(30, 'Trial Company 29', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 'Glass Recycle Plant', NULL, NULL, '2025-11-05 10:09:27'),
(31, 'Trial Company 30', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 'Glass Recycle Plant', NULL, NULL, '2025-11-05 10:09:27'),
(32, 'Trial Company 31', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 'Glass Recycle Plant', NULL, NULL, '2025-11-05 10:09:28'),
(33, 'Trial Company 32', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 'Glass Factory', NULL, NULL, '2025-11-05 10:09:28'),
(34, 'Trial Company 33', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 'Trader', NULL, NULL, '2025-11-05 10:09:29'),
(35, 'Trial Company 34', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 'Glass Factory', NULL, NULL, '2025-11-05 10:09:29'),
(36, 'Trial Company 35', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 'Glass Recycle Plant', NULL, NULL, '2025-11-05 10:09:29'),
(37, 'Trial Company 36', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 'Glass Factory', NULL, NULL, '2025-11-05 10:09:30'),
(38, 'Trial Company 37', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 'Trader', NULL, NULL, '2025-11-05 10:09:30'),
(39, 'Trial Company 38', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 'Glass Recycle Plant', NULL, NULL, '2025-11-05 10:09:31'),
(40, 'Trial Company 39', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 'Collection Company', NULL, NULL, '2025-11-05 10:09:31'),
(41, 'Trial Company 40', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 'Glass Recycle Plant', NULL, NULL, '2025-11-05 10:09:32'),
(42, 'Trial Company 41', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 'Collection Company', NULL, NULL, '2025-11-05 10:09:32'),
(43, 'Trial Company 42', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 'Collection Company', NULL, NULL, '2025-11-05 10:09:32'),
(44, 'Trial Company 43', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 'Glass Factory', NULL, NULL, '2025-11-05 10:09:33'),
(45, 'Trial Company 44', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 'Trader', NULL, NULL, '2025-11-05 10:09:33'),
(46, 'Trial Company 45', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 'Collection Company', NULL, NULL, '2025-11-05 10:09:34'),
(47, 'Trial Company 46', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 'Collection Company', NULL, NULL, '2025-11-05 10:09:34'),
(48, 'Trial Company 47', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 'Trader', NULL, NULL, '2025-11-05 10:09:35'),
(49, 'Trial Company 48', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 'Trader', NULL, NULL, '2025-11-05 10:09:35'),
(50, 'Trial Company 49', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 'Glass Recycle Plant', NULL, NULL, '2025-11-05 10:09:35'),
(51, 'Trial Company 50', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 'Glass Factory', NULL, NULL, '2025-11-05 10:09:36'),
(52, 'Test_Company', NULL, 'Hey', 'van Randwijcklaan 47c', '', '', 'Amersfoort', 'Netherlands', 66, 0, NULL, 'Glass Recycle Plant', '', '', '2025-11-05 13:45:04');

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
  `user_id` bigint(20) DEFAULT NULL,
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

INSERT INTO `listings` (`id`, `location_id`, `company_id`, `user_id`, `side`, `glass_type`, `glass_type_other`, `quantity_tons`, `quantity_note`, `recycled`, `tested`, `storage_location`, `price_text`, `currency`, `created_at`, `valid_until`, `published`, `quality_notes`, `image_path`, `accepted_by_contract`) VALUES
(1, NULL, 1, 4, 'WTS', 'Amber Glass', NULL, 3797.00, 'Loose bulk', 'unknown', 'unknown', NULL, '€348/ton', 'EUR', '2025-11-05 10:09:15', '2026-02-03', 1, 'Premium grade glass material', 'image.png', 0),
(2, NULL, 1, 4, 'WTS', 'Blue Glass', NULL, 3413.00, 'Container loads', 'unknown', 'unknown', NULL, '£190/ton', 'GBP', '2025-11-05 10:09:15', '2026-02-03', 1, 'Long-term contract available', 'image.png', 0),
(3, NULL, 1, 4, 'WTB', 'Brown Glass', NULL, 3715.00, 'Weekly deliveries available', 'not_recycled', 'tested', NULL, '$163/ton', 'USD', '2025-11-05 10:09:15', '2026-02-03', 1, 'Bulk quantities available', 'image.png', 0),
(4, NULL, 1, 4, 'WTB', 'Green Glass', NULL, 4576.00, 'Regular monthly supply', 'unknown', 'unknown', NULL, '$238/ton', 'USD', '2025-11-05 10:09:15', '2026-02-03', 1, 'FOB pricing negotiable', 'image.png', 0),
(5, NULL, 1, 4, 'WTS', 'Blue Glass', NULL, 3513.00, 'One-time bulk sale', 'not_recycled', 'untested', NULL, '£287/ton', 'GBP', '2025-11-05 10:09:15', '2026-02-03', 1, 'Certified recycled content', 'image.png', 0),
(6, NULL, 1, 4, 'WTB', 'Blue Glass', NULL, 909.00, 'One-time bulk sale', 'not_recycled', 'tested', NULL, '$128/ton', 'USD', '2025-11-05 10:09:15', '2026-02-03', 1, 'FOB pricing negotiable', 'image.png', 0),
(7, NULL, 1, 4, 'WTB', 'Brown Glass', NULL, 1935.00, 'Ongoing contract', 'unknown', 'untested', NULL, '£458/ton', 'GBP', '2025-11-05 10:09:15', '2026-02-03', 1, 'Bulk quantities available', 'image.png', 0),
(8, NULL, 1, 4, 'WTS', 'Other Glass', NULL, 4283.00, 'Pre-sorted material', 'not_recycled', 'untested', NULL, '€443/ton', 'EUR', '2025-11-05 10:09:15', '2026-02-03', 1, 'Premium grade glass material', 'image.png', 0),
(9, NULL, 1, 4, 'WTB', 'Brown Glass', NULL, 4110.00, 'Container loads', 'not_recycled', 'tested', NULL, '£411/ton', 'GBP', '2025-11-05 10:09:15', '2026-02-03', 1, 'Industrial grade glass cullet', 'image.png', 0),
(10, NULL, 1, 4, 'WTB', 'Amber Glass', NULL, 183.00, 'Palletized', 'recycled', 'untested', NULL, '£290/ton', 'GBP', '2025-11-05 10:09:15', '2026-02-03', 1, 'Bulk quantities available', 'image.png', 0),
(11, NULL, 1, 4, 'WTS', 'Brown Glass', NULL, 4194.00, 'Big bags available', 'not_recycled', 'tested', NULL, '£298/ton', 'GBP', '2025-11-05 10:09:15', '2026-02-03', 1, 'Industrial grade glass cullet', 'image.png', 0),
(12, NULL, 1, 4, 'WTS', 'Brown Glass', NULL, 4802.00, 'Ongoing contract', 'not_recycled', 'untested', NULL, '€470/ton', 'EUR', '2025-11-05 10:09:15', '2026-02-03', 1, 'Certified recycled content', 'image.png', 0),
(13, NULL, 1, 4, 'WTS', 'Green Glass', NULL, 2164.00, 'Regular monthly supply', 'unknown', 'tested', NULL, '€254/ton', 'EUR', '2025-11-05 10:09:15', '2026-02-03', 1, 'Mixed color glass available', 'image.png', 0),
(14, NULL, 1, 4, 'WTS', 'Amber Glass', NULL, 4136.00, 'One-time bulk sale', 'not_recycled', 'unknown', NULL, '$298/ton', 'USD', '2025-11-05 10:09:15', '2026-02-03', 1, 'Long-term contract available', 'image.png', 0),
(15, NULL, 1, 4, 'WTB', 'Green Glass', NULL, 590.00, 'Ongoing contract', 'recycled', 'untested', NULL, '£500/ton', 'GBP', '2025-11-05 10:09:15', '2026-02-03', 1, 'Industrial grade glass cullet', 'image.png', 0),
(16, NULL, 1, 4, 'WTB', 'Green Glass', NULL, 804.00, 'Palletized', 'not_recycled', 'unknown', NULL, '$300/ton', 'USD', '2025-11-05 10:09:15', '2026-02-03', 1, 'High-quality recycled glass', 'image.png', 0),
(17, NULL, 1, 4, 'WTS', 'Green Glass', NULL, 3109.00, 'Palletized', 'unknown', 'unknown', NULL, '$59/ton', 'USD', '2025-11-05 10:09:15', '2026-02-03', 1, 'Mixed color glass available', 'image.png', 0),
(18, NULL, 1, 4, 'WTB', 'Green Glass', NULL, 553.00, 'Regular monthly supply', 'recycled', 'unknown', NULL, '£268/ton', 'GBP', '2025-11-05 10:09:15', '2026-02-03', 1, 'Ready for immediate pickup', 'image.png', 0),
(19, NULL, 1, 4, 'WTB', 'Blue Glass', NULL, 778.00, 'Regular monthly supply', 'unknown', 'tested', NULL, '€344/ton', 'EUR', '2025-11-05 10:09:15', '2026-02-03', 1, 'Mixed color glass available', 'image.png', 0),
(20, NULL, 1, 4, 'WTB', 'Clear Glass', NULL, 246.00, 'Palletized', 'not_recycled', 'tested', NULL, '€414/ton', 'EUR', '2025-11-05 10:09:15', '2026-02-03', 1, 'Industrial grade glass cullet', 'image.png', 0),
(21, NULL, 1, 4, 'WTB', 'Brown Glass', NULL, 350.00, 'Ongoing contract', 'unknown', 'untested', NULL, '$312/ton', 'USD', '2025-11-05 10:09:15', '2026-02-03', 1, 'Industrial grade glass cullet', 'image.png', 0),
(22, NULL, 1, 4, 'WTB', 'Brown Glass', NULL, 283.00, 'Loose bulk', 'recycled', 'tested', NULL, '$436/ton', 'USD', '2025-11-05 10:09:15', '2026-02-03', 1, 'Premium grade glass material', 'image.png', 0),
(23, NULL, 1, 4, 'WTS', 'Brown Glass', NULL, 2219.00, 'Loose bulk', 'unknown', 'tested', NULL, '€387/ton', 'EUR', '2025-11-05 10:09:15', '2026-02-03', 1, 'Clean sorted glass', 'image.png', 0),
(24, NULL, 1, 4, 'WTS', 'Other Glass', NULL, 4317.00, 'Palletized', 'recycled', 'unknown', NULL, '$477/ton', 'USD', '2025-11-05 10:09:15', '2026-02-03', 1, 'Bulk quantities available', 'image.png', 0),
(25, NULL, 1, 4, 'WTS', 'Clear Glass', NULL, 3240.00, 'Ongoing contract', 'not_recycled', 'unknown', NULL, '£156/ton', 'GBP', '2025-11-05 10:09:15', '2026-02-03', 1, 'Industrial grade glass cullet', 'image.png', 0),
(26, NULL, 1, 4, 'WTB', 'Other Glass', NULL, 1009.00, 'Big bags available', 'recycled', 'unknown', NULL, '$351/ton', 'USD', '2025-11-05 10:09:15', '2026-02-03', 1, 'Certified recycled content', 'image.png', 0),
(27, NULL, 1, 4, 'WTS', 'Other Glass', NULL, 4632.00, 'Ongoing contract', 'unknown', 'tested', NULL, '£85/ton', 'GBP', '2025-11-05 10:09:15', '2026-02-03', 1, 'Bulk quantities available', 'image.png', 0),
(28, NULL, 1, 4, 'WTS', 'Green Glass', NULL, 187.00, 'Palletized', 'unknown', 'tested', NULL, '£174/ton', 'GBP', '2025-11-05 10:09:15', '2026-02-03', 1, 'Certified recycled content', 'image.png', 0),
(29, NULL, 1, 4, 'WTS', 'Blue Glass', NULL, 2807.00, 'One-time bulk sale', 'not_recycled', 'tested', NULL, '$255/ton', 'USD', '2025-11-05 10:09:15', '2026-02-03', 1, 'Clean sorted glass', 'image.png', 0),
(30, NULL, 1, 4, 'WTB', 'Brown Glass', NULL, 1294.00, 'Big bags available', 'not_recycled', 'unknown', NULL, '€397/ton', 'EUR', '2025-11-05 10:09:15', '2026-02-03', 1, 'Ready for immediate pickup', 'image.png', 0),
(31, NULL, 1, 4, 'WTB', 'Amber Glass', NULL, 3657.00, 'Spot sale available', 'recycled', 'untested', NULL, '$323/ton', 'USD', '2025-11-05 10:09:15', '2026-02-03', 1, 'High-quality recycled glass', 'image.png', 0),
(32, NULL, 1, 4, 'WTB', 'Clear Glass', NULL, 4472.00, 'Pre-sorted material', 'unknown', 'untested', NULL, '£134/ton', 'GBP', '2025-11-05 10:09:15', '2026-02-03', 1, 'Premium grade glass material', 'image.png', 0),
(33, NULL, 1, 4, 'WTS', 'Brown Glass', NULL, 705.00, 'Spot sale available', 'recycled', 'tested', NULL, '€309/ton', 'EUR', '2025-11-05 10:09:15', '2026-02-03', 1, 'Premium grade glass material', 'image.png', 0),
(34, NULL, 1, 4, 'WTS', 'Green Glass', NULL, 2101.00, 'Big bags available', 'not_recycled', 'unknown', NULL, '€155/ton', 'EUR', '2025-11-05 10:09:15', '2026-02-03', 1, 'Premium grade glass material', 'image.png', 0),
(35, NULL, 1, 4, 'WTS', 'Amber Glass', NULL, 1538.00, 'One-time bulk sale', 'recycled', 'unknown', NULL, '€82/ton', 'EUR', '2025-11-05 10:09:15', '2026-02-03', 1, 'Premium grade glass material', 'image.png', 0),
(36, NULL, 1, 4, 'WTB', 'Brown Glass', NULL, 1327.00, 'One-time bulk sale', 'recycled', 'unknown', NULL, '$306/ton', 'USD', '2025-11-05 10:09:15', '2026-02-03', 1, 'Long-term contract available', 'image.png', 0),
(37, NULL, 1, 4, 'WTB', 'Other Glass', NULL, 393.00, 'Big bags available', 'unknown', 'untested', NULL, '£412/ton', 'GBP', '2025-11-05 10:09:15', '2026-02-03', 1, 'Clean sorted glass', 'image.png', 0),
(38, NULL, 1, 4, 'WTS', 'Green Glass', NULL, 2442.00, 'Container loads', 'recycled', 'unknown', NULL, '€106/ton', 'EUR', '2025-11-05 10:09:15', '2026-02-03', 1, 'Bulk quantities available', 'image.png', 0),
(39, NULL, 1, 4, 'WTS', 'Brown Glass', NULL, 2442.00, 'Loose bulk', 'unknown', 'unknown', NULL, '£348/ton', 'GBP', '2025-11-05 10:09:15', '2026-02-03', 1, 'Clean sorted glass', 'image.png', 0),
(40, NULL, 1, 4, 'WTS', 'Blue Glass', NULL, 3984.00, 'Big bags available', 'not_recycled', 'untested', NULL, '€500/ton', 'EUR', '2025-11-05 10:09:15', '2026-02-03', 1, 'Premium grade glass material', 'image.png', 0),
(41, NULL, 1, 4, 'WTS', 'Clear Glass', NULL, 18.00, 'Palletized', 'unknown', 'untested', NULL, '£114/ton', 'GBP', '2025-11-05 10:09:15', '2026-02-03', 1, 'FOB pricing negotiable', 'image.png', 0),
(42, NULL, 1, 4, 'WTB', 'Blue Glass', NULL, 2392.00, 'One-time bulk sale', 'recycled', 'unknown', NULL, '€92/ton', 'EUR', '2025-11-05 10:09:15', '2026-02-03', 1, 'Bulk quantities available', 'image.png', 0),
(43, NULL, 1, 4, 'WTS', 'Amber Glass', NULL, 765.00, 'Palletized', 'recycled', 'tested', NULL, '£177/ton', 'GBP', '2025-11-05 10:09:15', '2026-02-03', 1, 'Certified recycled content', 'image.png', 0),
(44, NULL, 1, 4, 'WTB', 'Brown Glass', NULL, 4415.00, 'Palletized', 'recycled', 'unknown', NULL, '$55/ton', 'USD', '2025-11-05 10:09:15', '2026-02-03', 1, 'Mixed color glass available', 'image.png', 0),
(45, NULL, 1, 4, 'WTB', 'Brown Glass', NULL, 3799.00, 'Big bags available', 'recycled', 'tested', NULL, '$161/ton', 'USD', '2025-11-05 10:09:15', '2026-02-03', 1, 'High-quality recycled glass', 'image.png', 0),
(46, NULL, 1, 4, 'WTB', 'Clear Glass', NULL, 1502.00, 'Big bags available', 'not_recycled', 'tested', NULL, '£86/ton', 'GBP', '2025-11-05 10:09:15', '2026-02-03', 1, 'Bulk quantities available', 'image.png', 0),
(47, NULL, 1, 4, 'WTB', 'Other Glass', NULL, 2242.00, 'Container loads', 'not_recycled', 'tested', NULL, '$266/ton', 'USD', '2025-11-05 10:09:15', '2026-02-03', 1, 'Premium grade glass material', 'image.png', 0),
(48, NULL, 1, 4, 'WTS', 'Green Glass', NULL, 1681.00, 'One-time bulk sale', 'unknown', 'unknown', NULL, '£144/ton', 'GBP', '2025-11-05 10:09:15', '2026-02-03', 1, 'Industrial grade glass cullet', 'image.png', 0),
(49, NULL, 1, 4, 'WTS', 'Brown Glass', NULL, 1416.00, 'Ongoing contract', 'not_recycled', 'unknown', NULL, '$185/ton', 'USD', '2025-11-05 10:09:15', '2026-02-03', 1, 'Bulk quantities available', 'image.png', 0),
(50, NULL, 1, 4, 'WTS', 'Clear Glass', NULL, 2398.00, 'Ongoing contract', 'recycled', 'unknown', NULL, '€92/ton', 'EUR', '2025-11-05 10:09:15', '2026-02-03', 1, 'Premium grade glass material', 'image.png', 0),
(51, NULL, 1, 4, 'WTS', 'Blue Glass', NULL, 2267.00, 'Big bags available', 'not_recycled', 'unknown', NULL, '$166/ton', 'USD', '2025-11-05 10:09:15', '2026-02-03', 1, 'Mixed color glass available', 'image.png', 0),
(52, NULL, 1, 4, 'WTS', 'Blue Glass', NULL, 2668.00, 'Big bags available', 'not_recycled', 'tested', NULL, '€356/ton', 'EUR', '2025-11-05 10:09:15', '2026-02-03', 1, 'Long-term contract available', 'image.png', 0),
(53, NULL, 1, 4, 'WTB', 'Clear Glass', NULL, 4457.00, 'Weekly deliveries available', 'not_recycled', 'tested', NULL, '$315/ton', 'USD', '2025-11-05 10:09:15', '2026-02-03', 1, 'Certified recycled content', 'image.png', 0),
(54, NULL, 1, 4, 'WTB', 'Clear Glass', NULL, 4529.00, 'Palletized', 'recycled', 'unknown', NULL, '€143/ton', 'EUR', '2025-11-05 10:09:15', '2026-02-03', 1, 'High-quality recycled glass', 'image.png', 0),
(55, NULL, 1, 4, 'WTB', 'Brown Glass', NULL, 1280.00, 'Spot sale available', 'unknown', 'untested', NULL, '£196/ton', 'GBP', '2025-11-05 10:09:15', '2026-02-03', 1, 'Certified recycled content', 'image.png', 0),
(56, NULL, 1, 4, 'WTB', 'Other Glass', NULL, 2058.00, 'Pre-sorted material', 'not_recycled', 'untested', NULL, '$333/ton', 'USD', '2025-11-05 10:09:15', '2026-02-03', 1, 'Long-term contract available', 'image.png', 0),
(57, NULL, 1, 4, 'WTB', 'Other Glass', NULL, 4362.00, 'Big bags available', 'unknown', 'unknown', NULL, '€429/ton', 'EUR', '2025-11-05 10:09:15', '2026-02-03', 1, 'Certified recycled content', 'image.png', 0),
(58, NULL, 1, 4, 'WTB', 'Brown Glass', NULL, 2926.00, 'Weekly deliveries available', 'recycled', 'untested', NULL, '$356/ton', 'USD', '2025-11-05 10:09:15', '2026-02-03', 1, 'Industrial grade glass cullet', 'image.png', 0),
(59, NULL, 1, 4, 'WTB', 'Clear Glass', NULL, 2634.00, 'Big bags available', 'not_recycled', 'tested', NULL, '€111/ton', 'EUR', '2025-11-05 10:09:15', '2026-02-03', 1, 'Bulk quantities available', 'image.png', 0),
(60, NULL, 1, 4, 'WTS', 'Amber Glass', NULL, 531.00, 'Ongoing contract', 'recycled', 'tested', NULL, '£85/ton', 'GBP', '2025-11-05 10:09:15', '2026-02-03', 1, 'Long-term contract available', 'image.png', 0),
(61, NULL, 1, 4, 'WTS', 'Amber Glass', NULL, 424.00, 'Regular monthly supply', 'unknown', 'untested', NULL, '€250/ton', 'EUR', '2025-11-05 10:09:15', '2026-02-03', 1, 'Ready for immediate pickup', 'image.png', 0),
(62, NULL, 1, 4, 'WTS', 'Other Glass', NULL, 3624.00, 'Pre-sorted material', 'not_recycled', 'untested', NULL, '$431/ton', 'USD', '2025-11-05 10:09:15', '2026-02-03', 1, 'FOB pricing negotiable', 'image.png', 0),
(63, NULL, 1, 4, 'WTB', 'Clear Glass', NULL, 3294.00, 'Loose bulk', 'recycled', 'unknown', NULL, '£328/ton', 'GBP', '2025-11-05 10:09:15', '2026-02-03', 1, 'Bulk quantities available', 'image.png', 0),
(64, NULL, 1, 4, 'WTS', 'Green Glass', NULL, 2029.00, 'One-time bulk sale', 'not_recycled', 'untested', NULL, '$349/ton', 'USD', '2025-11-05 10:09:15', '2026-02-03', 1, 'Certified recycled content', 'image.png', 0),
(65, NULL, 1, 4, 'WTB', 'Amber Glass', NULL, 2591.00, 'Loose bulk', 'not_recycled', 'tested', NULL, '$320/ton', 'USD', '2025-11-05 10:09:15', '2026-02-03', 1, 'Ready for immediate pickup', 'image.png', 0),
(66, NULL, 1, 4, 'WTS', 'Brown Glass', NULL, 2619.00, 'Weekly deliveries available', 'not_recycled', 'tested', NULL, '€227/ton', 'EUR', '2025-11-05 10:09:15', '2026-02-03', 1, 'Long-term contract available', 'image.png', 0),
(67, NULL, 1, 4, 'WTB', 'Blue Glass', NULL, 180.00, 'Spot sale available', 'not_recycled', 'untested', NULL, '€332/ton', 'EUR', '2025-11-05 10:09:15', '2026-02-03', 1, 'Industrial grade glass cullet', 'image.png', 0),
(68, NULL, 1, 4, 'WTS', 'Blue Glass', NULL, 4776.00, 'Container loads', 'unknown', 'tested', NULL, '€249/ton', 'EUR', '2025-11-05 10:09:15', '2026-02-03', 1, 'Clean sorted glass', 'image.png', 0),
(69, NULL, 1, 4, 'WTB', 'Blue Glass', NULL, 3054.00, 'Big bags available', 'unknown', 'tested', NULL, '€277/ton', 'EUR', '2025-11-05 10:09:15', '2026-02-03', 1, 'Industrial grade glass cullet', 'image.png', 0),
(70, NULL, 1, 4, 'WTS', 'Clear Glass', NULL, 3499.00, 'Weekly deliveries available', 'unknown', 'unknown', NULL, '$128/ton', 'USD', '2025-11-05 10:09:15', '2026-02-03', 1, 'High-quality recycled glass', 'image.png', 0),
(71, NULL, 1, 4, 'WTS', 'Brown Glass', NULL, 4191.00, 'Pre-sorted material', 'unknown', 'tested', NULL, '$413/ton', 'USD', '2025-11-05 10:09:15', '2026-02-03', 1, 'Mixed color glass available', 'image.png', 0),
(72, NULL, 1, 4, 'WTS', 'Other Glass', NULL, 1317.00, 'Pre-sorted material', 'not_recycled', 'tested', NULL, '€231/ton', 'EUR', '2025-11-05 10:09:15', '2026-02-03', 1, 'Industrial grade glass cullet', 'image.png', 0),
(73, NULL, 1, 4, 'WTS', 'Amber Glass', NULL, 1845.00, 'One-time bulk sale', 'not_recycled', 'untested', NULL, '£329/ton', 'GBP', '2025-11-05 10:09:15', '2026-02-03', 1, 'Certified recycled content', 'image.png', 0),
(74, NULL, 1, 4, 'WTS', 'Brown Glass', NULL, 2280.00, 'Container loads', 'not_recycled', 'tested', NULL, '$287/ton', 'USD', '2025-11-05 10:09:15', '2026-02-03', 1, 'Clean sorted glass', 'image.png', 0),
(75, NULL, 1, 4, 'WTB', 'Other Glass', NULL, 4401.00, 'Pre-sorted material', 'not_recycled', 'tested', NULL, '$442/ton', 'USD', '2025-11-05 10:09:15', '2026-02-03', 1, 'Premium grade glass material', 'image.png', 0),
(76, NULL, 1, 4, 'WTS', 'Clear Glass', NULL, 1159.00, 'Palletized', 'not_recycled', 'unknown', NULL, '$238/ton', 'USD', '2025-11-05 10:09:15', '2026-02-03', 1, 'FOB pricing negotiable', 'image.png', 0),
(77, NULL, 1, 4, 'WTB', 'Amber Glass', NULL, 4808.00, 'Pre-sorted material', 'not_recycled', 'tested', NULL, '$307/ton', 'USD', '2025-11-05 10:09:15', '2026-02-03', 1, 'High-quality recycled glass', 'image.png', 0),
(78, NULL, 1, 4, 'WTS', 'Clear Glass', NULL, 4600.00, 'Pre-sorted material', 'recycled', 'tested', NULL, '€276/ton', 'EUR', '2025-11-05 10:09:15', '2026-02-03', 1, 'Certified recycled content', 'image.png', 0),
(79, NULL, 1, 4, 'WTB', 'Amber Glass', NULL, 1938.00, 'Regular monthly supply', 'not_recycled', 'untested', NULL, '$474/ton', 'USD', '2025-11-05 10:09:15', '2026-02-03', 1, 'Premium grade glass material', 'image.png', 0),
(80, NULL, 1, 4, 'WTS', 'Other Glass', NULL, 2853.00, 'Ongoing contract', 'unknown', 'tested', NULL, '$142/ton', 'USD', '2025-11-05 10:09:15', '2026-02-03', 1, 'Ready for immediate pickup', 'image.png', 0),
(81, NULL, 1, 4, 'WTB', 'Amber Glass', NULL, 2909.00, 'Regular monthly supply', 'unknown', 'tested', NULL, '£76/ton', 'GBP', '2025-11-05 10:09:15', '2026-02-03', 1, 'Certified recycled content', 'image.png', 0),
(82, NULL, 1, 4, 'WTS', 'Clear Glass', NULL, 4401.00, 'Container loads', 'not_recycled', 'untested', NULL, '€60/ton', 'EUR', '2025-11-05 10:09:15', '2026-02-03', 1, 'Long-term contract available', 'image.png', 0),
(83, NULL, 1, 4, 'WTB', 'Brown Glass', NULL, 2138.00, 'Spot sale available', 'unknown', 'unknown', NULL, '£138/ton', 'GBP', '2025-11-05 10:09:15', '2026-02-03', 1, 'Certified recycled content', 'image.png', 0),
(84, NULL, 1, 4, 'WTB', 'Other Glass', NULL, 35.00, 'Container loads', 'unknown', 'tested', NULL, '$335/ton', 'USD', '2025-11-05 10:09:15', '2026-02-03', 1, 'FOB pricing negotiable', 'image.png', 0),
(85, NULL, 1, 4, 'WTS', 'Other Glass', NULL, 4804.00, 'Regular monthly supply', 'recycled', 'untested', NULL, '€229/ton', 'EUR', '2025-11-05 10:09:15', '2026-02-03', 1, 'High-quality recycled glass', 'image.png', 0),
(86, NULL, 1, 4, 'WTB', 'Brown Glass', NULL, 4824.00, 'Container loads', 'unknown', 'unknown', NULL, '£477/ton', 'GBP', '2025-11-05 10:09:15', '2026-02-03', 1, 'Mixed color glass available', 'image.png', 0),
(87, NULL, 1, 4, 'WTB', 'Blue Glass', NULL, 713.00, 'Weekly deliveries available', 'unknown', 'unknown', NULL, '£363/ton', 'GBP', '2025-11-05 10:09:15', '2026-02-03', 1, 'Bulk quantities available', 'image.png', 0),
(88, NULL, 1, 4, 'WTB', 'Amber Glass', NULL, 944.00, 'Regular monthly supply', 'not_recycled', 'tested', NULL, '£457/ton', 'GBP', '2025-11-05 10:09:15', '2026-02-03', 1, 'Certified recycled content', 'image.png', 0),
(89, NULL, 1, 4, 'WTB', 'Green Glass', NULL, 2100.00, 'Pre-sorted material', 'unknown', 'untested', NULL, '$288/ton', 'USD', '2025-11-05 10:09:15', '2026-02-03', 1, 'Industrial grade glass cullet', 'image.png', 0),
(90, NULL, 1, 4, 'WTB', 'Clear Glass', NULL, 345.00, 'Weekly deliveries available', 'recycled', 'unknown', NULL, '$364/ton', 'USD', '2025-11-05 10:09:15', '2026-02-03', 1, 'Certified recycled content', 'image.png', 0),
(91, NULL, 1, 4, 'WTB', 'Brown Glass', NULL, 1192.00, 'Spot sale available', 'recycled', 'unknown', NULL, '£73/ton', 'GBP', '2025-11-05 10:09:15', '2026-02-03', 1, 'Industrial grade glass cullet', 'image.png', 0),
(92, NULL, 1, 4, 'WTS', 'Green Glass', NULL, 1101.00, 'Spot sale available', 'recycled', 'unknown', NULL, '$467/ton', 'USD', '2025-11-05 10:09:15', '2026-02-03', 1, 'Premium grade glass material', 'image.png', 0),
(93, NULL, 1, 4, 'WTB', 'Amber Glass', NULL, 2624.00, 'One-time bulk sale', 'unknown', 'untested', NULL, '€225/ton', 'EUR', '2025-11-05 10:09:15', '2026-02-03', 1, 'Industrial grade glass cullet', 'image.png', 0),
(94, NULL, 1, 4, 'WTS', 'Amber Glass', NULL, 1771.00, 'One-time bulk sale', 'unknown', 'tested', NULL, '£450/ton', 'GBP', '2025-11-05 10:09:15', '2026-02-03', 1, 'Bulk quantities available', 'image.png', 0),
(95, NULL, 1, 4, 'WTB', 'Amber Glass', NULL, 47.00, 'Pre-sorted material', 'unknown', 'untested', NULL, '£159/ton', 'GBP', '2025-11-05 10:09:15', '2026-02-03', 1, 'Clean sorted glass', 'image.png', 0),
(96, NULL, 1, 4, 'WTB', 'Brown Glass', NULL, 1816.00, 'Loose bulk', 'recycled', 'untested', NULL, '€433/ton', 'EUR', '2025-11-05 10:09:15', '2026-02-03', 1, 'Long-term contract available', 'image.png', 0),
(97, NULL, 1, 4, 'WTS', 'Clear Glass', NULL, 1853.00, 'Ongoing contract', 'not_recycled', 'untested', NULL, '$416/ton', 'USD', '2025-11-05 10:09:15', '2026-02-03', 1, 'Industrial grade glass cullet', 'image.png', 0),
(98, NULL, 1, 4, 'WTB', 'Green Glass', NULL, 1533.00, 'One-time bulk sale', 'not_recycled', 'unknown', NULL, '£427/ton', 'GBP', '2025-11-05 10:09:15', '2026-02-03', 1, 'Certified recycled content', 'image.png', 0),
(99, NULL, 1, 4, 'WTS', 'Brown Glass', NULL, 4403.00, 'Ongoing contract', 'recycled', 'unknown', NULL, '£330/ton', 'GBP', '2025-11-05 10:09:15', '2026-02-03', 1, 'High-quality recycled glass', 'image.png', 0),
(100, NULL, 1, 4, 'WTS', 'Green Glass', NULL, 1276.00, 'Big bags available', 'recycled', 'untested', NULL, '$118/ton', 'USD', '2025-11-05 10:09:15', '2026-02-03', 1, 'Premium grade glass material', 'image.png', 0),
(102, NULL, NULL, 65, 'WTS', 'Green Glass', NULL, 23.00, 'Premium Green Bottle Glass Cullet – Clean and Ready for Melt', 'recycled', 'tested', NULL, '€145/ton', 'EUR', '2025-11-05 13:22:17', NULL, 1, 'ASAP', 'image.png', 0),
(104, NULL, 52, 66, 'WTS', 'Clear Cullet', NULL, 123.00, 'TEST!', 'not_recycled', 'untested', 'FSDF', '323', 'EUR', '2025-11-05 14:54:20', NULL, 1, '23', 'image.png', 0),
(105, NULL, 52, 66, 'WTS', 'Green Cullet', '', 23.00, '', 'unknown', 'unknown', '', '', 'EUR', '2025-11-05 15:23:43', NULL, 0, '', 'image.png', 0),
(107, NULL, NULL, 66, 'WTS', 'Green Cullet', '', 2.00, '', 'unknown', 'unknown', '', '', 'EUR', '2025-11-05 16:28:10', NULL, 1, '', 'image.png', 0);

-- --------------------------------------------------------

--
-- Table structure for table `listing_images`
--

CREATE TABLE `listing_images` (
  `id` bigint(20) NOT NULL,
  `listing_id` bigint(20) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `is_main` tinyint(1) DEFAULT 0,
  `display_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `listing_images`
--

INSERT INTO `listing_images` (`id`, `listing_id`, `image_path`, `is_main`, `display_order`, `created_at`) VALUES
(20, 102, 'uploads/listings/listing_102_1762348937_0.jpg', 1, 0, '2025-11-05 13:22:17'),
(21, 104, 'uploads/listings/listing_104_1762354460_0.png', 0, 0, '2025-11-05 14:54:20'),
(22, 104, 'uploads/listings/listing_104_1762354460_1.png', 0, 1, '2025-11-05 14:54:20'),
(23, 104, 'uploads/listings/listing_104_1762354460_2.png', 1, 2, '2025-11-05 14:54:20'),
(25, 107, 'uploads/listings/listing_107_1762360090_0.jpg', 1, 0, '2025-11-05 16:28:10');

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
(5, 10, 'tr_HwHjPAt8YXQsH2AotGUGJ', 9.99, 'open', 1, '2025-11-03 13:42:41', NULL, NULL),
(6, 65, 'tr_T2ywwKTcf2zjjpMtM7ZGJ', 9.99, 'open', 1, '2025-11-05 10:30:24', NULL, NULL),
(7, 65, 'tr_eBrACDSnBdv6eUUBN7ZGJ', 9.99, 'open', 1, '2025-11-05 10:30:26', NULL, NULL),
(8, 65, 'tr_KjrgAk34uZHJGHsYN7ZGJ', 9.99, 'paid', 1, '2025-11-05 10:30:31', '2025-11-05 10:32:15', '2025-11-05 10:32:15'),
(9, 65, 'tr_crMErBgHvFpRcrW9a7ZGJ', 9.99, 'open', 1, '2025-11-05 10:32:29', NULL, NULL);

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
-- Table structure for table `push_notifications`
--

CREATE TABLE `push_notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `body` text DEFAULT NULL,
  `url` varchar(500) DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT 0,
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
-- Table structure for table `saved_listings`
--

CREATE TABLE `saved_listings` (
  `id` bigint(20) NOT NULL,
  `user_id` bigint(20) NOT NULL,
  `listing_id` bigint(20) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `saved_listings`
--

INSERT INTO `saved_listings` (`id`, `user_id`, `listing_id`, `created_at`) VALUES
(2, 65, 1, '2025-11-05 13:22:33'),
(3, 66, 102, '2025-11-05 14:17:57');

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
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `notify_new_listings` tinyint(1) DEFAULT 1 COMMENT 'Email notification for new listings',
  `notify_account_updates` tinyint(1) DEFAULT 1 COMMENT 'Email notification for account updates',
  `notify_newsletter` tinyint(1) DEFAULT 0 COMMENT 'Receive newsletter emails',
  `push_new_listings` tinyint(1) DEFAULT 0 COMMENT 'Push notification for new listings',
  `push_messages` tinyint(1) DEFAULT 0 COMMENT 'Push notification for messages'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `company_id`, `created_by`, `email`, `avatar`, `email_verified_at`, `password`, `remember_token`, `name`, `company_name`, `phone`, `roles`, `is_admin`, `is_approved`, `approved_at`, `approved_by`, `last_login`, `created_at`, `updated_at`, `notify_new_listings`, `notify_account_updates`, `notify_newsletter`, `push_new_listings`, `push_messages`) VALUES
(4, NULL, NULL, 'admin@glassmarket.com', NULL, '2025-10-27 13:08:53', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, 'Admin', NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, '2025-10-27 12:29:44', '2025-10-29 14:40:57', 1, 1, 0, 0, 0),
(5, NULL, NULL, 'colinpoort@hotmail.com', '/glass-market/public/uploads/avatars/avatar_5_1761660214.jpg', '2025-10-27 13:14:36', '$2y$10$3le7iqImsFG85PwGuK60i.KcZpRP0wxk9MoHF3iBFXjpRx3oaJxpq', '30804072962d6ccea488d67d676f56a61fda774d49489c2114982b317eef78c9', 'Cornelis Wim Poort', 'CP Company', NULL, NULL, 0, 0, NULL, NULL, NULL, '2025-10-27 13:13:58', '2025-11-04 11:27:20', 1, 1, 0, 0, 0),
(6, NULL, NULL, 'gijs@gmail.com', NULL, '2025-10-27 13:33:45', '$2y$10$qiL225fIXPba/gq8Z/mZwOGOaXBiYU8lmIjQzBMy4vn6mZ4X0fWoa', NULL, 'Gijsje Radijsje', NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, '2025-10-27 13:33:06', '2025-10-27 13:33:45', 1, 1, 0, 0, 0),
(7, NULL, NULL, 'Kaj@gmail.com', NULL, '2025-10-27 14:04:47', '$2y$10$e3IsWujghleCxhlVwmlOwOph2Ijlq3gOfOMc8EcTxIUJVkZL6wpdq', NULL, 'Kaj', NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, '2025-10-27 13:56:22', '2025-10-27 14:04:47', 1, 1, 0, 0, 0),
(9, NULL, NULL, 'colinpoort12@hotmail.com', NULL, NULL, '$2y$10$5LuxQblwc2OqwTw7tzQU7eDA3g1QV4L/.3rn6XsQSn5301dSLpcQ.', NULL, 'Colin', NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, '2025-10-28 14:15:57', '2025-10-29 13:40:57', 1, 1, 0, 0, 0),
(10, NULL, NULL, 'test_sub@testsub.com', NULL, '2025-10-29 14:07:18', '$2y$10$qYcQxm8kHYBDjXc2Skh05erADJ5uxwub0DOHWojfcgYLEVfiKHwOi', NULL, 'test_sub!', 'test_sub', NULL, NULL, 0, 0, NULL, NULL, NULL, '2025-10-29 14:06:07', '2025-11-05 08:40:12', 1, 1, 0, 0, 0),
(11, NULL, NULL, 'testaccount@gmail.com', '/glass-market/public/uploads/avatars/avatar_11_1761905779.jpg', '2025-10-31 09:40:18', '$2y$10$9SuQyKcOKfwc5v5Nxep.tOeqwCztfV3ikKI/cQHFRq2Sx.9IHj.h.', NULL, 'testaccount', 'testaccount', NULL, NULL, 0, 0, NULL, NULL, NULL, '2025-10-31 09:39:51', '2025-10-31 10:16:19', 1, 1, 0, 0, 0),
(12, NULL, NULL, 'testsa@gmail.com', NULL, '2025-11-04 18:15:36', '$2y$10$d2m/MmsVZILbXCCy5u2Q.euUrb5RwIMDYg6pBhU0c.csJBCNilXka', NULL, 'testsa', NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, '2025-11-04 18:15:36', NULL, 1, 1, 0, 0, 0),
(13, NULL, NULL, 'asdf@gmail.com', NULL, '2025-11-05 09:53:19', '$2y$10$c97289zCDVnWSzwqly7BFug7aQ3rJioiQs4Z0jE4lmXGvqFwl8MUa', NULL, 'asdf', NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, '2025-11-05 09:53:19', NULL, 1, 1, 0, 0, 0),
(14, 2, NULL, 'trial1@trial.test', NULL, NULL, '$2y$12$24IcOjWiFllc4mG4Ld5CzetsizrCo2jzazt2wF8Po/VgG1Bfv3QXq', NULL, 'Trial User 01', NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, '2025-11-05 10:09:15', NULL, 1, 1, 0, 0, 0),
(15, 3, NULL, 'trial2@trial.test', NULL, NULL, '$2y$12$Va42Hx10AOfROZ1yNJ6w3OI7l/5LFf0oBa14LF1jeJYzNJH6LR7ZW', NULL, 'Trial User 02', NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, '2025-11-05 10:09:16', NULL, 1, 1, 0, 0, 0),
(16, 4, NULL, 'trial3@trial.test', NULL, NULL, '$2y$12$ZaGgMtl4d1Q5N1KCW94a1uBNu/cDFg4iEB/JU3elnKzcvx2lLnXw6', NULL, 'Trial User 03', NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, '2025-11-05 10:09:16', NULL, 1, 1, 0, 0, 0),
(17, 5, NULL, 'trial4@trial.test', NULL, NULL, '$2y$12$DmwVHRpO6ftSL1YJrkNHUeZs6bYPMe9FvW31NFXkMS54dg6q66V3S', NULL, 'Trial User 04', NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, '2025-11-05 10:09:17', NULL, 1, 1, 0, 0, 0),
(18, 6, NULL, 'trial5@trial.test', NULL, NULL, '$2y$12$zr5mgtwTFOdPxCo4tj3Lgui3ucYA3obIPn3GuzMTTAHO8YxBg.XkO', NULL, 'Trial User 05', NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, '2025-11-05 10:09:17', NULL, 1, 1, 0, 0, 0),
(19, 7, NULL, 'trial6@trial.test', NULL, NULL, '$2y$12$IIeuPOt24DtL0OHAQgCJJ.CCINrDtl0hPN8x9SS2Q9TLh4ESU9XB6', NULL, 'Trial User 06', NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, '2025-11-05 10:09:17', NULL, 1, 1, 0, 0, 0),
(20, 8, NULL, 'trial7@trial.test', NULL, NULL, '$2y$12$1wtZlScRYBawcX9xgSeQ1OzpIxxjbQXhludz8bbKuj03zB2AlDK.y', NULL, 'Trial User 07', NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, '2025-11-05 10:09:18', NULL, 1, 1, 0, 0, 0),
(21, 9, NULL, 'trial8@trial.test', NULL, NULL, '$2y$12$s1KreuvYiKeFjCEIC1pgwOM2jVwITn.hPz4z12obv05xe.JB65uGO', NULL, 'Trial User 08', NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, '2025-11-05 10:09:18', NULL, 1, 1, 0, 0, 0),
(22, 10, NULL, 'trial9@trial.test', NULL, NULL, '$2y$12$tcRZdHTyn9PWmXXTVkZhIuKdnvS9K0JmuKVKxV0zB4xoLrLPpO09i', NULL, 'Trial User 09', NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, '2025-11-05 10:09:19', NULL, 1, 1, 0, 0, 0),
(23, 11, NULL, 'trial10@trial.test', NULL, NULL, '$2y$12$PaRBzdVTcLb1Ul/oKh/23O0DYX4hYHUdGSAj5d2QeazVgJNNJFkG.', NULL, 'Trial User 10', NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, '2025-11-05 10:09:19', NULL, 1, 1, 0, 0, 0),
(24, 12, NULL, 'trial11@trial.test', NULL, NULL, '$2y$12$Xx6RdSIRB1Dp.iHpLMP2SOLdlWqL4jmdS/O5o3csLUkHjXCqnVLte', NULL, 'Trial User 11', NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, '2025-11-05 10:09:20', NULL, 1, 1, 0, 0, 0),
(25, 13, NULL, 'trial12@trial.test', NULL, NULL, '$2y$12$uqQXPZcvWw/qBRgoU6CIiey3EZI2ed8F0Lz/vfvDNHuBY4CBHICTe', NULL, 'Trial User 12', NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, '2025-11-05 10:09:20', NULL, 1, 1, 0, 0, 0),
(26, 14, NULL, 'trial13@trial.test', NULL, NULL, '$2y$12$iewHQv8eTRAd0BRgCGpqLOFJL/fptl1VtbmjpwtmybiRF5gumE5FW', NULL, 'Trial User 13', NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, '2025-11-05 10:09:20', NULL, 1, 1, 0, 0, 0),
(27, 15, NULL, 'trial14@trial.test', NULL, NULL, '$2y$12$UcE15iFTPjLjKr95S57M/ueNRcAgHjmv0nRu7OY6umjK4BtMcl9CC', NULL, 'Trial User 14', NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, '2025-11-05 10:09:21', NULL, 1, 1, 0, 0, 0),
(28, 16, NULL, 'trial15@trial.test', NULL, NULL, '$2y$12$H6IOUzZK/tEz6V/jQ1QlS.Vau5GcZsMwxbK7aqShf9D4IhfFOLS1O', NULL, 'Trial User 15', NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, '2025-11-05 10:09:21', NULL, 1, 1, 0, 0, 0),
(29, 17, NULL, 'trial16@trial.test', NULL, NULL, '$2y$12$UZotye75DxJSzdukFZtPButoDkw4lriQVdleZHYIbyDxw/KR0IdDC', NULL, 'Trial User 16', NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, '2025-11-05 10:09:22', NULL, 1, 1, 0, 0, 0),
(30, 18, NULL, 'trial17@trial.test', NULL, NULL, '$2y$12$e6grkfLVggc/4Ro2l39t3uZKoeSA3vTU0WfDYRLvqmNbuvxeflqve', NULL, 'Trial User 17', NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, '2025-11-05 10:09:22', NULL, 1, 1, 0, 0, 0),
(31, 19, NULL, 'trial18@trial.test', NULL, NULL, '$2y$12$Tl5swxQk3jhNxaSxfpJh0uJAigIWPqvcnOz6ajZD4fZ.6OqhmD2EG', NULL, 'Trial User 18', NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, '2025-11-05 10:09:22', NULL, 1, 1, 0, 0, 0),
(32, 20, NULL, 'trial19@trial.test', NULL, NULL, '$2y$12$JFQOJSoaSZoa7ejksuDmVuybDkwdL.eB6mCgLorQS4j2u6vmJCc56', NULL, 'Trial User 19', NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, '2025-11-05 10:09:23', NULL, 1, 1, 0, 0, 0),
(33, 21, NULL, 'trial20@trial.test', NULL, NULL, '$2y$12$bA22sCqprVcaXAowacDXa.143yhZnYHdERIlSdnzKPD3dWB/tlBpq', NULL, 'Trial User 20', NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, '2025-11-05 10:09:23', NULL, 1, 1, 0, 0, 0),
(34, 22, NULL, 'trial21@trial.test', NULL, NULL, '$2y$12$1EeLP.HE9S0v9m/7lXeJAOtifaN8BDx6.x54aLd/0nNcWvV5Fxp2q', NULL, 'Trial User 21', NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, '2025-11-05 10:09:24', NULL, 1, 1, 0, 0, 0),
(35, 23, NULL, 'trial22@trial.test', NULL, NULL, '$2y$12$fJRyTSdPJu7ranKFOY6UouZ7YLX7wm7HeCpk2SymykSbNcjC0MYV6', NULL, 'Trial User 22', NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, '2025-11-05 10:09:24', NULL, 1, 1, 0, 0, 0),
(36, 24, NULL, 'trial23@trial.test', NULL, NULL, '$2y$12$.1uLb0PdzgbInoPBGR20AusiACpvQOKd4b7bWLgbUE65/I9Zj6tn2', NULL, 'Trial User 23', NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, '2025-11-05 10:09:24', NULL, 1, 1, 0, 0, 0),
(37, 25, NULL, 'trial24@trial.test', NULL, NULL, '$2y$12$2uKj8JEKjUZLC3QgYFzmvetahG1HmC530tO.dna6fow.zH.ZU3VUm', NULL, 'Trial User 24', NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, '2025-11-05 10:09:25', NULL, 1, 1, 0, 0, 0),
(38, 26, NULL, 'trial25@trial.test', NULL, NULL, '$2y$12$3P5cxI8oxmbW.E8Zjeoaoee9MvWQdegtRbSuNGTHPqCUcNpaYyN1G', NULL, 'Trial User 25', NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, '2025-11-05 10:09:25', NULL, 1, 1, 0, 0, 0),
(39, 27, NULL, 'trial26@trial.test', NULL, NULL, '$2y$12$qEmofqMqmRdS66hZmmCB6u6tqjS/gZqYUnCFcWeFqb1Kx/Ytp3Knq', NULL, 'Trial User 26', NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, '2025-11-05 10:09:26', NULL, 1, 1, 0, 0, 0),
(40, 28, NULL, 'trial27@trial.test', NULL, NULL, '$2y$12$W6INqa.x3Aqk8ivvkI/g2e0gTQJV7qwLQJ3VLlMvFyykz5N4LIX7q', NULL, 'Trial User 27', NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, '2025-11-05 10:09:26', NULL, 1, 1, 0, 0, 0),
(41, 29, NULL, 'trial28@trial.test', NULL, NULL, '$2y$12$slhBvj6ReMI/.Wo67oWRJuvcEbb4LPWQ89NyY81BiW/xonnOoWKH2', NULL, 'Trial User 28', NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, '2025-11-05 10:09:27', NULL, 1, 1, 0, 0, 0),
(42, 30, NULL, 'trial29@trial.test', NULL, NULL, '$2y$12$j9KuzbCu3FbRBXFYTUqLPu9JLPJ.3AqQ4lBqTkybmnUfTDFdJBNTe', NULL, 'Trial User 29', NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, '2025-11-05 10:09:27', NULL, 1, 1, 0, 0, 0),
(43, 31, NULL, 'trial30@trial.test', NULL, NULL, '$2y$12$re/26T638HwAJD7fIe9/Resijnqy1Bu7a.z8ZEu5Rd/UIkCOeAK2i', NULL, 'Trial User 30', NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, '2025-11-05 10:09:27', NULL, 1, 1, 0, 0, 0),
(44, 32, NULL, 'trial31@trial.test', NULL, NULL, '$2y$12$mbNa2e1.JaYR0f1RrIVgHeR8spA8j1ltyqBY3iSbUbYlfCDYlrPM6', NULL, 'Trial User 31', NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, '2025-11-05 10:09:28', NULL, 1, 1, 0, 0, 0),
(45, 33, NULL, 'trial32@trial.test', NULL, NULL, '$2y$12$yXVpQz9WnvSymiX6I8WVwuwnEmbooA0oj/n.NXXPAwsTTCbx/UPx2', NULL, 'Trial User 32', NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, '2025-11-05 10:09:28', NULL, 1, 1, 0, 0, 0),
(46, 34, NULL, 'trial33@trial.test', NULL, NULL, '$2y$12$sRJd0nAHDk.snZLN9g068.JVgjn9i9qbytKaddyrtaUgGB4DrqRa6', NULL, 'Trial User 33', NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, '2025-11-05 10:09:29', NULL, 1, 1, 0, 0, 0),
(47, 35, NULL, 'trial34@trial.test', NULL, NULL, '$2y$12$NmIyNpfCOk/7JUYoVPDHHOKp9L9zW/Yc3BjhfKOOXLQvCeDhuQ7RG', NULL, 'Trial User 34', NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, '2025-11-05 10:09:29', NULL, 1, 1, 0, 0, 0),
(48, 36, NULL, 'trial35@trial.test', NULL, NULL, '$2y$12$A..7d8TEJSCKmnUrdogLtuvdKaBg0xi8GF.s8a5o4nXy30Fj/3y3G', NULL, 'Trial User 35', NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, '2025-11-05 10:09:29', NULL, 1, 1, 0, 0, 0),
(49, 37, NULL, 'trial36@trial.test', NULL, NULL, '$2y$12$JfOQdKnOMjPvHg9C4Rp5JuZ/W/Lzs30KZYbCRPwwNssiE6Osh/GJi', NULL, 'Trial User 36', NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, '2025-11-05 10:09:30', NULL, 1, 1, 0, 0, 0),
(50, 38, NULL, 'trial37@trial.test', NULL, NULL, '$2y$12$6DGRdUUhTvMw3dJap61pR.pjD6YTttOuCEE05DesO/PKnmgNAEF/a', NULL, 'Trial User 37', NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, '2025-11-05 10:09:30', NULL, 1, 1, 0, 0, 0),
(51, 39, NULL, 'trial38@trial.test', NULL, NULL, '$2y$12$hJBSO0sA6pysP0N8xiE4I.UIrwlKYqBX5IS8iz1D0/no6SiSZGbDS', NULL, 'Trial User 38', NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, '2025-11-05 10:09:31', NULL, 1, 1, 0, 0, 0),
(52, 40, NULL, 'trial39@trial.test', NULL, NULL, '$2y$12$iLlXwMYB7cRFZgZpG8s1A.hfa3mzW4GOnY.T0OC7dGL1KSx1JOMUu', NULL, 'Trial User 39', NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, '2025-11-05 10:09:31', NULL, 1, 1, 0, 0, 0),
(53, 41, NULL, 'trial40@trial.test', NULL, NULL, '$2y$12$lc.gAOEehsa0JhIOwdg4..KfrdwMJf.8h1SdCVGTIE.8kevlHSy1m', NULL, 'Trial User 40', NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, '2025-11-05 10:09:32', NULL, 1, 1, 0, 0, 0),
(54, 42, NULL, 'trial41@trial.test', NULL, NULL, '$2y$12$o2G2e5LhRJn1cimuSeZ1o.RNrmvu30LibfCbuxbgX8/kowr.VOgSm', NULL, 'Trial User 41', NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, '2025-11-05 10:09:32', NULL, 1, 1, 0, 0, 0),
(55, 43, NULL, 'trial42@trial.test', NULL, NULL, '$2y$12$eWwC0SsXGNIXr7X3jIG.TeYDcZsn32D.CNkpxo.AbLGZKmSK0vt0G', NULL, 'Trial User 42', NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, '2025-11-05 10:09:32', NULL, 1, 1, 0, 0, 0),
(56, 44, NULL, 'trial43@trial.test', NULL, NULL, '$2y$12$VpudpDhDJnqHQgr2sZ.ineQ1TfPNIqOvtQUhDKgyyOlKMglImNfea', NULL, 'Trial User 43', NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, '2025-11-05 10:09:33', NULL, 1, 1, 0, 0, 0),
(57, 45, NULL, 'trial44@trial.test', NULL, NULL, '$2y$12$H.4Y5OzkbmCXRo/bFLR7leAI8vOfwHYyPDlXB9gXWcKtA2P9tIwH2', NULL, 'Trial User 44', NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, '2025-11-05 10:09:33', NULL, 1, 1, 0, 0, 0),
(58, 46, NULL, 'trial45@trial.test', NULL, NULL, '$2y$12$tiIkN/tg8LZj2EFSZ01KtuGUQFqT2ai7dYgFlWWFFMBib.VM7GCOy', NULL, 'Trial User 45', NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, '2025-11-05 10:09:34', NULL, 1, 1, 0, 0, 0),
(59, 47, NULL, 'trial46@trial.test', NULL, NULL, '$2y$12$WBtPRHP3oVj.LDJ2ZDeJCeErXvQ..CE.JWO8VnRuzq2S402EPT/72', NULL, 'Trial User 46', NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, '2025-11-05 10:09:34', NULL, 1, 1, 0, 0, 0),
(60, 48, NULL, 'trial47@trial.test', NULL, NULL, '$2y$12$fPnTNfEUvrPIXyDizMWWq.E8CZEEFK8G0lMVxGC/yoZ4PhadX0.TW', NULL, 'Trial User 47', NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, '2025-11-05 10:09:35', NULL, 1, 1, 0, 0, 0),
(61, 49, NULL, 'trial48@trial.test', NULL, NULL, '$2y$12$NZBgWAwsnJdyNAs3r0rg5OLjSCwYcFQmXljYN0WX78foETjvJgiJe', NULL, 'Trial User 48', NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, '2025-11-05 10:09:35', NULL, 1, 1, 0, 0, 0),
(62, 50, NULL, 'trial49@trial.test', NULL, NULL, '$2y$12$XJxjvj/XoUFtaFWhR7JgFuf7JmfgpSSEH9Ii7e6RpF0pmCAfvyfG6', NULL, 'Trial User 49', NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, '2025-11-05 10:09:35', NULL, 1, 1, 0, 0, 0),
(63, 51, NULL, 'trial50@trial.test', NULL, NULL, '$2y$12$D1gP.NMwUBNAblRnWG/51OPTzma7O4qnZKv5gAcgjG86WA0CvQeoK', NULL, 'Trial User 50', NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, '2025-11-05 10:09:36', NULL, 1, 1, 0, 0, 0),
(64, NULL, NULL, 'jarrin@dmg.nu', NULL, '2025-11-05 10:25:39', '$2y$10$qBsoQVo5NG2JL6W2V1rNpe06EwLaAR/3WucJrlZshy.Q1lFd09h8C', NULL, 'Zeker', 'Je moeder', NULL, NULL, 0, 0, NULL, NULL, NULL, '2025-11-05 10:25:39', NULL, 1, 1, 0, 0, 0),
(65, NULL, NULL, 'testjarrin@gmail.com', '/uploads/avatars/avatar_65_1762349169.jpg', '2025-11-05 10:27:20', '$2y$10$BHuXPgqXXYExKh.SyOspKeaY/xOQ0hYp5tnDF6ztQqJc433Kd5gKK', NULL, 'Zeker!', 'Je moeder', NULL, NULL, 0, 0, NULL, NULL, NULL, '2025-11-05 10:27:20', '2025-11-05 13:26:09', 1, 1, 0, 0, 0),
(66, 52, NULL, 'Test_Company@gmail.com', '/uploads/avatars/avatar_66_1762350855.jpg', '2025-11-05 13:45:04', '$2y$10$RRXcxWB1uzPGeqhIqtv7Bele8LT9tvt5nbPyFSH8ZBRun2U984a4W', NULL, 'glasmarket', 'Test_Company', NULL, NULL, 0, 0, NULL, NULL, NULL, '2025-11-05 13:45:04', '2025-11-05 13:54:15', 1, 1, 0, 0, 0);

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
(7, 11, '2025-10-31', '2025-12-01', 0, 1, '2025-10-31 09:42:51', NULL),
(8, 12, '2025-11-04', '2026-02-04', 1, 1, '2025-11-04 18:15:36', NULL),
(9, 12, '2025-11-04', '2026-02-04', 1, 1, '2025-11-04 18:20:42', NULL),
(10, 13, '2025-11-05', '2026-02-05', 1, 1, '2025-11-05 09:53:19', NULL),
(11, 14, '2025-11-05', '2025-11-19', 1, 1, '2025-11-05 10:09:15', NULL),
(12, 15, '2025-11-05', '2025-11-19', 1, 1, '2025-11-05 10:09:16', NULL),
(13, 16, '2025-11-05', '2025-11-19', 1, 1, '2025-11-05 10:09:16', NULL),
(14, 17, '2025-11-05', '2025-11-19', 1, 1, '2025-11-05 10:09:17', NULL),
(15, 18, '2025-11-05', '2025-11-19', 1, 1, '2025-11-05 10:09:17', NULL),
(16, 19, '2025-11-05', '2025-11-19', 1, 1, '2025-11-05 10:09:17', NULL),
(17, 20, '2025-11-05', '2025-11-19', 1, 1, '2025-11-05 10:09:18', NULL),
(18, 21, '2025-11-05', '2025-11-19', 1, 1, '2025-11-05 10:09:18', NULL),
(19, 22, '2025-11-05', '2025-11-19', 1, 1, '2025-11-05 10:09:19', NULL),
(20, 23, '2025-11-05', '2025-11-19', 1, 1, '2025-11-05 10:09:19', NULL),
(21, 24, '2025-11-05', '2025-11-19', 1, 1, '2025-11-05 10:09:20', NULL),
(22, 25, '2025-11-05', '2025-11-19', 1, 1, '2025-11-05 10:09:20', NULL),
(23, 26, '2025-11-05', '2025-11-19', 1, 1, '2025-11-05 10:09:20', NULL),
(24, 27, '2025-11-05', '2025-11-19', 1, 1, '2025-11-05 10:09:21', NULL),
(25, 28, '2025-11-05', '2025-11-19', 1, 1, '2025-11-05 10:09:21', NULL),
(26, 29, '2025-11-05', '2025-11-19', 1, 1, '2025-11-05 10:09:22', NULL),
(27, 30, '2025-11-05', '2025-11-19', 1, 1, '2025-11-05 10:09:22', NULL),
(28, 31, '2025-11-05', '2025-11-19', 1, 1, '2025-11-05 10:09:22', NULL),
(29, 32, '2025-11-05', '2025-11-19', 1, 1, '2025-11-05 10:09:23', NULL),
(30, 33, '2025-11-05', '2025-11-19', 1, 1, '2025-11-05 10:09:23', NULL),
(31, 34, '2025-11-05', '2025-11-19', 1, 1, '2025-11-05 10:09:24', NULL),
(32, 35, '2025-11-05', '2025-11-19', 1, 1, '2025-11-05 10:09:24', NULL),
(33, 36, '2025-11-05', '2025-11-19', 1, 1, '2025-11-05 10:09:24', NULL),
(34, 37, '2025-11-05', '2025-11-19', 1, 1, '2025-11-05 10:09:25', NULL),
(35, 38, '2025-11-05', '2025-11-19', 1, 1, '2025-11-05 10:09:25', NULL),
(36, 39, '2025-11-05', '2025-11-19', 1, 1, '2025-11-05 10:09:26', NULL),
(37, 40, '2025-11-05', '2025-11-19', 1, 1, '2025-11-05 10:09:26', NULL),
(38, 41, '2025-11-05', '2025-11-19', 1, 1, '2025-11-05 10:09:27', NULL),
(39, 42, '2025-11-05', '2025-11-19', 1, 1, '2025-11-05 10:09:27', NULL),
(40, 43, '2025-11-05', '2025-11-19', 1, 1, '2025-11-05 10:09:27', NULL),
(41, 44, '2025-11-05', '2025-11-19', 1, 1, '2025-11-05 10:09:28', NULL),
(42, 45, '2025-11-05', '2025-11-19', 1, 1, '2025-11-05 10:09:28', NULL),
(43, 46, '2025-11-05', '2025-11-19', 1, 1, '2025-11-05 10:09:29', NULL),
(44, 47, '2025-11-05', '2025-11-19', 1, 1, '2025-11-05 10:09:29', NULL),
(45, 48, '2025-11-05', '2025-11-19', 1, 1, '2025-11-05 10:09:29', NULL),
(46, 49, '2025-11-05', '2025-11-19', 1, 1, '2025-11-05 10:09:30', NULL),
(47, 50, '2025-11-05', '2025-11-19', 1, 1, '2025-11-05 10:09:30', NULL),
(48, 51, '2025-11-05', '2025-11-19', 1, 1, '2025-11-05 10:09:31', NULL),
(49, 52, '2025-11-05', '2025-11-19', 1, 1, '2025-11-05 10:09:31', NULL),
(50, 53, '2025-11-05', '2025-11-19', 1, 1, '2025-11-05 10:09:32', NULL),
(51, 54, '2025-11-05', '2025-11-19', 1, 1, '2025-11-05 10:09:32', NULL),
(52, 55, '2025-11-05', '2025-11-19', 1, 1, '2025-11-05 10:09:32', NULL),
(53, 56, '2025-11-05', '2025-11-19', 1, 1, '2025-11-05 10:09:33', NULL),
(54, 57, '2025-11-05', '2025-11-19', 1, 1, '2025-11-05 10:09:33', NULL),
(55, 58, '2025-11-05', '2025-11-19', 1, 1, '2025-11-05 10:09:34', NULL),
(56, 59, '2025-11-05', '2025-11-19', 1, 1, '2025-11-05 10:09:34', NULL),
(57, 60, '2025-11-05', '2025-11-19', 1, 1, '2025-11-05 10:09:35', NULL),
(58, 61, '2025-11-05', '2025-11-19', 1, 1, '2025-11-05 10:09:35', NULL),
(59, 62, '2025-11-05', '2025-11-19', 1, 1, '2025-11-05 10:09:35', NULL),
(60, 63, '2025-11-05', '2025-11-19', 1, 1, '2025-11-05 10:09:36', NULL),
(61, 64, '2025-11-05', '2026-02-05', 1, 1, '2025-11-05 10:25:39', NULL),
(62, 65, '2025-11-05', '2026-03-05', 0, 1, '2025-11-05 10:27:20', '2025-11-05 10:32:15'),
(63, 66, '2025-11-05', '2026-02-05', 1, 1, '2025-11-05 13:45:04', NULL);

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
  ADD KEY `company_type` (`company_type`),
  ADD KEY `idx_companies_owner` (`owner_user_id`),
  ADD KEY `idx_companies_verified` (`is_verified`);

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
  ADD KEY `currency` (`currency`),
  ADD KEY `idx_listings_user` (`user_id`);
ALTER TABLE `listings` ADD FULLTEXT KEY `ft_search` (`glass_type`,`glass_type_other`,`storage_location`,`price_text`,`quality_notes`);

--
-- Indexes for table `listing_images`
--
ALTER TABLE `listing_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_listing_id` (`listing_id`),
  ADD KEY `idx_is_main` (`is_main`);

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
-- Indexes for table `push_notifications`
--
ALTER TABLE `push_notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_is_read` (`is_read`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Indexes for table `recycled_statuses`
--
ALTER TABLE `recycled_statuses`
  ADD PRIMARY KEY (`status`);

--
-- Indexes for table `saved_listings`
--
ALTER TABLE `saved_listings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user_listing` (`user_id`,`listing_id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_listing_id` (`listing_id`);

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
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT for table `contracts`
--
ALTER TABLE `contracts`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `listings`
--
ALTER TABLE `listings`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=108;

--
-- AUTO_INCREMENT for table `listing_images`
--
ALTER TABLE `listing_images`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

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
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `pages`
--
ALTER TABLE `pages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `page_content`
--
ALTER TABLE `page_content`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=223;

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
-- AUTO_INCREMENT for table `push_notifications`
--
ALTER TABLE `push_notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `saved_listings`
--
ALTER TABLE `saved_listings`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `subscriptions`
--
ALTER TABLE `subscriptions`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=67;

--
-- AUTO_INCREMENT for table `user_emails`
--
ALTER TABLE `user_emails`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `user_subscriptions`
--
ALTER TABLE `user_subscriptions`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=64;

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
  ADD CONSTRAINT `companies_ibfk_1` FOREIGN KEY (`company_type`) REFERENCES `company_types` (`type`),
  ADD CONSTRAINT `fk_companies_owner` FOREIGN KEY (`owner_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

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
  ADD CONSTRAINT `fk_listings_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `listings_ibfk_1` FOREIGN KEY (`location_id`) REFERENCES `locations` (`id`),
  ADD CONSTRAINT `listings_ibfk_2` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`),
  ADD CONSTRAINT `listings_ibfk_3` FOREIGN KEY (`side`) REFERENCES `listing_sides` (`side`),
  ADD CONSTRAINT `listings_ibfk_4` FOREIGN KEY (`recycled`) REFERENCES `recycled_statuses` (`status`),
  ADD CONSTRAINT `listings_ibfk_5` FOREIGN KEY (`tested`) REFERENCES `tested_statuses` (`status`),
  ADD CONSTRAINT `listings_ibfk_6` FOREIGN KEY (`currency`) REFERENCES `currency_iso` (`currency`);

--
-- Constraints for table `listing_images`
--
ALTER TABLE `listing_images`
  ADD CONSTRAINT `listing_images_ibfk_1` FOREIGN KEY (`listing_id`) REFERENCES `listings` (`id`) ON DELETE CASCADE;

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
-- Constraints for table `saved_listings`
--
ALTER TABLE `saved_listings`
  ADD CONSTRAINT `fk_saved_listing` FOREIGN KEY (`listing_id`) REFERENCES `listings` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_saved_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

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
