<?php 
if (session_status() === PHP_SESSION_NONE) { session_start(); }
include "db.php"; 

// 1. LOGIN PROTOCOL: Mandatory redirect if no session exists
if(!isset($_SESSION['email'])){
    // Redirect to login page and remember the tier they wanted
    $tier = isset($_GET['tier']) ? $_GET['tier'] : 'Nourish';
    header("Location: login.php?redirect=donate_details.php?tier=$tier");
    exit();
}

include "includes/header.php"; 

$user_email = $_SESSION['email'];

// 2. FETCH LOGGED-IN GUARDIAN DATA
$user_res = mysqli_query($conn, "SELECT * FROM users WHERE email='$user_email'");
$user = mysqli_fetch_assoc($user_res);

// 3. CAPTURE THE TIER FROM URL
$tier_name = isset($_GET['tier']) ? mysqli_real_escape_string($conn, $_GET['tier']) : "General Support";

// 4. TIER-TO-AMOUNT MAPPING ENGINE
$tier_prices = [
    "Nourish" => 1500,
    "Heal"    => 8500,
    "Rescue"  => 25000
];

// 5. ASSIGN THE AMOUNT
if (isset($_GET['amount'])) {
    $final_amount = (int)$_GET['amount'];
} elseif (array_key_exists($tier_name, $tier_prices)) {
    $final_amount = $tier_prices[$tier_name];
} else {
    $final_amount = 0; 
}
?>

<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
    :root {
        --primary: #518992;   /* Sovereign Teal */
        --navy: #0f1c3f;      /* Enterprise Navy */
        --bg-warm: #fdfaf5;   /* Brand Warmth */
        --white: #ffffff;
        --heart: #e74c3c;     /* Action Red */
    }

    body { font-family: 'Plus Jakarta Sans', sans-serif; background: var(--bg-warm); color: var(--navy); }

    .confirmation-vault { padding: 100px 8%; display: flex; justify-content: center; }
    
    .confirm-card { 
        background: var(--white); width: 100%; max-width: 800px; padding: 60px; 
        border-radius: 60px; box-shadow: 0 40px 100px rgba(15, 28, 63, 0.08); text-align: center;
    }

    .amount-badge {
        display: inline-block; padding: 15px 40px; border: 2.5px dashed var(--primary);
        border-radius: 50px; font-size: 2.5rem; font-weight: 800; color: var(--primary);
        margin: 30px 0;
    }

    .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 30px; text-align: left; margin-top: 40px; }
    .form-group label { display: block; font-weight: 800; font-size: 13px; text-transform: uppercase; margin-bottom: 12px; opacity: 0.7; }
    .form-control { width: 100%; padding: 18px; border: 2px solid #f1f5f9; border-radius: 20px; font-weight: 600; background: #fcfcfc; box-sizing: border-box; transition: 0.3s; }
    .form-control:focus { border-color: var(--primary); outline: none; background: #fff; }
    .form-control[readonly] { background: #f8fafc; color: #94a3b8; cursor: not-allowed; }

    .btn-sync { 
        width: 100%; margin-top: 50px; padding: 22px; background: var(--heart); 
        color: white !important; border: none; border-radius: 20px; font-weight: 900; 
        text-transform: uppercase; letter-spacing: 2px; cursor: pointer; transition: 0.4s;
    }
    .btn-sync:hover { transform: translateY(-5px); box-shadow: 0 15px 30px rgba(231, 76, 60, 0.3); }
</style>

<main class="confirmation-vault">
    <div class="confirm-card animate__animated animate__fadeInUp">
        <h4 style="color:var(--primary); font-weight:800; text-transform:uppercase; letter-spacing:3px;">Guardian Protocol</h4>
        <h1 style="font-size:3rem; font-weight:900; margin:15px 0;">Finalize Donation</h1>

        <div class="amount-badge">₹<?php echo number_format($final_amount); ?></div>

        <p style="font-size:1.1rem; color:#64748b;">Allocating mission funds to the <b style="color:var(--navy);"><?php echo $tier_name; ?></b> clinical cycle.</p>

        <form action="process_donation.php" method="POST">
            <input type="hidden" name="tier" value="<?php echo $tier_name; ?>">
            <input type="hidden" name="amount" value="<?php echo $final_amount; ?>">

            <div class="form-grid">
                <div class="form-group">
                    <label>Guardian Identity</label>
                    <input type="text" name="name" class="form-control" value="<?php echo $user['first_name'] . ' ' . $user['last_name']; ?>" readonly>
                </div>
                <div class="form-group">
                    <label>Mobile Link</label>
                    <input type="tel" name="phone" class="form-control" value="<?php echo $user['mobile_number']; ?>" required>
                </div>
                <div class="form-group" style="grid-column: span 2;">
                    <label>Guardian Email (Synchronized)</label>
                    <input type="email" name="email" class="form-control" value="<?php echo $user['email']; ?>" readonly>
                </div>
            </div>

            <button type="submit" class="btn-sync">Deploy Funds & Sync Records</button>
        </form>
    </div>
</main>

<?php include 'includes/footer.php'; ?>