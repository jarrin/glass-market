-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 31, 2025 at 09:39 AM
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
(3, 'CollectionCo BE', 'Collection Company', 'https://ccbe.example', '+32 2 555 1234', '2025-10-15 11:48:18');

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
  `accepted_by_contract` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `listings`
--

INSERT INTO `listings` (`id`, `location_id`, `company_id`, `side`, `glass_type`, `glass_type_other`, `quantity_tons`, `quantity_note`, `recycled`, `tested`, `storage_location`, `price_text`, `currency`, `created_at`, `valid_until`, `published`, `quality_notes`, `accepted_by_contract`) VALUES
(1, 1, 1, 'WTS', 'Clear Cullet', NULL, 250.00, NULL, 'recycled', 'tested', 'Rotterdam yard', '€120/ton CIF', 'EUR', '2025-10-15 11:48:18', NULL, 1, 'Low Fe content', 0),
(2, 2, 2, 'WTB', 'Brown Cullet', NULL, 150.00, NULL, 'recycled', 'tested', 'Amsterdam warehouse', '€110/ton CIF', 'EUR', '2025-10-15 11:48:18', NULL, 1, 'High purity required', 0),
(3, 3, 3, 'WTS', 'Mixed Cullet', NULL, 100.00, NULL, 'not_recycled', 'untested', 'Brussels yard', '€80/ton EXW', 'EUR', '2025-10-15 11:48:18', NULL, 1, 'Unsorted mix', 0);

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
(10, NULL, NULL, 'test_sub@testsub.com', NULL, '2025-10-29 14:07:18', '$2y$10$qYcQxm8kHYBDjXc2Skh05erADJ5uxwub0DOHWojfcgYLEVfiKHwOi', NULL, 'test_sub', 'test_sub', NULL, NULL, 0, 0, NULL, NULL, NULL, '2025-10-29 14:06:07', '2025-10-29 14:07:18');

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
(4, 10, '2025-10-29', '2025-10-28', 1, 1, '2025-10-29 14:06:07', '2025-10-29 14:22:20');

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
-- Indexes for table `payment_cards`
--
ALTER TABLE `payment_cards`
  ADD PRIMARY KEY (`id`);

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
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `contracts`
--
ALTER TABLE `contracts`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `listings`
--
ALTER TABLE `listings`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

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
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payment_cards`
--
ALTER TABLE `payment_cards`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `subscriptions`
--
ALTER TABLE `subscriptions`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `user_emails`
--
ALTER TABLE `user_emails`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `user_subscriptions`
--
ALTER TABLE `user_subscriptions`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

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
