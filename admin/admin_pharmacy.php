<?php
include "../db.php";
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['admin_id'])) { header("Location: login.php"); exit(); }
$success_msg = "";

// --- 1. CREATE (ADD MEDICINE) ---
if(isset($_POST['add_medicine'])){
    $name  = mysqli_real_escape_string($conn, $_POST['p_name']);
    $brand = mysqli_real_escape_string($conn, $_POST['brand']);
    $size  = mysqli_real_escape_string($conn, $_POST['size_options']);
    $cat   = mysqli_real_escape_string($conn, $_POST['category']);
    $price = mysqli_real_escape_string($conn, $_POST['price']);
    
    $img_name = time() . "_" . $_FILES['p_image']['name'];
    $target   = "../uploads/products/" . $img_name;
    
    if(move_uploaded_file($_FILES['p_image']['tmp_name'], $target)) {
        $q = "INSERT INTO pharmacy_products (product_name, brand_name, category, price, image, size_options) 
              VALUES ('$name', '$brand', '$cat', '$price', '$img_name', '$size')";
        if(mysqli_query($conn, $q)) {
            $success_msg = "Medicine protocol deployed successfully!";
        }
    }
}

// --- 2. UPDATE (EDIT MEDICINE) ---
if(isset($_POST['update_medicine'])){
    $id    = $_POST['edit_id'];
    $name  = mysqli_real_escape_string($conn, $_POST['p_name']);
    $brand = mysqli_real_escape_string($conn, $_POST['brand']);
    $size  = mysqli_real_escape_string($conn, $_POST['size_options']);
    $cat   = mysqli_real_escape_string($conn, $_POST['category']);
    $price = mysqli_real_escape_string($conn, $_POST['price']);

    $q = "UPDATE pharmacy_products SET product_name='$name', brand_name='$brand', category='$cat', price='$price', size_options='$size' WHERE id='$id'";
    if(mysqli_query($conn, $q)) {
        $success_msg = "Intelligence updated for Protocol ID: $id";
    }
}

// --- 3. DELETE (TERMINATION) ---
if(isset($_GET['delete'])){
    $id = (int)$_GET['delete'];
    mysqli_query($conn, "DELETE FROM pharmacy_products WHERE id=$id");
    header("Location: admin_pharmacy.php?status=terminated");
    exit();
}
?>

<?php include "includes/header.php"; ?>

