<?php
session_start();
require_once('../db.php'); // DB Connection

// Check admin session
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

$msg = "";
$admin_id = $_SESSION['admin_id'];

// --- 1. LOGIC: UPDATE CURRENT ADMIN PASSWORD ---
if(isset($_POST['update_admin_profile'])){
    $new_pass = mysqli_real_escape_string($conn, $_POST['new_password']);
    $confirm_pass = mysqli_real_escape_string($conn, $_POST['confirm_password']);

    if($new_pass === $confirm_pass){
        $sql = "UPDATE admins SET password = '$new_pass' WHERE id = '$admin_id'";
        if(mysqli_query($conn, $sql)){
            $msg = "<div class='alert-premium success'><i class='fas fa-check-circle'></i> Your password has been updated successfully!</div>";
        } else {
            $msg = "<div class='alert-premium error'>Database Error: Could not update password.</div>";
        }
    } else {
        $msg = "<div class='alert-premium error'>Validation Error: Passwords do not match.</div>";
    }
}

// --- 2. LOGIC: ADD NEW ADMINISTRATOR ---
if(isset($_POST['add_new_admin'])){
    $new_email = mysqli_real_escape_string($conn, $_POST['admin_email']);
    $new_pass = mysqli_real_escape_string($conn, $_POST['admin_password']);
    
    $check = mysqli_query($conn, "SELECT id FROM admins WHERE email = '$new_email'");
    if(mysqli_num_rows($check) > 0){
        $msg = "<div class='alert-premium error'>Error: An administrator with this email already exists.</div>";
    } else {
        $sql = "INSERT INTO admins (email, password) VALUES ('$new_email', '$new_pass')";
        if(mysqli_query($conn, $sql)){
            $msg = "<div class='alert-premium success'><i class='fas fa-user-plus'></i> New Administrator registered successfully!</div>";
        }
    }
}

// --- 3. FETCH GLOBAL COUNTS AND DETAILS ---
// Count total administrators registered
$admin_count_res = mysqli_query($conn, "SELECT COUNT(id) AS total_admins FROM admins");
$admin_count_data = mysqli_fetch_assoc($admin_count_res);
$total_admins = $admin_count_data['total_admins'];

// Fetch current admin details
$admin_res = mysqli_query($conn, "SELECT email FROM admins WHERE id = '$admin_id'");
$current_admin = mysqli_fetch_assoc($admin_res);

include('includes/header.php'); 
?>

