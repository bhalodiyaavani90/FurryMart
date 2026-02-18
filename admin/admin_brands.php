<?php
/**
 * FURRYMART ADMIN - BRAND SOVEREIGN COMMAND CENTER
 * Features: High-Density CRUD + Dual-Mode Form + Advanced Animation
 */
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['admin_id'])) { header("Location: login.php"); exit(); }
include '../db.php'; 

$is_edit = false;
$edit_data = ['id'=>'','brand_name'=>'','category'=>'','brand_logo'=>'','target_url'=>'','is_featured'=>0];

// --- 1. DELETE PROTOCOL ---
if(isset($_GET['delete'])) {
    $id = mysqli_real_escape_string($conn, $_GET['delete']);
    // Optional: Unlink file from server here
    mysqli_query($conn, "DELETE FROM brands WHERE id='$id'");
    header("Location: admin_brands.php?status=success&msg=purged"); exit();
}

// --- 2. EDIT FETCH PROTOCOL ---
if(isset($_GET['edit'])) {
    $is_edit = true;
    $id = mysqli_real_escape_string($conn, $_GET['edit']);
    $res = mysqli_query($conn, "SELECT * FROM brands WHERE id='$id'");
    $edit_data = mysqli_fetch_assoc($res);
}

// --- 3. SAVE / UPDATE PROTOCOL ---
if(isset($_POST['submit_brand'])) {
    $target_dir = "../uploads/brands/";
    if (!is_dir($target_dir)) { mkdir($target_dir, 0777, true); }

    $b_id   = mysqli_real_escape_string($conn, $_POST['brand_id']);
    $name   = mysqli_real_escape_string($conn, $_POST['brand_name']);
    $cat    = mysqli_real_escape_string($conn, $_POST['category']);
    $url    = mysqli_real_escape_string($conn, $_POST['target_url']);
    $feat   = isset($_POST['is_featured']) ? 1 : 0;
    
    // Debug: Log the category being saved
    error_log("Saving brand with category: " . $cat);
    
    // Handle Image Logic
    if(!empty($_FILES["brand_logo"]["name"])) {
        $logo_name = time() . "_" . basename($_FILES["brand_logo"]["name"]);
        move_uploaded_file($_FILES["brand_logo"]["tmp_name"], $target_dir . $logo_name);
    } else {
        $logo_name = isset($_POST['existing_logo']) ? $_POST['existing_logo'] : '';
    }

    if($b_id) {
        // UPDATE
        $sql = "UPDATE brands SET brand_name='$name', category='$cat', brand_logo='$logo_name', target_url='$url', is_featured='$feat' WHERE id='$b_id'";
        $act = "updated";
    } else {
        // INSERT - Check table structure and insert accordingly
        // First try with sort_order, if fails try without
        $sql = "INSERT INTO brands (brand_name, category, brand_logo, target_url, is_featured, status) 
                VALUES ('$name', '$cat', '$logo_name', '$url', '$feat', 'active')";
        $act = "published";
    }
    
    // Execute query with detailed error reporting
    if(mysqli_query($conn, $sql)) {
        header("Location: admin_brands.php?status=success&msg=$act"); exit();
    } else {
        $error = mysqli_error($conn);
        // Log the full query for debugging
        error_log("SQL Error: " . $error);
        error_log("Query: " . $sql);
        header("Location: admin_brands.php?status=error&msg=".urlencode($error)); exit();
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
        :root { --primary: #518992; --navy: #0f1c3f; --bg: #f8fafc; --white: #ffffff; --border: #eef2f6; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: var(--bg); color: var(--navy); }
        .admin-wrapper { padding: 40px 5%; max-width: 1400px; margin: auto; }
        
        /* Stats & Header */
        .dash-header { background: var(--navy); color: white; padding: 40px 50px; border-radius: 35px; display: flex; justify-content: space-between; align-items: center; margin-bottom: 40px; box-shadow: 0 20px 50px rgba(15,28,63,0.1); }
        .dash-header h2 { margin: 0; font-size: 32px; font-weight: 800; }

        /* Sovereign Form Architecture */
        .form-card { background: var(--white); padding: 45px; border-radius: 45px; border: 1.5px solid var(--border); box-shadow: 0 10px 40px rgba(0,0,0,0.02); margin-bottom: 50px; animation: slideUp 0.6s ease; }
        @keyframes slideUp { from { opacity:0; transform: translateY(30px); } to { opacity:1; transform: translateY(0); } }

        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 30px; margin-bottom: 25px; }
        .form-group label { display: block; font-weight: 800; font-size: 14px; margin-bottom: 10px; color: var(--navy); text-transform: uppercase; letter-spacing: 0.5px; }
        .form-group input, .form-group select { width: 100%; padding: 16px 20px; border-radius: 15px; border: 1.5px solid #eee; outline: none; transition: 0.3s; font-size: 14px; font-weight: 600; }
        .form-group input:focus { border-color: var(--primary); background: #f0fdfa; }

        /* Featured Toggle Hub */
        .featured-zone { background: #fffbeb; padding: 20px 30px; border-radius: 20px; border: 1px solid #fde68a; display: flex; align-items: center; gap: 15px; margin-bottom: 30px; }
        .featured-zone input[type="checkbox"] { width: 22px; height: 22px; accent-color: #fbbf24; cursor: pointer; }
        .featured-zone b { font-size: 14px; color: #92400e; font-weight: 800; }

        .btn-main { background: var(--primary); color: white; border: none; padding: 20px 50px; border-radius: 50px; font-weight: 800; cursor: pointer; transition: 0.4s; width: 100%; font-size: 14px; letter-spacing: 1px; }
        .btn-main:hover { background: var(--navy); transform: scale(1.02); }

        /* Clinical Grid Layout */
        .brand-inventory { display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 30px; }
        .brand-item-card { background: white; padding: 30px; border-radius: 35px; border: 1.5px solid var(--border); transition: 0.4s; position: relative; display: flex; align-items: center; gap: 20px; }
        .brand-item-card:hover { transform: translateY(-10px); border-color: var(--primary); box-shadow: 0 20px 40px rgba(15,28,63,0.06); }
        
        .brand-logo-preview { width: 70px; height: 70px; object-fit: contain; background: #f8fafc; border-radius: 15px; padding: 10px; }
        .brand-info h4 { margin: 0; font-size: 18px; font-weight: 800; color: var(--navy); }
        .brand-info span { font-size: 11px; font-weight: 800; color: var(--primary); text-transform: uppercase; background: #eff6ff; padding: 4px 10px; border-radius: 50px; }

        /* Action Trays */
        .action-tray { position: absolute; top: 20px; right: 20px; display: flex; gap: 8px; }
        .icon-btn { width: 35px; height: 35px; border-radius: 50%; display: flex; align-items: center; justify-content: center; text-decoration: none; font-size: 14px; transition: 0.3s; }
        .btn-edit { background: #eff6ff; color: #2563eb; }
        .btn-edit:hover { background: #2563eb; color: white; }
        .btn-del { background: #fee2e2; color: #ef4444; }
        .btn-del:hover { background: #ef4444; color: white; transform: rotate(90deg); }
    </style>
</head>
<body>

<main class="admin-wrapper">
    <div class="dash-header">
        <div>
            <h2>Brand Command Center</h2>
            <p style="opacity: 0.7; font-size: 14px; margin-top:5px;">Managing Clinical Retail Infrastructure</p>
        </div>
        <i class="fas fa-microchip" style="font-size: 45px; opacity: 0.2;"></i>
    </div>

    <div class="form-card">
        <h3 style="font-size:18px; font-weight:800; margin-bottom:30px;">
            <i class="fas <?php echo $is_edit ? 'fa-edit' : 'fa-plus-circle'; ?>"></i> 
            <?php echo $is_edit ? 'Modifying Partner: '.$edit_data['brand_name'] : 'Register New Clinical Brand'; ?>
        </h3>
        
        <form action="admin_brands.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="brand_id" value="<?php echo $edit_data['id']; ?>">
            <input type="hidden" name="existing_logo" value="<?php echo $edit_data['brand_logo']; ?>">

            <div class="form-grid">
                <div class="form-group">
                    <label>Brand Name</label>
                    <input type="text" name="brand_name" value="<?php echo $edit_data['brand_name']; ?>" placeholder="e.g. Royal Canin" required>
                </div>
                <div class="form-group">
                    <label>Market Category</label>
                    <select name="category" required>
                        <option value="">-- Select Category --</option>
                        <option value="Food" <?php if($edit_data['category']=='Food') echo 'selected'; ?>>Food Brands</option>
                        <option value="Grooming" <?php if($edit_data['category']=='Grooming') echo 'selected'; ?>>Grooming Brands</option>
                        <option value="Accessories" <?php if($edit_data['category']=='Accessories') echo 'selected'; ?>>Accessories Brands</option>
                        <option value="Clothing" <?php if($edit_data['category']=='Clothing') echo 'selected'; ?>>Clothing Brands</option>
                        <option value="Beds and Mats" <?php if($edit_data['category']=='Beds and Mats') echo 'selected'; ?>>Beds and Mats Brands</option>
                        <option value="Bowls" <?php if($edit_data['category']=='Bowls') echo 'selected'; ?>>Bowls Brands</option>
                        <option value="Litter" <?php if($edit_data['category']=='Litter') echo 'selected'; ?>>Litter Brands</option>
                    </select>
                </div>
            </div>
            
            <div class="featured-zone">
                <input type="checkbox" name="is_featured" id="fCheck" <?php echo ($edit_data['is_featured']==1) ? 'checked' : ''; ?>>
                <label for="fCheck"><b>Enable Gold VIP Highlight (Featured Partner)</b></label>
            </div>

            <div class="form-grid">
                <div class="form-group">
                    <label>Target URL (Internal Path)</label>
                    <input type="text" name="target_url" value="<?php echo $edit_data['target_url']; ?>" placeholder="e.g. brands/pedigree.php" required>
                </div>
                <div class="form-group">
                    <label>Brand Logo <?php if($is_edit) echo '<small>(Leave empty to keep current)</small>'; ?></label>
                    <input type="file" name="brand_logo" <?php echo $is_edit ? '' : 'required'; ?>>
                </div>
            </div>

            <div style="display:flex; gap:20px; align-items:center;">
                <button type="submit" name="submit_brand" class="btn-main">
                    <?php echo $is_edit ? 'EXECUTE UPDATE PROTOCOL' : 'PUBLISH TO RETAIL GRID'; ?>
                </button>
                <?php if($is_edit): ?> <a href="admin_brands.php" style="font-weight:800; color:#64748b; text-decoration:none; font-size:14px;">Cancel</a> <?php endif; ?>
            </div>
        </form>
    </div>

    <div class="brand-inventory">
        <?php
        $res = mysqli_query($conn, "SELECT * FROM brands ORDER BY id DESC");
        while($r = mysqli_fetch_assoc($res)) {
            $feat_badge = ($r['is_featured']) ? '<i class="fas fa-crown" style="color:#fbbf24; margin-left:8px;"></i>' : '';
            echo '
            <div class="brand-item-card">
                <div class="action-tray">
                    <a href="?edit='.$r['id'].'" class="icon-btn btn-edit"><i class="fas fa-pen"></i></a>
                    <a href="?delete='.$r['id'].'" class="icon-btn btn-del" onclick="return confirm(\'Purge this brand record?\')"><i class="fas fa-times"></i></a>
                </div>
                <img src="../uploads/brands/'.$r['brand_logo'].'" class="brand-logo-preview">
                <div class="brand-info">
                    <h4>'.$r['brand_name'].$feat_badge.'</h4>
                    <span>'.$r['category'].'</span>
                </div>
            </div>';
        }
        ?>
    </div>
</main>

<script>
    // SUCCESS POPUPS
    const params = new URLSearchParams(window.location.search);
    if(params.get('status') === 'success') {
        Swal.fire({
            title: 'Sovereign Success',
            text: `Brand has been ${params.get('msg')} correctly.`,
            icon: 'success',
            confirmButtonColor: '#518992',
            borderRadius: '30px'
        }).then(() => window.history.replaceState({}, '', 'admin_brands.php'));
    }
    if(params.get('status') === 'error') {
        Swal.fire({
            title: 'Error',
            text: `Database Error: ${params.get('msg')}`,
            icon: 'error',
            confirmButtonColor: '#ef4444',
            borderRadius: '30px'
        }).then(() => window.history.replaceState({}, '', 'admin_brands.php'));
    }
    
    // Form validation and debugging
    document.querySelector('form').addEventListener('submit', function(e) {
        const category = document.querySelector('select[name="category"]').value;
        const brandName = document.querySelector('input[name="brand_name"]').value;
        
        console.log('Form submitting with:');
        console.log('Brand Name:', brandName);
        console.log('Category:', category);
        
        if(!category || category === '') {
            e.preventDefault();
            Swal.fire({
                title: 'Missing Category',
                text: 'Please select a category for the brand',
                icon: 'warning',
                confirmButtonColor: '#518992'
            });
            return false;
        }
    });

</script>

<?php include('includes/footer.php'); ?>