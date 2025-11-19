-- MikhMon Database Backup
-- Generated on: 2025-11-17 08:16:50
-- Database: mikhmon_billing


-- Table structure for table `agent_billing_payments`
DROP TABLE IF EXISTS `agent_billing_payments`;
CREATE TABLE `agent_billing_payments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `agent_id` int(11) NOT NULL,
  `invoice_id` bigint(20) unsigned NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `fee` decimal(15,2) DEFAULT 0.00,
  `status` enum('pending','paid','failed') DEFAULT 'paid',
  `processed_by` varchar(50) DEFAULT 'system',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_agent_invoice` (`agent_id`,`invoice_id`),
  KEY `fk_abp_invoice` (`invoice_id`),
  CONSTRAINT `fk_abp_agent` FOREIGN KEY (`agent_id`) REFERENCES `agents` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_abp_invoice` FOREIGN KEY (`invoice_id`) REFERENCES `billing_invoices` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table `agent_billing_payments`
INSERT INTO `agent_billing_payments` (`id`, `agent_id`, `invoice_id`, `amount`, `fee`, `status`, `processed_by`, `created_at`) VALUES
('1', '1', '3', '110000.00', '0.00', 'paid', 'system', '2025-11-12 11:19:16');

-- Table structure for table `agent_commissions`
DROP TABLE IF EXISTS `agent_commissions`;
CREATE TABLE `agent_commissions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `agent_id` int(11) NOT NULL,
  `voucher_id` int(11) DEFAULT NULL,
  `commission_amount` decimal(15,2) NOT NULL,
  `commission_percent` decimal(5,2) NOT NULL,
  `voucher_price` decimal(15,2) NOT NULL,
  `status` enum('pending','paid','cancelled') DEFAULT 'pending',
  `earned_at` timestamp NULL DEFAULT current_timestamp(),
  `paid_at` timestamp NULL DEFAULT NULL,
  `notes` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `voucher_id` (`voucher_id`),
  KEY `idx_agent_id` (`agent_id`),
  KEY `idx_status` (`status`),
  CONSTRAINT `agent_commissions_ibfk_1` FOREIGN KEY (`agent_id`) REFERENCES `agents` (`id`) ON DELETE CASCADE,
  CONSTRAINT `agent_commissions_ibfk_2` FOREIGN KEY (`voucher_id`) REFERENCES `agent_vouchers` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table `agent_commissions`
INSERT INTO `agent_commissions` (`id`, `agent_id`, `voucher_id`, `commission_amount`, `commission_percent`, `voucher_price`, `status`, `earned_at`, `paid_at`, `notes`) VALUES
('1', '1', '1', '150.00', '5.00', '3000.00', 'pending', '2025-11-14 16:00:37', NULL, NULL),
('2', '1', '2', '150.00', '5.00', '3000.00', 'pending', '2025-11-15 14:57:28', NULL, NULL);

-- Table structure for table `agent_prices`
DROP TABLE IF EXISTS `agent_prices`;
CREATE TABLE `agent_prices` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `agent_id` int(11) NOT NULL,
  `profile_name` varchar(100) NOT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `buy_price` decimal(15,2) NOT NULL,
  `sell_price` decimal(15,2) NOT NULL,
  `stock_limit` int(11) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_agent_profile` (`agent_id`,`profile_name`),
  KEY `idx_agent_id` (`agent_id`),
  KEY `idx_profile` (`profile_name`),
  CONSTRAINT `agent_prices_ibfk_1` FOREIGN KEY (`agent_id`) REFERENCES `agents` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table `agent_prices`
INSERT INTO `agent_prices` (`id`, `agent_id`, `profile_name`, `price`, `buy_price`, `sell_price`, `stock_limit`, `created_at`, `updated_at`) VALUES
('1', '1', '3k', '0.00', '2000.00', '3000.00', '0', '2025-11-10 12:52:55', '2025-11-10 12:52:55'),
('3', '1', '5k', '0.00', '4000.00', '5000.00', '0', '2025-11-10 13:00:07', '2025-11-10 13:00:07'),
('4', '1', '10k', '0.00', '7000.00', '10000.00', '0', '2025-11-10 13:00:07', '2025-11-10 13:00:07'),
('5', '2', '3k', '0.00', '2000.00', '3000.00', '0', '2025-11-10 13:00:07', '2025-11-10 13:00:07'),
('6', '2', '5k', '0.00', '4000.00', '5000.00', '0', '2025-11-10 13:00:07', '2025-11-10 13:00:07'),
('7', '3', '3k', '0.00', '0.00', '3000.00', '0', '2025-11-10 13:00:07', '2025-11-10 13:00:07'),
('8', '3', '5k', '0.00', '0.00', '5000.00', '0', '2025-11-10 13:00:07', '2025-11-10 13:00:07'),
('9', '3', '10k', '0.00', '0.00', '10000.00', '0', '2025-11-10 13:00:07', '2025-11-10 13:00:07');

-- Table structure for table `agent_profile_pricing`
DROP TABLE IF EXISTS `agent_profile_pricing`;
CREATE TABLE `agent_profile_pricing` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `agent_id` int(11) NOT NULL,
  `profile_name` varchar(100) NOT NULL,
  `display_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `original_price` decimal(10,2) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `is_featured` tinyint(1) DEFAULT 0,
  `icon` varchar(50) DEFAULT 'fa-wifi',
  `color` varchar(20) DEFAULT 'blue',
  `sort_order` int(11) DEFAULT 0,
  `user_type` enum('voucher','member') DEFAULT 'voucher',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_agent_profile` (`agent_id`,`profile_name`),
  CONSTRAINT `fk_agent_profile_pricing_agent` FOREIGN KEY (`agent_id`) REFERENCES `agents` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table `agent_profile_pricing`
INSERT INTO `agent_profile_pricing` (`id`, `agent_id`, `profile_name`, `display_name`, `description`, `price`, `original_price`, `is_active`, `is_featured`, `icon`, `color`, `sort_order`, `user_type`, `created_at`, `updated_at`) VALUES
('17', '1', '3k', 'Voucher 1 Hari', 'Speed Upto 5Mbps', '3000.00', '3500.00', '1', '0', 'fa-wifi', 'blue', '0', 'voucher', '2025-11-17 08:03:21', '2025-11-17 08:03:21');

-- Table structure for table `agent_settings`
DROP TABLE IF EXISTS `agent_settings`;
CREATE TABLE `agent_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `agent_id` int(11) NOT NULL DEFAULT 1,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `setting_type` varchar(20) DEFAULT 'string',
  `description` text DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `setting_key` (`setting_key`),
  KEY `fk_agent_settings_agent` (`agent_id`),
  CONSTRAINT `fk_agent_settings_agent` FOREIGN KEY (`agent_id`) REFERENCES `agents` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=92 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table `agent_settings`