<style>
    /* --- PROFESSIONAL SETTINGS LAYOUT --- */
    .settings-wrapper { max-width: 1200px; margin: 0 auto; padding: 50px 40px; }
    .page-header { margin-bottom: 30px; display: flex; justify-content: space-between; align-items: flex-end; border-bottom: 1.5px solid #f1f5f9; padding-bottom: 25px; }
    .page-header h1 { font-size: 2.2rem; font-weight: 800; color: #1e293b; letter-spacing: -1px; margin: 0; }
    
    .btn-logout-alt { background: #fee2e2; color: #dc2626; padding: 12px 25px; border-radius: 10px; text-decoration: none; font-weight: 700; font-size: 0.9rem; transition: 0.3s; border: 1px solid #fecaca; }
    .btn-logout-alt:hover { background: #dc2626; color: white; }

    /* --- STATISTIC COUNTER CARD --- */
    .admin-stats-bar {
        background: #f0fdfa;
        border: 1px solid #ccfbf1;
        padding: 15px 25px;
        border-radius: 16px;
        display: inline-flex;
        align-items: center;
        gap: 15px;
        margin-bottom: 35px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.02);
    }
    .stat-icon-small { width: 40px; height: 40px; background: #518992; color: white; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.1rem; }
    .stat-text { color: #1e3a8a; font-weight: 700; font-size: 0.95rem; }
    .stat-count { font-size: 1.3rem; font-weight: 800; color: #0d9488; }

    .settings-card { background: white; border-radius: 24px; padding: 40px; border: 1px solid #f1f5f9; box-shadow: 0 10px 40px rgba(0,0,0,0.02); margin-bottom: 40px; }
    .settings-card h3 { font-size: 1.3rem; font-weight: 800; color: #0f1c3f; margin-bottom: 30px; display: flex; align-items: center; gap: 12px; }
    .settings-card h3 i { color: #518992; }

    .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 30px; margin-bottom: 20px; }
    .form-group { margin-bottom: 20px; text-align: left; }
    .form-label { display: block; font-size: 0.75rem; font-weight: 800; color: #94a3b8; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 10px; }
    
    .form-input-premium { width: 100%; padding: 16px; border-radius: 14px; border: 1.5px solid #edf2f7; background: #f8fafc; font-size: 1rem; color: #1e293b; transition: 0.3s ease; box-sizing: border-box; }
    .form-input-premium:focus { border-color: #518992; background: #fff; outline: none; box-shadow: 0 0 0 5px rgba(81, 137, 146, 0.1); }
    .form-input-premium:disabled { cursor: not-allowed; opacity: 0.6; background: #f1f5f9; }

    .btn-save-settings { background: #0f1c3f; color: white; border: none; padding: 18px 40px; border-radius: 14px; font-weight: 800; font-size: 0.95rem; cursor: pointer; transition: 0.3s; text-transform: uppercase; letter-spacing: 1px; }
    .btn-save-settings:hover { background: #1e3a8a; transform: translateY(-3px); box-shadow: 0 10px 20px rgba(30, 58, 138, 0.2); }
    
    .btn-teal { background: #518992; }
    .btn-teal:hover { background: #3d6b73; }

    /* Alert Styling */
    .alert-premium { padding: 20px; border-radius: 15px; margin-bottom: 35px; font-weight: 700; display: flex; align-items: center; gap: 12px; }
    .alert-premium.success { background: #ecfdf5; color: #065f46; border: 1px solid #a7f3d0; }
    .alert-premium.error { background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; }
</style>

<main class="settings-wrapper">
    <div class="page-header">
        <div>
            <h1>Security Settings</h1>
            <p style="color: #64748b; margin-top: 5px;">Configure administrative access and team credentials.</p>
        </div>
        <a href="logout.php" class="btn-logout-alt"><i class="fas fa-sign-out-alt"></i> Secure Logout</a>
    </div>

    <div class="admin-stats-bar">
        <div class="stat-icon-small">
            <i class="fas fa-users-cog"></i>
        </div>
        <div class="stat-text">
            Total Registered Administrators: <span class="stat-count"><?php echo $total_admins; ?></span>
        </div>
    </div>

    <?php echo $msg; ?>

    <div class="settings-card">
        <h3><i class="fas fa-user-lock"></i> Update Personal Credentials</h3>
        <form method="POST">
            <div class="form-group">
                <label class="form-label">Administrator Email (Read Only)</label>
                <input type="text" class="form-input-premium" value="<?php echo $current_admin['email']; ?>" disabled>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">New Secure Password</label>
                    <input type="password" name="new_password" class="form-input-premium" placeholder="Enter new password" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Confirm New Password</label>
                    <input type="password" name="confirm_password" class="form-input-premium" placeholder="Repeat password" required>
                </div>
            </div>
            <button type="submit" name="update_admin_profile" class="btn-save-settings">
                Save Updated Password
            </button>
        </form>
    </div>

    <div class="settings-card">
        <h3><i class="fas fa-user-plus"></i> Register New Administrator</h3>
        <form method="POST">
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">New Admin Login Email</label>
                    <input type="email" name="admin_email" class="form-input-premium" placeholder="e.g. manager@gmail.com" required>
                </div>
                <div class="form-group">
                    <label class="form-label">New Admin Password</label>
                    <input type="password" name="admin_password" class="form-input-premium" placeholder="Create  password" required>
                </div>
            </div>
            <button type="submit" name="add_new_admin" class="btn-save-settings btn-teal">
                Register Administrator
            </button>
        </form>
    </div>
</main>

<?php include('includes/footer.php'); ?>