<?php
session_start();
include "db.php";

// Set timezone to match database
date_default_timezone_set('Asia/Kolkata');

$message = "";
$message_type = "";

if(isset($_POST['submit'])){
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    
    // Check if email exists
    $check_query = "SELECT id, first_name FROM users WHERE email = '$email'";
    $check_result = mysqli_query($conn, $check_query);
    
    if(mysqli_num_rows($check_result) > 0){
        $user = mysqli_fetch_assoc($check_result);
        
        // Generate unique reset token
        $token = bin2hex(random_bytes(32));
        $token_hash = hash('sha256', $token);
        
        // Set expiry time (1 hour from now) in IST
        $current_time = new DateTime('now', new DateTimeZone('Asia/Kolkata'));
        $expiry_time = $current_time->modify('+1 hour');
        $expiry = $expiry_time->format('Y-m-d H:i:s');
        
        // Create password_resets table if it doesn't exist
        $create_table = "CREATE TABLE IF NOT EXISTS password_resets (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            email VARCHAR(255) NOT NULL,
            token_hash VARCHAR(64) NOT NULL,
            expires_at DATETIME NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX(email),
            INDEX(token_hash)
        )";
        mysqli_query($conn, $create_table);
        
        // Delete any existing reset tokens for this user
        mysqli_query($conn, "DELETE FROM password_resets WHERE email = '$email'");
        
        // Insert new reset token
        $insert_query = "INSERT INTO password_resets (user_id, email, token_hash, expires_at) 
                        VALUES ('{$user['id']}', '$email', '$token_hash', '$expiry')";
        
        if(mysqli_query($conn, $insert_query)){
            // In a real-world scenario, you would send this link via email
            // For now, we'll display it on the page
            $reset_link = "http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/reset_password.php?token=" . $token;
            
            // Get readable times for display
            $now = new DateTime('now', new DateTimeZone('Asia/Kolkata'));
            $now_display = $now->format('d M Y, h:i:s A');
            $expiry_display = $expiry_time->format('d M Y, h:i:s A');
            
            $message = "Password reset link generated! Copy the link below:<br><br>
                       <div style='background:#f0f9ff; padding:15px; border-radius:8px; word-break:break-all; border:2px dashed #0284c7;'>
                       <strong>Reset Link:</strong><br>
                       <a href='$reset_link' style='color:#0284c7;'>$reset_link</a>
                       </div><br>
                       <small style='color:#666;'>⏱️ Generated: <strong>$now_display</strong></small><br>
                       <small style='color:#666;'>⏱️ Expires: <strong>$expiry_display</strong> (1 hour validity)</small><br><br>
                       <small style='color:#888;'>💡 In production, this link would be sent to your email: <strong>$email</strong></small>";
            $message_type = "success";
        } else {
            $message = "Failed to generate reset link. Please try again.";
            $message_type = "error";
        }
    } else {
        $message = "No account found with that email address.";
        $message_type = "error";
    }
}
?>

<?php include "includes/header.php"; ?>

<style>
    .forgot-wrapper {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 70vh;
        padding: 20px;
    }
    .forgot-container {
        width: 100%;
        max-width: 500px;
        background: #fff;
        padding: 40px;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        border-top: 5px solid #ff6b6b;
    }
    .forgot-container h2 {
        text-align: center;
        color: var(--text);
        margin-bottom: 10px;
        font-size: 26px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
    }
    .forgot-container h2 i {
        color: #ff6b6b;
    }
    .forgot-container p.subtitle {
        text-align: center;
        color: #777;
        margin-bottom: 30px;
        font-size: 14px;
        line-height: 1.6;
    }
    .form-group {
        margin-bottom: 20px;
        display: flex;
        flex-direction: column;
    }
    .form-group label {
        font-size: 14px;
        font-weight: 600;
        margin-bottom: 8px;
        color: #555;
    }
    .form-group input {
        padding: 12px;
        border: 1px solid #ddd;
        border-radius: 8px;
        font-size: 15px;
        outline: none;
        transition: 0.3s;
    }
    .form-group input:focus {
        border-color: #ff6b6b;
        box-shadow: 0 0 5px rgba(255, 107, 107, 0.3);
    }
    .error-msg {
        background: #ffebee;
        color: #c62828;
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 20px;
        font-size: 14px;
        border: 1px solid #ef9a9a;
    }
    .success-msg {
        background: #e8f5e9;
        color: #2e7d32;
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 20px;
        font-size: 14px;
        border: 1px solid #a5d6a7;
        line-height: 1.8;
    }
    .submit-btn {
        width: 100%;
        background: linear-gradient(135deg, #ff6b6b 0%, #ff5252 100%);
        color: white;
        padding: 14px;
        border: none;
        border-radius: 8px;
        font-size: 16px;
        font-weight: bold;
        cursor: pointer;
        transition: 0.3s;
    }
    .submit-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(255, 107, 107, 0.4);
    }
    .back-links {
        display: flex;
        justify-content: space-between;
        margin-top: 25px;
        font-size: 13px;
    }
    .back-links a {
        color: #666;
        text-decoration: none;
        transition: 0.3s;
    }
    .back-links a:hover {
        color: var(--primary);
    }
    .info-box {
        background: #fff3cd;
        border: 1px solid #ffc107;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 20px;
        font-size: 13px;
        color: #856404;
    }
    .info-box i {
        margin-right: 8px;
    }
</style>

<div class="forgot-wrapper">
    <div class="forgot-container">
        <h2><i class="fas fa-key"></i> Forgot Password?</h2>
        <p class="subtitle">
            Don't worry! Enter your email address below and we'll help you reset your password.
        </p>

        <?php if(!empty($message)): ?>
            <div class="<?php echo $message_type; ?>-msg">
                <i class="fas fa-<?php echo $message_type == 'success' ? 'check-circle' : 'exclamation-circle'; ?>"></i> 
                <?php echo $message; ?>
            </div>
        <?php else: ?>
            <div class="info-box">
                <i class="fas fa-info-circle"></i>
                <strong>How it works:</strong> Enter your registered email address. You'll receive a password reset link valid for 1 hour.
            </div>
        <?php endif; ?>

        <?php if($message_type != 'success'): ?>
        <form action="forgot_password.php" method="POST">
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" placeholder="Enter your registered email" required>
            </div>

            <button type="submit" name="submit" class="submit-btn">
                <i class="fas fa-paper-plane"></i> Send Reset Link
            </button>
        </form>
        <?php endif; ?>

        <div class="back-links">
            <a href="login.php"><i class="fas fa-arrow-left"></i> Back to Login</a>
            <a href="index.php">Go to Homepage <i class="fas fa-home"></i></a>
        </div>
    </div>
</div>

<?php include "includes/footer.php"; ?>
