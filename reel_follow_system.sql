-- FURRYMART: Reel Follow System
-- This creates tables for following reels and tracking new content

-- Table to store user follows (which categories/moods user follows)
CREATE TABLE IF NOT EXISTS reel_follows (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    category VARCHAR(50) NOT NULL,
    followed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_follow (user_id, category),
    INDEX idx_user_id (user_id),
    INDEX idx_category (category)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Add 'is_new' and 'created_at' columns to pet_moods if they don't exist
ALTER TABLE pet_moods 
ADD COLUMN IF NOT EXISTS is_new TINYINT(1) DEFAULT 1,
ADD COLUMN IF NOT EXISTS created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
ADD COLUMN IF NOT EXISTS admin_added_at TIMESTAMP NULL;

-- Mark reels added in last 7 days as new
UPDATE pet_moods 
SET is_new = 1 
WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY);

-- Verification
SELECT 'Follow system tables created successfully!' as Status;
