<?php 
include "includes/header.php"; 
?>

<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

<style>
    :root {
        --fm-navy: #0f1c3f;
        --fm-teal: #518992;
        --fm-rose: #db2777;
        --fm-amber: #d97706;
        --fm-sky: #0284c7;
        --fm-bg: #f8fafc;
        --fm-white: #ffffff;
        --fm-border: #e2e8f0;
        --fm-shadow: 0 25px 60px rgba(15, 28, 63, 0.08);
    }

    body { background-color: var(--fm-bg); font-family: 'Plus Jakarta Sans', sans-serif; color: #1e293b; scroll-behavior: smooth; }

    /* --- PREMIUM CORPORATE HERO --- */
    .ship-hero {
        background: linear-gradient(135deg, var(--fm-navy) 0%, #1e3a8a 100%);
        padding: 140px 8% 200px;
        text-align: center;
        color: white;
        position: relative;
        clip-path: ellipse(150% 100% at 50% 0%);
    }
    .ship-tag {
        display: inline-block; padding: 8px 20px; background: rgba(255,255,255,0.1);
        border-radius: 50px; font-size: 13px; font-weight: 700; text-transform: uppercase;
        letter-spacing: 2px; margin-bottom: 25px; border: 1px solid rgba(255,255,255,0.2);
    }
    .ship-hero h1 { font-size: 62px; font-weight: 800; letter-spacing: -2px; margin-bottom: 20px; }
    .ship-hero p { font-size: 21px; opacity: 0.85; max-width: 850px; margin: 0 auto; line-height: 1.6; }

    /* --- DYNAMIC LAYOUT STRUCTURE --- */
    .ship-wrapper {
        max-width: 1450px; margin: -120px auto 100px;
        display: grid; grid-template-columns: 350px 1fr; gap: 50px;
        padding: 0 5%; position: relative; z-index: 10;
    }

    /* --- INTERACTIVE STICKY NAVIGATION --- */
    .ship-nav-card {
        background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(20px);
        border-radius: 40px; border: 1px solid var(--fm-white);
        padding: 45px; position: sticky; top: 100px; height: fit-content;
        box-shadow: var(--fm-shadow);
    }
    .ship-nav-card h4 { font-size: 14px; font-weight: 800; color: var(--fm-teal); text-transform: uppercase; margin-bottom: 30px; letter-spacing: 1px; }
    
    .ship-link {
        display: flex; align-items: center; gap: 15px; padding: 18px 22px;
        text-decoration: none; color: #64748b; font-weight: 700; border-radius: 20px;
        transition: 0.4s cubic-bezier(0.4, 0, 0.2, 1); margin-bottom: 8px;
    }
    .ship-link i { font-size: 18px; width: 30px; text-align: center; }
    .ship-link:hover { color: var(--fm-teal); background: #f0fdfa; transform: translateX(10px); }
    .ship-link.active { background: var(--fm-navy); color: white; box-shadow: 0 10px 25px rgba(15, 28, 63, 0.2); }

    /* --- ENTERPRISE CONTENT CARDS --- */
    .content-card {
        background: var(--fm-white); padding: 80px; border-radius: 60px;
        margin-bottom: 50px; border: 1px solid var(--fm-border); box-shadow: var(--fm-shadow);
        opacity: 0; transform: translateY(50px); transition: all 1s ease;
    }
    .content-card.reveal { opacity: 1; transform: translateY(0); }

    /* Colorful High-Definition Icons */
    .hd-icon {
        width: 75px; height: 75px; border-radius: 25px; display: flex;
        align-items: center; justify-content: center; font-size: 32px; margin-bottom: 40px;
        transition: 0.5s;
    }
    .hd-blue { background: #eff6ff; color: #3b82f6; border: 1px solid #dbeafe; }
    .hd-teal { background: #f0fdfa; color: #518992; border: 1px solid #ccfbf1; }
    .hd-rose { background: #fdf2f8; color: #db2777; border: 1px solid #fce7f3; }
    .hd-amber { background: #fffbeb; color: #d97706; border: 1px solid #fef3c7; }

    .content-card h2 { font-size: 40px; font-weight: 800; color: var(--fm-navy); margin-bottom: 30px; letter-spacing: -1px; }
    .content-card p, .content-card li { font-size: 18.5px; line-height: 2; color: #475569; margin-bottom: 15px; }

    /* Visual Delivery Grid */
    .delivery-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 25px; margin-top: 40px; }
    .delivery-box { 
        background: #f8fafc; padding: 35px; border-radius: 30px; border: 1px solid var(--fm-border);
        text-align: center; transition: 0.3s;
    }
    .delivery-box:hover { background: white; border-color: var(--fm-teal); transform: translateY(-10px); }
    .delivery-box b { font-size: 32px; color: var(--fm-navy); display: block; margin-bottom: 10px; }
    .delivery-box span { font-size: 15px; color: var(--fm-muted); font-weight: 600; text-transform: uppercase; letter-spacing: 1px; }

    /* Support Callout */
    .support-footer {
        background: #1e293b; color: white; padding: 45px; border-radius: 40px;
        display: flex; justify-content: space-between; align-items: center; margin-top: 60px;
        position: relative; overflow: hidden;
    }
    .support-footer::before { content: '\f1d8'; font-family: 'Font Awesome 6 Free'; font-weight: 900; position: absolute; right: -20px; bottom: -20px; font-size: 120px; opacity: 0.05; }
    .btn-action { background: var(--fm-teal); color: white; padding: 18px 40px; border-radius: 50px; font-weight: 700; text-decoration: none; transition: 0.3s; }
    .btn-action:hover { background: #3d6a71; transform: scale(1.05); }

    @media (max-width: 1200px) {
        .ship-wrapper { grid-template-columns: 1fr; }
        .ship-nav-card { display: none; }
        .delivery-grid { grid-template-columns: 1fr; }
    }
</style>

<main>
    <section class="ship-hero">
        <div class="ship-tag">Reliable Logistics</div>
        <h1>FurryMart Delivery Policy</h1>
        <p>Expertly handled. Carefully delivered. Our nationwide fulfillment network ensures your pet's essentials arrive with speed and absolute safety.</p>
    </section>

    <div class="ship-wrapper">
        <aside class="ship-nav-card">
            <h4>Logistics Hub</h4>
            <nav id="ship-nav">
                <a href="#rules" class="ship-link active"><i class="fas fa-file-contract"></i> Guidelines</a>
                <a href="#time" class="ship-link"><i class="fas fa-hourglass-start"></i> Timelines</a>
                <a href="#cost" class="ship-link"><i class="fas fa-receipt"></i> Charges</a>
                <a href="#tracking" class="ship-link"><i class="fas fa-map-marked-alt"></i> Fulfillment</a>
                <a href="#help" class="ship-link"><i class="fas fa-headset"></i> Support</a>
            </nav>
        </aside>

        <div class="ship-content">
            
            <div class="content-card scroll-reveal" id="rules">
                <div class="hd-icon hd-blue"><i class="fas fa-landmark-flag"></i></div>
                <h2>Definitions & Scope</h2>
                <p>At <strong>FurryMart</strong>, our "Shipping Policy" outlines the rigorous standards we maintain to deliver products across the country. This policy applies to all orders placed through our official platform and mobile application.</p>
                <ul>
                    <li>Orders are fulfilled through a smart inventory management system.</li>
                    <li>Items are dispatched from the warehouse center geographically closest to your location.</li>
                    <li>We prioritize safety and temperature control for all medical and hygiene supplies.</li>
                </ul>
            </div>

            <div class="content-card scroll-reveal" id="time">
                <div class="hd-icon hd-teal"><i class="fas fa-calendar-check"></i></div>
                <h2>Expected Delivery Timelines</h2>
                <p>Standard processing takes 1 working day. Once your package leaves our fulfillment center, these are the estimated arrival windows:</p>
                
                <div class="delivery-grid">
                    <div class="delivery-box">
                        <b>1-2</b>
                        <span>Working Days<br>Metro Cities</span>
                    </div>
                    <div class="delivery-box">
                        <b>3-4</b>
                        <span>Working Days<br>Tier 1 & 2 Cities</span>
                    </div>
                    <div class="delivery-box">
                        <b>5-8</b>
                        <span>Working Days<br>Other Locations</span>
                    </div>
                </div>
            </div>

            <div class="content-card scroll-reveal" id="cost">
                <div class="hd-icon hd-rose"><i class="fas fa-wallet"></i></div>
                <h2>Shipping Fees & Extra Costs</h2>
                <p>We believe in transparent pricing. No additional "order hopping" or hidden fulfillment fees are ever added to your final bill.</p>
                <ul class="legal-list">
                    <li><strong style="color:var(--fm-navy);">FREE SHIPPING:</strong> Applied automatically on all orders above ₹749.</li>
                    <li><strong style="color:var(--fm-navy);">STANDARD FEE:</strong> A flat delivery charge of ₹79 applies to orders under ₹749.</li>
                    <li><strong style="color:var(--fm-navy);">EXPRESS DELIVERY:</strong> Priority shipping options are available for selected pin-codes.</li>
                </ul>
            </div>

            <div class="content-card scroll-reveal" id="tracking">
                <div class="hd-icon hd-amber"><i class="fas fa-boxes-stacked"></i></div>
                <h2>Tracking & Split Shipments</h2>
                <p>To ensure you receive the freshest stock, products from different brands or warehouses may arrive in separate packages. Each shipment receives its own unique real-time tracking link.</p>
                <p>FurryMart uses verified third-party logistics partners to guarantee end-to-end security for every "Furry Family" order.</p>
            </div>

            <div class="content-card scroll-reveal" id="help">
                <div class="hd-icon hd-blue"><i class="fas fa-circle-question"></i></div>
                <h2>Logistics Assistance</h2>
                <p>Our dedicated logistics support team is ready to assist you with any delivery-related inquiries or unexpected delays.</p>
                
                <div class="support-footer">
                    <div>
                        <b style="font-size: 22px;">FurryMart Care Desk</b>
                        <p style="margin: 0; font-size: 15px; opacity: 0.8;">Mon-Sun, 09:30 AM - 06:30 PM</p>
                    </div>
                    <a href="contact.php" class="btn-action">CONTACT US</a>
                </div>
            </div>

        </div>
    </div>
</main>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Advanced Scroll Observer for Reveal
        const observer = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('reveal');
                }
            });
        }, { threshold: 0.15 });

        document.querySelectorAll('.scroll-reveal').forEach(el => observer.observe(el));

        // Progressive Nav Tracker
        const navLinks = document.querySelectorAll('.ship-link');
        window.addEventListener('scroll', () => {
            let current = "";
            document.querySelectorAll('.content-card').forEach(section => {
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