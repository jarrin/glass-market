-- Migration: Create payment_errors table for logging failed payment attempts
-- Date: 2025-10-31

CREATE TABLE IF NOT EXISTS `payment_errors` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) DEFAULT NULL,
  `plan` varchar(50) DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `error_message` text NOT NULL,
  `error_context` text DEFAULT NULL COMMENT 'JSON with request details',
  `payment_id` varchar(255) DEFAULT NULL COMMENT 'Mollie payment ID if created',
  `request_data` text DEFAULT NULL COMMENT 'JSON of the request that caused error',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `created_at` (`created_at`),
  CONSTRAINT `payment_errors_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
