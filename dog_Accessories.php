<?php
include "db.php";
if (session_status() === PHP_SESSION_NONE) { session_start(); }

// 1. CAPTURE ALL INTELLIGENCE FILTERS
$current_stage = isset($_GET['stage']) ? mysqli_real_escape_string($conn, $_GET['stage']) : '';
$type_filter = isset($_GET['type']) ? mysqli_real_escape_string($conn, $_GET['type']) : '';
$price_filter = isset($_GET['price']) ? $_GET['price'] : '';

// 2. ACCESSORY TYPES ARRAY
$accessory_types = [
    
    'Collar' => ['collar', 'neck'],
    'Leash' => ['leash', 'lead', 'rope'],
    'Harness' => ['harness'],
    
];

// 3. MASTER SYNC QUERY (Products + Variants Joined)
$sql = "SELECT p.*, v.price, v.mrp, v.weight_size, v.stock_qty, v.id as variant_id 
        FROM products p 
        INNER JOIN product_variants v ON p.id = v.product_id 
        WHERE p.is_available = 1 AND p.main_cat_id = 4 AND p.sub_cat_id = 12";

// Filter Mapping Logic
if($current_stage) { $sql .= " AND p.life_stage = '$current_stage'"; }
if($type_filter && isset($accessory_types[$type_filter])) {
    $keywords = $accessory_types[$type_filter];
    $type_conditions = [];
    foreach($keywords as $keyword) {
        $type_conditions[] = "p.name LIKE '%$keyword%'";
    }
    $sql .= " AND (" . implode(' OR ', $type_conditions) . ")";
}

if($price_filter == 'under500') { $sql .= " AND v.price < 500"; }
elseif($price_filter == '500-1000') { $sql .= " AND v.price BETWEEN 500 AND 1000"; }
elseif($price_filter == 'above1000') { $sql .= " AND v.price > 1000"; }

$sql .= " ORDER BY v.stock_qty DESC, p.id DESC"; 
$result = mysqli_query($conn, $sql);
function isInWishlist($conn, $pid) {
    if(!isset($_SESSION['user_email'])) return false;
    $email = $_SESSION['user_email'];
    $check = mysqli_query($conn, "SELECT id FROM wishlist WHERE user_email = '$email' AND product_id = $pid");
    return mysqli_num_rows($check) > 0;
}
?>

