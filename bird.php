<?php include "includes/header.php"; ?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>

<style>
    :root {
        --primary: #518992;
        --orange: #ff6b35;
        --gold: #f7931e;
        --navy: #0f172a;
        --light-bg: #f8fafc;
    }
    
    body { 
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        min-height: 100vh;
        overflow-x: hidden;
    }

    /* Animated Background Particles */
    .particles {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: 0;
        pointer-events: none;
    }
    
    .particle {
        position: absolute;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
        animation: float 15s infinite ease-in-out;
    }
    
    @keyframes float {
        0%, 100% { transform: translateY(0) translateX(0) rotate(0deg); opacity: 0; }
        10% { opacity: 1; }
        90% { opacity: 1; }
        100% { transform: translateY(-100vh) translateX(100px) rotate(360deg); opacity: 0; }
    }

    /* Main Container */
    .coming-soon-container {
        position: relative;
        z-index: 1;
        min-height: 90vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 60px 5%;
    }

    /* Content Card */
    .content-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(20px);
        border-radius: 50px;
        padding: 80px 60px;
        max-width: 900px;
        width: 100%;
        box-shadow: 0 30px 90px rgba(0, 0, 0, 0.3);
        text-align: center;
        position: relative;
        overflow: hidden;
    }

    /* Animated Gradient Border */
    .content-card::before {
        content: '';
        position: absolute;
        top: -2px;
        left: -2px;
        right: -2px;
        bottom: -2px;
        background: linear-gradient(45deg, #a07f73, #d1a470, #868b9e, #9170b3);
        border-radius: 50px;
        z-index: -1;
        background-size: 400% 400%;
        animation: gradientShift 8s ease infinite;
    }

    @keyframes gradientShift {
        0% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
        100% { background-position: 0% 50%; }
    }

    /* Bird Icon Animation */
    .bird-icon {
        font-size: 120px;
        background: linear-gradient(135deg, var(--orange), var(--gold));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        margin-bottom: 30px;
        display: inline-block;
        animation: bounce 2s infinite ease-in-out;
    }

    @keyframes bounce {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-20px); }
    }

    /* Main Heading */
    .main-heading {
        font-size: 56px;
        font-weight: 900;
        background: linear-gradient(135deg, var(--navy), var(--primary));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        margin-bottom: 20px;
        letter-spacing: -1px;
        line-height: 1.2;
    }

    /* Sub Heading */
    .sub-heading {
        font-size: 24px;
        color: #64748b;
        font-weight: 600;
        margin-bottom: 15px;
    }

    /* Description */
    .description {
        font-size: 18px;
        color: #475569;
        line-height: 1.8;
        margin-bottom: 40px;
        max-width: 700px;
        margin-left: auto;
        margin-right: auto;
    }

    /* New Badge */
    .new-badge {
        display: inline-block;
        background: linear-gradient(135deg, #ff6b35, #f7931e);
        color: white;
        padding: 12px 30px;
        border-radius: 50px;
        font-weight: 800;
        font-size: 16px;
        text-transform: uppercase;
        letter-spacing: 2px;
        box-shadow: 0 10px 30px rgba(255, 107, 53, 0.4);
        animation: pulse 2s infinite;
        margin-bottom: 40px;
    }

    @keyframes pulse {
        0%, 100% { transform: scale(1); box-shadow: 0 10px 30px rgba(255, 107, 53, 0.4); }
        50% { transform: scale(1.05); box-shadow: 0 15px 40px rgba(255, 107, 53, 0.6); }
    }

    /* Feature Icons Grid */
    .features-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 30px;
        margin-top: 50px;
        padding-top: 50px;
        border-top: 2px dashed #e2e8f0;
    }

    .feature-item {
        padding: 25px;
        background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
        border-radius: 25px;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }

    .feature-item:hover {
        transform: translateY(-10px) scale(1.05);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
    }

    .feature-icon {
        font-size: 48px;
        margin-bottom: 15px;
        background: linear-gradient(135deg, var(--orange), var(--gold));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .feature-title {
        font-size: 18px;
        font-weight: 700;
        color: var(--navy);
        margin-bottom: 8px;
    }

    .feature-desc {
        font-size: 14px;
        color: #64748b;
        line-height: 1.6;
    }

    /* Countdown Timer */
    .countdown-timer {
        display: flex;
        justify-content: center;
        gap: 20px;
        margin: 40px 0;
    }

    .countdown-item {
        background: linear-gradient(135deg, var(--navy), var(--primary));
        color: white;
        padding: 20px 30px;
        border-radius: 20px;
        min-width: 100px;
        box-shadow: 0 10px 30px rgba(15, 28, 63, 0.3);
    }

    .countdown-number {
        font-size: 42px;
        font-weight: 900;
        display: block;
        line-height: 1;
        margin-bottom: 8px;
    }

    .countdown-label {
        font-size: 12px;
        text-transform: uppercase;
        font-weight: 700;
        letter-spacing: 1px;
        opacity: 0.9;
    }

    /* Social Connect */
    .social-connect {
        margin-top: 40px;
    }

    .social-title {
        font-size: 16px;
        font-weight: 700;
        color: #64748b;
        margin-bottom: 20px;
    }

    .social-icons {
        display: flex;
        justify-content: center;
        gap: 20px;
    }

    .social-icon {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        color: white;
        transition: all 0.3s ease;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
    }

    .social-icon:hover {
        transform: translateY(-5px) scale(1.1);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
    }

    .social-icon.facebook { background: linear-gradient(135deg, #3b5998, #2d4373); }
    .social-icon.twitter { background: linear-gradient(135deg, #1da1f2, #0c85d0); }
    .social-icon.instagram { background: linear-gradient(135deg, #e1306c, #c13584); }
    .social-icon.youtube { background: linear-gradient(135deg, #ff0000, #cc0000); }

    /* Responsive */
    @media (max-width: 768px) {
        .content-card { padding: 50px 30px; border-radius: 35px; }
        .main-heading { font-size: 36px; }
        .sub-heading { font-size: 18px; }
        .description { font-size: 16px; }
        .bird-icon { font-size: 80px; }
        .countdown-timer { flex-wrap: wrap; }
        .features-grid { grid-template-columns: 1fr; }
    }
</style>

<!-- Animated Background Particles -->
<div class="particles" id="particles"></div>

<div class="coming-soon-container animate__animated animate__fadeIn">
    <div class="content-card animate__animated animate__zoomIn animate__delay-1s">
        
        <!-- Bird Icon -->
        <div class="bird-icon">
            <i class="fas fa-dove"></i>
        </div>

        <!-- New Badge -->
        <div class="new-badge">
            <i class="fas fa-star"></i> Launching Soon
        </div>

        <!-- Main Heading -->
        <h1 class="main-heading">
            Bird Products Are Taking Flight!
        </h1>

        <!-- Sub Heading -->
        <h2 class="sub-heading">
            🐦 Premium Collection Coming Soon
        </h2>

        <!-- Description -->
        <p class="description">
            We're preparing an extraordinary collection of bird products for your feathered friends! 
            From premium bird food and nutritious treats to spacious cages, engaging toys, and essential health supplies – 
            we're curating the finest selection to keep your birds happy, healthy, and chirping with joy.
        </p>

        <!-- Countdown Timer -->
        <div class="countdown-timer">
            <div class="countdown-item animate__animated animate__fadeInUp animate__delay-2s">
                <span class="countdown-number" id="days">60</span>
                <span class="countdown-label">Days</span>
            </div>
            <div class="countdown-item animate__animated animate__fadeInUp animate__delay-2s">
                <span class="countdown-number" id="hours">08</span>
                <span class="countdown-label">Hours</span>
            </div>
            <div class="countdown-item animate__animated animate__fadeInUp animate__delay-2s">
                <span class="countdown-number" id="minutes">45</span>
                <span class="countdown-label">Minutes</span>
            </div>
            <div class="countdown-item animate__animated animate__fadeInUp animate__delay-2s">
                <span class="countdown-number" id="seconds">30</span>
                <span class="countdown-label">Seconds</span>
            </div>
        </div>

        <!-- Features Grid -->
        <div class="features-grid">
            <div class="feature-item animate__animated animate__fadeInUp animate__delay-3s">
                <div class="feature-icon"><i class="fas fa-seedling"></i></div>
                <div class="feature-title">Premium Food</div>
                <div class="feature-desc">Nutritious seeds, pellets & treats for all bird species</div>
            </div>
            
            <div class="feature-item animate__animated animate__fadeInUp animate__delay-3s">
                <div class="feature-icon"><i class="fas fa-home"></i></div>
                <div class="feature-title">Spacious Cages</div>
                <div class="feature-desc">Comfortable & secure homes for your feathered companions</div>
            </div>
            
            <div class="feature-item animate__animated animate__fadeInUp animate__delay-3s">
                <div class="feature-icon"><i class="fas fa-gamepad"></i></div>
                <div class="feature-title">Fun Toys</div>
                <div class="feature-desc">Engaging toys to keep your birds active and entertained</div>
            </div>
            
            <div class="feature-item animate__animated animate__fadeInUp animate__delay-3s">
                <div class="feature-icon"><i class="fas fa-heartbeat"></i></div>
                <div class="feature-title">Health Care</div>
                <div class="feature-desc">Vitamins, supplements & grooming essentials</div>
            </div>
        </div>

        <!-- Social Connect -->
        <div class="social-connect animate__animated animate__fadeInUp animate__delay-4s">
            <div class="social-title">Stay Connected & Get Notified First!</div>
            <div class="social-icons">
                <a href="#" class="social-icon facebook"><i class="fab fa-facebook-f"></i></a>
                <a href="#" class="social-icon twitter"><i class="fab fa-twitter"></i></a>
                <a href="#" class="social-icon instagram"><i class="fab fa-instagram"></i></a>
                <a href="#" class="social-icon youtube"><i class="fab fa-youtube"></i></a>
            </div>
        </div>

    </div>
</div>

<script>
// Create floating particles
function createParticles() {
    const particlesContainer = document.getElementById('particles');
    const particleCount = 30;
    
    for (let i = 0; i < particleCount; i++) {
        const particle = document.createElement('div');
        particle.className = 'particle';
        
        // Random size between 5px and 15px
        const size = Math.random() * 10 + 5;
        particle.style.width = size + 'px';
        particle.style.height = size + 'px';
        
        // Random horizontal position
        particle.style.left = Math.random() * 100 + '%';
        
        // Random animation delay
        particle.style.animationDelay = Math.random() * 15 + 's';
        
        // Random animation duration
        particle.style.animationDuration = (Math.random() * 10 + 10) + 's';
        
        particlesContainer.appendChild(particle);
    }
}

// Countdown Timer Logic
function updateCountdown() {
    // Set launch date (15 days from now)
    const launchDate = new Date();
    launchDate.setDate(launchDate.getDate() + 60);
    launchDate.setHours(0, 0, 0, 0);
    
    function update() {
        const now = new Date().getTime();
        const distance = launchDate - now;
        
        if (distance < 0) {
            document.getElementById('days').textContent = '00';
            document.getElementById('hours').textContent = '00';
            document.getElementById('minutes').textContent = '00';
            document.getElementById('seconds').textContent = '00';
            return;
        }
        
        const days = Math.floor(distance / (1000 * 60 * 60 * 24));
        const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((distance % (1000 * 60)) / 1000);
        
        document.getElementById('days').textContent = String(days).padStart(2, '0');
        document.getElementById('hours').textContent = String(hours).padStart(2, '0');
        document.getElementById('minutes').textContent = String(minutes).padStart(2, '0');
        document.getElementById('seconds').textContent = String(seconds).padStart(2, '0');
    }
    
    update();
    setInterval(update, 1000);
}

// Initialize on page load
window.addEventListener('DOMContentLoaded', () => {
    createParticles();
    updateCountdown();
});

// Add smooth scroll behavior
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    });
});
</script>

<?php include "includes/footer.php"; ?>