INSERT INTO `agent_settings` (`id`, `agent_id`, `setting_key`, `setting_value`, `setting_type`, `description`, `updated_at`, `updated_by`) VALUES
('1', '1', 'min_topup_amount', '50000', 'number', 'Minimum amount untuk topup saldo', '2025-11-10 12:50:09', NULL),
('2', '1', 'max_topup_amount', '10000000', 'number', 'Maximum amount untuk topup saldo', '2025-11-10 12:50:09', NULL),
('3', '1', 'auto_approve_topup', '0', 'boolean', 'Auto approve topup request', '2025-11-10 12:50:09', NULL),
('4', '1', 'commission_enabled', '1', 'boolean', 'Enable commission system', '2025-11-10 12:50:09', NULL),
('5', '1', 'default_commission_percent', '5', 'number', 'Default commission percentage', '2025-11-10 12:50:09', NULL),
('6', '1', 'agent_registration_enabled', '1', 'boolean', 'Allow agent self registration', '2025-11-10 12:50:09', NULL),
('7', '1', 'min_balance_alert', '10000', 'number', 'Alert when balance below this amount', '2025-11-10 12:50:09', NULL),
('8', '1', 'whatsapp_notification_enabled', '1', 'boolean', 'Send WhatsApp notification to agents', '2025-11-10 12:50:09', NULL),
('9', '1', 'agent_can_set_sell_price', '1', 'boolean', 'Allow agent to set their own sell price', '2025-11-10 12:50:09', NULL),
('10', '1', 'voucher_prefix_agent', 'AG', 'string', 'Prefix for agent generated vouchers', '2025-11-10 12:50:09', NULL),
('11', '1', 'digiflazz_enabled', '0', 'boolean', 'Enable Digiflazz integration', '2025-11-10 12:50:09', NULL),
('12', '1', 'digiflazz_username', '', 'string', 'Digiflazz buyer username', '2025-11-10 12:50:09', NULL),
('13', '1', 'digiflazz_api_key', '', 'string', 'Digiflazz API key', '2025-11-10 12:50:09', NULL),
('14', '1', 'digiflazz_allow_test', '1', 'boolean', 'Allow Digiflazz testing mode', '2025-11-10 12:50:09', NULL),
('15', '1', 'digiflazz_default_markup_percent', '5', 'number', 'Default markup percent for Digiflazz products', '2025-11-10 12:50:09', NULL),
('16', '1', 'digiflazz_last_sync', NULL, 'datetime', 'Last price list sync timestamp', '2025-11-10 12:50:09', NULL),
('17', '1', 'voucher_username_password_same', '1', 'string', 'Voucher generation setting', '2025-11-10 12:54:17', 'admin'),
('18', '1', 'voucher_username_type', 'numeric', 'string', 'Voucher generation setting', '2025-11-10 12:54:17', 'admin'),
('19', '1', 'voucher_username_length', '5', 'string', 'Voucher generation setting', '2025-11-10 12:54:17', 'admin'),
('20', '1', 'voucher_password_type', 'alphanumeric', 'string', 'Voucher generation setting', '2025-11-10 12:54:17', 'admin'),
('21', '1', 'voucher_password_length', '6', 'string', 'Voucher generation setting', '2025-11-10 12:54:17', 'admin'),
('22', '1', 'voucher_prefix_enabled', '0', 'string', 'Voucher generation setting', '2025-11-10 12:54:41', 'admin'),
('23', '1', 'voucher_prefix', 'AG', 'string', 'Voucher generation setting', '2025-11-10 12:54:18', 'admin'),
('24', '1', 'voucher_uppercase', '1', 'string', 'Voucher generation setting', '2025-11-10 12:54:18', 'admin'),
('25', '1', 'payment_bank_name', 'BRI', 'string', 'Payment information setting', '2025-11-10 12:54:18', 'admin'),
('26', '1', 'payment_account_number', '420601003953531', 'string', 'Payment information setting', '2025-11-10 12:54:18', 'admin'),
('27', '1', 'payment_account_name', 'WARJAYA', 'string', 'Payment information setting', '2025-11-10 12:54:18', 'admin'),
('28', '1', 'payment_wa_confirm', '081947215703', 'string', 'Payment information setting', '2025-11-10 12:54:41', 'admin'),
('41', '1', 'admin_whatsapp_numbers', '6281234567890', 'string', NULL, '2025-11-10 13:00:07', NULL),
('50', '1', 'whatsapp_gateway_url', 'https://api.whatsapp.com', 'string', NULL, '2025-11-10 13:00:07', NULL),
('51', '1', 'whatsapp_token', '', 'string', NULL, '2025-11-10 13:00:07', NULL),
('63', '2', 'billing_fee_amount', '5000', 'string', NULL, '2025-11-12 13:33:29', NULL),
('64', '2', 'billing_sell_price', '0', 'string', NULL, '2025-11-12 13:33:29', NULL),
('75', '1', 'billing_commission_amount', '5000', 'string', NULL, '2025-11-12 14:00:28', NULL),
('87', '1', 'voucher_format', 'USER-{RANDOM}', 'string', NULL, '2025-11-17 07:49:50', NULL),
('88', '1', 'voucher_length', '8', 'string', NULL, '2025-11-17 07:49:50', NULL),
('89', '1', 'voucher_password_format', '{RANDOM}', 'string', NULL, '2025-11-17 07:49:50', NULL);

-- Table structure for table `agent_summary`
DROP TABLE IF EXISTS `agent_summary`;
CREATE ALGORITHM=UNDEFINED DEFINER=`cpses_al52memi2d`@`localhost` SQL SECURITY DEFINER VIEW `agent_summary` AS select `a`.`id` AS `id`,`a`.`agent_code` AS `agent_code`,`a`.`agent_name` AS `agent_name`,`a`.`phone` AS `phone`,`a`.`balance` AS `balance`,`a`.`status` AS `status`,`a`.`level` AS `level`,count(distinct `av`.`id`) AS `total_vouchers`,count(distinct case when `av`.`status` = 'used' then `av`.`id` end) AS `used_vouchers`,sum(case when `at`.`transaction_type` = 'topup' then `at`.`amount` else 0 end) AS `total_topup`,sum(case when `at`.`transaction_type` = 'generate' then `at`.`amount` else 0 end) AS `total_spent`,coalesce(sum(`ac`.`commission_amount`),0) AS `total_commission`,`a`.`created_at` AS `created_at`,`a`.`last_login` AS `last_login` from (((`agents` `a` left join `agent_vouchers` `av` on(`a`.`id` = `av`.`agent_id`)) left join `agent_transactions` `at` on(`a`.`id` = `at`.`agent_id`)) left join `agent_commissions` `ac` on(`a`.`id` = `ac`.`agent_id` and `ac`.`status` = 'paid')) group by `a`.`id`;


-- Table structure for table `agent_topup_requests`
DROP TABLE IF EXISTS `agent_topup_requests`;
CREATE TABLE `agent_topup_requests` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `agent_id` int(11) NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `payment_proof` varchar(255) DEFAULT NULL,
  `bank_name` varchar(50) DEFAULT NULL,
  `account_number` varchar(50) DEFAULT NULL,
  `account_name` varchar(100) DEFAULT NULL,
  `status` enum('pending','approved','rejected','cancelled') DEFAULT 'pending',
  `requested_at` timestamp NULL DEFAULT current_timestamp(),
  `processed_at` timestamp NULL DEFAULT NULL,
  `processed_by` varchar(50) DEFAULT NULL,
  `admin_notes` text DEFAULT NULL,
  `agent_notes` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_agent_id` (`agent_id`),
  KEY `idx_status` (`status`),
  KEY `idx_requested_at` (`requested_at`),
  CONSTRAINT `agent_topup_requests_ibfk_1` FOREIGN KEY (`agent_id`) REFERENCES `agents` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- Table structure for table `agent_transactions`
DROP TABLE IF EXISTS `agent_transactions`;
CREATE TABLE `agent_transactions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `agent_id` int(11) NOT NULL,
  `transaction_type` enum('topup','generate','refund','commission','penalty','digiflazz') NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `balance_before` decimal(15,2) NOT NULL,
  `balance_after` decimal(15,2) NOT NULL,
  `profile_name` varchar(100) DEFAULT NULL,
  `voucher_username` varchar(100) DEFAULT NULL,
  `voucher_password` varchar(100) DEFAULT NULL,
  `quantity` int(11) DEFAULT 1,
  `description` text DEFAULT NULL,
  `reference_id` varchar(50) DEFAULT NULL,
  `created_by` varchar(50) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_agent_id` (`agent_id`),
  KEY `idx_type` (`transaction_type`),
  KEY `idx_date` (`created_at`),
  KEY `idx_reference` (`reference_id`),
  KEY `idx_agent_transactions_date` (`created_at`,`agent_id`),
  CONSTRAINT `agent_transactions_ibfk_1` FOREIGN KEY (`agent_id`) REFERENCES `agents` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table `agent_transactions`
INSERT INTO `agent_transactions` (`id`, `agent_id`, `transaction_type`, `amount`, `balance_before`, `balance_after`, `profile_name`, `voucher_username`, `voucher_password`, `quantity`, `description`, `reference_id`, `created_by`, `created_at`, `ip_address`, `user_agent`) VALUES
('1', '1', 'topup', '500000.00', '100000.00', '600000.00', NULL, NULL, NULL, '1', '', NULL, 'alijaya', '2025-11-10 12:53:26', NULL, NULL),
('2', '1', '', '110000.00', '600000.00', '490000.00', 'billing_payment', '3', NULL, '1', 'Payment for invoice 3', '3', 'system', '2025-11-12 11:19:16', NULL, NULL),
('3', '1', '', '100000.00', '490000.00', '390000.00', 'billing_payment', '4', NULL, '1', 'Payment for invoice 4', '4', 'system', '2025-11-12 11:24:05', NULL, NULL),
('4', '1', '', '110000.00', '390000.00', '280000.00', 'billing_payment', '5', NULL, '1', 'Payment for invoice 5', '5', 'system', '2025-11-12 11:34:26', NULL, NULL),
('5', '1', '', '100000.00', '280000.00', '180000.00', 'billing_payment', '6', NULL, '1', 'Payment for invoice 6', '6', 'system', '2025-11-12 13:36:00', NULL, NULL),
('6', '1', '', '160000.00', '180000.00', '20000.00', 'billing_payment', '7', NULL, '1', 'Payment for invoice 7', '7', 'system', '2025-11-12 14:04:27', NULL, NULL),
('7', '1', 'generate', '2000.00', '20000.00', '18000.00', '3k', '59381', NULL, '1', 'Generate voucher: 59381', NULL, NULL, '2025-11-14 16:00:37', NULL, NULL),
('8', '1', 'generate', '2000.00', '18000.00', '16000.00', '3k', '25501', NULL, '1', 'Generate voucher: 25501', NULL, NULL, '2025-11-15 14:57:28', NULL, NULL);

-- Table structure for table `agent_vouchers`
DROP TABLE IF EXISTS `agent_vouchers`;
CREATE TABLE `agent_vouchers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `agent_id` int(11) NOT NULL,
  `transaction_id` int(11) DEFAULT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `profile_name` varchar(100) NOT NULL,
  `buy_price` decimal(15,2) NOT NULL,
  `sell_price` decimal(15,2) DEFAULT NULL,
  `status` enum('active','used','expired','deleted') DEFAULT 'active',
  `customer_phone` varchar(20) DEFAULT NULL,
  `customer_name` varchar(100) DEFAULT NULL,
  `sent_via` enum('web','whatsapp','manual') DEFAULT 'web',
  `sent_at` timestamp NULL DEFAULT NULL,
  `used_at` timestamp NULL DEFAULT NULL,
  `expired_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `notes` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `transaction_id` (`transaction_id`),
  KEY `idx_agent_id` (`agent_id`),
  KEY `idx_username` (`username`),
  KEY `idx_status` (`status`),
  KEY `idx_customer_phone` (`customer_phone`),
  KEY `idx_agent_vouchers_date` (`created_at`,`agent_id`),
  KEY `idx_agent_vouchers_profile` (`profile_name`,`status`),
  CONSTRAINT `agent_vouchers_ibfk_1` FOREIGN KEY (`agent_id`) REFERENCES `agents` (`id`) ON DELETE CASCADE,
  CONSTRAINT `agent_vouchers_ibfk_2` FOREIGN KEY (`transaction_id`) REFERENCES `agent_transactions` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table `agent_vouchers`
INSERT INTO `agent_vouchers` (`id`, `agent_id`, `transaction_id`, `username`, `password`, `profile_name`, `buy_price`, `sell_price`, `status`, `customer_phone`, `customer_name`, `sent_via`, `sent_at`, `used_at`, `expired_at`, `created_at`, `notes`) VALUES
('1', '1', '7', '59381', '59381', '3k', '2000.00', '3000.00', 'active', '', '', 'web', NULL, NULL, NULL, '2025-11-14 16:00:37', NULL),
('2', '1', '8', '25501', '25501', '3k', '2000.00', '3000.00', 'active', '', '', 'web', NULL, NULL, NULL, '2025-11-15 14:57:28', NULL);

-- Table structure for table `agents`
DROP TABLE IF EXISTS `agents`;
CREATE TABLE `agents` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `agent_code` varchar(20) NOT NULL,
  `agent_name` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `balance` decimal(15,2) DEFAULT 0.00,
  `status` enum('active','inactive','suspended') DEFAULT 'active',
  `level` enum('bronze','silver','gold','platinum') DEFAULT 'bronze',
  `commission_percent` decimal(5,2) DEFAULT 0.00,
  `created_by` varchar(50) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `last_login` timestamp NULL DEFAULT NULL,
  `notes` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `agent_code` (`agent_code`),
  UNIQUE KEY `phone` (`phone`),
  KEY `idx_phone` (`phone`),
  KEY `idx_agent_code` (`agent_code`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table `agents`
INSERT INTO `agents` (`id`, `agent_code`, `agent_name`, `phone`, `email`, `password`, `balance`, `status`, `level`, `commission_percent`, `created_by`, `created_at`, `updated_at`, `last_login`, `notes`) VALUES
('1', 'AG001', 'Agent Demo', '081947215703', 'agent@demo.com', '$2y$10$DPgy35C8ZczgLnD3nUbYcO4rqXCJFf8pFPhGv33pk1xsb1OciR3F2', '16000.00', 'active', 'silver', '5.00', 'admin', '2025-11-10 12:50:10', '2025-11-17 08:05:11', '2025-11-17 08:05:11', ''),
('2', 'AG5136', 'tester', 'seed-ag5136', NULL, '', '0.00', 'active', 'bronze', '0.00', NULL, '2025-11-10 13:00:07', '2025-11-10 13:00:07', NULL, NULL),
('3', 'PUBLIC', 'Public Catalog', 'seed-public', NULL, '', '0.00', 'active', 'bronze', '0.00', NULL, '2025-11-10 13:00:07', '2025-11-10 13:00:07', NULL, NULL);

-- Table structure for table `billing_customers`
DROP TABLE IF EXISTS `billing_customers`;
CREATE TABLE `billing_customers` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `profile_id` int(10) unsigned NOT NULL,
  `name` varchar(150) NOT NULL,
  `phone` varchar(32) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `service_number` varchar(100) DEFAULT NULL,
  `billing_day` tinyint(3) unsigned NOT NULL DEFAULT 1,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `is_isolated` tinyint(1) NOT NULL DEFAULT 0,
  `next_isolation_date` date DEFAULT NULL,
  `genieacs_match_mode` enum('device_id','phone_tag','pppoe_username') NOT NULL DEFAULT 'device_id',
  `genieacs_pppoe_username` varchar(191) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_profile_id` (`profile_id`),
  KEY `idx_billing_day` (`billing_day`),
  CONSTRAINT `fk_billing_customers_profile` FOREIGN KEY (`profile_id`) REFERENCES `billing_profiles` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=115 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `billing_customers`
