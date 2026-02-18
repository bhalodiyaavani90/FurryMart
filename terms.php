<?php 
include "includes/header.php"; 
?>

<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

<style>
    :root {
        --fm-navy: #0f1c3f;
        --fm-teal: #518992;
        --fm-gold: #e6b034;
        --fm-rose: #db2777;
        --fm-bg: #f8fafc;
        --fm-white: #ffffff;
        --fm-shadow: 0 30px 60px rgba(15, 28, 63, 0.12);
    }

    body { background-color: var(--fm-bg); font-family: 'Plus Jakarta Sans', sans-serif; scroll-behavior: smooth; }

    /* --- CINEMATIC HERO --- */
    .premium-hero {
        background: linear-gradient(135deg, var(--fm-navy) 0%, #1a3a5f 100%);
        padding: 160px 8% 220px;
        text-align: center;
        color: white;
        position: relative;
        clip-path: ellipse(150% 100% at 50% 0%);
    }
    .premium-hero h1 { font-size: 64px; font-weight: 800; letter-spacing: -2px; margin-bottom: 20px; }
    .premium-hero p { font-size: 20px; opacity: 0.8; max-width: 800px; margin: 0 auto; line-height: 1.6; }

    /* --- LAYOUT GRID --- */
    .main-wrapper {
        max-width: 1400px;
        margin: -140px auto 100px;
        display: grid;
        grid-template-columns: 350px 1fr;
        gap: 50px;
        padding: 0 5%;
        position: relative;
        z-index: 10;
    }

    /* --- FLOATING GLASS NAVIGATION --- */
    .sticky-nav {
        background: rgba(255, 255, 255, 0.85);
        backdrop-filter: blur(25px);
        border: 1px solid var(--fm-white);
        border-radius: 40px;
        padding: 45px;
        position: sticky;
        top: 100px;
        height: fit-content;
        box-shadow: var(--fm-shadow);
    }
    .sticky-nav h4 { font-size: 12px; font-weight: 800; color: var(--fm-teal); text-transform: uppercase; letter-spacing: 2.5px; margin-bottom: 35px; }
    
    .nav-item {
        display: flex; align-items: center; gap: 15px; padding: 16px 22px;
        text-decoration: none; color: #64748b; font-weight: 700; border-radius: 20px;
        transition: 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); margin-bottom: 10px;
    }
    .nav-item i { font-size: 18px; width: 30px; text-align: center; transition: 0.3s; }
    .nav-item:hover { color: var(--fm-teal); background: #f0fdfa; transform: translateX(10px); }
    .nav-item.active { background: var(--fm-navy); color: white; box-shadow: 0 15px 30px rgba(15, 28, 63, 0.2); }

    /* --- CONTENT CARDS --- */
    .legal-card {
        background: var(--fm-white);
        padding: 80px;
        border-radius: 50px;
        margin-bottom: 50px;
        border: 1px solid #eef2f6;
        box-shadow: var(--fm-shadow);
        opacity: 0;
        transform: translateY(60px);
        transition: all 1.2s cubic-bezier(0.2, 0.8, 0.2, 1);
    }
    .legal-card.reveal { opacity: 1; transform: translateY(0); }

    /* Colorful Dynamic Icons */
    .icon-box {
        width: 70px; height: 70px; border-radius: 22px;
        display: flex; align-items: center; justify-content: center;
        font-size: 28px; margin-bottom: 35px;
        transition: 0.5s;
    }
    .b-blue { background: #eff6ff; color: #3b82f6; box-shadow: 0 10px 20px rgba(59, 130, 246, 0.1); }
    .b-teal { background: #f0fdfa; color: #518992; box-shadow: 0 10px 20px rgba(81, 137, 146, 0.1); }
    .b-pink { background: #fdf2f8; color: #db2777; box-shadow: 0 10px 20px rgba(219, 39, 119, 0.1); }
    .b-gold { background: #fffbeb; color: #d97706; box-shadow: 0 10px 20px rgba(217, 119, 6, 0.1); }

    .legal-card h2 { font-size: 38px; font-weight: 800; color: var(--fm-navy); margin-bottom: 25px; letter-spacing: -1px; }
    .legal-card p { font-size: 18px; line-height: 2; color: #475569; margin-bottom: 25px; }
    
    /* Interactive List Items */
    .legal-list { list-style: none; padding: 0; }
    .legal-list li {
        padding: 20px 25px; background: #f8fafc; border-radius: 20px;
        margin-bottom: 15px; display: flex; align-items: center; gap: 15px;
        border: 1px solid #f1f5f9; transition: 0.3s; font-weight: 600;
    }
    .legal-list li:hover { background: #ffffff; border-color: var(--fm-teal); transform: scale(1.02); }
    .legal-list i { color: var(--fm-teal); font-size: 18px; }

    /* Highlighted Compliance Area */
    .critical-notice {
        background: #1e293b; color: #f8fafc; padding: 40px; border-radius: 30px;
        display: flex; gap: 25px; margin-top: 40px; position: relative; overflow: hidden;
    }
    .critical-notice::after { content: '\f071'; font-family: 'Font Awesome 6 Free'; font-weight: 900; position: absolute; right: -20px; bottom: -20px; font-size: 100px; opacity: 0.05; }
    .critical-notice i { font-size: 30px; color: var(--fm-gold); }
    .critical-notice h5 { font-size: 18px; margin-bottom: 10px; color: white; font-weight: 800; }

    @media (max-width: 1100px) {
        .main-wrapper { grid-template-columns: 1fr; }
        .sticky-nav { display: none; }
        .legal-card { padding: 40px; }
    }
</style>

<main>
    <section class="premium-hero">
        <div class="footer-animate">
            <h1>Terms of Service</h1>
            <p>Transparency, security, and exceptional care. These terms define our commitment to you and your pet's journey with FurryMart.</p>
        </div>
    </section>

    <div class="main-wrapper">
        <aside class="sticky-nav">
            <h4>Navigational Guide</h4>
            <nav id="scroll-nav">
                <a href="#acc" class="nav-item active"><i class="fas fa-user-shield"></i> Account Authority</a>
                <a href="#plat" class="nav-item"><i class="fas fa-paw"></i> Platform Use</a>
                <a href="#intel" class="nav-item"><i class="fas fa-fingerprint"></i> Intellectual Rights</a>
                <a href="#trans" class="nav-item"><i class="fas fa-credit-card"></i> Transactions</a>
                <a href="#gov" class="nav-item"><i class="fas fa-gavel"></i> Governance</a>
            </nav>
        </aside>

        <div class="content-body">
            
            <div class="legal-card scroll-reveal" id="acc">
                <div class="icon-box b-blue"><i class="fas fa-id-badge"></i></div>
                <h2>Registration & Account Responsibility</h2>
                <p>To access the full suite of <strong>FurryMart</strong> services, including Vetcare and Grooming, users must maintain a secure and verified account profile.</p>
                <ul class="legal-list">
                    <li><i class="fas fa-check-circle"></i> Accurate and current information is mandatory for pet safety.</li>
                    <li><i class="fas fa-check-circle"></i> Users are responsible for all activity occurring under their credentials.</li>
                    <li><i class="fas fa-check-circle"></i> Multiple account ownership for promotional abuse is strictly prohibited.</li>
                </ul>
            </div>

            <div class="legal-card scroll-reveal" id="plat">
                <div class="icon-box b-teal"><i class="fas fa-shield-cat"></i></div>
                <h2>Platform Usage & Conduct</h2>
                <p>FurryMart grants you a limited, non-exclusive license to use our platform for personal pet-care needs. Commercial data extraction or unauthorized scraping is a violation of these terms.</p>
                
                <div class="critical-notice">
                    <i class="fas fa-triangle-exclamation"></i>
                    <div>
                        <h5>Prohibited Activity</h5>
                        <p style="font-size: 15px; opacity: 0.9;">Any attempt to compromise the security of other users or provide fraudulent pet health information will result in immediate permanent suspension.</p>
                    </div>
                </div>
            </div>

            <div class="legal-card scroll-reveal" id="intel">
                <div class="icon-box b-pink"><i class="fas fa-medal"></i></div>
                <h2>Intellectual Property Rights</h2>
                <p>All software, brand logos, imagery, and veterinary content on this platform are the exclusive property of <strong>FurryMart Pet Care Global</strong>.</p>
                <p>Reproduction or redistribution of any platform materials without written consent is strictly prohibited and protected under international copyright law.</p>
            </div>

            <div class="legal-card scroll-reveal" id="trans">
                <div class="icon-box b-gold"><i class="fas fa-wallet"></i></div>
                <h2>Payments & Refund Governance</h2>
                <p>Our transaction layer is secured with enterprise-grade PCI-DSS encryption to ensure your financial safety.</p>
                <ul class="legal-list">
                    <li><i class="fas fa-circle-info"></i> All listed prices are inclusive of GST unless noted otherwise.</li>
                    <li><i class="fas fa-circle-info"></i> Refunds for pet supplies are governed by our standard Return Policy.</li>
                    <li><i class="fas fa-circle-info"></i> Vetcare appointments cancelled less than 2 hours prior may incur fees.</li>
                </ul>
            </div>

            <div class="legal-card scroll-reveal" id="gov">
                <div class="icon-box b-blue"><i class="fas fa-scale-balanced"></i></div>
                <h2>Liability & Indemnification</h2>
                <p>While we strive for excellence in our veterinary advice and products, <strong>FurryMart</strong> provides services on an "as-is" basis without warranties of any kind.</p>
                <p>We shall not be liable for any indirect or consequential damages arising from the use of our pet-care ecosystem.</p>
            </div>

        </div>
    </div>
</main>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Observer for Staggered Reveal
        const observer = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('reveal');
                }
            });
        }, { threshold: 0.1 });

        document.querySelectorAll('.scroll-reveal').forEach(el => observer.observe(el));

        // Smooth Scroll & Active State Tracker
        const navLinks = document.querySelectorAll('.nav-item');
        const sections = document.querySelectorAll('.legal-card');

        window.addEventListener('scroll', () => {
            let current = "";
            sections.forEach(section => {
                const sectionTop = section.offsetTop;
                if (pageYOffset >= sectionTop - 250) {
                    current = section.getAttribute('id');
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