<?php
/**
 * FURRYMART ADMIN - SOVEREIGN MAKEOVER HUB
 * Features: Dual-Mode Form (Add/Update) + Indestructible Delete Protocol
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

include '../db.php'; 

$is_edit = false;
$edit_id = $old_before = $old_after = "";

// --- 1. DELETE PROTOCOL ---
if (isset($_GET['delete'])) {
    $del_id = mysqli_real_escape_string($conn, $_GET['delete']);
    mysqli_query($conn, "DELETE FROM pawsome_makeovers WHERE id = '$del_id'");
    header("Location: admin_makeovers.php?status=success&action=purged");
    exit();
}

// --- 2. EDIT FETCH PROTOCOL ---
if (isset($_GET['edit'])) {
    $is_edit = true;
    $edit_id = mysqli_real_escape_string($conn, $_GET['edit']);
    $res = mysqli_query($conn, "SELECT * FROM pawsome_makeovers WHERE id = '$edit_id'");
    $row = mysqli_fetch_assoc($res);
    if ($row) {
        $old_before = $row['before_image'];
        $old_after  = $row['after_image'];
    }
}

// --- 3. SAVE/UPDATE PROTOCOL ---
if (isset($_POST['submit_makeover'])) {
    $target_dir = "../uploads/makeovers/";
    if (!is_dir($target_dir)) { mkdir($target_dir, 0777, true); }

    $m_id = isset($_POST['makeover_id']) ? $_POST['makeover_id'] : "";
    
    // Handle "Before" Image
    if (!empty($_FILES["before_image"]["name"])) {
        $before_file = time() . "_B_" . basename($_FILES["before_image"]["name"]);
        move_uploaded_file($_FILES["before_image"]["tmp_name"], $target_dir . $before_file);
    } else {
        $before_file = $_POST['existing_before'];
    }

    // Handle "After" Image
    if (!empty($_FILES["after_image"]["name"])) {
        $after_file = time() . "_A_" . basename($_FILES["after_image"]["name"]);
        move_uploaded_file($_FILES["after_image"]["tmp_name"], $target_dir . $after_file);
    } else {
        $after_file = $_POST['existing_after'];
    }

    if ($m_id) {
        $sql = "UPDATE pawsome_makeovers SET before_image='$before_file', after_image='$after_file' WHERE id='$m_id'";
        $act = "updated";
    } else {
        $sql = "INSERT INTO pawsome_makeovers (before_image, after_image, status) VALUES ('$before_file', '$after_file', 'active')";
        $act = "published";
    }
    
    if (mysqli_query($conn, $sql)) {
        header("Location: admin_makeovers.php?status=success&action=$act");
        exit();
    }
}

include('includes/header.php'); 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        :root { --primary: #518992; --navy: #0f1c3f; --bg: #f8fafc; --white: #ffffff; --border: #e2e8f0; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: var(--bg); color: var(--navy); }
        .admin-wrapper { padding: 40px 5%; max-width: 1400px; margin: auto; }
        
        .dash-hub { background: var(--navy); color: white; padding: 30px 50px; border-radius: 30px; display: flex; justify-content: space-between; align-items: center; margin-bottom: 40px; }
        .stat-group h2 { margin: 0; font-size: 32px; color: var(--primary); font-weight: 800; }
        .stat-group span { font-size: 11px; text-transform: uppercase; letter-spacing: 2px; font-weight: 800; opacity: 0.7; }

        /* Unified Form Card */
        .upload-card { background: var(--white); padding: 45px; border-radius: 45px; box-shadow: 0 10px 40px rgba(0,0,0,0.03); border: 1px solid var(--border); margin-bottom: 50px; }
        .dual-input-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 30px; margin-bottom: 30px; }
        
        .file-zone { border: 2px dashed var(--border); border-radius: 25px; padding: 30px; text-align: center; cursor: pointer; transition: 0.3s; position: relative; }
        .file-zone:hover { border-color: var(--primary); background: #f0fdfa; }
        .file-zone i { font-size: 30px; color: var(--primary); margin-bottom: 10px; }
        .file-zone span { display: block; font-size: 13px; font-weight: 700; color: #64748b; }
        .file-zone input { position: absolute; top: 0; left: 0; width: 100%; height: 100%; opacity: 0; cursor: pointer; }

        .btn-publish { background: var(--primary); color: white; border: none; padding: 20px 50px; border-radius: 50px; font-weight: 800; cursor: pointer; transition: 0.4s; width: 100%; font-size: 14px; }
        .btn-publish:hover { background: var(--navy); transform: scale(1.02); }

        /* Professional Comparison Grid */
        .index-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(380px, 1fr)); gap: 30px; }
        .makeover-item { background: white; padding: 25px; border-radius: 35px; border: 1px solid var(--border); transition: 0.4s; position: relative; }
        .makeover-item:hover { transform: translateY(-10px); box-shadow: 0 20px 40px rgba(0,0,0,0.05); }

        .thumb-pair { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; height: 160px; margin-bottom: 20px; }
        .thumb-box { border-radius: 15px; overflow: hidden; position: relative; border: 2px solid #f1f5f9; }
        .thumb-box img { width: 100%; height: 100%; object-fit: cover; }
        .thumb-label { position: absolute; bottom: 0; left: 0; font-size: 9px; font-weight: 900; padding: 4px 10px; text-transform: uppercase; border-top-right-radius: 10px; color: white; }
        .l-before { background: #0ea5e9; }
        .l-after { background: #10b981; }

        /* Action Buttons */
        .action-tray { display: flex; justify-content: flex-end; gap: 10px; position: absolute; top: 15px; right: 15px; }
        .icon-btn { width: 35px; height: 35px; border-radius: 50%; display: flex; align-items: center; justify-content: center; text-decoration: none; transition: 0.3s; font-size: 14px; }
        .edit-btn { background: #eff6ff; color: #2563eb; }
        .edit-btn:hover { background: #2563eb; color: white; }
        .delete-btn { background: #fee2e2; color: #ef4444; }
        .delete-btn:hover { background: #ef4444; color: white; transform: rotate(90deg); }
    </style>
</head>
<body>

<main class="admin-wrapper">
    <div class="dash-hub">
        <div class="stat-group">
            <span>Sovereign Portfolio Index</span>
            <h2><?php echo $is_edit ? 'Update Protocol' : 'Register New Transformation'; ?></h2>
        </div>
        <i class="fas fa-scissors" style="font-size: 40px; opacity: 0.2;"></i>
    </div>

    <div class="upload-card">
        <h3 style="font-size:18px; font-weight:800; margin-bottom:30px;">
            <i class="fas <?php echo $is_edit ? 'fa-edit' : 'fa-plus-circle'; ?>"></i> 
            <?php echo $is_edit ? 'Modifying Transformation #'.$edit_id : 'Clinical Registry'; ?>
        </h3>
        
        <form action="admin_makeovers.php" method="POST" enctype="multipart/form-data">
            <?php if($is_edit): ?>
                <input type="hidden" name="makeover_id" value="<?php echo $edit_id; ?>">
                <input type="hidden" name="existing_before" value="<?php echo $old_before; ?>">
                <input type="hidden" name="existing_after" value="<?php echo $old_after; ?>">
            <?php endif; ?>

            <div class="dual-input-grid">
                <div class="file-zone">
                    <i class="fas fa-camera-retro"></i>
                    <span id="before-text"><?php echo $is_edit ? 'Replace Before Image' : 'Select "Before" Image'; ?></span>
                    <input type="file" name="before_image" <?php echo $is_edit ? '' : 'required'; ?> onchange="updateFileName(this, 'before-text')">
                </div>
                <div class="file-zone">
                    <i class="fas fa-magic"></i>
                    <span id="after-text"><?php echo $is_edit ? 'Replace After Image' : 'Select "After" Image'; ?></span>
                    <input type="file" name="after_image" <?php echo $is_edit ? '' : 'required'; ?> onchange="updateFileName(this, 'after-text')">
                </div>
            </div>

            <div style="display:flex; gap:20px;">
                <button type="submit" name="submit_makeover" class="btn-publish">
                    <?php echo $is_edit ? 'EXECUTE UPDATE' : 'PUBLISH CLINICAL MAKEOVER'; ?>
                </button>
                <?php if($is_edit): ?> <a href="admin_makeovers.php" style="align-self:center; font-weight:800; color:#64748b; text-decoration:none;">Cancel</a> <?php endif; ?>
            </div>
        </form>
    </div>

    <div class="index-grid">
        <?php
        $res = mysqli_query($conn, "SELECT * FROM pawsome_makeovers ORDER BY id DESC");
        while($row = mysqli_fetch_assoc($res)) {
            echo '
            <div class="makeover-item">
                <div class="action-tray">
                    <a href="?edit='.$row['id'].'" class="icon-btn edit-btn"><i class="fas fa-pen"></i></a>
                    <a href="?delete='.$row['id'].'" class="icon-btn delete-btn" onclick="return confirm(\'Purge this makeover?\')"><i class="fas fa-times"></i></a>
                </div>
                <div class="thumb-pair">
                    <div class="thumb-box">
                        <img src="../uploads/makeovers/'.$row['before_image'].'">
                        <div class="thumb-label l-before">Before</div>
                    </div>
                    <div class="thumb-box">
                        <img src="../uploads/makeovers/'.$row['after_image'].'">
                        <div class="thumb-label l-after">After</div>
                    </div>
                </div>
                <div style="display:flex; justify-content:space-between; align-items:center;">
                    <span style="font-size:14px; font-weight:800; color:var(--navy);">Makeover #'.$row['id'].'</span>
                    <span style="font-size:11px; font-weight:700; color:var(--primary); text-transform:uppercase;">Verified Clinic</span>
                </div>
            </div>';
        }
        ?>
    </div>
</main>

<script>
    function updateFileName(input, targetId) {
        if (input.files && input.files[0]) {
            document.getElementById(targetId).innerText = input.files[0].name;
            document.getElementById(targetId).parentElement.style.borderColor = "#518992";
            document.getElementById(targetId).parentElement.style.background = "#f0fdfa";
        }
    }

    const params = new URLSearchParams(window.location.search);
    if(params.get('status') === 'success') {
        Swal.fire({
            title: 'Sovereign Success',
            text: `Protocol has been ${params.get('action')} correctly.`,
            icon: 'success',
            confirmButtonColor: '#518992',
            borderRadius: '30px'
        }).then(() => window.history.replaceState({}, '', 'admin_makeovers.php'));
    }
</script>

<?php include('includes/footer.php'); ?>