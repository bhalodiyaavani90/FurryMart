<?php
include "db.php";
if (session_status() === PHP_SESSION_NONE) { session_start(); }

// Redirect if not logged in
if (!isset($_SESSION['email'])) {
    header("Location: login.php?msg=Please login to view your collection");
    exit();
}

$user_email = $_SESSION['email'];

// Optimized Query to get Product Details + Variants with Stock
$sql = "SELECT p.*, v.price, v.mrp, v.weight_size, v.id as variant_id, v.stock_qty 
        FROM wishlist w 
        INNER JOIN products p ON w.product_id = p.id 
        INNER JOIN product_variants v ON p.id = v.product_id 
        WHERE w.user_email = '$user_email'
        GROUP BY p.id
        ORDER BY w.id DESC";
$result = mysqli_query($conn, $sql);
?>

<?php include "includes/header.php"; ?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
    :root { 
        --primary: #518992; --navy: #0f1c3f; --accent: #f87171; 
        --veg: #22c55e; --nonveg: #ef4444; --gray: #94a3b8; --bg: #f8fafc;
        --glass: rgba(255, 255, 255, 0.15);
    }

    body { background: var(--bg); color: var(--navy); overflow-x: hidden; }

    /* --- ANIMATED KEN BURNS HERO --- */
    .hero-perfect { 
        height: 450px; position: relative; overflow: hidden; 
        display: flex; align-items: center; justify-content: center; 
        color: #fff; border-radius: 0 0 80px 80px; margin-bottom: 60px;
        box-shadow: 0 20px 40px rgba(15, 28, 63, 0.1);
    }
    
    .hero-bg-animator {
        position: absolute; top: 0; left: 0; width: 100%; height: 100%;
        background: linear-gradient(to bottom, rgba(15,28,63,0.8), rgba(15,28,63,0.4)), 
                    url('https://images.unsplash.com/photo-1516734212186-a967f81ad0d7?auto=format&fit=crop&w=1600&q=80');
        background-size: cover; background-position: center;
        animation: kenBurns 20s infinite alternate ease-in-out;
        z-index: -1;
    }

    @keyframes kenBurns {
        0% { transform: scale(1); }
        100% { transform: scale(1.15) translateY(-20px); }
    }

    .hero-glass-box {
        background: var(--glass); backdrop-filter: blur(15px);
        padding: 40px 60px; border-radius: 30px; border: 1px solid rgba(255,255,255,0.2);
        text-align: center; box-shadow: 0 10px 30px rgba(0,0,0,0.2);
    }

    .hero-glass-box h1 { font-size: 64px; font-weight: 900; margin: 0; letter-spacing: -2px; }
    .hero-glass-box p { font-weight: 700; text-transform: uppercase; letter-spacing: 5px; opacity: 0.9; margin-top: 10px; }

    /* --- PRODUCT COLLECTION GRID --- */
    .wishlist-viewport { padding: 0 6% 80px; }
    .product-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 35px; }

    /* --- ATTRACTIVE MODERN CARDS --- */
    .premium-card {
        background: #fff; border-radius: 35px; padding: 25px;
        transition: all 0.6s cubic-bezier(0.23, 1, 0.32, 1);
        position: relative; border: 1px solid #f1f5f9;
        display: flex; flex-direction: column; height: 480px;
    }

    .premium-card:hover {
        transform: translateY(-15px) scale(1.02);
        box-shadow: 0 30px 60px rgba(15, 28, 63, 0.15);
        border-color: var(--primary);
    }

    .diet-mark { position: absolute; top: 25px; left: 25px; width: 18px; height: 18px; border: 2px solid; display: flex; align-items: center; justify-content: center; z-index: 5; background: #fff; border-radius: 3px; }
    .veg { border-color: var(--veg); } .veg::after { content: ''; width: 10px; height: 10px; background: var(--veg); border-radius: 50%; }
    .nonveg { border-color: var(--nonveg); } .nonveg::after { content: ''; width: 10px; height: 10px; background: var(--nonveg); border-radius: 50%; }

    .remove-trigger {
        position: absolute; top: 25px; right: 25px; z-index: 10;
        background: #fff; width: 42px; height: 42px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        box-shadow: 0 8px 20px rgba(0,0,0,0.1); cursor: pointer;
        transition: 0.4s; color: var(--accent); border: none;
    }
    .remove-trigger:hover { background: #fff1f2; transform: rotate(90deg); }

    .img-vault { height: 200px; overflow: hidden; border-radius: 20px; margin-bottom: 20px; display: flex; align-items: center; justify-content: center; background: #fdfdfd; transition: 0.5s; }
    .premium-card:hover .img-vault { background: #fff; }
    .product-img { max-width: 85%; max-height: 85%; object-fit: contain; transition: 0.8s ease; }
    .premium-card:hover .product-img { transform: scale(1.1); }

    .brand-tag { font-size: 11px; color: var(--primary); font-weight: 800; text-transform: uppercase; margin-bottom: 8px; }
    .name-tag { font-size: 18px; font-weight: 800; color: var(--navy); line-height: 1.3; margin-bottom: auto; }

    .price-tag { font-size: 28px; font-weight: 900; color: var(--navy); margin: 15px 0; }
    .size-badge { background: #f1f5f9; padding: 6px 12px; border-radius: 10px; font-size: 11px; font-weight: 800; color: var(--navy); display: inline-block; }

    .add-to-bag-btn {
        background: var(--accent); color: #fff; width: 100%; padding: 18px; 
        border-radius: 20px; font-weight: 800; text-transform: uppercase; 
        border: none; font-size: 14px; cursor: pointer; transition: 0.4s;
        box-shadow: 0 10px 20px rgba(248, 113, 113, 0.2);
    }
    .add-to-bag-btn:hover { background: var(--navy); box-shadow: 0 15px 30px rgba(15, 28, 63, 0.25); transform: translateY(-2px); }
    
    /* Coming Soon Button */
    .coming-soon-btn {
        background: linear-gradient(135deg, #fbbf24, #f59e0b);
        color: #fff; width: 100%; padding: 18px; 
        border-radius: 20px; font-weight: 800; text-transform: uppercase; 
        border: none; font-size: 14px; cursor: not-allowed; transition: 0.4s;
        box-shadow: 0 10px 20px rgba(251, 191, 36, 0.3);
        position: relative;
        overflow: hidden;
    }
    .coming-soon-btn::before {
        content: '';
        position: absolute;
        top: 0; left: -100%;
        width: 100%; height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
        animation: shimmer 2s infinite;
    }
    @keyframes shimmer {
        0% { left: -100%; }
        100% { left: 100%; }
    }

    /* --- EMPTY STATE --- */
    .empty-state { text-align: center; padding: 100px 20px; }
    .empty-state i { font-size: 100px; color: var(--gray); opacity: 0.2; margin-bottom: 30px; }
</style>

<div class="hero-perfect">
    <div class="hero-bg-animator"></div>
    <div class="hero-glass-box animate__animated animate__fadeInDown">
        <h1>My Collection</h1>
        <p>Your FurryMart Personal Vault</p>
    </div>
</div>

<div class="wishlist-viewport">
    <?php if(mysqli_num_rows($result) > 0): ?>
        <main class="product-grid">
            <?php 
            $delay = 0;
            while($p = mysqli_fetch_assoc($result)): 
                $delay += 0.1;
                $is_out_of_stock = ($p['stock_qty'] <= 0);
            ?>
            <div class="premium-card animate__animated animate__fadeInUp" 
                 style="animation-delay: <?php echo $delay; ?>s;" 
                 id="item-<?php echo $p['id']; ?>">
                
                <div class="diet-mark <?php echo ($p['veg_type'] == 'Veg') ? 'veg' : 'nonveg'; ?>"></div>

                <button class="remove-trigger" onclick="removeFromWishlist(this, <?php echo $p['id']; ?>)">
                    <i class="fa-solid fa-xmark"></i>
                </button>

                <div class="img-vault">
                    <img src="uploads/products/<?php echo $p['base_image']; ?>" class="product-img">
                </div>
                
                <div class="brand-tag"><?php echo $p['brand_name']; ?></div>
                <h3 class="name-tag"><?php echo $p['name']; ?></h3>
                
                <div style="margin: 10px 0;">
                    <span class="size-badge"><?php echo $p['weight_size']; ?></span>
                </div>

                <div class="price-tag">₹<?php echo number_format($p['price']); ?></div>

                <?php if($is_out_of_stock): ?>
                    <button class="coming-soon-btn" disabled>
                        <i class="fas fa-clock"></i> Coming Soon
                    </button>
                <?php else: ?>
                    <button class="add-to-bag-btn" onclick="addToCart(<?php echo $p['variant_id']; ?>, '<?php echo addslashes($p['name']); ?>', '<?php echo $p['base_image']; ?>', <?php echo $p['price']; ?>, '<?php echo $p['weight_size']; ?>', <?php echo $p['id']; ?>)">Add to Bag</button>
                <?php endif; ?>
            </div>
            <?php endwhile; ?>
        </main>
    <?php else: ?>
        <div class="empty-state animate__animated animate__fadeIn">
            <i class="fa-solid fa-heart-circle-exclamation"></i>
            <h2 style="font-weight: 900; color: var(--gray); font-size: 32px;">Your Vault is Empty</h2>
            <p style="color: var(--gray); margin-bottom: 40px; font-size: 18px;">Discover premium products and save them here.</p>
            <a href="index.php" class="add-to-bag-btn" style="display:inline-block; width:auto; padding: 20px 50px;">Start Shopping</a>
        </div>
    <?php endif; ?>
</div>

<style>
/* Toast Notification */
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
</style>

<script>
/**
 * ADD TO CART FUNCTIONALITY
 * Sends product data to cart.php via POST request and removes from wishlist
 */
function addToCart(variantId, name, image, price, weight, productId) {
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
        
        // Update cart count in header if function exists
        if(typeof updateHeaderCounts === 'function') {
            updateHeaderCounts();
        }
        
        // Remove product from wishlist
        const card = document.getElementById('item-' + productId);
        if(card) {
            card.style.transform = "scale(0.8) translateY(-20px)";
            card.style.opacity = "0";
            card.style.transition = "0.5s all ease-out";
            
            setTimeout(() => {
                card.remove();
                
                // Update wishlist count in header
                if(typeof updateHeaderCounts === 'function') {
                    updateHeaderCounts();
                }
                
                // Reload if grid is empty to show Empty State
                if(document.querySelectorAll('.premium-card').length === 0) {
                    location.reload();
                }
            }, 500);
            
            // Also remove from database
            fetch('wishlist_action.php?product_id=' + productId)
                .then(response => response.json())
                .catch(err => console.error("Wishlist removal error"));
        }
        
        // Optional: Show quick action
        setTimeout(() => {
            const goToCart = confirm("Item added to cart! Go to cart now?");
            if (goToCart) {
                window.location.href = 'cart.php';
            }
        }, 800);
    })
    .catch(err => {
        console.error(err);
        showToast("Failed to add item to cart", "error");
    });
}

/**
 * TOAST NOTIFICATION SYSTEM
 */
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

/**
 * AJAX Removal with DOM Animation
 */
function removeFromWishlist(btn, productId) {
    // Start AJAX
    fetch('wishlist_action.php?product_id=' + productId)
    .then(response => response.json())
    .then(data => {
        if (data.status === 'removed') {
            const card = document.getElementById('item-' + productId);
            
            // 1. Play Outbound Animation
            card.classList.remove('animate__fadeInUp');
            card.classList.add('animate__fadeOutScale'); // Custom or Animate.css exit
            
            // CSS exit animation
            card.style.transform = "scale(0.5) rotate(5deg)";
            card.style.opacity = "0";
            card.style.transition = "0.6s all cubic-bezier(0.6, -0.28, 0.735, 0.045)";
            
            setTimeout(() => {
                card.remove();
                
                // 2. Update Header Badge (Trigger function from header.php if exists)
                if(typeof updateHeaderCounts === 'function') {
                    updateHeaderCounts();
                }

                // 3. Reload if grid is empty to show Empty State
                if(document.querySelectorAll('.premium-card').length === 0) {
                    location.reload();
                }
            }, 600);
            
            showToast("Removed from collection", "success");
        }
    })
    .catch(err => console.error("Wishlist sync error"));
}
</script>

<?php include "includes/footer.php"; ?>