INSERT INTO `billing_customers` (`id`, `profile_id`, `name`, `phone`, `email`, `address`, `service_number`, `billing_day`, `status`, `is_isolated`, `next_isolation_date`, `genieacs_match_mode`, `genieacs_pppoe_username`, `notes`, `created_at`, `updated_at`) VALUES
('1', '1', 'juragan alijaya', '081947215703', 'alijayanet@gmail.com', 'Jln. Pantai Tanjungpura Desa Ujunggebang Kecamatan Sukra - Indramayu', NULL, '1', 'active', '0', NULL, 'pppoe_username', 'cecep', NULL, '2025-11-10 13:21:44', '2025-11-15 21:53:32'),
('2', '1', 'tester', '087828060111', 'alijaya@test.com', NULL, NULL, '1', 'active', '0', NULL, 'pppoe_username', 'server_umi', NULL, '2025-11-12 11:10:29', '2025-11-15 21:53:32'),
('8', '1', 'AANG SURINIH', '83148499019', 'email@contoh.com', 'RAWAGUNDA', 'CPE-001', '20', 'active', '0', NULL, 'pppoe_username', 'aang', 'Catatan opsional', '2025-11-15 21:54:54', NULL),
('9', '1', 'alam', '87875831116', 'email@contoh.com', 'LOR', 'CPE-002', '20', 'active', '0', NULL, 'pppoe_username', 'alam', NULL, '2025-11-15 21:54:55', NULL),
('10', '1', 'ALAN', '83844721121', 'email@contoh.com', 'KIDUL', 'CPE-003', '20', 'active', '0', NULL, 'pppoe_username', 'alan@pentud', NULL, '2025-11-15 21:54:55', NULL),
('11', '1', 'aldo kayem', '898564899', 'email@contoh.com', 'TENGAH', 'CPE-004', '20', 'active', '0', NULL, 'pppoe_username', 'aldo', NULL, '2025-11-15 21:54:55', NULL),
('12', '1', 'amsori', '83893116880', 'email@contoh.com', 'TENGAH', 'CPE-005', '20', 'active', '0', NULL, 'pppoe_username', 'coyih', NULL, '2025-11-15 21:54:55', NULL),
('13', '1', 'andi', '83841300234', 'email@contoh.com', 'LOR', 'CPE-006', '20', 'active', '0', NULL, 'pppoe_username', 'andi', NULL, '2025-11-15 21:54:55', NULL),
('14', '1', 'ASEP TARYUNAH', '83857856830', 'email@contoh.com', 'PEGAGAN', 'CPE-007', '20', 'active', '0', NULL, 'pppoe_username', 'adinesep@ujungmulya', NULL, '2025-11-15 21:54:55', NULL),
('15', '1', 'ASTRI PLENTONG', '83811693012', 'email@contoh.com', 'PEGAGAN', 'CPE-008', '20', 'active', '0', NULL, 'pppoe_username', 'astri@pegagan', NULL, '2025-11-15 21:54:55', NULL),
('16', '1', 'atam', '83148289472', 'email@contoh.com', 'SEKOLAHAN', 'CPE-009', '20', 'active', '0', NULL, 'pppoe_username', 'atam', NULL, '2025-11-15 21:54:55', NULL),
('17', '1', 'brohim', '83181897947', 'email@contoh.com', 'KIDUL', 'CPE-010', '20', 'active', '0', NULL, 'pppoe_username', 'brohim', NULL, '2025-11-15 21:54:55', NULL),
('18', '1', 'bunga ita', '83160332656', 'email@contoh.com', 'LOR', 'CPE-011', '20', 'active', '0', NULL, 'pppoe_username', 'bunga', NULL, '2025-11-15 21:54:55', NULL),
('19', '1', 'caskadi surni', '81333187793', 'email@contoh.com', 'LOR', 'CPE-012', '20', 'active', '0', NULL, 'pppoe_username', 'warungyuni@laut', NULL, '2025-11-15 21:54:55', NULL),
('20', '1', 'codet', '8179223138', 'email@contoh.com', 'SEKOLAHAN', 'CPE-013', '20', 'active', '0', NULL, 'pppoe_username', 'codet', NULL, '2025-11-15 21:54:55', NULL),
('21', '1', 'DADANG', '87828678278', 'email@contoh.com', 'JANGGAR', 'CPE-014', '20', 'active', '0', NULL, 'pppoe_username', 'dadang@genjong', NULL, '2025-11-15 21:54:55', NULL),
('22', '1', 'DARIM', '83101495091', 'email@contoh.com', 'RAWAGUNDA', 'CPE-015', '20', 'active', '0', NULL, 'pppoe_username', 'darim', NULL, '2025-11-15 21:54:55', NULL),
('23', '1', 'DARNIYAH', '83101302162', 'email@contoh.com', 'PEGAGAN', 'CPE-017', '20', 'active', '0', NULL, 'pppoe_username', 'durniah@pegagan', NULL, '2025-11-15 21:54:55', NULL),
('24', '1', 'daruni', '83893621842', 'email@contoh.com', 'RAWAGUNDA', 'CPE-018', '20', 'active', '0', NULL, 'pppoe_username', 'daruni@rawagunda', NULL, '2025-11-15 21:54:55', NULL),
('25', '1', 'DASTA', '81394119885', 'email@contoh.com', 'RAWAGUNDA', 'CPE-019', '20', 'active', '0', NULL, 'pppoe_username', 'dasta', NULL, '2025-11-15 21:54:55', NULL),
('26', '1', 'deni pegagan', '82117680180', 'email@contoh.com', 'UJUNG MULYA', 'CPE-020', '20', 'active', '0', NULL, 'pppoe_username', 'deni@pegagan', NULL, '2025-11-15 21:54:55', NULL),
('27', '1', 'desy sarna pegagan', '83850123479', 'email@contoh.com', 'PEGAGAN', 'CPE-021', '20', 'active', '0', NULL, 'pppoe_username', 'sarna@pegagan', NULL, '2025-11-15 21:54:55', NULL),
('28', '1', 'durakman', '81912976364', 'email@contoh.com', 'RAWAGUNDA', 'CPE-022', '20', 'active', '0', NULL, 'pppoe_username', 'nadiva', NULL, '2025-11-15 21:54:55', NULL),
('29', '1', 'EJUN ERETAN', '83856543965', 'email@contoh.com', 'KIDUL', 'CPE-024', '20', 'active', '0', NULL, 'pppoe_username', 'ejun', NULL, '2025-11-15 21:54:55', NULL),
('30', '1', 'eka', '82088889999', 'email@contoh.com', 'KIDUL', 'CPE-025', '20', 'active', '0', NULL, 'pppoe_username', 'eka', NULL, '2025-11-15 21:54:55', NULL),
('31', '1', 'ELIS', '83109909888', 'email@contoh.com', 'RAWAGUNDA', 'CPE-026', '20', 'active', '0', NULL, 'pppoe_username', 'elis@rawagunda', NULL, '2025-11-15 21:54:55', NULL),
('32', '1', 'erni', '82222628888', 'email@contoh.com', 'LOR', 'CPE-027', '20', 'active', '0', NULL, 'pppoe_username', 'erni', NULL, '2025-11-15 21:54:55', NULL),
('33', '1', 'Eryanto darniyah', '83863253007', 'email@contoh.com', 'PEGAGAN', 'CPE-028', '20', 'active', '0', NULL, 'pppoe_username', 'eryanto@pegagan', NULL, '2025-11-15 21:54:55', NULL),
('34', '1', 'ETY', '83148586860', 'email@contoh.com', 'PEGAGAN', 'CPE-029', '20', 'active', '0', NULL, 'pppoe_username', 'ety@pegagan', NULL, '2025-11-15 21:54:55', NULL),
('35', '1', 'EVA', '83110642829', 'email@contoh.com', 'TENGAH', 'CPE-030', '20', 'active', '0', NULL, 'pppoe_username', 'eva', NULL, '2025-11-15 21:54:55', NULL),
('36', '1', 'eva pegagan', '87763133900', 'email@contoh.com', 'PEGAGAN', 'CPE-031', '20', 'active', '0', NULL, 'pppoe_username', 'eva@pegagan', NULL, '2025-11-15 21:54:55', NULL),
('37', '1', 'h juroh', '87727085333', 'email@contoh.com', 'LOR', 'CPE-032', '20', 'active', '0', NULL, 'pppoe_username', 'h.juroh', NULL, '2025-11-15 21:54:55', NULL),
('38', '1', 'Hany tarsiti', '85223932032', 'email@contoh.com', 'SEKOLAHAN', 'CPE-033', '20', 'active', '0', NULL, 'pppoe_username', 'hany@tarsiti', NULL, '2025-11-15 21:54:55', NULL),
('39', '1', 'HARTONO', '83179872550', 'email@contoh.com', 'PEGAGAN', 'CPE-034', '20', 'active', '0', NULL, 'pppoe_username', 'kartono@pegagan', NULL, '2025-11-15 21:54:55', NULL),
('40', '1', 'iman jumroh', '6281385896890', 'email@contoh.com', 'SEKOLAHAN', 'CPE-036', '20', 'active', '0', NULL, 'pppoe_username', 'iman@', NULL, '2025-11-15 21:54:55', NULL),
('41', '1', 'indah madi', '6283840073601', 'email@contoh.com', 'JANGGAR', 'CPE-037', '20', 'active', '0', NULL, 'pppoe_username', 'indah@tanjungpura', NULL, '2025-11-15 21:54:55', NULL),
('42', '1', 'kama', '6281804880173', 'email@contoh.com', 'SEKOLAHAN', 'CPE-039', '20', 'active', '0', NULL, 'pppoe_username', 'kama', NULL, '2025-11-15 21:54:55', NULL),
('43', '1', 'KANG ASIAH', '6283821662411', 'email@contoh.com', 'TENGAH', 'CPE-040', '20', 'active', '0', NULL, 'pppoe_username', 'toax', NULL, '2025-11-15 21:54:55', NULL),
('44', '1', 'KANOL', '6285320809468', 'email@contoh.com', 'JANGGAR', 'CPE-041', '20', 'active', '0', NULL, 'pppoe_username', 'anwar@janggar', NULL, '2025-11-15 21:54:55', NULL),
('45', '1', 'karban', '6285314672400', 'email@contoh.com', 'LOR', 'CPE-042', '20', 'active', '0', NULL, 'pppoe_username', 'karban', NULL, '2025-11-15 21:54:56', NULL),
('46', '1', 'KARSIH', '6283870880008', 'email@contoh.com', 'KIDUL', 'CPE-043', '20', 'active', '0', NULL, 'pppoe_username', 'karsih', NULL, '2025-11-15 21:54:56', NULL),
('47', '1', 'kasdi', '62888666666', 'email@contoh.com', 'RAWAGUNDA', 'CPE-044', '20', 'active', '0', NULL, 'pppoe_username', 'kasdi@rawagunda', NULL, '2025-11-15 21:54:56', NULL),
('48', '1', 'kasmuri', '6283180274424', 'email@contoh.com', 'LOR', 'CPE-045', '20', 'active', '0', NULL, 'pppoe_username', 'kasmuri', NULL, '2025-11-15 21:54:56', NULL),
('49', '1', 'koriyah', '6285210617927', 'email@contoh.com', 'TENGAH', 'CPE-046', '20', 'active', '0', NULL, 'pppoe_username', 'koriya', NULL, '2025-11-15 21:54:56', NULL),
('50', '1', 'Kurnati / Tarno', '6283196108868', 'email@contoh.com', 'PEGAGAN', 'CPE-047', '20', 'active', '0', NULL, 'pppoe_username', 'yafie', NULL, '2025-11-15 21:54:56', NULL),
('51', '1', 'KUS TOYINAH', '6283897310863', 'email@contoh.com', 'SEKOLAHAN', 'CPE-048', '20', 'active', '0', NULL, 'pppoe_username', 'kus@toyinah', NULL, '2025-11-15 21:54:56', NULL),
('52', '1', 'leha', '6282315109088', 'email@contoh.com', 'PEGAGAN', 'CPE-049', '20', 'active', '0', NULL, 'pppoe_username', 'leha@pegagan', NULL, '2025-11-15 21:54:56', NULL),
('53', '1', 'LISAN TARONO', '6283149850493', 'email@contoh.com', 'KIDUL', 'CPE-050', '20', 'active', '0', NULL, 'pppoe_username', 'lisan@banteng', NULL, '2025-11-15 21:54:56', NULL),
('54', '1', 'maktub', '6287786722675', 'email@contoh.com', 'LOR', 'CPE-051', '20', 'active', '0', NULL, 'pppoe_username', 'maktub', NULL, '2025-11-15 21:54:56', NULL),
('55', '1', 'Mardi', '6281220564761', 'email@contoh.com', 'SEKOLAHAN', 'CPE-052', '20', 'active', '0', NULL, 'pppoe_username', 'mardi', NULL, '2025-11-15 21:54:56', NULL),
('56', '1', 'MUKROMIN', '6281271680253', 'email@contoh.com', 'RAWAGUNDA', 'CPE-053', '20', 'active', '0', NULL, 'pppoe_username', 'gadel', NULL, '2025-11-15 21:54:56', NULL),
('57', '1', 'muktar', '6288888555', 'email@contoh.com', 'LOR', 'CPE-054', '20', 'active', '0', NULL, 'pppoe_username', 'muktar@ompong', NULL, '2025-11-15 21:54:56', NULL),
('58', '1', 'murba bewok', '6285321316876', 'email@contoh.com', 'LOR', 'CPE-055', '20', 'active', '0', NULL, 'pppoe_username', 'murba', NULL, '2025-11-15 21:54:56', NULL),
('59', '1', 'murdani', '62882000842360', 'email@contoh.com', 'SEKOLAHAN', 'CPE-056', '20', 'active', '0', NULL, 'pppoe_username', 'murdani', NULL, '2025-11-15 21:54:56', NULL),
('60', '1', 'Naelin', '6287815724758', 'email@contoh.com', 'KIDUL', 'CPE-057', '20', 'active', '0', NULL, 'pppoe_username', 'aqiel', NULL, '2025-11-15 21:54:56', NULL),
('61', '1', 'nalda', '6283148381299', 'email@contoh.com', 'TENGAH', 'CPE-058', '20', 'active', '0', NULL, 'pppoe_username', 'nalda@rawagunda2', NULL, '2025-11-15 21:54:56', NULL),
('62', '1', 'nani', '6287864005073', 'email@contoh.com', 'PEGAGAN', 'CPE-059', '20', 'active', '0', NULL, 'pppoe_username', 'server@nani', NULL, '2025-11-15 21:54:56', NULL),
('63', '1', 'odah luwih', '6281222370404', 'email@contoh.com', 'SEKOLAHAN', 'CPE-061', '20', 'active', '0', NULL, 'pppoe_username', 'luwih@', NULL, '2025-11-15 21:54:56', NULL),
('64', '1', 'pa budi', '6281318500853', 'email@contoh.com', 'PEGAGAN', 'CPE-062', '20', 'active', '0', NULL, 'pppoe_username', 'pabudi@genjong', NULL, '2025-11-15 21:54:56', NULL),
('65', '1', 'pa rudi', '6285294761133', 'email@contoh.com', 'KIDUL', 'CPE-063', '20', 'active', '0', NULL, 'pppoe_username', 'salsa', NULL, '2025-11-15 21:54:56', NULL),
('66', '1', 'PANDI TANJUNGPURA', '6283824056357', 'email@contoh.com', 'PEGAGAN', 'CPE-064', '20', 'active', '0', NULL, 'pppoe_username', 'pandi@tanjungpura', NULL, '2025-11-15 21:54:56', NULL),
('67', '1', 'pendi yati', '6281288155242', 'email@contoh.com', 'TENGAH', 'CPE-065', '20', 'active', '0', NULL, 'pppoe_username', 'elvano1', NULL, '2025-11-15 21:54:56', NULL),
('68', '1', 'RADIAH CASINIH', '6283103244113', 'email@contoh.com', 'KIDUL', 'CPE-066', '20', 'active', '0', NULL, 'pppoe_username', 'memble@', NULL, '2025-11-15 21:54:56', NULL),
('69', '1', 'RAENI', '6282129823190', 'email@contoh.com', 'LOR', 'CPE-067', '20', 'active', '0', NULL, 'pppoe_username', 'raeni', NULL, '2025-11-15 21:54:56', NULL),
('70', '1', 'rangdu', '6288855588', 'email@contoh.com', 'LOR', 'CPE-068', '20', 'active', '0', NULL, 'pppoe_username', 'rangdu', NULL, '2025-11-15 21:54:56', NULL),
('71', '1', 'ranita kendo', '62878285555', 'email@contoh.com', 'KIDUL', 'CPE-069', '20', 'active', '0', NULL, 'pppoe_username', 'kendo@tanjungpura', NULL, '2025-11-15 21:54:56', NULL),
('72', '1', 'rasta', '6282130978555', 'email@contoh.com', 'RAWAGUNDA', 'CPE-070', '20', 'active', '0', NULL, 'pppoe_username', 'rasta', NULL, '2025-11-15 21:54:56', NULL),
('73', '1', 'ratinih linah', '6283805285549', 'email@contoh.com', 'UJUNG MULYA', 'CPE-071', '20', 'active', '0', NULL, 'pppoe_username', 'lina@ujungmulya', NULL, '2025-11-15 21:54:56', NULL),
('74', '1', 'RENDY BAWON', '6281321960111', 'email@contoh.com', 'KIDUL', 'CPE-072', '20', 'active', '0', NULL, 'pppoe_username', 'wendy@tanjungpura', NULL, '2025-11-15 21:54:56', NULL),
('75', '1', 'RITEM', '628788888', 'email@contoh.com', 'RAWAGUNDA', 'CPE-073', '20', 'active', '0', NULL, 'pppoe_username', 'rintem', NULL, '2025-11-15 21:54:56', NULL),
('76', '1', 'RUDI BENGKEL', '6283821322650', 'email@contoh.com', 'KIDUL', 'CPE-074', '20', 'active', '0', NULL, 'pppoe_username', 'rudibengkel@tanjungpura', NULL, '2025-11-15 21:54:56', NULL),
('77', '1', 'runaeni', '6287838812861', 'email@contoh.com', 'TENGAH', 'CPE-075', '20', 'active', '0', NULL, 'pppoe_username', 'runaeni', NULL, '2025-11-15 21:54:56', NULL),
('78', '1', 'RUSMINIH NARTO', '6283104729324', 'email@contoh.com', 'PEGAGAN', 'CPE-076', '20', 'active', '0', NULL, 'pppoe_username', 'surminih@pegagan', NULL, '2025-11-15 21:54:57', NULL),
('79', '1', 'SAEFUDIN JGR', '6287817041868', 'email@contoh.com', 'JANGGAR', 'CPE-077', '20', 'active', '0', NULL, 'pppoe_username', 'saefudin@janggar', NULL, '2025-11-15 21:54:57', NULL),
('80', '1', 'SANDI RAJAN', '6283162744042', 'email@contoh.com', 'LOR', 'CPE-078', '20', 'active', '0', NULL, 'pppoe_username', 'sandi@rajan', NULL, '2025-11-15 21:54:57', NULL),
('81', '1', 'SANITI', '6281317728767', 'email@contoh.com', 'PEGAGAN', 'CPE-079', '20', 'active', '0', NULL, 'pppoe_username', 'dasuki', NULL, '2025-11-15 21:54:57', NULL),
('82', '1', 'sarwan carinih', '628900000', 'email@contoh.com', 'KIDUL', 'CPE-080', '20', 'active', '0', NULL, 'pppoe_username', 'sarwan', NULL, '2025-11-15 21:54:57', NULL),
('83', '1', 'sd ug 1', '6281947287242', 'email@contoh.com', 'SEKOLAHAN', 'CPE-081', '20', 'active', '0', NULL, 'pppoe_username', 'sdn1@tanjungpura', NULL, '2025-11-15 21:54:57', NULL),
('84', '1', 'seka bajil', '6283115666635', 'email@contoh.com', 'KIDUL', 'CPE-082', '20', 'active', '0', NULL, 'pppoe_username', 'seka', NULL, '2025-11-15 21:54:57', NULL),
('85', '1', 'siti surnita', '6287824972048', 'email@contoh.com', 'LOR', 'CPE-083', '20', 'active', '0', NULL, 'pppoe_username', 'warung', NULL, '2025-11-15 21:54:57', NULL),
('86', '1', 'SOBIRIN', '6283190697316', 'email@contoh.com', 'RAWAGUNDA', 'CPE-084', '20', 'active', '0', NULL, 'pppoe_username', 'Uchi', NULL, '2025-11-15 21:54:57', NULL),
('87', '1', 'sofyan', '6283823766661', 'email@contoh.com', 'PEGAGAN', 'CPE-085', '20', 'active', '0', NULL, 'pppoe_username', 'opang@pegagan', NULL, '2025-11-15 21:54:57', NULL),
('88', '1', 'SOLEH / EGA', '6282129005200', 'email@contoh.com', 'PEGAGAN', 'CPE-086', '20', 'active', '0', NULL, 'pppoe_username', 'soleh@pegagan', NULL, '2025-11-15 21:54:57', NULL),
('89', '1', 'SUDINI / EEN', '6281313434488', 'email@contoh.com', 'PEGAGAN', 'CPE-087', '20', 'active', '0', NULL, 'pppoe_username', 'sudini@pegagan', NULL, '2025-11-15 21:54:57', NULL),
('90', '1', 'SULIWA', '6283824109147', 'email@contoh.com', 'RAWAGUNDA', 'CPE-088', '20', 'active', '0', NULL, 'pppoe_username', 'kasto', NULL, '2025-11-15 21:54:57', NULL),
('91', '1', 'sumi', '6283137462252', 'email@contoh.com', 'SEKOLAHAN', 'CPE-089', '20', 'active', '0', NULL, 'pppoe_username', 'sumi', NULL, '2025-11-15 21:54:57', NULL),
('92', '1', 'sunata', '6281324494588', 'email@contoh.com', 'LOR', 'CPE-090', '20', 'active', '0', NULL, 'pppoe_username', 'yogi', NULL, '2025-11-15 21:54:57', NULL),
('93', '1', 'sut rosud', '6287717603954', 'email@contoh.com', 'RAWAGUNDA', 'CPE-091', '20', 'active', '0', NULL, 'pppoe_username', 'jami@sut', NULL, '2025-11-15 21:54:57', NULL),
('94', '1', 'sutara pegagan', '6287820851413', 'email@contoh.com', 'PEGAGAN', 'CPE-092', '20', 'active', '0', NULL, 'pppoe_username', 'sutara@pegagan', NULL, '2025-11-15 21:54:57', NULL),
('95', '1', 'TARIMAN', '6283148432692', 'email@contoh.com', 'SEKOLAHAN', 'CPE-093', '20', 'active', '0', NULL, 'pppoe_username', 'tariman', NULL, '2025-11-15 21:54:57', NULL),
('96', '1', 'tarsinah', '6287705334501', 'email@contoh.com', 'KIDUL', 'CPE-094', '20', 'active', '0', NULL, 'pppoe_username', 'fajar1', NULL, '2025-11-15 21:54:57', NULL),
('97', '1', 'te dasmi', '6282214849195', 'email@contoh.com', 'TENGAH', 'CPE-095', '20', 'active', '0', NULL, 'pppoe_username', 'nana', NULL, '2025-11-15 21:54:57', NULL),
('98', '1', 'tibil', '6283141271951', 'email@contoh.com', 'KIDUL', 'CPE-096', '20', 'active', '0', NULL, 'pppoe_username', 'tibil', NULL, '2025-11-15 21:54:57', NULL),
('99', '1', 'tinih warmi', '6285294257671', 'email@contoh.com', 'LOR', 'CPE-097', '20', 'active', '0', NULL, 'pppoe_username', 'tinih', NULL, '2025-11-15 21:54:57', NULL),
('100', '1', 'toko satur', '6288887777', 'email@contoh.com', 'KIDUL', 'CPE-098', '20', 'active', '0', NULL, 'pppoe_username', 'toko@sayur', NULL, '2025-11-15 21:54:57', NULL),
('101', '1', 'Turidah narto', '6281340102820', 'email@contoh.com', 'PEGAGAN', 'CPE-099', '20', 'active', '0', NULL, 'pppoe_username', 'ggclink', NULL, '2025-11-15 21:54:57', NULL),
('102', '1', 'UMI KULSUM', '6281224276126', 'email@contoh.com', 'JANGGAR', 'CPE-100', '20', 'active', '0', NULL, 'pppoe_username', 'khopik2 umikulsum', NULL, '2025-11-15 21:54:57', NULL),
('103', '1', 'uung mail', '6283172740698', 'email@contoh.com', 'PEGAGAN', 'CPE-101', '20', 'active', '0', NULL, 'pppoe_username', 'uung@pegagan', NULL, '2025-11-15 21:54:57', NULL),
('104', '1', 'waidah', '6281395687271', 'email@contoh.com', 'PEGAGAN', 'CPE-102', '20', 'active', '0', NULL, 'pppoe_username', 'waidah@pegagan', NULL, '2025-11-15 21:54:57', NULL),
('105', '1', 'waran iis', '6283823371311', 'email@contoh.com', 'UJUNG MULYA', 'CPE-103', '20', 'active', '0', NULL, 'pppoe_username', 'wilda', NULL, '2025-11-15 21:54:57', NULL),
('106', '1', 'warsana', '6283101384784', 'email@contoh.com', 'RAWAGUNDA', 'CPE-104', '20', 'active', '0', NULL, 'pppoe_username', 'warsana', NULL, '2025-11-15 21:54:57', NULL),
('107', '1', 'warsem gunawan', '628888888', 'email@contoh.com', 'KIDUL', 'CPE-105', '20', 'active', '0', NULL, 'pppoe_username', 'gunawan', NULL, '2025-11-15 21:54:57', NULL),
('108', '1', 'winata', '6287728923052', 'email@contoh.com', 'LOR', 'CPE-106', '20', 'active', '0', NULL, 'pppoe_username', 'winata', NULL, '2025-11-15 21:54:57', NULL),
('109', '1', 'wiwin seblak', '6283821322734', 'email@contoh.com', 'SEKOLAHAN', 'CPE-108', '20', 'active', '0', NULL, 'pppoe_username', 'wiwin@seblak', NULL, '2025-11-15 21:54:57', NULL),
('110', '1', 'wiwin udin', '6583076003', 'email@contoh.com', 'TENGAH', 'CPE-109', '20', 'active', '0', NULL, 'pppoe_username', 'nurudin', NULL, '2025-11-15 21:54:57', NULL),
('111', '1', 'YAYAN', '6283893560013', 'email@contoh.com', 'LOR', 'CPE-110', '20', 'active', '0', NULL, 'pppoe_username', 'karmanda', NULL, '2025-11-15 21:54:57', NULL),
('112', '1', 'yohan', '6285858588', 'email@contoh.com', 'KIDUL', 'CPE-111', '20', 'active', '0', NULL, 'pppoe_username', 'yohan', NULL, '2025-11-15 21:54:57', NULL),
('113', '1', 'YUNI', '6283113446936', 'email@contoh.com', 'UJUNG MULYA', 'CPE-112', '20', 'active', '0', NULL, 'pppoe_username', 'yuni@ujungmulya', NULL, '2025-11-15 21:54:57', NULL),
('114', '1', 'yusuf service', '6281321239191', 'email@contoh.com', 'PEGAGAN', 'CPE-113', '20', 'active', '0', NULL, 'pppoe_username', 'yusuf@pegagan', NULL, '2025-11-15 21:54:57', NULL);

