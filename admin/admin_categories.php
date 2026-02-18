<?php
include "../db.php";
if (session_status() === PHP_SESSION_NONE) { session_start(); }

// --- 1. LOGIC: ADDING RECORDS ---
if(isset($_POST['add_main'])){
    $name = mysqli_real_escape_string($conn, $_POST['cat_name']);
    $slug = strtolower(str_replace(' ', '-', $name));
    mysqli_query($conn, "INSERT INTO main_categories (category_name, category_slug) VALUES ('$name', '$slug')");
    $_SESSION['alert'] = ['type' => 'success', 'msg' => 'Main Category Deployed!'];
    header("Location: admin_categories.php"); exit();
}

if(isset($_POST['add_sub'])){
    $main_id = $_POST['parent_id'];
    $name = mysqli_real_escape_string($conn, $_POST['sub_name']);
    $slug = strtolower(str_replace(' ', '-', $name));
    mysqli_query($conn, "INSERT INTO sub_categories (main_cat_id, sub_name, sub_slug) VALUES ('$main_id', '$name', '$slug')");
    $_SESSION['alert'] = ['type' => 'success', 'msg' => 'Sub-Protocol Synchronized!'];
    header("Location: admin_categories.php"); exit();
}

// --- 2. LOGIC: UPDATING RECORDS ---
if(isset($_POST['update_main'])){
    $id = $_POST['cat_id'];
    $name = mysqli_real_escape_string($conn, $_POST['cat_name']);
    $slug = strtolower(str_replace(' ', '-', $name));
    mysqli_query($conn, "UPDATE main_categories SET category_name='$name', category_slug='$slug' WHERE id='$id'");
    $_SESSION['alert'] = ['type' => 'info', 'msg' => 'Main Category Updated.'];
    header("Location: admin_categories.php"); exit();
}

if(isset($_POST['update_sub'])){
    $id = $_POST['sub_id'];
    $main_id = $_POST['parent_id'];
    $name = mysqli_real_escape_string($conn, $_POST['sub_name']);
    $slug = strtolower(str_replace(' ', '-', $name));
    mysqli_query($conn, "UPDATE sub_categories SET main_cat_id='$main_id', sub_name='$name', sub_slug='$slug' WHERE id='$id'");
    $_SESSION['alert'] = ['type' => 'info', 'msg' => 'Sub-Category Protocol Updated.'];
    header("Location: admin_categories.php"); exit();
}

// --- 3. LOGIC: DELETION ---
if(isset($_GET['delete_main'])){
    $id = (int)$_GET['delete_main'];
    mysqli_query($conn, "DELETE FROM main_categories WHERE id=$id");
    $_SESSION['alert'] = ['type' => 'error', 'msg' => 'Species Record Erased.'];
    header("Location: admin_categories.php"); exit();
}

if(isset($_GET['delete_sub'])){
    $id = (int)$_GET['delete_sub'];
    mysqli_query($conn, "DELETE FROM sub_categories WHERE id=$id");
    $_SESSION['alert'] = ['type' => 'error', 'msg' => 'Sub-Protocol Erased.'];
    header("Location: admin_categories.php"); exit();
}
?>

