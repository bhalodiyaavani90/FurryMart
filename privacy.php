<?php 
include "includes/header.php"; 
?>

<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

<style>
    :root {
        --fm-navy: #0f1c3f;
        --fm-teal: #518992;
        --fm-accent: #f0fdfa;
        --fm-white: #ffffff;
        --fm-text: #1e293b;
        --fm-muted: #64748b;
        --fm-border: #eef2f6;
        --fm-shadow: 0 15px 40px rgba(15, 28, 63, 0.06);
    }

    body { background-color: #f8fafc; color: var(--fm-text); font-family: 'Plus Jakarta Sans', sans-serif; scroll-behavior: smooth; }

    /* --- COMPACT PROFESSIONAL HERO --- */
    .p-hero {
        background: linear-gradient(135deg, var(--fm-navy) 0%, #1a3a5f 100%);
        padding: 100px 8% 180px;
        text-align: center;
        color: white;
        position: relative;
        clip-path: ellipse(150% 100% at 50% 0%);
    }
    .p-hero h1 { font-size: 42px; font-weight: 800; letter-spacing: -1.5px; margin-bottom: 15px; }
    .p-hero p { font-size: 16px; opacity: 0.8; max-width: 700px; margin: 0 auto; line-height: 1.6; }

    /* --- MASTER LAYOUT GRID --- */
    .p-master-wrapper {
        max-width: 1450px;
        margin: -100px auto 100px;
        display: grid;
        grid-template-columns: 320px 1fr; /* Same old side content list */
        gap: 40px;
        padding: 0 5%;
        position: relative;
        z-index: 10;
    }

    /* --- STICKY SIDEBAR (GLASSMORPHISM) --- */
    .p-sticky-sidebar {
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(20px);
        border: 1px solid var(--fm-white);
        border-radius: 30px;
        padding: 40px;
        position: sticky;
        top: 100px;
        height: fit-content;
        box-shadow: var(--fm-shadow);
    }
    .nav-title { font-size: 11px; font-weight: 800; color: var(--fm-teal); text-transform: uppercase; letter-spacing: 2px; margin-bottom: 30px; }
    .p-nav-link {
        display: flex; align-items: center; gap: 12px; padding: 14px 20px;
        text-decoration: none; color: #64748b; font-size: 14px; font-weight: 700; border-radius: 15px;
        transition: 0.3s ease; margin-bottom: 8px;
    }
    .p-nav-link i { font-size: 16px; width: 25px; text-align: center; }
    .p-nav-link:hover { color: var(--fm-teal); background: var(--fm-accent); transform: translateX(8px); }
    .p-nav-link.active { background: var(--fm-navy); color: white; box-shadow: 0 10px 20px rgba(15, 28, 63, 0.15); }

    /* --- DETAILED CONTENT CARDS --- */
    .p-section-card {
        background: var(--fm-white);
        border-radius: 40px;
        margin-bottom: 40px;
        border: 1px solid #eef2f6;
        box-shadow: var(--fm-shadow);
        overflow: hidden;
        opacity: 0;
        transform: translateY(30px);
        transition: all 0.8s cubic-bezier(0.2, 0.8, 0.2, 1);
    }
    .p-section-card.reveal { opacity: 1; transform: translateY(0); }

    .p-flex-grid { display: flex; align-items: stretch; }
    .p-flex-grid.reverse { flex-direction: row-reverse; }
    
    .p-visual-box { flex: 0 0 280px; background: #f1f5f9; position: relative; overflow: hidden; }
    .p-visual-box img { width: 100%; height: 100%; object-fit: cover; transition: 0.6s; }
    .p-section-card:hover .p-visual-box img { transform: scale(1.05); }

    .p-text-box { padding: 50px; flex: 1; }
    .p-badge {
        display: inline-block; padding: 5px 12px; background: #f0fdfa;
        color: var(--fm-teal); border-radius: 50px; font-size: 10px; font-weight: 800;
        text-transform: uppercase; margin-bottom: 20px; border: 1px solid #ccfbf1;
    }

    /* MEDIUM Heading Size */
    .p-text-box h2 { font-size: 28px; font-weight: 800; color: var(--fm-navy); margin-bottom: 15px; letter-spacing: -0.5px; }
    
    /* SMALL Content Font Size */
    .p-text-box p, .p-text-box li { font-size: 14px; line-height: 1.8; color: #475569; margin-bottom: 15px; }

    /* Interactive Data Hub */
    .p-data-strip {
        background: #f8fafc; padding: 15px 20px; border-radius: 15px;
        margin-bottom: 10px; display: flex; align-items: center; gap: 15px;
        border: 1px solid var(--fm-border); transition: 0.3s;
    }
    .p-data-strip:hover { background: white; border-color: var(--fm-teal); transform: scale(1.02); }
    .p-data-strip i { font-size: 16px; color: var(--fm-teal); }
    .p-data-strip b { font-size: 13px; color: var(--fm-navy); }

    @media (max-width: 1200px) {
        .p-master-wrapper { grid-template-columns: 1fr; }
        .p-sticky-sidebar { display: none; }
        .p-flex-grid { flex-direction: column !important; }
        .p-visual-box { height: 200px; flex: none; width: 100%; }
        .p-text-box { padding: 30px; }
    }
</style>

<main>
    <section class="p-hero">
        <div class="footer-animate">
            <h1>Privacy & Trust Center</h1>
            <p>At FurryMart, we guard your personal data with clinical precision. Discover how we protect the Furry Family through enterprise-grade transparency.</p>
        </div>
    </section>

    <div class="p-master-wrapper">
        <aside class="p-sticky-sidebar">
            <div class="nav-title">Policy Hub</div>
            <nav id="p-spy">
                <a href="#intro" class="p-nav-link active"><i class="fas fa-handshake"></i> Foundation</a>
                <a href="#data" class="p-nav-link"><i class="fas fa-database"></i> Collection</a>
                <a href="#usage" class="p-nav-link"><i class="fas fa-bolt"></i> Utilization</a>
                <a href="#safety" class="p-nav-link"><i class="fas fa-shield-halved"></i> Data Armor</a>
                <a href="#help" class="p-nav-link"><i class="fas fa-headset"></i> Support</a>
            </nav>
        </aside>

        <div class="p-content-body">
            
            <div class="p-section-card scroll-reveal" id="intro">
                <div class="p-flex-grid">
                    <div class="p-visual-box">
                        <img src="uploads/Foundation.jpg" alt="FurryMart Core">
                    </div>
                    <div class="p-text-box">
                        <span class="p-badge">Official Commitment</span>
                        <h2>FurryMart Foundation</h2>
                        <p>This Privacy Policy serves as the definitive legal framework for how <strong>FurryMart Pet Care Global</strong> ("FurryMart", "We", "Us") secures data across our digital and retail landscape.</p>
                        <p>By interacting with our ecosystem, you entrust us with critical pet health data. We handle this responsibility with 100% transparency and professional integrity.</p>
                    </div>
                </div>
            </div>

            <div class="p-section-card scroll-reveal" id="data">
                <div class="p-flex-grid reverse">
                    <div class="p-visual-box">
                        <img src="uploads/Intelligence Gathering.jpg" alt="Pet Data Hub">
                    </div>
                    <div class="p-text-box">
                        <span class="p-badge">Transparency</span>
                        <h2>Intelligence Gathering</h2>
                        <p>We only collect data required to provide a personalized, high-precision pet care journey for your furry companions:</p>
                        
                        <div class="p-data-strip">
                            <i class="fas fa-user-circle"></i>
                            <div><b>Profile Identity:</b> <span>Verified names and contact points for secure sessions.</span></div>
                        </div>
                        <div class="p-data-strip">
                            <i class="fas fa-paw"></i>
                            <div><b>Pet History:</b> <span>Clinical records required for high-accuracy Vetcare.</span></div>
                        </div>
                        <div class="p-data-strip">
                            <i class="fas fa-location-arrow"></i>
                            <div><b>Logistics Intel:</b> <span>Verified addresses for nationwide delivery efficiency.</span></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="p-section-card scroll-reveal" id="usage">
                <div class="p-flex-grid">
                    <div class="p-visual-box">
                        <img src="uploads/Purpose-Driven Usage.jpg" alt="Data Precision">
                    </div>
                    <div class="p-text-box">
                        <span class="p-badge">Service Optimization</span>
                        <h2>Purpose-Driven Usage</h2>
                        <p>At FurryMart, data is transformed into better pet health outcomes and logistics speed:</p>
                        <ul style="padding-left: 20px;">
                            <li><strong>Smart Dispatch:</strong> Routing essentials to the center closest to you for speed.</li>
                            <li><strong>Clinical History:</strong> Providing medical staff with accurate pet history during consultations.</li>
                            <li><strong>Personalization:</strong> Recommending nutrition based on breed and age.</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="p-section-card scroll-reveal" id="safety">
                <div class="p-flex-grid reverse">
                    <div class="p-visual-box">
                        <img src="uploads/Advanced Data Armor.jpg" alt="Data Shield">
                    </div>
                    <div class="p-text-box">
                        <span class="p-badge">Ironclad Armor</span>
                        <h2>Advanced Data Armor</h2>
                        <p>We employ enterprise-grade 256-bit SSL secure tunneling to ensure your records are never exposed to unauthorized entities.</p>
                        <div style="background: var(--fm-navy); padding: 25px; border-radius: 20px; color: white;">
                            <b style="color: var(--fm-teal); font-size: 14px; display: block; margin-bottom: 8px;"><i class="fas fa-user-shield"></i> Zero-Knowledge Policy</b>
                            <p style="font-size: 12px; opacity: 0.8; margin: 0; line-height: 1.6;">Access to pet medical history is strictly limited to authorized FurryMart medical staff during scheduled vet sessions only.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="p-section-card scroll-reveal" id="help">
                <div class="p-text-box" style="text-align: center; padding: 60px 40px;">
                    <span class="p-badge">Human Assistance</span>
                    <h2>Help with Your Privacy</h2>
                    <p>Our Data Protection Officer is available to assist you with any inquiries or data access requests.</p>
                    <div style="display: flex; justify-content: center; gap: 15px; margin-top: 30px;">
                        <a href="contact.php" style="background: var(--fm-navy); color: white; padding: 14px 35px; border-radius: 50px; text-decoration: none; font-size: 14px; font-weight: 800;">EMAIL OFFICER</a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</main>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // High-Precision Reveal Logic
        const observer = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('reveal');
                }
            });
        }, { threshold: 0.1 });

        document.querySelectorAll('.scroll-reveal').forEach(el => observer.observe(el));

        // Interactive Sidebar Hub
        const navLinks = document.querySelectorAll('.p-nav-link');
        const cards = document.querySelectorAll('.p-section-card');

        window.addEventListener('scroll', () => {
            let current = "";
            cards.forEach(card => {
                const cardTop = card.offsetTop;
                if (pageYOffset >= cardTop - 250) {
                    current = card.getAttribute('id');
                }
            });

            navLinks.forEach(link => {
                link.classList.remove('active');
                if (link.getAttribute('href').includes(current)) {
                    link.classList.add('active');
                }
            });
        });
    });
</script>

<?php include "includes/footer.php"; ?>