<?php
session_start();
include "../db.php";

// 2. REDIRECT IF ALREADY LOGGED IN
if (isset($_SESSION['admin_id'])) {
    header("Location: dashboard.php");
    exit();
}

$error = "";

// 3. LOGIN LOGIC
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize input to prevent SQL Injection
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    // Query the admins table
    $sql = "SELECT id, email, password FROM admins WHERE email = '$email' LIMIT 1";
    $result = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) === 1) {
        $row = mysqli_fetch_assoc($result);
        
        // CHECK PASSWORD (Direct comparison for plain text)
        // If you decide to use hashing later, use password_verify($password, $row['password'])
        if ($password === $row['password']) {
            $_SESSION['admin_id'] = $row['id'];
            $_SESSION['admin_email'] = $row['email'];
            
            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Invalid Password. Please try again.";
        }
    } else {
        $error = "No admin found with that email address.";
    }
}
?>


<div class="login-wrapper">
    <div class="login-card">
        <div class="login-header">
            <h2>Admin Login</h2>
            <p>Sign in to manage your panel</p>
        </div>
        
        <?php if($error): ?>
            <div class="error-msg">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" placeholder="admin@example.com" required>
            </div>
            
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" placeholder="••••••••" required>
            </div>
            
            <button type="submit" class="login-btn">Secure Login</button>
        </form>
    </div>
</div>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <title>FURRYMART ADMIN</title>
     <link rel="icon" type="image/png" href="uploads/logo.jpg">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;700&display=swap" rel="stylesheet">

<style>
    /* Admin Theme Styles */
    body { 
        background-color: #f0f2f5; 
        margin: 0;
        font-family: 'Segoe UI', sans-serif;
    }
    
    .login-wrapper {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 80vh;
        padding: 20px;
    }

    .login-card {
        background: #ffffff;
        padding: 40px;
        border-radius: 12px;
        box-shadow: 0 8px 24px rgba(0,0,0,0.1);
        width: 100%;
        max-width: 400px;
        box-sizing: border-box;
    }

    .login-header { text-align: center; margin-bottom: 30px; }
    .login-header h2 { color: #2c3e50; margin: 0; font-size: 24px; }
    .login-header p { color: #7f8c8d; font-size: 14px; margin-top: 5px; }

    .error-msg {
        background: #fff5f5;
        color: #c0392b;
        padding: 12px;
        border-left: 4px solid #e74c3c;
        border-radius: 4px;
        margin-bottom: 20px;
        font-size: 14px;
    }

    .form-group { margin-bottom: 20px; }
    .form-group label { 
        display: block; 
        margin-bottom: 8px; 
        color: #34495e; 
        font-weight: 600; 
        font-size: 14px;
    }
    
    .form-group input {
        width: 100%;
        padding: 12px 15px;
        border: 1px solid #dcdde1;
        border-radius: 6px;
        font-size: 15px;
        transition: border-color 0.3s;
        box-sizing: border-box;
    }

    .form-group input:focus {
        outline: none;
        border-color: #3498db;
    }

    .login-btn {
        width: 100%;
        padding: 13px;
        background: #2c3e50; /* Admin Theme Color */
        color: white;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        font-size: 16px;
        font-weight: bold;
        transition: background 0.3s;
    }

    .login-btn:hover { background: #1a252f; }

    /* Responsive adjustment */
    @media (max-width: 480px) {
        .login-card { padding: 25px; }
    }
</style>
</head>

<?php include('includes/footer.php'); ?>