<?php include "includes/header.php"; ?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
    :root { 
        --primary: #518992; --navy: #0f1c3f; --accent: #f87171; 
        --veg: #22c55e; --nonveg: #ef4444; --gray: #94a3b8; --bg: #f8fafc;
    }
    
    body { font-family: 'Plus Jakarta Sans', sans-serif; background: var(--bg); color: var(--navy); overflow-x: hidden; }

    /* --- PREMIUM ANIMATED HERO --- */
    .hero-vault { height: 350px; position: relative; overflow: hidden; display: flex; align-items: center; justify-content: center; color: #fff; margin-bottom: 50px; border-radius: 0 0 60px 60px; }
    .hero-glimmer { position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: linear-gradient(rgba(15,28,63,0.7), rgba(15,28,63,0.7)), url('uploads/productpageHero/dogaccHero.jpg'); background-size: cover; background-position: center; animation: slowReflect 20s infinite alternate; z-index: -1; }
    @keyframes slowReflect { from { transform: scale(1); } to { transform: scale(1.15); } }
    .hero-vault h1 { font-size: 56px; font-weight: 900; letter-spacing: -2px; }

    .catalog-viewport { display: grid; grid-template-columns: 240px 1fr; gap: 30px; padding: 0 5% 60px; }

    /* --- SIDEBAR FILTERS --- */
    .sidebar-filters { background: #fff; padding: 25px; border-radius: 20px; height: fit-content; position: sticky; top: 20px; box-shadow: 0 5px 20px rgba(0,0,0,0.03); border: 1px solid #f1f5f9; }
    .filter-group { border-bottom: 1px solid #f1f5f9; padding: 18px 0; }
    .filter-group h4 { font-size: 10px; font-weight: 800; text-transform: uppercase; color: var(--gray); margin-bottom: 12px; }
    .st-link { display: block; padding: 10px 15px; background: #f8fafc; border-radius: 10px; margin-bottom: 6px; color: var(--navy); text-decoration: none; font-weight: 700; transition: 0.3s; font-size: 11px; }
    .st-link:hover, .st-link.active { background: var(--primary); color: #fff; transform: translateX(5px); }

    /* --- FRONT VIEW: GROOM & ZOOM CARDS --- */
    .product-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 25px; }
    .product-card { 
        background: #fff; border-radius: 25px; padding: 20px; 
        transition: all 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275); 
        position: relative; border: 1px solid #f1f5f9; 
        display: flex; flex-direction: column; 
        height: 440px; overflow: hidden; cursor: pointer; 
    }
    .product-card:hover { transform: translateY(-12px) scale(1.02); box-shadow: 0 25px 50px rgba(15, 28, 63, 0.12); border-color: var(--primary); }

    .img-box { height: 180px; overflow: hidden; border-radius: 15px; margin-bottom: 15px; display: flex; align-items: center; justify-content: center; background: #fff; }
    .product-img { max-width: 90%; max-height: 90%; object-fit: contain; transition: 0.8s ease; }
    .product-card:hover .product-img { transform: scale(1.15); }

    .prod-brand { font-size: 10px; color: var(--primary); font-weight: 800; text-transform: uppercase; margin-bottom: 5px; }
    .prod-name { font-size: 15px; font-weight: 700; height: 42px; overflow: hidden; line-height: 1.4; color: var(--navy); margin-bottom: auto; }
    
    .price-zone { margin: 15px 0; display: flex; align-items: baseline; gap: 8px; }
    .price-main { font-size: 22px; font-weight: 900; color: var(--navy); }
    
    .btn-action { background: var(--accent); color: #fff; width: 100%; padding: 14px; border-radius: 15px; font-weight: 800; text-transform: uppercase; border: none; font-size: 12px; }

    /* --- LOADING SPINNER ROUND --- */
    .loader-overlay { position: fixed; inset: 0; background: rgba(255,255,255,0.8); backdrop-filter: blur(10px); display: none; z-index: 10001; align-items: center; justify-content: center; }
    .spinner-round { width: 60px; height: 60px; border: 6px solid #f1f5f9; border-top: 6px solid var(--primary); border-radius: 50%; animation: spin 1s linear infinite; }
    @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }

    /* --- THE ULTIMATE PROFESSIONAL MODAL --- */
    .qv-modal { position: fixed; inset: 0; background: rgba(15, 28, 63, 0.75); backdrop-filter: blur(10px); display: none; z-index: 10000; align-items: center; justify-content: center; padding: 20px; }
    .qv-content { background: #fff; width: 100%; max-width: 1200px; height: 99vh; border-radius: 45px; display: grid; grid-template-columns: 1.1fr 1.3fr; overflow: hidden; position: relative; animation: zoomIn 0.4s; border: 1px solid #fff; box-shadow: 0 50px 100px rgba(0,0,0,0.3); }
    .qv-close { position: absolute; top: 25px; right: 30px; font-size: 35px; cursor: pointer; color: var(--gray); z-index: 100; transition: 0.3s; }

    .modal-visual-vault { padding: 40px; background: #fff; display: flex; align-items: center; justify-content: center; border-right: 1px solid #f1f5f9; }
    .modal-visual-vault img { max-width: 95%; max-height: 500px; object-fit: contain; }

    /* Right Console - NO OVERLAP FLEX ARCHITECTURE */
    .modal-console { display: flex; flex-direction: column; height: 100%; position: relative; background: #fff; }
    .console-header { padding: 50px 50px 20px; flex-shrink: 0; border-bottom: 1px solid #f8fafc; }
    
    /* SCROLL ZONE: Product Dash */
    .console-scroll-zone { padding: 30px 50px; overflow-y: auto; flex-grow: 1; scroll-behavior: smooth; }
    .console-scroll-zone::-webkit-scrollbar { width: 6px; }
    .console-scroll-zone::-webkit-scrollbar-thumb { background: var(--primary); border-radius: 10px; }

    /* FOOTER: LOCKED AT BOTTOM */
    .console-footer { padding: 20px 50px 40px; background: #fff; border-top: 1px solid #f8fafc; flex-shrink: 0; }

    /* Dashboard Widgets */
    .dash-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 25px; }
    .dash-item { background: #f8fafc; padding: 18px; border-radius: 20px; border: 1px solid #f1f5f9; }
    .dash-label { font-size: 9px; font-weight: 800; color: var(--gray); text-transform: uppercase; margin-bottom: 5px; display: block; letter-spacing: 1px; }
    .dash-value { font-weight: 900; font-size: 14px; color: var(--navy); display: flex; align-items: center; gap: 8px; }

    .badge-pill { display: inline-block; padding: 5px 15px; border-radius: 25px; font-size: 11px; font-weight: 800; text-transform: uppercase; margin-right: 8px; }
    /* --- WISHLIST & TOAST STYLING --- */
.wishlist-btn {
    position: absolute; top: 20px; right: 20px; z-index: 10;
    background: #fff; width: 35px; height: 35px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    box-shadow: 0 4px 15px rgba(0,0,0,0.08); cursor: pointer;
    transition: all 0.3s ease; border: 1px solid #f1f5f9; color: var(--gray);
}
.wishlist-btn:hover { transform: scale(1.1); color: var(--accent); }
.wishlist-btn.active i { font-weight: 900; color: var(--accent); }

/* SOLD OUT Badge */
.sold-out-badge {
    position: absolute;
    top: 15px;
    left: 15px;
    background: linear-gradient(135deg, #ef4444, #dc2626);
    color: white;
    padding: 8px 16px;
    border-radius: 25px;
    font-size: 11px;
    font-weight: 900;
    letter-spacing: 1px;
    z-index: 10;
    box-shadow: 0 4px 15px rgba(239, 68, 68, 0.4);
    animation: pulse 2s infinite;
}
.product-card.out-of-stock {
    opacity: 0.7;
    position: relative;
}
.product-card.out-of-stock::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(255, 255, 255, 0.6);
    border-radius: 25px;
    pointer-events: none;
}
.product-card.out-of-stock .btn-action {
    background: #94a3b8;
    cursor: not-allowed;
    opacity: 0.6;
}
.product-card.out-of-stock .btn-action:hover {
    transform: none;
    box-shadow: none;
}
@keyframes pulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.05); }
}

/* Toast Notification Container */
#toast-container {
    position: fixed; bottom: 30px; right: 30px; z-index: 99999;
}
.toast {
    background: var(--navy); color: #fff; padding: 15px 25px;
    border-radius: 15px; margin-top: 10px; font-weight: 700;
    display: flex; align-items: center; gap: 12px;
    box-shadow: 0 10px 30px rgba(15,28,63,0.2);
    animation: slideIn 0.5s forwards;
}
@keyframes slideIn {
    from { transform: translateX(100%); opacity: 0; }
    to { transform: translateX(0); opacity: 1; }
}
.toast.success { border-left: 5px solid var(--veg); }
.toast.error { border-left: 5px solid var(--accent); }
    @media (max-width: 992px) {
        .catalog-viewport { grid-template-columns: 1fr; }
        .qv-content { grid-template-columns: 1fr; height: 95vh; }
        .modal-visual-vault { display: none; }
    }
    
</style>

<div id="loaderOverlay" class="loader-overlay"><div class="spinner-round"></div></div>

<div class="hero-vault animate__animated animate__fadeIn">
    <div class="hero-glimmer"></div>
    <div class="animate__animated animate__zoomIn" style="text-align: center;">
        <h1>Dog Accessories Hub</h1>
        <p style="font-weight: 800; text-transform: uppercase; letter-spacing: 4px; color: rgba(255,255,255,0.95);">Premium Accessories Collection</p>
    </div>
</div>

<div class="catalog-viewport">
    <aside class="sidebar-filters">
        <h3 style="font-weight: 900; margin-bottom: 20px; font-size: 15px;">Filters</h3>
        <div class="filter-group">
            <h4>Life Stage mapping</h4>
            <a href="dog_Accessories.php" class="st-link <?php if(!$current_stage) echo 'active'; ?>">All Stages</a>
            <a href="?stage=Puppy" class="st-link <?php if($current_stage=='Puppy') echo 'active'; ?>">Puppy</a>
            <a href="?stage=Adult" class="st-link <?php if($current_stage=='Adult') echo 'active'; ?>">Adult</a>
            <a href="?stage=Senior" class="st-link <?php if($current_stage=='Senior') echo 'active'; ?>">Senior</a>
        </div>
        <div class="filter-group">
            <h4>Accessory Type</h4>
            <a href="dog_Accessories.php" class="st-link <?php if(!$type_filter) echo 'active'; ?>">All Types</a>
      
            <a href="?type=Collar" class="st-link <?php if($type_filter=='Collar') echo 'active'; ?>">🎀 Collars</a>
            <a href="?type=Leash" class="st-link <?php if($type_filter=='Leash') echo 'active'; ?>">🦮 Leashes</a>
            <a href="?type=Harness" class="st-link <?php if($type_filter=='Harness') echo 'active'; ?>">🎒 Harnesses</a>
           
        </div>
        <div class="filter-group" style="border:none;">
            <h4>Price Bracket mapping</h4>
            <a href="?price=under500" class="st-link <?php if($price_filter=='under500') echo 'active'; ?>">Under ₹500</a>
            <a href="?price=500-1000" class="st-link <?php if($price_filter=='500-1000') echo 'active'; ?>">₹500 - ₹1000</a>
            <a href="?price=above1000" class="st-link <?php if($price_filter=='above1000') echo 'active'; ?>">Above ₹1000</a>
        </div>
    </aside>

 <main class="product-grid">
    <?php while($p = mysqli_fetch_assoc($result)): 
        $in_wish = isInWishlist($conn, $p['id']);
        $is_out_of_stock = ($p['stock_qty'] <= 0);
    ?>
    <div class="product-card <?php echo $is_out_of_stock ? 'out-of-stock' : ''; ?> animate__animated animate__fadeInUp">
        <?php if($is_out_of_stock): ?>
        <div class="sold-out-badge">
            <i class="fas fa-ban"></i> SOLD OUT
        </div>
        <?php endif; ?>
        
        <div class="wishlist-btn <?php echo $in_wish ? 'active' : ''; ?>" 
             onclick="toggleWishlist(this, <?php echo $p['id']; ?>)">
            <i class="<?php echo $in_wish ? 'fa-solid' : 'fa-regular'; ?> fa-heart"></i>
        </div>

        <div onclick='<?php echo !$is_out_of_stock ? "startIntelligenceLoading(" . htmlspecialchars(json_encode($p), ENT_QUOTES, "UTF-8") . ")" : ""; ?>'>
            <div class="img-box">
                <img src="uploads/products/<?php echo $p['base_image']; ?>" class="product-img">
            </div>
            <div class="prod-brand"><?php echo $p['brand_name']; ?></div>
            <h3 class="prod-name"><?php echo $p['name']; ?></h3>
            <div class="price-zone">
                <span class="price-main">₹<?php echo number_format($p['price']); ?></span>
            </div>
        </div>
        <button class="btn-action" 
                <?php echo $is_out_of_stock ? 'disabled' : ''; ?>
                onclick="<?php echo !$is_out_of_stock ? "addToCart(" . $p['variant_id'] . ", '" . addslashes($p['name']) . "', '" . $p['base_image'] . "', " . $p['price'] . ", '" . $p['weight_size'] . "')" : 'return false;'; ?>">
            <?php echo $is_out_of_stock ? '<i class="fas fa-ban"></i> SOLD OUT' : 'ADD TO BAG'; ?>
        </button>

    </div>
    <?php endwhile; ?>
</main>
</div>

<div id="qvBox" class="qv-modal">
    <div class="qv-content">
        <span class="qv-close" onclick="closeQuickView()">&times;</span>
        <div class="modal-visual-vault"><img id="qv-img" src=""></div>
        
        <div class="modal-console">
            <div class="console-header">
                <div id="qv-brand" style="color:var(--primary); font-weight:900; text-transform:uppercase; font-size:12px; margin-bottom: 5px;"></div>
                <h2 id="qv-name" style="font-size:32px; font-weight:900; margin:0 0 10px; line-height:1.1; color:var(--navy);"></h2>
                <div style="display: flex; align-items: center; gap: 10px;">
                    <span id="qv-price" style="font-size:34px; font-weight:900; color:var(--primary);"></span>
                    <span id="qv-stage-badge" class="badge-pill" style="background:#f1f5f9; color:var(--navy);"></span>
                </div>
            </div>

            <div class="console-scroll-zone">
                <h4 style="margin-bottom:10px; color:var(--gray); text-transform:uppercase; font-size:10px; letter-spacing:1px;">Product Intelligence</h4>
                <p id="qv-desc" style="font-size:15px; color:#64748b; line-height:1.8; margin-bottom:30px;"></p>

                <div class="dash-grid">
                    <div class="dash-item">
                        <small class="dash-label">Market Value (MRP)</small>
                        <div id="qv-mrp" class="dash-value" style="text-decoration: line-through; color:var(--gray);"></div>
                    </div>
                    <div class="dash-item">
                        <small class="dash-label">Synchronized Saving</small>
                        <div id="qv-save" class="dash-value" style="color:var(--veg);"></div>
                    </div>
                    <div class="dash-item">
                        <small class="dash-label">Inventory Logic</small>
                        <div id="qv-stock" class="dash-value"><i class="fas fa-boxes-stacked"></i> </div>
                    </div>
                    <div class="dash-item">
                        <small class="dash-label">Net Weight Capacity</small>
                        <div id="qv-weight" class="dash-value"><i class="fas fa-weight-hanging"></i> </div>
                    </div>
                </div>
            </div>
            
            <div class="console-footer">
                <button class="btn-action" onclick="addToCartFromModal()" style="padding:22px; font-size:16px; border-radius:20px; width:100%; box-shadow: 0 15px 30px rgba(81, 137, 146, 0.25);">DEPLOY TO BAG</button>
            </div>
        </div>
    </div>
</div>

<script>
/**
 * FURRYMART - Product Intelligence Console Logic
 * Refined for smoother transitions, currency formatting, and accessibility.
 */

const qvBox = document.getElementById('qvBox');
const loader = document.getElementById('loaderOverlay');

/**
 * 1. Helper: Format Currency (INR)
 * Converts numbers into the standard ₹X,XXX format
 */
const formatINR = (amount) => {
    return new Intl.NumberFormat('en-IN', {
        style: 'currency',
        currency: 'INR',
        maximumFractionDigits: 0
    }).format(amount);
};

/**
 * 2. Main Entry: Start Loader
 * Displays the spinner before opening the modal
 */
function startIntelligenceLoading(p) {
    loader.style.display = 'flex';
    
    // Simulate data synchronization for better UX
    setTimeout(() => {
        loader.style.display = 'none';
        launchQuickView(p);
    }, 600);
}

/**
 * 3. Populate and Show Modal
 */
// Store current product data globally for cart actions
let currentProduct = null;

function launchQuickView(p) {
    // Store product reference
    currentProduct = p;
    
    // Reset internal scroll so the user starts at the top
    const scrollZone = document.querySelector('.console-scroll-zone');
    if(scrollZone) scrollZone.scrollTop = 0;

    // Image Deployment with a smooth fade-in
    const modalImg = document.getElementById('qv-img');
    modalImg.style.opacity = '0'; 
    modalImg.src = 'uploads/products/' + p.base_image;
    modalImg.onload = () => { 
        modalImg.style.transition = 'opacity 0.4s ease';
        modalImg.style.opacity = '1'; 
    };

    // Text Content Update
    document.getElementById('qv-brand').innerText = p.brand_name || 'Premium Brand';
    document.getElementById('qv-name').innerText = p.name;
    
    // Pricing & Savings Calculation
    const price = parseFloat(p.price) || 0;
    const mrp = parseFloat(p.mrp) || 0;
    const savings = mrp - price;
    const savePercent = mrp > 0 ? Math.round((savings / mrp) * 100) : 0;

    document.getElementById('qv-price').innerText = formatINR(price);
    document.getElementById('qv-mrp').innerText = formatINR(mrp);
    
    // Dynamic Savings Label
    const saveElement = document.getElementById('qv-save');
    if (savings > 0) {
        saveElement.innerHTML = `SAVE ${formatINR(savings)} <small style="font-size:10px; opacity:0.8;">(${savePercent}% OFF)</small>`;
        saveElement.style.color = 'var(--veg)';
    } else {
        saveElement.innerText = 'Best Price Guaranteed';
    }

    // Detail Widgets
    document.getElementById('qv-desc').innerText = p.description || 'Product specifications are being updated.';
    document.getElementById('qv-weight').innerHTML = `<i class="fas fa-weight-hanging" style="color:var(--primary)"></i> ${p.weight_size}`;
    
    // Stock Logic: Red text if stock is low (Urgency)
    const stockQty = parseInt(p.stock_qty) || 0;
    const stockEl = document.getElementById('qv-stock');
    if (stockQty <= 5 && stockQty > 0) {
        stockEl.style.color = 'var(--nonveg)';
        stockEl.innerHTML = `<i class="fas fa-fire-flame-curved"></i> Only ${stockQty} Left - High Demand`;
    } else {
        stockEl.style.color = 'var(--navy)';
        stockEl.innerHTML = `<i class="fas fa-boxes-stacked" style="color:var(--primary)"></i> ${stockQty} Units Optimized`;
    }

    // Badge Stylizing
    document.getElementById('qv-stage-badge').innerText = p.life_stage;

    // Open Modal and Lock Body Scroll
    qvBox.style.display = 'flex';
    document.body.style.overflow = 'hidden'; 
}

/**
 * 4. Close Logic
 */
function closeQuickView() {
    qvBox.style.display = 'none';
    document.body.style.overflow = 'auto'; // Restore background scroll
}

// Close when clicking outside the modal box
window.addEventListener('click', function(e) {
    if (e.target === qvBox) closeQuickView();
});

// Close when pressing the Escape key
window.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && qvBox.style.display === 'flex') {
        closeQuickView();
    }
});
/**
 * ADD TO CART FUNCTIONALITY
 * Sends product data to cart.php via POST request
 */
function addToCart(variantId, name, image, price, weight) {
    const formData = new FormData();
    formData.append('action', 'add');
    formData.append('variant_id', variantId);
    
    fetch('cart.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        showToast("✓ Added to Cart Successfully!", "success");
        updateHeaderCounts(); // Update cart count in header
        
        // Optional: Show quick action
        setTimeout(() => {
            const goToCart = confirm("Item added to cart! Go to cart now?");
            if (goToCart) {
                window.location.href = 'cart.php';
            }
        }, 500);
    })
    .catch(err => {
        console.error(err);
        showToast("Failed to add item to cart", "error");
    });
}

/**
 * ADD TO CART FROM MODAL
 * Uses the stored currentProduct data
 */
function addToCartFromModal() {
    if (!currentProduct) {
        showToast("Product data not available", "error");
        return;
    }
    
    addToCart(
        currentProduct.variant_id,
        currentProduct.name,
        currentProduct.base_image,
        currentProduct.price,
        currentProduct.weight_size
    );
}

function toggleWishlist(element, productId) {
    fetch('wishlist_action.php?product_id=' + productId)
    .then(response => response.json())
    .then(data => {
        if (data.status === 'not_logged_in') {
            // PROFESSIONAL NOTIFICATION & 3 SECOND REDIRECT
            showToast("Login Required! Redirecting to login page...", "error");
            setTimeout(() => { 
                window.location.href = 'login.php'; 
            }, 3000); 
            return;
        }

        if (data.status === 'added') {
            element.classList.add('active');
            element.querySelector('i').classList.replace('fa-regular', 'fa-solid');
            updateHeaderCounts();
            showToast("Added to FurryMart Wishlist ❤️", "success");
            
        } else if (data.status === 'removed') {
            element.classList.remove('active');
            element.querySelector('i').classList.replace('fa-solid', 'fa-regular');
            updateHeaderCounts();
            showToast("Removed from Wishlist", "success");
        }
    })
    .catch(err => {
        console.error(err);
        showToast("Server Synchronization Error", "error");
    });
}

// --- TOAST SYSTEM ---
function showToast(message, type) {
    let container = document.getElementById('toast-container');
    if (!container) {
        container = document.createElement('div');
        container.id = 'toast-container';
        document.body.appendChild(container);
    }

    const toast = document.createElement('div');
    toast.className = `toast ${type}`;
    const icon = type === 'success' ? 'fa-circle-check' : 'fa-circle-exclamation';
    toast.innerHTML = `<i class="fas ${icon}"></i> ${message}`;
    
    container.appendChild(toast);

    // Auto-remove after 3 seconds
    setTimeout(() => {
        toast.style.animation = 'slideIn 0.5s reverse forwards';
        setTimeout(() => toast.remove(), 500);
    }, 3000);
}
</script>
<?php include "includes/footer.php"; ?>
