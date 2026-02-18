<?php
require_once '../db.php'; 
include('includes/header.php'); 
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

$msg_type = "";
$msg_text = "";

// --- 1. HANDLE NEW BANNER UPLOAD ---
if (isset($_POST['add_banner'])) {
    $target_dir = "../uploads/"; 
    if (!is_dir($target_dir)) { mkdir($target_dir, 0777, true); }

    $headline = mysqli_real_escape_string($conn, $_POST['headline']);
    $subheadline = mysqli_real_escape_string($conn, $_POST['subheadline']);
    $link = mysqli_real_escape_string($conn, $_POST['link_url']);
    $order = (int)$_POST['sort_order'];

    $image_name = basename($_FILES['banner_image']['name']);
    $unique_name = time() . "_" . $image_name; 
    $target_file = $target_dir . $unique_name;

    if (move_uploaded_file($_FILES['banner_image']['tmp_name'], $target_file)) {
        $sql = "INSERT INTO hero_banners (image_path, headline, subheadline, link_url, sort_order) 
                VALUES ('$unique_name', '$headline', '$subheadline', '$link', '$order')";
        mysqli_query($conn, $sql);
        $msg_type = "success";
        $msg_text = "Banner Kinetic Animation Synchronized!";
    } else {
        $msg_type = "error";
        $msg_text = "Transmission Failed: Check Folder Permissions.";
    }
}

// --- 2. HANDLE EDIT BANNER ---
if (isset($_POST['update_banner'])) {
    $id = (int)$_POST['banner_id'];
    $headline = mysqli_real_escape_string($conn, $_POST['headline']);
    $subheadline = mysqli_real_escape_string($conn, $_POST['subheadline']);
    $link = mysqli_real_escape_string($conn, $_POST['link_url']);
    $order = (int)$_POST['sort_order'];

    $update_sql = "UPDATE hero_banners SET headline='$headline', subheadline='$subheadline', link_url='$link', sort_order='$order' WHERE id=$id";
    
    if (!empty($_FILES['banner_image']['name'])) {
        $image_name = time() . "_" . basename($_FILES['banner_image']['name']);
        if (move_uploaded_file($_FILES['banner_image']['tmp_name'], "../uploads/" . $image_name)) {
            $update_sql = "UPDATE hero_banners SET image_path='$image_name', headline='$headline', subheadline='$subheadline', link_url='$link', sort_order='$order' WHERE id=$id";
        }
    }

    if (mysqli_query($conn, $update_sql)) {
        $msg_type = "success";
        $msg_text = "Visual Assets Re-compiled Successfully!";
    }
}

// --- 3. HANDLE DELETE ---
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    mysqli_query($conn, "DELETE FROM hero_banners WHERE id = $id");
    echo "<script>window.location='admin_manage_banners.php?deleted=1';</script>";
}

if(isset($_GET['deleted'])){
    $msg_type = "success";
    $msg_text = "Resource Purged from Database.";
}

$result = mysqli_query($conn, "SELECT * FROM hero_banners ORDER BY sort_order ASC");
?>

