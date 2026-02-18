<?php 
include "db.php";
if (session_status() === PHP_SESSION_NONE) { session_start(); }

$search_query = isset($_GET['query']) ? mysqli_real_escape_string($conn, $_GET['query']) : '';

if(empty($search_query)) {
    header("Location: index.php");
    exit;
}

// Search in regular products
$product_sql = "SELECT p.*, v.price, v.mrp, v.weight_size, v.stock_qty, v.id as variant_id
                FROM products p 
                INNER JOIN product_variants v ON p.id = v.product_id
                WHERE p.is_available = 1 
                AND (p.name LIKE '%$search_query%' 
                     OR p.brand_name LIKE '%$search_query%' 
                     OR p.description LIKE '%$search_query%')
                GROUP BY p.id
                ORDER BY 
                    CASE 
                        WHEN p.name LIKE '$search_query%' THEN 1
                        WHEN p.name LIKE '%$search_query%' THEN 2
                        ELSE 3
                    END,
                    v.stock_qty DESC";

$product_results = mysqli_query($conn, $product_sql);
if(!$product_results) {
    die("Product query error: " . mysqli_error($conn));
}

// Search in pharmacy products
$pharmacy_sql = "SELECT * FROM pharmacy_products
                 WHERE status = 'active'
                 AND (product_name LIKE '%$search_query%' 
                      OR brand LIKE '%$search_query%' 
                      OR description LIKE '%$search_query%'
                      OR category LIKE '%$search_query%')
                 ORDER BY 
                    CASE 
                        WHEN product_name LIKE '$search_query%' THEN 1
                        WHEN product_name LIKE '%$search_query%' THEN 2
                        ELSE 3
                    END";

$pharmacy_results = mysqli_query($conn, $pharmacy_sql);

// Calculate total results
$product_count = $product_results ? mysqli_num_rows($product_results) : 0;
$pharmacy_count = ($pharmacy_results && $pharmacy_results !== false) ? mysqli_num_rows($pharmacy_results) : 0;
$total_results = $product_count + $pharmacy_count;

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
    
    body { font-family: 'Plus Jakarta Sans', sans-serif; background: var(--bg); color: var(--navy); }

    .search-header {
        background: linear-gradient(135deg, var(--primary), #3a6b74);
        color: white;
        padding: 40px 5%;
        text-align: center;
    }
    .search-header h1 { font-size: 42px; font-weight: 900; margin: 0 0 10px; }
    .search-header p { font-size: 18px; opacity: 0.95; }

    .search-container { padding: 40px 5%; max-width: 1400px; margin: 0 auto; }
    
    .section-title {
        font-size: 24px;
        font-weight: 800;
        color: var(--navy);
        margin: 30px 0 20px;
        padding-bottom: 10px;
        border-bottom: 3px solid var(--primary);
        display: inline-block;
    }

    .product-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 25px; margin-bottom: 40px; }
    
    .product-card {
        background: #fff; border-radius: 20px; padding: 20px;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        position: relative; border: 1px solid #f1f5f9;
        display: flex; flex-direction: column; height: 420px; cursor: pointer;
    }
    .product-card:hover { transform: translateY(-10px); box-shadow: 0 20px 40px rgba(15, 28, 63, 0.12); border-color: var(--primary); }

    .img-box { height: 180px; overflow: hidden; border-radius: 15px; margin-bottom: 15px; display: flex; align-items: center; justify-content: center; background: #fafafa; }
    .product-img { max-width: 85%; max-height: 85%; object-fit: contain; transition: 0.6s ease; }
    .product-card:hover .product-img { transform: scale(1.1); }

    .prod-brand { font-size: 10px; color: var(--primary); font-weight: 800; text-transform: uppercase; margin-bottom: 5px; }
    .prod-name { font-size: 15px; font-weight: 700; height: 42px; overflow: hidden; line-height: 1.4; color: var(--navy); margin-bottom: auto; }
    .price-zone { margin: 15px 0; display: flex; align-items: baseline; gap: 8px; }
    .price-main { font-size: 22px; font-weight: 900; color: var(--navy); }
    .btn-action { background: var(--accent); color: #fff; width: 100%; padding: 14px; border-radius: 12px; font-weight: 800; text-transform: uppercase; border: none; font-size: 12px; cursor: pointer; }

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
    }
    .product-card.out-of-stock::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(255, 255, 255, 0.6);
        border-radius: 20px;
        pointer-events: none;
    }
    .product-card.out-of-stock .btn-action {
        background: #94a3b8;
        cursor: not-allowed;
        opacity: 0.6;
    }
    @keyframes pulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.05); }
    }

    .no-results {
        text-align: center;
        padding: 80px 20px;
        background: white;
        border-radius: 20px;
        margin: 40px 0;
    }
    .no-results i { font-size: 100px; color: var(--gray); margin-bottom: 20px; }
    .no-results h3 { font-size: 28px; font-weight: 900; color: var(--navy); margin-bottom: 10px; }
    .no-results p { color: var(--gray); font-size: 16px; }

    .pharmacy-badge {
        position: absolute;
        top: 15px;
        left: 15px;
        background: #10b981;
        color: white;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 10px;
        font-weight: 800;
        text-transform: uppercase;
    }
