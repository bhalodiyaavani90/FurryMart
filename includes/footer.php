<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<style>
    :root {
        --fm-navy: #0f1c3f;
        --fm-teal: #518992;
        --fm-light-teal: #76aab1;
        --fm-white: #ffffff;
        --fm-glass: rgba(255, 255, 255, 0.05);
        --fm-border: rgba(255, 255, 255, 0.1);
        --fm-transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
    }

    /* --- 1. TRUST BAR STYLES --- */
    .trust-bar {
        background: #ffffff;
        padding: 40px 8%;
        border-top: 1px solid #f1f5f9;
        position: relative;
        z-index: 2;
    }
    .trust-container {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 30px;
        max-width: 1400px;
        margin: 0 auto;
    }
    .trust-item { display: flex; align-items: center; gap: 15px; transition: 0.3s; }
    .trust-item:hover { transform: translateY(-5px); }
    .trust-icon {
        width: 50px; height: 50px; background: #f0fdfa; color: var(--fm-teal);
        border-radius: 15px; display: flex; align-items: center; justify-content: center;
        font-size: 20px; box-shadow: 0 5px 15px rgba(81, 137, 146, 0.1);
    }
    .trust-text b { display: block; font-size: 15px; color: var(--fm-navy); font-weight: 800; }
    .trust-text span { font-size: 13px; color: #64748b; font-weight: 500; }

    /* --- 2. ANIMATED MULTI-WAVE --- */
    .footer-wave-wrapper {
        width: 100%; height: 80px; min-height: 80px; background: #fff; margin-bottom: -1px;
    }
    .waves { position: relative; width: 100%; height: 80px; }
    .parallax > use { animation: move-forever 25s cubic-bezier(.55, .5, .45, .5) infinite; }
    .parallax > use:nth-child(1) { animation-delay: -2s; animation-duration: 7s; }
    .parallax > use:nth-child(2) { animation-delay: -3s; animation-duration: 10s; }
    .parallax > use:nth-child(3) { animation-delay: -4s; animation-duration: 13s; }
    .parallax > use:nth-child(4) { animation-delay: -5s; animation-duration: 20s; }
    @keyframes move-forever {
        0% { transform: translate3d(-90px, 0, 0); }
        100% { transform: translate3d(85px, 0, 0); }
    }

    /* --- 3. MAIN FOOTER CONTAINER --- */
    .premium-footer {
        background-color: var(--fm-navy);
        color: var(--fm-white);
        padding: 60px 8% 40px;
        font-family: 'Plus Jakarta Sans', sans-serif;
        position: relative;
        z-index: 1;
    }
    .footer-animate { opacity: 0; transform: translateY(30px); transition: var(--fm-transition); }
    .footer-animate.visible { opacity: 1; transform: translateY(0); }
    .footer-grid { display: grid; grid-template-columns: 1.5fr 1fr 1fr 1fr 1.2fr; gap: 50px; margin-bottom: 60px; }
    .footer-col h4 {
        font-size: 16px; font-weight: 800; text-transform: uppercase; margin-bottom: 30px;
        letter-spacing: 1.5px; position: relative; display: inline-block;
    }
    .footer-col h4::after {
        content: ''; position: absolute; bottom: -8px; left: 0; width: 30px; height: 3px;
        background: var(--fm-teal); border-radius: 10px;
    }
    .footer-col ul { list-style: none; padding: 0; margin: 0; }
    .footer-col ul li { margin-bottom: 15px; }
    .footer-col ul li a {
        color: rgba(255, 255, 255, 0.6); text-decoration: none; font-size: 14px;
        font-weight: 500; transition: var(--fm-transition); display: inline-flex; align-items: center; gap: 8px;
    }
    .footer-col ul li a:hover { color: var(--fm-white); transform: translateX(8px); }
    .footer-brand-logo { font-size: 34px; font-weight: 800; margin-bottom: 25px; display: block; text-decoration: none; color: white; }
    .footer-brand-logo span { color: var(--fm-teal); }
    .newsletter-premium { background: var(--fm-glass); padding: 30px; border-radius: 25px; border: 1px solid var(--fm-border); margin-top: 20px; }
    .premium-input { display: flex; gap: 10px; background: rgba(0, 0, 0, 0.2); padding: 8px; border-radius: 15px; }
    .premium-input input { background: transparent; border: none; color: white; padding-left: 15px; width: 100%; outline: none; }
    .premium-input button { background: var(--fm-teal); color: white; border: none; padding: 12px 25px; border-radius: 12px; font-weight: 700; cursor: pointer; transition: 0.3s; }
    .social-group { display: flex; gap: 15px; margin-top: 30px; }
    .social-icon {
        width: 45px; height: 45px; background: var(--fm-glass); border: 1px solid var(--fm-border);
        border-radius: 12px; display: flex; align-items: center; justify-content: center;
        color: white; text-decoration: none; transition: var(--fm-transition);
    }
    .social-icon:hover { background: var(--fm-teal); transform: rotate(10deg) translateY(-5px); }
    .contact-block { display: flex; gap: 15px; margin-bottom: 25px; }
    .contact-block i { font-size: 20px; color: var(--fm-teal); }
    .store-btn { margin-bottom: 12px; display: block; transition: 0.3s; }
    .store-btn img { height: 42px; border-radius: 8px; }
    .footer-legal { padding-top: 30px; border-top: 1px solid var(--fm-border); display: flex; justify-content: space-between; align-items: center; font-size: 13px; color: rgba(255, 255, 255, 0.4); }
    #scrollToTop {
        position: fixed; bottom: 40px; right: 40px; width: 55px; height: 55px;
        background: var(--fm-teal); color: white; border-radius: 50%;
        display: flex; align-items: center; justify-content: center; cursor: pointer;
        z-index: 99; box-shadow: 0 15px 30px rgba(0,0,0,0.4); opacity: 0; visibility: hidden; transition: 0.4s;
    }
    #scrollToTop.active { opacity: 1; visibility: visible; bottom: 30px; }

    @media (max-width: 1200px) { .footer-grid { grid-template-columns: 1fr 1fr; } .footer-col:first-child { grid-column: span 2; } }
    @media (max-width: 768px) { .footer-grid { grid-template-columns: 1fr; } .footer-col:first-child { grid-column: span 1; } .waves { height: 40px; } }
