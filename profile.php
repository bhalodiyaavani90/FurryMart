<?php
include "db.php";
if (session_status() === PHP_SESSION_NONE) { session_start(); }

// 1. ACCESS CONTROL: Redirect to login if not authenticated
if(!isset($_SESSION['email'])){
    header("Location: login.php?redirect=my_orders.php");
    exit();
}

$user_email = $_SESSION['email'];
$success_msg = "";

// 2. PROFILE UPDATE LOGIC
if(isset($_POST['update_profile'])){
    $fname  = mysqli_real_escape_string($conn, $_POST['fname']);
    $lname  = mysqli_real_escape_string($conn, $_POST['lname']);
    $mobile = mysqli_real_escape_string($conn, $_POST['mobile']);
    $city   = mysqli_real_escape_string($conn, $_POST['city']);
    $state  = mysqli_real_escape_string($conn, $_POST['state']);
    $zip    = mysqli_real_escape_string($conn, $_POST['zip']);
    $addr   = mysqli_real_escape_string($conn, $_POST['address']);

    $update_query = "UPDATE users SET first_name='$fname', last_name='$lname', mobile_number='$mobile', city='$city', state='$state', zip='$zip', address='$addr' WHERE email='$user_email'";
    if(mysqli_query($conn, $update_query)){
        $_SESSION['first_name'] = $fname;
        $success_msg = "Guardian Intelligence Profile Updated Successfully!";
    }
}

// 3. FETCH USER DATA
$user_res = mysqli_query($conn, "SELECT * FROM users WHERE email='$user_email'");
$user = mysqli_fetch_assoc($user_res);
$user_id = $user['id']; // Get user ID for callback/quick query lookups

// 4. FETCH DATA ARRAYS (Filtered by User Email)
$grooming_results = mysqli_query($conn, "SELECT * FROM appoint_grooming WHERE owner_email='$user_email' ORDER BY preferred_date DESC");
$vet_results = mysqli_query($conn, "SELECT * FROM vet_appointments WHERE email='$user_email' ORDER BY appointment_date DESC");
$contact_results = mysqli_query($conn, "SELECT * FROM contact_queries WHERE email='$user_email' ORDER BY created_at DESC");

// FIXED: Using 'foundation_donations' (plural) and column names from your database screenshot
$donation_results = mysqli_query($conn, "SELECT * FROM foundation_donations WHERE email='$user_email' ORDER BY id DESC");

// Fetch Birthday Bookings
$birthday_results = mysqli_query($conn, "SELECT * FROM birthday_bookings WHERE user_email='$user_email' ORDER BY party_date DESC");

// Fetch Callback Requests (Support Hub)
$callback_results = mysqli_query($conn, "SELECT * FROM callback_requests WHERE user_id='$user_id' ORDER BY created_at DESC");

// Fetch Quick Queries (Support Hub)
$quick_query_results = mysqli_query($conn, "SELECT * FROM quick_queries WHERE user_id='$user_id' ORDER BY created_at DESC");
?>

