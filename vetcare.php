<?php 
include "includes/header.php"; 
include "db.php"; 
// Check if user is logged in
$is_logged_in = isset($_SESSION['user_id']);
// Handle Appointment Submission
$msg = "";
if(isset($_POST['book_now'])){
    $name = mysqli_real_escape_string($conn, $_POST['parent_name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $mobile = mysqli_real_escape_string($conn, $_POST['mobile']);
    $pname = mysqli_real_escape_string($conn, $_POST['pet_name']);
    $ptype = $_POST['pet_type'];
    $service = $_POST['service'];
    $date = $_POST['date'];
    $time = $_POST['time'];
    $status = "Pending"; // Default status based on your DB schema

    $sql = "INSERT INTO vet_appointments (pet_parent_name, email, mobile, pet_name, pet_type, service_id, appointment_date, appointment_time, status) 
            VALUES ('$name', '$email', '$mobile', '$pname', '$ptype', '$service', '$date', '$time', '$status')";
    
    if(mysqli_query($conn, $sql)){
        $msg = "<div class='alert-success'><i class='fas fa-check-circle'></i> Appointment Requested Successfully! We will contact you soon.</div>";
    }
}
?>

<style>
    /* --- INTEGRATED PROFESSIONAL VIDEO HERO SECTION --- */
    .vet-hero { 
        position: relative; width: 100%; height: calc(100vh - 180px); 
        min-height: 500px; display: flex; align-items: center; 
        overflow: hidden; background: #000;
    }
    #hero-video { position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover; z-index: 1; }
    .hero-overlay { position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: linear-gradient(to right, rgba(0, 0, 0, 0.75) 0%, rgba(0, 0, 0, 0.3) 50%, transparent 100%); z-index: 2; }
    .hero-content { position: relative; z-index: 3; padding-left: 8%; max-width: 750px; color: white; }
    .hero-content h1 { font-size: clamp(32px, 5vw, 55px); font-weight: 800; margin-bottom: 20px; line-height: 1.1; text-shadow: 2px 2px 10px rgba(0,0,0,0.5); }
    .hero-content p { font-size: clamp(16px, 2vw, 20px); margin-bottom: 35px; line-height: 1.5; font-weight: 500; color: #f1f5f9; max-width: 600px; }
    .btn-appointment { display: inline-block; background: #e6b034c9; color: #fff; padding: 18px 45px; text-decoration: none; border-radius: 50px; font-weight: 700; font-size: 16px; transition: 0.3s; border: 2px solid transparent; text-transform: uppercase; }
    .btn-appointment:hover { background: transparent; border-color: #fff; transform: translateY(-3px); }

    /* --- SECTION HEADINGS --- */
    .section-padding { padding: 80px 8%; text-align: center; }
    .section-title { font-size: 38px; font-weight: 700; color: #1e293b; margin-bottom: 10px; position: relative; }
    .section-subtitle { color: #64748b; font-size: 16px; margin-bottom: 50px; display: block; }
    .section-title::after { content: ''; display: block; width: 60px; height: 4px; background: #518992ff; margin: 15px auto; border-radius: 2px; }

    /* --- VETERINARIANS GRID --- */
    .vet-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 30px; }
    .doc-card { background: white; border-radius: 20px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.06); transition: 0.4s; border: 1px solid #f1f5f9; }
    .doc-card:hover { transform: translateY(-10px); box-shadow: 0 15px 35px rgba(0,0,0,0.1); }
    .doc-img-box { width: 100%; height: 350px; overflow: hidden; }
    .doc-img-box img { width: 100%; height: 100%; object-fit: cover; object-position: top; }
    .doc-info { padding: 25px; }
    .doc-name { font-size: 22px; font-weight: 700; color: #0f172a; margin-bottom: 5px; }
    .doc-spec { color: #518992ff; font-weight: 600; font-size: 14px; margin-bottom: 10px; display: block; }
    .doc-exp { font-size: 12px; font-weight: 700; background: #f1f5f9; padding: 5px 15px; border-radius: 20px; color: #64748b; }

    /* --- SERVICES GRID --- */
    .grid-4 { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 25px; }
    .service-card { 
        background: white; padding: 40px 25px; border-radius: 20px; 
        box-shadow: 0 5px 15px rgba(0,0,0,0.04); transition: 0.3s;
        border-top: 5px solid transparent;
    }
    .service-card:hover { transform: translateY(-5px); border-top-color: var(--primary); box-shadow: 0 10px 25px rgba(0,0,0,0.08); }
    .service-card i { font-size: 45px; color: var(--primary); margin-bottom: 20px; display: block; }
    .service-card h4 { font-size: 18px; font-weight: 700; color: #1e293b; margin-bottom: 12px; }

    /* --- NEW SPACIOUS EXPLORE SERVICES (ZIGLY STYLE) --- */
    .explore-bg { background-color: #fffaf5; padding: 100px 8%; }
    
    .service-tabs {
        display: flex;
        justify-content: center;
        gap: 15px;
        margin-bottom: 60px;
        flex-wrap: wrap;
    }

    .tab-btn {
        padding: 14px 40px;
        background: white;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        font-weight: 700;
        color: #64748b;
        cursor: pointer;
        transition: all 0.3s ease;
        font-size: 15px;
    }

    .tab-btn.active {
        background: #1e3a8a; /* Deep Navy Blue */
        color: white;
        border-color: #1e3a8a;
        box-shadow: 0 10px 25px rgba(30, 58, 138, 0.2);
    }
    /* --- CSS STYLING --- */
    .stats-banner-wrapper {
        background-color: #fffaf5; 
        padding: 60px 8%;
    }

    .stats-container {
        background-color: #0f1c3f; /* Deep Navy */
        border-radius: 30px;
        padding: 70px 40px;
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 20px;
        text-align: center;
        box-shadow: 0 20px 50px rgba(15, 28, 63, 0.15);
    }

    .stat-item { color: #ffffff; }

    .stat-icon-circle {
        width: 75px; height: 75px;
        background-color: #ffffff;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        margin: 0 auto 25px;
    }

    .stat-icon-circle i { font-size: 30px; color: #0f1c3f; }

    /* Flex container for number and suffix (+ or K+) to keep them on one line */
    .stat-number {
        display: inline-block;
        font-size: 36px;
        font-weight: 800;
        letter-spacing: 0.5px;
    }

    .stat-suffix {
        font-size: 36px;
        font-weight: 800;
    }

    .stat-label {
        font-size: 15px;
        font-weight: 500;
        opacity: 0.85;
        margin-top: 10px;
        text-transform: capitalize;
    }

    @media (max-width: 992px) { .stats-container { grid-template-columns: repeat(2, 1fr); gap: 50px; } }
    @media (max-width: 576px) { .stats-container { grid-template-columns: 1fr; } }

    /* Spacious Linear Container */
   .grid-4 { display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 20px; }
    .service-card { 
        background: white; padding: 25px 20px; border-radius: 15px; 
        box-shadow: 0 4px 10px rgba(0,0,0,0.03); border-top: 4px solid transparent; transition: 0.3s;
    }
    .service-card:hover { border-top-color: #518992; transform: translateY(-5px); }
    .service-card i { font-size: 32px; color: #518992; margin-bottom: 15px; display: block; }
    .service-card h4 { font-size: 15px; font-weight: 800; color: #1e293b; margin-bottom: 10px; }
    .service-card p { font-size: 13px; color: #64748b; line-height: 1.5; }

    /* --- COMPACT ZIGLY EXPLORER --- */
    .explore-bg { background-color: #fffaf5; padding: 70px 8%; }
    .service-tabs { display: flex; justify-content: center; gap: 10px; margin-bottom: 35px; flex-wrap: wrap; }
    .tab-btn { padding: 10px 22px; background: white; border: 1px solid #e2e8f0; border-radius: 8px; font-weight: 700; color: #64748b; cursor: pointer; transition: 0.3s; font-size: 13px; }
    .tab-btn.active { background: #1e3a8a; color: white; border-color: #1e3a8a; box-shadow: 0 8px 15px rgba(30, 58, 138, 0.15); }

    .service-linear-box {
        display: grid; grid-template-columns: 1fr 1fr; gap: 40px; align-items: center;
        background: white; padding: 30px; border-radius: 25px; box-shadow: 0 15px 40px rgba(0,0,0,0.02);
        max-width: 1100px; margin: 0 auto; text-align: left;
    }
    .service-img-frame { width: 100%; height: 320px; border-radius: 20px; overflow: hidden; }
    .service-img-frame img { width: 100%; height: 100%; object-fit: cover; }
    .service-info-frame h2 { font-size: 26px; font-weight: 800; color: #0f172a; margin-bottom: 15px; }
    .medical-features { list-style: none; padding: 0; }
    .medical-features li { position: relative; padding-left: 25px; margin-bottom: 12px; font-size: 14px; color: #334155; font-weight: 600; }
    .medical-features li::before { content: '■'; position: absolute; left: 0; color: #518992; font-size: 12px; top: 1px; }
    /* --- UPDATED PROFESSIONAL BOOKING FORM CSS --- */
    /* --- LARGE HORIZONTAL VIDEO STYLING --- */
.furrymart-brand-video-section {
    background-color: #fffaf5; /* Matching your explore section background */
    padding: 80px 8%;
    text-align: center;
}

.video-container-wrapper {
    max-width: 1200px;
    margin: 0 auto;
}

.video-header-text {
    margin-bottom: 45px;
}

.large-horizontal-player {
    position: relative;
    width: 100%;
    aspect-ratio: 16 / 9; /* Cinematic horizontal look */
    background: #000;
    border-radius: 40px; /* Large professional curves */
    overflow: hidden;
    box-shadow: 0 40px 80px rgba(0, 0, 0, 0.15);
    border: 10px solid #ffffff; /* Elegant white frame */
}

.large-horizontal-player video {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}

.video-watermark {
    position: absolute;
    top: 30px;
    right: 30px;
    background: rgba(255, 255, 255, 0.2);
    backdrop-filter: blur(5px);
    padding: 8px 20px;
    border-radius: 50px;
    color: #fff;
    font-weight: 800;
    font-size: 14px;
    letter-spacing: 1px;
    pointer-events: none;
    text-transform: uppercase;
}

/* Responsive adjustment for tablets and phones */
@media (max-width: 768px) {
    .furrymart-brand-video-section { padding: 40px 5%; }
    .large-horizontal-player { border-radius: 20px; border-width: 5px; }
    .video-header-text h2 { font-size: 26px; }
}
   /* --- BOOKING FORM CSS --- */
    .appointment-box { max-width: 1000px; margin: 0 auto; background: #fff; padding: 50px 60px; border-radius: 30px; box-shadow: 0 20px 60px rgba(0,0,0,0.08); border: 1px solid #f1f5f9; }
    .form-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 30px; text-align: left; }
    .form-group label { font-weight: 700; color: #475569; margin-bottom: 10px; display: block; font-size: 14px; }
    .form-group input, .form-group select { width: 100%; padding: 15px 20px; border: 1.5px solid #e2e8f0; border-radius: 12px; font-size: 15px; background-color: #f8fafc; transition: all 0.3s ease; box-sizing: border-box; }
    
    /* --- STRICT DISABLED STYLING --- */
    input:disabled, select:disabled { cursor: not-allowed; opacity: 0.6; background-color: #e2e8f0 !important; }
    .btn-book:disabled { background: #94a3b8 !important; cursor: not-allowed; transform: none !important; }

    .btn-book { width: 100%; background: var(--primary); color: white; padding: 20px; border: none; border-radius: 15px; font-weight: 800; font-size: 18px; cursor: pointer; transition: 0.3s; text-transform: uppercase; }
    .alert-success { background: #dcfce7; color: #166534; padding: 20px; border-radius: 15px; margin-bottom: 40px; font-weight: 600; border: 1px solid #bbf7d0; text-align: center; }
    .alert-error { background: #fee2e2; color: #991b1b; padding: 20px; border-radius: 15px; margin-bottom: 30px; border: 1px solid #fecaca; font-weight: 600; text-align: center; }

/* --- PREMIUM TIPS GRID UPDATED FOR 5 IN A ROW --- */
.tips-section { padding: 80px 8%; background: #fffaf5; text-align: center; }
.tips-grid {
    display: grid;
    /* Updated for 5 cards in a row on desktop */
    grid-template-columns: repeat(auto-fit, minmax(210px, 1fr));
    gap: 25px;
    margin-top: 50px;
    text-align: left;
}

.tip-card {
    background: #fff;
    border-radius: 25px;
    overflow: hidden;
    box-shadow: 0 15px 45px rgba(0,0,0,0.05);
    transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
    display: flex;
    flex-direction: column;
    border: 1px solid #f1f5f9;
}

.tip-card:hover { transform: translateY(-10px); box-shadow: 0 25px 50px rgba(81, 137, 146, 0.12); }

.tip-thumb { height: 180px; width: 100%; overflow: hidden; background: #f8fafc; }
.tip-thumb img { width: 100%; height: 100%; object-fit: cover; transition: 0.5s; }
.tip-card:hover .tip-thumb img { scale: 1.08; }

.tip-content { padding: 20px; flex-grow: 1; display: flex; flex-direction: column; }
.tip-meta { font-size: 11px; font-weight: 800; color: #518992; text-transform: uppercase; margin-bottom: 10px; letter-spacing: 0.5px; }
.tip-title { font-size: 18px; font-weight: 800; color: #0f172a; margin-bottom: 12px; line-height: 1.3; }
.tip-desc { font-size: 13px; color: #64748b; line-height: 1.6; margin-bottom: 15px; flex-grow: 1; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; }

.read-more-btn {
    background: #518992;
    color: white;
    padding: 10px 20px;
    border-radius: 10px;
    font-weight: 700;
    font-size: 13px;
    border: none;
    cursor: pointer;
    transition: 0.3s;
    width: fit-content;
}

/* --- PREMIUM SPLIT-SCREEN MODAL --- */
.modal-overlay {
    display: none;
    position: fixed;
    top: 0; left: 0;
    width: 100%; height: 100%;
    background: rgba(15, 28, 63, 0.85); /* Navy Glass Effect */
    backdrop-filter: blur(10px);
    z-index: 10000;
    justify-content: center;
    align-items: center;
    padding: 20px;
}

.modal-container {
    background: #fff;
    width: 100%;
    max-width: 1100px; /* Wider for split view */
    height: 80vh; /* Fixed height for consistent scrolling */
    border-radius: 40px; /* FurryMart UI curves */
    overflow: hidden;
    position: relative;
    display: flex; /* Creates the side-by-side split */
    box-shadow: 0 40px 100px rgba(0,0,0,0.5);
}

/* LEFT SIDE: Fixed Image Frame */
.modal-left-img {
    flex: 0 0 45%; /* Image takes 45% width */
    background: #f8fafc;
    display: flex;
    justify-content: center;
    align-items: center;
    position: relative;
    border-right: 1px solid #f1f5f9;
}

/* ANTI-BLUR: contain ensures image is original quality */
.modal-left-img img {
    max-width: 90%;
    max-height: 90%;
    object-fit: contain;
    border-radius: 20px;
    display: none; /* Initially hidden for spinner */
}

/* RIGHT SIDE: Scrollable Content */
.modal-right-scroll {
    flex: 1;
    overflow-y: auto; /* Content scrolls while image stays fixed */
    padding: 60px;
}

/* Custom Scrollbar */
.modal-right-scroll::-webkit-scrollbar { width: 6px; }
.modal-right-scroll::-webkit-scrollbar-thumb { background: #518992; border-radius: 10px; }

/* MODAL SUMMARY STYLING */
.modal-summary-box {
    background: #f0fdfa;
    padding: 20px;
    border-left: 5px solid #518992;
    border-radius: 12px;
    font-size: 16px;
    font-style: italic;
    color: #475569;
    margin-bottom: 30px;
    line-height: 1.6;
}

/* SPINNER ELEMENTS */
.spinner-container {
    position: absolute;
    text-align: center;
}
.loader {
    width: 40px; height: 40px;
    border: 4px solid #f3f3f3;
    border-top: 4px solid #518992;
    border-radius: 50%;
    animation: spin 1s linear infinite;
    margin: 0 auto 10px;
}
@keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }

.modal-close {
    position: absolute; top: 25px; right: 25px;
    width: 40px; height: 40px; background: #fff;
    border: none; border-radius: 50%; font-size: 22px;
    color: #0f1c3f; cursor: pointer; z-index: 100;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}
    @media (max-width: 768px) { .form-grid { grid-template-columns: 1fr; } .stats-container { grid-template-columns: repeat(2, 1fr); } }
</style>

<main>
    <section class="vet-hero">
        <video autoplay muted loop playsinline id="hero-video">
            <source src="uploads/videos/vetcare_hero.mp4" type="video/mp4"> 
        </video>
        <div class="hero-overlay"></div>
        <div class="hero-content">
            <h1>Your Pet's Health <br> Is Our Priority!</h1>
            <p>Connect with India's most trusted veterinary specialists. World-class care for your furry best friends is just one click away.</p>
            <a href="#book" class="btn-appointment">BOOK AN APPOINTMENT</a>
        </div>
    </section>

    <section class="section-padding">
        <h2 class="section-title">Meet Our Expert Veterinarians</h2>
        <div class="vet-grid">
            <?php 
            $docs = mysqli_query($conn, "SELECT * FROM veterinarians ORDER BY id DESC");
            while($d = mysqli_fetch_assoc($docs)){
            ?>
            <div class="doc-card">
                <div class="doc-img-box"><img src="assets/images/docs/<?php echo $d['image']; ?>" alt="<?php echo $d['name']; ?>"></div>
                <div class="doc-info">
                    <h3 class="doc-name"><?php echo $d['name']; ?></h3>
                    <span class="doc-spec"><?php echo $d['specialization']; ?></span><br><br>
                    <span class="doc-exp">EXP: <?php echo $d['experience']; ?></span>
                </div>
            </div>
            <?php } ?>
        </div>
    </section>
    <section class="stats-banner-wrapper">
    <div class="stats-container">
        <div class="stat-item">
            <div class="stat-icon-circle">
                <i class="fas fa-paw"></i>
            </div>
            <div class="stat-number" data-target="200">0</div><span class="stat-suffix">K+</span>
            <div class="stat-label">Happy Pets</div>
        </div>

        <div class="stat-item">
            <div class="stat-icon-circle">
                <i class="far fa-lightbulb"></i>
            </div>
            <div class="stat-number" data-target="150">0</div><span class="stat-suffix">+</span>
            <div class="stat-label">Vet Care Professionals</div>
        </div>

        <div class="stat-item">
            <div class="stat-icon-circle">
                <i class="fas fa-user-md"></i>
            </div>
            <div class="stat-number" data-target="25">0</div><span class="stat-suffix">+</span>
            <div class="stat-label">Years of Experience</div>
        </div>

        <div class="stat-item">
            <div class="stat-icon-circle">
                <i class="fas fa-map-marker-alt"></i>
            </div>
            <div class="stat-number" data-target="30">0</div><span class="stat-suffix">+</span>
            <div class="stat-label">Stores Nationwide</div>
        </div>
    </div>
</section>

    <section class="section-padding" style="background: #f8fafc;">
        <h2 class="section-title">Comprehensive Health Services</h2>
        <p class="section-subtitle">Preventive care, surgical expertise, and advanced diagnostics under one roof.</p>
        <div class="grid-4">
            <?php 
            $srv_res = mysqli_query($conn, "SELECT * FROM vet_services");
            while($srv = mysqli_fetch_assoc($srv_res)){
            ?>
            <div class="service-card">
                <i class="<?php echo $srv['icon']; ?>"></i>
                <h4><?php echo $srv['title']; ?></h4>
                <p style="font-size:14px; color:#64748b; line-height: 1.6;"><?php echo $srv['description']; ?></p>
            </div>
            <?php } ?>
        </div>
    </section>

    <section class="explore-bg section-padding">
        <div class="explore-header">
            <h1>Explore Our Services</h1>
            <p class="section-subtitle">We provide high-quality medical attention for every stage of your pet's life.</p>
        </div>

        <div class="service-tabs">
            <button class="tab-btn active" onclick="showService('medicine', this)">Internal Medicine</button>
            <button class="tab-btn" onclick="showService('surgery', this)">Surgery</button>
            <button class="tab-btn" onclick="showService('imaging', this)">Diagnostic Imaging</button>
            <button class="tab-btn" onclick="showService('lab', this)">Laboratory</button>
        </div>

        <div class="service-linear-box" id="service-display">
            <div class="service-img-frame">
                <img id="service-img" src="uploads/internal medicine.jpg" alt="Pet Healthcare">
            </div>

            <div class="service-info-frame" id="service-text">
                <h2 id="service-title">We’re Here to Heal</h2>
                <p id="service-desc">Our Internal Medicine services focus on diagnosing, treating, and managing a wide range of conditions affecting your pet’s internal organs and systems.</p>
                
                <ul class="medical-features" id="service-list">
                    <li>Precise diagnoses and effective, targeted treatments.</li>
                    <li>Compassionate, personalized care from specialists.</li>
                    <li>Focus on long-term health and preventive wellbeing.</li>
                    <li>Expert management of chronic conditions.</li>
                </ul>
            </div>
        </div>
    </section>
    <section class="furrymart-brand-video-section">
    <div class="video-container-wrapper">
        <div class="video-header-text">
            <h2 class="section-title">Because Their Happiness is Our Heartbeat</h2>
            <p class="section-subtitle">"They can't describe the pain. But at FurryMart, we sense it, feel it, and most importantly—treat it."</p>
        </div>

        <div class="large-horizontal-player">
            <video controls poster="uploads/videos/furrymart_hero_poster.png" id="main-brand-video">
                <source src="uploads/videos/Furrymart4.mp4" type="video/mp4">
                Your browser does not support the video tag.
            </video>
            <div class="video-watermark">
                <span>FurryMart 24x7</span>
            </div>
        </div>
    </div>
</section>
   <section id="book" class="section-padding" style="background: white;">
        <div class="appointment-box">
            <h2 class="section-title">Request a Consultation</h2>
            
            <?php echo $msg; ?>
            
            <?php if(!$is_logged_in): ?>
                <div class="alert-error">
                    <i class="fas fa-lock"></i> Only registered users can book appointments. 
                    <a href="login.php" style="color: #991b1b; text-decoration: underline;">Login here</a>
                </div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="form-grid">
                    <div class="form-group">
                        <label>Parent Name</label>
                        <input type="text" name="parent_name" required placeholder="Full Name" <?php if(!$is_logged_in) echo 'disabled'; ?>>
                    </div>
                    <div class="form-group">
                        <label>Mobile Number</label>
                        <input type="text" name="mobile" required placeholder="10-digit number" <?php if(!$is_logged_in) echo 'disabled'; ?>>
                    </div>
                    <div class="form-group">
                        <label>Email Address</label>
                        <input type="email" name="email" required placeholder="Email ID" <?php if(!$is_logged_in) echo 'disabled'; ?>>
                    </div>
                    <div class="form-group">
                        <label>Pet's Name</label>
                        <input type="text" name="pet_name" required placeholder="Buddy" <?php if(!$is_logged_in) echo 'disabled'; ?>>
                    </div>
                    <div class="form-group">
                        <label>Pet Type</label>
                        <select name="pet_type" <?php if(!$is_logged_in) echo 'disabled'; ?>>
                            <option>Dog</option><option>Cat</option><option>Bird</option><option>Rabbit</option><option>Other</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Consultation For</label>
                        <select name="service" <?php if(!$is_logged_in) echo 'disabled'; ?>>
                            <?php 
                            $srv_res2 = mysqli_query($conn, "SELECT * FROM vet_services");
                            while($s = mysqli_fetch_assoc($srv_res2)){
                                echo "<option value='".$s['id']."'>".$s['title']."</option>";
                            } ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Preferred Date</label>
                        <input type="date" name="date" required <?php if(!$is_logged_in) echo 'disabled'; ?>>
                    </div>
                    <div class="form-group">
                        <label>Preferred Time Slot</label>
                        <input type="time" name="time" required <?php if(!$is_logged_in) echo 'disabled'; ?>>
                    </div>
                    
                    <div class="btn-submit-container" style="grid-column: span 2;">
                        <button type="submit" name="book_now" class="btn-book" <?php if(!$is_logged_in) echo 'disabled'; ?>>
                            <?php echo $is_logged_in ? 'CONFIRM APPOINTMENT REQUEST' : 'PLEASE LOGIN TO BOOK'; ?>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </section>
    
    <section class="tips-section">
    <h2 class="section-title">Expert Tips & Insights</h2>
    <p class="section-subtitle">Latest articles and advice from our veterinary specialists.</p>
    
    <div class="tips-grid">
        <?php
        // Fetch the latest 5 tips for one professional line
        $tips_sql = "SELECT * FROM expert_tips ORDER BY publish_date DESC LIMIT 4";
        $tips_result = mysqli_query($conn, $tips_sql);

        if(mysqli_num_rows($tips_result) > 0){
            while($row = mysqli_fetch_assoc($tips_result)){
                $dateStr = date("F j, Y", strtotime($row['publish_date']));
        ?>
        <div class="tip-card">
            <div class="tip-thumb">
                <img src="<?php echo $row['thumbnail_image']; ?>" alt="<?php echo $row['title']; ?>">
            </div>
            <div class="tip-content">
                <div class="tip-meta">
                    <span><?php echo $dateStr; ?></span>
                    <span class="tip-cat"><?php echo $row['category']; ?></span>
                </div>
                <h3 class="tip-title"><?php echo $row['title']; ?></h3>
                <p class="tip-desc"><?php echo $row['short_description']; ?></p>
                <button class="read-more-btn js-open-modal" data-id="<?php echo $row['id']; ?>">Read More</button>
            </div>
        </div>
        <?php 
            }
        } else {
            echo "<p>No tips found.</p>";
        }
        ?>
    </div>
</section>

<div class="modal-overlay" id="tipModal">
    <div class="modal-container">
        <button class="modal-close js-close-modal">&times;</button>
        
        <div class="modal-left-img">
            <div class="spinner-container" id="tipSpinner">
                <div class="loader"></div>
                <div style="font-size:11px; font-weight:700; color:#64748b;">LOADING...</div>
            </div>
            <img src="" id="modalImg" alt="Insight Visual">
        </div>

        <div class="modal-right-scroll">
            <div style="color:#518992; font-weight:800; margin-bottom:15px; text-transform:uppercase; font-size:13px;">
                <span id="modalDate"></span> | <span id="modalCat"></span>
            </div>
            <h2 id="modalTitle" style="font-size:32px; font-weight:800; color:#1e293b; margin-bottom:20px; line-height:1.2;"></h2>
            
            <div class="modal-summary-box" id="modalSummary"></div>

            <div id="modalContent" style="font-size:17px; line-height:1.9; color:#475569;">
                </div>
        </div>
    </div>
</div>
</main>
<script>
const serviceData = {
        medicine: {
            title: "We’re Here to Heal",
            desc: "Expert precision in managing internal conditions.",
            img: "uploads/internal medicine.jpg",
            list: ["Precise diagnoses and effective treatments.", "Compassionate, personalized care.", "Long-term health management."]
        },
        surgery: {
            title: "Advanced Surgical Care",
            desc: "High-end surgical procedures from routine to complex surgeries.",
            img: "uploads/pet surgery.jpg",
            list: ["Modern surgical tools and techniques.", "Full pre and post-op support.", "Minimally invasive options available."]
        },
        imaging: {
            title: "Diagnostic Imaging",
            desc: "State-of-the-art X-ray and Ultrasound for accurate insights.",
            img: "uploads/Diagnostic Imaging.jpg",
            list: ["High-resolution diagnostic scans.", "Quick pinpointing of concerns.", "Experienced on-site radiologists."]
        },
        lab: {
            title: "Medical Laboratory",
            desc: "Fast and accurate pathology results to support diagnosis.",
            img: "uploads/pet_laboratry.jpg",
            list: ["Rapid results for faster treatment.", "Full suite of pathology tests.", "Reliable medical reports."]
        }
    };

    function showService(type, btn) {
        document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');

        const display = document.getElementById('service-display');
        display.style.opacity = '0.3';
        
        setTimeout(() => {
            document.getElementById('service-title').innerText = serviceData[type].title;
            document.getElementById('service-desc').innerText = serviceData[type].desc;
            document.getElementById('service-img').src = serviceData[type].img;
            
            const listContainer = document.getElementById('service-list');
            listContainer.innerHTML = '';
            serviceData[type].list.forEach(item => {
                const li = document.createElement('li');
                li.innerText = item;
                listContainer.appendChild(li);
            });
            display.style.opacity = '1';
        }, 200);
    }
    /* --- JAVASCRIPT INFINITE LOOP LOGIC --- */
    const counters = document.querySelectorAll('.stat-number');

    const runCounterLoop = (el) => {
        const target = parseInt(el.getAttribute('data-target'));
        let current = 0;
        const duration = 2000; // Time for the count-up (2 seconds)
        const stepTime = 30; // Update frequency (ms)
        const totalSteps = duration / stepTime;
        const increment = target / totalSteps;

        const update = () => {
            current += increment;
            if (current < target) {
                el.innerText = Math.ceil(current);
                setTimeout(update, stepTime);
            } else {
                el.innerText = target; // Set exact final value
                
                // WAIT 2 SECONDS, THEN RESET
                setTimeout(() => {
                    el.innerText = "0";
                    // Brief pause at 0 before starting again
                    setTimeout(() => runCounterLoop(el), 200); 
                }, 5000); // 2-second hold time
            }
        };
        update();
    };

    // Use Intersection Observer to start the loop only when visible
    const observerOptions = { threshold: 0.8 };
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if(entry.isIntersecting) {
                runCounterLoop(entry.target);
                // We don't unobserve here because we want it to reset if the user scrolls away and back
            }
        });
    }, observerOptions);

    counters.forEach(c => observer.observe(c));
</script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    $('.js-open-modal').on('click', function() {
        var tid = $(this).data('id');
        
        // Reset and Show Loader
        $('#tipModal').fadeIn(200).css('display', 'flex');
        $('#modalImg').hide();
        $('#tipSpinner').show();
        $('body').addClass('modal-open');

        $.ajax({
            url: 'get_tip_details.php',
            type: 'GET',
            data: { id: tid },
            dataType: 'json',
            success: function(data) {
                if(data) {
                    // Populate Text
                    $('#modalTitle').text(data.title);
                    $('#modalCat').text(data.category);
                    $('#modalSummary').text(data.short_description); // NEW: Show summary in modal
                    $('#modalContent').html(data.full_content);
                    $('#modalDate').text(new Date(data.publish_date).toLocaleDateString());

                    // Image Loading Logic
                    var highResImg = $('#modalImg');
                    highResImg.attr('src', data.inside_image);
                    
                    highResImg.on('load', function() {
                        $('#tipSpinner').fadeOut(200);
                        $(this).fadeIn(500); // Smooth image reveal
                    });
                }
            }
        });
    });

    $('.js-close-modal, .modal-overlay').on('click', function(e) {
        if (e.target !== this && !$(e.target).hasClass('js-close-modal')) return;
        $('#tipModal').fadeOut(300);
        $('body').removeClass('modal-open');
    });
});
</script>
<?php include "includes/footer.php"; ?>