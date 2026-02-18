<?php
session_start();
include "db.php";
date_default_timezone_set('Asia/Kolkata');

// Get current time
$current_time = new DateTime('now', new DateTimeZone('Asia/Kolkata'));
$current_timestamp = $current_time->format('Y-m-d H:i:s');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset Debug - FurryMart</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Courier New', monospace;
            background: #1a1a2e;
            color: #eee;
            padding: 20px;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: #16213e;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.5);
        }
        h1 {
            color: #0f0;
            text-align: center;
            margin-bottom: 10px;
            text-shadow: 0 0 10px #0f0;
        }
        .subtitle {
            text-align: center;
            color: #888;
            margin-bottom: 30px;
            font-size: 14px;
        }
        .info-box {
            background: #0a0e27;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid #0f0;
        }
        .info-box h3 {
            color: #0ff;
            margin-bottom: 15px;
        }
        .info-item {
            padding: 8px 0;
            border-bottom: 1px solid #2a2a3e;
        }
        .info-item:last-child {
            border-bottom: none;
        }
        .label {
            color: #999;
            font-size: 12px;
        }
        .value {
            color: #fff;
            font-weight: bold;
            margin-left: 10px;
        }
        .success {
            color: #0f0;
        }
        .error {
            color: #f00;
        }
        .warning {
            color: #ff0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: #0a0e27;
            border-radius: 8px;
            overflow: hidden;
        }
        th {
            background: #0f3460;
            color: #0ff;
            padding: 12px;
            text-align: left;
            font-size: 12px;
        }
        td {
            padding: 10px;
            border-bottom: 1px solid #2a2a3e;
            font-size: 11px;
        }
        tr:hover {
            background: #1a1f3a;
        }
        .expired {
            color: #f00;
        }
        .valid {
            color: #0f0;
        }
        .actions {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }
        .btn {
            flex: 1;
            padding: 12px;
            text-align: center;
            text-decoration: none;
            border-radius: 8px;
            font-weight: bold;
            transition: 0.3s;
        }
        .btn-primary {
            background: #0f3460;
            color: #0ff;
            border: 2px solid #0ff;
        }
        .btn-primary:hover {
            background: #0ff;
            color: #000;
        }
        .btn-danger {
            background: #8b0000;
            color: #fff;
            border: 2px solid #f00;
        }
        .btn-danger:hover {
            background: #f00;
        }
        code {
            background: #000;
            padding: 2px 6px;
            border-radius: 4px;
            color: #0f0;
            font-size: 11px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1><i class="fas fa-bug"></i> PASSWORD RESET DEBUG PANEL</h1>
        <p class="subtitle">System Diagnostics & Token Analysis</p>

        <!-- System Information -->
        <div class="info-box">
            <h3><i class="fas fa-server"></i> System Information</h3>
            <div class="info-item">
                <span class="label">PHP Timezone:</span>
                <span class="value success"><?php echo date_default_timezone_get(); ?></span>
            </div>
            <div class="info-item">
                <span class="label">Current PHP Time:</span>
                <span class="value"><?php echo $current_time->format('d M Y, h:i:s A'); ?></span>
            </div>
            <div class="info-item">
                <span class="label">MySQL Time:</span>
                <span class="value">
                    <?php 
                    $mysql_time = mysqli_fetch_row(mysqli_query($conn, "SELECT NOW()"));
                    echo $mysql_time[0]; 
                    ?>
                </span>
            </div>
            <div class="info-item">
                <span class="label">Database:</span>
                <span class="value success">furrymart</span>
            </div>
        </div>

        <!-- Active Reset Tokens -->
        <div class="info-box">
            <h3><i class="fas fa-key"></i> Active Reset Tokens</h3>
            <?php
            $tokens_query = "SELECT pr.*, u.first_name, u.email as user_email 
                            FROM password_resets pr 
                            LEFT JOIN users u ON pr.user_id = u.id 
                            ORDER BY pr.created_at DESC 
                            LIMIT 20";
            $tokens_result = mysqli_query($conn, $tokens_query);
            $token_count = mysqli_num_rows($tokens_result);
            ?>
            <p style="margin-bottom: 15px; color: #999;">
                Found <span class="value <?php echo $token_count > 0 ? 'success' : 'warning'; ?>"><?php echo $token_count; ?></span> token(s) in database
            </p>
            
            <?php if($token_count > 0): ?>
                <div style="overflow-x: auto;">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>User</th>
                                <th>Email</th>
                                <th>Token Hash (First 20 chars)</th>
                                <th>Created At</th>
                                <th>Expires At</th>
                                <th>Status</th>
                                <th>Time Left</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($token = mysqli_fetch_assoc($tokens_result)): 
                                $expires_dt = new DateTime($token['expires_at'], new DateTimeZone('Asia/Kolkata'));
                                $is_expired = $expires_dt < $current_time;
                                $diff = $current_time->diff($expires_dt);
                                
                                if($is_expired) {
                                    $time_left = "Expired " . $diff->format('%i min ago');
                                } else {
                                    $time_left = $diff->format('%i min left');
                                }
                            ?>
                            <tr>
                                <td><?php echo $token['id']; ?></td>
                                <td><?php echo htmlspecialchars($token['first_name']); ?></td>
                                <td><?php echo htmlspecialchars($token['email']); ?></td>
                                <td><code><?php echo substr($token['token_hash'], 0, 20); ?>...</code></td>
                                <td><?php echo date('d M, h:i A', strtotime($token['created_at'])); ?></td>
                                <td><?php echo $expires_dt->format('d M, h:i A'); ?></td>
                                <td class="<?php echo $is_expired ? 'expired' : 'valid'; ?>">
                                    <?php echo $is_expired ? '❌ EXPIRED' : '✅ VALID'; ?>
                                </td>
                                <td class="<?php echo $is_expired ? 'expired' : 'valid'; ?>">
                                    <?php echo $time_left; ?>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p style="text-align: center; color: #666; padding: 20px;">
                    <i class="fas fa-inbox"></i> No reset tokens found. Generate one from <a href="forgot_password.php" style="color: #0ff;">forgot password page</a>.
                </p>
            <?php endif; ?>
        </div>

        <!-- Statistics -->
        <div class="info-box">
            <h3><i class="fas fa-chart-bar"></i> Statistics</h3>
            <?php
            $total_tokens = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM password_resets"));
            $expired_tokens = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM password_resets WHERE expires_at < '$current_timestamp'"));
            $valid_tokens = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM password_resets WHERE expires_at >= '$current_timestamp'"));
            ?>
            <div class="info-item">
                <span class="label">Total Tokens Generated:</span>
                <span class="value"><?php echo $total_tokens[0]; ?></span>
            </div>
            <div class="info-item">
                <span class="label">Currently Valid:</span>
                <span class="value success"><?php echo $valid_tokens[0]; ?></span>
            </div>
            <div class="info-item">
                <span class="label">Expired:</span>
                <span class="value error"><?php echo $expired_tokens[0]; ?></span>
            </div>
        </div>

        <!-- Actions -->
        <div class="actions">
            <a href="forgot_password.php" class="btn btn-primary">
                <i class="fas fa-key"></i> Generate New Token
            </a>
            <a href="?clean_expired=1" class="btn btn-danger" onclick="return confirm('Delete all expired tokens?')">
                <i class="fas fa-trash"></i> Clean Expired Tokens
            </a>
        </div>

        <?php
        // Clean expired tokens
        if(isset($_GET['clean_expired'])){
            $delete_query = "DELETE FROM password_resets WHERE expires_at < '$current_timestamp'";
            if(mysqli_query($conn, $delete_query)){
                $deleted_count = mysqli_affected_rows($conn);
                echo "<div style='background: #0a0e27; padding: 15px; border-radius: 8px; margin-top: 20px; border-left: 4px solid #0f0;'>
                      <span class='success'>✓ Deleted $deleted_count expired token(s)</span>
                      </div>
                      <meta http-equiv='refresh' content='2;url=debug_password_reset.php'>";
            }
        }
        ?>
    </div>
</body>
</html>
