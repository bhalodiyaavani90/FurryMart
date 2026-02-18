<?php
include "db.php";
if (session_status() === PHP_SESSION_NONE) { session_start(); }

// Get brand name from URL parameter
$brand_name = isset($_GET['name']) ? mysqli_real_escape_string($conn, $_GET['name']) : '';

// If no brand specified, redirect to home
if(empty($brand_name)) {
    header("Location: index.php");
    exit;
}

// Check if brand exists
$brand_check = mysqli_query($conn, "SELECT * FROM brands WHERE brand_name = '$brand_name' AND status='active'");
if(mysqli_num_rows($brand_check) == 0) {
    header("Location: index.php");
    exit;
}
$brand_info = mysqli_fetch_assoc($brand_check);

// CAPTURE FILTERS
$current_stage = isset($_GET['stage']) ? mysqli_real_escape_string($conn, $_GET['stage']) : '';
$price_filter = isset($_GET['price']) ? $_GET['price'] : '';
$diet_filter = isset($_GET['diet']) ? mysqli_real_escape_string($conn, $_GET['diet']) : '';

// MASTER QUERY - Products filtered by brand
$sql = "SELECT p.*, 
        MIN(v.price) as price, 
        MIN(v.mrp) as mrp, 
        (SELECT v2.weight_size FROM product_variants v2 WHERE v2.product_id = p.id ORDER BY v2.price ASC LIMIT 1) as weight_size,
        (SELECT v2.stock_qty FROM product_variants v2 WHERE v2.product_id = p.id ORDER BY v2.price ASC LIMIT 1) as stock_qty,
        (SELECT v2.id FROM product_variants v2 WHERE v2.product_id = p.id ORDER BY v2.price ASC LIMIT 1) as variant_id,
        p.sub_cat_id
        FROM products p 
        INNER JOIN product_variants v ON p.id = v.product_id
        WHERE p.is_available = 1 AND p.brand_name = '$brand_name'";

// Apply additional filters
if($current_stage) { $sql .= " AND p.life_stage = '$current_stage'"; }
if($diet_filter) { $sql .= " AND p.veg_type = '$diet_filter'"; }

// Add GROUP BY before price filtering
$sql .= " GROUP BY p.id";

// Price filter - apply to the aggregated price
if($price_filter == 'under500') { $sql .= " HAVING price < 500"; }
elseif($price_filter == '500-1000') { $sql .= " HAVING price BETWEEN 500 AND 1000"; }
elseif($price_filter == '1000-2000') { $sql .= " HAVING price BETWEEN 1000 AND 2000"; }
elseif($price_filter == 'above2000') { $sql .= " HAVING price > 2000"; }

$sql .= " ORDER BY p.id DESC";
$result = mysqli_query($conn, $sql);

