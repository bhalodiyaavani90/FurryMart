<?php
/**
 * FURRYMART ADMIN - MANAGE NEWSLETTER SUBSCRIBERS
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check admin session to prevent header warnings
if (!isset($_SESSION['admin_email'])) {
    $_SESSION['admin_email'] = "Admin"; 
}
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}
include '../db.php'; 

$is_edit = false;
$edit_id = "";
$edit_email = "";

// --- 1. HANDLE DELETE ACTION ---
if (isset($_GET['delete'])) {
    $del_id = mysqli_real_escape_string($conn, $_GET['delete']);
    if (mysqli_query($conn, "DELETE FROM newsletter_subscribers WHERE id = '$del_id'")) {
        header("Location: admin_manage_subscribers.php?status=success&action=deleted");
        exit();
    }
}

// --- 2. HANDLE EDIT FETCH ---
if (isset($_GET['edit'])) {
    $is_edit = true;
    $edit_id = mysqli_real_escape_string($conn, $_GET['edit']);
    $res = mysqli_query($conn, "SELECT * FROM newsletter_subscribers WHERE id = '$edit_id'");
    $row = mysqli_fetch_assoc($res);
    if ($row) {
        $edit_email = $row['email'];
    }
}

// --- 3. HANDLE UPDATE ACTION ---
if (isset($_POST['update_subscriber'])) {
    $sub_id = mysqli_real_escape_string($conn, $_POST['sub_id']);
    $new_email = mysqli_real_escape_string($conn, $_POST['email']);

    $sql = "UPDATE newsletter_subscribers SET email='$new_email' WHERE id='$sub_id'";
    if (mysqli_query($conn, $sql)) {
        header("Location: admin_manage_subscribers.php?status=success&action=updated");
        exit();
    }
}

include('includes/header.php'); 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>FurryMart | Manage Subscribers</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        :root { --primary: #518992; --navy: #0f1c3f; --bg: #f8fafc; --white: #ffffff; --border: #e2e8f0; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: var(--bg); color: var(--navy); margin: 0; }
        .admin-wrapper { padding: 40px 5%; max-width: 1200px; margin: auto; }
        
        /* Form Card */
        .admin-card { background: var(--white); padding: 35px; border-radius: 30px; box-shadow: 0 10px 40px rgba(0,0,0,0.04); border: 1px solid var(--border); margin-bottom: 40px; }
        label { display: block; font-weight: 700; font-size: 13px; margin-bottom: 10px; text-transform: uppercase; color: var(--primary); }
        .input-group { display: flex; gap: 15px; }
        input[type="email"] { width: 100%; padding: 14px 20px; border: 1.5px solid var(--border); border-radius: 12px; outline: none; background: #fbfcfe; transition: 0.3s; }
        input:focus { border-color: var(--primary); background: #fff; box-shadow: 0 0 0 4px rgba(81, 137, 146, 0.1); }
        .btn-update { background: var(--primary); color: white; border: none; padding: 0 35px; border-radius: 12px; font-weight: 800; cursor: pointer; transition: 0.3s; }
        .btn-update:hover { background: #3d6a71; transform: translateY(-2px); }

        /* Table Card */
        .table-container { background: var(--white); border-radius: 30px; padding: 35px; box-shadow: 0 10px 40px rgba(0,0,0,0.04); border: 1px solid var(--border); }
        h3 { font-weight: 800; margin-bottom: 25px; display: flex; align-items: center; gap: 10px; }
        table { width: 100%; border-collapse: separate; border-spacing: 0; }
        th { text-align: left; padding: 15px; color: #64748b; font-size: 12px; text-transform: uppercase; border-bottom: 2px solid #f1f5f9; letter-spacing: 1px; }
        td { padding: 18px 15px; border-bottom: 1px solid #f1f5f9; font-size: 14px; vertical-align: middle; }
        
        /* Action Buttons */
        .action-btns { display: flex; gap: 8px; }
        .act-btn { width: 35px; height: 35px; border-radius: 10px; display: flex; align-items: center; justify-content: center; text-decoration: none; transition: 0.3s; }
        .btn-edit { background: #eff6ff; color: #2563eb; }
        .btn-del { background: #fff1f2; color: #e11d48; }
        .act-btn:hover { transform: scale(1.1); }

        .empty-state { text-align: center; padding: 40px; color: #94a3b8; }
    </style>
</head>
<body>

<main class="admin-wrapper">
    <?php if($is_edit): ?>
    <div class="admin-card">
        <h3><i class="fas fa-user-edit"></i> Edit Subscriber</h3>
        <form action="admin_manage_subscribers.php" method="POST">
            <input type="hidden" name="sub_id" value="<?php echo $edit_id; ?>">
            <div class="input-group">
                <div style="flex: 1;">
                    <label>Subscriber Email Address</label>
                    <input type="email" name="email" value="<?php echo $edit_email; ?>" required>
                </div>
                <div style="display: flex; align-items: flex-end;">
                    <button type="submit" name="update_subscriber" class="btn-update">UPDATE INFO</button>
                    <a href="admin_manage_subscribers.php" style="margin-left: 15px; align-self: center; color: #64748b; text-decoration: none; font-size: 13px;">Cancel</a>
                </div>
            </div>
        </form>
    </div>
    <?php endif; ?>

    <div class="table-container">
        <h3><i class="fas fa-users-viewfinder"></i> Newsletter Audience</h3>
        <table>
            <thead>
                <tr>
                    <th width="80">ID</th>
                    <th>Subscriber Email</th>
                    <th>Date Joined</th>
                    <th width="120">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $res = mysqli_query($conn, "SELECT * FROM newsletter_subscribers ORDER BY id DESC");
                if(mysqli_num_rows($res) > 0) {
                    while($row = mysqli_fetch_assoc($res)) {
                ?>
                <tr>
                    <td><b style="color: #cbd5e1;">#<?php echo $row['id']; ?></b></td>
                    <td style="font-weight: 600;"><?php echo $row['email']; ?></td>
                    <td style="color: #64748b;"><?php echo date('M d, Y - h:i A', strtotime($row['subscribed_at'])); ?></td>
                    <td>
                        <div class="action-btns">
                            <a href="?edit=<?php echo $row['id']; ?>" class="act-btn btn-edit" title="Edit"><i class="fa fa-edit"></i></a>
                            <a href="?delete=<?php echo $row['id']; ?>" class="act-btn btn-del" title="Unsubscribe" onclick="return confirm('Remove this email from the list?')"><i class="fa fa-trash"></i></a>
                        </div>
                    </td>
                </tr>
                <?php 
                    }
                } else {
                    echo "<tr><td colspan='4' class='empty-state'>No active subscribers found.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</main>

<script>
    // SUCCESS POPUP HANDLER
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('status') === 'success') {
        const action = urlParams.get('action');
        Swal.fire({
            title: 'Action Successful!',
            text: `Subscriber has been ${action} correctly.`,
            icon: 'success',
            confirmButtonColor: '#518992'
        }).then(() => {
            window.history.replaceState({}, document.title, window.location.pathname);
        });
    }
</script>

<?php include('includes/footer.php'); ?>