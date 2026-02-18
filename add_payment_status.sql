-- Add payment_status column to orders table
-- Run this SQL in your phpMyAdmin or MySQL console

ALTER TABLE `orders` 
ADD COLUMN `payment_status` ENUM('Pending', 'Paid', 'Failed') NOT NULL DEFAULT 'Pending' 
AFTER `order_status`;

-- Update existing orders:
-- Set UPI/Card payments as Paid
UPDATE `orders` 
SET `payment_status` = 'Paid' 
WHERE `payment_method` IN ('UPI', 'Card');

-- Set COD payments as Paid only if delivered
UPDATE `orders` 
SET `payment_status` = 'Paid' 
WHERE `payment_method` = 'COD' AND `order_status` = 'Delivered';

-- Leave COD orders as Pending if not delivered
UPDATE `orders` 
SET `payment_status` = 'Pending' 
WHERE `payment_method` = 'COD' AND `order_status` != 'Delivered';
