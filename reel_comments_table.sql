-- FURRYMART: Pet Feelings Reels Comments System
-- This table stores user comments on pet mood reels
-- Run this in phpMyAdmin SQL tab or MySQL command line

-- First, check if table exists and drop it (optional, remove these lines if you want to keep existing data)
-- DROP TABLE IF EXISTS reel_comments;

-- Create the comments table
CREATE TABLE IF NOT EXISTS reel_comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    reel_id INT NOT NULL,
    user_id INT NOT NULL,
    username VARCHAR(100) NOT NULL,
    comment TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_reel_id (reel_id),
    INDEX idx_user_id (user_id),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Verify the table was created
SELECT 'Comments table created successfully!' as Status;