<?php include "includes/header.php"; ?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
    :root { --teal: #518992; --navy: #0f1c3f; --pink: #f87171; --bg: #f4f7f6; }
    body { background: var(--bg); font-family: 'Plus Jakarta Sans', sans-serif; }
    
    .admin-viewport { margin-left: 20px; padding: 60px 40px; display: grid; grid-template-columns: 1fr 1fr; gap: 40px; }

    .hub-card { background: #fff; border-radius: 35px; padding: 45px; box-shadow: 0 30px 60px rgba(15, 28, 63, 0.05); border: 1px solid #fff; position: relative; }
    .section-title { font-weight: 900; color: var(--navy); margin-bottom: 30px; display: flex; align-items: center; gap: 15px; }
    
    .form-control { width: 100%; padding: 16px; border-radius: 15px; border: 2px solid #f1f5f9; background: #fcfcfc; font-weight: 600; margin-bottom: 20px; outline: none; transition: 0.3s; }
    .form-control:focus { border-color: var(--teal); background: #fff; }
    
    .btn-deploy { width: 100%; padding: 18px; background: var(--navy); color: #fff; border-radius: 15px; border: none; font-weight: 800; cursor: pointer; transition: 0.3s; }
    .btn-deploy:hover { background: var(--teal); transform: translateY(-3px); }

    /* List Management */
    .data-list { margin-top: 35px; border-top: 2px solid #f8fafc; padding-top: 25px; }
    .list-item { display: flex; justify-content: space-between; align-items: center; padding: 15px; border-radius: 15px; background: #fafbfc; margin-bottom: 10px; border: 1px solid #f1f5f9; transition: 0.3s; }
    .list-item:hover { background: #fff; transform: scale(1.02); box-shadow: 0 10px 20px rgba(0,0,0,0.02); }
    
    .action-group { display: flex; gap: 10px; }
    .btn-action { width: 35px; height: 35px; border-radius: 10px; display: flex; align-items: center; justify-content: center; text-decoration: none; font-size: 14px; transition: 0.2s; border: none; cursor: pointer; }
    .btn-edit { background: #e0f2fe; color: #0284c7; }
    .btn-delete { background: #fee2e2; color: var(--pink); }
    .btn-action:hover { transform: scale(1.1); }

    /* Modal Styling */
    .modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(15, 28, 63, 0.5); backdrop-filter: blur(5px); display: none; z-index: 1000; justify-content: center; align-items: center; }
    .modal-content { background: #fff; padding: 40px; border-radius: 30px; width: 450px; box-shadow: 0 30px 70px rgba(0,0,0,0.2); }
</style>

<div class="admin-viewport">
    <div class="hub-card animate__animated animate__fadeInLeft">
        <h2 class="section-title"><i class="fas fa-paw" style="color:var(--teal);"></i> Species Protocol</h2>
        <form method="POST">
            <input type="text" name="cat_name" class="form-control" placeholder="New Species Name (e.g. Birds)" required>
            <button type="submit" name="add_main" class="btn-deploy">Deploy Species</button>
        </form>

        <div class="data-list">
            <?php 
            $m_res = mysqli_query($conn, "SELECT * FROM main_categories ORDER BY category_name ASC");
            while($m = mysqli_fetch_assoc($m_res)): ?>
                <div class="list-item">
                    <span style="font-weight:800; color:var(--navy);"><?php echo $m['category_name']; ?></span>
                    <div class="action-group">
                        <button class="btn-action btn-edit" onclick='openMainEdit(<?php echo json_encode($m); ?>)'><i class="fas fa-pen"></i></button>
                        <a href="?delete_main=<?php echo $m['id']; ?>" class="btn-action btn-delete" onclick="return confirm('Erase species protocol?')"><i class="fas fa-trash"></i></a>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>

    <div class="hub-card animate__animated animate__fadeInRight">
        <h2 class="section-title"><i class="fas fa-tags" style="color:var(--teal);"></i> Sub-Category Intelligence</h2>
        <form method="POST">
            <select name="parent_id" class="form-control">
                <option value="">Select Parent Species</option>
                <?php 
                $m_res2 = mysqli_query($conn, "SELECT * FROM main_categories");
                while($m2 = mysqli_fetch_assoc($m_res2)) echo "<option value='{$m2['id']}'>{$m2['category_name']}</option>";
                ?>
            </select>
            <input type="text" name="sub_name" class="form-control" placeholder="New Sub-Category (e.g. Toys)" required>
            <button type="submit" name="add_sub" class="btn-deploy">Deploy Sub-Protocol</button>
        </form>

        <div class="data-list">
            <?php 
            $s_res = mysqli_query($conn, "SELECT s.*, m.category_name FROM sub_categories s JOIN main_categories m ON s.main_cat_id = m.id ORDER BY s.id DESC");
            while($s = mysqli_fetch_assoc($s_res)): ?>
                <div class="list-item">
                    <div>
                        <div style="font-weight:800; color:var(--navy);"><?php echo $s['sub_name']; ?></div>
                        <div style="font-size:10px; color:var(--teal); font-weight:800; text-transform:uppercase;"><?php echo $s['category_name']; ?></div>
                    </div>
                    <div class="action-group">
                        <button class="btn-action btn-edit" onclick='openSubEdit(<?php echo json_encode($s); ?>)'><i class="fas fa-edit"></i></button>
                        <a href="?delete_sub=<?php echo $s['id']; ?>" class="btn-action btn-delete" onclick="return confirm('Terminate sub-protocol?')"><i class="fas fa-trash-alt"></i></a>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</div>

<div id="mainModal" class="modal-overlay">
    <div class="modal-content animate__animated animate__zoomIn">
        <h3 style="margin-bottom:20px; font-weight:900;">Update Species</h3>
        <form method="POST">
            <input type="hidden" name="cat_id" id="edit_m_id">
            <input type="text" name="cat_name" id="edit_m_name" class="form-control" required>
            <button type="submit" name="update_main" class="btn-deploy">Save Changes</button>
            <button type="button" onclick="closeModals()" style="background:none; border:none; color:#94a3b8; font-weight:800; width:100%; margin-top:15px; cursor:pointer;">CANCEL</button>
        </form>
    </div>
</div>

<div id="subModal" class="modal-overlay">
    <div class="modal-content animate__animated animate__zoomIn">
        <h3 style="margin-bottom:20px; font-weight:900;">Update Sub-Protocol</h3>
        <form method="POST">
            <input type="hidden" name="sub_id" id="edit_s_id">
            <label style="font-size:10px; font-weight:800; color:#94a3b8;">PARENT SPECIES</label>
            <select name="parent_id" id="edit_s_parent" class="form-control">
                <?php 
                mysqli_data_seek($m_res2, 0); // Reset pointer
                while($m2 = mysqli_fetch_assoc($m_res2)) echo "<option value='{$m2['id']}'>{$m2['category_name']}</option>";
                ?>
            </select>
            <label style="font-size:10px; font-weight:800; color:#94a3b8;">SUB-CATEGORY NAME</label>
            <input type="text" name="sub_name" id="edit_s_name" class="form-control" required>
            <button type="submit" name="update_sub" class="btn-deploy">Sync Changes</button>
            <button type="button" onclick="closeModals()" style="background:none; border:none; color:#94a3b8; font-weight:800; width:100%; margin-top:15px; cursor:pointer;">CANCEL</button>
        </form>
    </div>
</div>

<script>
// Modal Controls
function openMainEdit(data) {
    document.getElementById('mainModal').style.display = 'flex';
    document.getElementById('edit_m_id').value = data.id;
    document.getElementById('edit_m_name').value = data.category_name;
}

function openSubEdit(data) {
    document.getElementById('subModal').style.display = 'flex';
    document.getElementById('edit_s_id').value = data.id;
    document.getElementById('edit_s_name').value = data.sub_name;
    document.getElementById('edit_s_parent').value = data.main_cat_id;
}

function closeModals() {
    document.querySelectorAll('.modal-overlay').forEach(m => m.style.display = 'none');
}

// SweetAlert Feedbacks
<?php if(isset($_SESSION['alert'])): ?>
    Swal.fire({
        icon: '<?php echo $_SESSION['alert']['type']; ?>',
        title: '<?php echo $_SESSION['alert']['msg']; ?>',
        showConfirmButton: false, timer: 2000, background: '#fff', color: '#0f1c3f'
    });
<?php unset($_SESSION['alert']); endif; ?>
</script>

<?php include "includes/footer.php"; ?>