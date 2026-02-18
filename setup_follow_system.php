<?php
/**
 * ONE-CLICK SETUP - Follow System & NEW Badges
 * This will automatically create the required database tables and columns
 * Access: http://localhost/FURRYMART/setup_follow_system.php
 */

session_start();
include 'db.php';

$errors = [];
$success = [];
$warnings = [];

// Check if form is submitted
if (isset($_POST['setup'])) {
    
    // 1. Create reel_follows table
    $createFollowsTable = "CREATE TABLE IF NOT EXISTS reel_follows (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        category VARCHAR(50) NOT NULL,
        followed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY unique_follow (user_id, category),
        INDEX idx_user_id (user_id),
        INDEX idx_category (category)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    if (mysqli_query($conn, $createFollowsTable)) {
        $success[] = "✅ Follow system table created successfully!";
    } else {
        $errors[] = "❌ Error creating follow table: " . mysqli_error($conn);
    }
    
    // 2. Check if pet_moods table exists
    $checkPetMoods = mysqli_query($conn, "SHOW TABLES LIKE 'pet_moods'");
    if (mysqli_num_rows($checkPetMoods) > 0) {
        
        // 3. Add columns to pet_moods if they don't exist
        $alterQueries = [
            "ALTER TABLE pet_moods ADD COLUMN IF NOT EXISTS is_new TINYINT(1) DEFAULT 1",
            "ALTER TABLE pet_moods ADD COLUMN IF NOT EXISTS created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP"
        ];
        
        foreach ($alterQueries as $query) {
            $result = mysqli_query($conn, $query);
            if ($result || stripos(mysqli_error($conn), 'Duplicate column') !== false) {
                // Success or column already exists
            } else {
                $warnings[] = "⚠️ Could not add column: " . mysqli_error($conn);
            }
        }
        
        $success[] = "✅ Pet moods table updated with NEW tracking columns!";
        
        // 4. Set created_at for existing reels
        $updateExisting = "UPDATE pet_moods SET created_at = NOW() WHERE created_at IS NULL OR created_at = '0000-00-00 00:00:00'";
        mysqli_query($conn, $updateExisting);
        
        // 5. Mark recent reels as new
        $markNew = "UPDATE pet_moods SET is_new = 1 WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
        mysqli_query($conn, $markNew);
        
        $success[] = "✅ Existing reels marked with timestamps!";
        
    } else {
        $warnings[] = "⚠️ pet_moods table doesn't exist yet. Create it first.";
    }
    
    $success[] = "🎉 Setup completed! Follow system is ready to use.";
}

// Check current status
$followsTableExists = false;
$petMoodsExists = false;
$hasColumns = false;

if ($conn) {
    $result = mysqli_query($conn, "SHOW TABLES LIKE 'reel_follows'");
    $followsTableExists = mysqli_num_rows($result) > 0;
    
    $result = mysqli_query($conn, "SHOW TABLES LIKE 'pet_moods'");
    $petMoodsExists = mysqli_num_rows($result) > 0;
    
    if ($petMoodsExists) {
        $columns = mysqli_query($conn, "SHOW COLUMNS FROM pet_moods LIKE 'is_new'");
        $hasColumns = mysqli_num_rows($columns) > 0;
    }
}

$isSetup = $followsTableExists && $hasColumns;

