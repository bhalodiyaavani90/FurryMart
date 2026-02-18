<?php 
include "includes/header.php"; 
include "db.php"; 
?>

<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

<style>
    :root {
        --fm-navy: #0f1c3f;
        --fm-teal: #518992;
        --fm-accent: #f0fdfa;
        --fm-white: #ffffff;
        --fm-shadow: 0 40px 100px rgba(15, 28, 63, 0.12);
    }

    body { background-color: #f8fafc; font-family: 'Plus Jakarta Sans', sans-serif; overflow-x: hidden; scroll-behavior: smooth; }

    /* --- master HERO --- */
    .faq-hero {
        background: linear-gradient(135deg, var(--fm-navy) 0%, #1a3a5f 100%);
        padding: 140px 8% 220px;
        text-align: center;
        color: white;
        clip-path: ellipse(150% 100% at 50% 0%);
    }
    .faq-hero h1 { font-size: 62px; font-weight: 800; letter-spacing: -3px; margin-bottom: 20px; }
    .faq-hero p { font-size: 18px; opacity: 0.8; max-width: 800px; margin: 0 auto; line-height: 1.7; }

    /* --- CATEGORY NAVIGATION PILLS --- */
    .faq-nav-bar {
        display: flex; justify-content: center; gap: 15px; flex-wrap: wrap;
        margin: -70px auto 30px; position: relative; z-index: 100;
    }
    .nav-pill {
        background: white; padding: 18px 45px; border-radius: 50px;
        font-weight: 800; color: var(--fm-navy); cursor: pointer;
        box-shadow: var(--fm-shadow); transition: 0.4s cubic-bezier(0.2, 1, 0.2, 1);
        border: 1px solid #eef2f6; font-size: 13px; text-transform: uppercase; letter-spacing: 1px;
    }
    .nav-pill.active { background: var(--fm-teal); color: white; transform: translateY(-10px); }

    /* Explicit Instructional Hint */
    .interaction-hint {
        text-align: center;
        margin-bottom: 40px;
        font-size: 12px;
        font-weight: 800;
        color: var(--fm-teal);
        text-transform: uppercase;
        letter-spacing: 2px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
    }
    .interaction-hint i { animation: pulse 1.5s infinite; }

    /* --- HIGH-DENSITY GRID --- */
    .faq-stream {
        max-width: 1400px; margin: 0 auto 100px;
        display: grid; grid-template-columns: repeat(auto-fill, minmax(420px, 1fr));
        gap: 30px; padding: 0 5%;
    }
    .faq-card {
        background: white; padding: 40px; border-radius: 45px;
        box-shadow: var(--fm-shadow); border: 1px solid #eef2f6;
        cursor: pointer; transition: 0.4s; opacity: 0; transform: translateY(40px);
        display: flex; flex-direction: column; gap: 20px;
        position: relative; overflow: hidden;
        user-select: none;
    }
    .faq-card.reveal { opacity: 1; transform: translateY(0); }
    .faq-card:hover { border-color: var(--fm-teal); transform: translateY(-8px) scale(1.02); box-shadow: 0 50px 120px rgba(15, 28, 63, 0.15); }
    .faq-card:active { transform: translateY(-6px) scale(0.98); }
    
    .card-top { display: flex; align-items: center; gap: 20px; }
    .card-icon { width: 60px; height: 60px; background: var(--fm-accent); border-radius: 20px; display: flex; align-items: center; justify-content: center; color: var(--fm-teal); font-size: 24px; flex-shrink: 0; }
    
    .faq-card h3 { font-size: 17px; font-weight: 700; color: var(--fm-navy); line-height: 1.4; margin: 0; }

    /* Action Hint Badge */
    .card-action-hint {
        margin-top: 10px;
        font-size: 11px;
        font-weight: 800;
        color: var(--fm-teal);
        opacity: 0.6;
        transition: 0.3s;
        display: flex;
        align-items: center;
        gap: 8px;
        text-transform: uppercase;
    }
    .faq-card:hover .card-action-hint { opacity: 1; transform: translateX(5px); }

    /* --- QUANTUM VAULT MODAL --- */
    .vault-overlay {
        position: fixed; top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(15, 28, 63, 0.95); backdrop-filter: blur(25px);
        display: none; align-items: center; justify-content: center; z-index: 1000000;
        animation: fadeIn 0.3s ease;
    }
    .vault-modal {
        width: 1000px; max-width: 95%; background: white; border-radius: 60px;
        display: flex; overflow: hidden; transform: scale(0.6) rotateX(15deg);
        opacity: 0; transition: all 0.7s cubic-bezier(0.34, 1.56, 0.64, 1);
        box-shadow: 0 60px 180px rgba(0,0,0,0.5);
    }
    .vault-overlay.active { display: flex; }
    .vault-overlay.active .vault-modal { transform: scale(1) rotateX(0); opacity: 1; }

    .v-side-icon { flex: 0 0 35%; background: var(--fm-navy); display: flex; align-items: center; justify-content: center; color: var(--fm-teal); font-size: 120px; position: relative; overflow: hidden; }
    .v-side-icon::after { content: ''; position: absolute; width: 100%; height: 100%; background: radial-gradient(circle, rgba(81,137,146,0.2) 0%, transparent 70%); }
    
    .v-content { flex: 1; padding: 80px; position: relative; max-height: 85vh; overflow-y: auto; }
    .v-content::-webkit-scrollbar { width: 8px; }
    .v-content::-webkit-scrollbar-track { background: #f1f5f9; border-radius: 10px; }
    .v-content::-webkit-scrollbar-thumb { background: var(--fm-teal); border-radius: 10px; }
    .v-content::-webkit-scrollbar-thumb:hover { background: var(--fm-navy); }
    
    .v-content h2 { font-size: 32px; font-weight: 800; color: var(--fm-navy); margin-bottom: 25px; line-height: 1.2; letter-spacing: -1px; }
    .v-content p { font-size: 14.5px; line-height: 2; color: #475569; white-space: pre-wrap; word-wrap: break-word; }

    .v-close { position: absolute; top: 40px; right: 50px; font-size: 35px; color: var(--fm-navy); cursor: pointer; transition: 0.3s; }
    .v-close:hover { transform: rotate(90deg); color: #ef4444; }

    @keyframes pulse {
        0% { transform: scale(1); opacity: 1; }
        50% { transform: scale(1.2); opacity: 0.7; }
        100% { transform: scale(1); opacity: 1; }
    }
    
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    @media (max-width: 1000px) {
        .vault-modal { flex-direction: column; }
        .v-side-icon { height: 200px; font-size: 60px; }
        .v-content { padding: 40px; max-height: 70vh; }
    }
    
    @media (max-width: 768px) {
        .faq-hero { padding: 100px 5% 180px; }
        .faq-hero h1 { font-size: 42px; letter-spacing: -2px; }
        .faq-hero p { font-size: 16px; }
        
        .faq-nav-bar { margin: -60px auto 25px; gap: 10px; padding: 0 5%; }
        .nav-pill { padding: 14px 28px; font-size: 11px; }
        
        .faq-stream { 
            grid-template-columns: 1fr; 
            gap: 20px; 
            padding: 0 5%; 
        }
        
        .faq-card { padding: 30px; }
        .card-icon { width: 50px; height: 50px; font-size: 20px; }
        .faq-card h3 { font-size: 15px; }
        
        .v-content { padding: 30px; }
        .v-content h2 { font-size: 24px; }
        .v-content p { font-size: 13.5px; }
        .v-close { top: 20px; right: 25px; font-size: 28px; }
    }
</style>

<main>
    <section class="faq-hero">
        <div class="footer-animate">
            <h1>Intelligence Sovereignty</h1>
            <p>Welcome to the global FurryMart PetCare knowledge base. Select a clinical module below to begin your inquiry.</p>
        </div>
    </section>

    <div class="faq-nav-bar">
        <div class="nav-pill active" onclick="fm_filter('all')">Global Intel</div>
        <div class="nav-pill" onclick="fm_filter('Commonly Asked')">General</div>
        <div class="nav-pill" onclick="fm_filter('Products')">Products</div>
        <div class="nav-pill" onclick="fm_filter('Shipping')">Logistics</div>
        <div class="nav-pill" onclick="fm_filter('Returns')">Returns</div>
        <div class="nav-pill" onclick="fm_filter('Services')">Vetcare</div>
    </div>

    <div class="interaction-hint">
        <i class="fas fa-hand-pointer"></i> 
        <span>Click any clinical module below to reveal guidance</span>
    </div>

    <div class="faq-stream" id="faq-stream">
        <?php
        $sql = "SELECT * FROM faqs ORDER BY category ASC";
        $res = mysqli_query($conn, $sql);
        if($res) {
            while($row = mysqli_fetch_assoc($res)) {
                // Use htmlspecialchars and base64 for safe data transfer
                $question = htmlspecialchars($row['question'], ENT_QUOTES, 'UTF-8');
                $answer = htmlspecialchars($row['answer'], ENT_QUOTES, 'UTF-8');
                $category = htmlspecialchars($row['category'], ENT_QUOTES, 'UTF-8');
                $icon = htmlspecialchars($row['icon_class'], ENT_QUOTES, 'UTF-8');
                
                echo '
                <div class="faq-card scroll-reveal" 
                     data-cat="'.$category.'" 
                     data-question="'.$question.'"
                     data-answer="'.$answer.'"
                     data-icon="'.$icon.'">
                    <div class="card-top">
                        <div class="card-icon"><i class="'.$icon.'"></i></div>
                        <h3>'.$question.'</h3>
                    </div>
                    <div class="card-action-hint">
                        <i class="fas fa-arrow-right-long"></i> Click to unlock answer
                    </div>
                </div>';
            }
        }
        ?>
    </div>
</main>

<div class="vault-overlay" id="v-overlay">
    <div class="vault-modal">
        <div class="v-side-icon">
            <i id="v-icon-display" class="fas fa-paw"></i>
        </div>
        <div class="v-content">
            <span class="v-close" onclick="fm_closeVault()">&times;</span>
            <h2 id="v-q">Sovereign Question</h2>
            <p id="v-a">Clinical Answer Guidance...</p>
            <div style="margin-top:40px; padding:25px; background:var(--fm-accent); border-radius:25px; border:1px solid #ccfbf1;">
                <b style="color:var(--fm-teal); font-size:11px; text-transform:uppercase; letter-spacing:2px;">
                    <i class="fas fa-fingerprint"></i> Verified Clinical Resource
                </b>
            </div>
        </div>
    </div>
</div>

<script>
    // 1. Quantum Vault Controller
    function fm_openVault(q, a, icon) {
        document.getElementById('v-q').innerText = q;
        document.getElementById('v-a').innerText = a;
        document.getElementById('v-icon-display').className = icon;
        document.getElementById('v-overlay').classList.add('active');
        document.body.style.overflow = 'hidden'; 
    }

    function fm_closeVault() {
        document.getElementById('v-overlay').classList.remove('active');
        document.body.style.overflow = 'auto';
    }
    
    // Click handler for FAQ cards
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.faq-card').forEach(card => {
            card.addEventListener('click', function() {
                const question = this.getAttribute('data-question');
                const answer = this.getAttribute('data-answer');
                const icon = this.getAttribute('data-icon');
                fm_openVault(question, answer, icon);
            });
        });
    });

    // 2. Intelligence Filter logic
    function fm_filter(cat) {
        document.querySelectorAll('.nav-pill').forEach(p => p.classList.remove('active'));
        event.target.classList.add('active');
        
        document.querySelectorAll('.faq-card').forEach(card => {
            if(cat === 'all' || card.dataset.cat === cat) {
                card.style.display = 'flex';
            } else {
                card.style.display = 'none';
            }
        });
    }
    
    // Close modal on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && document.getElementById('v-overlay').classList.contains('active')) {
            fm_closeVault();
        }
    });
    
    // Close modal when clicking overlay background
    document.getElementById('v-overlay').addEventListener('click', function(e) {
        if (e.target === this) {
            fm_closeVault();
        }
    });

    // 3. Staggered Entrance Physics
    const obs = new IntersectionObserver((entries) => {
        entries.forEach((entry, i) => {
            if (entry.isIntersecting) {
                setTimeout(() => entry.target.classList.add('reveal'), i * 60);
            }
        });
    }, { threshold: 0.15 });
    document.querySelectorAll('.scroll-reveal').forEach(el => obs.observe(el));
</script>

<?php include "includes/footer.php"; ?>