-- Table structure for table `billing_invoices`
DROP TABLE IF EXISTS `billing_invoices`;
CREATE TABLE `billing_invoices` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `customer_id` int(10) unsigned NOT NULL,
  `profile_snapshot` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`profile_snapshot`)),
  `period` char(7) NOT NULL,
  `due_date` date NOT NULL,
  `amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `status` enum('draft','unpaid','paid','overdue','cancelled') NOT NULL DEFAULT 'unpaid',
  `paid_at` datetime DEFAULT NULL,
  `payment_channel` varchar(100) DEFAULT NULL,
  `reference_number` varchar(100) DEFAULT NULL,
  `paid_via` varchar(50) DEFAULT NULL,
  `paid_via_agent_id` int(10) unsigned DEFAULT NULL,
  `whatsapp_sent_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_customer_period` (`customer_id`,`period`),
  KEY `idx_status` (`status`),
  CONSTRAINT `fk_billing_invoices_customer` FOREIGN KEY (`customer_id`) REFERENCES `billing_customers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `billing_invoices`
INSERT INTO `billing_invoices` (`id`, `customer_id`, `profile_snapshot`, `period`, `due_date`, `amount`, `status`, `paid_at`, `payment_channel`, `reference_number`, `paid_via`, `paid_via_agent_id`, `whatsapp_sent_at`, `created_at`, `updated_at`) VALUES
('1', '1', '[]', '2025-11', '2025-11-18', '110000.00', 'paid', '2025-11-12 01:06:39', 'admin_manual', 'ADMIN-1762909589421', NULL, NULL, NULL, '2025-11-11 11:29:20', '2025-11-12 08:06:39'),
('3', '2', '[]', '2025-11', '2025-11-19', '110000.00', 'paid', '2025-11-12 05:19:16', 'agent_balance', 'AG-AG001', 'agent_balance', '1', NULL, '2025-11-12 11:11:02', '2025-11-12 11:19:16');

