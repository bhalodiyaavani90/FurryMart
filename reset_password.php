<?php
session_start();
include "db.php";

// Set timezone to match database
date_default_timezone_set('Asia/Kolkata');

$message = "";
$message_type = "";
$valid_token = false;
$email = "";

// Check if token is provided
if(!isset($_GET['token'])){
    header("Location: forgot_password.php");
    exit();
}

$token = $_GET['token'];
$token_hash = hash('sha256', $token);

// Get current time in IST
$current_time = new DateTime('now', new DateTimeZone('Asia/Kolkata'));
$current_timestamp = $current_time->format('Y-m-d H:i:s');

// Validate token
$query = "SELECT pr.*, u.first_name 
          FROM password_resets pr 
          JOIN users u ON pr.user_id = u.id 
          WHERE pr.token_hash = '$token_hash' 
          AND pr.expires_at > '$current_timestamp'";
$result = mysqli_query($conn, $query);

if(mysqli_num_rows($result) > 0){
    $valid_token = true;
    $reset_data = mysqli_fetch_assoc($result);
    $email = $reset_data['email'];
    $user_name = $reset_data['first_name'];
} else {
    // Check if token exists but expired
    $check_expired = "SELECT email, expires_at, created_at FROM password_resets WHERE token_hash = '$token_hash'";
    $expired_result = mysqli_query($conn, $check_expired);
    
    if(mysqli_num_rows($expired_result) > 0){
        $token_data = mysqli_fetch_assoc($expired_result);
        $expiry_dt = new DateTime($token_data['expires_at'], new DateTimeZone('Asia/Kolkata'));
        $expiry_display = $expiry_dt->format('d M Y, h:i:s A');
        $current_display = $current_time->format('d M Y, h:i:s A');
        
        $message = "This password reset link has expired.<br><br>
                   <small style='color:#666;'>🕐 Link expired at: <strong>$expiry_display</strong></small><br>
                   <small style='color:#666;'>🕐 Current time: <strong>$current_display</strong></small><br><br>
                   Please request a new reset link.";
    } else {
        $message = "Invalid password reset link. This token doesn't exist in our system.<br><br>
                   <small style='color:#666;'>💡 Possible reasons:</small><br>
                   <small style='color:#666;'>• Token was already used</small><br>
                   <small style='color:#666;'>• Token was never generated</small><br>
                   <small style='color:#666;'>• Link was copied incorrectly</small>";
    }
    $message_type = "error";
}

// Process password reset
if(isset($_POST['reset_password']) && $valid_token){
    $new_password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Validation
    if(strlen($new_password) < 6){
        $message = "Password must be at least 6 characters long.";
        $message_type = "error";
    } elseif($new_password !== $confirm_password){
        $message = "Passwords do not match!";
        $message_type = "error";
    } else {
        // Hash the new password
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        
        // Update user password
        $update_query = "UPDATE users SET password = '$hashed_password' WHERE email = '$email'";
        
        if(mysqli_query($conn, $update_query)){
            // Delete used token
            mysqli_query($conn, "DELETE FROM password_resets WHERE token_hash = '$token_hash'");
            
            $message = "Password reset successful! You can now login with your new password.";
            $message_type = "success";
            $valid_token = false; // Hide form after success
            
            // Auto redirect to login after 3 seconds
            header("refresh:3;url=login.php?msg=Password reset successful! Please login.");
        } else {
            $message = "Failed to reset password. Please try again.";
            $message_type = "error";
        }
    }
}
?>

<?php include "includes/header.php"; ?>

