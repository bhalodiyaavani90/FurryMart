-- Birthday Bookings Table Structure
-- FurryMart Birthday Party Booking System
-- Created: January 14, 2026

CREATE TABLE IF NOT EXISTS `birthday_bookings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_email` varchar(255) NOT NULL,
  `user_name` varchar(255) NOT NULL,
  `pet_name` varchar(255) NOT NULL,
  `pet_type` enum('Dog','Cat','Bird','Other') NOT NULL,
  `party_date` date NOT NULL,
  `party_time` varchar(50) NOT NULL,
  `guest_count` int(11) NOT NULL,
  `party_package` varchar(100) DEFAULT NULL,
  `special_requests` text DEFAULT NULL,
  `contact_phone` varchar(20) NOT NULL,
  `status` enum('pending','approved','rejected','completed') NOT NULL DEFAULT 'pending',
  `admin_notes` text DEFAULT NULL,
  `booking_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_email` (`user_email`),
  KEY `party_date` (`party_date`),
  KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Index for better query performance
CREATE INDEX idx_user_email_date ON birthday_bookings(user_email, party_date);
CREATE INDEX idx_status_date ON birthday_bookings(status, party_date);
