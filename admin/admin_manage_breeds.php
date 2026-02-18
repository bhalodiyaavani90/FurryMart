<?php


require_once '../db.php'; 


include('includes/header.php');
 


$msg_type = ""; $msg_text = "";

// --- 1. HANDLE NEW BREED ADDITION ---
if (isset($_POST['add_breed'])) {
    $target_dir = "../uploads/breeds/"; 
    if (!is_dir($target_dir)) { mkdir($target_dir, 0777, true); }

    $name = mysqli_real_escape_string($conn, $_POST['breed_name']);
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    $url = mysqli_real_escape_string($conn, $_POST['details_page_url']);
    $order = (int)$_POST['sort_order'];

    $image_name = time() . "_" . basename($_FILES['breed_image']['name']);
    if (move_uploaded_file($_FILES['breed_image']['tmp_name'], $target_dir . $image_name)) {
        $sql = "INSERT INTO breed_picks (breed_name, category, image_path, details_page_url, sort_order) 
                VALUES ('$name', '$category', '$image_name', '$url', '$order')";
        mysqli_query($conn, $sql);
        $msg_type = "success"; $msg_text = "Breed Digital Asset Synchronized!";
    }
}

// --- 2. HANDLE EDIT ---
if (isset($_POST['update_breed'])) {
    $id = (int)$_POST['breed_id'];
    $name = mysqli_real_escape_string($conn, $_POST['breed_name']);
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    $url = mysqli_real_escape_string($conn, $_POST['details_page_url']);
    $order = (int)$_POST['sort_order'];

    $update_sql = "UPDATE breed_picks SET breed_name='$name', category='$category', details_page_url='$url', sort_order='$order' WHERE id=$id";
    
    if (!empty($_FILES['breed_image']['name'])) {
        $image_name = time() . "_" . basename($_FILES['breed_image']['name']);
        if (move_uploaded_file($_FILES['breed_image']['tmp_name'], "../uploads/breeds/" . $image_name)) {
            $update_sql = "UPDATE breed_picks SET image_path='$image_name', breed_name='$name', category='$category', details_page_url='$url', sort_order='$order' WHERE id=$id";
        }
    }
    if (mysqli_query($conn, $update_sql)) {
        $msg_type = "success"; $msg_text = "Breed Matrix Re-Compiled Successfully!";
    }
}

// --- 3. HANDLE DELETE ---
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    mysqli_query($conn, "DELETE FROM breed_picks WHERE id = $id");
    echo "<script>window.location='admin_manage_breeds.php?deleted=1';</script>";
}
if(isset($_GET['deleted'])){ $msg_type = "success"; $msg_text = "Resource Purged from Registry."; }

$result = mysqli_query($conn, "SELECT * FROM breed_picks ORDER BY category, sort_order ASC");
?>

