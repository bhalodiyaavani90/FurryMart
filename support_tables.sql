-- ========================================
-- FurryMart Support System Tables
-- ========================================
-- Three types of support requests:
-- 1. Contact Form (existing: contact_queries)
-- 2. Callback Requests (new)
-- 3. Quick Queries via Email (new)
-- ========================================

-- Table for Callback Requests
CREATE TABLE IF NOT EXISTS `callback_requests` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `status` enum('pending','in_progress','completed','resolved','cancelled') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `status` (`status`),
  KEY `created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table for Quick Queries (Email Support)
CREATE TABLE IF NOT EXISTS `quick_queries` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `status` enum('unread','read','pending','in_process','resolved') DEFAULT 'unread',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `status` (`status`),
  KEY `created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Add indexes to existing contact_queries table if not exists
-- (Check your existing table structure, these might already exist)
ALTER TABLE `contact_queries` 
ADD INDEX IF NOT EXISTS `idx_status` (`status`),
ADD INDEX IF NOT EXISTS `idx_created_at` (`created_at`);
