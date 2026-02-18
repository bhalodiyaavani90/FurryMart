-- FURRYMART: Reel Likes and Saves System
-- This table stores user likes and saves for pet mood reels
-- Run this in phpMyAdmin SQL tab or MySQL command line

-- Create the likes table
CREATE TABLE IF NOT EXISTS reel_likes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    reel_id INT NOT NULL,
    user_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_like (reel_id, user_id),
    INDEX idx_reel_id (reel_id),
    INDEX idx_user_id (user_id),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create the saves table
CREATE TABLE IF NOT EXISTS reel_saves (
    id INT AUTO_INCREMENT PRIMARY KEY,
    reel_id INT NOT NULL,
    user_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_save (reel_id, user_id),
    INDEX idx_reel_id (reel_id),
    INDEX idx_user_id (user_id),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Verify the tables were created
SELECT 'Likes and Saves tables created successfully!' as Status;
