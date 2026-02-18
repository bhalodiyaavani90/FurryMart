<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['admin_id'])) { header("Location: login.php"); exit(); }
include '../db.php'; 

// --- 1. master ACTION HANDLERS ---
if (isset($_GET['delete'])) {
    $del_id = mysqli_real_escape_string($conn, $_GET['delete']);
    mysqli_query($conn, "DELETE FROM foundation_donations WHERE id = '$del_id'");
    header("Location: admin_manage_donations.php?status=success&action=deleted");
    exit();
}

// --- master NOTIFICATION & STATUS UPDATE PROTOCOL ---
if (isset($_POST['update_status'])) {
    $id = mysqli_real_escape_string($conn, $_POST['id']);
    $new_status = mysqli_real_escape_string($conn, $_POST['new_status']);
    
    // FETCH SPECIFIC GUARDIAN DETAILS
    $user_q = mysqli_query($conn, "SELECT * FROM foundation_donations WHERE id = '$id'");
    $u = mysqli_fetch_assoc($user_q);
    
    if ($u) {
        $guardian_email = $u['email'];
        $guardian_name = $u['guardian_name'];
        $tier = $u['tier_name'];
        $txn_id = $u['transaction_id'];

        // UPDATE DATABASE
        mysqli_query($conn, "UPDATE foundation_donations SET status = '$new_status' WHERE id = '$id'");

        // TRIGGER EMAIL ONLY ON "COMPLETED" STATUS
        if ($new_status == 'Completed') {
            $subject = "FurryMart Foundation | Impact Confirmation: $txn_id";
            $message = "
            <html>
            <body style='font-family: Arial, sans-serif; line-height: 1.6; color: #0f1c3f;'>
                <div style='background:#fdfaf5; padding:40px; border-radius:30px;'>
                    <h2 style='color: #518992;'>Dear $guardian_name,</h2>
                    <p>Your contribution to the <b>$tier Protocol</b> has been successfully verified.</p>
                    <p style='background:#fff; padding:15px; border-radius:10px; border:1px dashed #518992;'>
                        <b>Sovereign Transaction ID:</b> $txn_id
                    </p>
                    <p>Because of your support, community pets are receiving life-saving clinical care today.</p>
                    <hr style='border:none; border-top:1px solid #eee; margin:20px 0;'>
                    <p style='font-size: 12px; color: #64748b;'>FurryMart Foundation | India's Professional Rescue Ecosystem</p>
                </div>
            </body>
            </html>";

            $headers = "MIME-Version: 1.0" . "\r\n";
            $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
            $headers .= 'From: FurryMart Foundation <foundation@furrymart.com>' . "\r\n";

            mail($guardian_email, $subject, $message, $headers);
        }
    }
    header("Location: admin_manage_donations.php?status=success&action=updated_and_notified");
    exit();
}

include('includes/header.php'); 
?>