</style>

<div class="search-header">
    <h1><i class="fas fa-search"></i> Search Results</h1>
    <p>Found <strong><?php echo $total_results; ?></strong> results for "<?php echo htmlspecialchars($search_query); ?>"</p>
</div>

<div class="search-container">
    <?php if($total_results == 0): ?>
        <div class="no-results">
            <i class="fas fa-search"></i>
            <h3>No Results Found</h3>
            <p>We couldn't find any products matching "<?php echo htmlspecialchars($search_query); ?>". Try different keywords or browse our categories.</p>
        </div>
    <?php else: ?>
        
        <?php if($product_results && mysqli_num_rows($product_results) > 0): ?>
            <h2 class="section-title">Products</h2>
            <div class="product-grid">
                <?php 
                mysqli_data_seek($product_results, 0);
                while($p = mysqli_fetch_assoc($product_results)): 
                    $in_wish = isInWishlist($conn, $p['id']);
                    $is_out_of_stock = ($p['stock_qty'] <= 0);
                ?>
                <div class="product-card <?php echo $is_out_of_stock ? 'out-of-stock' : ''; ?>" onclick='<?php echo !$is_out_of_stock ? "openProductDetail(" . json_encode($p) . ")" : ""; ?>'>
                    <?php if($is_out_of_stock): ?>
                    <div class="sold-out-badge">
                        <i class="fas fa-ban"></i> SOLD OUT
                    </div>
                    <?php endif; ?>
                    
                    <div class="img-box">
                        <img src="uploads/products/<?php echo $p['base_image']; ?>" class="product-img">
                    </div>
                    <div class="prod-brand"><?php echo $p['brand_name']; ?></div>
                    <h3 class="prod-name"><?php echo $p['name']; ?></h3>
                    <div class="price-zone">
                        <span class="price-main">₹<?php echo number_format($p['price']); ?></span>
                    </div>
                    <button class="btn-action" 
                            <?php echo $is_out_of_stock ? 'disabled' : ''; ?>
                            onclick="<?php echo !$is_out_of_stock ? "event.stopPropagation(); addToCart(" . $p['variant_id'] . ", '" . addslashes($p['name']) . "', '" . $p['base_image'] . "', " . $p['price'] . ", '" . $p['weight_size'] . "')" : 'event.stopPropagation(); return false;'; ?>">
                        <?php echo $is_out_of_stock ? '<i class="fas fa-ban"></i> SOLD OUT' : 'ADD TO CART'; ?>
                    </button>
                </div>
                <?php endwhile; ?>
            </div>
        <?php endif; ?>

        <?php if($pharmacy_results && mysqli_num_rows($pharmacy_results) > 0): ?>
            <h2 class="section-title">Pharmacy Products</h2>
            <div class="product-grid">
                <?php 
                mysqli_data_seek($pharmacy_results, 0);
                while($ph = mysqli_fetch_assoc($pharmacy_results)): 
                ?>
                <a href="pharmacy.php?product_id=<?php echo $ph['id']; ?>" class="product-card" style="text-decoration: none; color: inherit;">
                    <span class="pharmacy-badge"><i class="fas fa-prescription-bottle"></i> Pharmacy</span>
                    <div class="img-box">
                        <img src="<?php echo $ph['image_url'] ? 'uploads/pharmacy/'.$ph['image_url'] : 'uploads/default-pharmacy.jpg'; ?>" class="product-img">
                    </div>
                    <div class="prod-brand"><?php echo $ph['brand'] ?? 'Pharmacy'; ?></div>
                    <h3 class="prod-name"><?php echo $ph['product_name']; ?></h3>
                    <div class="price-zone">
                        <span class="price-main">₹<?php echo number_format($ph['price']); ?></span>
                    </div>
                    <button class="btn-action">VIEW DETAILS</button>
                </a>
                <?php endwhile; ?>
            </div>
        <?php endif; ?>

    <?php endif; ?>
</div>

<script>
function openProductDetail(product) {
    // You can implement quick view or redirect to product page
    console.log('Open product:', product);
}

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
        alert('Added to cart successfully!');
        updateHeaderCounts();
    })
    .catch(err => {
        console.error(err);
        alert('Failed to add to cart');
    });
}
</script>

<?php include "includes/footer.php"; ?>
