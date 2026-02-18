<?php 
if (session_status() === PHP_SESSION_NONE) { session_start(); }
include "db.php"; 

// --- 1. master INTAKE PROTOCOL ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize Guardian Identity
    $tier      = mysqli_real_escape_string($conn, $_POST['tier']);
    $amount    = mysqli_real_escape_string($conn, $_POST['amount']);
    $name      = mysqli_real_escape_string($conn, $_POST['name']);
    $phone     = mysqli_real_escape_string($conn, $_POST['phone']);
    $email     = mysqli_real_escape_string($conn, $_POST['email']);
    
    // Generate Sovereign Transaction ID
    $transaction_id = "FM-" . strtoupper(substr(md5(time()), 0, 8));

    // --- 2. master DATABASE SYNCHRONIZATION ---
    $sql = "INSERT INTO foundation_donations 
            (tier_name, amount, guardian_name, mobile, email, status, transaction_id) 
            VALUES 
            ('$tier', '$amount', '$name', '$phone', '$email', 'Pending', '$transaction_id')";

    if (mysqli_query($conn, $sql)) {
        // Success: Trigger Kinetic Loader then redirect
        $status = "success";
    } else {
        $status = "error";
    }
} else {
    header("Location: foundation.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>FurryMart | Processing Protocol</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #518992;   /* Sovereign Teal */
            --navy: #0f1c3f;      /* Enterprise Navy */
            --white: #ffffff;
        }
        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            background: #fffaf5; 
            height: 100vh; display: flex; align-items: center; justify-content: center; 
            margin: 0; overflow: hidden;
        }
        .processing-vault { text-align: center; }
        .quantum-loader {
            width: 80px; height: 80px; border: 8px solid #eef2f6;
            border-top: 8px solid var(--primary); border-radius: 50%;
            animation: spin 1s cubic-bezier(0.68, -0.55, 0.27, 1.55) infinite;
            margin: 0 auto 30px;
        }
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
        h2 { color: var(--navy); font-size: 1.8rem; font-weight: 800; letter-spacing: -1px; margin-bottom: 10px; }
        p { color: #64748b; font-weight: 600; text-transform: uppercase; letter-spacing: 2px; font-size: 12px; }
    </style>
</head>
<body>

<div class="processing-vault">
    <div class="quantum-loader"></div>
    <h2 id="statusText">Initializing Sovereign Sync...</h2>
    <p>Secure Philanthropic Gateway</p>
</div>

<script>
    // --- master REDIRECT ENGINE ---
    const status = "<?php echo $status; ?>";
    
    setTimeout(() => {
        if (status === "success") {
            document.getElementById('statusText').innerText = "Protocol Secured";
            setTimeout(() => {
                window.location.href = "foundation.php?status=success&action=contribution_synced";
            }, 1000);
        } else {
            document.getElementById('statusText').innerText = "Sync Failed";
            setTimeout(() => {
                window.location.href = "foundation.php?status=error";
            }, 1000);
        }
    }, 2000); // 2-second professional delay for visual weight
</script>

</body>
</html>