<?php 
if (session_status() === PHP_SESSION_NONE) { session_start(); }
include "db.php"; 
include "includes/header.php"; 
?>

<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>

<style>
    :root {
        --primary: #518992;   /* Sovereign Teal */
        --navy: #0f1c3f;      /* Enterprise Navy */
        --warm: #fdfaf5;      /* Brand Warmth */
        --white: #ffffff;
        --heart: #e74c3c;     /* Action Red */
        --shadow-pro: 0 15px 40px rgba(15, 28, 63, 0.05);
    }

    body { font-family: 'Plus Jakarta Sans', sans-serif; background: var(--white); color: var(--navy); overflow-x: hidden; scroll-behavior: smooth; font-size: 15px; }

    /* --- master REFINED HERO --- */
    .foundation-hero {
        height: 80vh; width: 100%; position: relative;
        background: linear-gradient(rgba(15, 28, 63, 0.5), rgba(15, 28, 63, 0.6)), 
                    url('uploads/foundationbanner.jpg'); 
        background-size: cover; background-position: center; background-attachment: fixed;
        display: flex; align-items: center; justify-content: center; text-align: center; color: white;
    }
    .hero-content h1 { font-size: 3.5rem; font-weight: 800; line-height: 1.1; letter-spacing: -2px; margin-bottom: 20px; }
    .hero-content p { font-size: 1.1rem; max-width: 750px; margin: 0 auto 40px; opacity: 0.9; font-weight: 500; }

    /* --- master SECTION SPACING --- */
    .section-gap { padding: 100px 10%; }

    /* --- master IMPACT HUB (FIXED OVERLAP) --- */
    .impact-vault { padding: 80px 10%; background: var(--white); position: relative; z-index: 10; border-bottom: 1px solid #f1f5f9; }
    .impact-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; }
    .impact-card { 
        background: var(--white); padding: 40px 25px; border-radius: 30px; text-align: center;
        box-shadow: var(--shadow-pro); transition: 0.5s cubic-bezier(0.165, 0.84, 0.44, 1); border: 1.5px solid #f1f5f9;
    }
    .impact-card:hover { transform: translateY(-10px); border-color: var(--primary); }
    .impact-card i { font-size: 2.5rem; color: var(--primary); margin-bottom: 20px; }
    .impact-card h2 { font-size: 2.2rem; font-weight: 800; margin-bottom: 5px; color: var(--navy); }
    .impact-card p { font-size: 0.9rem; font-weight: 700; opacity: 0.6; text-transform: uppercase; }

    /* --- master MISSION GRID --- */
    .mission-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 70px; align-items: center; }
    .img-stack { position: relative; height: 500px; }
    .img-stack-main { width: 85%; height: 90%; border-radius: 35px; object-fit: cover; box-shadow: var(--shadow-pro); }
    .img-stack-float { position: absolute; bottom: 0; right: 0; width: 50%; height: 50%; border-radius: 30px; border: 10px solid white; object-fit: cover; }

    /* --- master NEW: PHILOSOPHY GRID --- */
    .phil-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 30px; }
    .phil-card { padding: 40px; background: var(--warm); border-radius: 25px; transition: 0.4s; }
    .phil-card:hover { background: var(--white); box-shadow: var(--shadow-pro); }
    .phil-card h4 { font-size: 1.3rem; margin-bottom: 15px; color: var(--primary); font-weight: 800; }

    /* --- master SUCCESS STORIES --- */
    .success-vault { background: var(--warm); padding: 100px 10%; }
    .success-card { background: white; border-radius: 35px; overflow: hidden; display: grid; grid-template-columns: 1fr 1fr; min-height: 400px; box-shadow: 0 20px 50px rgba(0,0,0,0.03); }
    .success-info { padding: 50px; display: flex; flex-direction: column; justify-content: center; }

    /* --- master HORIZONTAL DONATION TIERS --- */
    .tier-grid { 
        display: grid; 
        grid-template-columns: repeat(3, 1fr); /* FORCED HORIZONTAL LINE */
        gap: 25px; margin-top: 50px; 
    }
    .tier-card { 
        background: white; padding: 50px 30px; border-radius: 35px; border: 1.5px solid #f1f5f9; 
        text-align: center; transition: 0.5s; display: flex; flex-direction: column; height: 100%;
    }
    .tier-card:hover { transform: translateY(-12px); border-color: var(--primary); box-shadow: var(--shadow-pro); }
    .tier-card.featured { background: var(--navy); color: white; border: none; }
    .tier-card h3 { font-size: 1.8rem; font-weight: 800; margin-bottom: 15px; }
    .tier-card p { font-size: 0.95rem; line-height: 1.6; margin-bottom: 25px; flex-grow: 1; }

    /* --- master NEW: VOLUNTEER BANNER --- */
    .volunteer-banner { background: var(--primary); padding: 80px 10%; border-radius: 40px; color: white; display: flex; align-items: center; justify-content: space-between; gap: 40px; }

    .btn-heart { 
        background: var(--heart); color: white !important; padding: 16px 40px; border-radius: 50px; 
        font-weight: 800; text-decoration: none; display: inline-block; transition: 0.4s;
        text-transform: uppercase; letter-spacing: 1.5px; font-size: 0.85rem; border: none; cursor: pointer;
    }
    .btn-heart:hover { transform: scale(1.05); background: white; color: var(--heart) !important; }

    @media (max-width: 992px) {
        .impact-grid, .tier-grid, .phil-grid { grid-template-columns: 1fr; }
        .mission-grid, .success-card, .volunteer-banner { grid-template-columns: 1fr; flex-direction: column; text-align: center; }
    }
