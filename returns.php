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

    /* --- COMPACT PROFESSIONAL HERO (MATCHES PRIVACY.PHP) --- */
    .r-hero {
        background: linear-gradient(135deg, var(--fm-navy) 0%, #1a3a5f 100%);
        padding: 100px 8% 180px; /* Precise high-end padding */
        text-align: center;
        color: white;
        position: relative;
        clip-path: ellipse(150% 100% at 50% 0%);
    }
    .r-hero h1 { font-size: 42px; font-weight: 800; letter-spacing: -1.5px; margin-bottom: 15px; }
    .r-hero p { font-size: 16px; opacity: 0.8; max-width: 700px; margin: 0 auto; line-height: 1.6; }

    /* --- MASTER LAYOUT GRID --- */
    .r-master-wrapper {
        max-width: 1450px;
        margin: -100px auto 100px;
        display: grid;
        grid-template-columns: 320px 1fr; /* Traditional sidebar layout */
        gap: 40px;
        padding: 0 5%;
        position: relative;
        z-index: 10;
    }

    /* --- STICKY SIDEBAR (GLASSMORPHISM) --- */
    .r-sticky-sidebar {
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
    .r-nav-link {
        display: flex; align-items: center; gap: 12px; padding: 14px 20px;
        text-decoration: none; color: #64748b; font-size: 14px; font-weight: 700; border-radius: 15px;
        transition: 0.3s ease; margin-bottom: 8px;
    }
    .r-nav-link i { font-size: 16px; width: 25px; text-align: center; }
    .r-nav-link:hover { color: var(--fm-teal); background: var(--fm-accent); transform: translateX(8px); }
    .r-nav-link.active { background: var(--fm-navy); color: white; box-shadow: 0 10px 20px rgba(15, 28, 63, 0.15); }

    /* --- DETAILED ALTERNATING CARDS --- */
    .r-section-card {
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
    .r-section-card.reveal { opacity: 1; transform: translateY(0); }

    .r-flex-grid { display: flex; align-items: stretch; }
    .r-flex-grid.reverse { flex-direction: row-reverse; }
    
    .r-visual-box { flex: 0 0 280px; background: #f1f5f9; position: relative; overflow: hidden; }
    .r-visual-box img { width: 100%; height: 100%; object-fit: cover; transition: 0.6s; }
    .r-section-card:hover .r-visual-box img { transform: scale(1.05); }

    .r-text-box { padding: 50px; flex: 1; }
    .r-badge {
        display: inline-block; padding: 5px 12px; background: #f0fdfa;
        color: var(--fm-teal); border-radius: 50px; font-size: 10px; font-weight: 800;
        text-transform: uppercase; margin-bottom: 20px; border: 1px solid #ccfbf1;
    }

    /* MEDIUM Heading Size */
    .r-text-box h2 { font-size: 28px; font-weight: 800; color: var(--fm-navy); margin-bottom: 15px; letter-spacing: -0.5px; }
    
    /* SMALL Content Font Size */
    .r-text-box p, .r-text-box li { font-size: 14px; line-height: 1.8; color: #475569; margin-bottom: 15px; }

    /* Interactive Data Strips */
    .r-data-strip {
        background: #f8fafc; padding: 15px 20px; border-radius: 15px;
        margin-bottom: 10px; display: flex; align-items: center; gap: 15px;
        border: 1px solid var(--fm-border); transition: 0.3s;
    }
    .r-data-strip:hover { background: white; border-color: var(--fm-teal); transform: scale(1.02); }
    .r-data-strip i { font-size: 16px; color: var(--fm-teal); }
    .r-data-strip b { font-size: 13px; color: var(--fm-navy); }

    @media (max-width: 1200px) {
        .r-master-wrapper { grid-template-columns: 1fr; }
        .r-sticky-sidebar { display: none; }
        .r-flex-grid { flex-direction: column !important; }
        .r-visual-box { height: 200px; flex: none; width: 100%; }
        .r-text-box { padding: 30px; }
    }
</style>

<main>
    <section class="r-hero">
        <div class="footer-animate">
            <h1>Returns & Refund Policy</h1>
            <p>At FurryMart, we want your shopping experience to be as joyful as a pet's wagging tail. Our transparent returns process ensures peace of mind for every pet parent.</p>
        </div>
    </section>

    <div class="r-master-wrapper">
        <aside class="r-sticky-sidebar">
            <div class="nav-title">Quick Navigation</div>
            <nav id="r-spy">
                <a href="#eligibility" class="r-nav-link active"><i class="fas fa-calendar-check"></i> Eligibility</a>
                <a href="#non-returnable" class="r-nav-link"><i class="fas fa-ban"></i> Exceptions</a>
                <a href="#process" class="r-nav-link"><i class="fas fa-rotate"></i> Refund Process</a>
                <a href="#cancellation" class="r-nav-link"><i class="fas fa-rectangle-xmark"></i> Cancellations</a>
                <a href="#help" class="r-nav-link"><i class="fas fa-headset"></i> Support</a>
            </nav>
        </aside>

        <div class="r-content-body">
            
            <div class="r-section-card scroll-reveal" id="eligibility">
                <div class="r-flex-grid">
                    <div class="r-visual-box">
                        <img src="uploads/Return Eligibility.jpg" alt="FurryMart Returns">
                    </div>
                    <div class="r-text-box">
                        <span class="r-badge">7-Day Guarantee</span>
                        <h2>Return Eligibility</h2>
                        <p>FurryMart offers a detailed return policy for items that arrive damaged, defective, or incorrect.</p>
                        <ul style="padding-left: 20px;">
                            <li>Returns must be requested within 7 days of delivery.</li>
                            <li>Items must be in original packaging with tags intact.</li>
                            <li>Proof of purchase (invoice) is mandatory for all requests.</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="r-section-card scroll-reveal" id="non-returnable">
                <div class="r-flex-grid reverse">
                    <div class="r-visual-box">
                        <img src="uploads/Non-Returnable Items.jpg" alt="Hygiene Control">
                    </div>
                    <div class="r-text-box">
                        <span class="r-badge">Safety First</span>
                        <h2>Non-Returnable Items</h2>
                        <p>To maintain clinical hygiene standards for all Furry Family members, certain items cannot be returned:</p>
                        
                        <div class="r-data-strip">
                            <i class="fas fa-pills"></i>
                            <div><b>Medicines:</b> <span>Temperature-sensitive healthcare products.</span></div>
                        </div>
                        <div class="r-data-strip">
                            <i class="fas fa-bowl-food"></i>
                            <div><b>Opened Food:</b> <span>Any consumable with a broken seal.</span></div>
                        </div>
                        <div class="r-data-strip">
                            <i class="fas fa-soap"></i>
                            <div><b>Hygiene Products:</b> <span>Grooming tools and used pet accessories.</span></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="r-section-card scroll-reveal" id="process">
                <div class="r-flex-grid">
                    <div class="r-visual-box">
                        <img src="uploads/Refund.jpg" alt="Verification">
                    </div>
                    <div class="r-text-box">
                        <span class="r-badge">Transaction Transparency</span>
                        <h2>Refund Timeline</h2>
                        <p>Once your return is verified by our quality control hub, refunds are initiated realistically:</p>
                        <ul style="padding-left: 20px;">
                            <li><strong>Inspection:</strong> verification takes 24-48 hours.</li>
                            <li><strong>Initiation:</strong> approved refunds are processed within 7-10 business days.</li>
                            <li><strong>Method:</strong> refunds are credited back to the original payment source.</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="r-section-card scroll-reveal" id="cancellation">
                <div class="r-flex-grid reverse">
                    <div class="r-visual-box">
                        <img src="uploads/cancellation policy sign.jpg" alt="Security Shield">
                    </div>
                    <div class="r-text-box">
                        <span class="r-badge">Order Governance</span>
                        <h2>Cancellation Policy</h2>
                        <p>At FurryMart, you can cancel your order realistically until it enters the "Shipped" phase.</p>
                        <div style="background: var(--fm-navy); padding: 25px; border-radius: 20px; color: white;">
                            <b style="color: var(--fm-teal); font-size: 14px; display: block; margin-bottom: 8px;"><i class="fas fa-truck-fast"></i> In-Transit Clause</b>
                            <p style="font-size: 12px; opacity: 0.8; margin: 0; line-height: 1.6;">Once an order is handed over to our logistics partner, cancellations are not possible; however, you may refuse delivery for a full refund.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="r-section-card scroll-reveal" id="help">
                <div class="r-text-box" style="text-align: center; padding: 60px 40px;">
                    <span class="r-badge">Care Hub</span>
                    <h2>Need Assistance?</h2>
                    <p>Our dedicated care team is available to help you with return status updates.</p>
                    <div style="display: flex; justify-content: center; gap: 15px; margin-top: 30px;">
                        <a href="contact.php" style="background: var(--fm-navy); color: white; padding: 14px 35px; border-radius: 50px; text-decoration: none; font-size: 14px; font-weight: 800;">RAISE A TICKET</a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</main>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // High-Precision Scroll Reveal
        const observer = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('reveal');
                }
            });
        }, { threshold: 0.1 });

        document.querySelectorAll('.scroll-reveal').forEach(el => observer.observe(el));

        // Interactive Navigation tracker
        const navLinks = document.querySelectorAll('.r-nav-link');
        const cards = document.querySelectorAll('.r-section-card');

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