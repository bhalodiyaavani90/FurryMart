-- Free Callback Request System
-- Customer requests callback, you get notified, call them manually
-- NO API COSTS!

CREATE TABLE IF NOT EXISTS `callback_requests` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_name` varchar(100) DEFAULT 'Guest',
  `customer_phone` varchar(15) NOT NULL,
  `request_time` datetime NOT NULL,
  `status` enum('pending','completed','missed','cancelled') DEFAULT 'pending',
  `notes` text,
  `called_at` datetime DEFAULT NULL,
  `called_by` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `status` (`status`),
  KEY `request_time` (`request_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- View recent callback requests
-- SELECT * FROM callback_requests ORDER BY request_time DESC LIMIT 20;

-- Count pending requests
-- SELECT COUNT(*) as pending_callbacks FROM callback_requests WHERE status = 'pending';