// Check if query failed
if(!$result) {
    die("Query Error: " . mysqli_error($conn));
}

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
    .hero-vault { 
        min-height: 320px; 
        position: relative; 
        overflow: hidden; 
        display: flex; 
        align-items: center; 
        justify-content: center; 
        color: #fff; 
        margin-bottom: 40px; 
        border-radius: 0 0 50px 50px;
        background: linear-gradient(135deg, #0a1628 0%, #1e3a5f 40%, #2c5282 70%, #3d7ea6 100%);
        padding: 35px 5%;
        box-shadow: 0 12px 35px rgba(0,0,0,0.12);
    }
    .hero-glimmer { 
        position: absolute; 
        top: 0; 
        left: 0; 
        width: 100%; 
        height: 100%; 
        background: 
            radial-gradient(circle at 15% 30%, rgba(81,137,146,0.4) 0%, transparent 35%),
            radial-gradient(circle at 85% 70%, rgba(225,29,72,0.3) 0%, transparent 40%),
            linear-gradient(135deg, rgba(81,137,146,0.15) 0%, rgba(15,28,63,0.85) 100%),
            url('https://images.unsplash.com/photo-1548199973-03cce0bbc87b?auto=format&fit=crop&w=1350&q=80'); 
        background-size: cover; 
        background-position: center; 
        animation: slowReflect 30s ease-in-out infinite alternate; 
        z-index: 1;
    }
    .hero-glimmer::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: radial-gradient(circle, rgba(255,255,255,0.08) 1px, transparent 1px);
        background-size: 40px 40px;
        animation: moveParticles 20s linear infinite;
        opacity: 0.3;
    }
    @keyframes slowReflect { 
        0% { transform: scale(1) rotate(0deg); opacity: 0.9; } 
        100% { transform: scale(1.1) rotate(1.5deg); opacity: 1; } 
    }
    @keyframes moveParticles {
        0% { transform: translate(0, 0); }
        100% { transform: translate(40px, 40px); }
    }
    
    .hero-content-wrapper {
        position: relative;
        z-index: 2;
        text-align: center;
        max-width: 900px;
        padding: 20px;
        animation: fadeInUp 0.8s ease-out;
    }
    
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .brand-logo-container { 
        width: 100px; 
        height: 100px;
        margin: 0 auto 20px; 
        background: linear-gradient(135deg, rgba(255,255,255,0.98) 0%, rgba(240,245,255,1) 100%); 
        padding: 18px; 
        border-radius: 28px; 
        box-shadow: 
            0 15px 40px rgba(0,0,0,0.25), 
            0 0 0 1px rgba(255,255,255,0.2),
            inset 0 1px 0 rgba(255,255,255,0.8);
        display: flex;
        align-items: center;
        justify-content: center;
        animation: float 6s ease-in-out infinite, glow 3s ease-in-out infinite;
        backdrop-filter: blur(15px);
        border: 2.5px solid rgba(255,255,255,0.3);
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        position: relative;
    }
    
    .brand-logo-container::before {
        content: '';
        position: absolute;
        top: -3px;
        left: -3px;
        right: -3px;
        bottom: -3px;
        background: linear-gradient(135deg, rgba(81,137,146,0.6), rgba(225,29,72,0.6));
        border-radius: 30px;
        z-index: -1;
        opacity: 0;
        transition: opacity 0.4s;
    }
    
    .brand-logo-container:hover::before {
        opacity: 1;
        animation: rotate 3s linear infinite;
    }
    
    @keyframes rotate {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    
    .brand-logo-container:hover {
        transform: scale(1.08) translateY(-3px);
        box-shadow: 0 25px 60px rgba(0,0,0,0.35);
    }
    
    .brand-logo-hero {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
        filter: drop-shadow(0 3px 10px rgba(0,0,0,0.15));
        transition: transform 0.4s ease;
    }
    
    .brand-logo-container:hover .brand-logo-hero {
        transform: scale(1.05);
    }
    
    @keyframes float {
        0%, 100% { transform: translateY(0px); }
        50% { transform: translateY(-10px); }
    }
    
    @keyframes glow {
        0%, 100% { box-shadow: 0 15px 40px rgba(0,0,0,0.25); }
        50% { box-shadow: 0 15px 40px rgba(81,137,146,0.35), 0 0 35px rgba(81,137,146,0.2); }
    }
    
    .hero-vault h1 { 
        font-size: 2.6rem; 
        font-weight: 900; 
        letter-spacing: -1.5px; 
        margin-bottom: 10px;
        text-shadow: 0 3px 20px rgba(0,0,0,0.5);
        background: linear-gradient(135deg, #ffffff 0%, #bfdbfe 50%, #e0e7ff 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        line-height: 1.1;
        animation: shimmerText 3s ease-in-out infinite;
    }
    
    @keyframes shimmerText {
        0%, 100% { filter: brightness(1); }
        50% { filter: brightness(1.15); }
    }
    
    .hero-subtitle {
        font-size: 0.85rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 3px;
        color: rgba(255,255,255,0.93);
        text-shadow: 0 2px 15px rgba(0,0,0,0.35);
        margin-bottom: 22px;
        position: relative;
        display: inline-block;
        padding: 0 22px;
    }
    
    .hero-subtitle::before,
    .hero-subtitle::after {
        content: '✦';
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        font-size: 0.75rem;
        color: rgba(255,255,255,0.5);
        animation: twinkle 2s ease-in-out infinite;
    }
    
    .hero-subtitle::before { left: 0; }
    .hero-subtitle::after { right: 0; animation-delay: 1s; }
    
    @keyframes twinkle {
        0%, 100% { opacity: 0.3; transform: translateY(-50%) scale(1); }
        50% { opacity: 1; transform: translateY(-50%) scale(1.15); }
    }
    
    .hero-stats {
        display: flex;
        gap: 16px;
        justify-content: center;
        margin-top: 24px;
        flex-wrap: wrap;
    }
    
    .hero-stat-item {
        text-align: center;
        padding: 12px 20px;
        background: linear-gradient(135deg, rgba(255,255,255,0.15) 0%, rgba(255,255,255,0.08) 100%);
        backdrop-filter: blur(20px);
        border-radius: 18px;
        border: 2px solid rgba(255,255,255,0.3);
        min-width: 105px;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        position: relative;
        overflow: hidden;
    }
    
    .hero-stat-item::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
        transition: left 0.6s;
    }
    
    .hero-stat-item:hover::before {
        left: 100%;
    }
    
    .hero-stat-item:hover {
        transform: translateY(-5px) scale(1.03);
        background: linear-gradient(135deg, rgba(255,255,255,0.22) 0%, rgba(255,255,255,0.12) 100%);
        box-shadow: 0 12px 30px rgba(0,0,0,0.25);
        border-color: rgba(255,255,255,0.45);
    }
    
    .hero-stat-number {
        font-size: 1.5rem;
        font-weight: 900;
        color: #fff;
        display: block;
        line-height: 1;
        margin-bottom: 6px;
        text-shadow: 0 2px 10px rgba(0,0,0,0.35);
        background: linear-gradient(135deg, #fff 0%, #bfdbfe 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    
    .hero-stat-label {
        font-size: 0.65rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: rgba(255,255,255,0.88);
        font-weight: 700;
    }
    
    @media (max-width: 768px) {
        .hero-vault { 
            min-height: 280px; 
            padding: 30px 5%;
            border-radius: 0 0 35px 35px;
        }
        .brand-logo-container { width: 80px; height: 80px; padding: 14px; }
        .hero-vault h1 { font-size: 1.9rem; letter-spacing: -1px; }
        .hero-subtitle { font-size: 0.7rem; letter-spacing: 2px; padding: 0 18px; }
        .hero-stats { gap: 12px; margin-top: 18px; }
        .hero-stat-item { min-width: 85px; padding: 10px 16px; }
        .hero-stat-number { font-size: 1.2rem; }
        .hero-stat-label { font-size: 0.6rem; }
    }

    /* --- BRAND NAVIGATION SECTION --- */
    .brand-nav-section {
        background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
        padding: 45px 0;
        margin: 0 auto 50px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.08);
        position: relative;
        overflow: hidden;
    }
    
    .brand-nav-section::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, 
            var(--primary) 0%, 
            rgba(225,29,72,0.8) 25%, 
            rgba(251,191,36,0.8) 50%, 
            rgba(34,197,94,0.8) 75%, 
            var(--primary) 100%);
        animation: shimmerLine 3s linear infinite;
    }
    
    @keyframes shimmerLine {
        0% { transform: translateX(-100%); }
        100% { transform: translateX(100%); }
    }
    
    .brand-nav-header {
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 35px;
        padding: 0 5%;
        animation: fadeInDown 0.8s ease-out;
    }
    
    @keyframes fadeInDown {
        from { opacity: 0; transform: translateY(-30px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .brand-nav-title {
        font-size: 2rem;
        font-weight: 900;
        color: var(--navy);
        display: flex;
        align-items: center;
        gap: 15px;
        text-transform: uppercase;
        letter-spacing: 1px;
        position: relative;
        padding: 0 30px;
    }
    
    .brand-nav-title::before,
    .brand-nav-title::after {
        content: '';
        width: 60px;
        height: 3px;
        background: linear-gradient(90deg, transparent, var(--primary));
        animation: expandLine 2s ease-in-out infinite alternate;
    }
    
    .brand-nav-title::after {
        background: linear-gradient(90deg, var(--primary), transparent);
    }
    
    @keyframes expandLine {
        0% { width: 40px; opacity: 0.5; }
        100% { width: 80px; opacity: 1; }
    }
    
    .brand-nav-title i {
        color: var(--primary);
        font-size: 1.8rem;
        animation: rotateIcon 4s linear infinite;
        filter: drop-shadow(0 4px 8px rgba(81,137,146,0.3));
    }
    
    @keyframes rotateIcon {
        0%, 90% { transform: rotateY(0deg); }
        95% { transform: rotateY(180deg); }
        100% { transform: rotateY(360deg); }
    }
    
    .brand-nav-wrapper {
        position: relative;
        overflow: hidden;
        mask-image: linear-gradient(90deg, 
            transparent 0%, 
            black 10%, 
            black 90%, 
            transparent 100%);
        -webkit-mask-image: linear-gradient(90deg, 
            transparent 0%, 
            black 10%, 
            black 90%, 
            transparent 100%);
    }
    
    .brand-nav-grid {
        display: flex;
        gap: 20px;
        animation: autoScroll 30s linear infinite;
        width: max-content;
    }
    
    .brand-nav-grid:hover {
        animation-play-state: paused;
    }
    
    @keyframes autoScroll {
        0% { transform: translateX(0); }
        100% { transform: translateX(-50%); }
    }
    
    .brand-nav-item {
        position: relative;
        background: linear-gradient(135deg, #ffffff 0%, #fafbff 100%);
        border: 2px solid transparent;
        border-radius: 20px;
        padding: 20px 15px;
        text-align: center;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        cursor: pointer;
        text-decoration: none;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 12px;
        overflow: hidden;
        min-width: 150px;
        flex-shrink: 0;
        box-shadow: 0 5px 20px rgba(0,0,0,0.06);
    }
    
    .brand-nav-item::before {
        content: '';
        position: absolute;
        top: -2px;
        left: -2px;
        right: -2px;
        bottom: -2px;
        background: linear-gradient(135deg, 
            var(--primary) 0%, 
            rgba(225,29,72,0.8) 50%, 
            rgba(251,191,36,0.8) 100%);
        border-radius: 20px;
        z-index: -1;
        opacity: 0;
        transition: opacity 0.4s;
    }
    
    .brand-nav-item::after {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, 
            transparent, 
            rgba(255,255,255,0.5), 
            transparent);
        transition: left 0.6s;
    }
    
    .brand-nav-item:hover::after {
        left: 100%;
    }
    
    .brand-nav-item:hover {
        transform: translateY(-8px) scale(1.05);
        border-color: transparent;
        box-shadow: 
            0 15px 40px rgba(81,137,146,0.25),
            0 0 0 4px rgba(81,137,146,0.1);
    }
    
    .brand-nav-item:hover::before {
        opacity: 1;
        animation: rotateBorder 2s linear infinite;
    }
    
    @keyframes rotateBorder {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    
    .brand-nav-item.active {
        background: linear-gradient(135deg, #e0f2f4 0%, #ffffff 100%);
        box-shadow: 
            0 8px 30px rgba(81,137,146,0.3),
            inset 0 2px 10px rgba(81,137,146,0.1);
        border-color: var(--primary);
    }
    
    .brand-nav-item.active::after {
        content: '✓';
        position: absolute;
        top: 10px;
        right: 10px;
        background: linear-gradient(135deg, var(--primary), #2d5f6f);
        color: white;
        width: 28px;
        height: 28px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.8rem;
        font-weight: 900;
        box-shadow: 0 4px 15px rgba(81,137,146,0.4);
        animation: popIn 0.5s cubic-bezier(0.68, -0.55, 0.265, 1.55);
        left: auto;
    }
    
    @keyframes popIn {
        0% { transform: scale(0) rotate(-180deg); }
        100% { transform: scale(1) rotate(0deg); }
    }
    
    .brand-nav-logo {
        width: 80px;
        height: 80px;
        border-radius: 16px;
        background: linear-gradient(135deg, #f8fafc 0%, #ffffff 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 12px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        transition: all 0.4s ease;
        position: relative;
        overflow: hidden;
    }
    
    .brand-nav-logo::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 0;
        height: 0;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(81,137,146,0.2), transparent);
        transform: translate(-50%, -50%);
        transition: all 0.5s ease;
    }
    
    .brand-nav-item:hover .brand-nav-logo::before {
        width: 150%;
        height: 150%;
    }
    
    .brand-nav-item:hover .brand-nav-logo {
        transform: scale(1.15) rotate(5deg);
        box-shadow: 0 8px 25px rgba(81,137,146,0.25);
    }
    
    .brand-nav-logo img {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
        filter: drop-shadow(0 2px 8px rgba(0,0,0,0.1));
        transition: filter 0.3s;
        position: relative;
        z-index: 1;
    }
    
    .brand-nav-item:hover .brand-nav-logo img {
        filter: drop-shadow(0 4px 12px rgba(81,137,146,0.3));
    }
    
    .brand-nav-name {
        font-size: 0.9rem;
        font-weight: 800;
        color: var(--navy);
        line-height: 1.3;
        transition: all 0.3s;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .brand-nav-item:hover .brand-nav-name {
        color: var(--primary);
        transform: scale(1.05);
        text-shadow: 0 2px 8px rgba(81,137,146,0.2);
    }
    
    @media (max-width: 768px) {
        .brand-nav-section {
            padding: 30px 0;
            margin-bottom: 35px;
        }
        
        .brand-nav-title {
            font-size: 1.3rem;
            padding: 0 15px;
        }
        
        .brand-nav-title::before,
        .brand-nav-title::after {
            width: 30px;
        }
        
        .brand-nav-grid {
            gap: 15px;
            animation: autoScroll 25s linear infinite;
        }
        
        .brand-nav-item {
            min-width: 120px;
            padding: 15px 10px;
        }
        
        .brand-nav-logo {
            width: 60px;
            height: 60px;
        }
        
        .brand-nav-name {
            font-size: 0.75rem;
        }
    }

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

    .diet-mark { position: absolute; top: 20px; left: 20px; width: 16px; height: 16px; border: 2px solid; display: flex; align-items: center; justify-content: center; z-index: 5; background: #fff; border-radius: 2px; }    
    .veg { border-color: var(--veg); } .veg::after { content: ''; width: 8px; height: 8px; background: var(--veg); border-radius: 50%; }
    .nonveg { border-color: var(--nonveg); } .nonveg::after { content: ''; width: 8px; height: 8px; background: var(--nonveg); border-radius: 50%; }

    .img-box { height: 180px; overflow: hidden; border-radius: 15px; margin-bottom: 15px; display: flex; align-items: center; justify-content: center; background: #fff; }
    .product-img { max-width: 90%; max-height: 90%; object-fit: contain; transition: 0.8s ease; }
    .product-card:hover .product-img { transform: scale(1.15); }

    .prod-brand { font-size: 10px; color: var(--primary); font-weight: 800; text-transform: uppercase; margin-bottom: 5px; }
    .prod-name { font-size: 15px; font-weight: 700; height: 42px; overflow: hidden; line-height: 1.4; color: var(--navy); margin-bottom: auto; }

    .price-zone { margin: 15px 0; display: flex; align-items: baseline; gap: 8px; }
    .price-main { font-size: 22px; font-weight: 900; color: var(--navy); }

    .btn-action { background: var(--accent); color: #fff; width: 100%; padding: 14px; border-radius: 15px; font-weight: 800; text-transform: uppercase; border: none; font-size: 12px; cursor: pointer; }

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

<?php
// DYNAMIC Product count based on current filters
$count_sql = "SELECT COUNT(DISTINCT p.id) as total 
              FROM products p 
              INNER JOIN product_variants v ON p.id = v.product_id
              WHERE p.is_available = 1 AND p.brand_name = '$brand_name'";

// Apply same filters as main query
if($current_stage) { $count_sql .= " AND p.life_stage = '$current_stage'"; }
if($diet_filter) { $count_sql .= " AND p.veg_type = '$diet_filter'"; }

$product_count_query = mysqli_query($conn, $count_sql);
if($product_count_query) {
    $product_count_data = mysqli_fetch_assoc($product_count_query);
    $product_count = $product_count_data ? $product_count_data['total'] : 0;
} else {
    $product_count = 0;
}

// Get category count
$category_count_query = mysqli_query($conn, "SELECT COUNT(DISTINCT p.category) as total FROM products p WHERE p.brand_name = '$brand_name' AND p.is_available = 1");
if($category_count_query) {
    $category_count_data = mysqli_fetch_assoc($category_count_query);
    $category_count = $category_count_data ? $category_count_data['total'] : 0;
} else {
    $category_count = 0;
}

// Check if brand has Food products (sub_cat_id = 3) - Only show dietary filter for food brands
$brand_category_query = mysqli_query($conn, "SELECT sub_cat_id FROM products WHERE brand_name = '$brand_name' AND is_available = 1 AND sub_cat_id = 3 LIMIT 1");
$show_dietary_filter = (mysqli_num_rows($brand_category_query) > 0);

// Fetch all active brands for navigation
$all_brands_query = mysqli_query($conn, "SELECT brand_name, brand_logo FROM brands WHERE status='active' ORDER BY brand_name ASC");
?>

<div class="hero-vault animate__animated animate__fadeIn">
    <div class="hero-glimmer"></div>
    <div class="hero-content-wrapper animate__animated animate__zoomIn">
        <?php if($brand_info['brand_logo']): ?>
            <div class="brand-logo-container">
                <img src="uploads/brands/<?php echo $brand_info['brand_logo']; ?>" class="brand-logo-hero" alt="<?php echo $brand_name; ?>">
            </div>
        <?php endif; ?>
        <h1><?php echo htmlspecialchars($brand_name); ?></h1>
        <p class="hero-subtitle">Premium Quality Pet Products</p>
        
        <div class="hero-stats">
            <div class="hero-stat-item">
                <span class="hero-stat-number"><?php echo $product_count; ?>+</span>
                <span class="hero-stat-label">Products</span>
            </div>
            <div class="hero-stat-item">
                <span class="hero-stat-number"><?php echo $category_count; ?>+</span>
                <span class="hero-stat-label">Categories</span>
            </div>
            <div class="hero-stat-item">
                <span class="hero-stat-number">4.8</span>
                <span class="hero-stat-label"><i class="fas fa-star" style="color:#fbbf24; font-size:0.7rem;"></i> Rating</span>
            </div>
        </div>
    </div>
</div>

<div class="catalog-viewport">
    <aside class="sidebar-filters">
        <h3 style="font-weight: 900; margin-bottom: 20px; font-size: 15px;">Filters</h3>
        
        <div class="filter-group">
            <h4>Life Stage Mapping</h4>
            <a href="brand.php?name=<?php echo urlencode($brand_name); ?>" class="st-link <?php if(!$current_stage) echo 'active'; ?>">All Stages</a>  
            <?php if(strtolower($brand_name) != 'whiskas' && strtolower($brand_name) != 'sheba'): ?>
            <a href="?<?php echo http_build_query(array_merge($_GET, ['stage' => 'Puppy'])); ?>" class="st-link <?php if($current_stage=='Puppy') echo 'active'; ?>">Puppy</a>
            <?php endif; ?>
            <?php if(strtolower($brand_name) != 'pedigree' && strtolower($brand_name) != 'fresh for paws' && strtolower($brand_name) != 'sktars'): ?>
            <a href="?<?php echo http_build_query(array_merge($_GET, ['stage' => 'Kitten'])); ?>" class="st-link <?php if($current_stage=='Kitten') echo 'active'; ?>">Kitten</a>
            <?php endif; ?>
            <a href="?<?php echo http_build_query(array_merge($_GET, ['stage' => 'Adult'])); ?>" class="st-link <?php if($current_stage=='Adult') echo 'active'; ?>">Adult</a>
            <a href="?<?php echo http_build_query(array_merge($_GET, ['stage' => 'Senior'])); ?>" class="st-link <?php if($current_stage=='Senior') echo 'active'; ?>">Senior</a>
        </div>
        
        <div class="filter-group">
            <h4>Price Bracket Mapping</h4>
            <a href="?<?php echo http_build_query(array_merge($_GET, ['price' => 'under500'])); ?>"
               class="st-link <?php if($price_filter=='under500') echo 'active'; ?>">Under ₹500</a>
            <a href="?<?php echo http_build_query(array_merge($_GET, ['price' => '500-1000'])); ?>"
               class="st-link <?php if($price_filter=='500-1000') echo 'active'; ?>">₹500 - ₹1000</a>      
            <a href="?<?php echo http_build_query(array_merge($_GET, ['price' => '1000-2000'])); ?>"
               class="st-link <?php if($price_filter=='1000-2000') echo 'active'; ?>">₹1000 - ₹2000</a>
            <a href="?<?php echo http_build_query(array_merge($_GET, ['price' => 'above2000'])); ?>"
               class="st-link <?php if($price_filter=='above2000') echo 'active'; ?>">Above ₹2000</a>        
        </div>
        
        <?php if($show_dietary_filter): ?>
        <div class="filter-group" style="border:none;">
            <h4>Dietary Reference</h4>
            <a href="?<?php echo http_build_query(array_merge($_GET, ['diet' => 'Veg'])); ?>"
               class="st-link <?php if($diet_filter=='Veg') echo 'active'; ?>">Vegetarian</a>
            <a href="?<?php echo http_build_query(array_merge($_GET, ['diet' => 'Non-Veg'])); ?>"
               class="st-link <?php if($diet_filter=='Non-Veg') echo 'active'; ?>">Non-Vegetarian</a>
        </div>
        <?php endif; ?>
    </aside>

    <main class="product-grid">
        <?php 
        if(mysqli_num_rows($result) > 0):
            while($p = mysqli_fetch_assoc($result)):
                $in_wish = isInWishlist($conn, $p['id']);
                $is_out_of_stock = ($p['stock_qty'] <= 0);
        ?>
        <div class="product-card <?php echo $is_out_of_stock ? 'out-of-stock' : ''; ?> animate__animated animate__fadeInUp">
            <?php if($is_out_of_stock): ?>
            <div class="sold-out-badge">
                <i class="fas fa-ban"></i> SOLD OUT
            </div>
            <?php endif; ?>
            
            <!-- Diet Mark - ONLY show for Food Products (sub_cat_id = 3) -->
            <?php if(isset($p['sub_cat_id']) && $p['sub_cat_id'] == 3): ?>
            <div class="diet-mark <?php echo ($p['veg_type'] == 'Veg') ? 'veg' : 'nonveg'; ?>"></div>
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
        <?php 
            endwhile;
        else:
        ?>
        <div style="grid-column: 1/-1; text-align: center; padding: 60px 20px;">
            <i class="fas fa-box-open" style="font-size: 80px; color: var(--gray); margin-bottom: 20px;"></i>
            <h3 style="color: var(--navy); font-weight: 900; margin-bottom: 10px;">No Products Found</h3>
            <p style="color: var(--gray);">Try adjusting your filters or check back later for new products from <?php echo $brand_name; ?></p>
        </div>
        <?php endif; ?>
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
                    <span id="qv-diet-badge" class="badge-pill"></span>
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
const qvBox = document.getElementById('qvBox');
const loader = document.getElementById('loaderOverlay');

const formatINR = (amount) => {
    return new Intl.NumberFormat('en-IN', {
        style: 'currency',
        currency: 'INR',
        maximumFractionDigits: 0
    }).format(amount);
};

function startIntelligenceLoading(p) {
    loader.style.display = 'flex';
    setTimeout(() => {
        loader.style.display = 'none';
        launchQuickView(p);
    }, 600);
}

let currentProduct = null;

function launchQuickView(p) {
    currentProduct = p;

    const scrollZone = document.querySelector('.console-scroll-zone');
    if(scrollZone) scrollZone.scrollTop = 0;

    const modalImg = document.getElementById('qv-img');
    modalImg.style.opacity = '0';
    modalImg.src = 'uploads/products/' + p.base_image;
    modalImg.onload = () => {
        modalImg.style.transition = 'opacity 0.4s ease';
        modalImg.style.opacity = '1';
    };

    document.getElementById('qv-brand').innerText = p.brand_name || 'Premium Brand';
    document.getElementById('qv-name').innerText = p.name;

    const price = parseFloat(p.price) || 0;
    const mrp = parseFloat(p.mrp) || 0;
    const savings = mrp - price;
    const savePercent = mrp > 0 ? Math.round((savings / mrp) * 100) : 0;

    document.getElementById('qv-price').innerText = formatINR(price);
    document.getElementById('qv-mrp').innerText = formatINR(mrp);

    const saveElement = document.getElementById('qv-save');
    if (savings > 0) {
        saveElement.innerHTML = `SAVE ${formatINR(savings)} <small style="font-size:10px; opacity:0.8;">(${savePercent}% OFF)</small>`;
        saveElement.style.color = 'var(--veg)';
    } else {
        saveElement.innerText = 'Best Price Guaranteed';
    }

    document.getElementById('qv-desc').innerText = p.description || 'Nutritional specifications for this protocol are being updated.';
    document.getElementById('qv-weight').innerHTML = `<i class="fas fa-weight-hanging" style="color:var(--primary)"></i> ${p.weight_size}`;

    const stockQty = parseInt(p.stock_qty) || 0;
    const stockEl = document.getElementById('qv-stock');
    if (stockQty <= 5 && stockQty > 0) {
        stockEl.style.color = 'var(--nonveg)';
        stockEl.innerHTML = `<i class="fas fa-fire-flame-curved"></i> Only ${stockQty} Left - High Demand`;    
    } else {
        stockEl.style.color = 'var(--navy)';
        stockEl.innerHTML = `<i class="fas fa-boxes-stacked" style="color:var(--primary)"></i> ${stockQty} Units Optimized`;
    }

    document.getElementById('qv-stage-badge').innerText = p.life_stage;
    
    // Badge Stylizing - ONLY for Food Products (sub_cat_id = 3)
    const dBadge = document.getElementById('qv-diet-badge');
    const isFoodProduct = (p.sub_cat_id == 3);
    
    if(isFoodProduct) {
        dBadge.innerText = p.veg_type;
        const isVeg = (p.veg_type === 'Veg');
        dBadge.style.background = isVeg ? 'rgba(34, 197, 94, 0.1)' : 'rgba(239, 68, 68, 0.1)';
        dBadge.style.color = isVeg ? 'var(--veg)' : 'var(--nonveg)';
        dBadge.style.display = 'inline-block';
    } else {
        dBadge.style.display = 'none';
    }

    qvBox.style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function closeQuickView() {
    qvBox.style.display = 'none';
    document.body.style.overflow = 'auto';
}

window.addEventListener('click', function(e) {
    if (e.target === qvBox) closeQuickView();
});

window.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && qvBox.style.display === 'flex') {
        closeQuickView();
    }
});

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
        updateHeaderCounts();

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
            showToast("Added to FurryMart Wishlist ♥", "success");

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
</script>

<!-- Brand Navigation Section -->
<div class="brand-nav-section animate__animated animate__fadeInUp">
    <div class="brand-nav-header">
        <h2 class="brand-nav-title">
            <i class="fas fa-layer-group"></i>
            Explore All Brands
        </h2>
    </div>
    <div class="brand-nav-wrapper">
        <div class="brand-nav-grid">
            <?php 
            // Reset the pointer for the brands query
            mysqli_data_seek($all_brands_query, 0);
            $brands_array = [];
            while($brand = mysqli_fetch_assoc($all_brands_query)) {
                $brands_array[] = $brand;
            }
            
            // Display brands twice for seamless infinite scroll
            for($i = 0; $i < 2; $i++):
                foreach($brands_array as $brand): 
            ?>
                <a href="brand.php?name=<?php echo urlencode($brand['brand_name']); ?>" 
                   class="brand-nav-item <?php echo ($brand['brand_name'] == $brand_name) ? 'active' : ''; ?>">
                    <div class="brand-nav-logo">
                        <?php if($brand['brand_logo']): ?>
                            <img src="uploads/brands/<?php echo $brand['brand_logo']; ?>" alt="<?php echo htmlspecialchars($brand['brand_name']); ?>">
                        <?php else: ?>
                            <i class="fas fa-image" style="color: #cbd5e1; font-size: 2rem;"></i>
                        <?php endif; ?>
                    </div>
                    <div class="brand-nav-name"><?php echo htmlspecialchars($brand['brand_name']); ?></div>
                </a>
            <?php 
                endforeach;
            endfor; 
            ?>
        </div>
    </div>
</div>

<?php include "includes/footer.php"; ?>