-- Table structure for table `billing_logs`
DROP TABLE IF EXISTS `billing_logs`;
CREATE TABLE `billing_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `invoice_id` bigint(20) unsigned DEFAULT NULL,
  `customer_id` int(10) unsigned DEFAULT NULL,
  `event` varchar(100) NOT NULL,
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`metadata`)),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_invoice_id` (`invoice_id`),
  KEY `idx_customer_id` (`customer_id`),
  CONSTRAINT `fk_billing_logs_customer` FOREIGN KEY (`customer_id`) REFERENCES `billing_customers` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_billing_logs_invoice` FOREIGN KEY (`invoice_id`) REFERENCES `billing_invoices` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `billing_logs`
INSERT INTO `billing_logs` (`id`, `invoice_id`, `customer_id`, `event`, `metadata`, `created_at`) VALUES
('2', '1', '1', 'invoice_paid', '{\"channel\":\"admin_manual\",\"reference\":\"ADMIN-1762909589421\",\"paid_via\":null,\"paid_via_agent_id\":null}', '2025-11-12 08:06:40'),
('3', '3', '2', 'invoice_paid', '{\"channel\":\"agent_balance\",\"reference\":\"AG-AG001\",\"paid_via\":\"agent_balance\",\"paid_via_agent_id\":1}', '2025-11-12 11:19:16'),
('4', NULL, '2', 'customer_isolation_restored', '{\"reason\":\"invoice_paid_by_agent\"}', '2025-11-12 11:19:16'),
('5', '3', '2', 'invoice_paid_by_agent', '{\"agent_id\":1,\"amount\":\"110000.00\",\"fee\":0}', '2025-11-12 11:19:16'),
('6', NULL, NULL, 'invoice_paid', '{\"channel\":\"agent_balance\",\"reference\":\"AG-AG001\",\"paid_via\":\"agent_balance\",\"paid_via_agent_id\":1}', '2025-11-12 11:24:05'),
('7', NULL, NULL, 'customer_isolation_restored', '{\"reason\":\"invoice_paid_by_agent\"}', '2025-11-12 11:24:05'),
('8', NULL, NULL, 'invoice_paid_by_agent', '{\"agent_id\":1,\"amount\":\"100000.00\",\"fee\":0}', '2025-11-12 11:24:05'),
('9', NULL, NULL, 'invoice_paid', '{\"channel\":\"agent_balance\",\"reference\":\"AG-AG001\",\"paid_via\":\"agent_balance\",\"paid_via_agent_id\":1}', '2025-11-12 11:34:27'),
('10', NULL, NULL, 'customer_isolation_restored', '{\"reason\":\"invoice_paid_by_agent\"}', '2025-11-12 11:34:27'),
('11', NULL, NULL, 'invoice_paid_by_agent', '{\"agent_id\":1,\"amount\":\"110000.00\",\"fee\":0}', '2025-11-12 11:34:27'),
('12', NULL, NULL, 'invoice_paid', '{\"channel\":\"agent_balance\",\"reference\":\"AG-AG001\",\"paid_via\":\"agent_balance\",\"paid_via_agent_id\":1}', '2025-11-12 13:36:01'),
('13', NULL, NULL, 'customer_isolation_restored', '{\"reason\":\"invoice_paid_by_agent\"}', '2025-11-12 13:36:01'),
('14', NULL, NULL, 'invoice_paid_by_agent', '{\"agent_id\":1,\"amount\":\"100000.00\",\"fee\":0}', '2025-11-12 13:36:01'),
('15', NULL, NULL, 'invoice_paid', '{\"channel\":\"agent_balance\",\"reference\":\"AG-AG001\",\"paid_via\":\"agent_balance\",\"paid_via_agent_id\":1}', '2025-11-12 14:04:27'),
('16', NULL, NULL, 'customer_isolation_restored', '{\"reason\":\"invoice_paid_by_agent\"}', '2025-11-12 14:04:27'),
('17', NULL, NULL, 'invoice_paid_by_agent', '{\"agent_id\":1,\"amount\":\"165000.00\",\"fee\":-5000}', '2025-11-12 14:04:27');

-- Table structure for table `billing_payments`
DROP TABLE IF EXISTS `billing_payments`;
CREATE TABLE `billing_payments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `invoice_id` bigint(20) unsigned NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `payment_date` datetime NOT NULL DEFAULT current_timestamp(),
  `method` varchar(100) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_by` int(10) unsigned DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_invoice_id` (`invoice_id`),
  CONSTRAINT `fk_billing_payments_invoice` FOREIGN KEY (`invoice_id`) REFERENCES `billing_invoices` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `billing_payments`
