<?php 
include "db.php"; 
if (session_status() === PHP_SESSION_NONE) { session_start(); }
?>
<?php include "includes/header.php"; ?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<?php
if (!function_exists('fm_time_ago')) {
    function fm_time_ago($timestamp) {
        $time_ago = strtotime($timestamp);
        $current_time = time();
        $time_diff = $current_time - $time_ago;
        $seconds = $time_diff;
        $minutes = round($seconds / 60);
        $hours = round($seconds / 3600);
        $days = round($seconds / 86400);
        if($seconds <= 60) return "Just now";
        else if($minutes <= 60) return ($minutes==1) ? "1 min ago" : "$minutes mins ago";
        else if($hours <= 24) return ($hours==1) ? "1 hr ago" : "$hours hrs ago";
        else if($days <= 30) return ($days==1) ? "Yesterday" : "$days days ago";
        else return date('M d, Y', $time_ago);
    }
}
?>

<style>
    /* --- PREVIOUS SECTIONS (STRICTLY UNTOUCHED PER REQUEST) --- */
    .hero-wrapper { width: 100%; position: relative; overflow: hidden; }
    .hero-swiper { width: 100%; height: 75vh; }
    .swiper-slide { position: relative; overflow: hidden; display: flex; align-items: center; }
    .slide-bg { position: absolute; top: 0; left: 0; width: 100%; height: 100%; background-size: cover; background-position: center; z-index: 1; transform: scale(1.1); transition: transform 6s linear; }
    .swiper-slide-active .slide-bg { transform: scale(1); } 
    .slide-overlay { position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: linear-gradient(to right, rgba(0,0,0,0.5) 0%, rgba(0,0,0,0) 60%); z-index: 2; }
    .hero-content { position: relative; z-index: 3; margin-left: 8%; max-width: 700px; color: white; }
    .hero-content h1 { font-size: 3rem; font-weight: 800; margin-bottom: 15px; line-height: 1.1; text-transform: uppercase; text-shadow: 2px 2px 15px rgba(0,0,0,0.6); transform: translateY(30px); opacity: 0; transition: all 0.8s ease 0.5s; }
    .hero-content p { font-size: 1.1rem; margin-bottom: 30px; font-weight: 400; text-shadow: 1px 1px 10px rgba(0,0,0,0.6); transform: translateY(30px); opacity: 0; transition: all 0.8s ease 0.7s; }
    .swiper-slide-active h1, .swiper-slide-active p { transform: translateY(0); opacity: 1; }
    .swiper-pagination-bullet { background: white !important; opacity: 0.5; }
    .swiper-pagination-bullet-active { opacity: 1; width: 30px; border-radius: 5px; }

    .breed-ready-section { padding: 80px 5% 30px; background: #fdfaf7; position: relative; }
    .breed-header-flex { display: flex; justify-content: space-between; align-items: center; margin-bottom: 50px; }
    .breed-title-area h2 { font-size: 2.6rem; font-weight: 800; color: #1a1c1e; margin: 0; letter-spacing: -1px; }
    .breed-switch { display: flex; background: #fff; border: 1px solid #e2e8f0; border-radius: 50px; padding: 5px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
    .breed-btn { border: none; background: none; padding: 10px 30px; border-radius: 50px; font-weight: 700; cursor: pointer; transition: 0.4s; color: #64748b; }
    .breed-btn.active { background: #e11d48; color: #fff; box-shadow: 0 4px 12px rgba(225, 29, 72, 0.3); }
    .breed-swiper-container { position: relative; padding: 0 40px; }
    .breed-nav-btn { background: #fff !important; width: 45px !important; height: 45px !important; border-radius: 50% !important; box-shadow: 0 4px 12px rgba(0,0,0,0.1) !important; color: var(--primary) !important; border: 1px solid #eee !important; }
    .breed-nav-btn:after { font-size: 18px !important; font-weight: 900; }
    .breed-container { display: none; animation: slideInUp 0.6s ease; }
    .breed-container.active { display: block; }
    .breed-card { text-align: center; text-decoration: none; display: block; padding: 10px; transition: 0.3s; }
    .breed-circle-wrapper { width: 170px; height: 170px; border-radius: 50%; margin: 0 auto 15px; position: relative; overflow: hidden; border: 4px solid #fff; background: #ffb84d; box-shadow: 0 10px 25px rgba(0,0,0,0.08); transition: all 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275); }
    .breed-circle-wrapper img { width: 100%; height: 100%; object-fit: cover; transition: 0.5s ease; }
    .breed-name-label { font-weight: 700; font-size: 1.1rem; color: #1e293b; display: block; margin-top: 12px; transition: 0.3s; position: relative; }

    .feedback-section { padding: 30px 8% 60px; background: #ffffff; }
    .google-trust-card { background: #f1f7f9; border: 1px solid #e1ebed; border-radius: 35px; padding: 45px; margin-bottom: 60px; display: flex; align-items: center; justify-content: space-between; gap: 30px; }
    .btn-google-large { background: #0f1c3f; color: #fff; border-radius: 60px; font-weight: 800; font-size: 16px; text-transform: uppercase; padding: 22px 45px; letter-spacing: 1.5px; text-decoration: none; transition: 0.4s; display: inline-block; min-width: 280px; text-align: center; }
    .btn-google-large:hover { background: #518992; transform: translateY(-5px); box-shadow: 0 15px 30px rgba(15, 28, 63, 0.15); }
    .square-feedback-box { background: #f6f9fb; border: 1.5px solid #edf2f4; border-radius: 40px; padding: 30px; height: 320px; width: 100%; display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center; transition: 0.6s cubic-bezier(0.34, 1.56, 0.64, 1); }
    .square-feedback-box:hover { transform: translateY(-10px); background: #ffffff; border-color: #518992; box-shadow: 0 30px 60px rgba(15, 28, 63, 0.08); }
    .fb-avatar-circle { width: 75px; height: 75px; background: #518992; color: #fff; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 28px; font-weight: 800; margin-bottom: 20px; box-shadow: 0 10px 20px rgba(81, 137, 146, 0.15); }
    .fb-user-title h6 { font-size: 17px; font-weight: 800; color: #0f1c3f; margin-bottom: 5px; }
    .fb-badge-verified { font-size: 9px; font-weight: 800; background: #e0f2f1; color: #00897b; padding: 4px 12px; border-radius: 50px; text-transform: uppercase; letter-spacing: 1px; }
    .fb-star-hub { color: #f43f5e; font-size: 14px; margin: 15px 0; }
    .fb-trigger-btn { color: #518992; font-weight: 800; font-size: 14px; cursor: pointer !important; text-decoration: underline; transition: 0.3s; margin-top: 10px; border: none; background: none; outline: none; }
    .fb-posted-relative { font-size: 10px; color: #94a3b8; font-weight: 700; margin-top: 20px; }
    .review-dot-pagination { margin-top: 60px; text-align: center; }
    .review-dot-pagination .swiper-pagination-bullet-active { background: #518992 !important; width: 40px; border-radius: 10px; }

    .makeover-section { padding: 40px 8% 80px; background: #ffffff; }
    .makeover-title { text-align: center; color: #0f1c3f; font-weight: 800; font-size: 3.2rem; letter-spacing: -2px; margin-bottom: 60px; }
    .makeover-card { background: #ffffff; border: 1.5px solid #eef2f6; border-radius: 40px; padding: 18px; transition: 0.5s cubic-bezier(0.34, 1.56, 0.64, 1); box-shadow: 0 10px 30px rgba(15, 28, 63, 0.02); }
    .makeover-card:hover { border-color: #518992; transform: translateY(-8px); box-shadow: 0 35px 70px rgba(15, 28, 63, 0.08); }
    .m-img-box { position: relative; border-radius: 25px; overflow: hidden; aspect-ratio: 1 / 1; width: 100%; border-width: 3px; border-style: solid; cursor: crosshair; }
    .m-img-box img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.8s cubic-bezier(0.2, 1, 0.3, 1); }
    .m-img-box:hover img { transform: scale(1.3); }
    .m-before { border-color: #bae6fd; }
    .m-after { border-color: #bbf7d0; margin-top: 15px; }
    .m-badge { position: absolute; bottom: 0; left: 0; padding: 8px 30px; font-weight: 900; font-size: 11px; text-transform: uppercase; border-top-right-radius: 25px; letter-spacing: 1.5px; z-index: 10; }
    .badge-blue { background: #e0f2fe; color: #0369a1; }
    .badge-green { background: #f0fdf4; color: #15803d; }

    /* --- NEW ARRIVALS & BEST SELLERS SECTIONS --- */
    .new-arrivals-section, .best-sellers-section { padding: 60px 8% 50px; background: #fff; position: relative; }
    .best-sellers-section { background: #fdfaf7; }
    .section-header { text-align: center; margin-bottom: 40px; }
    .section-header h2 { font-size: 2.2rem; font-weight: 900; color: #0f1c3f; letter-spacing: -1px; margin-bottom: 8px; position: relative; display: inline-block; }
    .section-header h2::after { content: ''; position: absolute; bottom: -8px; left: 50%; transform: translateX(-50%); width: 60px; height: 3px; background: linear-gradient(90deg, #518992, #e11d48); border-radius: 10px; }
    .section-header p { color: #64748b; font-size: 0.95rem; margin-top: 15px; }
    .new-badge { position: absolute; top: 12px; right: 12px; background: linear-gradient(135deg, #22c55e, #16a34a); color: #fff; padding: 4px 12px; border-radius: 15px; font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; box-shadow: 0 4px 12px rgba(34, 197, 94, 0.3); animation: pulseGlow 2s infinite; z-index: 5; }
    .best-badge { position: absolute; top: 12px; right: 12px; background: linear-gradient(135deg, #f59e0b, #d97706); color: #fff; padding: 4px 12px; border-radius: 15px; font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3); animation: pulseGlow 2s infinite; z-index: 5; }
    @keyframes pulseGlow { 0%, 100% { transform: scale(1); opacity: 1; } 50% { transform: scale(1.05); opacity: 0.9; } }

    .products-grid { display: grid; grid-template-columns: repeat(5, 1fr); gap: 20px; }
    @media (max-width: 1400px) { .products-grid { grid-template-columns: repeat(4, 1fr); } }
    @media (max-width: 1024px) { .products-grid { grid-template-columns: repeat(3, 1fr); } }
    @media (max-width: 768px) { .products-grid { grid-template-columns: repeat(2, 1fr); gap: 15px; } }
    @media (max-width: 480px) { .products-grid { grid-template-columns: 1fr; } }
    
    .product-showcase-card { background: #fff; border-radius: 20px; padding: 15px; position: relative; border: 1.5px solid #f1f5f9; transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1); box-shadow: 0 2px 10px rgba(0,0,0,0.02); height: 380px; display: flex; flex-direction: column; }
    .product-showcase-card:hover { transform: translateY(-8px); border-color: #518992; box-shadow: 0 15px 35px rgba(15, 28, 63, 0.1); }
    .product-img-box { height: 150px; display: flex; align-items: center; justify-content: center; border-radius: 15px; background: #f8fafc; overflow: hidden; margin-bottom: 12px; position: relative; }
    .product-img-box img { max-width: 80%; max-height: 80%; object-fit: contain; transition: 0.5s; }
    .product-showcase-card:hover .product-img-box img { transform: scale(1.08); }
    .diet-mark { position: absolute; top: 10px; left: 10px; width: 16px; height: 16px; border: 2px solid; display: flex; align-items: center; justify-content: center; z-index: 5; background: #fff; border-radius: 2px; }
    .veg-mark { border-color: #22c55e; } .veg-mark::after { content: ''; width: 9px; height: 9px; background: #22c55e; border-radius: 50%; }
    .nonveg-mark { border-color: #ef4444; } .nonveg-mark::after { content: ''; width: 9px; height: 9px; background: #ef4444; border-radius: 50%; }
    .wishlist-btn-card { position: absolute; top: 10px; left: 10px; background: #fff; width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; border: none; box-shadow: 0 3px 12px rgba(0,0,0,0.1); cursor: pointer; transition: 0.3s; z-index: 10; }
    .wishlist-btn-card:hover { background: #fff1f2; transform: scale(1.1); }
    .wishlist-btn-card i { color: #f87171; font-size: 14px; }
    .wishlist-btn-card.in-wishlist i { font-weight: 900; }
    .brand-tag-small { font-size: 10px; color: #518992; font-weight: 800; text-transform: uppercase; margin-bottom: 6px; letter-spacing: 0.5px; }
    .product-name-showcase { font-size: 14px; font-weight: 800; color: #0f1c3f; margin-bottom: 8px; line-height: 1.3; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; min-height: 36px; }
    .weight-display { background: #f1f5f9; padding: 4px 10px; border-radius: 6px; font-size: 10px; font-weight: 800; color: #64748b; display: inline-block; margin-bottom: 8px; }
    .price-row { display: flex; align-items: center; gap: 8px; margin: 8px 0; flex-wrap: wrap; }
    .price-main { font-size: 20px; font-weight: 900; color: #0f1c3f; }
    .price-old { font-size: 13px; color: #94a3b8; text-decoration: line-through; }
    .discount-tag { background: #fee2e2; color: #dc2626; padding: 3px 8px; border-radius: 6px; font-size: 9px; font-weight: 800; }
    .btn-actions { display: flex; gap: 8px; margin-top: auto; }
    .btn-quick-view { flex: 1; background: #fff; color: #518992; border: 1.5px solid #518992; padding: 10px 8px; border-radius: 12px; font-weight: 800; font-size: 11px; cursor: pointer; transition: 0.3s; }
    .btn-quick-view:hover { background: #518992; color: #fff; }
    .btn-add-cart { flex: 1; background: linear-gradient(135deg, #e11d48, #be123c); color: #fff; border: none; padding: 10px 8px; border-radius: 12px; font-weight: 800; font-size: 11px; cursor: pointer; transition: 0.3s; box-shadow: 0 4px 15px rgba(225, 29, 72, 0.25); }
    .btn-add-cart:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(225, 29, 72, 0.35); }
    .sold-out-badge { position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); background: linear-gradient(135deg, #ef4444, #dc2626); color: #fff; padding: 8px 16px; border-radius: 12px; font-weight: 800; font-size: 12px; text-transform: uppercase; box-shadow: 0 6px 20px rgba(239, 68, 68, 0.4); z-index: 10; animation: pulse 2s infinite; }
    .product-showcase-card.out-of-stock { opacity: 0.7; pointer-events: none; }
    .product-showcase-card.out-of-stock::after { content: ''; position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: rgba(255,255,255,0.6); border-radius: 20px; z-index: 5; }

    /* Quick View Modal */
    .quick-modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.7); z-index: 9999; align-items: center; justify-content: center; animation: fadeIn 0.3s; }
    .quick-modal.active { display: flex; }
    .modal-content-quick { background: #fff; border-radius: 30px; padding: 40px; max-width: 900px; width: 90%; max-height: 90vh; overflow-y: auto; position: relative; animation: slideUp 0.4s; }
    @keyframes slideUp { from { transform: translateY(50px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
    .modal-close-btn { position: absolute; top: 20px; right: 20px; background: #f1f5f9; width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; border: none; cursor: pointer; transition: 0.3s; }
    .modal-close-btn:hover { background: #e11d48; color: #fff; transform: rotate(90deg); }
    .modal-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 40px; }
    .modal-img-section { display: flex; align-items: center; justify-content: center; background: #f8fafc; border-radius: 20px; padding: 30px; }
    .modal-img-section img { max-width: 100%; max-height: 400px; object-fit: contain; }
    .modal-info-section h2 { font-size: 28px; font-weight: 900; color: #0f1c3f; margin-bottom: 15px; }
    .modal-brand { font-size: 13px; color: #518992; font-weight: 800; text-transform: uppercase; margin-bottom: 10px; }
    .modal-desc { color: #64748b; line-height: 1.8; margin: 20px 0; }
    .variant-selector { margin: 20px 0; }
    .variant-label { font-weight: 800; color: #0f1c3f; margin-bottom: 10px; display: block; }
    .variant-options { display: flex; gap: 10px; flex-wrap: wrap; }
    .variant-option { padding: 10px 20px; border: 2px solid #e2e8f0; border-radius: 12px; cursor: pointer; transition: 0.3s; font-weight: 700; font-size: 14px; background: #fff; }
    .variant-option:hover { border-color: #518992; }
    .variant-option.selected { border-color: #e11d48; background: #fef2f2; color: #e11d48; }

    /* --- master SOVEREIGN BRAND GRID WITH CLINICAL FILTER --- */
    .brand-section { padding: 80px 8% 120px; background: #fdfaf7; border-top: 1px solid #eee; }
    .brand-header-flex { display: flex; justify-content: space-between; align-items: center; margin-bottom: 50px; flex-wrap: wrap; gap: 20px; }
    .brand-filter-tabs { display: flex; gap: 6px; background: #fff; padding: 6px; border-radius: 60px; box-shadow: 0 10px 30px rgba(0,0,0,0.04); border: 1.5px solid #eef2f6; flex-wrap: wrap; }
    
    .filter-brand-btn { border: none; background: none; padding: 8px 16px; border-radius: 50px; font-weight: 800; font-size: 10px; color: #64748b; cursor: pointer; transition: 0.4s; text-transform: uppercase; letter-spacing: 0.3px; white-space: nowrap; }
    .filter-brand-btn.active { background: #518992; color: #fff; box-shadow: 0 8px 20px rgba(81, 137, 146, 0.3); }

    .brand-grid-container {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
        grid-template-rows: repeat(2, auto); /* FORCED PROFESSIONAL 2 LINES */
        gap: 30px;
    }

    .brand-sovereign-card {
        background: #fff;
        border: 1.5px solid #eef2f6;
        border-radius: 35px;
        padding: 35px 20px;
        text-align: center;
        text-decoration: none;
        transition: 0.6s cubic-bezier(0.34, 1.56, 0.64, 1);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        position: relative;
        box-shadow: 0 10px 25px rgba(0,0,0,0.02);
    }

    /* VIP FEATURED GLOW */
    .brand-sovereign-card.featured {
        border: 2px solid #fbbf24;
        background: linear-gradient(135deg, #ffffff 0%, #fffbeb 100%);
    }
    .brand-featured-badge {
        position: absolute; top: -12px; right: -12px; background: #fbbf24; color: #fff;
        width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center;
        font-size: 14px; border: 4px solid #fff; box-shadow: 0 5px 15px rgba(251, 191, 36, 0.4);
    }

    .brand-sovereign-card.fade-in { animation: brandPop 0.5s ease-out forwards; }
    @keyframes brandPop { from { opacity: 0; transform: scale(0.85) translateY(20px); } to { opacity: 1; transform: scale(1) translateY(0); } }

    .brand-sovereign-card:hover {
        transform: translateY(-15px) scale(1.03);
        border-color: #518992;
        box-shadow: 0 35px 70px rgba(15, 28, 63, 0.1);
    }

    .brand-sovereign-card img {
        width: 100px;
        height: auto;
        margin-bottom: 20px;
        transition: 0.5s;
        filter: grayscale(100%);
        opacity: 0.8;
    }

    .brand-sovereign-card:hover img {
        filter: grayscale(0%);
        opacity: 1;
        transform: scale(1.1);
    }

    .brand-name-tag {
        font-size: 15px;
        font-weight: 800;
        color: #1a1c1e;
        text-transform: uppercase;
        letter-spacing: 1.5px;
    }
    .breed-ready-section {
    padding-top: 30px !important;
}

/* Remove extra space after header */
.breed-header-flex {
    margin-bottom: 20px !important;
}

/* Force swiper to shrink to content */
.breed-swiper-container {
    padding-top: 0 !important;
    padding-bottom: 0 !important;
}

/* Prevent swiper slides from centering vertically */
.breed-swiper-container .swiper-slide {
    display: flex;
    justify-content: center;
    align-items: flex-start !important;
}

/* Remove any accidental min-height */
.breed-swiper-container,
.breed-swiper-container .swiper,
.breed-swiper-container .swiper-wrapper {
    min-height: 0 !important;
    height: auto !important;
}

/* Reduce space under each breed circle */
.breed-card {
    padding-top: 0 !important;
    padding-bottom: 0 !important;
}
</style>

<div class="hero-wrapper">
    <div class="swiper hero-swiper">
        <div class="swiper-wrapper">
            <?php $banners = mysqli_query($conn, "SELECT * FROM hero_banners WHERE status='active' ORDER BY sort_order ASC");
            while($b = mysqli_fetch_assoc($banners)){ ?>
                <div class="swiper-slide">
                    <div class="slide-bg" style="background-image: url('uploads/<?php echo $b['image_path']; ?>');"></div>
                    <div class="slide-overlay"></div>
                    <div class="hero-content">
                        <h1><?php echo $b['headline']; ?></h1>
                        <p><?php echo $b['subheadline']; ?></p>
                    </div>
                </div>
            <?php } ?>
        </div>
        <div class="swiper-pagination"></div>
    </div>
</div>

<section class="breed-ready-section">
    <div class="breed-header-flex">
        <div class="breed-title-area"><h2>Breed Ready Picks</h2></div>
        <div class="breed-switch">
            <button class="breed-btn active" onclick="toggleBreedCat('Dog', this)">Dog</button>
            <button class="breed-btn" onclick="toggleBreedCat('Cat', this)">Cat</button>
            <button class="breed-btn" onclick="toggleBreedCat('Bird', this)">Bird</button>
        </div>
    </div>
    <div class="breed-swiper-container">
        <?php $cats = ['Dog', 'Cat', 'Bird']; $colors = ['Dog' => '#ffb84d', 'Cat' => '#87ceeb', 'Bird' => '#98fb98'];
        foreach($cats as $cat): $active_class = ($cat == 'Dog') ? 'active' : ''; ?>
        <div class="breed-container <?php echo $active_class; ?>" id="cat-<?php echo $cat; ?>">
            <div class="swiper breed-swiper-<?php echo $cat; ?>">
                <div class="swiper-wrapper">
                    <?php $breeds = mysqli_query($conn, "SELECT * FROM breed_picks WHERE category='$cat' AND status='active' ORDER BY sort_order ASC");
                    while($row = mysqli_fetch_assoc($breeds)): ?>
                        <div class="swiper-slide">
                            <a href="<?php echo $row['details_page_url']; ?>" class="breed-card">
                                <div class="breed-circle-wrapper" style="background-color: <?php echo $colors[$cat]; ?>">
                                    <img src="uploads/breeds/<?php echo $row['image_path']; ?>" alt="<?php echo $row['breed_name']; ?>">
                                </div>
                                <span class="breed-name-label"><?php echo $row['breed_name']; ?></span>
                            </a>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>
            <div class="swiper-button-next breed-nav-btn" id="next-<?php echo $cat; ?>"></div>
            <div class="swiper-button-prev breed-nav-btn" id="prev-<?php echo $cat; ?>"></div>
        </div>
        <?php endforeach; ?>
    </div>
</section>

<!-- NEW ARRIVALS SECTION -->
<section class="new-arrivals-section">
    <div class="section-header">
        <h2>✨ New Arrivals</h2>
        <p>Discover the latest additions to our premium pet collection</p>
    </div>
    <div class="products-grid">
        <?php
        $new_products_query = "SELECT p.*, v.price, v.mrp, v.weight_size, v.stock_qty, v.id as variant_id 
                               FROM products p 
                               INNER JOIN product_variants v ON p.id = v.product_id 
                               WHERE p.is_available = 1 
                               GROUP BY p.id 
                               ORDER BY p.id DESC 
                               LIMIT 4";
        $new_products = mysqli_query($conn, $new_products_query);
        
        if($new_products && mysqli_num_rows($new_products) > 0):
            while($prod = mysqli_fetch_assoc($new_products)):
            $is_out_of_stock = ($prod['stock_qty'] <= 0);
            $discount = 0;
            if($prod['mrp'] > $prod['price']) {
                $discount = round((($prod['mrp'] - $prod['price']) / $prod['mrp']) * 100);
            }
            $in_wishlist = false;
            if(isset($_SESSION['user_email'])) {
                $email = $_SESSION['user_email'];
                $w_check = mysqli_query($conn, "SELECT id FROM wishlist WHERE user_email = '$email' AND product_id = {$prod['id']}");
                $in_wishlist = mysqli_num_rows($w_check) > 0;
            }
        ?>
        <div class="product-showcase-card <?php echo $is_out_of_stock ? 'out-of-stock' : ''; ?>" data-product-id="<?php echo $prod['id']; ?>">
            <div class="new-badge"><i class="fas fa-star"></i> New</div>
            <button class="wishlist-btn-card <?php echo $in_wishlist ? 'in-wishlist' : ''; ?>" onclick="toggleWishlist(<?php echo $prod['id']; ?>, this)">
                <i class="<?php echo $in_wishlist ? 'fas' : 'far'; ?> fa-heart"></i>
            </button>
            <div class="product-img-box">
                <div class="diet-mark <?php echo $prod['veg_type'] == 'Veg' ? 'veg-mark' : 'nonveg-mark'; ?>"></div>
                <?php if($is_out_of_stock): ?>
                    <div class="sold-out-badge"><i class="fas fa-ban"></i> SOLD OUT</div>
                <?php endif; ?>
                <img src="uploads/products/<?php echo $prod['base_image']; ?>" alt="<?php echo $prod['name']; ?>">
            </div>
            <div class="brand-tag-small"><?php echo $prod['brand_name']; ?></div>
            <h3 class="product-name-showcase"><?php echo $prod['name']; ?></h3>
            <span class="weight-display"><?php echo $prod['weight_size']; ?></span>
            <div class="price-row">
                <span class="price-main">₹<?php echo number_format($prod['price']); ?></span>
                <?php if($prod['mrp'] > $prod['price']): ?>
                    <span class="price-old">₹<?php echo number_format($prod['mrp']); ?></span>
                    <span class="discount-tag"><?php echo $discount; ?>% OFF</span>
                <?php endif; ?>
            </div>
            <div class="btn-actions">
                <button class="btn-quick-view" onclick="openQuickView(<?php echo $prod['id']; ?>)">
                    <i class="fas fa-eye"></i> Quick View
                </button>
                <?php if(!$is_out_of_stock): ?>
                    <button class="btn-add-cart" onclick="addToCartDirect(<?php echo $prod['variant_id']; ?>)">
                        <i class="fas fa-shopping-bag"></i> Add to Cart
                    </button>
                <?php else: ?>
                    <button class="btn-add-cart" disabled style="opacity: 0.5; cursor: not-allowed;">
                        <i class="fas fa-ban"></i> Out of Stock
                    </button>
                <?php endif; ?>
            </div>
        </div>
        <?php endwhile; 
        else: ?>
            <div style="text-align: center; padding: 60px 20px; color: #94a3b8;">
                <i class="fas fa-box-open" style="font-size: 60px; margin-bottom: 20px; opacity: 0.3;"></i>
                <h3 style="font-weight: 800; color: #64748b;">No New Products Yet</h3>
                <p>Check back soon for exciting new arrivals!</p>
            </div>
        <?php endif; ?>
    </div>
</section>

<section class="feedback-section">
    <div class="breed-title-area mb-5"><h2 style="color: #1a2749ff; letter-spacing: -2px; font-size: 3rem;">Real Stories, Real Smiles</h2></div>
    <div class="google-trust-card">
        <div class="d-flex align-items-center gap-3">
            <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/c/c1/Google_%22G%22_logo.svg/1200px-Google_%22G%22_logo.svg.png" width="40">
            <h3 style="font-weight: 800; color: #0f1c3f; margin:0; font-size: 24px;">Google Reviews</h3>
        </div>
        <div class="mt-3">
            <span style="font-size: 28px; font-weight: 900; color: #0f1c3f;">4.7</span>
            <span style="color: #f43f5e; font-size: 22px; margin: 0 15px;"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star-half-alt"></i></span>
            <span class="text-muted fw-700" style="font-size:15px;">(12,850 Verified Parent Stories)</span>
        </div>
        <a href="feedback.php" class="btn-google-large">Review us on Google</a>
    </div>

    <div class="swiper feedbackSlider">
        <div class="swiper-wrapper">
            <?php $fb_query = mysqli_query($conn, "SELECT * FROM customer_feedbacks WHERE status='approved' ORDER BY RAND() LIMIT 10");
            while($fb = mysqli_fetch_assoc($fb_query)):
                $u_name = $fb['user_name']; $u_text = $fb['feedback_text']; $u_time = fm_time_ago($fb['created_at']);
                $initial = strtoupper(substr($u_name, 0, 1)); $u_rating = (int)$fb['rating']; ?>
            <div class="swiper-slide">
                <div class="square-feedback-box">
                    <div class="fb-avatar-circle"><?php echo $initial; ?></div>
                    <div class="fb-user-title"><h6><?php echo htmlspecialchars($u_name); ?> <i class="fas fa-check-circle" style="color:#3b82f6; font-size:12px;"></i></h6><span class="fb-badge-verified">Verified Parent</span></div>
                    <div class="fb-star-hub"><?php for($i=1; $i<=5; $i++) { echo $i <= $u_rating ? '<i class="fas fa-star"></i>' : '<i class="far fa-star"></i>'; } ?></div>
                    <button class="fb-trigger-btn" onclick='fm_vault(<?php echo json_encode($u_name); ?>, <?php echo json_encode($u_text); ?>, "<?php echo $u_time; ?>", <?php echo $u_rating; ?>)'>Read more</button>
                    <div class="fb-posted-relative"><?php echo $u_time; ?></div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
        <div class="review-dot-pagination"></div>
    </div>
</section>

<!-- BEST SELLERS SECTION -->
<section class="best-sellers-section">
    <div class="section-header">
        <h2>🏆 Best Sellers</h2>
        <p>Our most loved products - Trusted by thousands of pet parents</p>
    </div>
    <div class="products-grid">
        <?php
        $best_sellers_query = "SELECT p.*, v.price, v.mrp, v.weight_size, v.stock_qty, v.id as variant_id, 
                               COALESCE(SUM(oi.quantity), 0) as total_orders 
                               FROM products p 
                               INNER JOIN product_variants v ON p.id = v.product_id 
                               LEFT JOIN order_items oi ON v.id = oi.variant_id 
                               WHERE p.is_available = 1 
                               GROUP BY p.id 
                               ORDER BY total_orders DESC 
                               LIMIT 4";
        $best_sellers = mysqli_query($conn, $best_sellers_query);
        
        if($best_sellers && mysqli_num_rows($best_sellers) > 0):
            while($prod = mysqli_fetch_assoc($best_sellers)):
            $is_out_of_stock = ($prod['stock_qty'] <= 0);
            $discount = 0;
            if($prod['mrp'] > $prod['price']) {
                $discount = round((($prod['mrp'] - $prod['price']) / $prod['mrp']) * 100);
            }
            $in_wishlist = false;
            if(isset($_SESSION['user_email'])) {
                $email = $_SESSION['user_email'];
                $w_check = mysqli_query($conn, "SELECT id FROM wishlist WHERE user_email = '$email' AND product_id = {$prod['id']}");
                $in_wishlist = mysqli_num_rows($w_check) > 0;
            }
        ?>
        <div class="product-showcase-card <?php echo $is_out_of_stock ? 'out-of-stock' : ''; ?>" data-product-id="<?php echo $prod['id']; ?>">
            <div class="best-badge"><i class="fas fa-crown"></i> Best Seller</div>
            <button class="wishlist-btn-card <?php echo $in_wishlist ? 'in-wishlist' : ''; ?>" onclick="toggleWishlist(<?php echo $prod['id']; ?>, this)">
                <i class="<?php echo $in_wishlist ? 'fas' : 'far'; ?> fa-heart"></i>
            </button>
            <div class="product-img-box">
                <div class="diet-mark <?php echo $prod['veg_type'] == 'Veg' ? 'veg-mark' : 'nonveg-mark'; ?>"></div>
                <?php if($is_out_of_stock): ?>
                    <div class="sold-out-badge"><i class="fas fa-ban"></i> SOLD OUT</div>
                <?php endif; ?>
                <img src="uploads/products/<?php echo $prod['base_image']; ?>" alt="<?php echo $prod['name']; ?>">
            </div>
            <div class="brand-tag-small"><?php echo $prod['brand_name']; ?></div>
            <h3 class="product-name-showcase"><?php echo $prod['name']; ?></h3>
            <span class="weight-display"><?php echo $prod['weight_size']; ?></span>
            <div class="price-row">
                <span class="price-main">₹<?php echo number_format($prod['price']); ?></span>
                <?php if($prod['mrp'] > $prod['price']): ?>
                    <span class="price-old">₹<?php echo number_format($prod['mrp']); ?></span>
                    <span class="discount-tag"><?php echo $discount; ?>% OFF</span>
                <?php endif; ?>
            </div>
            <div class="btn-actions">
                <button class="btn-quick-view" onclick="openQuickView(<?php echo $prod['id']; ?>)">
                    <i class="fas fa-eye"></i> Quick View
                </button>
                <?php if(!$is_out_of_stock): ?>
                    <button class="btn-add-cart" onclick="addToCartDirect(<?php echo $prod['variant_id']; ?>)">
                        <i class="fas fa-shopping-bag"></i> Add to Cart
                    </button>
                <?php else: ?>
                    <button class="btn-add-cart" disabled style="opacity: 0.5; cursor: not-allowed;">
                        <i class="fas fa-ban"></i> Out of Stock
                    </button>
                <?php endif; ?>
            </div>
        </div>
        <?php endwhile;
        else: ?>
            <div style="text-align: center; padding: 60px 20px; color: #94a3b8;">
                <i class="fas fa-chart-line" style="font-size: 60px; margin-bottom: 20px; opacity: 0.3;"></i>
                <h3 style="font-weight: 800; color: #64748b;">No Best Sellers Yet</h3>
                <p>Our most popular products will appear here soon!</p>
            </div>
        <?php endif; ?>
    </div>
</section>

<section class="makeover-section">
    <h2 class="makeover-title">Pawsome Makeovers</h2>
    <div class="swiper makeoverSlider">
        <div class="swiper-wrapper">
            <?php $m_query = mysqli_query($conn, "SELECT * FROM pawsome_makeovers WHERE status='active' ORDER BY id DESC");
            while($m = mysqli_fetch_assoc($m_query)): ?>
            <div class="swiper-slide">
                <div class="makeover-card">
                    <div class="m-img-box m-before">
                        <img src="uploads/makeovers/<?php echo $m['before_image']; ?>" alt="Before">
                        <div class="m-badge badge-blue">Before</div>
                    </div>
                    <div class="m-img-box m-after">
                        <img src="uploads/makeovers/<?php echo $m['after_image']; ?>" alt="After">
                        <div class="m-badge badge-green">After</div>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </div>
</section>


<section class="brand-section">
    <div class="brand-header-flex">
        <h2 style="font-size:3rem; font-weight:800; color:#0f1c3f; letter-spacing:-2px; margin:0;">Shop by Brands</h2>
        <div class="brand-filter-tabs">
            <button class="filter-brand-btn active" onclick="filterBrands('all', this)">All Brands</button>
            <button class="filter-brand-btn" onclick="filterBrands('Food', this)">Food</button>
            <button class="filter-brand-btn" onclick="filterBrands('Grooming', this)">Grooming</button>
            <button class="filter-brand-btn" onclick="filterBrands('Accessories', this)">Accessories</button>
            <button class="filter-brand-btn" onclick="filterBrands('Clothing', this)">Clothing</button>
            <button class="filter-brand-btn" onclick="filterBrands('Beds and Mats', this)">Beds & Mats</button>
            <button class="filter-brand-btn" onclick="filterBrands('Bowls', this)">Bowls</button>
            <button class="filter-brand-btn" onclick="filterBrands('Litter', this)">Litter</button>
        </div>
    </div>

    <div class="brand-grid-container" id="brandGrid">
        <?php
        $brands_query = mysqli_query($conn, "SELECT * FROM brands WHERE status='active' ORDER BY is_featured DESC, sort_order ASC");
        while($brand = mysqli_fetch_assoc($brands_query)): 
            $featured_class = ($brand['is_featured'] == 1) ? 'featured' : '';
            // Make sure category has a value, default to 'Accessories' if empty
            $brand_category = !empty($brand['category']) ? $brand['category'] : 'Accessories';
        ?>
            <a href="brand.php?name=<?php echo urlencode($brand['brand_name']); ?>" 
               class="brand-sovereign-card fade-in <?php echo $featured_class; ?>" 
               data-category="<?php echo htmlspecialchars($brand_category); ?>"
               title="Category: <?php echo htmlspecialchars($brand_category); ?>">
                
                <?php if($brand['is_featured'] == 1): ?>
                    <div class="brand-featured-badge" title="Featured Partner"><i class="fas fa-crown"></i></div>
                <?php endif; ?>

                <img src="uploads/brands/<?php echo $brand['brand_logo']; ?>" alt="<?php echo $brand['brand_name']; ?>">
                <span class="brand-name-tag"><?php echo $brand['brand_name']; ?></span>
                <small style="display:block; font-size:9px; color:#94a3b8; margin-top:5px;"><?php echo htmlspecialchars($brand_category); ?></small>
            </a>
        <?php endwhile; ?>
    </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script>
    var heroSwiper = new Swiper(".hero-swiper", { loop: true, effect: "fade", speed: 1000, autoplay: { delay: 5000 }, pagination: { el: ".swiper-pagination", clickable: true } });
    function initBreedSwiper(selector, n, p) { return new Swiper(selector, { slidesPerView: 2, spaceBetween: 30, navigation: { nextEl: n, prevEl: p }, breakpoints: { 640: { slidesPerView: 3 }, 1024: { slidesPerView: 5 }, 1400: { slidesPerView: 6 } } }); }
    initBreedSwiper(".breed-swiper-Dog", "#next-Dog", "#prev-Dog");
    initBreedSwiper(".breed-swiper-Cat", "#next-Cat", "#prev-Cat");
    initBreedSwiper(".breed-swiper-Bird", "#next-Bird", "#prev-Bird");
    function toggleBreedCat(c, b) { document.querySelectorAll('.breed-btn').forEach(btn => btn.classList.remove('active')); b.classList.add('active'); document.querySelectorAll('.breed-container').forEach(cont => cont.classList.remove('active')); document.getElementById('cat-' + c).classList.add('active'); }

    function fm_vault(name, text, time, rating) {
        let stars = '';
        for(let i=1; i<=5; i++) { stars += i <= rating ? '<i class="fas fa-star" style="color:#f43f5e; margin-right:6px;"></i>' : '<i class="far fa-star" style="color:#cbd5e1; margin-right:6px;"></i>'; }
        Swal.fire({
            title: `Thank You, ${name}!`,
            html: `<div style="text-align:left; padding:20px;">${stars}<p style="font-size:18px; line-height:2.2; font-style:italic; border-left:5px solid #518992; padding:25px; background:#f8fafc; border-radius:0 30px 30px 0;">"${text}"</p><div style="margin-top:20px; color:#94a3b8; font-size:12px; text-transform:uppercase; letter-spacing:1px;">Story Posted: ${time}</div></div>`,
            showConfirmButton: false, showCloseButton: true, width: '800px'
        });
    }

    new Swiper(".feedbackSlider", { slidesPerView: 1, spaceBetween: 30, loop: true, autoplay: { delay: 4500 }, pagination: { el: ".review-dot-pagination", clickable: true }, breakpoints: { 768: { slidesPerView: 2 }, 1024: { slidesPerView: 4 } } });
    
    new Swiper(".makeoverSlider", { slidesPerView: 2, spaceBetween: 10, loop: true, autoplay: { delay: 3000 }, breakpoints: { 768: { slidesPerView: 3, spaceBetween: 15 }, 1024: { slidesPerView: 4, spaceBetween: 20 }, 1400: { slidesPerView: 5, spaceBetween: 20 } } });

    // --- master BRAND FILTER ENGINE ---
    function filterBrands(cat, btn) {
        document.querySelectorAll('.filter-brand-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        const cards = document.querySelectorAll('.brand-sovereign-card');
        cards.forEach(card => {
            card.classList.remove('fade-in');
            if (cat === 'all' || card.getAttribute('data-category') === cat) {
                card.style.display = "flex";
                setTimeout(() => card.classList.add('fade-in'), 10);
            } else {
                card.style.display = "none";
            }
        });
    }

    // --- NEW ARRIVALS & BEST SELLERS FUNCTIONALITY ---
    function addToCartDirect(variantId) {
        const formData = new FormData();
        formData.append('action', 'add');
        formData.append('variant_id', variantId);
        
        fetch('cart.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.text())
        .then(data => {
            Swal.fire({
                icon: 'success',
                title: 'Added to Cart!',
                text: 'Product successfully added to your cart',
                showConfirmButton: false,
                timer: 1500
            });
            if(typeof updateHeaderCounts === 'function') {
                updateHeaderCounts();
            }
        })
        .catch(err => {
            console.error(err);
            Swal.fire({
                icon: 'error',
                title: 'Oops!',
                text: 'Failed to add item to cart'
            });
        });
    }

    function toggleWishlist(productId, btn) {
        <?php if(!isset($_SESSION['user_email'])): ?>
            Swal.fire({
                icon: 'warning',
                title: 'Login Required',
                text: 'Please login to add items to wishlist',
                showCancelButton: true,
                confirmButtonText: 'Login Now',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'login.php';
                }
            });
            return;
        <?php endif; ?>

        fetch('wishlist_action.php?product_id=' + productId)
        .then(response => response.json())
        .then(data => {
            const icon = btn.querySelector('i');
            if(data.status === 'added') {
                icon.classList.remove('far');
                icon.classList.add('fas');
                btn.classList.add('in-wishlist');
                Swal.fire({
                    icon: 'success',
                    title: 'Added to Wishlist!',
                    showConfirmButton: false,
                    timer: 1200
                });
            } else if(data.status === 'removed') {
                icon.classList.remove('fas');
                icon.classList.add('far');
                btn.classList.remove('in-wishlist');
                Swal.fire({
                    icon: 'info',
                    title: 'Removed from Wishlist',
                    showConfirmButton: false,
                    timer: 1200
                });
            }
            if(typeof updateHeaderCounts === 'function') {
                updateHeaderCounts();
            }
        })
        .catch(err => console.error(err));
    }

    function openQuickView(productId) {
        fetch('get_product_details.php?id=' + productId)
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                showQuickViewModal(data.product);
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Unable to load product details'
                });
            }
        })
        .catch(err => {
            console.error(err);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Failed to fetch product details'
            });
        });
    }

    function showQuickViewModal(product) {
        const modal = document.createElement('div');
        modal.className = 'quick-modal active';
        modal.innerHTML = `
            <div class="modal-content-quick">
                <button class="modal-close-btn" onclick="this.parentElement.parentElement.remove()">
                    <i class="fas fa-times"></i>
                </button>
                <div class="modal-grid">
                    <div class="modal-img-section">
                        <img src="uploads/products/${product.base_image}" alt="${product.name}">
                    </div>
                    <div class="modal-info-section">
                        <div class="modal-brand">${product.brand_name}</div>
                        <h2>${product.name}</h2>
                        <div class="diet-mark ${product.veg_type === 'Veg' ? 'veg-mark' : 'nonveg-mark'}" style="position:relative; display:inline-flex; margin-bottom:15px;"></div>
                        <div class="price-row">
                            <span class="price-main">₹${parseFloat(product.price).toLocaleString()}</span>
                            ${product.mrp > product.price ? `
                                <span class="price-old">₹${parseFloat(product.mrp).toLocaleString()}</span>
                                <span class="discount-tag">${Math.round(((product.mrp - product.price) / product.mrp) * 100)}% OFF</span>
                            ` : ''}
                        </div>
                        <p class="modal-desc">${product.description || 'Premium quality product for your beloved pet.'}</p>
                        ${product.variants && product.variants.length > 1 ? `
                            <div class="variant-selector">
                                <label class="variant-label">Select Size/Weight:</label>
                                <div class="variant-options">
                                    ${product.variants.map((v, idx) => `
                                        <button class="variant-option ${idx === 0 ? 'selected' : ''}" 
                                                onclick="selectVariant(this, ${v.id}, ${v.price})"
                                                data-variant-id="${v.id}">
                                            ${v.weight_size}
                                        </button>
                                    `).join('')}
                                </div>
                            </div>
                        ` : ''}
                        <div class="btn-actions" style="margin-top: 30px;">
                            ${product.stock_qty > 0 ? `
                                <button class="btn-add-cart" style="flex:1;" onclick="addToCartDirect(${product.variant_id}); this.parentElement.parentElement.parentElement.parentElement.remove();">
                                    <i class="fas fa-shopping-bag"></i> Add to Cart
                                </button>
                            ` : `
                                <button class="btn-add-cart" disabled style="flex:1; opacity:0.5; cursor:not-allowed;">
                                    <i class="fas fa-ban"></i> Out of Stock
                                </button>
                            `}
                        </div>
                    </div>
                </div>
            </div>
        `;
        document.body.appendChild(modal);
        modal.addEventListener('click', (e) => {
            if(e.target === modal) {
                modal.remove();
            }
        });
    }

    function selectVariant(btn, variantId, price) {
        document.querySelectorAll('.variant-option').forEach(opt => opt.classList.remove('selected'));
        btn.classList.add('selected');
        document.querySelector('.modal-info-section .price-main').textContent = '₹' + price.toLocaleString();
        const addBtn = document.querySelector('.modal-info-section .btn-add-cart');
        addBtn.setAttribute('onclick', `addToCartDirect(${variantId}); this.parentElement.parentElement.parentElement.parentElement.remove();`);
    }
</script>

<?php include "includes/footer.php"; ?>