<?php 
/* Include your existing header */
include('includes/header.php'); 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us | FurryMart Pet Care Ecosystem</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        /* --- PREMIUM CSS VARIABLES --- */
        :root {
            --primary-blue: #0b3d59;
            --accent-red: #e63946;
            --bg-cream: #fffcf7;
            --text-dark: #333;
            --white: #ffffff;
            --glass: rgba(255, 255, 255, 0.8);
            --transition: all 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            --card-light-blue: #f1faff;
            --border-light-blue: #a2d2ff;
            --border-grey: #e8e8e8;
        }

        /* --- SMOOTH SCROLLING --- */
        html { scroll-behavior: smooth; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body { 
            font-family: 'Poppins', sans-serif; 
            background-color: var(--bg-cream); 
            color: var(--text-dark);
            overflow-x: hidden;
            line-height: 1.6;
        }

        .container { max-width: 1140px; margin: 0 auto; padding: 0 15px; }

        /* --- ENHANCED ANIMATIONS --- */
        @keyframes slideUpFade {
            0% { opacity: 0; transform: translateY(50px); filter: blur(5px); }
            100% { opacity: 1; transform: translateY(0); filter: blur(0); }
        }

        .reveal { opacity: 0; }
        .reveal.active { animation: slideUpFade 0.9s ease-out forwards; }

        section { padding: 40px 0; }

        /* --- HERO SECTION --- */
        .hero-flex { 
            display: flex; 
            align-items: center; 
            gap: 40px; 
            background: var(--white); 
            padding: 40px; 
            border-radius: 20px; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            border: 1px solid rgba(11, 61, 89, 0.1);
        }
        .hero-video-card { 
            flex: 1; 
            position: relative; 
            border-radius: 20px; 
            overflow: hidden; 
            background: #000; 
            height: 350px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.2);
        }
        .hero-video-card video { width: 100%; height: 100%; object-fit: cover; }
        .hero-text { flex: 1; }
        .hero-text h4 { color: var(--accent-red); font-weight: 600; letter-spacing: 2px; text-transform: uppercase; font-size: 0.8rem; margin-bottom: 10px; }
        .hero-text h1 { font-size: 2.8rem; color: var(--primary-blue); margin-bottom: 20px; line-height: 1.1; font-weight: 700; }

        /* --- STATS SECTION --- */
        .stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 15px; }
        .stat-box { 
            padding: 30px; 
            text-align: center; 
            border-radius: 15px; 
            transition: var(--transition);
            background: var(--white);
            box-shadow: 0 5px 15px rgba(0,0,0,0.03);
        }
        .stat-box:hover { transform: translateY(-10px); box-shadow: 0 15px 30px rgba(0,0,0,0.1); }
        .stat-box h2 { font-size: 2.2rem; color: var(--primary-blue); margin-bottom: 5px; }
        
        .color-1 { border-bottom: 5px solid #a2d2ff; } 
        .color-2 { border-bottom: 5px solid #c7f9cc; }
        .color-3 { border-bottom: 5px solid #ffe5b4; } 
        .color-4 { border-bottom: 5px solid #cdb4db; }

        /* --- JOURNEY SECTION --- */
        .journey-flex { display: flex; align-items: center; gap: 50px; padding: 40px 0; }
        .journey-text { flex: 1; }
        .journey-image { flex: 1; position: relative; }
        .journey-image img { 
            width: 100%; 
            border-radius: 20px; 
            box-shadow: 20px 20px 0px var(--primary-blue);
            transition: var(--transition);
        }
        .journey-image:hover img { transform: translate(5px, 5px); box-shadow: 10px 10px 0px var(--accent-red); }

        /* --- MISSION SECTION --- */
        .mission-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-top: 20px; }
        .mission-card {
            background: var(--card-light-blue);
            border: 1.5px solid var(--border-light-blue);
            border-radius: 15px;
            padding: 35px;
            display: flex;
            align-items: center;
            gap: 25px;
            transition: var(--transition);
        }
        .mission-card:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.05); background: var(--white); }
        .mission-icon svg { width: 90px; height: 90px; fill: var(--primary-blue); }
        .mission-content h3 { color: var(--primary-blue); margin-bottom: 10px; font-size: 1.6rem; font-weight: 700; }
        .mission-content p { font-size: 0.95rem; color: #444; line-height: 1.5; }

        /* --- VALUES SECTION --- */
        .values-section { padding: 40px 0; }
        .values-heading { 
            font-size: 2.2rem; 
            font-weight: 700; 
            color: var(--text-dark); 
            margin-bottom: 25px; 
            text-align: left; 
        }
        .values-grid { 
            display: grid; 
            grid-template-columns: repeat(3, 1fr); 
            gap: 20px; 
        }
        .value-card {
            background: var(--white);
            border: 1px solid var(--border-grey);
            border-radius: 12px;
            padding: 50px 20px;
            text-align: center;
            transition: var(--transition);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 250px;
        }
        .value-card:hover { 
            transform: translateY(-8px); 
            box-shadow: 0 15px 30px rgba(0,0,0,0.05); 
            border-color: var(--primary-blue);
        }
        .value-icon-box { 
            margin-bottom: 25px; 
            display: flex; 
            justify-content: center; 
            align-items: center; 
        }
        .value-icon-box svg { 
            width: 80px; 
            height: 80px; 
            fill: var(--primary-blue); 
        }
        .value-card p {
            color: var(--primary-blue);
            font-weight: 600;
            font-size: 1.05rem;
            margin: 0;
            line-height: 1.4;
        }

        /* --- WHAT WE OFFER --- */
        .offer-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-top: 30px; }
        .offer-card { 
            background: var(--white); border-radius: 20px; padding: 40px 20px; text-align: center; 
            border: 1px solid rgba(0,0,0,0.05); transition: var(--transition);
        }
        .offer-card:hover { transform: scale(1.05); box-shadow: 0 20px 40px rgba(0,0,0,0.08); border-color: var(--accent-red); }
        .offer-icon { font-size: 3rem; margin-bottom: 20px; display: block; }

        /* --- AUTOMATIC PET VIDEO SLIDER SECTION --- */
        .video-slider-section { 
            background: #fdfaf3; 
            padding: 60px 0; 
            overflow: hidden; 
        }
        .slider-container { 
            position: relative; 
            width: 100%; 
            margin-top: 30px; 
        }
        .video-track { 
            display: flex; 
            gap: 20px; 
            transition: transform 0.6s cubic-bezier(0.45, 0, 0.55, 1); 
        }
        .video-item { 
            min-width: calc(100% / 4 - 15px); /* Shows 4 videos at a time on desktop */
            height: 420px; 
            background: #000; 
            border-radius: 20px; 
            overflow: hidden; 
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            position: relative;
        }
        .video-item video { 
            width: 100%; 
            height: 100%; 
            object-fit: cover; 
        }
        .video-label {
            position: absolute;
            bottom: 15px;
            left: 15px;
            background: rgba(11, 61, 89, 0.8);
            color: #fff;
            padding: 5px 15px;
            border-radius: 50px;
            font-size: 0.85rem;
            backdrop-filter: blur(5px);
        }

        @media (max-width: 992px) { .video-item { min-width: calc(100% / 2 - 10px); } }
        @media (max-width: 600px) { .video-item { min-width: 100%; } }

        /* --- BUTTONS --- */
        .btn-main { 
            background: var(--accent-red); 
            color: #fff; 
            padding: 12px 30px; 
            border-radius: 50px; 
            text-decoration: none; 
            display: inline-block; 
            font-weight: 600;
            transition: var(--transition);
            box-shadow: 0 10px 20px rgba(230, 57, 70, 0.3);
        }

        @media (max-width: 768px) {
            .hero-flex, .offer-grid, .stats-grid, .values-grid, .mission-grid { flex-direction: column; grid-template-columns: 1fr; }
            .hero-text h1 { font-size: 2rem; }
            .value-card { min-height: auto; padding: 30px 20px; }
        }
    </style>
</head>
<body>

<div class="container">
     <section class="reveal">
        <div class="hero-flex">
            <div class="hero-video-card">
                <video id="furryVideo" autoplay loop playsinline muted>
                    <source src="uploads/furrymart1.mp4" type="video/mp4">
                </video>
                <div id="unmuteBtn" style="position:absolute; bottom:15px; right:15px; background:rgba(11, 61, 89, 0.8); color:#fff; padding:8px 15px; border-radius:50px; cursor:pointer; font-size:12px; backdrop-filter: blur(5px);">🔇 Click for Sound</div>
            </div>
            <div class="hero-text">
                <h4>FurryMart: A Global Vision</h4>
                <h1>Complete Pet Care Ecosystem</h1>
                <p>FurryMart is India’s leading tech-enabled integrated pet-care platform. We provide a full-stack ecosystem designed to make pet parenting a joyful, stress-free experience for everyone.</p>
                <br>
                <a href="#journey" class="btn-main">Learn Our Story</a>
            </div>
        </div>
    </section>

    <section class="reveal">
        <div class="stats-grid">
            <div class="stat-box color-1"><h2>2000+</h2><p>Pet Parents</p></div>
            <div class="stat-box color-2"><h2>150+</h2><p>Expert Vets</p></div>
            <div class="stat-box color-3"><h2>40+</h2><p>Care Centers</p></div>
            <div class="stat-box color-4"><h2>20+</h2><p>Cities Nationwide</p></div>
        </div>
    </section>

    <section class="reveal" id="journey">
        <div class="journey-flex">
            <div class="journey-text">
                <h2 style="color: var(--primary-blue); font-size: 2.2rem; margin-bottom: 20px;">From Vision to Reality</h2>
                <p style="margin-bottom: 20px;">At FurryMart, we believe pets are family. Founded in 2024, our mission has always been to simplify pet parenting through technology and heartfelt care.</p>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                    <p>✅ 2024: The Spark</p>
                    <p>✅ 2025: Tech Integration</p>
                    <p>✅ 2026: Nationwide Growth</p>
                    <p>✅ 2027: Global Reach</p>
                </div>
            </div>
            <div class="journey-image">
                <img src="https://images.unsplash.com/photo-1516734212186-a967f81ad0d7?q=80&w=800" alt="FurryMart Journey">
            </div>
        </div>
    </section>

    <section class="reveal">
        <h2 style="color: var(--text-dark); font-size: 2.2rem; margin-bottom: 20px;">Mission</h2>
        <div class="mission-grid">
            <div class="mission-card">
                <div class="mission-icon">
                    <svg viewBox="0 0 24 24"><path d="M12,2A10,10 0 0,0 2,12A10,10 0 0,0 12,22A10,10 0 0,0 22,12A10,10 0 0,0 12,2M12,4A8,8 0 0,1 20,12A8,8 0 0,1 12,20A8,8 0 0,1 4,12A8,8 0 0,1 12,4M12,6A6,6 0 0,0 6,12A6,6 0 0,0 12,18A6,6 0 0,0 18,12A6,6 0 0,0 12,6M12,8A4,4 0 0,1 16,12A4,4 0 0,1 12,16A4,4 0 0,1 8,12A4,4 0 0,1 12,8Z" /></svg>
                </div>
                <div class="mission-content">
                    <h3>Pet families</h3>
                    <p>To provide easy access to reliable, standardized and quality pet care that enhances the joy of pet parenting.</p>
                </div>
            </div>
            <div class="mission-card">
                <div class="mission-icon">
                    <svg viewBox="0 0 24 24"><path d="M12,12C14.21,12 16,10.21 16,8C16,5.79 14.21,4 12,4C9.79,4 8,5.79 8,8C8,10.21 9.79,12 12,12M12,14C9.33,14 4,15.34 4,18V20H20V18C20,15.34 14.67,14 12,14Z" /></svg>
                </div>
                <div class="mission-content">
                    <h3>Caregivers</h3>
                    <p>To provide steady and equitable income opportunities to pet lovers who chose to transform their passion into profession.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="reveal values-section">
        <h2 class="values-heading">The Values that Guide Us</h2>
        <div class="values-grid">
            <div class="value-card">
                <div class="value-icon-box">
                    <svg viewBox="0 0 24 24"><path d="M16 13c-2.21 0-4 1.79-4 4 0 2.21 1.79 4 4 4s4-1.79 4-4c0-2.21-1.79-4-4-4zM8 13c-2.21 0-4 1.79-4 4 0 2.21 1.79 4 4 4s4-1.79 4-4c0-2.21-1.79-4-4-4zM12 4c-2.21 0-4 1.79-4 4s1.79 4 4 4 4-1.79 4-4-1.79-4-4-4z"/></svg>
                </div>
                <p>People first, foremost and above all.</p>
            </div>
            <div class="value-card">
                <div class="value-icon-box">
                    <svg viewBox="0 0 24 24"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
                </div>
                <p>Thrive together and win as a team.</p>
            </div>
            <div class="value-card">
                <div class="value-icon-box">
                    <svg viewBox="0 0 24 24"><path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm-2 16l-4-4 1.41-1.41L10 14.17l6.59-6.59L18 9l-8 8z"/></svg>
                </div>
                <p>Keep building trust with all stakeholders.</p>
            </div>
            <div class="value-card">
                <div class="value-icon-box">
                    <svg viewBox="0 0 24 24"><path d="M12 3v9.28c-.47-.17-.97-.28-1.5-.28C8.01 12 6 14.01 6 16.5S8.01 21 10.5 21c2.31 0 4.2-1.75 4.46-4H18v-2h-3.04c-.26-2.25-2.15-4-4.46-4V5l7 2.1V3l-10-3z"/></svg>
                </div>
                <p>Act with Integrity, no matter what.</p>
            </div>
            <div class="value-card">
                <div class="value-icon-box">
                    <svg viewBox="0 0 24 24"><path d="M12 2C8.13 2 5 5.13 5 9c0 2.38 1.19 4.47 3 5.74V17c0 .55.45 1 1 1h6c.55 0 1-.45 1-1v-2.26c1.81-1.27 3-3.36 3-5.74 0-3.87-3.13-7-7-7zM9 21c0 .55.45 1 1 1h4c.55 0 1-.45 1-1v-1H9v1z"/></svg>
                </div>
                <p>Champion Innovation to stay a step ahead.</p>
            </div>
            <div class="value-card">
                <div class="value-icon-box">
                    <svg viewBox="0 0 24 24"><path d="M4.5 9.5c-1.38 0-2.5 1.12-2.5 2.5s1.12 2.5 2.5 2.5 2.5-1.12 2.5-2.5-1.12-2.5-2.5-2.5zm15 0c-1.38 0-2.5 1.12-2.5 2.5s1.12 2.5 2.5 2.5 2.5-1.12 2.5-2.5-1.12-2.5-2.5-2.5zm-7.5-3c-1.38 0-2.5 1.12-2.5 2.5s1.12 2.5 2.5 2.5 2.5-1.12 2.5-2.5-1.12-2.5-2.5-2.5zm0 10c-3.5 0-6.5-1.58-8.13-4h16.26c-1.63 2.42-4.63 4-8.13 4z"/></svg>
                </div>
                <p>We’re here to keep your Pets happy & healthy.</p>
            </div>
        </div>
    </section>

    <section class="reveal">
        <h2 style="text-align:center; color: var(--primary-blue); font-size: 2.5rem;">What We Offer</h2>
        <p style="text-align:center; color: #777; margin-bottom: 40px;">Professional services tailored for your furry family members.</p>
        <div class="offer-grid">
            <div class="offer-card">
                <span class="offer-icon">✂️</span>
                <h3>Premium Grooming</h3>
                <p>Top-tier styling and hygiene by certified professionals.</p>
            </div>
            <div class="offer-card">
                <span class="offer-icon">🩺</span>
                <h3>Instant Tele-Health</h3>
                <p>Connect with expert vets in under 10 minutes, 24/7.</p>
            </div>
            <div class="offer-card">
                <span class="offer-icon">🏡</span>
                <h3>Home Care</h3>
                <p>Grooming and medical checkups in the comfort of your home.</p>
            </div>
        </div>
    </section>

</div>

<section class="video-slider-section reveal">
    <div class="container">
        <h2 style="text-align:center; color: var(--primary-blue); font-size: 2.2rem;">Happy Tails & Funny Moments</h2>
        <p style="text-align:center; color: #777; margin-bottom: 30px;">Heartwarming memories from our FurryMart family members.</p>
        
        <div class="slider-container">
            <div class="video-track" id="sliderTrack">
                <div class="video-item"><video autoplay loop muted playsinline><source src="uploads/funtime.mp4" type="video/mp4"></video><span class="video-label">Fun Time</span></div>
                <div class="video-item"><video autoplay loop muted playsinline><source src="uploads/cute.mp4" type="video/mp4"></video><span class="video-label">Cuteness Overload</span></div>
                <div class="video-item"><video autoplay loop muted playsinline><source src="uploads/happytail.mp4" type="video/mp4"></video><span class="video-label">Happy Tails</span></div>
                <div class="video-item"><video autoplay loop muted playsinline><source src="uploads/naugty.mp4" type="video/mp4"></video><span class="video-label">Naughty Kitty</span></div>
                <div class="video-item"><video autoplay loop muted playsinline><source src="uploads/cute2.mp4" type="video/mp4"></video><span class="video-label">Zoomies</span></div>
                <div class="video-item"><video autoplay loop muted playsinline><source src="uploads/bestfrd.mp4" type="video/mp4"></video><span class="video-label">Best Friends</span></div>
                <div class="video-item"><video autoplay loop muted playsinline><source src="uploads/bath.mp4" type="video/mp4"></video><span class="video-label">Bath Day</span></div>
            </div>
        </div>
    </div>
</section>

<script>
    /* --- SCROLL REVEAL LOGIC --- */
    const reveals = document.querySelectorAll('.reveal');
    const observer = new IntersectionObserver(entries => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('active');
            }
        });
    }, { threshold: 0.15 });

    reveals.forEach(reveal => observer.observe(reveal));

    /* --- AUTOMATIC SLIDER LOGIC --- */
    const track = document.getElementById('sliderTrack');
    let index = 0;
    const itemsToShow = window.innerWidth > 992 ? 4 : (window.innerWidth > 600 ? 2 : 1);
    const totalItems = 7;

    function autoSlide() {
        index++;
        if (index > totalItems - itemsToShow) {
            index = 0; // Return to start
        }
        const moveAmount = index * (track.children[0].offsetWidth + 20);
        track.style.transform = `translateX(-${moveAmount}px)`;
    }

    // Slider moves every 3 seconds
    setInterval(autoSlide, 3000);

    /* --- VIDEO SOUND CONTROLLER --- */
    const video = document.getElementById('furryVideo');
    const unmuteBtn = document.getElementById('unmuteBtn');

    unmuteBtn.addEventListener('click', () => {
        if (video.muted) {
            video.muted = false;
            unmuteBtn.innerHTML = "🔊 Sound On";
            unmuteBtn.style.background = "var(--accent-red)";
        } else {
            video.muted = true;
            unmuteBtn.innerHTML = "🔇 Click for Sound";
            unmuteBtn.style.background = "rgba(11, 61, 89, 0.8)";
        }
    });
</script>

</body>
</html>

<?php 
/* Include your existing footer */
include('includes/footer.php'); 
?>