INSERT INTO `billing_payments` (`id`, `invoice_id`, `amount`, `payment_date`, `method`, `notes`, `created_by`, `created_at`) VALUES
('1', '1', '110000.00', '2025-11-12 01:06:39', 'admin_manual', NULL, NULL, '2025-11-12 08:06:40'),
('2', '3', '110000.00', '2025-11-12 05:19:16', 'agent_balance', 'Paid by agent: Agent Demo', '0', '2025-11-12 11:19:16');

-- Table structure for table `billing_portal_otps`
DROP TABLE IF EXISTS `billing_portal_otps`;
CREATE TABLE `billing_portal_otps` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `customer_id` int(10) unsigned NOT NULL,
  `identifier` varchar(191) NOT NULL,
  `otp_code` varchar(191) NOT NULL,
  `expires_at` datetime NOT NULL,
  `attempts` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `max_attempts` tinyint(3) unsigned NOT NULL DEFAULT 5,
  `sent_via` enum('whatsapp','sms','email') DEFAULT 'whatsapp',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_customer_identifier` (`customer_id`,`identifier`),
  KEY `idx_expires_at` (`expires_at`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- Table structure for table `billing_profiles`
DROP TABLE IF EXISTS `billing_profiles`;
CREATE TABLE `billing_profiles` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `profile_name` varchar(100) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `billing_cycle` int(11) NOT NULL DEFAULT 30,
  `price_monthly` decimal(12,2) NOT NULL DEFAULT 0.00,
  `speed_label` varchar(100) DEFAULT NULL,
  `mikrotik_profile_normal` varchar(100) NOT NULL,
  `mikrotik_profile_isolation` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_profile_name` (`profile_name`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `billing_profiles`
INSERT INTO `billing_profiles` (`id`, `profile_name`, `price`, `billing_cycle`, `price_monthly`, `speed_label`, `mikrotik_profile_normal`, `mikrotik_profile_isolation`, `description`, `created_at`, `updated_at`) VALUES
('1', 'BRONZE', '0.00', '30', '110000.00', 'Upto 5Mbps', 'BRONZE', 'ISOLIR', '', '2025-11-10 13:21:05', NULL),
('2', 'SILVER', '0.00', '30', '165000.00', 'Upto 5Mbps', 'silver', 'isolir', '', '2025-11-12 14:03:17', NULL);

-- Table structure for table `billing_settings`
DROP TABLE IF EXISTS `billing_settings`;
CREATE TABLE `billing_settings` (
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `billing_settings`
INSERT INTO `billing_settings` (`setting_key`, `setting_value`, `updated_at`) VALUES
('billing_isolation_delay', '1', '2025-11-10 13:00:09'),
('billing_portal_base_url', '', '2025-11-10 13:00:09'),
('billing_portal_contact_body', 'Jam operasional: 08.00 - 22.00', '2025-11-10 13:00:09'),
('billing_portal_contact_email', 'support@ispanda.com', '2025-11-10 13:00:09'),
('billing_portal_contact_heading', 'Butuh bantuan? Hubungi Admin ISP', '2025-11-10 13:00:09'),
('billing_portal_contact_whatsapp', '081234567890', '2025-11-10 13:00:09'),
('billing_portal_otp_digits', '6', '2025-11-10 13:00:09'),
('billing_portal_otp_enabled', '1', '2025-11-17 07:47:50'),
('billing_portal_otp_expiry_minutes', '5', '2025-11-10 13:00:09'),
('billing_portal_otp_max_attempts', '5', '2025-11-10 13:00:09'),
('billing_reminder_days_before', '3,1', '2025-11-10 13:00:09');

-- Table structure for table `daily_agent_sales`
DROP TABLE IF EXISTS `daily_agent_sales`;
CREATE ALGORITHM=UNDEFINED DEFINER=`cpses_al52memi2d`@`localhost` SQL SECURITY DEFINER VIEW `daily_agent_sales` AS select cast(`av`.`created_at` as date) AS `sale_date`,`a`.`agent_code` AS `agent_code`,`a`.`agent_name` AS `agent_name`,`av`.`profile_name` AS `profile_name`,count(0) AS `voucher_count`,sum(`av`.`buy_price`) AS `total_buy_price`,sum(`av`.`sell_price`) AS `total_sell_price`,sum(`av`.`sell_price` - `av`.`buy_price`) AS `total_profit` from (`agent_vouchers` `av` join `agents` `a` on(`av`.`agent_id` = `a`.`id`)) where `av`.`status` <> 'deleted' group by cast(`av`.`created_at` as date),`a`.`id`,`av`.`profile_name`;


-- Table structure for table `digiflazz_products`
DROP TABLE IF EXISTS `digiflazz_products`;
CREATE TABLE `digiflazz_products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `buyer_sku_code` varchar(50) NOT NULL,
  `product_name` varchar(150) NOT NULL,
  `brand` varchar(100) DEFAULT NULL,
  `category` varchar(50) DEFAULT NULL,
  `type` enum('prepaid','postpaid') DEFAULT 'prepaid',
  `price` int(11) NOT NULL,
  `buyer_price` int(11) DEFAULT NULL,
  `seller_price` int(11) DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `desc_header` varchar(150) DEFAULT NULL,
  `desc_footer` text DEFAULT NULL,
  `icon_url` varchar(255) DEFAULT NULL,
  `allow_markup` tinyint(1) DEFAULT 1,
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_sku` (`buyer_sku_code`),
  KEY `idx_category` (`category`),
  KEY `idx_brand` (`brand`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- Table structure for table `digiflazz_transactions`
DROP TABLE IF EXISTS `digiflazz_transactions`;
CREATE TABLE `digiflazz_transactions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `agent_id` int(11) DEFAULT NULL,
  `ref_id` varchar(60) NOT NULL,
  `buyer_sku_code` varchar(50) NOT NULL,
  `customer_no` varchar(50) NOT NULL,
  `customer_name` varchar(100) DEFAULT NULL,
  `status` enum('pending','success','failed','refund') DEFAULT 'pending',
  `message` varchar(255) DEFAULT NULL,
  `price` int(11) DEFAULT 0,
  `sell_price` int(11) DEFAULT 0,
  `serial_number` varchar(100) DEFAULT NULL,
  `response` text DEFAULT NULL,
  `whatsapp_notified` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_ref` (`ref_id`),
  KEY `idx_agent` (`agent_id`),
  KEY `idx_status` (`status`),
  CONSTRAINT `digiflazz_transactions_ibfk_1` FOREIGN KEY (`agent_id`) REFERENCES `agents` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- Table structure for table `payment_gateway_config`
DROP TABLE IF EXISTS `payment_gateway_config`;
CREATE TABLE `payment_gateway_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `gateway_name` varchar(50) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `provider` varchar(50) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 0,
  `is_sandbox` tinyint(1) DEFAULT 1,
  `api_key` varchar(255) DEFAULT NULL,
  `api_secret` varchar(255) DEFAULT NULL,
  `merchant_code` varchar(100) DEFAULT NULL,
  `callback_token` varchar(255) DEFAULT NULL,
  `config_json` text DEFAULT NULL,
  `callback_url` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_gateway` (`gateway_name`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table `payment_gateway_config`
INSERT INTO `payment_gateway_config` (`id`, `gateway_name`, `name`, `provider`, `is_active`, `is_sandbox`, `api_key`, `api_secret`, `merchant_code`, `callback_token`, `config_json`, `callback_url`, `created_at`, `updated_at`) VALUES
('1', 'tripay', NULL, NULL, '1', '1', NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-10 13:00:08', '2025-11-10 13:00:08');

-- Table structure for table `payment_methods`
DROP TABLE IF EXISTS `payment_methods`;
CREATE TABLE `payment_methods` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `gateway_name` varchar(50) NOT NULL,
  `method_code` varchar(50) NOT NULL,
  `method_name` varchar(100) NOT NULL,
  `method_type` varchar(20) NOT NULL,
  `name` varchar(100) NOT NULL DEFAULT '',
  `type` varchar(50) NOT NULL DEFAULT '',
  `display_name` varchar(100) NOT NULL DEFAULT '',
  `icon` varchar(100) DEFAULT NULL,
  `icon_url` varchar(255) DEFAULT NULL,
  `admin_fee_type` enum('percentage','fixed','flat','percent') DEFAULT 'fixed',
  `admin_fee_value` decimal(10,2) DEFAULT 0.00,
  `min_amount` decimal(10,2) DEFAULT 0.00,
  `max_amount` decimal(12,2) DEFAULT 999999999.99,
  `is_active` tinyint(1) DEFAULT 1,
  `sort_order` int(11) DEFAULT 0,
  `config` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_gateway_method` (`gateway_name`,`method_code`)
) ENGINE=InnoDB AUTO_INCREMENT=45 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table `payment_methods`
INSERT INTO `payment_methods` (`id`, `gateway_name`, `method_code`, `method_name`, `method_type`, `name`, `type`, `display_name`, `icon`, `icon_url`, `admin_fee_type`, `admin_fee_value`, `min_amount`, `max_amount`, `is_active`, `sort_order`, `config`, `created_at`, `updated_at`) VALUES
('37', 'tripay', 'QRIS', 'QRIS (Semua Bank & E-Wallet)', 'qris', '', '', '', NULL, NULL, 'flat', '0.00', '10000.00', '5000000.00', '1', '1', NULL, '2025-11-17 07:49:50', '2025-11-17 07:49:50'),
('38', 'tripay', 'BRIVA', 'BRI Virtual Account', 'va', '', '', '', NULL, NULL, 'flat', '4000.00', '10000.00', '5000000.00', '1', '2', NULL, '2025-11-17 07:49:50', '2025-11-17 07:49:50'),
('39', 'tripay', 'BNIVA', 'BNI Virtual Account', 'va', '', '', '', NULL, NULL, 'flat', '4000.00', '10000.00', '5000000.00', '1', '3', NULL, '2025-11-17 07:49:50', '2025-11-17 07:49:50'),
('40', 'tripay', 'BCAVA', 'BCA Virtual Account', 'va', '', '', '', NULL, NULL, 'flat', '4000.00', '10000.00', '5000000.00', '1', '4', NULL, '2025-11-17 07:49:50', '2025-11-17 07:49:50'),
('41', 'tripay', 'OVO', 'OVO', 'ewallet', '', '', '', NULL, NULL, 'percentage', '2.50', '10000.00', '2000000.00', '1', '5', NULL, '2025-11-17 07:49:50', '2025-11-17 07:49:50'),
('42', 'tripay', 'DANA', 'DANA', 'ewallet', '', '', '', NULL, NULL, 'percentage', '2.50', '10000.00', '2000000.00', '1', '6', NULL, '2025-11-17 07:49:50', '2025-11-17 07:49:50'),
('43', 'tripay', 'SHOPEEPAY', 'ShopeePay', 'ewallet', '', '', '', NULL, NULL, 'percentage', '2.50', '10000.00', '2000000.00', '1', '7', NULL, '2025-11-17 07:49:50', '2025-11-17 07:49:50'),
('44', 'tripay', 'ALFAMART', 'Alfamart', 'retail', '', '', '', NULL, NULL, 'flat', '5000.00', '10000.00', '5000000.00', '1', '8', NULL, '2025-11-17 07:49:50', '2025-11-17 07:49:50');

-- Table structure for table `public_sales`
DROP TABLE IF EXISTS `public_sales`;
CREATE TABLE `public_sales` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `transaction_id` varchar(100) NOT NULL,
  `payment_reference` varchar(100) DEFAULT NULL,
  `agent_id` int(11) NOT NULL DEFAULT 1,
  `profile_id` int(11) NOT NULL DEFAULT 1,
  `customer_name` varchar(100) NOT NULL DEFAULT '',
  `customer_phone` varchar(20) NOT NULL DEFAULT '',
  `customer_email` varchar(100) DEFAULT NULL,
  `profile_name` varchar(100) NOT NULL DEFAULT '',
  `price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `admin_fee` decimal(10,2) DEFAULT 0.00,
  `total_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `gateway_name` varchar(50) NOT NULL DEFAULT '',
  `payment_method` varchar(50) DEFAULT NULL,
  `payment_channel` varchar(50) DEFAULT NULL,
  `payment_url` text DEFAULT NULL,
  `qr_url` text DEFAULT NULL,
  `virtual_account` varchar(50) DEFAULT NULL,
  `payment_instructions` text DEFAULT NULL,
  `expired_at` datetime DEFAULT NULL,
  `paid_at` datetime DEFAULT NULL,
  `status` varchar(20) DEFAULT 'pending',
  `voucher_code` varchar(50) DEFAULT NULL,
  `voucher_password` varchar(50) DEFAULT NULL,
  `voucher_generated_at` datetime DEFAULT NULL,
  `voucher_sent_at` datetime DEFAULT NULL,
  `ip_address` varchar(50) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `callback_data` text DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_transaction` (`transaction_id`),
  KEY `fk_public_sales_agent` (`agent_id`),
  KEY `fk_public_sales_profile` (`profile_id`),
  KEY `idx_payment_reference` (`payment_reference`),
  KEY `idx_status` (`status`),
  KEY `idx_customer_phone` (`customer_phone`),
  KEY `idx_created_at` (`created_at`),
  CONSTRAINT `fk_public_sales_agent` FOREIGN KEY (`agent_id`) REFERENCES `agents` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_public_sales_profile` FOREIGN KEY (`profile_id`) REFERENCES `agent_profile_pricing` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- Table structure for table `site_pages`
DROP TABLE IF EXISTS `site_pages`;
CREATE TABLE `site_pages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `page_key` varchar(50) NOT NULL,
  `page_slug` varchar(50) NOT NULL,
  `page_title` varchar(200) NOT NULL,
  `content` text DEFAULT NULL,
  `page_content` text NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `page_slug` (`page_slug`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table `site_pages`
INSERT INTO `site_pages` (`id`, `page_key`, `page_slug`, `page_title`, `content`, `page_content`, `is_active`, `created_at`, `updated_at`) VALUES
('1', '', 'tos', 'Syarat dan Ketentuan', NULL, '<h3>Syarat dan Ketentuan</h3><p>Sesuaikan konten ini.</p>', '1', '2025-11-10 13:00:08', '2025-11-10 13:00:08'),
('2', '', 'privacy', 'Kebijakan Privasi', NULL, '<h3>Kebijakan Privasi</h3><p>Sesuaikan konten ini.</p>', '1', '2025-11-10 13:00:08', '2025-11-10 13:00:08'),
('3', '', 'faq', 'FAQ', NULL, '<h3>FAQ</h3><p>Sesuaikan konten ini.</p>', '1', '2025-11-10 13:00:08', '2025-11-10 13:00:08');

-- Table structure for table `voucher_settings`
DROP TABLE IF EXISTS `voucher_settings`;
CREATE TABLE `voucher_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `setting_key` (`setting_key`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table `voucher_settings`
INSERT INTO `voucher_settings` (`id`, `setting_key`, `setting_value`, `description`, `created_at`, `updated_at`) VALUES
('1', 'voucher_header_text', 'Internet Voucher', NULL, '2025-11-17 07:49:50', '2025-11-17 07:49:50'),
('2', 'voucher_footer_text', 'Terima kasih telah berlangganan', NULL, '2025-11-17 07:49:50', '2025-11-17 07:49:50'),
('3', 'voucher_show_qr', '1', NULL, '2025-11-17 07:49:50', '2025-11-17 07:49:50'),
('4', 'voucher_paper_size', 'A4', NULL, '2025-11-17 07:49:50', '2025-11-17 07:49:50');