</style>

<section class="foundation-hero">
    <div class="hero-content" data-aos="zoom-in" data-aos-duration="1200">
        <h1>One Heart.<br>Every Paw.</h1>
        <p>A professional philanthropic ecosystem providing clinical rescue operations and life-long guardianship.</p>
        <a href="#donate" class="btn-heart">JOIN THE CAUSE</a>
    </div>
</section>

<div class="impact-vault">
    <div class="impact-grid">
        <div class="impact-card" data-aos="fade-up">
            <i class="fas fa-hand-holding-heart"></i>
            <h2 class="counter" data-target="5800">0</h2>
            <p>Rescues Completed</p>
        </div>
        <div class="impact-card" data-aos="fade-up" data-aos-delay="100">
            <i class="fas fa-microscope"></i>
            <h2 class="counter" data-target="11000">0</h2>
            <p>Clinical Surgeries</p>
        </div>
        <div class="impact-card" data-aos="fade-up" data-aos-delay="200">
            <i class="fas fa-house-chimney"></i>
            <h2 class="counter" data-target="2200">0</h2>
            <p>Forever Homes</p>
        </div>
        <div class="impact-card" data-aos="fade-up" data-aos-delay="300">
            <i class="fas fa-utensils"></i>
            <h2 class="counter" data-target="85">0</h2><h2 style="display:inline; font-size:2rem;">K+</h2>
            <p>Daily Meals</p>
        </div>
    </div>
</div>

<section class="section-gap">
    <div class="mission-grid">
        <div class="img-stack" data-aos="fade-right">
            <img src="uploads/mission_main.jpg" class="img-stack-main" alt="Rescue Story">
            <img src="uploads/mission_sub.jpg" class="img-stack-float" alt="Medical Team">
        </div>
        <div data-aos="fade-left">
            <h4 style="color:var(--primary); font-weight:800; text-transform:uppercase; letter-spacing:3px; margin-bottom:10px;">The Voice for the Voiceless</h4>
            <h2 style="font-size:2.8rem; font-weight:900; line-height:1.2; margin-bottom:25px;">Healing What the<br>World Forgets.</h2>
            <p style="color:#64748b; line-height:1.8; margin-bottom:30px;">The FurryMart Foundation allocates 10% of global retail revenue toward India's first 24/7 stray trauma clinical protocol.</p>
            <div style="display:flex; gap:20px; align-items:flex-start;">
                <div style="background:var(--warm); padding:15px; border-radius:12px;"><i class="fas fa-ambulance" style="color:var(--primary); font-size:1.2rem;"></i></div>
                <div><h5 style="font-size:1.1rem; font-weight:800;">Rapid Trauma Units</h5><p style="font-size:0.9rem; opacity:0.8;">Emergency response units deployed within 30 minutes of clinical alerts.</p></div>
            </div>
        </div>
    </div>
</section>

<section class="section-gap" style="background: var(--bg-warm);">
    <div style="text-align:center; margin-bottom:60px;">
        <h2 style="font-size:2.8rem; font-weight:800;">The Guardian Philosophy</h2>
        <p style="color:#64748b;">Our commitment to ethical rescue and clinical excellence.</p>
    </div>
    <div class="phil-grid">
        <div class="phil-card" data-aos="fade-up">
            <h4>Total Transparency</h4>
            <p>Every rupee is tracked and audited, ensuring your empathy reaches the paws that need it most.</p>
        </div>
        <div class="phil-card" data-aos="fade-up" data-aos-delay="100">
            <h4>Clinical Dignity</h4>
            <p>We provide strays with the same high-density medical equipment used in our retail hospitals.</p>
        </div>
        <div class="phil-card" data-aos="fade-up" data-aos-delay="200">
            <h4>Life-Long Support</h4>
            <p>Our responsibility doesn't end at adoption; we provide life-long medical checkups for rescued pets.</p>
        </div>
    </div>