<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<style>
    :root {
        --primary: #518992; --navy: #0f1c3f; --accent: #f87171; --bg: #f4f7f6; --white: #ffffff;
    }

    body { background: var(--bg); font-family: 'Plus Jakarta Sans', sans-serif; }

    /* Admin Viewport - Balanced Offset for Sidebar */
    .admin-viewport { 
        margin-left: 20px; /* Reduced to pull content slightly left */
        padding: 60px 40px; 
        display: flex; 
        flex-direction: column; 
        align-items: center; 
        gap: 50px; 
        min-height: 100vh;
    }

    /* Centered Glass Cards with specific width */
    .hub-card {
        background: var(--white); 
        border-radius: 40px; 
        padding: 50px; 
        width: 100%; 
        max-width: 900px; /* Perfect width for centered form */
        box-shadow: 0 30px 80px rgba(15, 28, 63, 0.05); 
        border: 1px solid rgba(255,255,255,0.8);
        box-sizing: border-box;
    }

    .section-header { text-align: center; margin-bottom: 40px; }
    .section-header h2 { font-weight: 800; color: var(--navy); font-size: 30px; letter-spacing: -1px; }

    /* Deployment Form Grid */
    .deployment-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 25px; }
    .input-protocol { display: flex; flex-direction: column; }
    .input-protocol label { font-size: 11px; font-weight: 800; color: #94a3b8; text-transform: uppercase; margin-bottom: 10px; letter-spacing: 1px; }
    .input-protocol input, .input-protocol select { 
        padding: 16px; border-radius: 18px; border: 2px solid #f1f5f9; font-weight: 600; font-size: 14px; transition: 0.3s;
    }
    .input-protocol input:focus { border-color: var(--primary); outline: none; background: #fff; box-shadow: 0 0 0 4px rgba(81, 137, 146, 0.05); }

    /* Live Preview Section */
    .preview-box { 
        grid-column: span 2; border: 2px dashed #e2e8f0; border-radius: 20px; 
        padding: 20px; text-align: center; background: #fafbfc; 
    }
    #livePreviewImg { max-height: 140px; border-radius: 15px; margin-top: 10px; display: none; box-shadow: 0 10px 20px rgba(0,0,0,0.1); }

    .btn-sync { 
        grid-column: span 2; padding: 22px; background: var(--navy); color: #fff; border: none;
        border-radius: 20px; font-weight: 800; text-transform: uppercase; cursor: pointer; 
        transition: 0.4s; margin-top: 10px; letter-spacing: 2px;
    }
    .btn-sync:hover { background: var(--primary); transform: translateY(-3px); box-shadow: 0 15px 30px rgba(81, 137, 146, 0.3); }

    /* Table Intelligence (Below the Form) */
    .inventory-table { width: 100%; border-collapse: separate; border-spacing: 0 15px; }
    .inventory-table th { padding: 15px; text-align: left; font-size: 11px; color: #94a3b8; text-transform: uppercase; }
    .med-row { background: #fff; border-radius: 25px; transition: 0.3s; }
    .med-row:hover { transform: scale(1.01); box-shadow: 0 15px 40px rgba(0,0,0,0.04); }
    .med-row td { padding: 20px 15px; vertical-align: middle; border-top: 1px solid #f8fafc; border-bottom: 1px solid #f8fafc; }
    .med-row td:first-child { border-left: 1px solid #f8fafc; border-radius: 25px 0 0 25px; }
    .med-row td:last-child { border-right: 1px solid #f8fafc; border-radius: 0 25px 25px 0; }

    .img-thumb { width: 55px; height: 55px; border-radius: 15px; object-fit: cover; background: #f1f5f9; }
    .action-btn { width: 40px; height: 40px; border-radius: 12px; border: none; cursor: pointer; transition: 0.3s; display: inline-flex; align-items: center; justify-content: center; text-decoration: none; font-size: 14px; }
    .btn-edit { background: #e0f2fe; color: #0284c7; margin-right: 10px; }
    .btn-delete { background: #fee2e2; color: #dc2626; }
    .action-btn:hover { transform: translateY(-2px) scale(1.05); }

    /* Modal Styling for EDIT */
    .modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(15, 28, 63, 0.4); display: none; z-index: 1000; justify-content: center; align-items: center; backdrop-filter: blur(5px); }
    .modal-content { background: #fff; padding: 45px; border-radius: 40px; width: 550px; box-shadow: 0 40px 100px rgba(0,0,0,0.3); }

    @media (max-width: 1100px) { .admin-viewport { margin-left: 0; } }
</style>

<div class="admin-viewport">
    
    <div class="hub-card animate__animated animate__fadeInDown">
        <div class="section-header">
            <i class="fas fa-pills fa-3x" style="color:var(--primary); margin-bottom:15px; display: block;"></i>
            <h2>Deployment Command</h2>
            <p style="color:#94a3b8; font-weight:600;">Centralize Pharmacy Inventory Intelligence</p>
        </div>

        <?php if($success_msg): ?>
            <div class="animate__animated animate__bounceIn" style="background:#ecfdf5; color:#059669; padding:20px; border-radius:20px; margin-bottom:30px; font-weight:700; text-align:center; border-left: 6px solid #059669;">
                <i class="fas fa-check-circle"></i> <?php echo $success_msg; ?>
            </div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data" class="deployment-grid">
            <div class="input-protocol">
                <label>Medicine Name</label>
                <input type="text" name="p_name" required placeholder="e.g. Himalaya Digyton Plus">
            </div>
            <div class="input-protocol">
                <label>Pharmaceutical Brand</label>
                <input type="text" name="brand" required placeholder="e.g. Himalaya Pet Care">
            </div>
            <div class="input-protocol">
                <label>Size / Packaging</label>
                <input type="text" name="size_options" required placeholder="e.g. 100ml / 60 Tablets">
            </div>
            <div class="input-protocol">
                <label>Clinical Category</label>
                <select name="category">
                    <option value="Supplements">Supplements & Vitamins</option>
                    <option value="Eye & Ear">Eye & Ear Care</option>
                    <option value="Digestive">Digestive Support</option>
                    <option value="Skin & Coat">Skin & Coat Clinical</option>
                </select>
            </div>
            <div class="input-protocol">
                <label>Market Price (₹)</label>
                <input type="number" name="price" required placeholder="0.00" step="0.01">
            </div>
            <div class="input-protocol">
                <label>Inventory Visual</label>
                <input type="file" name="p_image" id="p_image" required onchange="previewImage(this)" style="font-size: 12px; padding: 12px;">
            </div>
            
            <div class="preview-box">
                <label style="font-size:11px; font-weight:800; color:#94a3b8; display: block; margin-bottom: 10px;">LIVE VISUAL PREVIEW</label>
                <img id="livePreviewImg" src="" alt="Preview">
            </div>

            <button type="submit" name="add_medicine" class="btn-sync">
                <i class="fas fa-cloud-upload-alt"></i> Initialize Protocol Deployment
            </button>
        </form>
    </div>

    <div class="hub-card animate__animated animate__fadeInUp">
        <h3 style="margin-bottom:30px; color:var(--navy); font-weight: 800; font-size: 24px;"><i class="fas fa-database" style="color: var(--primary);"></i> Live Intelligence Repository</h3>
        <table class="inventory-table">
            <thead>
                <tr>
                    <th>Media</th>
                    <th>Product Data</th>
                    <th>Price</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $medicines = mysqli_query($conn, "SELECT * FROM pharmacy_products ORDER BY id DESC");
                while($row = mysqli_fetch_assoc($medicines)): 
                ?>
                <tr class="med-row">
                    <td><img src="../uploads/products/<?php echo $row['image']; ?>" class="img-thumb"></td>
                    <td>
                        <div style="font-weight:900; color:var(--navy); font-size: 15px;"><?php echo $row['product_name']; ?></div>
                        <div style="font-size:11px; color:#94a3b8; font-weight:700; margin-top: 4px;">
                            <?php echo $row['brand_name']; ?> | <span style="color:var(--primary);"><?php echo $row['size_options']; ?></span>
                        </div>
                    </td>
                    <td style="font-weight:900; color:var(--navy); font-size: 16px;">₹<?php echo number_format($row['price'], 2); ?></td>
                    <td>
                        <div style="display: flex;">
                            <button class="action-btn btn-edit" onclick='openEditModal(<?php echo json_encode($row); ?>)'>
                                <i class="fas fa-edit"></i>
                            </button>
                            <a href="?delete=<?php echo $row['id']; ?>" class="action-btn btn-delete" onclick="return confirm('Terminate record protocol?')">
                                <i class="fas fa-trash-alt"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<div id="editModal" class="modal-overlay">
    <div class="modal-content animate__animated animate__zoomIn">
        <h3 style="margin-bottom:30px; font-weight:800; color:var(--navy); font-size: 24px;">Update Intelligence</h3>
        <form method="POST">
            <input type="hidden" name="edit_id" id="edit_id">
            <div class="deployment-grid" style="grid-template-columns: 1fr;">
                <div class="input-protocol"><label>Medicine Name</label><input type="text" name="p_name" id="edit_name"></div>
                <div class="input-protocol"><label>Brand</label><input type="text" name="brand" id="edit_brand"></div>
                <div class="input-protocol"><label>Packaging Size</label><input type="text" name="size_options" id="edit_size"></div>
                <div class="input-protocol">
                    <label>Category</label>
                    <select name="category" id="edit_cat">
                        <option value="Supplements">Supplements & Vitamins</option>
                        <option value="Eye & Ear">Eye & Ear Care</option>
                        <option value="Digestive">Digestive Support</option>
                        <option value="Skin & Coat">Skin & Coat Clinical</option>
                    </select>
                </div>
                <div class="input-protocol"><label>Market Price (₹)</label><input type="number" name="price" id="edit_price" step="0.01"></div>
                
                <button type="submit" name="update_medicine" class="btn-sync" style="margin-top: 20px;">
                    <i class="fas fa-sync-alt"></i> Synchronize Intelligence
                </button>
                <button type="button" onclick="closeModal()" style="background:none; border:none; color:#94a3b8; font-weight:800; cursor:pointer; margin-top:15px; font-size: 12px; letter-spacing: 1px;">TERMINATE UPDATE</button>
            </div>
        </form>
    </div>
</div>

<script>
// Image Preview Logic
function previewImage(input) {
    const preview = document.getElementById('livePreviewImg');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.style.display = 'inline-block';
        }
        reader.readAsDataURL(input.files[0]);
    }
}

// Modal Logic
function openEditModal(data) {
    document.getElementById('editModal').style.display = 'flex';
    document.getElementById('edit_id').value = data.id;
    document.getElementById('edit_name').value = data.product_name;
    document.getElementById('edit_brand').value = data.brand_name;
    document.getElementById('edit_size').value = data.size_options;
    document.getElementById('edit_price').value = data.price;
    document.getElementById('edit_cat').value = data.category;
}

function closeModal() { document.getElementById('editModal').style.display = 'none'; }

// Close modal if clicking outside the content
window.onclick = function(event) {
    let modal = document.getElementById('editModal');
    if (event.target == modal) { closeModal(); }
}
</script>

<?php include "includes/footer.php"; ?>