?>
<!DOCTYPE html>
<html>
<head>
    <title>FurryMart - Follow System Setup</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            color: #fff; padding: 40px; min-height: 100vh;
        }
        .container { max-width: 800px; margin: 0 auto; }
        .header { text-align: center; margin-bottom: 40px; }
        .header h1 { color: #518992; font-size: 36px; margin-bottom: 10px; }
        .header p { color: #94a3b8; font-size: 16px; }
        
        .card { 
            background: #1e293b; padding: 30px; border-radius: 20px; 
            box-shadow: 0 20px 60px rgba(0,0,0,0.5);
            border: 1px solid rgba(255,255,255,0.05);
            margin-bottom: 20px;
        }
        
        .status-box {
            padding: 20px; border-radius: 12px; margin-bottom: 20px;
            border-left: 5px solid;
        }
        .status-box.success { 
            background: rgba(34, 197, 94, 0.1); 
            border-color: #22c55e; color: #22c55e;
        }
        .status-box.error { 
            background: rgba(248, 113, 113, 0.1); 
            border-color: #f87171; color: #f87171;
        }
        .status-box.warning { 
            background: rgba(251, 191, 36, 0.1); 
            border-color: #fbbf24; color: #fbbf24;
        }
        .status-box.info { 
            background: rgba(81, 137, 146, 0.1); 
            border-color: #518992; color: #518992;
        }
        
        .message-list { list-style: none; padding: 0; }
        .message-list li { 
            padding: 12px; margin: 8px 0; 
            background: rgba(255,255,255,0.03); 
            border-radius: 8px;
            font-size: 15px;
        }
        
        .btn {
            display: inline-block; background: #518992; color: #fff;
            padding: 16px 32px; border-radius: 12px; text-decoration: none;
            font-weight: 700; font-size: 16px; border: none; cursor: pointer;
            transition: all 0.3s; margin: 5px;
        }
        .btn:hover { background: #6ba3ac; transform: translateY(-2px); }
        .btn.secondary { background: #334155; }
        .btn.secondary:hover { background: #475569; }
        
        .btn-container { text-align: center; margin-top: 30px; }
        
        .icon { font-size: 60px; margin-bottom: 20px; }
        .icon.success { color: #22c55e; }
        .icon.pending { color: #fbbf24; }
        
        .feature-grid { 
            display: grid; grid-template-columns: 1fr 1fr; 
            gap: 15px; margin-top: 20px;
        }
        .feature-item { 
            background: rgba(255,255,255,0.03); 
            padding: 20px; border-radius: 10px;
            border: 1px solid rgba(255,255,255,0.05);
        }
        .feature-item h4 { 
            color: #518992; margin-bottom: 10px;
            font-size: 14px; text-transform: uppercase;
        }
        .feature-item p { 
            color: #94a3b8; font-size: 13px; line-height: 1.5;
        }

        form { display: inline; }
    </style>
    <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css'>
</head>
<body>
<div class='container'>
    <div class='header'>
        <h1><i class='fas fa-user-plus'></i> Follow System Setup</h1>
        <p>Instagram-style Click-to-Mute + Follow System with NEW Badges</p>
    </div>

    <div class='card'>
        <?php if (!empty($success)): ?>
            <div class='status-box success'>
                <ul class='message-list'>
                    <?php foreach ($success as $msg): ?>
                        <li><?php echo $msg; ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <?php if (!empty($warnings)): ?>
            <div class='status-box warning'>
                <ul class='message-list'>
                    <?php foreach ($warnings as $msg): ?>
                        <li><?php echo $msg; ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <?php if (!empty($errors)): ?>
            <div class='status-box error'>
                <ul class='message-list'>
                    <?php foreach ($errors as $msg): ?>
                        <li><?php echo $msg; ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <div style='text-align: center;'>
            <?php if ($isSetup && empty($errors)): ?>
                <div class='icon success'><i class='fas fa-check-circle'></i></div>
                <h2 style='color: #22c55e; margin-bottom: 15px;'>Setup Complete!</h2>
                <p style='color: #94a3b8; margin-bottom: 25px;'>
                    Follow system and NEW badges are ready to use!
                </p>
                
            <?php else: ?>
                <div class='icon pending'><i class='fas fa-cog'></i></div>
                <h2 style='color: #fbbf24; margin-bottom: 15px;'>Setup Required</h2>
                <p style='color: #94a3b8; margin-bottom: 25px;'>
                    Click below to automatically set up the follow system and NEW badge features.
                </p>
                
                <form method='post'>
                    <button type='submit' name='setup' class='btn' style='font-size: 18px; padding: 18px 40px;'>
                        <i class='fas fa-magic'></i> Run Setup Now
                    </button>
                </form>
            <?php endif; ?>
        </div>

        <div class='feature-grid' style='margin-top: 30px;'>
            <div class='feature-item'>
                <h4><i class='fas fa-volume-up'></i> Click-to-Mute</h4>
                <p>Tap anywhere on video to toggle mute/unmute, just like Instagram!</p>
            </div>
            <div class='feature-item'>
                <h4><i class='fas fa-user-plus'></i> Follow System</h4>
                <p>Follow categories (Happy, Sad, etc.) to see NEW badges on fresh content!</p>
            </div>
            <div class='feature-item'>
                <h4><i class='fas fa-sparkles'></i> NEW+ Badge</h4>
                <p>Pulsing red badge appears only for followers when new reels are added!</p>
            </div>
            <div class='feature-item'>
                <h4><i class='fas fa-clock'></i> Auto-Expire</h4>
                <p>NEW badges automatically disappear after 7 days!</p>
            </div>
        </div>
    </div>

    <div class='btn-container'>
        <?php if ($isSetup): ?>
            <a href='pet_feelings.php' class='btn'>
                <i class='fas fa-play'></i> Go to Pet Feelings
            </a>
            <a href='NEW_FEATURES_GUIDE.md' class='btn secondary'>
                <i class='fas fa-book'></i> Read Guide
            </a>
        <?php endif; ?>
        <a href='?refresh=1' class='btn secondary'>
            <i class='fas fa-sync'></i> Refresh Status
        </a>
    </div>

    <div style='text-align: center; margin-top: 40px; color: #64748b; font-size: 14px;'>
        <p><i class='fas fa-shield-alt'></i> Safe to run multiple times • No data will be lost</p>
    </div>
</div>
</body>
</html>
<?php
if ($conn) mysqli_close($conn);
?>
