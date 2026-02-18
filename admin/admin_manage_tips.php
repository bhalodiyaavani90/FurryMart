<?php
/**
 * FURRYMART ADMIN - ALL-IN-ONE MANAGEMENT (CRUD)
 * Handles Add, Edit, Delete, and View on a single page.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

include '../db.php'; 

$msg = "";
$is_edit = false;
$edit_id = "";
$title = $category = $short_description = $full_content = $publish_date = "";
$thumbnail_path = $inside_path = "";

// --- 1. HANDLE DELETE ACTION ---
if (isset($_GET['delete'])) {
    $del_id = mysqli_real_escape_string($conn, $_GET['delete']);
    $res = mysqli_query($conn, "SELECT thumbnail_image, inside_image FROM expert_tips WHERE id = '$del_id'");
    $data = mysqli_fetch_assoc($res);
    
    if ($data) {
        // Delete physical files from the folder
        if(!empty($data['thumbnail_image'])) @unlink("../" . $data['thumbnail_image']); 
        if(!empty($data['inside_image'])) @unlink("../" . $data['inside_image']);
        
        mysqli_query($conn, "DELETE FROM expert_tips WHERE id = '$del_id'");
        header("Location: admin_manage_tips.php?status=success&action=deleted");
        exit();
    }
}

// --- 2. HANDLE EDIT FETCH (Loads data into the form) ---
if (isset($_GET['edit'])) {
    $is_edit = true;
    $edit_id = mysqli_real_escape_string($conn, $_GET['edit']);
    $res = mysqli_query($conn, "SELECT * FROM expert_tips WHERE id = '$edit_id'");
    $row = mysqli_fetch_assoc($res);
    
    if ($row) {
        $title = $row['title'];
        $category = $row['category'];
        $short_description = $row['short_description'];
        $full_content = $row['full_content'];
        $publish_date = $row['publish_date'];
        $thumbnail_path = $row['thumbnail_image'];
        $inside_path = $row['inside_image'];
    }
}

// --- 3. HANDLE SAVE (INSERT OR UPDATE) ---
if (isset($_POST['save_tip'])) {
    $form_id = mysqli_real_escape_string($conn, $_POST['form_id']);
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    $short_desc = mysqli_real_escape_string($conn, $_POST['short_description']);
    $full_content = mysqli_real_escape_string($conn, $_POST['full_content']);
    $p_date = $_POST['publish_date'];

    function uploadImage($fileInputName, $targetDir, $existingPath = "") {
        if (!isset($_FILES[$fileInputName]) || $_FILES[$fileInputName]['error'] == UPLOAD_ERR_NO_FILE) {
            return $existingPath; // Keep old image if new one isn't selected
        }
        
        $fileName = basename($_FILES[$fileInputName]["name"]);
        $targetFilePath = $targetDir . time() . "_" . $fileName; 
        $imageFileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));
        $allowedTypes = array('jpg', 'jpeg', 'png', 'gif', 'webp', 'jfif', 'bmp');

        if (in_array($imageFileType, $allowedTypes)) {
            if (move_uploaded_file($_FILES[$fileInputName]["tmp_name"], $targetFilePath)) {
                return str_replace("../", "", $targetFilePath);
            }
        }
        return $existingPath;
    }

    $uploadDir = "../uploads/tips/"; 
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
    
    $finalThumb = uploadImage('thumbnail_image', $uploadDir, $_POST['old_thumb']);
    $finalInside = uploadImage('inside_image', $uploadDir, $_POST['old_inside']);

    if ($form_id == "") {
        // ADD NEW
        $sql = "INSERT INTO expert_tips (title, category, thumbnail_image, inside_image, short_description, full_content, publish_date) 
                VALUES ('$title', '$category', '$finalThumb', '$finalInside', '$short_desc', '$full_content', '$p_date')";
        $action_text = "added";
    } else {
        // UPDATE EXISTING
        $sql = "UPDATE expert_tips SET title='$title', category='$category', thumbnail_image='$finalThumb', 
                inside_image='$finalInside', short_description='$short_desc', full_content='$full_content', publish_date='$p_date' 
                WHERE id='$form_id'";
        $action_text = "updated";
    }

    if (mysqli_query($conn, $sql)) {
        header("Location: admin_manage_tips.php?status=success&action=$action_text");
        exit();
    }
}

include('includes/header.php'); 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>FurryMart | Manage Insights</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        :root { --primary: #518992; --bg: #f8fafc; --white: #ffffff; --text: #1e293b; --border: #e2e8f0; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: var(--bg); color: var(--text); }
        .admin-page-wrapper { padding: 40px 5%; max-width: 1400px; margin: auto; }
        
        /* Form Styling */
        .admin-card { background: var(--white); padding: 40px; border-radius: 30px; box-shadow: 0 10px 40px rgba(0,0,0,0.04); border: 1px solid var(--border); margin-bottom: 50px; }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 25px; margin-bottom: 20px; }
        label { display: block; font-weight: 700; font-size: 13px; margin-bottom: 8px; color: var(--primary); text-transform: uppercase; }
        input[type="text"], input[type="date"], textarea { width: 100%; padding: 14px; border: 1.5px solid var(--border); border-radius: 12px; font-family: inherit; box-sizing: border-box; background: #fbfcfe; }
        .btn-publish { background: var(--primary); color: white; padding: 16px 30px; border: none; border-radius: 12px; font-weight: 800; cursor: pointer; transition: 0.3s; text-transform: uppercase; width: 100%; }
        
        /* Table Styling */
        .table-container { background: var(--white); border-radius: 30px; padding: 35px; box-shadow: 0 10px 40px rgba(0,0,0,0.04); border: 1px solid var(--border); }
        table { width: 100%; border-collapse: separate; border-spacing: 0; }
        th { text-align: left; padding: 18px; color: #64748b; font-size: 12px; text-transform: uppercase; border-bottom: 2px solid #f1f5f9; }
        td { padding: 18px; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
        .thumb-preview { width: 60px; height: 60px; border-radius: 12px; object-fit: cover; }
        .action-btn { padding: 10px 18px; border-radius: 10px; text-decoration: none; font-weight: 700; font-size: 13px; transition: 0.3s; display: inline-flex; align-items: center; gap: 6px; }
        .btn-edit { background: #eff6ff; color: #2563eb; }
        .btn-del { background: #fff1f2; color: #e11d48; }
    </style>
</head>
<body>

<main class="admin-page-wrapper">
    <div class="admin-card">
        <h2 style="font-weight:800; margin: 0; color: #0f172a;"><?php echo $is_edit ? "Update Insight Info" : "Publish New Insight"; ?></h2>
        <form action="admin_manage_tips.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="form_id" value="<?php echo $edit_id; ?>">
            <input type="hidden" name="old_thumb" value="<?php echo $thumbnail_path; ?>">
            <input type="hidden" name="old_inside" value="<?php echo $inside_path; ?>">

            <div class="form-row" style="margin-top:25px;">
                <div><label>Title</label><input type="text" name="title" value="<?php echo $title; ?>" required></div>
                <div><label>Category</label><input type="text" name="category" value="<?php echo $category; ?>" required></div>
            </div>

            <div class="form-row">
                <div><label>Thumbnail (JPG/PNG)</label><input type="file" name="thumbnail_image"></div>
                <div><label>Popup Image (JPG/PNG)</label><input type="file" name="inside_image"></div>
            </div>

            <div style="margin-bottom:20px;"><label>Short Summary</label><textarea name="short_description" rows="2" required><?php echo $short_description; ?></textarea></div>
            <div style="margin-bottom:20px;"><label>Full Article Content</label><textarea name="full_content" rows="6" required><?php echo $full_content; ?></textarea></div>

            <div class="form-row">
                <div><label>Date</label><input type="date" name="publish_date" value="<?php echo $publish_date ? $publish_date : date('Y-m-d'); ?>" required></div>
                <div style="display: flex; align-items: flex-end;">
                    <button type="submit" name="save_tip" class="btn-publish"><?php echo $is_edit ? "Save Changes" : "Publish to Website"; ?></button>
                </div>
            </div>
            <?php if($is_edit): ?>
                <a href="admin_manage_tips.php" style="display:block; margin-top:15px; color:#64748b; font-size:13px; text-decoration:none;"><i class="fa fa-times"></i> Cancel Edit & Add New</a>
            <?php endif; ?>
        </form>
    </div>

    <div class="table-container">
        <h3 style="font-weight:800; margin-bottom: 25px;">Live Inventory</h3>
        <table>
            <thead>
                <tr><th>Visual</th><th>Title</th><th>Category</th><th>Action</th></tr>
            </thead>
            <tbody>
                <?php
                $res = mysqli_query($conn, "SELECT * FROM expert_tips ORDER BY id DESC");
                while($row = mysqli_fetch_assoc($res)) {
                ?>
                <tr>
                    <td><img src="../<?php echo $row['thumbnail_image']; ?>" class="thumb-preview" onerror="this.src='../assets/images/placeholder.jpg'"></td>
                    <td><b style="color:#0f172a;"><?php echo $row['title']; ?></b></td>
                    <td><span style="color:var(--primary); font-weight:700;"><?php echo strtoupper($row['category']); ?></span></td>
                    <td>
                        <a href="?edit=<?php echo $row['id']; ?>" class="action-btn btn-edit"><i class="fa fa-edit"></i> Edit</a>
                        <a href="?delete=<?php echo $row['id']; ?>" class="action-btn btn-del" onclick="return confirm('Delete this insight?')"><i class="fa fa-trash"></i></a>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</main>

<script>
    // 3. SUCCESS POPUP LOGIC
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('status') === 'success') {
        const action = urlParams.get('action');
        Swal.fire({
            title: 'Success!',
            text: `Insight has been ${action} successfully.`,
            icon: 'success',
            confirmButtonColor: '#518992'
        }).then(() => {
            window.history.replaceState({}, document.title, window.location.pathname);
        });
    }
</script>

<?php include('includes/footer.php'); ?>