<?php include "includes/header.php"; ?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<style>
    :root { 
        --primary: #518992; 
        --navy: #0f1c3f; 
        --accent: #e74c3c; 
        --pm-blue: #AEC6CF; 
        --gold: #c5a059; 
        --pink: #ec4899; 
        --purple: #8b5cf6; 
        --green: #10b981;
        --orange: #f59e0b;
    }
    
    * { margin: 0; padding: 0; box-sizing: border-box; }
    
    body { 
        overflow-x: hidden; 
        font-family: 'Plus Jakarta Sans', -apple-system, sans-serif;
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    }
    
    /* Hero Header */
    .profile-hero {
        background: linear-gradient(135deg, var(--navy) 0%, var(--primary) 100%);
        padding: 60px 5%;
        color: white;
        text-align: center;
        position: relative;
        overflow: hidden;
        animation: fadeIn 0.6s ease;
    }
    
    .profile-hero::before {
        content: '';
        position: absolute;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(255,255,255,0.1) 1px, transparent 1px);
        background-size: 50px 50px;
        animation: drift 20s linear infinite;
        top: 0;
        left: 0;
    }
    
    @keyframes drift {
        0% { transform: translate(0, 0); }
        100% { transform: translate(50px, 50px); }
    }
    
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    
    .profile-hero h1 {
        font-size: 3rem;
        font-weight: 900;
        margin: 0;
        position: relative;
        z-index: 2;
        text-shadow: 0 4px 20px rgba(0,0,0,0.3);
        animation: slideDown 0.6s ease;
    }
    
    @keyframes slideDown {
        from { transform: translateY(-30px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }
    
    .profile-page { 
        padding: 40px 5%; 
        display: grid; 
        grid-template-columns: 320px 1fr; 
        gap: 30px; 
        max-width: 1400px;
        margin: -50px auto 50px;
        position: relative;
        z-index: 10;
    }
    
    /* Sidebar Styling with Animations */
    .profile-sidebar { 
        background: white; 
        padding: 35px; 
        border-radius: 25px; 
        box-shadow: 0 10px 40px rgba(0,0,0,0.1); 
        height: fit-content; 
        text-align: center; 
        border-top: 6px solid var(--primary);
        position: sticky;
        top: 20px;
        animation: slideInLeft 0.6s cubic-bezier(0.68, -0.55, 0.265, 1.55);
    }
    
    @keyframes slideInLeft {
        from { transform: translateX(-50px); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    
    .avatar-circle { 
        width: 120px; 
        height: 120px; 
        background: linear-gradient(135deg, var(--primary), var(--navy)); 
        color: white; 
        border-radius: 50%; 
        display: flex; 
        align-items: center; 
        justify-content: center; 
        font-size: 48px; 
        margin: 0 auto 20px; 
        border: 5px solid #e0f7fa; 
        box-shadow: 0 8px 25px rgba(81, 137, 146, 0.3);
        animation: bounceIn 0.8s cubic-bezier(0.68, -0.55, 0.265, 1.55);
        font-weight: 900;
        position: relative;
    }
    
    .avatar-circle::after {
        content: '';
        position: absolute;
        inset: -8px;
        border-radius: 50%;
        border: 3px solid var(--primary);
        opacity: 0;
        animation: pulse 2s infinite;
    }
    
    @keyframes bounceIn {
        0% { transform: scale(0); opacity: 0; }
        50% { transform: scale(1.1); }
        100% { transform: scale(1); opacity: 1; }
    }
    
    @keyframes pulse {
        0% { opacity: 1; transform: scale(1); }
        100% { opacity: 0; transform: scale(1.3); }
    }
    
    .profile-sidebar h3 {
        font-size: 1.4rem;
        font-weight: 900;
        color: var(--navy);
        margin: 0;
    }
    
    .profile-sidebar p {
        color: #94a3b8; 
        font-size: 14px; 
        font-weight: 600;
        margin-top: 5px;
    }
    
    .sidebar-menu { 
        margin-top: 35px; 
        text-align: left; 
    }
    
    .sidebar-menu a { 
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 14px 18px; 
        color: #64748b; 
        text-decoration: none; 
        border-radius: 12px; 
        margin-bottom: 8px; 
        transition: all 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55); 
        font-weight: 700;
        font-size: 15px;
        position: relative;
        overflow: hidden;
    }
    
    .sidebar-menu a::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        height: 100%;
        width: 4px;
        background: var(--primary);
        transform: scaleY(0);
        transition: transform 0.3s;
    }
    
    .sidebar-menu a.active { 
        background: linear-gradient(135deg, var(--primary), var(--navy)); 
        color: white;
        transform: translateX(5px);
        box-shadow: 0 5px 15px rgba(81, 137, 146, 0.3);
    }
    
    .sidebar-menu a:hover:not(.active) { 
        background: #e0f7fa; 
        color: var(--primary);
        transform: translateX(5px);
    }
    
    .sidebar-menu a:hover::before {
        transform: scaleY(1);
    }
    
    .sidebar-menu a i {
        font-size: 18px;
        width: 24px;
        text-align: center;
    }

    /* Main Content Styling */
    .profile-main { 
        background: white; 
        padding: 45px; 
        border-radius: 25px; 
        box-shadow: 0 10px 40px rgba(0,0,0,0.1);
        animation: slideInRight 0.6s cubic-bezier(0.68, -0.55, 0.265, 1.55);
    }
    
    @keyframes slideInRight {
        from { transform: translateX(50px); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    
    .profile-header { 
        display: flex; 
        justify-content: space-between; 
        align-items: center; 
        margin-bottom: 35px; 
        padding-bottom: 20px;
        border-bottom: 3px solid transparent;
        background: linear-gradient(white, white) padding-box,
                    linear-gradient(90deg, var(--primary), var(--navy)) border-box;
        border-image: linear-gradient(90deg, var(--primary), var(--navy)) 1;
    }
    
    .profile-header h2 { 
        margin: 0; 
        color: var(--navy); 
        font-weight: 900;
        font-size: 2rem;
        display: flex;
        align-items: center;
        gap: 15px;
    }
    
    .profile-header i {
        font-size: 2.5rem;
        background: linear-gradient(135deg, var(--primary), var(--navy));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        animation: rotate 3s linear infinite;
    }
    
    @keyframes rotate {
        0%, 100% { transform: rotate(0deg); }
        50% { transform: rotate(10deg); }
    }
    
    .form-grid { 
        display: grid; 
        grid-template-columns: repeat(2, 1fr); 
        gap: 25px;
        animation: fadeInUp 0.6s ease 0.2s both;
    }
    
    @keyframes fadeInUp {
        from { transform: translateY(30px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }
    
    .form-group { 
        display: flex; 
        flex-direction: column; 
    }
    
    .form-group label { 
        font-size: 11px; 
        font-weight: 900; 
        color: #94a3b8; 
        margin-bottom: 10px; 
        text-transform: uppercase; 
        letter-spacing: 1px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .form-group input, .form-group textarea { 
        padding: 15px 20px; 
        border: 2px solid #e2e8f0; 
        border-radius: 15px; 
        outline: none; 
        transition: all 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55); 
        font-size: 15px; 
        font-weight: 600; 
        background: #f8fafc;
        font-family: inherit;
    }
    
    .form-group input:focus, .form-group textarea:focus { 
        border-color: var(--primary); 
        box-shadow: 0 8px 25px rgba(81, 137, 146, 0.15);
        transform: translateY(-2px);
        background: white;
    }
    
    .form-group input[readonly] { 
        background: #f1f5f9; 
        color: #94a3b8; 
        cursor: not-allowed;
        border-style: dashed;
    }
    
    .form-group textarea {
        resize: vertical;
        min-height: 100px;
    }

    /* Card Grid Layout */
    .appointment-container { 
        margin-top: 50px;
    }
    
    .section-subtitle { 
        font-size: 1.6rem; 
        font-weight: 900; 
        color: var(--navy); 
        margin: 50px 0 25px; 
        display: flex; 
        align-items: center; 
        gap: 12px;
        padding: 20px;
        background: linear-gradient(135deg, #f8fafc 0%, #e0f7fa 100%);
        border-radius: 15px;
        border-left: 6px solid var(--primary);
        animation: slideInLeft 0.5s ease;
    }
    
    .section-subtitle i {
        font-size: 1.8rem;
        animation: bounce 2s infinite;
    }
    
    @keyframes bounce {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-10px); }
    }
    
    .appointment-grid { 
        display: grid; 
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); 
        gap: 25px; 
        padding: 10px 0;
    }
    
    .apt-card { 
        background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
        border: 2px solid #e2e8f0; 
        border-radius: 20px; 
        padding: 28px; 
        transition: all 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55); 
        position: relative;
        overflow: hidden;
        animation: scaleIn 0.5s ease;
    }
    
    @keyframes scaleIn {
        from { transform: scale(0.9); opacity: 0; }
        to { transform: scale(1); opacity: 1; }
    }
    
    .apt-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.8), transparent);
        transition: left 0.5s;
    }
    
    .apt-card:hover::before {
        left: 100%;
    }
    
    .apt-card:hover { 
        transform: translateY(-10px) scale(1.02); 
        border-color: var(--primary); 
        box-shadow: 0 15px 40px rgba(81, 137, 146, 0.2); 
        background: white;
    }
    
    .apt-card h4 { 
        margin: 0 0 18px; 
        font-size: 1.2rem; 
        font-weight: 900; 
        display: flex; 
        align-items: center; 
        gap: 12px;
        color: var(--navy);
        padding-bottom: 12px;
        border-bottom: 2px solid #f1f5f9;
    }
    
    .apt-card h4 i {
        font-size: 1.4rem;
        background: linear-gradient(135deg, var(--primary), var(--navy));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }
    
    .apt-detail { 
        font-size: 14px; 
        color: #64748b; 
        margin-bottom: 10px; 
        font-weight: 600;
        line-height: 1.6;
        display: flex;
        align-items: start;
        gap: 8px;
    }
    
    .apt-detail strong { 
        color: var(--navy);
        font-weight: 800;
    }
    
    .apt-status-badge { 
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 18px; 
        border-radius: 50px; 
        font-size: 11px; 
        font-weight: 900; 
        text-transform: uppercase; 
        margin-top: 18px; 
        letter-spacing: 1px;
        animation: fadeIn 0.5s ease 0.3s both;
    }
    
    .apt-status-badge::before {
        content: '';
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: currentColor;
        animation: pulse 2s infinite;
    }
    
    /* STATUS COLORS with Gradients */
    .status-unread, .status-pending { 
        background: linear-gradient(135deg, #fffbeb, #fef3c7); 
        color: #d97706; 
        border: 2px solid #fbbf24;
        box-shadow: 0 4px 15px rgba(251, 191, 36, 0.2);
    }
    
    .status-read, .status-in_process, .status-in_progress { 
        background: linear-gradient(135deg, #eff6ff, #dbeafe); 
        color: #2563eb; 
        border: 2px solid #60a5fa;
        box-shadow: 0 4px 15px rgba(96, 165, 250, 0.2);
    }
    
    .status-confirmed, .status-resolved, .status-solved, .status-completed, .status-complete, .status-approved { 
        background: linear-gradient(135deg, #ecfdf5, #d1fae5); 
        color: #059669; 
        border: 2px solid #34d399;
        box-shadow: 0 4px 15px rgba(52, 211, 153, 0.2);
    }
    
    .status-cancelled, .status-rejected { 
        background: linear-gradient(135deg, #fef2f2, #fee2e2); 
        color: var(--accent); 
        border: 2px solid #fca5a5;
        box-shadow: 0 4px 15px rgba(252, 165, 165, 0.2);
    }

    .update-btn { 
        background: linear-gradient(135deg, var(--navy), var(--primary)); 
        color: white; 
        border: none; 
        padding: 18px 45px; 
        border-radius: 15px; 
        font-weight: 900; 
        cursor: pointer; 
        transition: all 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55); 
        margin-top: 30px; 
        text-transform: uppercase; 
        font-size: 15px;
        letter-spacing: 1px;
        box-shadow: 0 8px 25px rgba(81, 137, 146, 0.3);
        display: inline-flex;
        align-items: center;
        gap: 10px;
        animation: fadeInUp 0.6s ease 0.4s both;
    }
    
    .update-btn:hover { 
        transform: translateY(-3px) scale(1.05); 
        box-shadow: 0 15px 40px rgba(81, 137, 146, 0.4);
    }
    
    .update-btn:active {
        transform: translateY(-1px) scale(1.02);
    }
    
    .update-btn i {
        animation: spin 2s linear infinite;
    }
    
    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
    
    .success-alert {
        background: linear-gradient(135deg, #ecfdf5, #d1fae5); 
        color: #059669; 
        padding: 18px 25px; 
        border-radius: 15px; 
        margin-bottom: 25px; 
        font-weight: 700;
        border-left: 5px solid #059669;
        box-shadow: 0 5px 20px rgba(5, 150, 105, 0.2);
        display: flex;
        align-items: center;
        gap: 12px;
        animation: slideInDown 0.5s cubic-bezier(0.68, -0.55, 0.265, 1.55);
    }
    
    @keyframes slideInDown {
        from { transform: translateY(-20px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }
    
    .success-alert::before {
        content: '✓';
        width: 30px;
        height: 30px;
        background: #059669;
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 900;
        flex-shrink: 0;
    }
    
    .no-data-message {
        padding: 50px;
        text-align: center;
        color: #94a3b8;
        font-weight: 600;
        background: linear-gradient(135deg, #f8fafc, #f1f5f9);
        border-radius: 20px;
        border: 3px dashed #e2e8f0;
        animation: fadeIn 0.6s ease;
    }
    
    .no-data-message i {
        font-size: 50px;
        display: block;
        margin-bottom: 15px;
        opacity: 0.3;
        animation: float 3s ease-in-out infinite;
    }
    
    @keyframes float {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-10px); }
    }

    @media (max-width: 1024px) { 
        .profile-page { 
            grid-template-columns: 1fr; 
            margin-top: -30px;
        }
        .profile-sidebar {
            position: static;
        }
        .form-grid { 
            grid-template-columns: 1fr; 
        }
        .appointment-grid { 
            grid-template-columns: 1fr; 
        }
        .profile-hero h1 {
            font-size: 2rem;
        }
    }
</style>

<!-- Hero Header -->
<div class="profile-hero">
    <h1><i class="fas fa-user-shield"></i> User Profile Command Center</h1>
</div>

<div class="profile-page">
    <aside class="profile-sidebar">
        <div class="avatar-circle"><?php echo strtoupper(substr($user['first_name'], 0, 1)); ?></div>
        <h3><?php echo $user['first_name'] . " " . $user['last_name']; ?></h3>
        <p><?php echo $user['email']; ?></p>
        <nav class="sidebar-menu">
            <a href="profile.php" class="active"><i class="fas fa-user-shield"></i> Profile Intelligence</a>
            <a href="my_orders.php"><i class="fas fa-history"></i> Order History</a>
            <a href="wishlist.php"><i class="fas fa-star"></i> Saved Items</a>
            <a href="logout.php" style="color: #ff4757; margin-top: 15px; padding-top: 15px; border-top: 1px solid #f1f5f9;"><i class="fas fa-power-off"></i> System Logout</a>
        </nav>
    </aside>

    <main class="profile-main">
        <div class="profile-header">
            <h2><i class="fas fa-id-card"></i> Account Credentials</h2>
        </div>

        <?php if($success_msg != ""): ?>
            <div class="success-alert"><?php echo $success_msg; ?></div>
        <?php endif; ?>

        <form action="profile.php" method="POST">
            <div class="form-grid">
                <div class="form-group"><label>First Name</label><input type="text" name="fname" value="<?php echo $user['first_name']; ?>" required></div>
                <div class="form-group"><label>Last Name</label><input type="text" name="lname" value="<?php echo $user['last_name']; ?>" required></div>
                <div class="form-group"><label>Email Axis (Locked)</label><input type="email" value="<?php echo $user['email']; ?>" readonly></div>
                <div class="form-group"><label>Mobile Comms</label><input type="text" name="mobile" value="<?php echo $user['mobile_number']; ?>" required></div>
                <div class="form-group"><label>Current City</label><input type="text" name="city" value="<?php echo $user['city']; ?>" required></div>
                <div class="form-group"><label>State Territory</label><input type="text" name="state" value="<?php echo $user['state']; ?>" required></div>
                <div class="form-group"><label>Postal Code</label><input type="text" name="zip" value="<?php echo $user['zip']; ?>" required></div>
                <div class="form-group"><label>DOB</label><input type="text" value="<?php echo $user['dob']; ?>" readonly></div>
                <div class="form-group" style="grid-column: span 2;"><label>Full Address</label><textarea name="address" rows="3" required><?php echo $user['address']; ?></textarea></div>
            </div>
            <button type="submit" name="update_profile" class="update-btn"><i class="fas fa-sync-alt"></i> Deploy Changes</button>
        </form>

        <div class="appointment-container">
            <h3 class="section-subtitle"><i class="fas fa-cut" style="color:var(--primary);"></i> Grooming Matrix Deployments</h3>
            <div class="appointment-grid">
                <?php if(mysqli_num_rows($grooming_results) > 0): 
                    while($g = mysqli_fetch_assoc($grooming_results)): ?>
                    <div class="apt-card">
                        <h4><i class="fas fa-paw"></i> <?php echo $g['pet_breed']; ?></h4>
                        <div class="apt-detail">SERVICE: <strong><?php echo $g['service_type']; ?></strong></div>
                        <div class="apt-detail">DATE: <strong><?php echo date('d M, Y', strtotime($g['preferred_date'])); ?></strong></div>
                        <span class="apt-status-badge status-<?php echo strtolower($g['status']); ?>">● <?php echo $g['status']; ?></span>
                    </div>
                <?php endwhile; else: ?>
                    <div class="no-data-message"><i class="fas fa-inbox" style="font-size: 40px; display: block; margin-bottom: 10px; opacity: 0.3;"></i>No grooming history found.</div>
                <?php endif; ?>
            </div>

            <h3 class="section-subtitle" style="margin-top: 50px;"><i class="fas fa-user-md" style="color:var(--accent);"></i> VetCare Command Missions</h3>
            <div class="appointment-grid">

                <?php if(mysqli_num_rows($vet_results) > 0): 
                    while($v = mysqli_fetch_assoc($vet_results)): ?>
                    <div class="apt-card">
                        <h4><i class="fas fa-heartbeat"></i> <?php echo $v['pet_name']; ?></h4>
                        <div class="apt-detail">SPECIES: <strong><?php echo $v['pet_type']; ?></strong></div>
                        <div class="apt-detail">DATE: <strong><?php echo date('d M, Y', strtotime($v['appointment_date'])); ?></strong></div>
                        <span class="apt-status-badge status-<?php echo strtolower($v['status']); ?>">● <?php echo $v['status']; ?></span>
                    </div>
                <?php endwhile; else: ?>
                    <div class="no-data-message"><i class="fas fa-inbox" style="font-size: 40px; display: block; margin-bottom: 10px; opacity: 0.3;"></i>No medical history found.</div>
                <?php endif; ?>
            </div>

            <h3 id="donation-sec" class="section-subtitle" style="margin-top: 50px;"><i class="fas fa-hand-holding-heart" style="color:var(--gold);"></i> Guardian Donation Matrix</h3>
            <div class="appointment-grid">
                <?php if($donation_results && mysqli_num_rows($donation_results) > 0): 
                    while($d = mysqli_fetch_assoc($donation_results)): ?>
                    <div class="apt-card" style="border-left: 5px solid var(--gold);">
                        <h4><i class="fas fa-donate"></i> Tier: <?php echo $d['tier_name']; ?></h4>
                        <div class="apt-detail">AMOUNT: <strong style="color:var(--gold); font-size:16px;">₹<?php echo number_format($d['amount']); ?></strong></div>
                        <div class="apt-detail">TXN ID: <strong><?php echo $d['transaction_id']; ?></strong></div>
                        <div class="apt-detail">DATE: <strong><?php echo date('d M, Y', strtotime($d['created_at'])); ?></strong></div>
                        <span class="apt-status-badge status-<?php echo strtolower($d['status']); ?>">● <?php echo strtoupper($d['status']); ?></span>
                    </div>
                <?php endwhile; else: ?>
                    <div class="no-data-message"><i class="fas fa-inbox" style="font-size: 40px; display: block; margin-bottom: 10px; opacity: 0.3;"></i>No donation history found.</div>
                <?php endif; ?>
            </div>

            <h3 class="section-subtitle" style="margin-top: 50px;"><i class="fas fa-birthday-cake" style="background: linear-gradient(135deg, var(--pink), var(--purple)); -webkit-background-clip: text; -webkit-text-fill-color: transparent;"></i> Birthday Party Bookings</h3>
            <div class="appointment-grid">
                <?php if($birthday_results && mysqli_num_rows($birthday_results) > 0): 
                    while($b = mysqli_fetch_assoc($birthday_results)): ?>
                    <div class="apt-card" style="border-left: 5px solid var(--pink);">
                        <h4><i class="fas fa-paw"></i> <?php echo $b['pet_name']; ?></h4>
                        <div class="apt-detail">PET TYPE: <strong><?php echo $b['pet_type']; ?></strong></div>
                        <div class="apt-detail">PARTY DATE: <strong><?php echo date('d M, Y', strtotime($b['party_date'])); ?></strong></div>
                        <div class="apt-detail">TIME: <strong><?php echo $b['party_time']; ?></strong></div>
                        <div class="apt-detail">GUESTS: <strong><?php echo $b['guest_count']; ?> Pets</strong></div>
                        <?php if($b['party_package']): ?>
                        <div class="apt-detail">PACKAGE: <strong style="color: var(--pink);"><?php echo $b['party_package']; ?></strong></div>
                        <?php endif; ?>
                        <div class="apt-detail">PHONE: <strong><?php echo $b['contact_phone']; ?></strong></div>
                        <?php if($b['admin_notes']): ?>
                        <div class="apt-detail" style="margin-top: 10px; padding: 10px; background: #fef3c7; border-radius: 8px; font-size: 12px;">
                            <strong>Admin Note:</strong> <?php echo $b['admin_notes']; ?>
                        </div>
                        <?php endif; ?>
                        <span class="apt-status-badge status-<?php echo strtolower($b['status']); ?>">● <?php echo strtoupper($b['status']); ?></span>
                    </div>
                <?php endwhile; else: ?>
                    <div class="no-data-message">
                        <i class="fas fa-birthday-cake" style="font-size: 40px; display: block; margin-bottom: 10px; opacity: 0.3;"></i>
                        No birthday party bookings yet. <a href="birthday.php" style="color: var(--pink); font-weight: 800;">Book your pet's party now!</a>
                    </div>
                <?php endif; ?>
            </div>

            <h3 class="section-subtitle" style="margin-top: 50px;"><i class="fas fa-headset" style="color:var(--pm-blue);"></i> Support Intelligence Hub</h3>
            
            <!-- Callback Requests -->
            <h4 style="margin: 30px 0 15px 0; color: var(--navy); font-size: 18px; font-weight: 800;">
                <i class="fas fa-phone-alt" style="color: #ef4444;"></i> Callback Requests
            </h4>
            <div class="appointment-grid">
                <?php if($callback_results && mysqli_num_rows($callback_results) > 0): 
                    while($cb = mysqli_fetch_assoc($callback_results)): ?>
                    <div class="apt-card" style="border-left: 5px solid #ef4444;">
                        <h4><i class="fas fa-phone"></i> Request FREE Callback</h4>
                        <div class="apt-detail">NAME: <strong><?php echo $cb['name']; ?></strong></div>
                        <div class="apt-detail">PHONE: <strong style="color: #ef4444;">+91 <?php echo $cb['phone']; ?></strong></div>
                        <div class="apt-detail">SUBMITTED: <strong><?php echo date('d M, Y - h:i A', strtotime($cb['created_at'])); ?></strong></div>
                        <span class="apt-status-badge status-<?php echo strtolower($cb['status']); ?>">● <?php echo strtoupper($cb['status']); ?></span>
                        <?php if($cb['status'] === 'completed' || $cb['status'] === 'resolved'): ?>
                        <div style="margin-top: 10px; padding: 10px; background: #d1fae5; border-radius: 8px; font-size: 12px; color: #065f46;">
                            <i class="fas fa-check-circle"></i> <strong>Call completed successfully!</strong>
                        </div>
                        <?php elseif($cb['status'] === 'in_progress'): ?>
                        <div style="margin-top: 10px; padding: 10px; background: #fef3c7; border-radius: 8px; font-size: 12px; color: #92400e;">
                            <i class="fas fa-spinner fa-pulse"></i> <strong>Our team will call you shortly!</strong>
                        </div>
                        <?php endif; ?>
                    </div>
                <?php endwhile; else: ?>
                    <div class="no-data-message">
                        <i class="fas fa-phone-slash" style="font-size: 40px; display: block; margin-bottom: 10px; opacity: 0.3;"></i>
                        No callback requests yet. Need help? <a href="contact.php" style="color: var(--primary); font-weight: 800;">Request a callback now!</a>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Quick Queries -->
            <h4 style="margin: 30px 0 15px 0; color: var(--navy); font-size: 18px; font-weight: 800;">
                <i class="fas fa-envelope-open-text" style="color: #0ea5e9;"></i> Quick Email Queries
            </h4>
            <div class="appointment-grid">
                <?php if($quick_query_results && mysqli_num_rows($quick_query_results) > 0): 
                    while($qq = mysqli_fetch_assoc($quick_query_results)): ?>
                    <div class="apt-card" style="border-left: 5px solid #0ea5e9;">
                        <h4><i class="fas fa-comment-dots"></i> Quick Query</h4>
                        <div class="apt-detail">NAME: <strong><?php echo $qq['name']; ?></strong></div>
                        <div class="apt-detail">EMAIL: <strong style="color: #0ea5e9;"><?php echo $qq['email']; ?></strong></div>
                        <div class="apt-detail">MESSAGE: <strong style="font-weight: 500; font-style: italic;"><?php echo substr($qq['message'], 0, 100); ?><?php echo strlen($qq['message']) > 100 ? '...' : ''; ?></strong></div>
                        <div class="apt-detail">SUBMITTED: <strong><?php echo date('d M, Y - h:i A', strtotime($qq['created_at'])); ?></strong></div>
                        <span class="apt-status-badge status-<?php echo strtolower($qq['status']); ?>">● <?php echo strtoupper($qq['status']); ?></span>
                        <?php if($qq['status'] === 'resolved'): ?>
                        <div style="margin-top: 10px; padding: 10px; background: #d1fae5; border-radius: 8px; font-size: 12px; color: #065f46;">
                            <i class="fas fa-check-circle"></i> <strong>Query resolved! Check your email for our response.</strong>
                        </div>
                        <?php elseif($qq['status'] === 'in_process'): ?>
                        <div style="margin-top: 10px; padding: 10px; background: #fef3c7; border-radius: 8px; font-size: 12px; color: #92400e;">
                            <i class="fas fa-cog fa-spin"></i> <strong>Our team is working on your query!</strong>
                        </div>
                        <?php elseif($qq['status'] === 'unread'): ?>
                        <div style="margin-top: 10px; padding: 10px; background: #dbeafe; border-radius: 8px; font-size: 12px; color: #1e3a8a;">
                            <i class="fas fa-clock"></i> <strong>We'll respond to your email soon!</strong>
                        </div>
                        <?php endif; ?>
                    </div>
                <?php endwhile; else: ?>
                    <div class="no-data-message">
                        <i class="fas fa-envelope" style="font-size: 40px; display: block; margin-bottom: 10px; opacity: 0.3;"></i>
                        No email queries yet. Have a question? <a href="contact.php" style="color: var(--primary); font-weight: 800;">Send us a quick query!</a>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Contact Form Submissions -->
            <h4 style="margin: 30px 0 15px 0; color: var(--navy); font-size: 18px; font-weight: 800;">
                <i class="fas fa-file-alt" style="color: #8b5cf6;"></i> Contact Form Submissions
            </h4>
            <div class="appointment-grid">
                <?php if(mysqli_num_rows($contact_results) > 0): 
                    while($c = mysqli_fetch_assoc($contact_results)): ?>
                    <div class="apt-card" style="border-left: 5px solid #8b5cf6;">
                        <h4><i class="fas fa-question-circle"></i> <?php echo $c['subject']; ?></h4>
                        <div class="apt-detail">MESSAGE: <strong style="font-weight: 500; font-style: italic;"><?php echo substr($c['message'], 0, 80); ?>...</strong></div>
                        <div class="apt-detail">SUBMITTED: <strong><?php echo date('d M, Y', strtotime($c['created_at'])); ?></strong></div>
                        <span class="apt-status-badge status-<?php echo strtolower($c['status']); ?>">● <?php echo strtoupper($c['status']); ?></span>
                    </div>
                <?php endwhile; else: ?>
                    <div class="no-data-message">
                        <i class="fas fa-inbox" style="font-size: 40px; display: block; margin-bottom: 10px; opacity: 0.3;"></i>
                        No contact form submissions yet. <a href="contact.php" style="color: var(--primary); font-weight: 800;">Contact us now!</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>
</div>

<?php include "includes/footer.php"; ?>