<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;700;800&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
    :root {
        --primary: #518992;
        --navy: #0f1c3f;
        --glass: rgba(255, 255, 255, 0.9);
        --glow: rgba(81, 137, 146, 0.4);
    }

    body { font-family: 'Plus Jakarta Sans', sans-serif; background: #f0f4f8; }

    .dashboard-container { padding: 40px; animation: slideIn 0.8s cubic-bezier(0.25, 1, 0.5, 1); }
    @keyframes slideIn { from { opacity: 0; transform: translateY(40px) scale(0.95); } to { opacity: 1; transform: translateY(0) scale(1); } }

    .premium-card {
        background: var(--glass);
        backdrop-filter: blur(15px);
        border-radius: 30px;
        border: 1px solid rgba(255,255,255,0.6);
        box-shadow: 0 20px 60px rgba(15, 28, 63, 0.08);
        padding: 35px;
        position: relative;
        overflow: hidden;
        transition: all 0.5s ease;
    }

    /* Floating Inputs */
    .form-group-custom { position: relative; margin-bottom: 25px; }
    .form-control {
        border-radius: 18px;
        padding: 15px 20px;
        border: 2px solid #eef2f6;
        background: white;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        font-weight: 600;
    }
    .form-control:focus {
        border-color: var(--primary);
        box-shadow: 0 0 25px var(--glow);
        transform: scale(1.02);
    }

    /* Kinetic Action Buttons */
    .btn-kinetic {
        background: linear-gradient(135deg, var(--navy) 0%, #1a3a5f 100%);
        color: white;
        border: none;
        border-radius: 20px;
        padding: 16px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 2px;
        transition: all 0.4s;
        box-shadow: 0 10px 20px rgba(15, 28, 63, 0.2);
    }
    .btn-kinetic:hover {
        transform: translateY(-5px) scale(1.02);
        box-shadow: 0 15px 30px var(--glow);
        background: var(--primary);
    }

    /* Table Styles */
    .pro-table { border-collapse: separate; border-spacing: 0 15px; width: 100%; }
    .pro-table tr { background: white; border-radius: 25px; transition: all 0.4s; }
    .pro-table tr:hover { transform: translateX(10px) scale(1.01); box-shadow: 0 10px 30px rgba(0,0,0,0.05); }
    .pro-table td { padding: 20px; border: none; vertical-align: middle; }
    .pro-table td:first-child { border-radius: 25px 0 0 25px; }
    .pro-table td:last-child { border-radius: 0 25px 25px 0; }

    .parallax-img-box { width: 130px; height: 75px; border-radius: 20px; overflow: hidden; position: relative; }
    .parallax-img-box img { width: 100%; height: 100%; object-fit: cover; transition: 0.8s; }
    .parallax-img-box:hover img { transform: scale(1.4) rotate(3deg); }

    .hologram-icon { width: 40px; height: 40px; display: inline-flex; align-items: center; justify-content: center; border-radius: 12px; margin: 0 5px; transition: 0.3s; cursor: pointer; }
    .h-edit { background: #e0f2fe; color: #0284c7; }
    .h-edit:hover { background: #0284c7; color: white; }
    .h-delete { background: #fee2e2; color: #dc2626; }
    .h-delete:hover { background: #dc2626; color: white; }
</style>

<div class="dashboard-container">
    <div class="row g-5">
        <div class="col-xl-4 col-lg-5">
            <div class="premium-card" id="form-section">
                <h4 class="fw-800 mb-4" id="form-title" style="color: var(--navy);">
                    <i class="fas fa-layer-group text-primary"></i> CREATIVE ENGINE
                </h4>
                
                <form method="POST" enctype="multipart/form-data" id="main-banner-form">
                    <input type="hidden" name="banner_id" id="banner_id">
                    
                    <div class="form-group-custom">
                        <label class="small fw-800 text-uppercase mb-2">Headline Alpha</label>
                        <input type="text" name="headline" id="edit_headline" class="form-control" placeholder="The big title..." required>
                    </div>

                    <div class="form-group-custom">
                        <label class="small fw-800 text-uppercase mb-2">Narrative Subtext</label>
                        <input type="text" name="subheadline" id="edit_subheadline" class="form-control" placeholder="Supporting details...">
                    </div>

                    <div class="form-group-custom">
                        <label class="small fw-800 text-uppercase mb-2">Source Visual</label>
                        <input type="file" name="banner_image" id="edit_image" class="form-control" required>
                        <div id="image-note" style="display:none;" class="mt-2 small fw-700 text-primary">
                            <i class="fas fa-sync-alt fa-spin"></i> Persistence Active: Leave empty to retain current visual.
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-7">
                            <label class="small fw-800 text-uppercase mb-2">Redirect Vector</label>
                            <input type="text" name="link_url" id="edit_link" class="form-control" value="#">
                        </div>
                        <div class="col-5">
                            <label class="small fw-800 text-uppercase mb-2">Priority Index</label>
                            <input type="number" name="sort_order" id="edit_order" class="form-control" value="0">
                        </div>
                    </div>

                    <button type="submit" name="add_banner" id="submit-btn" class="btn btn-kinetic w-100 mt-5">
                        Initialize Banner
                    </button>
                    
                    <button type="button" onclick="resetForm()" id="cancel-btn" class="btn btn-light w-100 mt-3" style="display:none; border-radius:18px; font-weight:800;">
                        ABORT EDIT
                    </button>
                </form>
            </div>
        </div>

        <div class="col-xl-8 col-lg-7">
            <div class="premium-card h-100">
                <h4 class="fw-800 mb-4" style="color: var(--navy);">
                    <i class="fas fa-tachometer-alt text-primary"></i> LIVE DEPLOYMENTS
                </h4>
                <div class="table-responsive">
                    <table class="pro-table">
                        <thead>
                            <tr class="text-uppercase small fw-800 text-muted">
                                <th>#</th>
                                <th>Visual Core</th>
                                <th>Metadata</th>
                                <th class="text-end">Command</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($row = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td><span class="fw-800 text-primary">0<?php echo $row['sort_order']; ?></span></td>
                                <td>
                                    <div class="parallax-img-box shadow-sm">
                                        <img src="../uploads/<?php echo $row['image_path']; ?>">
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-800 text-navy mb-1"><?php echo $row['headline']; ?></div>
                                    <div class="small fw-600 text-muted"><?php echo $row['subheadline']; ?></div>
                                </td>
                                <td class="text-end">
                                    <button class="hologram-icon h-edit border-0" 
                                            onclick="editAndScroll(<?php echo htmlspecialchars(json_encode($row)); ?>)">
                                        <i class="fas fa-sliders-h"></i>
                                    </button>
                                    <button class="hologram-icon h-delete border-0" 
                                            onclick="confirmDelete(<?php echo $row['id']; ?>)">
                                        <i class="fas fa-burn"></i>
                                    </button>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Logic to handle system popups
<?php if($msg_type != ""): ?>
Swal.fire({
    icon: '<?php echo $msg_type; ?>',
    title: 'SYSTEM NOTIFICATION',
    text: '<?php echo $msg_text; ?>',
    timer: 2500,
    showConfirmButton: false,
    toast: true,
    position: 'top-end'
});
<?php endif; ?>

function confirmDelete(id) {
    Swal.fire({
        title: 'PURGE DATA?',
        text: "This visual will be permanently erased.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#0f1c3f',
        cancelButtonColor: '#dc2626',
        confirmButtonText: 'PURGE NOW'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = 'admin_manage_banners.php?delete=' + id;
        }
    })
}

// THE KEY FUNCTION FOR AUTOMATIC SCROLL AND FILL
function editAndScroll(data) {
    // 1. SCROLL to the form section smoothly
    document.getElementById('form-section').scrollIntoView({
        behavior: 'smooth',
        block: 'start'
    });

    // 2. HIGHLIGHT form context
    const ctx = document.getElementById('form-section');
    ctx.style.boxShadow = "0 0 50px var(--glow)";
    ctx.style.borderColor = "var(--primary)";

    // 3. FILL the inputs
    document.getElementById('form-title').innerHTML = '<i class="fas fa-microchip text-success"></i> RE-PROGRAMMING';
    document.getElementById('banner_id').value = data.id;
    document.getElementById('edit_headline').value = data.headline;
    document.getElementById('edit_subheadline').value = data.subheadline;
    document.getElementById('edit_link').value = data.link_url;
    document.getElementById('edit_order').value = data.sort_order;
    
    // 4. CHANGE button state
    const btn = document.getElementById('submit-btn');
    btn.name = "update_banner";
    btn.innerText = "Execute Re-program";
    btn.style.background = "#10b981"; 
    
    document.getElementById('cancel-btn').style.display = "block";
    document.getElementById('edit_image').required = false;
    document.getElementById('image-note').style.display = "block";

    // 5. FOCUS on the first input field
    setTimeout(() => {
        document.getElementById('edit_headline').focus();
    }, 600);
}

function resetForm() {
    document.getElementById('main-banner-form').reset();
    document.getElementById('form-title').innerHTML = '<i class="fas fa-layer-group text-primary"></i> CREATIVE ENGINE';
    document.getElementById('banner_id').value = "";
    
    const btn = document.getElementById('submit-btn');
    btn.name = "add_banner";
    btn.innerText = "Initialize Banner";
    btn.style.background = "var(--navy)";
    
    document.getElementById('cancel-btn').style.display = "none";
    document.getElementById('image-note').style.display = "none";
    document.getElementById('edit_image').required = true;
    document.getElementById('form-section').style.boxShadow = "0 20px 60px rgba(15, 28, 63, 0.08)";
}
</script>

<?php include('includes/footer.php'); ?>