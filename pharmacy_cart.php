<?php
include "db.php";
if (session_status() === PHP_SESSION_NONE) { session_start(); }
include "includes/header.php";
?>

<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
    :root {
        --primary: #518992;
        --navy: #0f1c3f;
        --pink: #f87171;
        --bg-soft: #f8fafc;
    }

    body { background: var(--bg-soft); font-family: 'Plus Jakarta Sans', sans-serif; }

    .cart-hero { background: linear-gradient(135deg, var(--navy), var(--primary)); padding: 50px 5%; color: white; text-align: center; margin-bottom: 40px; }
    
    .cart-container { max-width: 1400px; margin: 0 auto; padding: 0 5% 80px; display: grid; grid-template-columns: 1fr 400px; gap: 30px; }
    
    .cart-items { background: #fff; border-radius: 25px; padding: 30px; }
    .cart-item { display: grid; grid-template-columns: 120px 1fr auto; gap: 20px; padding: 25px; border: 1px solid #f1f5f9; border-radius: 20px; margin-bottom: 20px; align-items: center; transition: 0.3s; }
    .cart-item:hover { box-shadow: 0 10px 30px rgba(0,0,0,0.05); }
    
    .item-img { width: 100px; height: 100px; object-fit: contain; border-radius: 15px; background: #f8fafc; padding: 10px; }
    
    .item-details h4 { font-size: 18px; font-weight: 700; margin-bottom: 8px; color: var(--navy); }
    .item-brand { font-size: 12px; color: var(--primary); font-weight: 800; text-transform: uppercase; margin-bottom: 5px; }
    .item-meta { font-size: 13px; color: #64748b; margin-bottom: 10px; }
    .item-price { font-size: 20px; font-weight: 900; color: var(--primary); }
    
    .item-actions { display: flex; flex-direction: column; gap: 15px; align-items: flex-end; }
    .qty-control { display: flex; align-items: center; gap: 10px; background: #f8fafc; border-radius: 15px; padding: 8px 15px; }
    .qty-btn { background: var(--primary); color: white; border: none; width: 35px; height: 35px; border-radius: 10px; cursor: pointer; font-size: 16px; transition: 0.3s; }
    .qty-btn:hover { background: var(--navy); transform: scale(1.1); }
    .qty-value { font-weight: 800; font-size: 16px; width: 40px; text-align: center; }
    
    .remove-btn { background: var(--pink); color: white; border: none; padding: 10px 20px; border-radius: 12px; cursor: pointer; font-weight: 700; font-size: 12px; transition: 0.3s; }
    .remove-btn:hover { background: #dc2626; transform: translateY(-2px); }
    
    .cart-summary { background: #fff; border-radius: 25px; padding: 30px; height: fit-content; position: sticky; top: 20px; box-shadow: 0 20px 40px rgba(0,0,0,0.05); }
    .summary-title { font-size: 22px; font-weight: 900; margin-bottom: 25px; color: var(--navy); }
    .summary-row { display: flex; justify-content: space-between; padding: 15px 0; border-bottom: 1px solid #f1f5f9; font-size: 15px; }
    .summary-total { font-size: 24px; font-weight: 900; color: var(--primary); padding-top: 20px; display: flex; justify-content: space-between; }
    
    .checkout-btn { background: var(--pink); color: white; border: none; width: 100%; padding: 18px; border-radius: 15px; font-weight: 800; font-size: 16px; cursor: pointer; margin-top: 25px; transition: 0.3s; }
    .checkout-btn:hover { background: var(--navy); transform: translateY(-3px); box-shadow: 0 15px 30px rgba(15, 28, 63, 0.2); }
    
    .empty-cart { text-align: center; padding: 80px 20px; }
    .empty-icon { font-size: 80px; color: #e2e8f0; margin-bottom: 20px; }
    .shop-btn { background: var(--primary); color: white; text-decoration: none; padding: 15px 40px; border-radius: 15px; font-weight: 800; display: inline-block; margin-top: 20px; transition: 0.3s; }
    .shop-btn:hover { background: var(--navy); transform: translateY(-3px); }

    @media (max-width: 992px) { .cart-container { grid-template-columns: 1fr; } }
</style>

<div class="cart-hero">
    <h1 style="font-weight: 900; font-size: 3rem; margin-bottom: 10px;">Your Pharmacy Cart</h1>
    <p style="font-weight: 500; opacity: 0.9;">Review your items and proceed to checkout</p>
</div>

<div class="cart-container">
    <div class="cart-items">
        <h2 style="font-size: 24px; font-weight: 900; margin-bottom: 25px; color: var(--navy);">
            <i class="fas fa-shopping-basket"></i> Cart Items
        </h2>

        <div id="cart-items-container">
            <?php
            if (isset($_SESSION['pharmacy_cart']) && count($_SESSION['pharmacy_cart']) > 0):
                foreach ($_SESSION['pharmacy_cart'] as $item):
            ?>
                <div class="cart-item" data-product-id="<?php echo $item['id']; ?>">
                    <img src="uploads/products/<?php echo $item['image']; ?>" class="item-img" alt="<?php echo $item['product_name']; ?>">
                    
                    <div class="item-details">
                        <div class="item-brand"><?php echo $item['brand_name']; ?></div>
                        <h4><?php echo $item['product_name']; ?></h4>
                        <div class="item-meta">
                            <i class="fas fa-flask"></i> <?php echo $item['size_options']; ?> | 
                            <i class="fas fa-tag"></i> <?php echo $item['category']; ?>
                        </div>
                        <div class="item-price">₹<?php echo number_format($item['price'], 2); ?></div>
                    </div>
                    
                    <div class="item-actions">
                        <div class="qty-control">
                            <button class="qty-btn" onclick="updateQuantity(<?php echo $item['id']; ?>, -1)">
                                <i class="fas fa-minus"></i>
                            </button>
                            <span class="qty-value" id="qty-<?php echo $item['id']; ?>"><?php echo $item['qty']; ?></span>
                            <button class="qty-btn" onclick="updateQuantity(<?php echo $item['id']; ?>, 1)">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                        <button class="remove-btn" onclick="removeItem(<?php echo $item['id']; ?>)">
                            <i class="fas fa-trash"></i> Remove
                        </button>
                    </div>
                </div>
            <?php
                endforeach;
            else:
            ?>
                <div class="empty-cart">
                    <div class="empty-icon"><i class="fas fa-shopping-basket"></i></div>
                    <h3 style="font-size: 24px; font-weight: 800; margin-bottom: 10px;">Your cart is empty</h3>
                    <p style="color: #64748b; margin-bottom: 20px;">Add some pharmacy products to get started!</p>
                    <a href="pharmacy.php" class="shop-btn"><i class="fas fa-capsules"></i> Browse Pharmacy</a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php if (isset($_SESSION['pharmacy_cart']) && count($_SESSION['pharmacy_cart']) > 0): ?>
    <div class="cart-summary">
        <h3 class="summary-title">Order Summary</h3>
        
        <?php
        $subtotal = 0;
        $total_items = 0;
        foreach ($_SESSION['pharmacy_cart'] as $item) {
            $subtotal += $item['price'] * $item['qty'];
            $total_items += $item['qty'];
        }
        $delivery = $subtotal > 500 ? 0 : 50;
        $total = $subtotal + $delivery;
        ?>
        
        <div class="summary-row">
            <span>Subtotal (<?php echo $total_items; ?> items)</span>
            <span id="subtotal">₹<?php echo number_format($subtotal, 2); ?></span>
        </div>
        <div class="summary-row">
            <span>Delivery Charges</span>
            <span id="delivery"><?php echo $delivery == 0 ? 'FREE' : '₹' . number_format($delivery, 2); ?></span>
        </div>
        <div class="summary-total">
            <span>Total Amount</span>
            <span id="total">₹<?php echo number_format($total, 2); ?></span>
        </div>
        
        <button class="checkout-btn" onclick="proceedToCheckout()">
            <i class="fas fa-lock"></i> PROCEED TO CHECKOUT
        </button>
        
        <div style="text-align: center; margin-top: 20px; padding-top: 20px; border-top: 1px solid #f1f5f9;">
            <a href="pharmacy.php" style="color: var(--primary); text-decoration: none; font-weight: 700; font-size: 14px;">
                <i class="fas fa-arrow-left"></i> Continue Shopping
            </a>
        </div>
    </div>
    <?php endif; ?>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
function updateQuantity(productId, change) {
    const qtyElement = document.getElementById('qty-' + productId);
    let currentQty = parseInt(qtyElement.textContent);
    let newQty = currentQty + change;
    
    if (newQty < 1) {
        removeItem(productId);
        return;
    }
    
    const formData = new FormData();
    formData.append('action', 'update');
    formData.append('product_id', productId);
    formData.append('quantity', newQty);
    
    fetch('pharmacy_cart_handler.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            qtyElement.textContent = newQty;
            updateSummary();
        }
    });
}

function removeItem(productId) {
    if (!confirm('Remove this item from cart?')) return;
    
    const formData = new FormData();
    formData.append('action', 'remove');
    formData.append('product_id', productId);
    
    fetch('pharmacy_cart_handler.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            location.reload();
        }
    });
}

function updateSummary() {
    location.reload();
}

function proceedToCheckout() {
    window.location.href = 'checkout.php';
}
</script>

<?php include "includes/footer.php"; ?>
