<?php
include "../db.php";
if (session_status() === PHP_SESSION_NONE) { session_start(); }

// --- 1. CORE LOGIC: DEPLOYMENT (ADD) ---
if(isset($_POST['deploy_product'])){
    $name    = mysqli_real_escape_string($conn, $_POST['name']);
    $brand   = mysqli_real_escape_string($conn, $_POST['brand_name']);
    $m_cat   = $_POST['main_cat'];
    $s_cat   = $_POST['sub_cat'];
    $stage   = $_POST['life_stage'];
    $veg     = $_POST['veg_type']; 
    $desc    = mysqli_real_escape_string($conn, $_POST['description']);
    
    $img_name = time() . "_" . $_FILES['image']['name'];
    move_uploaded_file($_FILES['image']['tmp_name'], "../uploads/products/" . $img_name);
    
    $q = "INSERT INTO products (name, brand_name, main_cat_id, sub_cat_id, veg_type, life_stage, base_image, description) 
          VALUES ('$name', '$brand', '$m_cat', '$s_cat', '$veg', '$stage', '$img_name', '$desc')";
    
    if(mysqli_query($conn, $q)){
        $pid = mysqli_insert_id($conn);
        foreach($_POST['weights'] as $key => $w){
            $p   = $_POST['prices'][$key];
            $mrp = $_POST['mrp_list'][$key]; 
            $stk = $_POST['stock_list'][$key]; 
            mysqli_query($conn, "INSERT INTO product_variants (product_id, weight_size, price, mrp, stock_qty) 
                                 VALUES ('$pid', '$w', '$p', '$mrp', '$stk')");
        }
        $_SESSION['alert'] = ['type' => 'success', 'msg' => 'Inventory Protocol Synchronized!'];
        header("Location: admin_products.php"); exit();
    }
}

// --- 2. UPGRADED: A-TO-Z UPDATE PROTOCOL ---
if(isset($_POST['update_product'])){
    $pid     = $_POST['product_id'];
    $name    = mysqli_real_escape_string($conn, $_POST['name']);
    $brand   = mysqli_real_escape_string($conn, $_POST['brand_name']);
    $m_cat   = $_POST['main_cat']; 
    $s_cat   = $_POST['sub_cat'];  
    $stage   = $_POST['life_stage'];
    $veg     = $_POST['veg_type']; 
    $desc    = mysqli_real_escape_string($conn, $_POST['description']);
    
    $img_query = "";
    if(!empty($_FILES['image']['name'])){
        $img_name = time() . "_" . $_FILES['image']['name'];
        move_uploaded_file($_FILES['image']['tmp_name'], "../uploads/products/" . $img_name);
        $img_query = ", base_image='$img_name'";
    }

    // 1. Update Core Product Details
    $u_q = "UPDATE products SET name='$name', brand_name='$brand', main_cat_id='$m_cat', sub_cat_id='$s_cat', 
            veg_type='$veg', life_stage='$stage', description='$desc' $img_query WHERE id='$pid'";
    
    if(mysqli_query($conn, $u_q)){
        // 2. Sync Variant Matrix (Update Existing & Insert New)
        if(isset($_POST['weights'])){
            foreach($_POST['weights'] as $key => $w){
                $p    = $_POST['prices'][$key];
                $mrp  = $_POST['mrp_list'][$key];
                $stk  = $_POST['stock_list'][$key];
                $v_id = isset($_POST['variant_ids'][$key]) ? $_POST['variant_ids'][$key] : null;

                if($v_id){
                    // Update existing variant
                    mysqli_query($conn, "UPDATE product_variants SET weight_size='$w', price='$p', mrp='$mrp', stock_qty='$stk' WHERE id='$v_id'");
                } else {
                    // Insert newly added variant during edit
                    mysqli_query($conn, "INSERT INTO product_variants (product_id, weight_size, price, mrp, stock_qty) VALUES ('$pid', '$w', '$p', '$mrp', '$stk')");
                }
            }
        }
        $_SESSION['alert'] = ['type' => 'success', 'msg' => 'Intelligence & Variants Fully Updated!'];
        header("Location: admin_products.php"); exit();
    }
}

// --- 3. TERMINATION LOGIC (DELETE) ---
if(isset($_GET['terminate'])){
    $id = (int)$_GET['terminate'];
    mysqli_query($conn, "DELETE FROM products WHERE id=$id");
    $_SESSION['alert'] = ['type' => 'error', 'msg' => 'Product Protocol Erased.'];
    header("Location: admin_products.php"); exit();
}

// FETCHING DATA WITH GROUP_CONCAT TO BUNDLE VARIANTS INTO THE MODAL
$all_products = mysqli_query($conn, "SELECT p.*, m.category_name, s.sub_name, 
                                     SUM(v.stock_qty) as total_stock,
                                     GROUP_CONCAT(CONCAT(v.id, ':', v.weight_size, ':', v.price, ':', v.mrp, ':', v.stock_qty) SEPARATOR '|') as variants
                                     FROM products p 
                                     JOIN main_categories m ON p.main_cat_id = m.id 
                                     JOIN sub_categories s ON p.sub_cat_id = s.id 
                                     LEFT JOIN product_variants v ON p.id = v.product_id
                                     GROUP BY p.id ORDER BY p.id DESC");
?>

<?php include "includes/header.php"; ?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
    :root { --teal: #518992; --navy: #0f1c3f; --pink: #f87171; --bg: #f4f7f6; }
    body { background: var(--bg); font-family: 'Plus Jakarta Sans', sans-serif; }
    .admin-viewport { margin-left: 20px; padding: 60px 40px; display: flex; flex-direction: column; align-items: center; gap: 40px; }
    .hub-card { background: #fff; border-radius: 45px; padding: 50px; width: 100%; max-width: 1100px; box-shadow: 0 40px 100px rgba(15, 28, 63, 0.05); border: 1px solid #fff; }
    .matrix-box { background: #f8fafc; padding: 35px; border-radius: 30px; border: 2px dashed #e2e8f0; margin-bottom: 25px; }
    .variant-row { display: grid; grid-template-columns: 2fr 1fr 1fr 1fr; gap: 15px; margin-bottom: 12px; }
    .form-control { width: 100%; padding: 16px; border-radius: 15px; border: 2px solid #f1f5f9; background: #fcfcfc; font-weight: 600; font-size: 13px; box-sizing: border-box; }
    .btn-sync { width: 100%; padding: 20px; background: var(--navy); color: #fff; border-radius: 20px; border: none; font-weight: 900; letter-spacing: 2px; cursor: pointer; transition: 0.4s; }
    .btn-sync:hover { background: var(--teal); transform: translateY(-3px); }
    .modal-overlay { position: fixed; top:0; left:0; width:100%; height:100%; background:rgba(15,28,63,0.5); backdrop-filter:blur(5px); display:none; z-index:1000; justify-content:center; align-items:center; }
    .modal-content { background:#fff; border-radius:40px; padding:50px; width:950px; max-height:90vh; overflow-y:auto; box-shadow:0 30px 80px rgba(0,0,0,0.3); }
</style>

<div class="admin-viewport">
    <div class="hub-card animate__animated animate__fadeInDown">
        <h2 style="font-weight: 900; color: var(--navy); margin-bottom: 35px; text-align: center;"><i class="fas fa-rocket"></i> Marketplace Deployment Hub</h2>
        <form method="POST" enctype="multipart/form-data">
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 25px;">
                <input type="text" name="name" class="form-control" placeholder="Product Name" required>
                <input type="text" name="brand_name" class="form-control" placeholder="Brand" required>
                <input type="file" name="image" required style="font-size: 12px;">
            </div>
            <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 25px;">
                <select name="main_cat" id="main_cat_add" class="form-control" required onchange="updateLifeStage('add')">
                    <option value="">Species</option>
                    <?php $m_res = mysqli_query($conn, "SELECT * FROM main_categories"); while($m = mysqli_fetch_assoc($m_res)) echo "<option value='{$m['id']}'>{$m['category_name']}</option>"; ?>
                </select>
                <select name="sub_cat" id="sub_cat_add" class="form-control" required onchange="toggleVegField()">
                    <option value="">Category</option>
                    <?php $s_res = mysqli_query($conn, "SELECT * FROM sub_categories"); while($s = mysqli_fetch_assoc($s_res)) echo "<option value='{$s['id']}'>{$s['sub_name']}</option>"; ?>
                </select>
                <select name="life_stage" id="life_stage_add" class="form-control" required>
                    <option value="">Select Species First</option>
                    <option value="Puppy">Puppy</option><option value="Adult">Adult</option><option value="Senior">Senior</option>
                </select>
                <select name="veg_type" id="veg_type_add" class="form-control" disabled>
                    <option value="N/A">N/A</option>
                    <option value="Veg">Vegetarian</option>
                    <option value="Non-Veg">Non-Veg</option>
                </select>
            </div>
            <textarea name="description" class="form-control" style="height:80px; margin-bottom:25px;" placeholder="Description Protocol..."></textarea>
            <div class="matrix-box">
                <h4 style="margin-bottom: 20px; font-size: 14px;">Variant Pricing & Stock Matrix</h4>
                <div id="variantZone">
                    <div class="variant-row">
                        <input type="text" name="weights[]" placeholder="e.g. 500g" class="form-control" required>
                        <input type="number" name="mrp_list[]" placeholder="MRP ₹" class="form-control" required>
                        <input type="number" name="prices[]" placeholder="Sale ₹" class="form-control" required>
                        <input type="number" name="stock_list[]" placeholder="Stock" class="form-control" required>
                    </div>
                </div>
                <button type="button" onclick="addVariantRow('variantZone')" style="background:var(--teal); color:#fff; border:none; padding:10px 20px; border-radius:12px; font-weight:800; cursor:pointer; margin-top:10px;">+ Add Size Variant</button>
            </div>
            <button type="submit" name="deploy_product" class="btn-sync">INITIALIZE GLOBAL SYNC</button>
        </form>
    </div>

    <div class="hub-card animate__animated animate__fadeInUp">
        <h3 style="font-weight: 900; color: var(--navy); margin-bottom: 30px;"><i class="fas fa-warehouse"></i> Live Intelligence Repository</h3>
        <table style="width: 100%; border-collapse: separate; border-spacing: 0 12px;">
            <thead>
                <tr style="text-align:left; font-size:11px; color:#94a3b8; text-transform:uppercase;">
                    <th>Media</th><th>Identity</th><th>Hierarchy</th><th>Stock</th><th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php mysqli_data_seek($all_products, 0); while($row = mysqli_fetch_assoc($all_products)): ?>
                <tr style="background: #fff; transition:0.3s;">
                    <td style="padding: 15px; border-radius: 20px 0 0 20px;"><img src="../uploads/products/<?php echo $row['base_image']; ?>" style="width:50px; height:50px; border-radius:12px; object-fit:cover;"></td>
                    <td style="padding: 15px;">
                        <div style="font-weight:800; color:var(--navy);"><?php echo $row['name']; ?></div>
                        <div style="font-size:11px; color:var(--teal);"><?php echo $row['brand_name']; ?></div>
                    </td>
                    <td style="padding: 15px;"><span style="font-size:10px; font-weight:800; color:#94a3b8;"><?php echo $row['category_name']; ?> > <?php echo $row['sub_name']; ?></span></td>
                    <td style="padding: 15px;"><span class="stock-badge <?php echo ($row['total_stock'] < 5) ? 'low-stock' : 'in-stock'; ?>"><?php echo (int)$row['total_stock']; ?> Units</span></td>
                    <td style="padding: 15px; border-radius: 0 20px 20px 0;">
                        <button class="btn-edit" onclick='openEditModal(<?php echo json_encode($row); ?>)' style="border:none; background:#e0f2fe; color:#0284c7; padding:8px; border-radius:8px; cursor:pointer;"><i class="fas fa-edit"></i></button>
                        <a href="?terminate=<?php echo $row['id']; ?>" style="color:var(--pink); margin-left:10px;" onclick="return confirm('Erase record?')"><i class="fas fa-trash-alt"></i></a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<div id="editModal" class="modal-overlay">
    <div class="modal-content animate__animated animate__zoomIn">
        <h2 style="font-weight:900; color:var(--navy); margin-bottom:30px;"><i class="fas fa-edit"></i> Full Intelligence Update</h2>
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="product_id" id="edit_id">
            
            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; margin-bottom: 20px;">
                <input type="text" name="name" id="edit_name" class="form-control" placeholder="Name">
                <input type="text" name="brand_name" id="edit_brand" class="form-control" placeholder="Brand">
                <select name="main_cat" id="edit_main_cat" class="form-control" onchange="updateLifeStage('edit')">
                    <?php mysqli_data_seek($m_res, 0); while($m = mysqli_fetch_assoc($m_res)) echo "<option value='{$m['id']}'>{$m['category_name']}</option>"; ?>
                </select>
                <select name="sub_cat" id="edit_sub_cat" class="form-control" onchange="toggleVegFieldEdit()">
                    <?php mysqli_data_seek($s_res, 0); while($s = mysqli_fetch_assoc($s_res)) echo "<option value='{$s['id']}'>{$s['sub_name']}</option>"; ?>
                </select>
                <select name="life_stage" id="edit_stage" class="form-control">
                    <option value="Puppy">Puppy</option><option value="Adult">Adult</option><option value="Senior">Senior</option>
                </select>
                <select name="veg_type" id="edit_veg" class="form-control">
                    <option value="N/A">N/A</option>
                    <option value="Veg">Vegetarian</option>
                    <option value="Non-Veg">Non-Veg</option>
                </select>
                <input type="file" name="image" class="form-control">
            </div>

            <textarea name="description" id="edit_desc" class="form-control" style="height:100px; margin-bottom:20px;" placeholder="Full Description Update..."></textarea>

            <div class="matrix-box">
                <h4 style="margin-bottom: 20px; font-size: 14px;">Update Variant Details (Size, Price, MRP, Stock)</h4>
                <div id="editVariantZone">
                    </div>
                <button type="button" onclick="addVariantRow('editVariantZone')" style="background:var(--teal); color:#fff; border:none; padding:10px 20px; border-radius:12px; font-weight:800; cursor:pointer;">+ Add New Variant</button>
            </div>
            
            <button type="submit" name="update_product" class="btn-sync" style="margin-top:20px;">SAVE ALL UPDATES</button>
            <button type="button" onclick="closeModal()" style="width:100%; background:none; border:none; color:#94a3b8; font-weight:800; margin-top:15px; cursor:pointer;">CANCEL</button>
        </form>
    </div>
</div>

<script>
// Logic to add dynamic rows to variant zones
function addVariantRow(zoneId) {
    let div = document.createElement('div');
    div.className = "variant-row";
    div.innerHTML = `<input type="text" name="weights[]" placeholder="Size" class="form-control" required>
                     <input type="number" name="mrp_list[]" placeholder="MRP" class="form-control" required>
                     <input type="number" name="prices[]" placeholder="Sale" class="form-control" required>
                     <input type="number" name="stock_list[]" placeholder="Stock" class="form-control" required>`;
    document.getElementById(zoneId).appendChild(div);
}

function openEditModal(data) {
    document.getElementById('editModal').style.display = 'flex';
    document.getElementById('edit_id').value = data.id;
    document.getElementById('edit_name').value = data.name;
    document.getElementById('edit_brand').value = data.brand_name;
    document.getElementById('edit_main_cat').value = data.main_cat_id;
    document.getElementById('edit_sub_cat').value = data.sub_cat_id;
    
    // Update life stage options based on species, then set the value
    updateLifeStage('edit');
    document.getElementById('edit_stage').value = data.life_stage;
    
    document.getElementById('edit_veg').value = data.veg_type;
    document.getElementById('edit_desc').value = data.description;
    
    // Trigger veg field toggle for edit modal
    toggleVegFieldEdit();

    // INJECTING VARIANTS FOR FULL A-TO-Z UPDATES
    let variantZone = document.getElementById('editVariantZone');
    variantZone.innerHTML = ""; // Clear old data
    if(data.variants) {
        let variantArray = data.variants.split('|');
        variantArray.forEach(v => {
            let parts = v.split(':'); // ID, weight_size, price, mrp, stock_qty
            let row = document.createElement('div');
            row.className = "variant-row";
            row.innerHTML = `
                <input type="hidden" name="variant_ids[]" value="${parts[0]}">
                <input type="text" name="weights[]" value="${parts[1]}" class="form-control">
                <input type="number" name="mrp_list[]" value="${parts[3]}" class="form-control">
                <input type="number" name="prices[]" value="${parts[2]}" class="form-control">
                <input type="number" name="stock_list[]" value="${parts[4]}" class="form-control">
            `;
            variantZone.appendChild(row);
        });
    }
}

function closeModal() { document.getElementById('editModal').style.display = 'none'; }

// Update Life Stage options based on Species selection
function updateLifeStage(formType) {
    const mainCatSelect = document.getElementById(formType === 'add' ? 'main_cat_add' : 'edit_main_cat');
    const lifeStageSelect = document.getElementById(formType === 'add' ? 'life_stage_add' : 'edit_stage');
    
    const selectedValue = mainCatSelect.value;
    const selectedText = mainCatSelect.options[mainCatSelect.selectedIndex].text.toLowerCase();
    const currentValue = lifeStageSelect.value;
    
    // If no species selected yet, show placeholder
    if(!selectedValue) {
        lifeStageSelect.innerHTML = '<option value="">Select Species First</option>';
        return;
    }
    
    // Check if Cat is selected
    if(selectedText.includes('cat')) {
        // For Cats: Kitten, Adult, Senior
        lifeStageSelect.innerHTML = '<option value="Kitten">Kitten</option><option value="Adult">Adult</option><option value="Senior">Senior</option>';
        // Preserve selection or default to appropriate value
        if(currentValue === 'Puppy' || currentValue === '') {
            lifeStageSelect.value = 'Kitten';
        } else if(['Kitten', 'Adult', 'Senior'].includes(currentValue)) {
            lifeStageSelect.value = currentValue;
        } else {
            lifeStageSelect.value = 'Kitten';
        }
    } else {
        // For Dogs (default): Puppy, Adult, Senior
        lifeStageSelect.innerHTML = '<option value="Puppy">Puppy</option><option value="Adult">Adult</option><option value="Senior">Senior</option>';
        // Preserve selection or default to appropriate value
        if(currentValue === 'Kitten' || currentValue === '') {
            lifeStageSelect.value = 'Puppy';
        } else if(['Puppy', 'Adult', 'Senior'].includes(currentValue)) {
            lifeStageSelect.value = currentValue;
        } else {
            lifeStageSelect.value = 'Puppy';
        }
    }
}

// Enable/Disable Veg Type based on Category Selection (Add Form)
function toggleVegField() {
    const subCat = document.getElementById('sub_cat_add');
    const vegType = document.getElementById('veg_type_add');
    const selectedText = subCat.options[subCat.selectedIndex].text.toLowerCase();
    
    // Enable only for Food or Treats categories
    if(selectedText.includes('food') || selectedText.includes('treat')) {
        vegType.disabled = false;
        vegType.style.opacity = '1';
    } else {
        vegType.disabled = true;
        vegType.value = 'N/A';
        vegType.style.opacity = '0.5';
    }
}

// Enable/Disable Veg Type based on Category Selection (Edit Form)
function toggleVegFieldEdit() {
    const subCat = document.getElementById('edit_sub_cat');
    const vegType = document.getElementById('edit_veg');
    const selectedText = subCat.options[subCat.selectedIndex].text.toLowerCase();
    
    // Enable only for Food or Treats categories
    if(selectedText.includes('food') || selectedText.includes('treat')) {
        vegType.disabled = false;
        vegType.style.opacity = '1';
    } else {
        vegType.disabled = true;
        vegType.value = 'N/A';
        vegType.style.opacity = '0.5';
    }
}

<?php if(isset($_SESSION['alert'])): ?>
    Swal.fire({ icon: '<?php echo $_SESSION['alert']['type']; ?>', title: '<?php echo $_SESSION['alert']['msg']; ?>', showConfirmButton: false, timer: 2000 });
<?php unset($_SESSION['alert']); endif; ?>
</script>

<?php include "includes/footer.php"; ?>