<style>
    .reset-wrapper {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 70vh;
        padding: 20px;
    }
    .reset-container {
        width: 100%;
        max-width: 500px;
        background: #fff;
        padding: 40px;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        border-top: 5px solid #22c55e;
    }
    .reset-container h2 {
        text-align: center;
        color: var(--text);
        margin-bottom: 10px;
        font-size: 26px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
    }
    .reset-container h2 i {
        color: #22c55e;
    }
    .reset-container p.subtitle {
        text-align: center;
        color: #777;
        margin-bottom: 30px;
        font-size: 14px;
        line-height: 1.6;
    }
    .user-badge {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: #fff;
        padding: 10px 20px;
        border-radius: 50px;
        text-align: center;
        margin-bottom: 25px;
        font-size: 14px;
        font-weight: 600;
    }
    .user-badge i {
        margin-right: 8px;
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
        border-color: #22c55e;
        box-shadow: 0 0 5px rgba(34, 197, 94, 0.3);
    }
    .password-strength {
        font-size: 12px;
        margin-top: 5px;
        color: #666;
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
        background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
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
        box-shadow: 0 5px 15px rgba(34, 197, 94, 0.4);
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
    .requirements {
        background: #f0f9ff;
        border: 1px solid #0284c7;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 20px;
        font-size: 13px;
        color: #0c4a6e;
    }
    .requirements ul {
        margin: 10px 0 0 0;
        padding-left: 20px;
    }
    .requirements li {
        margin: 5px 0;
    }
</style>

<div class="reset-wrapper">
    <div class="reset-container">
        <h2><i class="fas fa-lock"></i> Reset Password</h2>
        
        <?php if($valid_token): ?>
            <div class="user-badge">
                <i class="fas fa-user-circle"></i> Resetting password for: <?php echo htmlspecialchars($user_name); ?>
            </div>
            <p class="subtitle">
                Enter your new password below. Make sure it's strong and memorable!
            </p>
        <?php endif; ?>

        <?php if(!empty($message)): ?>
            <div class="<?php echo $message_type; ?>-msg">
                <i class="fas fa-<?php echo $message_type == 'success' ? 'check-circle' : 'exclamation-circle'; ?>"></i> 
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <?php if($valid_token): ?>
            <div class="requirements">
                <strong><i class="fas fa-shield-alt"></i> Password Requirements:</strong>
                <ul>
                    <li>✓ Minimum 6 characters</li>
                    <li>✓ Include letters and numbers (recommended)</li>
                    <li>✓ Avoid common words or patterns</li>
                </ul>
            </div>

            <form action="reset_password.php?token=<?php echo htmlspecialchars($token); ?>" method="POST" id="resetForm">
                <div class="form-group">
                    <label>New Password</label>
                    <input type="password" name="password" id="password" placeholder="Enter new password" required minlength="6">
                    <div class="password-strength" id="strength"></div>
                </div>

                <div class="form-group">
                    <label>Confirm Password</label>
                    <input type="password" name="confirm_password" id="confirm_password" placeholder="Re-enter new password" required minlength="6">
                    <div class="password-strength" id="match"></div>
                </div>

                <button type="submit" name="reset_password" class="submit-btn">
                    <i class="fas fa-check-circle"></i> Reset Password
                </button>
            </form>
        <?php else: ?>
            <div style="text-align: center; margin-top: 20px;">
                <a href="forgot_password.php" class="submit-btn" style="display: inline-block; padding: 12px 24px; text-decoration: none; width: auto;">
                    <i class="fas fa-redo"></i> Request New Reset Link
                </a>
            </div>
        <?php endif; ?>

        <div class="back-links">
            <a href="login.php"><i class="fas fa-arrow-left"></i> Back to Login</a>
            <a href="index.php">Go to Homepage <i class="fas fa-home"></i></a>
        </div>
    </div>
</div>

<script>
// Password strength indicator
const passwordInput = document.getElementById('password');
const confirmInput = document.getElementById('confirm_password');
const strengthDiv = document.getElementById('strength');
const matchDiv = document.getElementById('match');

if(passwordInput) {
    passwordInput.addEventListener('input', function() {
        const password = this.value;
        let strength = 0;
        
        if(password.length >= 6) strength++;
        if(password.length >= 10) strength++;
        if(/[a-z]/.test(password) && /[A-Z]/.test(password)) strength++;
        if(/\d/.test(password)) strength++;
        if(/[^a-zA-Z\d]/.test(password)) strength++;
        
        if(password.length === 0) {
            strengthDiv.textContent = '';
        } else if(strength <= 2) {
            strengthDiv.innerHTML = '<span style="color: #ef4444;">● Weak password</span>';
        } else if(strength <= 3) {
            strengthDiv.innerHTML = '<span style="color: #f59e0b;">●● Medium password</span>';
        } else {
            strengthDiv.innerHTML = '<span style="color: #22c55e;">●●● Strong password</span>';
        }
        
        checkMatch();
    });
}

if(confirmInput) {
    confirmInput.addEventListener('input', checkMatch);
}

function checkMatch() {
    if(confirmInput.value.length === 0) {
        matchDiv.textContent = '';
    } else if(passwordInput.value === confirmInput.value) {
        matchDiv.innerHTML = '<span style="color: #22c55e;">✓ Passwords match</span>';
    } else {
        matchDiv.innerHTML = '<span style="color: #ef4444;">✗ Passwords do not match</span>';
    }
}
</script>

<?php include "includes/footer.php"; ?>