<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
    :root { 
        --primary: #518992; --navy: #0f1c3f; --white: #ffffff; 
        --bg: #f8fafc; --border: #e2e8f0; 
    }
    body { font-family: 'Plus Jakarta Sans', sans-serif; background: var(--bg); color: var(--navy); margin: 0; }
    .admin-wrapper { padding: 50px 5%; max-width: 1500px; margin: auto; }

    .dashboard-stats { display: grid; grid-template-columns: repeat(3, 1fr); gap: 25px; margin-bottom: 50px; }
    .stat-box { background: var(--white); padding: 30px; border-radius: 25px; box-shadow: 0 10px 30px rgba(0,0,0,0.03); border: 1px solid var(--border); display: flex; align-items: center; gap: 20px; }
    .stat-box i { font-size: 2rem; color: var(--primary); }
    .stat-box h2 { margin: 0; font-size: 1.8rem; font-weight: 800; }
    .stat-box p { margin: 0; font-weight: 700; opacity: 0.5; text-transform: uppercase; font-size: 11px; }

    .table-vault { background: var(--white); border-radius: 40px; padding: 40px; box-shadow: 0 20px 60px rgba(15, 28, 63, 0.05); border: 1px solid var(--border); }
    table { width: 100%; border-collapse: separate; border-spacing: 0; }
    th { text-align: left; padding: 18px; color: #64748b; font-size: 11px; text-transform: uppercase; border-bottom: 2px solid #f1f5f9; }
    td { padding: 18px; border-bottom: 1px solid #f1f5f9; vertical-align: middle; font-size: 14px; font-weight: 600; }

    .badge { padding: 6px 14px; border-radius: 50px; font-size: 10px; font-weight: 800; text-transform: uppercase; }
    .badge-Pending { background: #fffbeb; color: #b45309; }
    .badge-Completed { background: #f0fdf4; color: #15803d; }
    .badge-Cancelled { background: #fef2f2; color: #b91c1c; }

    .status-select { padding: 8px; border-radius: 10px; border: 1.5px solid var(--border); font-family: inherit; font-size: 12px; font-weight: 700; cursor: pointer; }
    .btn-del { background: #fff1f2; color: #e11d48; width: 38px; height: 38px; border-radius: 10px; display: inline-flex; align-items: center; justify-content: center; text-decoration: none; transition: 0.3s; }
</style>

<main class="admin-wrapper">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:40px;">
        <h1 style="font-weight:900; letter-spacing:-2px; margin:0;">Donation Ecosystem</h1>
        <p style="font-weight:700; color:var(--primary);">Status & Notification Hub</p>
    </div>

    <div class="dashboard-stats">
        <?php 
        $total = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(amount) as total FROM foundation_donations WHERE status='Completed'"))['total'];
        $count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM foundation_donations"))['count'];
        $pending = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM foundation_donations WHERE status='Pending'"))['count'];
        ?>
        <div class="stat-box">
            <i class="fas fa-hand-holding-heart"></i>
            <div><h2>₹<?php echo number_format($total ?? 0); ?></h2><p>Revenue</p></div>
        </div>
        <div class="stat-box">
            <i class="fas fa-users"></i>
            <div><h2><?php echo $count; ?></h2><p>Guardians</p></div>
        </div>
        <div class="stat-box">
            <i class="fas fa-sync-alt"></i>
            <div><h2><?php echo $pending; ?></h2><p>Pending Sync</p></div>
        </div>
    </div>

    <div class="table-vault">
        <table>
            <thead>
                <tr>
                    <th>Guardian</th>
                    <th>Tier / Date</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Update</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $res = mysqli_query($conn, "SELECT * FROM foundation_donations ORDER BY id DESC");
                while($row = mysqli_fetch_assoc($res)) {
                ?>
                <tr>
                    <td>
                        <div style="color:var(--navy);"><?php echo $row['guardian_name']; ?></div>
                        <div style="font-size:10px; opacity:0.6;"><?php echo $row['email']; ?></div>
                    </td>
                    <td>
                        <span style="color:var(--primary);"><?php echo strtoupper($row['tier_name']); ?></span>
                        <div style="font-size:9px; opacity:0.5;"><?php echo date('d M Y', strtotime($row['created_at'])); ?></div>
                    </td>
                    <td>₹<?php echo number_format($row['amount']); ?></td>
                    <td><span class="badge badge-<?php echo $row['status']; ?>"><?php echo $row['status']; ?></span></td>
                    <td>
                        <form action="" method="POST">
                            <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                            <select name="new_status" class="status-select" onchange="this.form.submit()">
                                <option value="Pending" <?php if($row['status'] == 'Pending') echo 'selected'; ?>>Pending</option>
                                <option value="Completed" <?php if($row['status'] == 'Completed') echo 'selected'; ?>>Completed</option>
                                <option value="Cancelled" <?php if($row['status'] == 'Cancelled') echo 'selected'; ?>>Cancelled</option>
                            </select>
                            <input type="hidden" name="update_status" value="1">
                        </form>
                    </td>
                    <td>
                        <a href="?delete=<?php echo $row['id']; ?>" class="btn-del" onclick="return confirm('Purge record?')">
                            <i class="fa fa-trash-alt"></i>
                        </a>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</main>

<script>
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('status') === 'success') {
        const action = urlParams.get('action');
        Swal.fire({
            title: 'Protocol Confirmed',
            text: `Matrix record has been ${action.replace('_', ' ')} successfully.`,
            icon: 'success',
            confirmButtonColor: '#518992'
        });
    }
</script>

<?php include('includes/footer.php'); ?>