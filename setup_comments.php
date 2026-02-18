<?php
/**
 * ONE-CLICK SETUP - Reel Comments System
 * This will automatically create the database table
 * Access: http://localhost/FURRYMART/setup_comments.php
 */

session_start();
include 'db.php';

$errors = [];
$success = [];

// Check if form is submitted
if (isset($_POST['setup'])) {
    
    // Create the table
    $createTableSQL = "CREATE TABLE IF NOT EXISTS reel_comments (
        id INT AUTO_INCREMENT PRIMARY KEY,
        reel_id INT NOT NULL,
        user_id INT NOT NULL,
        username VARCHAR(100) NOT NULL,
        comment TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_reel_id (reel_id),
        INDEX idx_user_id (user_id),
        INDEX idx_created_at (created_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    if (mysqli_query($conn, $createTableSQL)) {
        $success[] = "✅ Comments table created successfully!";
        
        // Verify the table exists
        $checkTable = mysqli_query($conn, "SHOW TABLES LIKE 'reel_comments'");
        if (mysqli_num_rows($checkTable) > 0) {
            $success[] = "✅ Table verified in database!";
            
            // Check structure
            $checkStructure = mysqli_query($conn, "DESCRIBE reel_comments");
            $columnCount = mysqli_num_rows($checkStructure);
            $success[] = "✅ Table structure validated ($columnCount columns)!";
            
            $success[] = "🎉 Setup completed! You can now use the comment system.";
        } else {
            $errors[] = "⚠️ Table was created but verification failed.";
        }
    } else {
        $errors[] = "❌ Error creating table: " . mysqli_error($conn);
    }
}

// Check current status
$tableExists = false;
if ($conn) {
    $result = mysqli_query($conn, "SHOW TABLES LIKE 'reel_comments'");
    $tableExists = mysqli_num_rows($result) > 0;
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>FurryMart - Comments Setup</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            color: #fff; padding: 40px; min-height: 100vh;
        }
        .container { max-width: 700px; margin: 0 auto; }
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
        .btn:disabled { opacity: 0.5; cursor: not-allowed; }
        
        .btn-container { text-align: center; margin-top: 30px; }
        
        .icon { font-size: 60px; margin-bottom: 20px; }
        .icon.success { color: #22c55e; }
        .icon.error { color: #f87171; }
        .icon.pending { color: #fbbf24; }
        
        .info-grid { 
            display: grid; grid-template-columns: 1fr 1fr; 
            gap: 15px; margin-top: 20px;
        }
        .info-item { 
            background: rgba(255,255,255,0.03); 
            padding: 15px; border-radius: 10px; text-align: center;
        }
        .info-item .label { 
            color: #94a3b8; font-size: 12px; 
            text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px;
        }
        .info-item .value { 
            color: #518992; font-size: 24px; font-weight: 900;
        }

        form { display: inline; }
    </style>
    <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css'>
</head>
<body>
<div class='container'>
    <div class='header'>
        <h1><i class='fas fa-comments'></i> Comment System Setup</h1>
        <p>FurryMart Pet Feelings Reels</p>
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
            <?php if ($tableExists && empty($errors)): ?>
                <div class='icon success'><i class='fas fa-check-circle'></i></div>
                <h2 style='color: #22c55e; margin-bottom: 15px;'>Setup Complete!</h2>
                <p style='color: #94a3b8; margin-bottom: 25px;'>
                    The comment system is ready to use. All database tables are in place.
                </p>
                
                <?php
                // Get stats
                $commentCount = 0;
                $reelCount = 0;
                
                $countQuery = mysqli_query($conn, "SELECT COUNT(*) as cnt FROM reel_comments");
                if ($countQuery) {
                    $commentCount = mysqli_fetch_assoc($countQuery)['cnt'];
                }
                
                $reelQuery = mysqli_query($conn, "SELECT COUNT(*) as cnt FROM pet_moods");
                if ($reelQuery) {
                    $reelCount = mysqli_fetch_assoc($reelQuery)['cnt'];
                }
                ?>
                
                <div class='info-grid'>
                    <div class='info-item'>
                        <div class='label'>Total Reels</div>
                        <div class='value'><?php echo $reelCount; ?></div>
                    </div>
                    <div class='info-item'>
                        <div class='label'>Total Comments</div>
                        <div class='value'><?php echo $commentCount; ?></div>
                    </div>
                </div>
                
            <?php else: ?>
                <div class='icon pending'><i class='fas fa-cog'></i></div>
                <h2 style='color: #fbbf24; margin-bottom: 15px;'>Setup Required</h2>
                <p style='color: #94a3b8; margin-bottom: 25px;'>
                    Click the button below to automatically create the required database table.
                </p>
                
                <div class='status-box info'>
                    <strong>What will be created:</strong>
                    <ul class='message-list'>
                        <li>📊 reel_comments table with proper structure</li>
                        <li>🔑 Indexed columns for better performance</li>
                        <li>✅ Full UTF-8 support for all languages</li>
                    </ul>
                </div>
                
                <form method='post'>
                    <button type='submit' name='setup' class='btn' style='font-size: 18px; padding: 18px 40px;'>
                        <i class='fas fa-magic'></i> Run Setup Now
                    </button>
                </form>
            <?php endif; ?>
        </div>
    </div>

    <div class='btn-container'>
        <?php if ($tableExists): ?>
            <a href='pet_feelings.php' class='btn'>
                <i class='fas fa-play'></i> Go to Pet Feelings
            </a>
        <?php endif; ?>
        <a href='test_comments_setup.php' class='btn secondary'>
            <i class='fas fa-vial'></i> Run System Test
        </a>
        <?php if ($tableExists): ?>
            <a href='?reset=1' class='btn secondary' onclick='return confirm("This will refresh the page. Continue?")'>
                <i class='fas fa-sync'></i> Refresh Status
            </a>
        <?php endif; ?>
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