<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
    :root { --primary: #518992; --navy: #0f1c3f; --glass: rgba(255, 255, 255, 0.95); --glow: rgba(81, 137, 146, 0.3); }
    body { font-family: 'Plus Jakarta Sans', sans-serif; background: #f4f7fa; color: var(--navy); }

    .dashboard-container { padding: 40px; animation: fadeInUp 0.8s cubic-bezier(0.2, 1, 0.2, 1); }
    @keyframes fadeInUp { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }

    /* PROFESSIONAL HORIZONTAL GRID FORM STRUCTURE */
    .premium-card {
        background: var(--glass);
        backdrop-filter: blur(25px);
        border-radius: 35px;
        border: 2px solid #ffffff;
        box-shadow: 0 25px 50px -12px rgba(15, 28, 63, 0.1);
        padding: 45px;
        margin-bottom: 40px;
        transition: all 0.4s ease;
    }

    /* Strict 3-Column Grid for perfect field alignment */
    .breed-form-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr); 
        gap: 35px; /* Increased gap to prevent fields joining */
        align-items: end;
    }

    .form-group-custom { margin-bottom: 0; display: flex; flex-direction: column; }
    .label-alpha { font-size: 0.72rem; font-weight: 800; text-transform: uppercase; letter-spacing: 2px; color: #64748b; display: block; margin-bottom: 12px; }

    .form-control-kinetic {
        border-radius: 18px;
        padding: 15px 22px;
        border: 2px solid #eef2f6;
        background: #fdfdfe;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        font-weight: 600;
        width: 100%;
        outline: none;
    }
    .form-control-kinetic:focus { border-color: var(--primary); box-shadow: 0 10px 30px var(--glow); transform: translateY(-3px); }

    /* KINETIC BUTTON DESIGN - Separated by margin */
    .btn-deploy {
        background: var(--navy); color: white; border: none; border-radius: 20px; padding: 20px;
        font-weight: 800; text-transform: uppercase; letter-spacing: 2px;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        box-shadow: 0 15px 30px rgba(15, 28, 63, 0.2);
        width: 100%;
        cursor: pointer;
    }
    .btn-deploy:hover { transform: translateY(-5px) scale(1.01); box-shadow: 0 20px 40px var(--glow); background: var(--primary); }

    /* STAGGERED DATA REGISTRY */
    .pro-table { border-collapse: separate; border-spacing: 0 15px; width: 100%; }
    .pro-table tr { background: white; border-radius: 25px; transition: all 0.4s; animation: slideLeft 0.5s ease backwards; }
    @keyframes slideLeft { from { opacity: 0; transform: translateX(30px); } to { opacity: 1; transform: translateX(0); } }
    <?php for($i=1; $i<=20; $i++) { echo ".pro-table tr:nth-child($i) { animation-delay: ".($i * 0.08)."s; }"; } ?>

    .pro-table td { padding: 25px; border: none; vertical-align: middle; }
    .pro-table td:first-child { border-radius: 25px 0 0 25px; padding-left: 30px; }
    .pro-table td:last-child { border-radius: 0 25px 25px 0; padding-right: 30px; }

    /* Ken Burns Zoom Effect for database visual cores */
    .visual-core { width: 95px; height: 95px; border-radius: 50%; overflow: hidden; border: 4px solid #fff; box-shadow: 0 8px 20px rgba(0,0,0,0.1); }
    .visual-core img { width: 100%; height: 100%; object-fit: cover; transition: 0.8s ease; }
    .pro-table tr:hover .visual-core img { transform: scale(1.4) rotate(3deg); }

    .cmd-icon { width: 44px; height: 44px; display: inline-flex; align-items: center; justify-content: center; border-radius: 15px; margin: 0 5px; transition: 0.3s; border: none; cursor: pointer; }
    .h-edit { background: #eff6ff; color: #3b82f6; }
    .h-edit:hover { background: #3b82f6; color: white; transform: rotate(-10deg) scale(1.1); }
    .h-delete { background: #fff1f2; color: #f43f5e; }
    .h-delete:hover { background: #f43f5e; color: white; transform: rotate(10deg) scale(1.1); }

    .action-container { margin-top: 45px; display: flex; gap: 20px; }
</style>

<div class="dashboard-container">
    <div class="premium-card" id="form-context">
        <div class="d-flex align-items-center mb-5">
            <div style="width: 50px; height: 50px; background: var(--navy); border-radius: 15px; display: flex; align-items: center; justify-content: center; color: white; box-shadow: 0 10px 20px rgba(0,0,0,0.1);">
                <i class="fas fa-fingerprint"></i>
            </div>
            <h4 class="fw-800 m-0 ms-3" id="form-title">BREED ENGINE COMMAND</h4>
        </div>
        
        <form method="POST" enctype="multipart/form-data" id="breed-form">
            <input type="hidden" name="breed_id" id="breed_id">
            
            <div class="breed-form-grid">
                <div class="form-group-custom">
                    <label class="label-alpha">Breed Identity</label>
                    <input type="text" name="breed_name" id="edit_breed_name" class="form-control-kinetic" placeholder="e.g. Persian Cat" required>
                </div>

                <div class="form-group-custom">
                    <label class="label-alpha">Species Group</label>
                    <select name="category" id="edit_category" class="form-control-kinetic">
                        <option value="Dog">Dog</option>
                        <option value="Cat">Cat</option>
                        <option value="Bird">Bird</option>
                    </select>
                </div>

                <div class="form-group-custom">
                    <label class="label-alpha">Priority Index</label>
                    <input type="number" name="sort_order" id="edit_sort_order" class="form-control-kinetic" value="0">
                </div>

                <div class="form-group-custom" style="grid-column: span 2;">
                    <label class="label-alpha">Profile Vector (Page URL)</label>
                    <input type="text" name="details_page_url" id="edit_details_page_url" class="form-control-kinetic" placeholder="e.g. pages/persian.php" required>
                </div>

                <div class="form-group-custom">
                    <label class="label-alpha">Source Visual</label>
                    <input type="file" name="breed_image" id="edit_image" class="form-control-kinetic" required>
                </div>
            </div>

            <div id="image-note" style="display:none;" class="mt-4 small fw-800 text-primary">
                <i class="fas fa-sync-alt fa-spin"></i> PERSISTENCE ACTIVE: Existing visual file remains if field is empty.
            </div>

            <div class="action-container">
                <button type="submit" name="add_breed" id="submit-btn" class="btn-deploy">Initialize Deployment</button>
                <button type="button" onclick="resetForm()" id="cancel-btn" class="btn btn-light" style="display:none; border-radius:20px; font-weight:800; padding: 0 40px; color: #94a3b8; border: 2px solid #eef2f6;">Abort Command</button>
            </div>
        </form>
    </div>

    <div class="premium-card">
        <div class="d-flex justify-content-between align-items-center mb-5">
            <h4 class="fw-800 m-0"><i class="fas fa-layer-group text-primary me-2"></i> DATABASE REGISTRY</h4>
            <span class="badge bg-white text-navy px-4 py-2 border-radius-15 shadow-sm fw-800 border" style="font-size: 0.75rem;"><?php echo mysqli_num_rows($result); ?> BREEDS LOGGED</span>
        </div>
        
        <div class="table-responsive">
            <table class="pro-table">
                <thead>
                    <tr class="label-alpha">
                        <th style="padding-left: 30px;">Visual Core</th>
                        <th>Identity</th>
                        <th>Species</th>
                        <th class="text-end" style="padding-right: 30px;">Command</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td style="padding-left: 30px;"><div class="visual-core"><img src="../uploads/breeds/<?php echo $row['image_path']; ?>"></div></td>
                        <td>
                            <div class="fw-800 text-navy mb-1" style="font-size: 1.1rem;"><?php echo $row['breed_name']; ?></div>
                            <div class="small fw-600 text-muted">Index Rank #<?php echo $row['sort_order']; ?></div>
                        </td>
                        <td><span class="badge bg-light text-navy px-4 py-2 border-radius-10 fw-800"><?php echo $row['category']; ?></span></td>
                        <td class="text-end" style="padding-right: 30px;">
                            <button class="cmd-icon h-edit shadow-sm" onclick="editAndScroll(<?php echo htmlspecialchars(json_encode($row)); ?>)"><i class="fas fa-sliders-h"></i></button>
                            <button class="cmd-icon h-delete shadow-sm" onclick="confirmDelete(<?php echo $row['id']; ?>)"><i class="fas fa-trash-alt"></i></button>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
<?php if($msg_type != ""): ?>
Swal.fire({ icon: '<?php echo $msg_type; ?>', title: 'SYSTEM LOG', text: '<?php echo $msg_text; ?>', timer: 2500, showConfirmButton: false, toast: true, position: 'top-end', background: '#fff', color: '#0f1c3f' });
<?php endif; ?>

function confirmDelete(id) {
    Swal.fire({ title: 'PURGE DATA?', text: "Asset will be permanently erased.", icon: 'warning', showCancelButton: true, confirmButtonColor: '#0f1c3f', confirmButtonText: 'PURGE NOW' }).then((r) => { if (r.isConfirmed) window.location.href = 'admin_manage_breeds.php?delete=' + id; });
}

// THE KEY SCROLL & FILL LOGIC
function editAndScroll(data) {
    document.getElementById('form-context').scrollIntoView({ behavior: 'smooth', block: 'start' });
    const card = document.getElementById('form-context');
    card.style.borderColor = "var(--primary)";
    card.style.boxShadow = "0 30px 60px -12px rgba(81, 137, 146, 0.4)";

    document.getElementById('form-title').innerHTML = 'RE-PROGRAMMING';
    document.getElementById('breed_id').value = data.id;
    document.getElementById('edit_breed_name').value = data.breed_name;
    document.getElementById('edit_category').value = data.category;
    document.getElementById('edit_details_page_url').value = data.details_page_url;
    document.getElementById('edit_sort_order').value = data.sort_order;
    
    const btn = document.getElementById('submit-btn');
    btn.name = "update_breed";
    btn.innerText = "COMMIT UPDATES";
    btn.style.background = "#10b981"; 
    
    document.getElementById('cancel-btn').style.display = "block";
    document.getElementById('edit_image').required = false;
    document.getElementById('image-note').style.display = "block";
    
    setTimeout(() => { document.getElementById('edit_breed_name').focus(); }, 600);
}

function resetForm() {
    document.getElementById('breed-form').reset();
    document.getElementById('form-title').innerHTML = 'BREED ENGINE COMMAND';
    document.getElementById('breed_id').value = "";
    const btn = document.getElementById('submit-btn');
    btn.name = "add_breed"; btn.innerText = "Initialize Deployment"; btn.style.background = "var(--navy)";
    document.getElementById('cancel-btn').style.display = "none";
    document.getElementById('image-note').style.display = "none";
    document.getElementById('edit_image').required = true;
    document.getElementById('form-context').style.borderColor = "#ffffff";
    document.getElementById('form-context').style.boxShadow = "0 25px 50px -12px rgba(15, 28, 63, 0.1)";
}
</script>

<?php include('includes/footer.php'); ?>