</style>

<section class="trust-bar footer-animate">
    <div class="trust-container">
        <div class="trust-item">
            <div class="trust-icon"><i class="fas fa-shipping-fast"></i></div>
            <div class="trust-text"><b>Pan-India Delivery</b><span>Safe & Speedy Arrival</span></div>
        </div>
        <div class="trust-item">
            <div class="trust-icon"><i class="fas fa-user-shield"></i></div>
            <div class="trust-text"><b>100% Secure Payments</b><span>SSL Encrypted Checkout</span></div>
        </div>
        <div class="trust-item">
            <div class="trust-icon"><i class="fas fa-hand-holding-heart"></i></div>
            <div class="trust-text"><b>Premium Quality</b><span>Vetted by Experts</span></div>
        </div>
        <div class="trust-item">
            <div class="trust-icon"><i class="fas fa-headset"></i></div>
            <div class="trust-text"><b>24/7 Support</b><span>Here for your pets</span></div>
        </div>
    </div>
</section>

<div class="footer-wave-wrapper">
    <svg class="waves" xmlns="http://www.w3.org/2000/svg" viewBox="0 24 150 28" preserveAspectRatio="none" shape-rendering="auto">
        <defs><path id="gentle-wave" d="M-160 44c30 0 58-18 88-18s 58 18 88 18 58-18 88-18 58 18 88 18 v44h-352z" /></defs>
        <g class="parallax">
            <use href="#gentle-wave" x="48" y="0" fill="rgba(15, 28, 63, 0.3)" />
            <use href="#gentle-wave" x="48" y="3" fill="rgba(81, 137, 146, 0.2)" />
            <use href="#gentle-wave" x="48" y="5" fill="rgba(15, 28, 63, 0.5)" />
            <use href="#gentle-wave" x="48" y="7" fill="#0f1c3f" />
        </g>
    </svg>
</div>

