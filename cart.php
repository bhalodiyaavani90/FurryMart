<?php
session_start();
include "db.php";

// Initialize cart if not exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$toast = null;
$toast_type = 'success';

// Handle all cart actions
if (isset($_POST['action'])) {
    $action = $_POST['action'];

    // ADD PHARMACY PRODUCT TO CART
    if ($action === 'add_pharmacy') {
        header('Content-Type: application/json');
        
        $product_id = intval($_POST['product_id']);
        $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;

        // Fetch pharmacy product details
        $q = mysqli_query($conn, "SELECT * FROM pharmacy_products WHERE id = $product_id AND is_available = 1");
        
        if ($row = mysqli_fetch_assoc($q)) {
            $cart_key = 'pharmacy_' . $product_id;
            
            if (isset($_SESSION['cart'][$cart_key])) {
                $_SESSION['cart'][$cart_key]['qty'] += $quantity;
            } else {
                $_SESSION['cart'][$cart_key] = [
                    'id' => $product_id,
                    'type' => 'pharmacy',
                    'name' => $row['product_name'],
                    'brand_name' => $row['brand_name'],
                    'image' => $row['image'],
                    'price' => (float)$row['price'],
                    'size_options' => $row['size_options'],
                    'category' => $row['category'],
                    'qty' => $quantity
                ];
            }
            
            echo json_encode(['status' => 'success', 'message' => 'Product added to cart']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Product not found']);
        }
        exit;
    }

    // ADD TO CART
    if ($action === 'add') {
        $vid = intval($_POST['variant_id']);
        $q = mysqli_query($conn, "SELECT p.name, p.base_image, v.price, v.weight_size, v.stock_qty 
                                  FROM product_variants v 
                                  JOIN products p ON p.id = v.product_id 
                                  WHERE v.id = $vid");
        if ($row = mysqli_fetch_assoc($q)) {
            // Check if product is out of stock
            if ($row['stock_qty'] <= 0) {
                echo json_encode(['status' => 'error', 'message' => 'This product is currently out of stock']);
                exit;
            }
            
            if (isset($_SESSION['cart'][$vid])) {
                // Check if increasing quantity exceeds available stock
                $new_qty = $_SESSION['cart'][$vid]['qty'] + 1;
                if ($new_qty > $row['stock_qty']) {
                    echo json_encode(['status' => 'error', 'message' => 'Only ' . $row['stock_qty'] . ' units available in stock']);
                    exit;
                }
                $_SESSION['cart'][$vid]['qty'] = $new_qty;
                // Ensure variant_id is set for legacy cart items
                if (!isset($_SESSION['cart'][$vid]['variant_id'])) {
                    $_SESSION['cart'][$vid]['variant_id'] = $vid;
                }
                if (!isset($_SESSION['cart'][$vid]['weight_size']) && isset($_SESSION['cart'][$vid]['weight'])) {
                    $_SESSION['cart'][$vid]['weight_size'] = $_SESSION['cart'][$vid]['weight'];
                }
                $toast = "Quantity updated in your cart";
            } else {
                $_SESSION['cart'][$vid] = [
                    'id' => $vid,
                    'variant_id' => $vid,  // Store variant_id for checkout
                    'type' => 'regular',
                    'name' => $row['name'],
                    'image' => $row['base_image'],
                    'price' => (float)$row['price'],
                    'weight' => $row['weight_size'],
                    'weight_size' => $row['weight_size'],
                    'qty' => 1
                ];
                $toast = "Item added to cart successfully";
            }
        }
    }

    // UPDATE QUANTITY
    if ($action === 'update' && isset($_POST['variant_id']) && isset($_POST['qty'])) {
        $vid = $_POST['variant_id'];
        if (isset($_SESSION['cart'][$vid])) {
            $requested_qty = max(1, intval($_POST['qty']));
            
            // Check if it's a pharmacy product
            if (strpos($vid, 'pharmacy_') === 0 || (isset($_SESSION['cart'][$vid]['type']) && $_SESSION['cart'][$vid]['type'] === 'pharmacy')) {
                // For pharmacy products, check if available
                $product_id = str_replace('pharmacy_', '', $vid);
                $stock_check = mysqli_query($conn, "SELECT is_available FROM pharmacy_products WHERE id = $product_id");
                if ($stock_check && $stock_row = mysqli_fetch_assoc($stock_check)) {
                    if ($stock_row['is_available'] == 1) {
                        $_SESSION['cart'][$vid]['qty'] = $requested_qty;
                        $toast = "Cart updated successfully";
                    } else {
                        $toast = "Product is no longer available";
                        $toast_type = 'error';
                    }
                } else {
                    $toast = "Product not found";
                    $toast_type = 'error';
                }
            } else {
                // For regular products, check stock availability
                $stock_check = mysqli_query($conn, "SELECT stock_qty FROM product_variants WHERE id = $vid");
                if ($stock_check && $stock_row = mysqli_fetch_assoc($stock_check)) {
                    if ($requested_qty > $stock_row['stock_qty']) {
                        $_SESSION['cart'][$vid]['qty'] = $stock_row['stock_qty'];
                        $toast = "Only " . $stock_row['stock_qty'] . " units available. Cart updated.";
                        $toast_type = 'error';
                    } else {
                        $_SESSION['cart'][$vid]['qty'] = $requested_qty;
                        $toast = "Cart updated successfully";
                    }
                } else {
                    $toast = "Product not found";
                    $toast_type = 'error';
                }
            }
        }
    }

    // INCREASE QUANTITY
    if ($action === 'increase' && isset($_POST['variant_id'])) {
        $vid = $_POST['variant_id'];
        if (isset($_SESSION['cart'][$vid])) {
            $new_qty = $_SESSION['cart'][$vid]['qty'] + 1;
            
            // Check if it's a pharmacy product
            if (strpos($vid, 'pharmacy_') === 0 || (isset($_SESSION['cart'][$vid]['type']) && $_SESSION['cart'][$vid]['type'] === 'pharmacy')) {
                // For pharmacy products, check if available
                $product_id = str_replace('pharmacy_', '', $vid);
                $stock_check = mysqli_query($conn, "SELECT is_available FROM pharmacy_products WHERE id = $product_id");
                if ($stock_check && $stock_row = mysqli_fetch_assoc($stock_check)) {
                    if ($stock_row['is_available'] == 1) {
                        $_SESSION['cart'][$vid]['qty'] = $new_qty;
                        $toast = "Quantity increased";
                    } else {
                        $toast = "Product is no longer available";
                        $toast_type = 'error';
                    }
                } else {
                    $toast = "Product not found";
                    $toast_type = 'error';
                }
            } else {
                // For regular products, check stock availability
                $stock_check = mysqli_query($conn, "SELECT stock_qty FROM product_variants WHERE id = $vid");
                if ($stock_check && $stock_row = mysqli_fetch_assoc($stock_check)) {
                    if ($new_qty > $stock_row['stock_qty']) {
                        $toast = "Maximum available stock reached (" . $stock_row['stock_qty'] . " units)";
                        $toast_type = 'error';
                    } else {
                        $_SESSION['cart'][$vid]['qty'] = $new_qty;
                        $toast = "Quantity increased";
                    }
                } else {
                    $toast = "Product not found";
                    $toast_type = 'error';
                }
            }
        }
    }

    // DECREASE QUANTITY
    if ($action === 'decrease' && isset($_POST['variant_id'])) {
        $vid = $_POST['variant_id'];
        if (isset($_SESSION['cart'][$vid]) && $_SESSION['cart'][$vid]['qty'] > 1) {
            $_SESSION['cart'][$vid]['qty']--;
            $toast = "Quantity decreased";
        }
    }

    // REMOVE ITEM
    if ($action === 'remove' && isset($_POST['variant_id'])) {
        $vid = $_POST['variant_id'];
        if (isset($_SESSION['cart'][$vid])) {
            unset($_SESSION['cart'][$vid]);
            $toast = "Item removed from cart";
            $toast_type = 'error';
        }
    }

    // CHECKOUT
    if ($action === 'checkout') {
        // Check if user is logged in (check multiple possible session variables)
        if (isset($_SESSION['user_email']) || isset($_SESSION['user_id']) || isset($_SESSION['username'])) {
            header("Location: checkout.php");
            exit();
        } else {
            $toast = "Login required before checkout";
            $toast_type = 'error';
        }
    }
}

// Get cart items
$cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];

// Calculate totals
$subtotal = 0;
if (!empty($cart)) {
    foreach ($cart as $item) {
        $subtotal += (float)$item['price'] * (int)$item['qty'];
    }
}

// Shipping logic: Free shipping above ₹499
$free_shipping_threshold = 499;
$shipping_charges = 0;
if ($subtotal > 0 && $subtotal < $free_shipping_threshold) {
    $shipping_charges = 40; // ₹40 shipping fee
}

// Calculate how much more needed for free shipping
$remaining_for_free_shipping = max(0, $free_shipping_threshold - $subtotal);
$free_shipping_progress = min(100, ($subtotal / $free_shipping_threshold) * 100);

// GST calculation (18%)
$gst_rate = 0.18;
$gst_amount = ($subtotal + $shipping_charges) * $gst_rate;

// Grand total
$total = $subtotal + $shipping_charges + $gst_amount;
?>

<?php include "includes/header.php"; ?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
:root { 
    --primary: #518992; --navy: #0f1c3f; --accent: #f87171; 
    --success: #22c55e; --gray: #94a3b8; --bg: #f8fafc;
}

body { font-family: 'Plus Jakarta Sans', sans-serif; background: var(--bg); color: var(--navy); overflow-x: hidden; }

/* --- PREMIUM ANIMATED HERO --- */
.hero-vault { 
    height: 280px; position: relative; overflow: hidden; 
    display: flex; align-items: center; justify-content: center; 
    color: #fff; margin-bottom: 50px; border-radius: 0 0 60px 60px; 
}
.hero-glimmer { 
    position: absolute; top: 0; left: 0; width: 100%; height: 100%; 
    background: linear-gradient(rgba(15,28,63,0.8), rgba(15,28,63,0.8)), 
                url('https://images.unsplash.com/photo-1607082348824-0a96f2a4b9da?auto=format&fit=crop&w=1350&q=80'); 
    background-size: cover; background-position: center; 
    animation: slowReflect 20s infinite alternate; z-index: -1; 
}
@keyframes slowReflect { from { transform: scale(1); } to { transform: scale(1.15); } }
.hero-vault h1 { font-size: 48px; font-weight: 900; letter-spacing: -2px; }
.hero-subtitle { 
    font-weight: 800; text-transform: uppercase; letter-spacing: 4px; 
    color: rgba(255,255,255,0.95); font-size: 13px; margin-top: 10px; 
}

/* --- CART LAYOUT --- */
.cart-container { 
    max-width: 1400px; margin: 0 auto; padding: 0 5% 80px; 
    display: grid; grid-template-columns: 1fr 400px; gap: 40px; 
}

/* --- EMPTY CART STATE --- */
.empty-cart {
    grid-column: 1 / -1;
    text-align: center; padding: 80px 40px;
    background: #fff; border-radius: 40px;
    box-shadow: 0 20px 60px rgba(15, 28, 63, 0.05);
    animation: fadeInUp 0.6s ease;
}
.empty-cart i { font-size: 80px; color: var(--gray); margin-bottom: 20px; opacity: 0.3; }
.empty-cart h3 { font-size: 28px; font-weight: 900; color: var(--navy); margin-bottom: 10px; }
.empty-cart p { color: var(--gray); margin-bottom: 30px; }
.btn-shop { 
    display: inline-block; padding: 18px 40px; background: var(--primary); 
    color: #fff; border-radius: 20px; text-decoration: none; 
    font-weight: 800; text-transform: uppercase; letter-spacing: 1px;
    transition: 0.3s; box-shadow: 0 10px 30px rgba(81, 137, 146, 0.2);
}
.btn-shop:hover { transform: translateY(-3px); box-shadow: 0 15px 40px rgba(81, 137, 146, 0.3); }

/* --- CART ITEMS SECTION --- */
.cart-items {
    background: #fff; border-radius: 40px; padding: 40px;
    box-shadow: 0 20px 60px rgba(15, 28, 63, 0.05);
    animation: fadeInLeft 0.6s ease;
}
.cart-header {
    display: flex; justify-content: space-between; align-items: center;
    margin-bottom: 30px; padding-bottom: 20px; border-bottom: 2px solid #f1f5f9;
}
.cart-header h2 { font-size: 28px; font-weight: 900; color: var(--navy); margin: 0; }
.cart-count { 
    background: var(--primary); color: #fff; padding: 8px 16px; 
    border-radius: 20px; font-weight: 800; font-size: 13px; 
}

/* --- CART ITEM CARD --- */
.cart-item {
    display: grid; grid-template-columns: 120px 1fr auto;
    gap: 25px; padding: 25px; background: #f8fafc;
    border-radius: 25px; margin-bottom: 20px;
    transition: 0.3s; animation: fadeInUp 0.5s ease;
    border: 2px solid transparent;
}
.cart-item:hover { 
    border-color: var(--primary); 
    box-shadow: 0 10px 30px rgba(81, 137, 146, 0.1); 
    transform: translateX(5px);
}

.item-image {
    width: 120px; height: 120px; background: #fff;
    border-radius: 20px; display: flex; align-items: center;
    justify-content: center; overflow: hidden;
}
.item-image img { 
    max-width: 90%; max-height: 90%; object-fit: contain; 
    transition: 0.5s; 
}
.cart-item:hover .item-image img { transform: scale(1.1); }

.item-details { display: flex; flex-direction: column; justify-content: center; }
.item-name { font-size: 18px; font-weight: 800; color: var(--navy); margin-bottom: 8px; }
.item-weight { 
    display: inline-block; padding: 5px 12px; background: #fff; 
    border-radius: 10px; font-size: 11px; font-weight: 800; 
    color: var(--primary); margin-bottom: 12px; width: fit-content;
}
.item-price { font-size: 24px; font-weight: 900; color: var(--primary); }

.item-actions { display: flex; flex-direction: column; gap: 12px; align-items: flex-end; }
.qty-control {
    display: flex; align-items: center; gap: 10px;
    background: #fff; padding: 8px 12px; border-radius: 15px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.05);
}
.qty-control input {
    width: 50px; text-align: center; border: none;
    font-weight: 800; font-size: 16px; color: var(--navy);
}
.qty-btn {
    width: 32px; height: 32px; border-radius: 50%;
    border: none; background: var(--primary); color: #fff;
    cursor: pointer; font-weight: 800; transition: 0.3s;
    display: flex; align-items: center; justify-content: center;
}
.qty-btn:hover { transform: scale(1.1); box-shadow: 0 4px 15px rgba(81, 137, 146, 0.3); }

.btn-remove {
    padding: 10px 20px; background: transparent; 
    color: var(--accent); border: 2px solid var(--accent);
    border-radius: 12px; font-weight: 800; cursor: pointer;
    transition: 0.3s; font-size: 12px; text-transform: uppercase;
}
.btn-remove:hover { 
    background: var(--accent); color: #fff; 
    box-shadow: 0 5px 20px rgba(248, 113, 113, 0.3); 
}

/* --- CART SUMMARY SIDEBAR --- */
.cart-summary {
    background: #fff; border-radius: 40px; padding: 40px;
    box-shadow: 0 20px 60px rgba(15, 28, 63, 0.05);
    height: fit-content; position: sticky; top: 20px;
    animation: fadeInRight 0.6s ease;
}
.summary-title { 
    font-size: 24px; font-weight: 900; color: var(--navy); 
    margin-bottom: 25px; padding-bottom: 20px; 
    border-bottom: 2px solid #f1f5f9; 
}

.summary-row {
    display: flex; justify-content: space-between; 
    padding: 15px 0; font-size: 15px; color: var(--gray);
    border-bottom: 1px solid #f8fafc;
}
.summary-row.total {
    font-size: 28px; font-weight: 900; color: var(--navy);
    border-bottom: none; margin-top: 20px; padding-top: 20px;
    border-top: 2px solid #f1f5f9;
}

.btn-checkout {
    width: 100%; padding: 20px; background: var(--success);
    color: #fff; border: none; border-radius: 20px;
    font-weight: 900; font-size: 16px; text-transform: uppercase;
    cursor: pointer; transition: 0.3s; margin-top: 25px;
    letter-spacing: 1px; box-shadow: 0 10px 30px rgba(34, 197, 94, 0.2);
}
.btn-checkout:hover { 
    transform: translateY(-3px); 
    box-shadow: 0 15px 40px rgba(34, 197, 94, 0.3); 
}

/* --- FREE SHIPPING PROGRESS BAR --- */
.shipping-progress-container {
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    padding: 20px;
    border-radius: 20px;
    margin-bottom: 25px;
    border: 2px solid #e2e8f0;
}

.shipping-message {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 12px;
    font-size: 13px;
    font-weight: 800;
}

.shipping-message.achieved {
    color: var(--success);
}

.shipping-message.pending {
    color: var(--primary);
}

.shipping-message i {
    font-size: 18px;
}

.progress-bar-wrapper {
    position: relative;
    width: 100%;
    height: 12px;
    background: #e2e8f0;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: inset 0 2px 4px rgba(0,0,0,0.1);
}

.progress-bar-fill {
    height: 100%;
    background: linear-gradient(90deg, #22c55e 0%, #16a34a 100%);
    border-radius: 10px;
    transition: width 0.8s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    box-shadow: 0 2px 8px rgba(34, 197, 94, 0.4);
}

.progress-bar-fill::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
    animation: shimmer 2s infinite;
}

@keyframes shimmer {
    0% { transform: translateX(-100%); }
    100% { transform: translateX(100%); }
}

.progress-percentage {
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
    font-size: 10px;
    font-weight: 900;
    color: #fff;
    text-shadow: 0 1px 2px rgba(0,0,0,0.2);
}

/* --- SUMMARY ROW ENHANCEMENTS --- */
.summary-row {
    display: flex; justify-content: space-between; 
    padding: 15px 0; font-size: 15px; color: var(--gray);
    border-bottom: 1px solid #f8fafc;
}

.summary-row .label {
    display: flex;
    align-items: center;
    gap: 8px;
}

.summary-row .label i {
    color: var(--primary);
    font-size: 14px;
}

.summary-row .value {
    font-weight: 800;
}

.summary-row.shipping-free .value {
    color: var(--success);
    font-weight: 900;
}

.summary-row.shipping-paid .value {
    color: var(--accent);
}

.summary-row.gst {
    font-size: 13px;
}

.summary-row.gst .label {
    color: #64748b;
}

.summary-row.total {
    font-size: 28px; font-weight: 900; color: var(--navy);
    border-bottom: none; margin-top: 20px; padding-top: 20px;
    border-top: 2px solid #f1f5f9;
}

.savings-badge {
    display: inline-block;
    background: rgba(34, 197, 94, 0.1);
    color: var(--success);
    padding: 4px 10px;
    border-radius: 8px;
    font-size: 11px;
    font-weight: 900;
    margin-left: 8px;
}

.btn-continue {
    display: block; text-align: center; padding: 15px;
    color: var(--primary); text-decoration: none;
    font-weight: 800; margin-top: 15px; transition: 0.3s;
}
.btn-continue:hover { color: var(--navy); }

/* --- TOAST NOTIFICATIONS --- */
.toast-container { position: fixed; bottom: 30px; right: 30px; z-index: 99999; }
.toast {
    background: var(--navy); color: #fff; padding: 18px 28px;
    border-radius: 18px; margin-top: 10px; font-weight: 800;
    display: flex; align-items: center; gap: 12px;
    box-shadow: 0 15px 40px rgba(15,28,63,0.3);
    animation: slideIn 0.5s forwards;
}
@keyframes slideIn {
    from { transform: translateX(100%); opacity: 0; }
    to { transform: translateX(0); opacity: 1; }
}
.toast.success { border-left: 5px solid var(--success); }
.toast.error { border-left: 5px solid var(--accent); }

/* --- ANIMATIONS --- */
@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(30px); }
    to { opacity: 1; transform: translateY(0); }
}
@keyframes fadeInLeft {
    from { opacity: 0; transform: translateX(-30px); }
    to { opacity: 1; transform: translateX(0); }
}
@keyframes fadeInRight {
    from { opacity: 0; transform: translateX(30px); }
    to { opacity: 1; transform: translateX(0); }
}

