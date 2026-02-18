<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start(); 
}
include "db.php"; 

$cart_count = (isset($_SESSION['cart']) && is_array($_SESSION['cart']) && !empty($_SESSION['cart'])) ? count($_SESSION['cart']) : 0;
$wishlist_count = 0;
if (isset($_SESSION['email'])) {
    $u_email = $_SESSION['email'];
    $count_res = mysqli_query($conn, "SELECT COUNT(*) AS total FROM wishlist WHERE user_email = '$u_email'");
    $count_data = mysqli_fetch_assoc($count_res);
    $wishlist_count = $count_data['total'];
}
?>
<html lang="en">
<head>
    <title>FURRYMART.com</title>
    <link rel="icon" type="image/png" href="uploads/logo.jpg">
  

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* ===== PRELOADER STYLES ===== */
        #furrymart-preloader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #ffffff 0%, #f0f9ff 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 99999;
            transition: opacity 0.5s ease, visibility 0.5s ease;
        }
        
        #furrymart-preloader.fade-out {
            opacity: 0;
            visibility: hidden;
        }
        
        .preloader-content {
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 20px;
        }
        
        .preloader-animation {
            width: 200px;
            height: 200px;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 40px rgba(81, 137, 146, 0.2);
        }
        
        .preloader-animation video {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .preloader-text {
            font-size: 24px;
            font-weight: 800;
            color: #518992;
            letter-spacing: 2px;
            animation: pulse 1.5s ease-in-out infinite;
        }
        
        @keyframes pulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.7; transform: scale(1.05); }
        }
        
        /* Hide body content until preloader is done */
        body.loading {
            overflow: hidden;
        }
        
        :root { --primary: #518992ff; --secondary: #e6b034c9; --text: #333; --border: #eee; --navy: #0f172a; --accent-red: #e11d48; }
        body { margin: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f9f9f9; color: var(--text); }
        header { background: #fff; border-bottom: 1px solid #eee; position: sticky; top: 0; z-index: 1000; }
        
        .top-bar { background: var(--primary); color: white; padding: 10px 5%; display: flex; justify-content: space-between; font-size: 14px; }
        .nav-container { display: flex; align-items: center; justify-content: space-between; padding: 15px 5%; gap: 20px; }
        
        .logo { font-size: 28px; font-weight: bold; color: var(--primary); text-decoration: none; display: flex; align-items: center; min-width: 150px; }
        .logo i { margin-right: 8px; }

        .search-box { flex-grow: 1; display: flex; max-width: 600px; position: relative; }
        .search-box input { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px 0 0 5px; outline: none; }
        .search-box button { background: var(--primary); color: white; border: none; padding: 0 25px; border-radius: 0 5px 5px 0; cursor: pointer; transition: 0.3s; }
        
        /* Search Suggestions Dropdown */
        .search-suggestions {
            position: absolute;
            top: 100%;
            left: 0;
            right: 50px;
            background: white;
            border: 1px solid #ddd;
            border-top: none;
            border-radius: 0 0 10px 10px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
            max-height: 450px;
            overflow-y: auto;
            z-index: 1001;
            display: none;
        }
        .search-suggestions.active { display: block; }
        
        .suggestion-item {
            display: flex;
            align-items: center;
            padding: 12px 15px;
            cursor: pointer;
            transition: 0.2s;
            border-bottom: 1px solid #f5f5f5;
            text-decoration: none;
            color: #333;
        }
        .suggestion-item:hover { background: #f8fafc; }
        .suggestion-item:last-child { border-bottom: none; }
        
        .suggestion-img {
            width: 50px;
            height: 50px;
            object-fit: contain;
            border-radius: 8px;
            margin-right: 15px;
            background: #f9f9f9;
            padding: 5px;
        }
        
        .suggestion-content { flex: 1; }
        .suggestion-name {
            font-weight: 600;
            font-size: 14px;
            color: #1e293b;
            margin-bottom: 3px;
            display: -webkit-box;
            -webkit-line-clamp: 1;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .suggestion-brand {
            font-size: 11px;
            color: var(--primary);
            text-transform: uppercase;
            font-weight: 700;
            margin-bottom: 2px;
        }
        .suggestion-price {
            font-weight: 800;
            color: #0f172a;
            font-size: 15px;
        }
        
        .suggestion-type-badge {
            padding: 4px 10px;
            border-radius: 15px;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            margin-left: 10px;
        }
        .badge-product { background: #e0f2fe; color: #0369a1; }
        .badge-pharmacy { background: #dcfce7; color: #15803d; }
        
        .no-suggestions {
            padding: 20px;
            text-align: center;
            color: #94a3b8;
            font-size: 14px;
        }
        
        .search-loading {
            padding: 20px;
            text-align: center;
            color: var(--primary);
            font-size: 14px;
        }
        
        .user-nav { display: flex; align-items: center; gap: 20px; }
        .user-nav a { text-decoration: none; color: var(--text); font-size: 14px; display: flex; align-items: center; position: relative; padding: 5px; transition: 0.3s; }
        .user-welcome { color: var(--primary); font-weight: bold; font-size: 15px; }
        .logout-icon { color: #ff4757 !important; margin-left: 10px; font-size: 18px; }
        
        .icon-wrapper { position: relative; }
        .badge { position: absolute; top: -10px; right: -10px; background: #ff4757; color: white; font-size: 10px; font-weight: bold; height: 18px; width: 18px; border-radius: 50%; display: flex; align-items: center; justify-content: center; border: 2px solid white; }

        .main-menu { background: white; padding: 0 5%; border-top: 1px solid #eee; display: flex; box-shadow: 0 2px 5px rgba(0,0,0,0.05); position: relative; }
        .menu-item-wrapper { position: static; }
        .menu-link { display: block; padding: 15px 18px; text-decoration: none; color: #555; font-weight: bold; font-size: 14px; text-transform: uppercase; transition: 0.3s; }
        .menu-link:hover { color: var(--primary); }

        /* --- VETCARE & GROOMING SPECIAL STYLING --- */
        .new-module-link { font-weight: 700 !important; display: flex !important; align-items: center; }
        .new-module-link .red-v { color: var(--accent-red); } 
        .new-module-link .blue-text { color: var(--navy); } 
        .new-tag { font-size: 9px; font-weight: 800; color: var(--primary); margin-left: 4px; vertical-align: super; }

        /* --- QUICK LINK COLORFUL CIRCULAR SLIDER --- */
        .quick-link-slider {
            background: #fff;
            padding: 25px 5%;
            display: flex;
            justify-content: center;
            gap: 45px;
            border-bottom: 1px solid #eee;
            overflow-x: auto;
            scrollbar-width: none; /* Hide scrollbar for clean look */
        }
        .quick-link-slider::-webkit-scrollbar { display: none; }
        
        .ql-item { text-decoration: none; text-align: center; min-width: 90px; transition: 0.3s; }
        .ql-item:hover { transform: translateY(-8px); }
        .ql-circle {
            width: 75px; height: 75px;
            border-radius: 50%;
            background: #fff;
            border: 1px solid #eee;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 12px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.06);
            transition: 0.3s;
        }
        .ql-item:hover .ql-circle { border-color: var(--primary); box-shadow: 0 12px 25px rgba(81, 137, 146, 0.15); }
        .ql-circle img { width: 50px; height: 50px; object-fit: contain; }
        .ql-item span { display: block; font-size: 14px; font-weight: 700; color: #334155; }

        /* MEGA DROPDOWN CONTAINER - COMPACT & PROFESSIONAL */
        .mega-dropdown {
            position: absolute;
            top: 100%; left: 0; width: 100%;
            background: linear-gradient(to bottom, #ffffff, #fafbfc);
            border-top: 2px solid var(--primary);
            box-shadow: 0 8px 20px rgba(0,0,0,0.08);
            display: none;
            padding: 25px 5%;
            box-sizing: border-box;
            z-index: 999;
            animation: dropFade 0.25s ease;
        }
        @keyframes dropFade {
            from { opacity: 0; transform: translateY(-8px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .menu-item-wrapper:hover .mega-dropdown { display: block; }

        /* CATEGORY GRID - COMPACT */
        .category-grid { 
            display: grid; 
            grid-template-columns: repeat(5, 1fr); 
            gap: 15px;
        }
        .category-item { 
            text-align: center; 
            text-decoration: none; 
            color: #1e293b;
            transition: all 0.25s ease;
            padding: 18px 12px;
            border-radius: 12px;
            background: #fff;
            border: 1px solid #e5e7eb;
            box-shadow: 0 2px 6px rgba(0,0,0,0.04);
        }
        .category-item:hover { 
            background: #f0f7f8;
            color: var(--primary);
            transform: translateY(-4px);
            box-shadow: 0 6px 15px rgba(81, 137, 146, 0.12);
            border-color: var(--primary);
        }
        .category-item i { 
            font-size: 32px; 
            display: block; 
            margin-bottom: 8px; 
            color: var(--primary);
            transition: all 0.25s ease;
        }
        .category-item:hover i {
            transform: scale(1.1);
            color: var(--secondary);
        }
        .category-item span { 
            font-size: 13px; 
            font-weight: 700; 
            text-transform: uppercase;
            color: #1e293b;
            letter-spacing: 0.3px;
        }

        /* BRAND GRID - COMPACT */
        .brand-grid { 
            display: grid; 
            grid-template-columns: repeat(6, 1fr); 
            gap: 16px;
        }
        .brand-item { 
            text-decoration: none; 
            text-align: center; 
            transition: all 0.25s ease;
            padding: 15px 12px;
            border-radius: 10px;
            background: #fff;
            border: 1px solid #e5e7eb;
            box-shadow: 0 2px 6px rgba(0,0,0,0.04);
        }
        .brand-item:hover { 
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(81, 137, 146, 0.12);
            border-color: var(--primary);
        }
        .brand-img-wrapper {
            width: 90%;
            height: 70px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 8px;
            background: #fafafa;
            border-radius: 8px;
            padding: 8px;
        }
        .brand-item img { 
            max-width: 100%; 
            max-height: 75px;
            object-fit: contain;
            filter: grayscale(30%);
            transition: all 0.25s ease;
        }
        .brand-item:hover img { 
            filter: grayscale(0%);
            transform: scale(1.08);
        }
        .brand-item p {
            margin: 0;
            font-size: 12px;
            font-weight: 700;
            color: #1e293b;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        .brand-item:hover p {
            color: var(--primary);
        }

        /* LIFESTAGE GRID - COMPACT */
        .lifestage-grid { 
            display: grid; 
            grid-template-columns: repeat(2, 1fr); 
            gap: 35px;
        }
        .ls-column h4 { 
            border-bottom: 2px solid var(--primary);
            padding-bottom: 8px;
            margin-top: 0;
            margin-bottom: 15px;
            color: var(--primary);
            font-size: 17px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .ls-link { 
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 15px;
            text-decoration: none;
            color: #334155;
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.25s ease;
            background: #fff;
            border: 1px solid #e5e7eb;
            margin-bottom: 8px;
        }
        .ls-link i {
            font-size: 22px;
            color: var(--primary);
            transition: all 0.25s ease;
        }
        .ls-link:hover { 
            background: #f0f7f8;
            color: var(--primary);
            transform: translateX(6px);
            border-color: var(--primary);
        }
        .ls-link:hover i {
            transform: scale(1.1);
            color: var(--secondary);
        }
        .badge { 
            position: absolute; top: -10px; right: -10px; 
            background: #ff4757; color: white; font-size: 10px; 
            font-weight: bold; height: 18px; width: 18px; 
            border-radius: 50%; display: flex; align-items: center; 
            justify-content: center; border: 2px solid white; 
            transition: transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }
        
        /* Stock Notification Popup */
        .stock-notification {
            position: fixed; top: 100px; right: 30px;
            background: linear-gradient(135deg, #22c55e, #16a34a);
            color: white; padding: 20px; border-radius: 20px;
            box-shadow: 0 15px 40px rgba(34, 197, 94, 0.4);
            z-index: 99999; max-width: 400px;
            border: 2px solid rgba(255,255,255,0.3);
        }
        .stock-notif-content { display: flex; align-items: center; gap: 15px; }
        .stock-notif-icon {
            font-size: 32px; background: rgba(255,255,255,0.2);
            width: 60px; height: 60px; display: flex;
            align-items: center; justify-content: center; border-radius: 15px;
        }
        .stock-notif-text { flex: 1; }
        .stock-notif-text strong { font-size: 16px; display: block; margin-bottom: 5px; }
        .stock-notif-text p { margin: 0; font-size: 13px; opacity: 0.95; }
        .stock-notif-btn {
            background: white; color: #16a34a; border: none;
            padding: 10px 20px; border-radius: 10px; font-weight: 800;
            cursor: pointer; font-size: 12px; transition: 0.3s;
        }
        .stock-notif-btn:hover { transform: scale(1.05); box-shadow: 0 5px 15px rgba(0,0,0,0.2); }

        .badge.bump { transform: scale(1.4); }
        
        /* Sparkle Animation for NEW/HOT badges */
        @keyframes sparkle {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.8; transform: scale(1.05); }
        }
        
        /* Top Bar Slogan Fade Animation */
        @keyframes fadeSlogan {
            0%, 100% { opacity: 1; transform: translateY(0); }
            50% { opacity: 0; transform: translateY(-5px); }
        }
        
        #rotating-slogan { transition: opacity 0.5s ease-in-out, transform 0.5s ease-in-out; }
        
        @media (max-width: 768px) { .main-menu { overflow-x: auto; white-space: nowrap; } .mega-dropdown { display: none !important; } .quick-link-slider { justify-content: flex-start; } }
    </style>
</head>
<body class="loading">
<!-- ===== FURRYMART PRELOADER ===== -->
<div id="furrymart-preloader">
    <div class="preloader-content">
        <div class="preloader-animation">
            <video autoplay loop muted playsinline>
                <source src="uploads/Animation/preloader.webm" type="video/webm">
            </video>
        </div>
        <div class="preloader-text">FURRYMART</div>
    </div>
</div>
<!-- ===== END PRELOADER ===== -->

<header>
    <div class="top-bar">
        <span id="rotating-slogan"><i class="fas fa-bullhorn"></i> Welcome to FurryMart - Your Pet's Paradise!</span>
        <span><i class="fas fa-phone"></i> +91 99257 08543</span>
    </div>
    <div class="nav-container">
        <a href="index.php" class="logo"><i class="fas fa-paw"></i> FurryMart</a>
        
        <form class="search-box" action="search.php" method="GET" id="searchForm">
            <input type="text" name="query" id="searchInput" placeholder="Search for food, toys, or accessories..." autocomplete="off">
            <div class="search-suggestions" id="searchSuggestions"></div>
            <button type="submit"><i class="fas fa-search"></i> Search</button>
        </form>

        <div class="user-nav">
            <?php if(isset($_SESSION['email'])): ?>
                <div style="display: flex; align-items: center;">
                    <a href="profile.php" class="user-welcome"><i class="fas fa-user-circle"></i> <span style="margin-left:5px;">Hieee!! <?php echo $_SESSION['first_name']; ?></span></a>
                    <a href="logout.php" title="Logout" class="logout-icon"><i class="fas fa-sign-out-alt"></i></a>
                </div>
            <?php else: ?>
                <a href="login.php"><i class="fas fa-user-lock"></i> <span style="margin-left:5px;">Login</span></a>
                <a href="signup.php"><i class="fas fa-user-plus"></i> <span style="margin-left:5px;">Register</span></a>
            <?php endif; ?>
            <!--<a href="wishlist.php" title="Wishlist"><div class="icon-wrapper"><i class="far fa-heart"></i><?php if($wishlist_count > 0): ?><span class="badge"><?php echo (int)$wishlist_count; ?></span><?php endif; ?></div><span>Wishlist</span></a>
           
                <a href="cart.php" title="View Cart"><div class="icon-wrapper"><i class="fas fa-shopping-basket"></i><?php if($cart_count > 0): ?><span class="badge"><?php echo (int)$cart_count; ?></span><?php endif; ?></div><span>Cart</span></a>-->
        <a href="<?php echo isset($_SESSION['email']) ? 'wishlist.php' : 'login.php'; ?>" title="Wishlist">
    <div class="icon-wrapper">
        <i class="far fa-heart"></i>
        <span class="badge" id="wishlist-badge" style="<?php echo ($wishlist_count > 0) ? '' : 'display:none;'; ?>">
            <?php echo (int)$wishlist_count; ?>
        </span>
    </div>
    <span>Wishlist</span>
</a>

        <a href="cart.php" title="View Cart">
            <div class="icon-wrapper">
                <i class="fas fa-shopping-basket"></i>
                <span class="badge" id="cart-badge" style="<?php echo ($cart_count > 0) ? '' : 'display:none;'; ?>">
                    <?php echo (int)$cart_count; ?>
                </span>
            </div>
            <span>Cart</span>
        </a>

        <a href="pet_feelings.php" title="Pet Feelings" style="position: relative;">
            <div class="icon-wrapper">
                <i class="fas fa-film" style="font-size: 20px;"></i>
            </div>
            <span>Reels</span>
        </a>
        
                </div>
    </div>
    
    <div class="main-menu">
        <div class="menu-item-wrapper"><a href="index.php" class="menu-link">Home</a></div>
        
        <div class="menu-item-wrapper">
            <a href="#" class="menu-link">Brands</a>
            <div class="mega-dropdown">
                <div class="brand-grid">
                    <a href="royal_canin.php" class="brand-item">
                        <div class="brand-img-wrapper">
                            <img src="uploads/brands/royalcanin.png" alt="Royal Canin">
                        </div>
                        <p>Royal Canin</p>
                    </a>
                    <a href="whiskas.php" class="brand-item">
                        <div class="brand-img-wrapper">
                            <img src="uploads/brands/whiskas.jfif" alt="Whiskas">
                        </div>
                        <p>Whiskas</p>
                    </a>
                    <a href="pedigree.php" class="brand-item">
                        <div class="brand-img-wrapper">
                            <img src="uploads/brands/1767239091_Pedigree.jpg" alt="Pedigree">
                        </div>
                        <p>Pedigree</p>
                    </a>
                    <a href="sheba.php" class="brand-item">
                        <div class="brand-img-wrapper">
                            <img src="uploads/brands/sheba.jfif" alt="Sheba">
                        </div>
                        <p>Sheba</p>
                    </a>
                    <a href="fresh_for_paws.php" class="brand-item">
                        <div class="brand-img-wrapper">
                            <img src="uploads/brands/ffp.jfif" alt="Fresh For Paws">
                        </div>
                        <p>Fresh For Paws</p>
                    </a>
                    <a href="orijen.php" class="brand-item">
                        <div class="brand-img-wrapper">
                            <img src="uploads/brands/orijen.png" alt="Orijen">
                        </div>
                        <p>Orijen</p>
                    </a>
                </div>
            </div>
        </div>

        <div class="menu-item-wrapper">
            <a href="Vetcare.php" class="menu-link new-module-link">
                <span class="red-v">V</span><span class="blue-text">etcare</span> 
                <span class="new-tag">New</span>
            </a>
        </div>
        <div class="menu-item-wrapper">
            <a href="pharmacy.php" class="menu-link new-module-link">
                <span class="red-v">P</span><span class="blue-text">harmacy</span> 
                <span class="new-tag">New</span>
            </a>
        </div>

        <div class="menu-item-wrapper">
            <a href="grooming.php" class="menu-link new-module-link">
                <span class="red-v">G</span><span class="blue-text">rooming</span> 
                <span class="new-tag">New</span>
            </a>
        </div>

        <div class="menu-item-wrapper">
            <a href="birthday.php" class="menu-link new-module-link">
                <span class="red-v" style="color: #ec4899;">B</span><span class="blue-text" style="background: linear-gradient(135deg, #ec4899, #8b5cf6); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">irthday</span>
                <span class="new-tag" style="background: linear-gradient(135deg, #ec4899 0%, #8b5cf6 100%); padding: 2px 6px; border-radius: 8px; color: white; animation: sparkle 2s infinite;">HOT</span>
            </a>
        </div>
        
       <div class="menu-item-wrapper">
            <a href="#" class="menu-link">Lifestage</a>
            <div class="mega-dropdown">
                <div class="lifestage-grid">
                    <div class="ls-column">
                        <h4>Shop for Dogs</h4>
                        <a href="dog_lifestage.php?stage=puppy" class="ls-link"><i class="fas fa-paw"></i> Puppy (0-1 Year)</a>
                        <a href="dog_lifestage.php?stage=adult" class="ls-link"><i class="fas fa-dog"></i> Adult Dog (1-7 Years)</a>
                        <a href="dog_lifestage.php?stage=senior" class="ls-link"><i class="fas fa-bone"></i> Senior Dog (7+ Years)</a>
                    </div>
                    <div class="ls-column">
                        <h4>Shop for Cats</h4>
                        <a href="cat_lifestage.php?stage=kitten" class="ls-link"><i class="fas fa-cat"></i> Kitten (0-1 Year)</a>
                        <a href="cat_lifestage.php?stage=adult" class="ls-link"><i class="fas fa-fish"></i> Adult Cat (1-7 Years)</a>
                        <a href="cat_lifestage.php?stage=senior" class="ls-link"><i class="fas fa-moon"></i> Senior Cat (7+ Years)</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="menu-item-wrapper">
            <a href="#" class="menu-link">Dogs</a>
            <div class="mega-dropdown">
                <div class="category-grid">
                    <a href="dog_food.php" class="category-item"><i class="fas fa-utensils"></i><span>Food</span></a>
                    <a href="dog_Toys.php" class="category-item"><i class="fas fa-baseball-ball"></i><span>Toys</span></a>
                    <a href="dog_Grooming.php" class="category-item"><i class="fas fa-bath"></i><span>Grooming</span></a>
                    <a href="dog_Bowles&Feeders.php" class="category-item"><i class="bi bi-cup-hot-fill"></i><span>Bowls</span></a>
                    <a href="dog_beds&Mats.php" class="category-item"><i class="fas fa-bed"></i><span>Beds & Mats</span></a>
                    <a href="dog_clothing.php" class="category-item"><i class="fas fa-tshirt"></i><span>Clothing</span></a>
                    <a href="dog_Accessories.php" class="category-item"><i class="fas fa-hat-wizard"></i><span>Accessories</span></a>
                </div>
            </div>
        </div>

        <div class="menu-item-wrapper">
            <a href="#" class="menu-link">Cats</a>
            <div class="mega-dropdown">
                <div class="category-grid">
                    <a href="cat_food.php" class="category-item"><i class="fas fa-fish"></i><span>Food</span></a>
                     <a href="cat_clothing.php" class="category-item"><i class="fas fa-tshirt"></i><span>Clothing</span></a>
                    <a href="cat_Toys.php" class="category-item"><i class="fas fa-mouse"></i><span>Toys</span></a>
                    <a href="cat_litter.php" class="category-item"><i class="fas fa-box-open"></i><span>Litter</span></a>
                    <a href="cat_Grooming.php" class="category-item"><i class="fas fa-cut"></i><span>Grooming</span></a>
                </div>
            </div>
        </div>

        <div class="menu-item-wrapper">
            <a href="#" class="menu-link new-module-link">
                <span class="red-v">B</span><span class="blue-text">irds</span> 
                <span class="new-tag" style="background: linear-gradient(135deg, #ff6b35 0%, #f7931e 100%); padding: 2px 6px; border-radius: 8px; color: white;">NEW</span>
            </a>
            <div class="mega-dropdown">
                <div class="category-grid">
                    <a href="bird.php" class="category-item"><i class="fas fa-seedling"></i><span>Food</span></a>
                    <a href="bird.php" class="category-item"><i class="fas fa-border-all"></i><span>Cages</span></a>
                    <a href="bird.php" class="category-item"><i class="fas fa-wind"></i><span>Toys</span></a>
                    <a href="bird.php" class="category-item"><i class="fas fa-medkit"></i><span>Health</span></a>
                    <a href="bird.php" class="category-item"><i class="fas fa-home"></i><span>Nesting</span></a>
                   
                </div>
            </div>
        </div>
        <div class="menu-item-wrapper"><a href="blog.php" class="menu-link">Blog</a></div>
    </div>
</header>

<script>
/**
 * Call this function inside your toggleWishlist success logic
 */
function updateHeaderCounts() {
    fetch('get_counts.php')
    .then(response => response.json())
    .then(data => {
        const wishBadge = document.getElementById('wishlist-badge');
        
        // Update Wishlist Badge
        if (data.wishlist > 0) {
            wishBadge.innerText = data.wishlist;
            wishBadge.style.display = 'flex';
            // Visual "Bump" effect
            wishBadge.classList.add('bump');
            setTimeout(() => wishBadge.classList.remove('bump'), 300);
        } else {
            wishBadge.style.display = 'none';
        }
    });
}

/**
 * AJAX Search with Threshold 2 & Auto-suggestions
 */
(function() {
    const searchInput = document.getElementById('searchInput');
    const suggestionsBox = document.getElementById('searchSuggestions');
    let searchTimeout = null;
    
    if (!searchInput || !suggestionsBox) return;
    
    // Listen to input changes
    searchInput.addEventListener('input', function() {
        const query = this.value.trim();
        
        // Clear previous timeout
        clearTimeout(searchTimeout);
        
        // Hide suggestions if less than threshold
        if (query.length < 2) {
            suggestionsBox.classList.remove('active');
            suggestionsBox.innerHTML = '';
            return;
        }
        
        // Show loading state
        suggestionsBox.classList.add('active');
        suggestionsBox.innerHTML = '<div class="search-loading"><i class="fas fa-spinner fa-spin"></i> Searching...</div>';
        
        // Debounce: Wait 300ms after user stops typing
        searchTimeout = setTimeout(() => {
            fetchSuggestions(query);
        }, 300);
    });
    
    // Fetch suggestions from server
    function fetchSuggestions(query) {
        fetch('search_suggestions.php?query=' + encodeURIComponent(query))
            .then(response => response.json())
            .then(data => {
                displaySuggestions(data);
            })
            .catch(error => {
                console.error('Search error:', error);
                suggestionsBox.innerHTML = '<div class="no-suggestions"><i class="fas fa-exclamation-triangle"></i> Error loading suggestions</div>';
            });
    }
    
    // Display suggestions in dropdown
    function displaySuggestions(results) {
        if (results.length === 0) {
            suggestionsBox.innerHTML = '<div class="no-suggestions"><i class="fas fa-search"></i> No products found</div>';
            return;
        }
        
        let html = '';
        results.forEach(item => {
            const badgeClass = item.type === 'pharmacy' ? 'badge-pharmacy' : 'badge-product';
            const badgeText = item.type === 'pharmacy' ? '<i class="fas fa-prescription-bottle"></i> Pharmacy' : 'Product';
            
            if (item.type === 'pharmacy') {
                html += `
                    <a href="${item.url}" class="suggestion-item">
                        <img src="${item.image}" class="suggestion-img" alt="${item.name}">
                        <div class="suggestion-content">
                            <div class="suggestion-brand">${item.brand}</div>
                            <div class="suggestion-name">${item.name}</div>
                            <div class="suggestion-price">₹${item.price}</div>
                        </div>
                        <span class="suggestion-type-badge ${badgeClass}">${badgeText}</span>
                    </a>
                `;
            } else {
                html += `
                    <div class="suggestion-item" onclick="redirectToSearch('${item.name.replace(/'/g, "\\'")}')">
                        <img src="${item.image}" class="suggestion-img" alt="${item.name}">
                        <div class="suggestion-content">
                            <div class="suggestion-brand">${item.brand}</div>
                            <div class="suggestion-name">${item.name}</div>
                            <div class="suggestion-price">₹${item.price}</div>
                        </div>
                        <span class="suggestion-type-badge ${badgeClass}">${badgeText}</span>
                    </div>
                `;
            }
        });
        
        suggestionsBox.innerHTML = html;
    }
    
    // Redirect to search page with selected product
    window.redirectToSearch = function(productName) {
        window.location.href = 'search.php?query=' + encodeURIComponent(productName);
    };
    
    // Close suggestions when clicking outside
    document.addEventListener('click', function(e) {
        if (!searchInput.contains(e.target) && !suggestionsBox.contains(e.target)) {
            suggestionsBox.classList.remove('active');
        }
    });
    
    // Reopen suggestions when clicking on input (if has value)
    searchInput.addEventListener('focus', function() {
        if (this.value.trim().length >= 2) {
            fetchSuggestions(this.value.trim());
        }
    });
    
    // Handle keyboard navigation (optional enhancement)
    searchInput.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            suggestionsBox.classList.remove('active');
        }
    });
})();

/**
 * ROTATING SLOGAN SYSTEM
 * Changes top bar slogan every 2 minutes with smooth fade transitions
 */
(function() {
    const slogans = [
        '<i class="fas fa-bullhorn"></i> Welcome to FurryMart - Your Pet\'s Paradise!',
        '<i class="fas fa-heart"></i> Where Every Pet Is Family - Shop with Love!',
        '<i class="fas fa-star"></i> Premium Quality Products for Your Furry Friends!',
        '<i class="fas fa-gift"></i> Exclusive Deals on Pet Care Essentials - Save Big!',
        '<i class="fas fa-shipping-fast"></i> Fast Delivery & Free Shipping on Orders Above ₹999!',
        '<i class="fas fa-paw"></i> Trusted by 10,000+ Happy Pet Parents Across India!',
        '<i class="fas fa-medal"></i> Award-Winning Pet Store - Quality Guaranteed!',
        '<i class="fas fa-stethoscope"></i> Vet-Approved Products for Your Pet\'s Health!',
        '<i class="fas fa-birthday-cake"></i> Book Your Pet\'s Birthday Party Today - Limited Slots!',
        '<i class="fas fa-phone-volume"></i> 24/7 Customer Support - We\'re Here to Help!'
    ];
    
    let currentIndex = 0;
    const sloganElement = document.getElementById('rotating-slogan');
    
    function updateSlogan() {
        if (!sloganElement) return;
        
        // Fade out effect
        sloganElement.style.opacity = '0';
        sloganElement.style.transform = 'translateY(-5px)';
        
        setTimeout(() => {
            currentIndex = (currentIndex + 1) % slogans.length;
            sloganElement.innerHTML = slogans[currentIndex];
            
            // Fade in effect
            sloganElement.style.opacity = '1';
            sloganElement.style.transform = 'translateY(0)';
        }, 500);
    }
    
    // Change slogan every 2 minutes (120000 milliseconds)
    setInterval(updateSlogan, 3000);
})();

/**
 * REAL-TIME STOCK NOTIFICATION SYSTEM
 * Checks every 30 seconds if wishlist items are back in stock
 */
<?php if(isset($_SESSION['email'])): ?>
(function() {
    function checkWishlistStock() {
        fetch('check_wishlist_stock.php')
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success' && data.restocked_items && data.restocked_items.length > 0) {
                data.restocked_items.forEach(item => {
                    showRestockNotification(item);
                });
            }
        })
        .catch(err => console.error('Stock check error:', err));
    }
    
    function showRestockNotification(product) {
        const notification = document.createElement('div');
        notification.className = 'stock-notification animate__animated animate__bounceInRight';
        notification.innerHTML = `
            <div class="stock-notif-content">
                <div class="stock-notif-icon">
                    <i class="fas fa-box-open"></i>
                </div>
                <div class="stock-notif-text">
                    <strong>Back in Stock!</strong>
                    <p>${product.name}</p>
                </div>
                <button onclick="window.location.href='wishlist.php'" class="stock-notif-btn">
                    View Wishlist
                </button>
            </div>
        `;
        
        document.body.appendChild(notification);
        
        // Auto remove after 10 seconds
        setTimeout(() => {
            notification.classList.remove('animate__bounceInRight');
            notification.classList.add('animate__fadeOutRight');
            setTimeout(() => notification.remove(), 1000);
        }, 10000);
    }
    
    // Check immediately on page load
    checkWishlistStock();
    
    // Then check every 30 seconds
    setInterval(checkWishlistStock, 30000);
})();
<?php endif; ?>
</script>

<!-- ===== PRELOADER SCRIPT ===== -->
<script>
// Hide preloader when page is fully loaded
window.addEventListener('load', function() {
    const preloader = document.getElementById('furrymart-preloader');
    const body = document.body;
    
    // Small delay for smooth transition
    setTimeout(function() {
        preloader.classList.add('fade-out');
        body.classList.remove('loading');
        
        // Remove preloader from DOM after animation
        setTimeout(function() {
            preloader.style.display = 'none';
        }, 500);
    }, 500); // Show preloader for at least 500ms
});

// Also hide preloader if it takes too long (fallback)
setTimeout(function() {
    const preloader = document.getElementById('furrymart-preloader');
    const body = document.body;
    if (preloader && !preloader.classList.contains('fade-out')) {
        preloader.classList.add('fade-out');
        body.classList.remove('loading');
        setTimeout(function() {
            preloader.style.display = 'none';
        }, 500);
    }
}, 5000); // Maximum 5 seconds
</script>
