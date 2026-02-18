-- FIX FOR "Unknown column 'user_id'" ERROR
-- This adds the missing user_id column to your existing tables

-- For callback_requests table
ALTER TABLE `callback_requests` 
ADD COLUMN `user_id` int(11) DEFAULT NULL AFTER `id`,
ADD KEY `user_id` (`user_id`);

-- For quick_queries table  
ALTER TABLE `quick_queries` 
ADD COLUMN `user_id` int(11) DEFAULT NULL AFTER `id`,
ADD KEY `user_id` (`user_id`);
