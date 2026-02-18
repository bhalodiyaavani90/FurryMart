<?php
include "db.php";
if (session_status() === PHP_SESSION_NONE) { session_start(); }

// Function to check if product is in wishlist
function isInWishlist($conn, $pid) {
    if(!isset($_SESSION['user_email']) && !isset($_SESSION['email'])) return false;
    $email = isset($_SESSION['user_email']) ? mysqli_real_escape_string($conn, $_SESSION['user_email']) : mysqli_real_escape_string($conn, $_SESSION['email']);
    $check = mysqli_query($conn, "SELECT id FROM wishlist WHERE user_email = '$email' AND product_id = $pid");
    return mysqli_num_rows($check) > 0;
}

$query = "SELECT * FROM pharmacy_products WHERE is_available = 1";

// DYNAMIC PRICE RANGE FILTER
if(isset($_POST['price']) && $_POST['price'] != 'all') {
    $p = mysqli_real_escape_string($conn, $_POST['price']);
    if($p == "500") { $query .= " AND price < 500"; }
    else if($p == "2000") { $query .= " AND price BETWEEN 500 AND 2000"; }
    else if($p == "above") { $query .= " AND price > 2000"; }
}

$result = mysqli_query($conn, $query);

if(mysqli_num_rows($result) > 0) {
    while($p = mysqli_fetch_assoc($result)) {
        // Prepare product data for JavaScript
        $productJson = htmlspecialchars(json_encode($p), ENT_QUOTES, 'UTF-8');
        
        echo '
        <div class="product-card animate__animated animate__fadeInUp">
            <div class="product-clickable" onclick=\'startIntelligenceLoading('.$productJson.')\' style="flex: 1; display: flex; flex-direction: column;">
                <div class="product-img-container">
                    <img src="uploads/products/'.$p['image'].'" class="product-image" alt="'.$p['product_name'].'">
                </div>

                <div class="product-info" style="flex: 1; display: flex; flex-direction: column;">
                    <span class="brand-tag">'.$p['brand_name'].'</span>
                    <h3 class="product-title">'.$p['product_name'].'</h3>
                    
                    <div class="meta-row">
                        <span class="variant-badge"><i class="fas fa-flask"></i> '.$p['size_options'].'</span>
                        <span class="category-badge">'.$p['category'].'</span>
                    </div>

                    <div class="delivery-indicator">
                        <i class="fas fa-bolt"></i> Fast Delivery
                    </div>
                </div>
            </div>
            
            <div class="price-action-row">
                <div class="price-block">
                    <span class="currency">₹</span>
                    <span class="amount">'.number_format($p['price'], 2).'</span>
                </div>
                <button class="add-to-bag-btn" onclick="event.stopPropagation(); addToCart('.$p['id'].')">
                    <i class="fas fa-shopping-basket"></i>
                </button>
            </div>
        </div>';
    }
} else {
    echo '
    <div class="no-results animate__animated animate__fadeIn">
        <div class="empty-state-icon">
            <i class="fas fa-mortar-pestle"></i>
        </div>
        <h3>No Medicines Found</h3>
        <p>We couldn\'t find any products matching your specific filters. Try adjusting your selection.</p>
        <button class="reset-link" onclick="location.reload()">Clear All Filters</button>
    </div>';
}
?>