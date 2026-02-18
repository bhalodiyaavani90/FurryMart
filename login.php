<?php
// Start session and include database connection
session_start();
include "db.php"; 

// Capture redirect parameter
$redirect_to = isset($_GET['redirect']) ? $_GET['redirect'] : 'index.php';

// Redirect to index if already logged in
if(isset($_SESSION['email'])){
    header("Location: " . $redirect_to);
    exit();
}

$error = "";
$login_success = false; // Flag for the popup

if(isset($_POST['login'])){
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    // Fetch user from database
    $query = "SELECT * FROM users WHERE email = '$email'";
    $result = mysqli_query($conn, $query);

    if(mysqli_num_rows($result) > 0){
        $user = mysqli_fetch_assoc($result);
        
        // Verify the hashed password
        if(password_verify($password, $user['password'])){
            // Set Session Variables (both formats for compatibility)
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['user_email'] = $user['email']; // Also set user_email for my_orders.php compatibility
            $_SESSION['first_name'] = $user['first_name'];
            
            $_SESSION['login_success_anim'] = true;
            // Set success flag instead of immediate redirect
            $login_success = true;
        } else {
            $error = "Incorrect password. Please try again.";
        }
    } else {
        $error = "No account found with that email.";
    }
}
?>

<?php include "includes/header.php"; ?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>

<style>
    .login-wrapper {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 70vh;
        padding: 20px;
    }
    .login-container {
        width: 100%;
        max-width: 450px;
        background: #fff;
        padding: 40px;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        border-top: 5px solid var(--primary);
    }
    .login-container h2 {
        text-align: center;
        color: var(--text);
        margin-bottom: 10px;
        font-size: 26px;
    }
    .login-container p.subtitle {
        text-align: center;
        color: #777;
        margin-bottom: 30px;
        font-size: 14px;
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
        border-color: var(--primary);
        box-shadow: 0 0 5px rgba(0, 188, 212, 0.2);
    }
    .error-msg {
        background: #ffebee;
        color: #c62828;
        padding: 12px;
        border-radius: 8px;
        margin-bottom: 20px;
        font-size: 14px;
        text-align: center;
        border: 1px solid #ef9a9a;
    }
    .success-msg {
        background: #e8f5e9;
        color: #2e7d32;
        padding: 12px;
        border-radius: 8px;
        margin-bottom: 20px;
        font-size: 14px;
        text-align: center;
        border: 1px solid #a5d6a7;
    }
    .login-btn {
        width: 100%;
        background: var(--primary);
        color: white;
        padding: 14px;
        border: none;
        border-radius: 8px;
        font-size: 16px;
        font-weight: bold;
        cursor: pointer;
        transition: 0.3s;
    }
    .login-btn:hover {
        background: #00acc1;
        transform: translateY(-2px);
    }
    .forgot-password {
        text-align: center;
        margin-top: 15px;
        font-size: 13px;
    }
    .forgot-password a {
        color: #ff6b6b;
        text-decoration: none;
        font-weight: 600;
    }
    .forgot-password a:hover {
        text-decoration: underline;
        color: #ff5252;
    }
    .signup-link {
        text-align: center;
        margin-top: 20px;
        font-size: 14px;
        color: #666;
    }
    .signup-link a {
        color: var(--primary);
        text-decoration: none;
        font-weight: bold;
    }
    .signup-link a:hover {
        text-decoration: underline;
    }
    .back-home {
        display: block;
        text-align: center;
        margin-top: 15px;
        font-size: 13px;
        color: #999;
        text-decoration: none;
    }
</style>

<div class="login-wrapper">
    <div class="login-container">
        <h2>Welcome Back!</h2>
        <p class="subtitle">Login to your FurryMart account</p>

        <?php if(isset($_GET['msg'])): ?>
            <div class="success-msg">
                <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($_GET['msg']); ?>
            </div>
        <?php endif; ?>

        <?php if(!empty($error)): ?>
            <div class="error-msg">
                <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form action="login.php<?php echo isset($_GET['redirect']) ? '?redirect=' . urlencode($_GET['redirect']) : ''; ?>" method="POST">
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" placeholder="Enter your email" required>
            </div>

            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" placeholder="Enter your password" required>
            </div>

            <button type="submit" name="login" class="login-btn">Login</button>
        </form>

        <div class="forgot-password">
            <a href="forgot_password.php"><i class="fas fa-key"></i> Forgot Password?</a>
        </div>

        <div class="signup-link">
            Don't have an account? <a href="signup.php">Register here</a>
        </div>
        
        <a href="index.php" class="back-home"><i class="fas fa-arrow-left"></i> Back to Homepage</a>
    </div>
</div>

<script>
<?php if($login_success): ?>
    Swal.fire({
        title: 'Welcome to FurryMart!',
        text: 'Login Successful, Welcome <?php echo $_SESSION['first_name']; ?>!',
        icon: 'success',
        timer: 2500, // Popup shows for 2.5 seconds
        timerProgressBar: true,
        showConfirmButton: false,
        showClass: {
            popup: 'animate__animated animate__fadeInDown'
        },
        hideClass: {
            popup: 'animate__animated animate__fadeOutUp'
        },
        background: '#fff',
        iconColor: '#518992', // Matches your primary color
        customClass: {
            title: 'user-welcome'
        }
    }).then(() => {
        window.location.href = '<?php echo htmlspecialchars($redirect_to); ?>';
    });
<?php endif; ?>
</script>

<?php include "includes/footer.php"; ?>