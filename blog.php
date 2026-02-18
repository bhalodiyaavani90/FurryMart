<?php 
if (session_status() === PHP_SESSION_NONE) { session_start(); }
include "db.php"; 
include "includes/header.php"; 
?>

<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>

<style>
    :root {
        --primary: #518992;   /* Sovereign Teal */
        --navy: #0f1c3f;      /* Enterprise Navy */
        --bg-warm: #fdfaf5;   /* Brand Warmth */
        --white: #ffffff;
        --shadow-soft: 0 20px 50px rgba(15, 28, 63, 0.06);
    }

    body { font-family: 'Plus Jakarta Sans', sans-serif; background: var(--bg-warm); color: var(--navy); overflow-x: hidden; }
    body.modal-open { overflow: hidden; }

    /* --- master BREATHABLE HERO --- */
    .hero-outer-vault { padding: 30px 8%; background: var(--white); }
    .hero-inner-frame {
        background: linear-gradient(135deg, #cedde0 0%, #dae7e9 100%); 
        border-radius: 50px;
        padding: 60px 8%; /* Reduced from 100px for Medium Scale */
        display: flex; align-items: center; justify-content: space-between;
        position: relative; overflow: hidden;
        box-shadow: 0 15px 40px rgba(81, 137, 146, 0.1);
    }
    .hero-text h1 { 
        font-size: 4rem; /* Reduced from 6rem for Medium Scale */
        font-weight: 900; line-height: 1; letter-spacing: -3px; color: var(--navy);
    }
    .hero-dog-asset { 
        height: 280px; /* Medium Scale */
        filter: drop-shadow(0 20px 30px rgba(0,0,0,0.1));
        animation: floatEffect 5s infinite ease-in-out;
    }
    @keyframes floatEffect { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-15px); } }

    /* --- SINGLE LINE CONTROLS --- */
    .controls-vault { 
        padding: 50px 8% 30px; 
        display: flex; 
        justify-content: space-between; 
        align-items: center; 
        flex-wrap: wrap;
        gap: 20px; 
    }
    
    .filter-row {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
        align-items: center;
        flex: 1;
    }
    
    .filter-btn { 
        padding: 12px 28px; 
        border-radius: 100px; 
        background: var(--white); 
        border: 1.5px solid #eef2f6;
        font-weight: 700; 
        cursor: pointer; 
        transition: 0.4s cubic-bezier(0.4, 0, 0.2, 1); 
        color: #64748b; 
        font-size: 13px;
        position: relative;
        overflow: hidden;
    }
    
    .filter-btn::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
        transition: left 0.5s;
    }
    
    .filter-btn:hover::before {
        left: 100%;
    }
    
    .filter-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(81, 137, 146, 0.15);
        border-color: var(--primary);
    }
    
    .filter-btn.active { 
        background: var(--navy); 
        color: white; 
        border-color: var(--navy); 
        box-shadow: 0 10px 20px rgba(15, 28, 63, 0.2);
        transform: scale(1.05);
    }
    
    /* --- BEAUTIFUL SORT DROPDOWN --- */
    .sort-vault {
        display: flex;
        align-items: center;
        gap: 15px;
        background: var(--white);
        padding: 12px 20px;
        border-radius: 50px;
        border: 2px solid #eef2f6;
        box-shadow: 0 5px 20px rgba(0,0,0,0.05);
        transition: all 0.3s ease;
        position: relative;
    }
    
    .sort-vault:hover {
        border-color: var(--primary);
        box-shadow: 0 8px 25px rgba(81, 137, 146, 0.15);
        transform: translateY(-2px);
    }
    
    .sort-vault i {
        font-size: 18px;
        color: var(--primary);
        transition: transform 0.3s ease;
    }
    
    .sort-vault:hover i {
        transform: scale(1.1) rotate(5deg);
    }
    
    .sort-vault select {
        border: none;
        background: transparent;
        font-weight: 700;
        font-size: 14px;
        color: var(--navy);
        cursor: pointer;
        outline: none;
        padding: 5px 25px 5px 0;
        appearance: none;
        -webkit-appearance: none;
        -moz-appearance: none;
        font-family: 'Plus Jakarta Sans', sans-serif;
        min-width: 150px;
    }
    
    /* Custom dropdown arrow */
    .sort-vault::after {
        content: '';
        position: absolute;
        right: 20px;
        top: 50%;
        transform: translateY(-50%);
        width: 0;
        height: 0;
        border-left: 5px solid transparent;
        border-right: 5px solid transparent;
        border-top: 6px solid var(--navy);
        pointer-events: none;
        transition: transform 0.3s ease;
    }
    
    .sort-vault:hover::after {
        transform: translateY(-50%) rotate(180deg);
    }
    
    .sort-vault select option {
        padding: 12px 20px;
        font-weight: 600;
        background: var(--white);
        color: var(--navy);
    }
    
    .sort-vault select option:hover {
        background: var(--primary);
        color: white;
    }

    /* --- BLOG COUNT DISPLAY --- */
    .blog-count-display {
        padding: 20px 8%;
        text-align: center;
    }
    
    #blogCounter {
        display: inline-block;
        font-size: 15px;
        font-weight: 700;
        color: var(--navy);
        background: linear-gradient(135deg, #e0f2fe 0%, #dbeafe 100%);
        padding: 12px 35px;
        border-radius: 50px;
        box-shadow: 0 5px 20px rgba(81, 137, 146, 0.12);
        letter-spacing: 0.5px;
        animation: slideDown 0.5s ease;
    }
    
    #blogCounter .count-number {
        color: var(--primary);
        font-weight: 900;
        font-size: 18px;
    }
    
    @keyframes slideDown {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* --- master MEDIUM SYMMETRICAL GRID --- */
    .blog-grid { 
        display: grid; 
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); /* Breathable Grid */
        gap: 30px; padding: 0 8% 100px; 
    }
    .blog-card { 
        background: var(--white); border-radius: 40px; overflow: hidden; border: 1px solid #f1f5f9;
        transition: 0.6s cubic-bezier(0.165, 0.84, 0.44, 1); display: flex; flex-direction: column;
        height: 520px; /* Medium Height Symmetry */
        box-shadow: var(--shadow-soft);
    }
    .blog-card:hover { transform: translateY(-15px); border-color: var(--primary); box-shadow: 0 30px 60px rgba(15,28,63,0.08); }
    
    .card-img-vault { flex: 0 0 200px; width: 100%; overflow: hidden; } /* Controlled Image Size */
    .card-img-vault img { width: 100%; height: 100%; object-fit: cover; transition: 1.5s; }
    .blog-card:hover .card-img-vault img { transform: scale(1.1); }
    
    .card-content { padding: 30px; flex: 1; display: flex; flex-direction: column; }
    .card-meta { font-size: 10px; font-weight: 800; color: var(--primary); text-transform: uppercase; margin-bottom: 12px; letter-spacing: 1.2px; }
    .card-title { 
        height: 55px; font-size: 1.4rem; font-weight: 800; line-height: 1.2; 
        margin-bottom: 15px; color: var(--navy); overflow: hidden; 
    }
    .card-desc { 
        flex: 1; font-size: 14px; color: #64748b; line-height: 1.6; 
        margin-bottom: 25px; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical;
    }

    .read-story-btn { 
        background: var(--primary); color: white !important; padding: 14px 30px; border-radius: 12px; 
        font-weight: 800; border: none; cursor: pointer; transition: 0.4s; width: 100%; 
        text-transform: uppercase; font-size: 11px; letter-spacing: 1px;
    }

    /* --- master QUANTUM MODAL --- */
    .modal-overlay { display: none; position: fixed; inset: 0; background: rgba(15, 28, 63, 0.95); backdrop-filter: blur(10px); z-index: 10000; justify-content: center; align-items: center; padding: 20px; animation: fadeIn 0.3s ease; }
    .modal-container { background: #fff; width: 90%; max-width: 1200px; height: 85vh; border-radius: 30px; overflow: hidden; display: flex; flex-direction: row; box-shadow: 0 40px 100px rgba(0,0,0,0.5); position: relative; animation: modalSlideUp 0.4s cubic-bezier(0.4, 0, 0.2, 1); }
    
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    
    @keyframes modalSlideUp {
        from { transform: translateY(50px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }
    
    @keyframes imageZoom {
        from { transform: scale(0.95); opacity: 0; }
        to { transform: scale(1); opacity: 1; }
    }
    
    /* Left Image Section */
    .modal-image-section {
        flex: 0 0 45%;
        background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 30px;
        position: relative;
        border-right: 2px solid #e2e8f0;
    }
    
    .modal-image-wrapper {
        width: 100%;
        height: 100%;
        position: relative;
        overflow: hidden;
        border-radius: 20px;
        box-shadow: 0 20px 50px rgba(0,0,0,0.15);
    }
    
    .modal-image-wrapper img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        object-position: center;
        transition: transform 0.5s ease;
        animation: imageZoom 0.6s ease;
    }
    
    .modal-image-wrapper:hover img {
        transform: scale(1.05);
    }
    
    .spinner-container {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        text-align: center;
        z-index: 5;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 25px;
    }
    
    /* Premium Paw Print Loader with Glow */
    .paw-loader {
        position: relative;
        width: 120px;
        height: 120px;
        filter: drop-shadow(0 0 20px rgba(81, 137, 146, 0.3));
    }
    
    .paw-pad {
        position: absolute;
        background: linear-gradient(135deg, var(--primary) 0%, #2d5a5f 100%);
        border-radius: 50% 50% 50% 50% / 60% 60% 40% 40%;
        animation: pawPulse 1.6s cubic-bezier(0.4, 0, 0.2, 1) infinite;
        box-shadow: 0 0 15px rgba(81, 137, 146, 0.4);
    }
    
    .paw-pad.center {
        width: 45px;
        height: 50px;
        left: 50%;
        top: 50%;
        transform: translate(-50%, -50%);
        animation-delay: 0s;
    }
    
    .paw-pad.toe1 {
        width: 24px;
        height: 28px;
        left: 18px;
        top: 15px;
        animation-delay: 0.15s;
    }
    
    .paw-pad.toe2 {
        width: 24px;
        height: 28px;
        left: 48px;
        top: 8px;
        animation-delay: 0.3s;
    }
    
    .paw-pad.toe3 {
        width: 24px;
        height: 28px;
        right: 18px;
        top: 15px;
        animation-delay: 0.45s;
    }
    
    @keyframes pawPulse {
        0%, 100% {
            transform: scale(1) translateY(0);
            opacity: 1;
            filter: brightness(1);
        }
        25% {
            transform: scale(1.15) translateY(-5px);
            opacity: 0.9;
            filter: brightness(1.2);
        }
        50% {
            transform: scale(1.25) translateY(-8px);
            opacity: 0.8;
            filter: brightness(1.3);
        }
        75% {
            transform: scale(1.15) translateY(-5px);
            opacity: 0.9;
            filter: brightness(1.2);
        }
    }
    
    /* Rotating Rings */
    .loader-ring {
        position: absolute;
        border-radius: 50%;
        border: 3px solid transparent;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
    }
    
    .loader-ring.ring1 {
        width: 140px;
        height: 140px;
        border-top-color: var(--primary);
        border-right-color: var(--primary);
        animation: spin 2s linear infinite;
        opacity: 0.6;
    }
    
    .loader-ring.ring2 {
        width: 160px;
        height: 160px;
        border-bottom-color: var(--navy);
        border-left-color: var(--navy);
        animation: spin 2.5s linear infinite reverse;
        opacity: 0.4;
    }
    
    .loader-ring.ring3 {
        width: 180px;
        height: 180px;
        border-top-color: var(--primary);
        animation: spin 3s linear infinite;
        opacity: 0.2;
    }
    
    @keyframes spin {
        0% { transform: translate(-50%, -50%) rotate(0deg); }
        100% { transform: translate(-50%, -50%) rotate(360deg); }
    }
    
    /* Orbiting Particles */
    .loader-particle {
        position: absolute;
        width: 8px;
        height: 8px;
        background: var(--primary);
        border-radius: 50%;
        top: 50%;
        left: 50%;
        animation: orbit 2s linear infinite;
        box-shadow: 0 0 10px var(--primary);
    }
    
    .loader-particle:nth-child(1) {
        animation-delay: 0s;
    }
    
    .loader-particle:nth-child(2) {
        animation-delay: 0.5s;
    }
    
    .loader-particle:nth-child(3) {
        animation-delay: 1s;
    }
    
    .loader-particle:nth-child(4) {
        animation-delay: 1.5s;
    }
    
    @keyframes orbit {
        0% {
            transform: translate(-50%, -50%) rotate(0deg) translateX(90px) rotate(0deg);
            opacity: 1;
        }
        50% {
            opacity: 0.5;
            transform: translate(-50%, -50%) rotate(180deg) translateX(90px) rotate(-180deg);
        }
        100% {
            transform: translate(-50%, -50%) rotate(360deg) translateX(90px) rotate(-360deg);
            opacity: 1;
        }
    }
    
    /* Loading Text with Shimmer */
    .loading-text {
        font-size: 13px;
        font-weight: 900;
        color: var(--navy);
        letter-spacing: 3px;
        text-transform: uppercase;
        position: relative;
        background: linear-gradient(90deg, var(--navy) 0%, var(--primary) 50%, var(--navy) 100%);
        background-size: 200% auto;
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        animation: shimmer 2s linear infinite;
    }
    
    @keyframes shimmer {
        0% {
            background-position: 200% center;
        }
        100% {
            background-position: -200% center;
        }
    }
    
    /* Loading Dots */
    .loading-dots {
        display: inline-block;
        margin-left: 5px;
    }
    
    .loading-dots span {
        display: inline-block;
        animation: dotJump 1.2s infinite cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    .loading-dots span:nth-child(1) { animation-delay: 0s; }
    .loading-dots span:nth-child(2) { animation-delay: 0.15s; }
    .loading-dots span:nth-child(3) { animation-delay: 0.3s; }
    
    @keyframes dotJump {
        0%, 60%, 100% {
            transform: translateY(0) scale(1);
        }
        30% {
            transform: translateY(-12px) scale(1.2);
        }
    }
    
    /* Progress Bar */
    .loader-progress {
        width: 200px;
        height: 4px;
        background: rgba(81, 137, 146, 0.1);
        border-radius: 10px;
        overflow: hidden;
        position: relative;
    }
    
    .loader-progress-bar {
        height: 100%;
        background: linear-gradient(90deg, var(--primary), var(--navy), var(--primary));
        background-size: 200% 100%;
        border-radius: 10px;
        animation: progressMove 1.5s ease-in-out infinite;
        box-shadow: 0 0 10px var(--primary);
    }
    
    @keyframes progressMove {
        0% {
            width: 0%;
            background-position: 0% 0%;
        }
        50% {
            width: 100%;
            background-position: 100% 0%;
        }
        100% {
            width: 0%;
            background-position: 200% 0%;
            transform: translateX(200px);
        }
    }
    
    /* Right Content Section */
    .modal-content-scroll { 
        flex: 1;
        overflow-y: auto; 
        padding: 45px 50px; 
        background: white;
        animation: fadeIn 0.5s ease 0.2s backwards;
    }
    
    .modal-content-scroll::-webkit-scrollbar {
        width: 8px;
    }
    
    .modal-content-scroll::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 10px;
    }
    
    .modal-content-scroll::-webkit-scrollbar-thumb {
        background: var(--primary);
        border-radius: 10px;
    }
    
    .modal-content-scroll::-webkit-scrollbar-thumb:hover {
        background: var(--navy);
    }
    
    .modal-close { 
        position: absolute; 
        top: 20px; 
        right: 20px; 
        width: 50px; 
        height: 50px; 
        background: rgba(255, 255, 255, 0.95); 
        border: none; 
        border-radius: 50%; 
        font-size: 24px; 
        cursor: pointer; 
        z-index: 10001; 
        box-shadow: 0 10px 25px rgba(0,0,0,0.2); 
        transition: all 0.3s ease;
        color: var(--navy);
        font-weight: 300;
        backdrop-filter: blur(10px);
    }
    
    .modal-close:hover {
        transform: rotate(90deg) scale(1.1);
        background: var(--navy);
        color: white;
    }

    @media (max-width: 992px) {
        .hero-inner-frame { flex-direction: column; text-align: center; padding: 50px 5%; }
        .hero-text h1 { font-size: 3rem; }
        .hero-dog-asset { height: 200px; margin-top: 30px; }
        
        .controls-vault {
            padding: 40px 5% 25px;
            flex-direction: column;
            align-items: stretch;
        }
        
        .filter-row {
            justify-content: center;
            order: 1;
        }
        
        .sort-vault {
            order: 2;
            margin: 0 auto;
        }
        
        .blog-count-display {
            padding: 15px 5%;
        }
        
        .blog-grid {
            padding: 0 5% 80px;
        }
        
        .blog-card { height: auto; }
        .modal-container { flex-direction: column; height: 95vh; width: 95%; }
        .modal-image-section { flex: 0 0 300px; padding: 20px; border-right: none; border-bottom: 2px solid #e2e8f0; }
        .modal-content-scroll { padding: 30px 25px; }
    }
    
    @media (max-width: 576px) {
        .filter-btn {
            padding: 10px 20px;
            font-size: 12px;
        }
        
        #blogCounter {
            font-size: 13px;
            padding: 10px 25px;
        }
        
        #blogCounter .count-number {
            font-size: 16px;
        }
    }
</style>

<div class="hero-outer-vault">
    <section class="hero-inner-frame animate__animated animate__fadeIn">
        <div class="hero-text" data-aos="fade-right">
            <h1>Sit. Stay. Read.</h1>
        </div>
        <img src="uploads/bloghero.jpg" class="hero-dog-asset" data-aos="fade-left">
    </section>
</div>

<div class="controls-vault" data-aos="fade-up">
    <div class="filter-row">
        <button class="filter-btn active" data-filter="ALL" data-label="All Insights">All Insights</button>
        <button class="filter-btn" data-filter="TOPICAL" data-label="Topical">Topical</button>
        <button class="filter-btn" data-filter="VETCARE" data-label="VetCare">VetCare</button>
        <button class="filter-btn" data-filter="GROOMING" data-label="Grooming">Grooming</button>
        <button class="filter-btn" data-filter="PETCARE" data-label="PetCare">PetCare</button>
        <button class="filter-btn" data-filter="NUTRITION" data-label="Nutrition">Nutrition</button>
    </div>

    <div class="sort-vault">
        <i class="fas fa-sort-amount-down"></i>
        <select id="dateSorter">
            <option value="DESC">Latest First</option>
            <option value="ASC">Oldest First</option>
        </select>
    </div>
</div>

<div class="blog-count-display">
    <div id="blogCounter"></div>
</div>

<section class="blog-grid" id="blogContainer">
    <?php 
    // Optimized fetch logic with category normalization
    $res = mysqli_query($conn, "SELECT * FROM expert_tips ORDER BY publish_date DESC");
    while($row = mysqli_fetch_assoc($res)) {
        // Normalize category for better filtering
        $category = strtoupper($row['category']);
        $normalizedCategory = $category;
        
        // Map various category names to standard filters
        if (strpos($category, 'GROOM') !== false || strpos($category, 'HYGIENE') !== false) {
            $normalizedCategory = 'GROOMING';
        } elseif (strpos($category, 'PETCARE') !== false || strpos($category, 'CENTER') !== false) {
            $normalizedCategory = 'PETCARE';
        } elseif (strpos($category, 'FOOD') !== false || strpos($category, 'NUTRITION') !== false) {
            $normalizedCategory = 'NUTRITION';
        } elseif (strpos($category, 'VET') !== false) {
            $normalizedCategory = 'VETCARE';
        } elseif (strpos($category, 'TOPIC') !== false) {
            $normalizedCategory = 'TOPICAL';
        }
    ?>
    <div class="blog-card" 
         data-category="<?php echo $normalizedCategory; ?>"
         data-original-category="<?php echo htmlspecialchars($row['category']); ?>"
         data-timestamp="<?php echo strtotime($row['publish_date']); ?>"
         data-aos="fade-up">
        <div class="card-img-vault">
            <img src="<?php echo $row['thumbnail_image']; ?>" alt="FurryMart Intel">
        </div>
        <div class="card-content">
            <div class="card-meta"><?php echo date("M j, Y", strtotime($row['publish_date'])); ?> | <?php echo $row['category']; ?></div>
            <h3 class="card-title"><?php echo $row['title']; ?></h3>
            <p class="card-desc"><?php echo $row['short_description']; ?></p>
            <button class="read-story-btn js-open-modal" data-id="<?php echo $row['id']; ?>">EXPLORE INSIGHT</button>
        </div>
    </div>
    <?php } ?>
</section>

<div class="modal-overlay" id="tipModal">
    <div class="modal-container">
        <button class="modal-close js-close-modal">&times;</button>
        
        <!-- Left: Image Section -->
        <div class="modal-image-section">
            <div class="modal-image-wrapper">
                <div class="spinner-container" id="tipSpinner">
                    <!-- Rotating Rings -->
                    <div class="loader-ring ring1"></div>
                    <div class="loader-ring ring2"></div>
                    <div class="loader-ring ring3"></div>
                    
                    <!-- Orbiting Particles -->
                    <div class="loader-particle"></div>
                    <div class="loader-particle"></div>
                    <div class="loader-particle"></div>
                    <div class="loader-particle"></div>
                    
                    <!-- Paw Print -->
                    <div class="paw-loader">
                        <div class="paw-pad center"></div>
                        <div class="paw-pad toe1"></div>
                        <div class="paw-pad toe2"></div>
                        <div class="paw-pad toe3"></div>
                    </div>
                    
                    <!-- Loading Text -->
                    <p class="loading-text">
                        Loading<span class="loading-dots"><span>.</span><span>.</span><span>.</span></span>
                    </p>
                    
                    <!-- Progress Bar -->
                    <div class="loader-progress">
                        <div class="loader-progress-bar"></div>
                    </div>
                </div>
                <img src="" id="modalInsideImg" alt="Blog Image">
            </div>
        </div>
        
        <!-- Right: Content Section -->
        <div class="modal-content-scroll">
            <div style="color:var(--primary); font-weight:800; margin-bottom:15px; text-transform:uppercase; font-size:12px; letter-spacing:2px;">
                <span id="modalDate"></span> | <span id="modalCat"></span>
            </div>
            <h2 id="modalTitle" style="font-size:32px; font-weight:900; color:var(--navy); margin-bottom:25px; line-height:1.1;"></h2>
            <div class="modal-summary-box" id="modalSummary" style="background:#f0fdfa; padding:25px; border-left:6px solid var(--primary); border-radius:15px; font-size:16px; font-style:italic; margin-bottom:30px; color:#475569;"></div>
            <div id="modalContent" style="font-size:17px; line-height:1.9; color:#475569;"></div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    AOS.init({ duration: 800, once: true, offset: 100 });

    // Global variables
    let currentFilter = 'ALL';
    let currentSort = 'DESC';
    const $blogContainer = $('#blogContainer');
    const $blogCards = $('.blog-card');
    const totalBlogs = $blogCards.length;

    // Update blog counter display
    function updateBlogCounter() {
        const visibleCards = $blogCards.filter(':visible');
        const visibleCount = visibleCards.length;
        const filterLabel = $('.filter-btn.active').data('label');
        
        let counterHTML = '';
        if (currentFilter === 'ALL') {
            counterHTML = `Total <span class="count-number">${totalBlogs}</span> blogs`;
        } else {
            counterHTML = `<span class="count-number">${visibleCount}</span> ${filterLabel} blog${visibleCount !== 1 ? 's' : ''} from ${totalBlogs}`;
        }
        
        $('#blogCounter').html(counterHTML);
    }

    // Sort blogs by date
    function sortByDate() {
        currentSort = $('#dateSorter').val();
        const cards = $blogCards.get();
        
        cards.sort((a, b) => {
            const timeA = $(a).data('timestamp');
            const timeB = $(b).data('timestamp');
            return currentSort === 'DESC' ? timeB - timeA : timeA - timeB;
        });
        
        $blogContainer.fadeOut(200, function() {
            $.each(cards, (idx, card) => $blogContainer.append(card));
            $blogContainer.fadeIn(300);
        });
    }

    // Filter blogs by category
    function filterBlogs(category) {
        currentFilter = category;
        
        $blogCards.each(function() {
            const cardCategory = $(this).data('category');
            if (category === 'ALL' || cardCategory === category) {
                $(this).fadeIn(400).css('display', 'flex');
            } else {
                $(this).fadeOut(250);
            }
        });
        
        setTimeout(() => {
            updateBlogCounter();
            sortByDate();
            AOS.refresh();
        }, 350);
    }

    $(document).ready(function() {
        // Initialize blog counter
        updateBlogCounter();
        
        // Filter button click handler
        $('.filter-btn').on('click', function() {
            const $btn = $(this);
            const filter = $btn.data('filter');
            
            if ($btn.hasClass('active')) return;
            
            $('.filter-btn').removeClass('active');
            $btn.addClass('active');
            
            filterBlogs(filter);
        });
        
        // Sort dropdown change handler
        $('#dateSorter').on('change', function() {
            sortByDate();
        });
        
        // Modal open handler
        $(document).on('click', '.js-open-modal', function() {
            var tid = $(this).data('id');
            $('#tipModal').fadeIn(300).css('display', 'flex');
            
            // Show spinner and hide image initially
            $('#modalInsideImg').hide();
            $('#tipSpinner').show();
            $('body').addClass('modal-open');
            
            $.ajax({
                url: 'get_tip_details.php', type: 'GET', data: { id: tid }, dataType: 'json',
                success: function(data) {
                    if(data) {
                        // Set text content
                        $('#modalTitle').text(data.title);
                        $('#modalCat').text(data.category);
                        $('#modalSummary').text(data.short_description);
                        $('#modalContent').html(data.full_content);
                        $('#modalDate').text(new Date(data.publish_date).toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' }));
                        
                        // Load inside image with smooth animation
                        $('#modalInsideImg').attr('src', data.inside_image).on('load', function() {
                            $('#tipSpinner').fadeOut(300);
                            $(this).fadeIn(600);
                        });
                    }
                }
            });
        });
        
        $('.js-close-modal, .modal-overlay').on('click', function(e) {
            if (e.target !== this && !$(e.target).hasClass('js-close-modal')) return;
            $('#tipModal').fadeOut(300);
            $('body').removeClass('modal-open');
        });
    });
</script>

<?php include "includes/footer.php"; ?>