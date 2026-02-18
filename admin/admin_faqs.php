<?php
/**
 * FURRYMART ADMIN - SOVEREIGN FAQ MANAGEMENT HUB
 * Features: Live Icon Preview Lab + Full CRUD Operations
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
$edit_id = $edit_question = $edit_answer = $edit_category = $edit_icon = "";

// --- 1. DELETE PROTOCOL ---
if (isset($_GET['delete'])) {
    $del_id = mysqli_real_escape_string($conn, $_GET['delete']);
    mysqli_query($conn, "DELETE FROM faqs WHERE id = '$del_id'");
    header("Location:admin_faqs.php?status=success&action=purged");
    exit();
}

// --- 2. EDIT FETCH PROTOCOL ---
if (isset($_GET['edit'])) {
    $is_edit = true;
    $edit_id = mysqli_real_escape_string($conn, $_GET['edit']);
    $res = mysqli_query($conn, "SELECT * FROM faqs WHERE id = '$edit_id'");
    $row = mysqli_fetch_assoc($res);
    if ($row) {
        $edit_question = $row['question'];
        $edit_answer   = $row['answer'];
        $edit_category = $row['category'];
        $edit_icon     = $row['icon_class'];
    }
}

// --- 3. SAVE/UPDATE PROTOCOL ---
if (isset($_POST['submit_faq'])) {
    $q = mysqli_real_escape_string($conn, $_POST['question']);
    $a = mysqli_real_escape_string($conn, $_POST['answer']);
    $c = mysqli_real_escape_string($conn, $_POST['category']);
    $i = mysqli_real_escape_string($conn, $_POST['icon_class']);

    if (isset($_POST['faq_id']) && !empty($_POST['faq_id'])) {
        $id = $_POST['faq_id'];
        $sql = "UPDATE faqs SET question='$q', answer='$a', category='$c', icon_class='$i' WHERE id='$id'";
        $act = "updated";
    } else {
        $sql = "INSERT INTO faqs (question, answer, category, icon_class) VALUES ('$q', '$a', '$c', '$i')";
        $act = "published";
    }
    
    if (mysqli_query($conn, $sql)) {
        header("Location: admin_faqs.php?status=success&action=$act");
        exit();
    }
}

$count_res = mysqli_query($conn, "SELECT COUNT(*) as total FROM faqs");
$total_faqs = mysqli_fetch_assoc($count_res)['total'];

include('includes/header.php'); 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        :root { --primary: #518992; --navy: #0f1c3f; --bg: #f4f7fa; --white: #ffffff; --border: #e2e8f0; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: var(--bg); color: var(--navy); }
        .admin-wrapper { padding: 40px 5%; max-width: 1400px; margin: auto; }
        
        /* Analytics Dashboard */
        .dash-hub { background: var(--navy); color: white; padding: 25px 40px; border-radius: 25px; display: flex; justify-content: space-between; align-items: center; margin-bottom: 40px; box-shadow: 0 20px 40px rgba(15,28,63,0.15); }
        .stat-group h2 { margin: 0; font-size: 32px; color: var(--primary); }
        .stat-group span { font-size: 11px; text-transform: uppercase; letter-spacing: 2px; font-weight: 800; opacity: 0.7; }

        /* Icon Selection Lab */
        .icon-lab { display: flex; gap: 20px; align-items: flex-start; background: #fbfcfe; padding: 20px; border-radius: 20px; border: 1.5px solid var(--border); }
        .preview-box { width: 80px; height: 80px; background: white; border-radius: 15px; display: flex; align-items: center; justify-content: center; font-size: 35px; color: var(--primary); box-shadow: 0 10px 20px rgba(0,0,0,0.05); flex-shrink: 0; }
        .quick-grid { display: grid; grid-template-columns: repeat(6, 1fr); gap: 10px; margin-top: 15px; }
        .icon-opt { cursor: pointer; padding: 10px; background: white; border: 1px solid var(--border); border-radius: 10px; text-align: center; transition: 0.3s; color: #64748b; }
        .icon-opt:hover { border-color: var(--primary); color: var(--primary); transform: scale(1.1); }

        /* Form Styling */
        .form-card { background: var(--white); padding: 45px; border-radius: 40px; box-shadow: 0 10px 40px rgba(0,0,0,0.03); border: 1px solid var(--border); margin-bottom: 40px; }
        label { display: block; font-weight: 800; font-size: 11px; margin-bottom: 10px; text-transform: uppercase; color: var(--primary); letter-spacing: 1px; }
        input, select, textarea { width: 100%; padding: 15px 20px; border: 1.5px solid var(--border); border-radius: 12px; outline: none; transition: 0.3s; font-family: inherit; font-size: 14px; }
        input:focus { border-color: var(--primary); box-shadow: 0 0 0 4px rgba(81, 137, 146, 0.1); }
        
        .btn-action { background: var(--primary); color: white; border: none; padding: 18px 45px; border-radius: 50px; font-weight: 800; cursor: pointer; transition: 0.4s; }
        .btn-action:hover { background: var(--navy); transform: translateY(-3px); }

        /* Sovereign Table Index */
        .table-container { background: var(--white); border-radius: 45px; padding: 45px; border: 1px solid var(--border); box-shadow: 0 10px 50px rgba(0,0,0,0.02); }
        table { width: 100%; border-collapse: separate; border-spacing: 0; }
        th { text-align: left; padding: 20px; color: #64748b; font-size: 11px; text-transform: uppercase; border-bottom: 2px solid #f1f5f9; letter-spacing: 1.5px; }
        td { padding: 20px; border-bottom: 1px solid #f1f5f9; font-size: 14px; }
        .cat-pill { padding: 6px 15px; border-radius: 50px; font-size: 11px; font-weight: 800; background: #f0fdfa; color: var(--primary); }
    </style>
</head>
<body>

<main class="admin-wrapper">
    <div class="dash-hub">
        <div class="stat-group">
            <span>Sovereign Intelligence Index</span>
            <h2><?php echo $total_faqs; ?> Modules Active</h2>
        </div>
        <i class="fas fa-microchip" style="font-size: 45px; opacity: 0.2;"></i>
    </div>

    <div class="form-card">
        <h3 style="font-size:18px; font-weight:800; margin-bottom:30px;"><i class="fas fa-shield-cat"></i> <?php echo $is_edit ? 'Update Protocol' : 'New Intelligence Module'; ?></h3>
        
        <form action="admin_faqs.php" method="POST">
            <?php if($is_edit): ?> <input type="hidden" name="faq_id" value="<?php echo $edit_id; ?>"> <?php endif; ?>

            <div style="display:grid; grid-template-columns: 1fr 1fr; gap: 30px; margin-bottom: 30px;">
                <div>
                    <label>Module Category</label>
                    <select name="category" required>
                        <option value="Commonly Asked" <?php echo ($edit_category == 'Commonly Asked') ? 'selected' : ''; ?>>Commonly Asked</option>
                        <option value="Products" <?php echo ($edit_category == 'Products') ? 'selected' : ''; ?>>Products Intelligence</option>
                        <option value="Shipping" <?php echo ($edit_category == 'Shipping') ? 'selected' : ''; ?>>Logistics Hub</option>
                        <option value="Returns" <?php echo ($edit_category == 'Returns') ? 'selected' : ''; ?>>Returns Protocol</option>
                        <option value="Services" <?php echo ($edit_category == 'Services') ? 'selected' : ''; ?>>Vetcare & Services</option>
                    </select>
                </div>
                <div>
                    <label>Module Question</label>
                    <input type="text" name="question" value="<?php echo $edit_question; ?>" placeholder="Enter clinical question..." required>
                </div>
            </div>

            <div style="margin-bottom:30px;">
                <label>Sovereign Icon Identifier</label>
                <div class="icon-lab">
                    <div class="preview-box" id="icon-preview-box">
                        <i class="<?php echo $edit_icon ?: 'fas fa-paw'; ?>"></i>
                    </div>
                    <div style="flex:1;">
                        <input type="text" name="icon_class" id="icon-input" value="<?php echo $edit_icon ?: 'fas fa-paw'; ?>" placeholder="Type icon class or select below..." required onkeyup="updateIconPreview(this.value)">
                       <div class="quick-grid" style="grid-template-columns: repeat(10, 1fr); max-height: 200px; overflow-y: auto; padding-right: 10px;">
    <div class="icon-opt" title="Dog" onclick="selectIcon('fas fa-dog')"><i class="fas fa-dog"></i></div>
    <div class="icon-opt" title="Cat" onclick="selectIcon('fas fa-cat')"><i class="fas fa-cat"></i></div>
    <div class="icon-opt" title="Paw" onclick="selectIcon('fas fa-paw')"><i class="fas fa-paw"></i></div>
    <div class="icon-opt" title="Fish" onclick="selectIcon('fas fa-fish')"><i class="fas fa-fish"></i></div>
    <div class="icon-opt" title="Bird" onclick="selectIcon('fas fa-dove')"><i class="fas fa-dove"></i></div>
    <div class="icon-opt" title="Bone" onclick="selectIcon('fas fa-bone')"><i class="fas fa-bone"></i></div>
    
    <div class="icon-opt" title="Vet" onclick="selectIcon('fas fa-stethoscope')"><i class="fas fa-stethoscope"></i></div>
    <div class="icon-opt" title="Medical" onclick="selectIcon('fas fa-pills')"><i class="fas fa-pills"></i></div>
    <div class="icon-opt" title="Health" onclick="selectIcon('fas fa-heart-pulse')"><i class="fas fa-heart-pulse"></i></div>
    <div class="icon-opt" title="Clinical" onclick="selectIcon('fas fa-microscope')"><i class="fas fa-microscope"></i></div>
    <div class="icon-opt" title="Hospital" onclick="selectIcon('fas fa-hospital')"><i class="fas fa-hospital"></i></div>
    <div class="icon-opt" title="First Aid" onclick="selectIcon('fas fa-kit-medical')"><i class="fas fa-kit-medical"></i></div>
    
    <div class="icon-opt" title="Shipping" onclick="selectIcon('fas fa-truck-fast')"><i class="fas fa-truck-fast"></i></div>
    <div class="icon-opt" title="Order" onclick="selectIcon('fas fa-box')"><i class="fas fa-box"></i></div>
    <div class="icon-opt" title="Tracking" onclick="selectIcon('fas fa-map-location-dot')"><i class="fas fa-map-location-dot"></i></div>
    <div class="icon-opt" title="Bag" onclick="selectIcon('fas fa-bag-shopping')"><i class="fas fa-bag-shopping"></i></div>
    <div class="icon-opt" title="Tag" onclick="selectIcon('fas fa-tags')"><i class="fas fa-tags"></i></div>
    <div class="icon-opt" title="Wallet" onclick="selectIcon('fas fa-wallet')"><i class="fas fa-wallet"></i></div>
    <div class="icon-opt" title="Card" onclick="selectIcon('fas fa-credit-card')"><i class="fas fa-credit-card"></i></div>
    <div class="icon-opt" title="Fetch Toy" onclick="selectIcon('fas fa-football')"><i class="fas fa-football"></i></div>
<div class="icon-opt" title="Ball Toy" onclick="selectIcon('fas fa-volleyball')"><i class="fas fa-volleyball"></i></div>
<div class="icon-opt" title="Teaser Toy" onclick="selectIcon('fas fa-table-tennis-paddle-ball')"><i class="fas fa-table-tennis-paddle-ball"></i></div>
<div class="icon-opt" title="Meat Treat" onclick="selectIcon('fas fa-drumstick-bite')"><i class="fas fa-drumstick-bite"></i></div>
<div class="icon-opt" title="Puzzle Toy" onclick="selectIcon('fas fa-puzzle-piece')"><i class="fas fa-puzzle-piece"></i></div>

<div class="icon-opt" title="Shower/Bath" onclick="selectIcon('fas fa-shower')"><i class="fas fa-shower"></i></div>
<div class="icon-opt" title="Shampoo/Soap" onclick="selectIcon('fas fa-pump-soap')"><i class="fas fa-pump-soap"></i></div>
<div class="icon-opt" title="Grooming Spray" onclick="selectIcon('fas fa-spray-can')"><i class="fas fa-spray-can"></i></div>
<div class="icon-opt" title="Wipes/Cleanup" onclick="selectIcon('fas fa-toilet-paper')"><i class="fas fa-toilet-paper"></i></div>
<div class="icon-opt" title="Waste Disposal" onclick="selectIcon('fas fa-poop')"><i class="fas fa-poop"></i></div>

<div class="icon-opt" title="Healthy Veggie" onclick="selectIcon('fas fa-carrot')"><i class="fas fa-carrot"></i></div>
<div class="icon-opt" title="Fruit Treat" onclick="selectIcon('fas fa-apple-whole')"><i class="fas fa-apple-whole"></i></div>
<div class="icon-opt" title="Baked Biscuit" onclick="selectIcon('fas fa-cookie-bite')"><i class="fas fa-cookie-bite"></i></div>
<div class="icon-opt" title="Travel Bottle" onclick="selectIcon('fas fa-bottle-water')"><i class="fas fa-bottle-water"></i></div>
<div class="icon-opt" title="Food Scoop" onclick="selectIcon('fas fa-spoon')"><i class="fas fa-spoon"></i></div>

<div class="icon-opt" title="Dog House" onclick="selectIcon('fas fa-house')"><i class="fas fa-house"></i></div>
<div class="icon-opt" title="Pet Mat/Rug" onclick="selectIcon('fas fa-rug')"><i class="fas fa-rug"></i></div>
<div class="icon-opt" title="Pet Furniture" onclick="selectIcon('fas fa-couch')"><i class="fas fa-couch"></i></div>
<div class="icon-opt" title="Hideout/Igloo" onclick="selectIcon('fas fa-igloo')"><i class="fas fa-igloo"></i></div>
<div class="icon-opt" title="Litter/Bedding" onclick="selectIcon('fas fa-layer-group')"><i class="fas fa-layer-group"></i></div>

<div class="icon-opt" title="Pet Clothing" onclick="selectIcon('fas fa-shirt')"><i class="fas fa-shirt"></i></div>
<div class="icon-opt" title="Fun Hat" onclick="selectIcon('fas fa-hat-cowboy')"><i class="fas fa-hat-cowboy"></i></div>
<div class="icon-opt" title="Paw Socks" onclick="selectIcon('fas fa-socks')"><i class="fas fa-socks"></i></div>
<div class="icon-opt" title="Accessories" onclick="selectIcon('fas fa-glasses')"><i class="fas fa-glasses"></i></div>
<div class="icon-opt" title="Leash Link" onclick="selectIcon('fas fa-link')"><i class="fas fa-link"></i></div>

<div class="icon-opt" title="Shopping Basket" onclick="selectIcon('fas fa-basket-shopping')"><i class="fas fa-basket-shopping"></i></div>
<div class="icon-opt" title="Checkout" onclick="selectIcon('fas fa-cash-register')"><i class="fas fa-cash-register"></i></div>
<div class="icon-opt" title="Product Scan" onclick="selectIcon('fas fa-barcode')"><i class="fas fa-barcode"></i></div>
<div class="icon-opt" title="Physical Shop" onclick="selectIcon('fas fa-shop')"><i class="fas fa-shop"></i></div>
<div class="icon-opt" title="Reviews/Ratings" onclick="selectIcon('fas fa-star-half-stroke')"><i class="fas fa-star-half-stroke"></i></div>
    <div class="icon-opt" title="Support" onclick="selectIcon('fas fa-headset')"><i class="fas fa-headset"></i></div>
    <div class="icon-opt" title="User" onclick="selectIcon('fas fa-user-gear')"><i class="fas fa-user-gear"></i></div>
    <div class="icon-opt" title="Shield" onclick="selectIcon('fas fa-shield-cat')"><i class="fas fa-shield-cat"></i></div>
    <div class="icon-opt" title="Return" onclick="selectIcon('fas fa-rotate-left')"><i class="fas fa-rotate-left"></i></div>
    <div class="icon-opt" title="Info" onclick="selectIcon('fas fa-circle-info')"><i class="fas fa-circle-info"></i></div>
    <div class="icon-opt" title="Help" onclick="selectIcon('fas fa-circle-question')"><i class="fas fa-circle-question"></i></div>
    <div class="icon-opt" title="Star" onclick="selectIcon('fas fa-star')"><i class="fas fa-star"></i></div>
    <div class="icon-opt" title="Clock" onclick="selectIcon('fas fa-clock')"><i class="fas fa-clock"></i></div>
    
    <div class="icon-opt" title="Grooming" onclick="selectIcon('fas fa-scissors')"><i class="fas fa-scissors"></i></div>
    <div class="icon-opt" title="Bath" onclick="selectIcon('fas fa-soap')"><i class="fas fa-soap"></i></div>
    <div class="icon-opt" title="Brush" onclick="selectIcon('fas fa-broom')"><i class="fas fa-broom"></i></div>
    <div class="icon-opt" title="Bowl" onclick="selectIcon('fas fa-bowl-food')"><i class="fas fa-bowl-food"></i></div>
    <div class="icon-opt" title="House" onclick="selectIcon('fas fa-house-chimney-window')"><i class="fas fa-house-chimney-window"></i></div>
    <div class="icon-opt" title="Small Dog" onclick="selectIcon('fas fa-puppy')"><i class="fas fa-dog"></i></div>
<div class="icon-opt" title="Large Cat" onclick="selectIcon('fas fa-cat-space')"><i class="fas fa-cat"></i></div>
<div class="icon-opt" title="Hamster" onclick="selectIcon('fas fa-otter')"><i class="fas fa-otter"></i></div>
<div class="icon-opt" title="Horse" onclick="selectIcon('fas fa-horse')"><i class="fas fa-horse"></i></div>
<div class="icon-opt" title="Dragon/Lizard" onclick="selectIcon('fas fa-dragon')"><i class="fas fa-dragon"></i></div>
<div class="icon-opt" title="DNA Genetics" onclick="selectIcon('fas fa-dna')"><i class="fas fa-dna"></i></div>
<div class="icon-opt" title="Egg/Hatching" onclick="selectIcon('fas fa-egg')"><i class="fas fa-egg"></i></div>

<div class="icon-opt" title="X-Ray Scan" onclick="selectIcon('fas fa-x-ray')"><i class="fas fa-x-ray"></i></div>
<div class="icon-opt" title="Thermometer" onclick="selectIcon('fas fa-thermometer')"><i class="fas fa-thermometer"></i></div>
<div class="icon-opt" title="Syringe" onclick="selectIcon('fas fa-syringe')"><i class="fas fa-syringe"></i></div>
<div class="icon-opt" title="Prescription" onclick="selectIcon('fas fa-file-prescription')"><i class="fas fa-file-prescription"></i></div>
<div class="icon-opt" title="Brain/Neurology" onclick="selectIcon('fas fa-brain')"><i class="fas fa-brain"></i></div>
<div class="icon-opt" title="Medicine Bottle" onclick="selectIcon('fas fa-flask-vial')"><i class="fas fa-flask-vial"></i></div>
<div class="icon-opt" title="Emergency Bandage" onclick="selectIcon('fas fa-bandage')"><i class="fas fa-bandage"></i></div>
<div class="icon-opt" title="Dropper" onclick="selectIcon('fas fa-droplet')"><i class="fas fa-droplet"></i></div>

<div class="icon-opt" title="Air Freight" onclick="selectIcon('fas fa-plane-up')"><i class="fas fa-plane-up"></i></div>
<div class="icon-opt" title="Shipment Hub" onclick="selectIcon('fas fa-warehouse')"><i class="fas fa-warehouse"></i></div>
<div class="icon-opt" title="Courier Bike" onclick="selectIcon('fas fa-motorcycle')"><i class="fas fa-motorcycle"></i></div>
<div class="icon-opt" title="Global Route" onclick="selectIcon('fas fa-route')"><i class="fas fa-route"></i></div>
<div class="icon-opt" title="Parcel Box" onclick="selectIcon('fas fa-boxes-stacked')"><i class="fas fa-boxes-stacked"></i></div>
<div class="icon-opt" title="Fast Clock" onclick="selectIcon('fas fa-stopwatch-20')"><i class="fas fa-stopwatch-20"></i></div>
<div class="icon-opt" title="Fuel/Energy" onclick="selectIcon('fas fa-gas-pump')"><i class="fas fa-gas-pump"></i></div>

<div class="icon-opt" title="Store Front" onclick="selectIcon('fas fa-store')"><i class="fas fa-store"></i></div>
<div class="icon-opt" title="Special Offer" onclick="selectIcon('fas fa-percent')"><i class="fas fa-percent"></i></div>
<div class="icon-opt" title="Cash Payment" onclick="selectIcon('fas fa-money-bill-1')"><i class="fas fa-money-bill-1"></i></div>
<div class="icon-opt" title="Coin/Currency" onclick="selectIcon('fas fa-coins')"><i class="fas fa-coins"></i></div>
<div class="icon-opt" title="Gift Card" onclick="selectIcon('fas fa-gift')"><i class="fas fa-gift"></i></div>
<div class="icon-opt" title="Receipt" onclick="selectIcon('fas fa-receipt')"><i class="fas fa-receipt"></i></div>
<div class="icon-opt" title="Safe/Vault" onclick="selectIcon('fas fa-vault')"><i class="fas fa-vault"></i></div>

<div class="icon-opt" title="Live Chat" onclick="selectIcon('fas fa-comment-medical')"><i class="fas fa-comment-medical"></i></div>
<div class="icon-opt" title="Video Call" onclick="selectIcon('fas fa-video')"><i class="fas fa-video"></i></div>
<div class="icon-opt" title="Settings" onclick="selectIcon('fas fa-sliders')"><i class="fas fa-sliders"></i></div>
<div class="icon-opt" title="Search Hub" onclick="selectIcon('fas fa-magnifying-glass-plus')"><i class="fas fa-magnifying-glass-plus"></i></div>
<div class="icon-opt" title="Privacy Lock" onclick="selectIcon('fas fa-user-lock')"><i class="fas fa-user-lock"></i></div>
<div class="icon-opt" title="Notification" onclick="selectIcon('fas fa-bell')"><i class="fas fa-bell"></i></div>
<div class="icon-opt" title="WiFi/Signal" onclick="selectIcon('fas fa-wifi')"><i class="fas fa-wifi"></i></div>

<div class="icon-opt" title="Pet Bed" onclick="selectIcon('fas fa-bed')"><i class="fas fa-bed"></i></div>
<div class="icon-opt" title="Car Travel" onclick="selectIcon('fas fa-car-side')"><i class="fas fa-car-side"></i></div>
<div class="icon-opt" title="Pet Collar" onclick="selectIcon('fas fa-award')"><i class="fas fa-award"></i></div>
<div class="icon-opt" title="Water Bowl" onclick="selectIcon('fas fa-faucet-drip')"><i class="fas fa-faucet-drip"></i></div>
<div class="icon-opt" title="Outdoor/Park" onclick="selectIcon('fas fa-tree')"><i class="fas fa-tree"></i></div>
<div class="icon-opt" title="Sun/Health" onclick="selectIcon('fas fa-sun')"><i class="fas fa-sun"></i></div>
<div class="icon-opt" title="Moon/Night" onclick="selectIcon('fas fa-moon')"><i class="fas fa-moon"></i></div>
<div class="icon-opt" title="Pet Training" onclick="selectIcon('fas fa-whistle')"><i class="fas fa-bullhorn"></i></div>
<div class="icon-opt" title="Play Time" onclick="selectIcon('fas fa-baseball')"><i class="fas fa-baseball"></i></div>

<div class="icon-opt" title="Terms" onclick="selectIcon('fas fa-file-contract')"><i class="fas fa-file-contract"></i></div>
<div class="icon-opt" title="Privacy" onclick="selectIcon('fas fa-user-shield')"><i class="fas fa-user-shield"></i></div>
<div class="icon-opt" title="Identity" onclick="selectIcon('fas fa-id-card-clip')"><i class="fas fa-id-card-clip"></i></div>
<div class="icon-opt" title="Email" onclick="selectIcon('fas fa-envelope-open-text')"><i class="fas fa-envelope-open-text"></i></div>
<div class="icon-opt" title="Download" onclick="selectIcon('fas fa-file-arrow-down')"><i class="fas fa-file-arrow-down"></i></div>
</div>
                    </div>
                </div>
            </div>

            <label>Clinical Answer Content</label>
            <textarea name="answer" rows="4" placeholder="Enter detailed guidance..." required><?php echo $edit_answer; ?></textarea>

            <div style="margin-top:40px; display:flex; gap:20px;">
                <button type="submit" name="submit_faq" class="btn-action">
                    <i class="fas fa-save"></i> <?php echo $is_edit ? 'EXECUTE UPDATE' : 'PUBLISH MODULE'; ?>
                </button>
                <?php if($is_edit): ?> <a href="admin_faqs.php" style="align-self:center; color:#64748b; font-weight:700; text-decoration:none;">Cancel</a> <?php endif; ?>
            </div>
        </form>
    </div>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th width="80">Icon</th>
                    <th width="150">Category</th>
                    <th>Question Module</th>
                    <th width="150">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $res = mysqli_query($conn, "SELECT * FROM faqs ORDER BY id DESC");
                while($row = mysqli_fetch_assoc($res)) {
                    echo '<tr>
                        <td><div style="font-size:22px; color:var(--primary);"><i class="'.$row['icon_class'].'"></i></div></td>
                        <td><span class="cat-pill">'.$row['category'].'</span></td>
                        <td style="font-weight:700; color:var(--navy);">'.$row['question'].'</td>
                        <td>
                            <a href="?edit='.$row['id'].'" style="color:#2563eb; margin-right:15px;"><i class="fas fa-pen"></i></a>
                            <a href="?delete='.$row['id'].'" style="color:#e11d48;" onclick="return confirm(\'Purge module?\')"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>';
                }
                ?>
            </tbody>
        </table>
    </div>
</main>

<script>
    // ICON LAB LOGIC
    function updateIconPreview(val) {
        document.getElementById('icon-preview-box').innerHTML = `<i class="${val}"></i>`;
    }
    function selectIcon(icon) {
        document.getElementById('icon-input').value = icon;
        updateIconPreview(icon);
    }

    // SUCCESS POPUPS
    const params = new URLSearchParams(window.location.search);
    if(params.get('status') === 'success') {
        Swal.fire({
            title: 'Sovereign Success',
            text: `Intelligence Hub has been ${params.get('action')} correctly.`,
            icon: 'success',
            confirmButtonColor: '#518992',
            borderRadius: '30px'
        }).then(() => window.history.replaceState({}, '', 'admin_faqs.php'));
    }
</script>

<?php include('includes/footer.php'); ?>