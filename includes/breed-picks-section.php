<style>
    .breed-ready-section { 
        padding: 35px 5% 30px; 
        background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%); 
        position: relative; 
        overflow: hidden;
        margin-bottom: 0;
    }
    
    .breed-ready-section::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, 
            #FFB84D 0%, 
            #87ceeb 50%, 
            #98fb98 100%);
        animation: shimmerFlow 4s ease-in-out infinite;
    }
    
    @keyframes shimmerFlow {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.7; }
    }
    
    .breed-header-flex { 
        display: flex; 
        justify-content: space-between; 
        align-items: center; 
        margin-bottom: 30px;
        flex-wrap: wrap;
        gap: 20px;
    }
    
    .breed-title-area h2 { 
        font-size: 2rem; 
        font-weight: 900; 
        color: #1a1c1e; 
        margin: 0; 
        letter-spacing: -0.5px; 
        position: relative;
        display: inline-block;
        text-transform: uppercase;
    }
    
    .breed-title-area h2::after {
        content: '';
        position: absolute;
        bottom: -6px;
        left: 0;
        width: 60px;
        height: 3px;
        background: linear-gradient(90deg, #e11d48, #FFB84D);
        border-radius: 2px;
        animation: pulseWidth 2s ease-in-out infinite;
    }
    
    @keyframes pulseWidth {
        0%, 100% { width: 60px; opacity: 1; }
        50% { width: 80px; opacity: 0.8; }
    }
    
    .breed-switch { 
        display: flex; 
        background: linear-gradient(135deg, #ffffff 0%, #fafbff 100%);
        border: 2px solid #e2e8f0; 
        border-radius: 50px; 
        padding: 5px; 
        box-shadow: 
            0 4px 20px rgba(0,0,0,0.08),
            inset 0 1px 0 rgba(255,255,255,0.9);
        gap: 4px;
        position: relative;
        overflow: hidden;
    }
    
    .breed-switch::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.6), transparent);
        animation: switchShimmer 3s infinite;
    }
    
    @keyframes switchShimmer {
        0% { left: -100%; }
        100% { left: 200%; }
    }
    
    .breed-btn { 
        border: none; 
        background: transparent; 
        padding: 10px 24px; 
        border-radius: 50px; 
        font-weight: 800; 
        cursor: pointer; 
        transition: all 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55); 
        color: #64748b; 
        font-family: 'Plus Jakarta Sans', sans-serif; 
        font-size: 0.9rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        position: relative;
        z-index: 1;
        display: flex;
        align-items: center;
        gap: 7px;
    }
    
    .breed-btn::before {
        content: '';
        position: absolute;
        inset: 0;
        border-radius: 50px;
        background: linear-gradient(135deg, #e11d48, #be123c);
        opacity: 0;
        transition: all 0.4s;
        z-index: -1;
        transform: scale(0.9);
    }
    
    .breed-btn:hover:not(.active) {
        color: #1e293b;
        transform: translateY(-2px);
        background: rgba(226, 232, 240, 0.3);
    }
    
    .breed-btn.active { 
        color: #fff; 
        box-shadow: 
            0 4px 15px rgba(225, 29, 72, 0.4),
            0 2px 8px rgba(225, 29, 72, 0.2),
            inset 0 1px 0 rgba(255,255,255,0.2);
        transform: scale(1.05);
    }
    
    .breed-btn.active::before {
        opacity: 1;
        transform: scale(1);
    }
    
    .breed-btn .emoji {
        font-size: 1.15rem;
        transition: transform 0.3s;
        display: inline-block;
    }
    
    .breed-btn.active .emoji {
        animation: emojiJump 0.6s cubic-bezier(0.68, -0.55, 0.265, 1.55);
    }
    
    @keyframes emojiJump {
        0%, 100% { transform: scale(1) rotate(0deg); }
        25% { transform: scale(1.3) rotate(-10deg); }
        75% { transform: scale(1.3) rotate(10deg); }
    }

    .breed-swiper-container { 
        position: relative; 
        overflow: hidden;
        padding: 5px 0;
        mask-image: linear-gradient(90deg, 
            transparent 0%, 
            black 4%, 
            black 96%, 
            transparent 100%);
        -webkit-mask-image: linear-gradient(90deg, 
            transparent 0%, 
            black 4%, 
            black 96%, 
            transparent 100%);
    }
    
    .breed-container { 
        display: none;
        opacity: 0;
    }
    
    .breed-container.active { 
        display: block;
        animation: categoryFadeSlide 0.6s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
    }
    
    @keyframes categoryFadeSlide {
        from { 
            opacity: 0; 
            transform: translateX(-40px) scale(0.9); 
        }
        to { 
            opacity: 1; 
            transform: translateX(0) scale(1); 
        }
    }
    
    .swiper-wrapper {
        display: flex;
        animation: infiniteScroll 35s linear infinite;
    }
    
    .breed-swiper-container:hover .swiper-wrapper {
        animation-play-state: paused;
    }
    
    @keyframes infiniteScroll {
        0% { transform: translateX(0); }
        100% { transform: translateX(-50%); }
    }
    
    .swiper-slide {
        flex-shrink: 0;
        width: auto;
    }
    
    .breed-card { 
        text-align: center; 
        text-decoration: none; 
        display: block; 
        padding: 12px; 
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        position: relative;
    }
    
    .breed-circle-wrapper { 
        width: 130px; 
        height: 130px; 
        border-radius: 50%; 
        margin: 0 auto 10px; 
        position: relative; 
        overflow: hidden; 
        border: 4px solid #fff; 
        box-shadow: 0 8px 25px rgba(0,0,0,0.12); 
        transition: all 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        background: linear-gradient(135deg, rgba(255,255,255,0.95), rgba(248,250,252,0.95));
    }
    
    /* Current page breed highlight */
    .breed-card.current-page .breed-circle-wrapper {
        border-color: #e11d48;
        border-width: 5px;
        box-shadow: 
            0 8px 30px rgba(225, 29, 72, 0.3),
            0 0 0 3px rgba(225, 29, 72, 0.1),
            inset 0 0 20px rgba(225, 29, 72, 0.05);
        animation: currentPulse 2s ease-in-out infinite;
    }
    
    @keyframes currentPulse {
        0%, 100% { 
            box-shadow: 
                0 8px 30px rgba(225, 29, 72, 0.3),
                0 0 0 3px rgba(225, 29, 72, 0.1),
                inset 0 0 20px rgba(225, 29, 72, 0.05);
        }
        50% { 
            box-shadow: 
                0 12px 40px rgba(225, 29, 72, 0.4),
                0 0 0 6px rgba(225, 29, 72, 0.2),
                inset 0 0 30px rgba(225, 29, 72, 0.1);
        }
    }
    
    .breed-card.current-page .breed-name-label {
        color: #e11d48;
        font-weight: 900;
    }
    
    .breed-card.current-page .breed-name-label::after {
        width: 100%;
        background: #e11d48;
    }
    
    .breed-circle-wrapper::before {
        content: '';
        position: absolute;
        inset: -30%;
        background: conic-gradient(
            from 0deg,
            transparent 0deg,
            rgba(255,255,255,0.7) 45deg,
            transparent 90deg
        );
        opacity: 0;
        animation: sparkRotate 3s linear infinite;
    }
    
    @keyframes sparkRotate {
        0% { transform: rotate(0deg); opacity: 0; }
        50% { opacity: 1; }
        100% { transform: rotate(360deg); opacity: 0; }
    }
    
    .breed-card:hover .breed-circle-wrapper::before,
    .breed-card.current-page .breed-circle-wrapper::before {
        opacity: 1;
    }
    
    .breed-circle-wrapper::after {
        content: '';
        position: absolute;
        inset: 0;
        border-radius: 50%;
        background: radial-gradient(circle at 35% 35%, rgba(255,255,255,0.5), transparent 70%);
        opacity: 0;
        transition: opacity 0.4s;
    }
    
    .breed-card:hover .breed-circle-wrapper::after,
    .breed-card.current-page .breed-circle-wrapper::after {
        opacity: 1;
    }
    
    .breed-circle-wrapper img { 
        width: 100%; 
        height: 100%; 
        object-fit: cover; 
        transition: all 0.5s ease;
        filter: brightness(1) contrast(1);
    }
    
    .breed-card:hover .breed-circle-wrapper { 
        transform: translateY(-12px) scale(1.08); 
        box-shadow: 
            0 18px 45px rgba(0,0,0,0.22),
            0 0 0 6px rgba(255,255,255,0.6);
        border-width: 5px;
    }
    
    .breed-card:hover .breed-circle-wrapper img {
        transform: scale(1.15) rotate(3deg);
        filter: brightness(1.12) contrast(1.05);
    }
    
    .breed-name-label { 
        font-weight: 800; 
        font-size: 0.9rem; 
        color: #1e293b; 
        display: block; 
        margin-top: 8px;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        transition: all 0.3s;
        position: relative;
    }
    
    .breed-name-label::after {
        content: '';
        position: absolute;
        bottom: -3px;
        left: 50%;
        transform: translateX(-50%);
        width: 0;
        height: 2px;
        background: linear-gradient(90deg, #e11d48, #FFB84D);
        border-radius: 2px;
        transition: width 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }
    
    .breed-card:hover .breed-name-label {
        color: #e11d48;
        transform: scale(1.06);
        text-shadow: 0 2px 8px rgba(225, 29, 72, 0.2);
    }
    
    .breed-card:hover .breed-name-label::after {
        width: 100%;
    }
    
    @media (max-width: 768px) {
        .breed-ready-section {
            padding: 25px 3% 20px;
        }
        
        .breed-header-flex {
            flex-direction: column;
            align-items: flex-start;
            gap: 15px;
            margin-bottom: 25px;
        }
        
        .breed-title-area h2 {
            font-size: 1.5rem;
        }
        
        .breed-switch {
            width: 100%;
            justify-content: space-between;
        }
        
        .breed-btn {
            padding: 8px 16px;
            font-size: 0.8rem;
            flex: 1;
            justify-content: center;
        }
        
        .breed-circle-wrapper {
            width: 110px;
            height: 110px;
        }
        
        .breed-name-label {
            font-size: 0.8rem;
        }
        
        .swiper-wrapper {
            animation: infiniteScroll 25s linear infinite;
        }
    }
</style>

<section class="breed-ready-section">
    <div class="breed-header-flex">
        <div class="breed-title-area">
            <h2>Breed Ready Picks</h2>
        </div>
        <div class="breed-switch">
            <button class="breed-btn active" onclick="switchBreed('Dog', this)" data-category="Dog">
                <span class="emoji">🐕</span>
                <span>Dog</span>
            </button>
            <button class="breed-btn" onclick="switchBreed('Cat', this)" data-category="Cat">
                <span class="emoji">🐱</span>
                <span>Cat</span>
            </button>
            <button class="breed-btn" onclick="switchBreed('Bird', this)" data-category="Bird">
                <span class="emoji">🦜</span>
                <span>Bird</span>
            </button>
        </div>
    </div>
    <div class="breed-swiper-container">
        <?php 
        $cats = ['Dog', 'Cat', 'Bird']; 
        $colors = ['Dog' => '#ffb84d', 'Cat' => '#87ceeb', 'Bird' => '#98fb98'];
        
        // Get current page name to highlight current breed
        $current_page = isset($_SERVER['REQUEST_URI']) ? basename(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '.php') : '';
        
        foreach($cats as $index => $cat): 
            $active_class = ($index === 0) ? 'active' : ''; 
        ?>
        <div class="breed-container <?php echo $active_class; ?>" id="cat-<?php echo $cat; ?>" data-category="<?php echo $cat; ?>">
            <div class="swiper breed-swiper-<?php echo $cat; ?>">
                <div class="swiper-wrapper">
                    <?php 
                    $breeds = mysqli_query($conn, "SELECT * FROM breed_picks WHERE category='$cat' AND status='active' ORDER BY sort_order ASC");
                    $breeds_array = [];
                    while($row = mysqli_fetch_assoc($breeds)) {
                        $breeds_array[] = $row;
                    }
                    
                    // Duplicate breeds for seamless infinite scroll
                    for($i = 0; $i < 2; $i++):
                        foreach($breeds_array as $row): 
                            // Check if this breed's page is currently open
                            $breed_page = basename($row['details_page_url'], '.php');
                            $is_current = ($current_page === $breed_page) ? 'current-page' : '';
                    ?>
                        <div class="swiper-slide">
                            <a href="<?php echo $row['details_page_url']; ?>" class="breed-card <?php echo $is_current; ?>">
                                <div class="breed-circle-wrapper" style="background: linear-gradient(135deg, <?php echo $colors[$cat]; ?>, rgba(255,255,255,0.8));">
                                    <img src="uploads/breeds/<?php echo $row['image_path']; ?>" alt="<?php echo $row['breed_name']; ?>">
                                </div>
                                <span class="breed-name-label"><?php echo $row['breed_name']; ?></span>
                            </a>
                        </div>
                    <?php 
                        endforeach;
                    endfor;
                    ?>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</section>

<script>
function switchBreed(category, button) {
    // Remove active from all buttons
    document.querySelectorAll('.breed-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    
    // Add active to clicked button
    button.classList.add('active');
    
    // Hide all breed containers
    document.querySelectorAll('.breed-container').forEach(container => {
        container.classList.remove('active');
    });
    
    // Show selected category with animation
    setTimeout(() => {
        document.getElementById('cat-' + category).classList.add('active');
    }, 150);
}
</script>