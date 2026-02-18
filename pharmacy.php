<?php
include "db.php";
if (session_status() === PHP_SESSION_NONE) { session_start(); }
include "includes/header.php"; 
?>

<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
    :root {
        --primary: #518992;
        --navy: #0f1c3f;
        --pink: #f87171;
        --bg-soft: #f8fafc;
        --border: #eef2f6;
    }

    body { background-color: #fff; font-family: 'Plus Jakarta Sans', sans-serif; color: var(--navy); }

    .pharmacy-hero { background: linear-gradient(135deg, var(--navy), var(--primary)); padding: 60px 5%; color: white; text-align: center; border-radius: 0 0 50px 50px; margin-bottom: 40px; }

    .pharmacy-container { display: grid; grid-template-columns: 300px 1fr; gap: 40px; padding: 0 5% 80px; max-width: 1600px; margin: 0 auto; }

    /* --- ADVANCED SIDEBAR --- */
    .filter-sidebar { background: #fff; border: 1px solid var(--border); border-radius: 30px; padding: 30px; height: fit-content; position: sticky; top: 110px; box-shadow: 0 20px 40px rgba(0,0,0,0.03); }
    .filter-section { margin-bottom: 30px; border-bottom: 1px solid var(--border); padding-bottom: 20px; }
    .filter-section:last-child { border: none; }
    .filter-section h5 { font-size: 13px; font-weight: 800; text-transform: uppercase; letter-spacing: 1.5px; color: #94a3b8; margin-bottom: 20px; display: flex; align-items: center; gap: 10px; }

    .filter-option { display: flex; align-items: center; gap: 12px; margin-bottom: 15px; font-size: 14px; font-weight: 600; color: var(--navy); cursor: pointer; transition: 0.3s; }
    .filter-option:hover { color: var(--primary); transform: translateX(5px); }
    .filter-option input { accent-color: var(--primary); width: 20px; height: 20px; cursor: pointer; border-radius: 6px; }

    /* --- PRODUCT GRID SYSTEM --- 4 CARDS PER ROW */
    #product-display-area { display: grid; grid-template-columns: repeat(4, 1fr); gap: 25px; }

    .product-card { 
        background: #fff; border: 1px solid var(--border); border-radius: 25px; padding: 20px; 
        transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1); position: relative;
        height: 440px; display: flex; flex-direction: column;
    }
    .product-card:hover { transform: translateY(-10px); box-shadow: 0 25px 50px rgba(15, 28, 63, 0.1); border-color: var(--primary); }

    .wishlist-overlay { position: absolute; top: 15px; right: 15px; z-index: 10; background: white; width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 12px rgba(0,0,0,0.1); transition: all 0.3s ease; }
    .wishlist-overlay:hover { transform: scale(1.1); box-shadow: 0 5px 18px rgba(0,0,0,0.15); }
    .wishlist-icon { font-size: 16px; color: #cbd5e1; cursor: pointer; transition: all 0.3s ease; }
    .wishlist-icon:hover { color: var(--pink); transform: scale(1.1); }
    .wishlist-icon.active { color: var(--pink); font-weight: 900; }

    .product-img-container { height: 180px; display: flex; align-items: center; justify-content: center; margin-bottom: 15px; background: #fcfcfc; border-radius: 18px; overflow: hidden; }
    .product-image { max-width: 80%; max-height: 80%; object-fit: contain; transition: 0.5s; }
    .product-card:hover .product-image { transform: scale(1.1); }

    .brand-tag { font-size: 10px; font-weight: 900; color: var(--primary); text-transform: uppercase; letter-spacing: 1.5px; margin-bottom: 6px; display: block; }
    .product-title { font-size: 14px; font-weight: 700; height: 42px; overflow: hidden; margin-bottom: 12px; line-height: 1.5; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; }
    
    .meta-row { display: flex; gap: 8px; margin-bottom: 12px; flex-wrap: wrap; }
    .variant-badge { font-size: 10px; font-weight: 700; background: #f1f5f9; padding: 5px 10px; border-radius: 10px; color: var(--navy); }
    .category-badge { font-size: 9px; font-weight: 700; border: 1px solid var(--border); padding: 4px 10px; border-radius: 8px; color: #94a3b8; }

    .delivery-indicator { font-size: 10px; font-weight: 700; color: #10b981; background: #f0fdf4; padding: 5px 12px; border-radius: 10px; margin-bottom: 14px; display: inline-flex; align-items: center; gap: 5px; }

    .price-action-row { display: flex; justify-content: space-between; align-items: center; padding-top: 12px; border-top: 1px solid var(--border); margin-top: auto; }
    .price-block .currency { font-size: 13px; font-weight: 800; vertical-align: top; }
    .price-block .amount { font-size: 20px; font-weight: 900; }

    .add-to-bag-btn { 
        background: var(--pink); color: white; border: none; width: 45px; height: 45px; 
        border-radius: 14px; cursor: pointer; transition: 0.3s; display: flex; align-items: center; justify-content: center; font-size: 16px;
    }
    .add-to-bag-btn:hover { background: var(--navy); transform: rotate(15deg); box-shadow: 0 10px 20px rgba(248, 113, 113, 0.3); }

    /* SOLD OUT Badge for Pharmacy */
    .sold-out-badge {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background: linear-gradient(135deg, #ef4444, #dc2626);
        color: white;
        padding: 6px 14px;
        border-radius: 20px;
        font-size: 10px;
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
        border-radius: 20px;
        pointer-events: none;
    }
    .product-card.out-of-stock .add-to-bag-btn {
        background: #94a3b8;
        cursor: not-allowed;
        opacity: 0.6;
    }
    .product-card.out-of-stock .add-to-bag-btn:hover {
        transform: none;
        box-shadow: none;
    }
    @keyframes pulse {
        0%, 100% { transform: translate(-50%, -50%) scale(1); }
        50% { transform: translate(-50%, -50%) scale(1.05); }
    }

    /* No Results State */
    .no-results { text-align: center; padding: 100px 20px; grid-column: 1/-1; }
    .empty-state-icon { font-size: 60px; color: #e2e8f0; margin-bottom: 20px; }
    .reset-link { background: var(--navy); color: white; border: none; padding: 12px 30px; border-radius: 15px; font-weight: 700; cursor: pointer; margin-top: 20px; }

    /* --- LOADING SPINNER --- */
    .loader-overlay { position: fixed; inset: 0; background: rgba(255,255,255,0.8); backdrop-filter: blur(10px); display: none; z-index: 10001; align-items: center; justify-content: center; }
    .spinner-round { width: 60px; height: 60px; border: 6px solid #f1f5f9; border-top: 6px solid var(--primary); border-radius: 50%; animation: spin 1s linear infinite; }
    @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }

    /* --- THE ULTIMATE PROFESSIONAL MODAL --- */
    .qv-modal { position: fixed; inset: 0; background: rgba(15, 28, 63, 0.75); backdrop-filter: blur(10px); display: none; z-index: 10000; align-items: center; justify-content: center; padding: 20px; }
    .qv-content { background: #fff; width: 100%; max-width: 1200px; max-height: 95vh; border-radius: 45px; display: grid; grid-template-columns: 1.1fr 1.3fr; overflow: hidden; position: relative; animation: zoomIn 0.4s; border: 1px solid #fff; box-shadow: 0 50px 100px rgba(0,0,0,0.3); }
    @keyframes zoomIn { from { transform: scale(0.8); opacity: 0; } to { transform: scale(1); opacity: 1; } }
    .qv-close { position: absolute; top: 25px; right: 30px; font-size: 35px; cursor: pointer; color: var(--gray); z-index: 100; transition: 0.3s; }
    .qv-close:hover { color: var(--navy); transform: rotate(90deg); }

    .modal-visual-vault { padding: 40px; background: #fff; display: flex; align-items: center; justify-content: center; border-right: 1px solid #f1f5f9; }
    .modal-visual-vault img { max-width: 95%; max-height: 500px; object-fit: contain; }

    /* Right Console - NO OVERLAP FLEX ARCHITECTURE */
    .modal-console { display: flex; flex-direction: column; height: 100%; position: relative; background: #fff; }
    .console-header { padding: 50px 50px 20px; flex-shrink: 0; border-bottom: 1px solid #f8fafc; }
    
    /* SCROLL ZONE: Product Details */
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

    /* Make product card content clickable */
    .product-clickable { cursor: pointer; }

    /* Toast Notification Container */
    #toast-container { position: fixed; bottom: 30px; right: 30px; z-index: 99999; }
    .toast { background: var(--navy); color: #fff; padding: 15px 25px; border-radius: 15px; margin-top: 10px; font-weight: 700; display: flex; align-items: center; gap: 12px; box-shadow: 0 10px 30px rgba(15,28,63,0.2); animation: slideIn 0.5s forwards; }
    @keyframes slideIn { from { transform: translateX(100%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
    .toast.success { border-left: 5px solid #10b981; }
    .toast.error { border-left: 5px solid var(--pink); }

    @media (max-width: 1400px) { #product-display-area { grid-template-columns: repeat(3, 1fr); } }
    @media (max-width: 1024px) { .pharmacy-container { grid-template-columns: 1fr; } .filter-sidebar { position: relative; top: 0; } #product-display-area { grid-template-columns: repeat(2, 1fr); } .qv-content { grid-template-columns: 1fr; } .modal-visual-vault { display: none; } }
    @media (max-width: 640px) { #product-display-area { grid-template-columns: 1fr; } }
</style>

<div id="loaderOverlay" class="loader-overlay"><div class="spinner-round"></div></div>

<div class="pharmacy-hero animate__animated animate__fadeIn">
    <h1 style="font-weight: 900; font-size: 3rem; margin-bottom: 10px;">FurryMart Pharmacy</h1>
    <p style="font-weight: 500; opacity: 0.9; font-size: 1.2rem;">Expert clinical care for your beloved pets, delivered to your door.</p>
</div>

<div class="pharmacy-container">
    <aside class="filter-sidebar">
        <form id="filter-form">
            <div class="filter-section">
                <h5><i class="fas fa-wallet"></i> Price Range</h5>
                <label class="filter-option"><input type="radio" name="price" value="all" checked class="filter-check"> All Products</label>
                <label class="filter-option"><input type="radio" name="price" value="500" class="filter-check"> Under ₹500</label>
                <label class="filter-option"><input type="radio" name="price" value="2000" class="filter-check"> ₹500 - ₹2000</label>
                <label class="filter-option"><input type="radio" name="price" value="above" class="filter-check"> Above ₹2000</label>
            </div>
        </form>
    </aside>

    <main>
        <div id="product-display-area">
            </div>
    </main>
</div>

<!-- PRODUCT DETAILS MODAL -->
<div id="qvBox" class="qv-modal">
    <div class="qv-content">
        <span class="qv-close" onclick="closeQuickView()">&times;</span>
        <div class="modal-visual-vault"><img id="qv-img" src="" alt="Product Image"></div>
        
        <div class="modal-console">
            <div class="console-header">
                <div id="qv-brand" style="color:var(--primary); font-weight:900; text-transform:uppercase; font-size:12px; margin-bottom: 5px;"></div>
                <h2 id="qv-name" style="font-size:32px; font-weight:900; margin:0 0 10px; line-height:1.1; color:var(--navy);"></h2>
                <div style="display: flex; align-items: center; gap: 10px;">
                    <span id="qv-price" style="font-size:34px; font-weight:900; color:var(--primary);"></span>
                    <span id="qv-category-badge" class="badge-pill" style="background:#f1f5f9; color:var(--navy);"></span>
                </div>
            </div>

            <div class="console-scroll-zone">
                <h4 style="margin-bottom:10px; color:var(--gray); text-transform:uppercase; font-size:10px; letter-spacing:1px;">Product Information</h4>
                <p id="qv-desc" style="font-size:15px; color:#64748b; line-height:1.8; margin-bottom:30px;"></p>

                <div class="dash-grid">
                    <div class="dash-item">
                        <small class="dash-label">Brand</small>
                        <div id="qv-brand-detail" class="dash-value"></div>
                    </div>
                    <div class="dash-item">
                        <small class="dash-label">Size/Options</small>
                        <div id="qv-size" class="dash-value"></div>
                    </div>
                    <div class="dash-item">
                        <small class="dash-label">Category</small>
                        <div id="qv-category" class="dash-value"></div>
                    </div>
                    <div class="dash-item">
                        <small class="dash-label">Stock Status</small>
                        <div id="qv-stock" class="dash-value"><i class="fas fa-check-circle"></i> Available</div>
                    </div>
                </div>
            </div>
            
            <div class="console-footer">
                <button class="btn-action" onclick="addToCartFromModal()" style="padding:22px; font-size:16px; border-radius:20px; width:100%; box-shadow: 0 15px 30px rgba(81, 137, 146, 0.25);">ADD TO CART</button>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
const qvBox = document.getElementById('qvBox');
const loader = document.getElementById('loaderOverlay');
let currentProduct = null;

// Helper: Format Currency (INR)
const formatINR = (amount) => {
    return new Intl.NumberFormat('en-IN', {
        style: 'currency',
        currency: 'INR',
        maximumFractionDigits: 2
    }).format(amount);
};

// Start Loading Animation
function startIntelligenceLoading(p) {
    loader.style.display = 'flex';
    
    setTimeout(() => {
        loader.style.display = 'none';
        launchQuickView(p);
    }, 600);
}

// Launch Product Details Modal
function launchQuickView(p) {
    currentProduct = p;
    
    const scrollZone = document.querySelector('.console-scroll-zone');
    if(scrollZone) scrollZone.scrollTop = 0;

    const modalImg = document.getElementById('qv-img');
    modalImg.style.opacity = '0';
    modalImg.src = 'uploads/products/' + p.image;
    modalImg.onload = () => { 
        modalImg.style.transition = 'opacity 0.4s ease';
        modalImg.style.opacity = '1'; 
    };

    document.getElementById('qv-brand').innerText = p.brand_name || 'Premium Brand';
    document.getElementById('qv-name').innerText = p.product_name;
    document.getElementById('qv-price').innerText = formatINR(p.price);
    document.getElementById('qv-desc').innerText = p.description || 'Professional veterinary-grade pharmaceutical product for optimal pet health.';
    document.getElementById('qv-brand-detail').innerHTML = '<i class="fas fa-award" style="color:var(--primary)"></i> ' + (p.brand_name || 'Premium');
    document.getElementById('qv-size').innerHTML = '<i class="fas fa-flask" style="color:var(--primary)"></i> ' + p.size_options;
    document.getElementById('qv-category').innerHTML = '<i class="fas fa-tag" style="color:var(--primary)"></i> ' + p.category;
    document.getElementById('qv-category-badge').innerText = p.category;

    qvBox.style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

// Close Modal
function closeQuickView() {
    qvBox.style.display = 'none';
    document.body.style.overflow = 'auto';
}

// Close on outside click
window.addEventListener('click', function(e) {
    if (e.target === qvBox) closeQuickView();
});

// Close on Escape key
window.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && qvBox.style.display === 'flex') {
        closeQuickView();
    }
});

// Add to Cart
function addToCart(productId) {
    const formData = new FormData();
    formData.append('action', 'add_pharmacy');
    formData.append('product_id', productId);
    formData.append('quantity', 1);
    
    fetch('cart.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            showToast("✓ Added to Cart Successfully!", "success");
            if(typeof updateHeaderCounts === 'function') {
                updateHeaderCounts();
            }
        } else {
            showToast(data.message || "Failed to add item to cart", "error");
        }
    })
    .catch(err => {
        console.error(err);
        showToast("Failed to add item to cart", "error");
    });
}

// Add to Cart from Modal
function addToCartFromModal() {
    if (!currentProduct) {
        showToast("Product data not available", "error");
        return;
    }
    
    addToCart(currentProduct.id);
    setTimeout(() => {
        closeQuickView();
    }, 1000);
}

// Toggle Wishlist
function toggleWishlist(el, productId) {
    // Add animation class
    $(el).addClass('animate__animated animate__heartBeat');
    
    fetch('wishlist.php?action=toggle&product_id=' + productId)
    .then(response => response.json())
    .then(data => {
        // Remove animation class after it completes
        setTimeout(() => {
            $(el).removeClass('animate__animated animate__heartBeat');
        }, 1000);
        
        if (data.status === 'not_logged_in') {
            showToast("Login Required! Redirecting to login page...", "error");
            setTimeout(() => { 
                window.location.href = 'login.php'; 
            }, 3000); 
            return;
        }

        if (data.status === 'added') {
            $(el).removeClass('far').addClass('fas active');
            showToast("Added to FurryMart Wishlist ❤️", "success");
            if(typeof updateHeaderCounts === 'function') {
                updateHeaderCounts();
            }
        } else if (data.status === 'removed') {
            $(el).removeClass('fas active').addClass('far');
            showToast("Removed from Wishlist", "success");
            if(typeof updateHeaderCounts === 'function') {
                updateHeaderCounts();
            }
        } else if (data.status === 'error') {
            showToast(data.message || "Failed to update wishlist", "error");
        }
    })
    .catch(err => {
        console.error(err);
        $(el).removeClass('animate__animated animate__heartBeat');
        showToast("Server Synchronization Error", "error");
    });
}

// Toast Notification System
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

    setTimeout(() => {
        toast.style.animation = 'slideIn 0.5s reverse forwards';
        setTimeout(() => toast.remove(), 500);
    }, 3000);
}

// Product Loading
$(document).ready(function() {
    function loadProducts() {
        var formData = $('#filter-form').serialize();
        $.ajax({
            url: "fetch_pharmacy.php",
            method: "POST",
            data: formData,
            beforeSend: function() {
                $('#product-display-area').css('opacity', '0.6');
            },
            success: function(data) {
                $('#product-display-area').html(data).css('opacity', '1');
            }
        });
    }

    loadProducts();

    $('.filter-check').on('change', function() {
        loadProducts();
    });
});
</script>

<?php include "includes/footer.php"; ?>