/* --- RESPONSIVE --- */
@media (max-width: 992px) {
    .cart-container { grid-template-columns: 1fr; }
    .cart-summary { position: static; }
    .cart-item { grid-template-columns: 100px 1fr; }
    .item-actions { grid-column: 1 / -1; flex-direction: row; justify-content: space-between; }
}
</style>

<div class="hero-vault animate__animated animate__fadeIn">
    <div class="hero-glimmer"></div>
    <div class="animate__animated animate__zoomIn" style="text-align: center;">
        <h1><i class="fas fa-shopping-cart"></i> Your Shopping Cart</h1>
        <p class="hero-subtitle">Review & Checkout Your Items</p>
    </div>
</div>

<div class="cart-container">
    <?php if (empty($cart)): ?>
        <div class="empty-cart animate__animated animate__fadeIn">
            <i class="fas fa-shopping-cart"></i>
            <h3>Your Cart is Empty</h3>
            <p>Looks like you haven't added any items yet. Start shopping now!</p>
            <a href="dog_food.php" class="btn-shop"><i class="fas fa-paw"></i> Start Shopping</a>
        </div>
    <?php else: ?>
        <!-- CART ITEMS -->
        <div class="cart-items">
            <div class="cart-header">
                <h2><i class="fas fa-shopping-bag"></i> Cart Items</h2>
                <span class="cart-count"><?php echo count($cart); ?> Items</span>
            </div>

            <?php 
            foreach($cart as $key => $item): 
                $type = isset($item['type']) ? $item['type'] : 'regular';
                $item_name = ($type == 'pharmacy') ? $item['name'] : $item['name'];
                $item_weight = ($type == 'pharmacy') ? (isset($item['size_options']) ? $item['size_options'] : 'N/A') : (isset($item['weight']) ? $item['weight'] : 'N/A');
                $item_brand = isset($item['brand_name']) ? $item['brand_name'] : '';
            ?>
            <div class="cart-item">
                <div class="item-image">
                    <img src="uploads/products/<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item_name); ?>">
                </div>
                
                <div class="item-details">
                    <?php if($item_brand): ?>
                        <div style="font-size: 11px; color: var(--primary); font-weight: 800; text-transform: uppercase; margin-bottom: 5px;">
                            <?php echo htmlspecialchars($item_brand); ?>
                        </div>
                    <?php endif; ?>
                    <div class="item-name">
                        <?php echo htmlspecialchars($item_name); ?>
                        <span style="display: inline-block; padding: 3px 8px; background: #f1f5f9; border-radius: 8px; font-size: 10px; font-weight: 700; color: #64748b; margin-left: 8px;">
                            <?php echo $type == 'pharmacy' ? 'Pharmacy' : 'Regular'; ?>
                        </span>
                    </div>
                    <span class="item-weight"><i class="fas fa-<?php echo $type == 'pharmacy' ? 'flask' : 'weight-hanging'; ?>"></i> <?php echo htmlspecialchars($item_weight); ?></span>
                    <div class="item-price">₹<?php echo number_format((float)$item['price'], 2); ?></div>
                </div>

                <div class="item-actions">
                    <form method="post" style="display: flex; gap: 10px; flex-direction: column;">
                        <input type="hidden" name="variant_id" value="<?php echo htmlspecialchars($key); ?>">
                        
                        <div class="qty-control">
                            <button type="submit" name="action" value="decrease" class="qty-btn">
                                <i class="fas fa-minus"></i>
                            </button>
                            <input type="text" value="<?php echo (int)$item['qty']; ?>" readonly>
                            <button type="submit" name="action" value="increase" class="qty-btn">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>

                        <button type="submit" name="action" value="remove" class="btn-remove" onclick="return confirm('Remove this item from cart?')">
                            <i class="fas fa-trash"></i> Remove
                        </button>
                    </form>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- CART SUMMARY -->
        <div class="cart-summary">
            <h3 class="summary-title">Order Summary</h3>
            
            <!-- FREE SHIPPING PROGRESS BAR -->
            <div class="shipping-progress-container">
                <?php if ($remaining_for_free_shipping > 0): ?>
                    <div class="shipping-message pending">
                        <i class="fas fa-truck"></i>
                        <span>Add ₹<?php echo number_format($remaining_for_free_shipping, 2); ?> more for FREE shipping!</span>
                    </div>
                <?php else: ?>
                    <div class="shipping-message achieved">
                        <i class="fas fa-check-circle"></i>
                        <span>Congratulations! You've unlocked FREE shipping! 🎉</span>
                    </div>
                <?php endif; ?>
                
                <div class="progress-bar-wrapper">
                    <div class="progress-bar-fill" style="width: <?php echo $free_shipping_progress; ?>%;">
                        <?php if ($free_shipping_progress >= 20): ?>
                            <span class="progress-percentage"><?php echo round($free_shipping_progress); ?>%</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <!-- SUBTOTAL -->
            <div class="summary-row">
                <span class="label">
                    <i class="fas fa-shopping-bag"></i>
                    Subtotal (<?php echo count($cart); ?> items)
                </span>
                <span class="value">₹<?php echo number_format($subtotal, 2); ?></span>
            </div>
            
            <!-- SHIPPING CHARGES -->
            <div class="summary-row <?php echo $shipping_charges == 0 ? 'shipping-free' : 'shipping-paid'; ?>">
                <span class="label">
                    <i class="fas fa-shipping-fast"></i>
                    Shipping Charges
                </span>
                <span class="value">
                    <?php if ($shipping_charges == 0): ?>
                        FREE <span class="savings-badge">SAVED ₹40</span>
                    <?php else: ?>
                        ₹<?php echo number_format($shipping_charges, 2); ?>
                    <?php endif; ?>
                </span>
            </div>
            
            <!-- GST -->
            <div class="summary-row gst">
                <span class="label">
                    <i class="fas fa-receipt"></i>
                    GST (18%)
                </span>
                <span class="value">₹<?php echo number_format($gst_amount, 2); ?></span>
            </div>
            
            <!-- TOTAL -->
            <div class="summary-row total">
                <span>Total Amount</span>
                <span>₹<?php echo number_format($total, 2); ?></span>
            </div>

            <form method="post">
                <button type="submit" name="action" value="checkout" class="btn-checkout">
                    <i class="fas fa-lock"></i> Proceed to Checkout
                </button>
            </form>

            <a href="dog_food.php" class="btn-continue">
                <i class="fas fa-arrow-left"></i> Continue Shopping
            </a>
        </div>
    <?php endif; ?>
</div>

<?php if ($toast): ?>
<div class="toast-container">
    <div class="toast <?php echo $toast_type; ?>">
        <i class="fas <?php echo $toast_type === 'success' ? 'fa-circle-check' : 'fa-circle-exclamation'; ?>"></i>
        <?php echo htmlspecialchars($toast); ?>
    </div>
</div>
<script>
setTimeout(() => {
    const toastEl = document.querySelector('.toast');
    if(toastEl) {
        toastEl.style.animation = 'slideIn 0.5s reverse forwards';
        setTimeout(() => {
            const container = document.querySelector('.toast-container');
            if(container) container.remove();
        }, 500);
    }
}, 3000);

<?php if($toast_type == 'error' && !isset($_SESSION['user_email']) && !isset($_SESSION['user_id']) && !isset($_SESSION['username'])): ?>
setTimeout(() => window.location.href = 'login.php', 2500);
<?php endif; ?>
</script>
<?php endif; ?>

<?php include "includes/footer.php"; ?>