</section>

<section style="padding: 100px 10%;">
    <div class="volunteer-banner" data-aos="flip-up">
        <div>
            <h2 style="font-size:2.2rem; font-weight:800; margin-bottom:10px;">Become a Guardian</h2>
            <p style="opacity:0.9; font-size:1rem;">Lend your hands to the cause. Volunteer for our weekend rescue drives.</p>
        </div>
        <a href="Contact.php" class="btn-heart" style="background:white; color:var(--primary) !important;">SIGN UP TO HELP</a>
    </div>
</section>

<section class="success-vault">
    <div style="text-align:center; margin-bottom:60px;">
        <h2 style="font-size:2.8rem; font-weight:800; letter-spacing:-1px;">Transformation Tales</h2>
    </div>
    <div class="swiper successSwiper">
        <div class="swiper-wrapper">
            <div class="swiper-slide">
                <div class="success-card">
                    <img src="uploads/success_1.jpg" style="width:100%; height:100%; object-fit:cover;">
                    <div class="success-info">
                        <h3 style="font-size:1.8rem; font-weight:800; color:var(--primary); margin-bottom:15px;">Bella's Recovery</h3>
                        <p style="color:#64748b; line-height:1.7; margin-bottom:25px;">Found in a critical clinical state, Bella underwent a 6-month protocol. Today, she lives in a loving home.</p>
                        <a href="blog.php" style="color:var(--heart); font-weight:800; text-decoration:none; font-size:0.9rem;">VIEW RECOVERY LOG <i class="fas fa-arrow-right"></i></a>
                    </div>
                </div>
            </div>
        </div>
        <div class="swiper-pagination"></div>
    </div>
</section>

<section id="donate" class="section-gap">
    <div style="text-align:center; margin-bottom:60px;">
        <h2 style="font-size:3rem; font-weight:800; letter-spacing:-1.5px;">Sovereign Support</h2>
        <p style="color:#64748b;">100% of public support is utilized for life-saving medical supplies.</p>
    </div>
    
    <div class="tier-grid">
        <div class="tier-card" data-aos="fade-up">
            <h3>Nourish</h3>
            <p>Provide 100 complete clinical meals for rescued community pets.</p>
            <a href="donate_details.php?tier=Nourish" class="btn-heart">Donate ₹1,500</a>
        </div>
        <div class="tier-card featured" data-aos="fade-up" data-aos-delay="200">
            <h3>Heal</h3>
            <p>Sponsor one critical trauma surgery and a 4-month rehabilitation cycle.</p>
            <a href="donate_details.php?tier=Heal" class="btn-heart" style="background:white; color:var(--navy) !important;">Donate ₹8,500</a>
        </div>
        <div class="tier-card" data-aos="fade-up" data-aos-delay="400">
            <h3>Rescue</h3>
            <p>Fund one full week of trauma unit fuel and rescue operations.</p>
            <a href="donate_details.php?tier=Rescue" class="btn-heart">Donate ₹25,000</a>
        </div>
    </div>
</section>

<section class="section-gap" style="background:var(--warm); border-radius:100px 100px 0 0;">
    <div style="text-align:center; max-width:800px; margin:0 auto;">
        <i class="fas fa-shield-check" style="font-size:3rem; color:var(--primary); margin-bottom:30px;"></i>
        <h2 style="font-size:2.5rem; font-weight:800; margin-bottom:20px;">Sovereign Transparency</h2>
        <p style="color:#64748b; line-height:1.8;">We believe in 100% transparency. Every rupee contributed is tracked through our digital ledger. Our annual clinical audit reports are accessible to all registered FurryMart Parents.</p>
    </div>
</section>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

<script>
    AOS.init({ duration: 1200, once: true, offset: 200 });

    new Swiper('.successSwiper', {
        loop: true, autoplay: { delay: 6000 },
        pagination: { el: '.swiper-pagination', clickable: true },
        spaceBetween: 50
    });

    const runCounters = () => {
        $('.counter').each(function() {
            const $this = $(this);
            const target = parseInt($this.data('target'));
            $({ countNum: 0 }).animate({ countNum: target }, {
                duration: 3000, easing: 'swing',
                step: function() { $this.text(Math.floor(this.countNum)); },
                complete: function() { $this.text(this.countNum); }
            });
        });
    };

    let counterDone = false;
    $(window).scroll(function() {
        const impactSection = $('.impact-vault');
        if (impactSection.length && !counterDone) {
            const top = impactSection.offset().top - window.innerHeight;
            if ($(window).scrollTop() > top) {
                runCounters();
                counterDone = true;
            }
        }
    });
</script>

<?php include 'includes/footer.php'; ?>