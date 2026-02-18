<?php
session_start();
include "db.php";

$setup_status = "";
$setup_type = "";

if(isset($_POST['setup'])){
    // Create password_resets table
    $create_table = "CREATE TABLE IF NOT EXISTS `password_resets` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `user_id` int(11) NOT NULL,
        `email` varchar(255) NOT NULL,
        `token_hash` varchar(64) NOT NULL,
        `expires_at` datetime NOT NULL,
        `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        KEY `email` (`email`),
        KEY `token_hash` (`token_hash`),
        KEY `expires_at` (`expires_at`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    
    if(mysqli_query($conn, $create_table)){
        $setup_status = "✓ Password reset system setup completed successfully!";
        $setup_type = "success";
    } else {
        $setup_status = "✗ Error: " . mysqli_error($conn);
        $setup_type = "error";
    }
}

// Check if table already exists
$check_table = mysqli_query($conn, "SHOW TABLES LIKE 'password_resets'");
$table_exists = mysqli_num_rows($check_table) > 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset Setup - FurryMart</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        .setup-container {
            background: #fff;
            max-width: 600px;
            width: 100%;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header i {
            font-size: 60px;
            color: #667eea;
            margin-bottom: 15px;
        }
        .header h1 {
            color: #333;
            font-size: 28px;
            margin-bottom: 10px;
        }
        .header p {
            color: #666;
            font-size: 14px;
        }
        .status-box {
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 25px;
            text-align: center;
            font-weight: 600;
        }
        .success {
            background: #d4edda;
            color: #155724;
            border: 2px solid #c3e6cb;
        }
        .error {
            background: #f8d7da;
            color: #721c24;
            border: 2px solid #f5c6cb;
        }
        .info {
            background: #d1ecf1;
            color: #0c5460;
            border: 2px solid #bee5eb;
        }
        .warning {
            background: #fff3cd;
            color: #856404;
            border: 2px solid #ffeeba;
        }
        .feature-list {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 25px;
        }
        .feature-list h3 {
            color: #333;
            margin-bottom: 15px;
            font-size: 18px;
        }
        .feature-list ul {
            list-style: none;
            padding: 0;
        }
        .feature-list li {
            padding: 10px 0;
            color: #555;
            border-bottom: 1px solid #e0e0e0;
        }
        .feature-list li:last-child {
            border-bottom: none;
        }
        .feature-list li i {
            color: #22c55e;
            margin-right: 10px;
            width: 20px;
        }
        .setup-btn {
            width: 100%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: 0.3s;
        }
        .setup-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
        }
        .setup-btn:disabled {
            background: #ccc;
            cursor: not-allowed;
            transform: none;
        }
        .actions {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }
        .actions a {
            flex: 1;
            padding: 12px;
            text-align: center;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            transition: 0.3s;
        }
        .btn-primary {
            background: #518992;
            color: white;
        }
        .btn-primary:hover {
            background: #3d6b73;
        }
        .btn-secondary {
            background: #e9ecef;
            color: #333;
        }
        .btn-secondary:hover {
            background: #d3d6d9;
        }
        .tech-info {
            background: #fff3cd;
            border: 1px solid #ffc107;
            border-radius: 8px;
            padding: 15px;
            margin-top: 20px;
            font-size: 13px;
            color: #856404;
        }
        .tech-info strong {
            display: block;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="setup-container">
        <div class="header">
            <i class="fas fa-cog fa-spin"></i>
            <h1>Password Reset System Setup</h1>
            <p>Setup the database for forgot password functionality</p>
        </div>

        <?php if(!empty($setup_status)): ?>
            <div class="status-box <?php echo $setup_type; ?>">
                <?php echo $setup_status; ?>
            </div>
        <?php endif; ?>

        <?php if($table_exists): ?>
            <div class="status-box success">
                <i class="fas fa-check-circle"></i> Password reset system is already set up!
            </div>
        <?php else: ?>
            <div class="status-box warning">
                <i class="fas fa-exclamation-triangle"></i> Password reset system not yet configured
            </div>
        <?php endif; ?>

        <div class="feature-list">
            <h3><i class="fas fa-list-check"></i> What this setup does:</h3>
            <ul>
                <li><i class="fas fa-database"></i> Creates <code>password_resets</code> table</li>
                <li><i class="fas fa-key"></i> Stores secure reset tokens</li>
                <li><i class="fas fa-clock"></i> Token auto-expiry after 1 hour</li>
                <li><i class="fas fa-shield-alt"></i> SHA-256 hash encryption</li>
                <li><i class="fas fa-envelope"></i> Email-based password recovery</li>
            </ul>
        </div>

        <?php if(!$table_exists): ?>
            <form method="POST">
                <button type="submit" name="setup" class="setup-btn">
                    <i class="fas fa-rocket"></i> Run Setup Now
                </button>
            </form>
        <?php else: ?>
            <button class="setup-btn" disabled>
                <i class="fas fa-check"></i> Already Set Up
            </button>
        <?php endif; ?>

        <div class="actions">
            <a href="forgot_password.php" class="btn-primary">
                <i class="fas fa-key"></i> Forgot Password
            </a>
            <a href="login.php" class="btn-secondary">
                <i class="fas fa-sign-in-alt"></i> Go to Login
            </a>
        </div>

        <div class="tech-info">
            <strong><i class="fas fa-info-circle"></i> Technical Details:</strong>
            <strong>Table Structure:</strong> password_resets table with user_id, email, token_hash, expires_at, created_at<br>
            <strong>Security:</strong> Tokens are hashed using SHA-256 before storage<br>
            <strong>Expiry:</strong> Reset links expire 1 hour after generation<br>
            <strong>Database:</strong> Using FurryMart database connection
        </div>
    </div>
</body>
</html>