<footer class="premium-footer">
    <div class="footer-grid">
        <div class="footer-col footer-animate">
            <a href="index.php" class="footer-brand-logo"><i class="fas fa-paw"></i> Furry<span>Mart</span></a>
            <div class="newsletter-premium">
                <h4>Join the Furry Family</h4>
                <p>Subscribe to get 10% off your first order and weekly pet health tips.</p>
                <form id="newsletterForm" class="premium-input">
                    <input type="email" id="subscriberEmail" placeholder="Email Address" required>
                    <button type="submit" id="subBtn">JOIN</button>
                </form>
                <div class="social-group">
                    <a href="https://www.facebook.com/" class="social-icon"><i class="fab fa-facebook-f"></i></a>
                    <a href="https://www.instagram.com/accounts/login/?hl=en" class="social-icon"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="social-icon"><i class="fab fa-tiktok"></i></a>
                    <a href="https://www.youtube.com/" class="social-icon"><i class="fab fa-youtube"></i></a>
                </div>
            </div>
        </div>

        <div class="footer-col footer-animate">
            <h4>Online Shop</h4>
            <ul>
               
                <li><a href="vetcare.php">Vet Care</a></li>
                <li><a href="grooming.php">Grooming</a></li>
                <li><a href="blog.php">Knowledge Hub</a></li>
            </ul>
        </div>

        <div class="footer-col footer-animate">
            <h4>Trust & Safety</h4>
            <ul>
                <li><a href="privacy.php">Privacy Center</a></li>
                <li><a href="terms.php">Terms of Service</a></li>
                <li><a href="shipping.php">Delivery Info</a></li>
                <li><a href="returns.php">Returns & Refund</a></li>
                <li><a href="faq.php">Expert FAQs</a></li>
            </ul>
        </div>

        <div class="footer-col footer-animate">
            <h4>Quick Links</h4>
            <ul>
                <li><a href="about.php">Our Story</a></li>
                <li><a href="contact.php">Help Center</a></li>
                <li><a href="foundation.php">Furry Foundation</a></li>
                <li><a href="feedback.php">User Reports</a></li>
            </ul>
        </div>

        <div class="footer-col footer-animate">
            <h4>Support</h4>
            <div class="contact-block"><i class="fas fa-headset"></i><span><b>Helpline:</b><br>1800-444-5555</span></div>
            <div class="contact-block"><i class="fas fa-envelope-open-text"></i><span><b>Email Us:</b><br>care@furrymart.com</span></div>
        </div>
    </div>

    <div class="footer-legal">
        <p>© 2025 FurryMart Pet Care Global. All rights reserved.</p>
        <div style="display: flex; gap: 20px;">
            <i class="fab fa-cc-visa fa-2x"></i><i class="fab fa-cc-mastercard fa-2x"></i><i class="fab fa-cc-paypal fa-2x"></i><i class="fab fa-google-pay fa-2x"></i>
        </div>
        <p>Made with 🐾 in India</p>
    </div>
</footer>

<div id="scrollToTop"><i class="fas fa-chevron-up"></i></div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Footer elements visible by default - no auto-animation
        const elements = document.querySelectorAll('.footer-animate');
        elements.forEach(el => el.classList.add('visible'));

        const scrollBtn = document.getElementById('scrollToTop');
        window.addEventListener('scroll', () => {
            if (window.pageYOffset > 500) { scrollBtn.classList.add('active'); }
            else { scrollBtn.classList.remove('active'); }
        });
        scrollBtn.addEventListener('click', () => { window.scrollTo({ top: 0, behavior: 'smooth' }); });

        // --- NEW: RUNNABLE SUBSCRIBE LOGIC ---
        $('#newsletterForm').on('submit', function(e) {
            e.preventDefault();
            const email = $('#subscriberEmail').val();
            const btn = $('#subBtn');

            btn.prop('disabled', true).text('...');

            $.ajax({
                url: 'subscribe_handler.php',
                type: 'POST',
                data: { email: email },
                success: function(response) {
                    if(response === 'success') {
                        Swal.fire({
                            title: 'Paw-some!',
                            text: 'You have successfully subscribed to FurryMart. Check your inbox for treats! 🐾',
                            icon: 'success',
                            confirmButtonColor: '#518992'
                        });
                        $('#subscriberEmail').val('');
                    } else if(response === 'exists') {
                        Swal.fire({
                            title: 'Already with us!',
                            text: 'This email is already part of the Furry Family.',
                            icon: 'info',
                            confirmButtonColor: '#518992'
                        });
                    } else {
                        Swal.fire('Oops', 'Something went wrong. Please try again.', 'error');
                    }
                    btn.prop('disabled', false).text('JOIN');
                }
            });
        });
    });
</script>