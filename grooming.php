<?php
// Include database connection
include 'db.php';
session_start(); 

// Replace 'user_id' with whatever session key you use for successful logins
$is_logged_in = isset($_SESSION['user_id']);
// --- Form Submission Handler ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_booking'])) {
    // 1. Sanitize and retrieve form data
    $owner_name    = mysqli_real_escape_string($conn, $_POST['owner_name']);
    $owner_phone   = mysqli_real_escape_string($conn, $_POST['owner_phone']);
    $owner_email   = mysqli_real_escape_string($conn, $_POST['owner_email']);
    $owner_address = mysqli_real_escape_string($conn, $_POST['owner_address']);
    
    $pet_type      = mysqli_real_escape_string($conn, $_POST['pet_type']);
    $pet_breed     = mysqli_real_escape_string($conn, $_POST['pet_breed']);
    $pet_age       = mysqli_real_escape_string($conn, $_POST['pet_age']);
    
    $service_type  = mysqli_real_escape_string($conn, $_POST['service_type']);
    $package_type  = mysqli_real_escape_string($conn, $_POST['package_type']);
    $preferred_date= mysqli_real_escape_string($conn, $_POST['preferred_date']);
    $preferred_time= mysqli_real_escape_string($conn, $_POST['preferred_time']);

    // 2. Insert into the database
    $sql = "INSERT INTO appoint_grooming 
            (owner_name, owner_phone, owner_email, owner_address, pet_type, pet_breed, pet_age, service_type, package_type, preferred_date, preferred_time) 
            VALUES 
            ('$owner_name', '$owner_phone', '$owner_email', '$owner_address', '$pet_type', '$pet_breed', '$pet_age', '$service_type', '$package_type', '$preferred_date', '$preferred_time')";

    if (mysqli_query($conn, $sql)) {
        $success_msg = "Thank you! Your booking request has been submitted successfully. We will confirm with you shortly.";
    } else {
        $error_msg = "Error: " . mysqli_error($conn);
    }
}
?>
<?php include 'includes/header.php'; ?>

<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css" />
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
    /* --- GLOBAL STYLES --- */
    :root {
      --primary: #518992;  /* Matching Vetcare Teal */
        --navy: #0f1c3f;     /* Matching Vetcare Navy */
        --bg-light: #fffaf5;
        --text-dark: #1e293b;
        --text-muted: #64748b;
    }
    body { font-family: 'Plus Jakarta Sans', sans-serif; color: var(--text-dark); background: #fff; }
    .section-padding { padding: 50px 0; }
    .bg-light-alt { background-color: var(--bg-light); }
    h1, h2, h3, h4, h5, h6 { font-weight: 800; color: var(--secondary-color); }
    p { color: var(--text-muted); line-height: 1.7; }
    .container { max-width: 1200px; margin: 0 auto; padding: 0 20px; }
    .btn-theme {
        background:#df4c1f9f; color: #fff; padding: 14px 32px; border-radius: 50px;
        font-weight: 700; text-decoration: none; display: inline-block; transition: all 0.3s ease;
        border: 2px solid var(--primary-color);
    }
    .btn-theme:hover { background: transparent; color: var(--primary-color); transform: translateY(-3px); }

    /* --- HERO VIDEO SLIDESHOW --- */
    .hero-slider-container {
        position: relative; height: 75vh; min-height: 400px; overflow: hidden; background: #000;
    }
    .video-slide {
        position: absolute; top: 0; left: 0; width: 100%; height: 100%; opacity: 0; transition: opacity 1.5s ease-in-out;
    }
    .video-slide.active { opacity: 1; z-index: 1; }
    .video-slide video {
        width: 100%; height: 100%; object-fit: cover;
    }
    .hero-overlay-content {
        position: absolute; top: 50%; left: 10%; transform: translateY(-50%); z-index: 10;
        color: #fff; max-width: 600px; text-shadow: 0 2px 15px rgba(0,0,0,0.5);
    }
    .hero-overlay-content h1 {
        font-size: 4rem; font-weight: 900; line-height: 1.1; margin-bottom: 20px; color: #fff;
    }
    .hero-overlay-content p { color: #eee; font-size: 1.2rem; margin-bottom: 30px; }
    .slider-dots {
        position: absolute; bottom: 30px; left: 50%; transform: translateX(-50%); z-index: 10; display: flex; gap: 10px;
    }
    .dot {
        width: 14px; height: 14px; border-radius: 50%; background: rgba(167, 33, 33, 0.6); cursor: pointer; transition: 0.3s;
    }
    .dot.active { background:rgba(167, 33, 33, 0.6) ; width: 35px; border-radius: 10px; }
/* --- UPDATED: SQUARE CINEMATIC SERVICE SECTION --- */
.section-padding { padding: 100px 0; }

.section-title { text-align: center; margin-bottom: 5px; }
.section-title h2 { font-size: 3rem; font-weight: 800; letter-spacing: -1.5px; margin-bottom: 5px;}

/* Grid Container for Square Cards */
.services-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 20px;
}

/* Square Card Structure */
.service-card {
    background: #fff;
    border-radius: 35px; /* Rounded corners like Zigly */
    padding: 0; /* Removed padding to allow two-part layout */
    text-align: center;
    box-shadow: 0 15px 35px rgba(0,0,0,0.05);
    transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
    border: 1px solid #edf2f7;
    overflow: hidden;
    aspect-ratio: 1 / 1.1; /* Professional square-ish ratio */
    display: flex;
    flex-direction: column;
}

/* Part 1: Top Image Section with Zooming Effect */
.service-img-container {
    width: 100%;
    height: 60%; /* Top 60% is image */
    overflow: hidden;
    position: relative;
    background: #f8fafc;
}

.service-card img {
    width: 100%;
    height: 100%;
    object-fit: cover; /* Ensures image fills the square perfectly */
    transition: transform 1.2s cubic-bezier(0.165, 0.84, 0.44, 1);
}

/* Part 2: Bottom Text Section */
.service-content {
    padding: 25px 20px;
    flex-grow: 1;
    display: flex;
    flex-direction: column;
    justify-content: center;
    gap: 10px;
}

.service-card h4 { 
    font-size: 1.5rem; 
    margin: 0; 
    color: #0f172a; 
    font-weight: 800;
}

.service-card p {
    font-size: 1rem;
    color: #64748b;
    line-height: 1.5;
    margin: 0;
}

/* --- HOVER ATTRACTION & ZOOMING --- */
.service-card:hover {
    transform: translateY(-15px);
    border-color: #e74c3c; /* Uses your primary red color */
    box-shadow: 0 35px 70px rgba(15, 23, 42, 0.1);
}

.service-card:hover img {
    transform: scale(1.15) rotate(2deg); /* High-end cinematic zoom and tilt */
}

/* Responsive adjustment */
@media (max-width: 768px) {
    .service-card { aspect-ratio: auto; }
}
    /* --- WHY CHOOSE US --- */
    .why-choose-list li {
        margin-bottom: 20px; display: flex; align-items: flex-start;
    }
    .why-choose-list i {
        color: var(--primary-color); font-size: 1.4rem; margin-right: 15px; margin-top: 5px;
    }

    /* --- BOOKING FORM SECTION --- */
    .booking-section {
        background: var(--secondary-color); color: #fff; padding: 80px 0; position: relative;
    }
    .booking-form-container {
        background: #fff; padding: 50px; border-radius: 30px; box-shadow: 0 25px 50px rgba(0,0,0,0.15);
        color: var(--text-dark); margin-top: -150px; position: relative; z-index: 5;
    }
    .form-title { text-align: center; margin-bottom: 40px; }
    .form-title h2 { color: var(--secondary-color); font-size: 2.4rem; }
    .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 25px; }
    .form-group label { display: block; font-weight: 700; margin-bottom: 10px; color: var(--secondary-color); }
    .form-control {
        width: 100%; padding: 8px; border: 2px solid #e2e8f0; border-radius: 12px;
        font-family: inherit; transition: all 0.3s; outline: none;
    }
    .form-control:focus { border-color: var(--primary-color); }
    .form-submit-btn {
        width: 100%; padding: 18px; background: #1e293b; color: #fff; border: none;
        border-radius: 12px; font-weight: 800; font-size: 1.1rem; cursor: pointer; transition: 0.3s;
    }
    .form-submit-btn:hover { background:green; }

    /* --- SWIPER SECTIONS --- */
    .swiper-slide .tip-card {
        background: #fff; border-radius: 20px; overflow: hidden; box-shadow: 0 10px 25px rgba(0,0,0,0.05);
    }
    .tip-card img { width: 100%; height: 220px; object-fit: cover; }
    .tip-card-body { padding: 25px; }
    .tip-card h4 { font-size: 1.2rem; margin-bottom: 10px; }
    .gallery-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 15px; }
    .gallery-item img { width: 100%; border-radius: 15px; height: 250px; object-fit: cover; transition: 0.4s; }
    .gallery-item:hover img { transform: scale(1.05); }

    @media (max-width: 768px) {
        .hero-overlay-content h1 { font-size: 2.5rem; }
        .booking-form-container { margin-top: 0; padding: 30px; }
        .form-grid { grid-template-columns: 1fr; }
        .gallery-grid { grid-template-columns: repeat(2, 1fr); }
    }
</style>

<div class="hero-slider-container">
    <div class="video-slide active">
        <video autoplay muted loop playsinline>
            <source src="uploads\Grooming\4.mp4" type="video/mp4">
        </video>
    </div>
    <div class="video-slide">
        <video autoplay muted loop playsinline>
            <source src="uploads\Grooming\1grooom.mp4" type="video/mp4">
        </video>
    </div>
    <div class="video-slide">
        <video autoplay muted loop playsinline>
            <source src="uploads\Grooming\3.mp4" type="video/mp4">
        </video>
    </div>
    <div class="video-slide">
        <video autoplay muted loop playsinline>
            <source src="uploads\Grooming\5.mp4" type="video/mp4">
        </video>
    </div>
    <div class="video-slide">
        <video autoplay muted loop playsinline>
            <source src="uploads\Grooming\6.mp4" type="video/mp4">
        </video>
    </div>
    <div class="video-slide">
        <video autoplay muted loop playsinline>
            <source src="uploads\Grooming\1.mp4" type="video/mp4">
        </video>
    </div>
    <div class="hero-overlay-content">
        <h1>Shiny Coats, Healthy Skin. <br>Happy Pets.</h1>
        <p>Professional grooming services to make your furry friend look and feel their absolute best.</p>
        <a href="#booking-form" class="btn-theme">Book a Glow-Up Now</a>
    </div>
    <div class="slider-dots">
        <div class="dot active" onclick="changeSlide(0)"></div>
        <div class="dot" onclick="changeSlide(1)"></div>
        <div class="dot" onclick="changeSlide(2)"></div>
        <div class="dot" onclick="changeSlide(3)"></div>
        <div class="dot" onclick="changeSlide(4)"></div>
         <div class="dot" onclick="changeSlide(5)"></div>
    </div>
</div>

<section class="section-padding">
    <div class="container">
        <div class="row align-items-center" style="display: flex; gap: 50px;">
            <div style="flex: 1;">
                <img src="uploads\Grooming\about_groom.jpg" alt="About Us" style="width: 100%; border-radius: 30px;">
            </div>
            <div style="flex: 1;">
                <h2 style="font-size: 2.5rem; margin-bottom: 20px;">About FurryMart Grooming</h2>
                <p style="margin-bottom: 20px;">We are a team of passionate and certified pet stylists dedicated to providing a stress-free and luxurious grooming experience for your pets. We use only premium, pet-safe products to ensure their coat and skin are healthy and radiant.</p>
                <p>From a simple bath to a full breed-specific haircut, we handle every pet with care, patience, and love, as if they were our own.</p>
            </div>
        </div>
    </div>
</section>

<section class="section-padding bg-light-alt">
    <div class="container">
        <div class="section-title">
            <h2>Our Premium Services</h2>
            <p>Pampering your pet from nose to tail.</p>
        </div>
        
        <div class="services-grid">
            <div class="service-card">
                <div class="service-img-container">
                    <img src="uploads/Grooming/B_B.jpg" alt="Bath & Dry">
                </div>
                <div class="service-content">
                    <h4>Bath & Blow Dry</h4>
                    <p>A refreshing bath with premium shampoo followed by a gentle.</p>
                </div>
            </div>
            
            <div class="service-card">
                <div class="service-img-container">
                    <img src="uploads/Grooming/pethaircut.jfif" alt="Haircut">
                </div>
                <div class="service-content">
                    <h4>Full Styling & Cut</h4>
                    <p>Breed-specific or custom haircut by our expert stylists.</p>
                </div>
            </div>

            <div class="service-card">
                <div class="service-img-container">
                    <img src="uploads/Grooming/pawcare.jfif" alt="Nail Care">
                </div>
                <div class="service-content">
                    <h4>Paw & Nail Care</h4>
                    <p>Safe nail trimming, filing, and paw pad moisturizing.</p>
                </div>
            </div>

            <div class="service-card">
                <div class="service-img-container">
                    <img src="uploads/Grooming/despa.jpg" alt="Spa">
                </div>
                <div class="service-content">
                    <h4>De-shedding Spa</h4>
                    <p>Special treatment to reduce shedding and healthy coat.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="section-padding">
    <div class="container">
        <div class="row align-items-center" style="display: flex; gap: 50px;">
            <div style="flex: 1;">
                <h2 style="font-size: 2.5rem; margin-bottom: 30px;">Why Choose Us?</h2>
                <ul class="why-choose-list" style="list-style: none; padding: 0;">
                    <li><i class="fas fa-check-circle"></i> <div><strong>Certified & Experienced Stylists:</strong> Our team is trained in handling all breeds and temperaments.</div></li>
                    <li><i class="fas fa-check-circle"></i> <div><strong>Premium, Safe Products:</strong> We use only high-quality, hypoallergenic shampoos and conditioners.</div></li>
                    <li><i class="fas fa-check-circle"></i> <div><strong>Stress-Free Environment:</strong> A calm and clean salon designed for your pet's comfort.</div></li>
                    <li><i class="fas fa-check-circle"></i> <div><strong>Personalized Care:</strong> We tailor our services to meet your pet's specific needs and skin type.</div></li>
                </ul>
            </div>
            <div style="flex: 1;">
                <img src="uploads/Grooming/whyus.jpg" alt="Why Choose Us" style="width: 100%; border-radius: 30px;">
            </div>
        </div>
    </div>
</section>

<section class="booking-section">
    <div class="container" style="text-align: center; margin-bottom: 160px;">
        <h2 style="font-size: 3rem; color: #10294eff; margin-bottom: 20px;">Ready for a Transformation?</h2>
        <?php if($is_logged_in): ?>
            <p style="color: #c41212c4; font-size: 1.4rem;">Your grooming matrix is ready for deployment.</p>
        <?php else: ?>
            <p style="color: #f43f5e; font-size: 1.3rem; font-weight: 800;">[ PARENT AUTHENTICATION REQUIRED ]</p>
        <?php endif; ?>
    </div>
</section>

<section style="padding-bottom: 80px; position: relative;" id="booking-form">
    <div class="container">
        
        <div class="booking-form-container" style="<?php echo !$is_logged_in ? 'filter: blur(10px); pointer-events: none; opacity: 0.7;' : ''; ?>">
            <div class="form-title">
                <h2>Book Your Appointment</h2>
                <p>Fill in the details below to schedule a session.</p>
            </div>
            
            <form method="POST" action="#booking-form">
                <div class="form-grid">
                    <div class="form-group">
                        <label>Your Name *</label>
                        <input type="text" name="owner_name" class="form-control" required placeholder="John Doe">
                    </div>
                    <div class="form-group">
                        <label>Phone Number *</label>
                        <input type="tel" name="owner_phone" class="form-control" required placeholder="+1 234 567 890">
                    </div>
                    <div class="form-group">
                        <label>Email Address</label>
                        <input type="email" name="owner_email" class="form-control" placeholder="john@example.com">
                    </div>
                    <div class="form-group">
                        <label>Address</label>
                        <input type="text" name="owner_address" class="form-control" placeholder="Your Area/City">
                    </div>
                </div>

                <hr style="margin: 40px 0; border-color: #eee;">

                <div class="form-grid">
                    <div class="form-group">
                        <label>Pet Type *</label>
                        <select name="pet_type" class="form-control" required>
                            <option value="">Select Pet Type</option>
                            <option value="Dog">Dog</option>
                            <option value="Cat">Cat</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Pet Breed</label>
                        <input type="text" name="pet_breed" class="form-control" placeholder="e.g., Golden Retriever">
                    </div>
                    <div class="form-group">
                        <label>Pet's Age</label>
                        <input type="text" name="pet_age" class="form-control" placeholder="e.g., 2 years">
                    </div>
                </div>
                
                <hr style="margin: 40px 0; border-color: #eee;">

                <div class="form-grid">
                    <div class="form-group">
                        <label>Service Type *</label>
                        <select name="service_type" class="form-control" required>
                            <option value="">Select Service</option>
                            <option value="Bath & Dry">Bath & Dry</option>
                            <option value="Full Grooming">Full Grooming (Haircut)</option>
                            <option value="Nail Clipping">Nail Clipping</option>
                            <option value="Spa Treatment">Spa Treatment</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Package Type</label>
                        <select name="package_type" class="form-control">
                            <option value="Basic">Basic</option>
                            <option value="Premium">Premium</option>
                            <option value="Luxury">Luxury Spa</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Preferred Date *</label>
                        <input type="date" name="preferred_date" class="form-control" required min="<?php echo date('Y-m-d'); ?>">
                    </div>
                    <div class="form-group">
                        <label>Preferred Time *</label>
                        <input type="time" name="preferred_time" class="form-control" required>
                    </div>
                </div>

                <div style="margin-top: 40px;">
                    <button type="submit" name="submit_booking" class="form-submit-btn">SUBMIT BOOKING REQUEST</button>
                </div>
            </form>
        </div>

        <?php if(!$is_logged_in): ?>
        <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: 100; display: flex; align-items: center; justify-content: center;">
             <div class="animate__animated animate__zoomIn" style="background: #0f172a; color: white; padding: 50px; border-radius: 35px; text-align: center; box-shadow: 0 30px 60px rgba(0,0,0,0.4); max-width: 500px;">
                <i class="fas fa-lock" style="font-size: 4rem; color: #e74c3c; margin-bottom: 25px;"></i>
                <h3 style="color: white; margin-bottom: 15px; font-size: 1.8rem;">Grooming Restricted</h3>
                <p style="color: #cbd5e1; margin-bottom: 30px;">Only registered FurryMart Parents can book grooming sessions. Please log in to proceed.</p>
                <a href="login.php" class="btn-theme" style="width: 100%; padding: 15px 0;">Login to Unlock</a>
                <p style="margin-top: 20px; font-size: 0.9rem;">New here? <a href="register.php" style="color: #e74c3c;">Register now</a></p>
             </div>
        </div>
        <?php endif; ?>

    </div>
</section>
<section class="section-padding bg-light-alt">
    <div class="container">
        <div class="section-title">
            <h2>Pet Grooming Tips</h2>
            <p>Expert advice to keep your pet healthy at home.</p>
        </div>
        <div class="swiper tips-swiper">
            <div class="swiper-wrapper">
                <div class="swiper-slide">
                    <div class="tip-card">
                        <img src="uploads/grooming/brushing.jpg" alt="Tip 1">
                        <div class="tip-card-body">
                            <h4>Brushing is Key</h4>
                            <p>Regular brushing prevents matting and distributes natural oils for a shiny coat.</p>
                        </div>
                    </div>
                </div>
                <div class="swiper-slide">
                    <div class="tip-card">
                        <img src="uploads/grooming/PAW.jpg" alt="Tip 2">
                        <div class="tip-card-body">
                            <h4>Don't Forget the Paws</h4>
                            <p>Check and clean their paws regularly to prevent irritation from dirt or debris.</p>
                        </div>
                    </div>
                </div>
                 <div class="swiper-slide">
                    <div class="tip-card">
                        <img src="uploads/grooming/DENTAL.jpg" alt="Tip 3">
                        <div class="tip-card-body">
                            <h4>Dental Hygiene</h4>
                            <p>Brushing their teeth or using dental chews helps prevent gum disease.</p>
                        </div>
                    </div>
                </div>
                <div class="swiper-slide">
                    <div class="tip-card">
                        <img src="uploads/grooming/YOUNG.jpg" alt="Tip 4">
                        <div class="tip-card-body">
                            <h4>Start Young</h4>
                            <p>Get your puppy or kitten used to grooming early to make it a positive experience.</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="swiper-pagination"></div>
        </div>
    </div>
</section>

<section class="section-padding">
    <div class="container">
        <div class="section-title">
            <h2>Happy Moments</h2>
            <p>Some of our furry customers looking their best!</p>
        </div>
        <div class="gallery-grid">
            <div class="gallery-item"><img src="uploads/grooming/HAPP1.JPG" alt="Happy Pet"></div>
            <div class="gallery-item"><img src="uploads/grooming/HAPP2.JPG" alt="Happy Pet"></div>
             <div class="gallery-item"><img src="uploads/grooming/HAPP3.JPG" alt="Happy Pet"></div>
            <div class="gallery-item"><img src="uploads/grooming/HAPP4.JPG" alt="Happy Pet"></div>
            <div class="gallery-item"><img src="uploads/grooming/HAPP5.JPG" alt="Happy Pet"></div>
            <div class="gallery-item"><img src="uploads/grooming/HAPP6.JPG" alt="Happy Pet"></div>
            <div class="gallery-item"><img src="uploads/grooming/HAPP7.JPG" alt="Happy Pet"></div>
            <div class="gallery-item"><img src="uploads/grooming/HAPP8.JPG" alt="Happy Pet"></div>
        </div>
    </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js"></script>
<script>
    // --- VIDEO SLIDESHOW LOGIC ---
    let currentSlide = 0;
    const slides = document.querySelectorAll('.video-slide');
    const dots = document.querySelectorAll('.dot');
    const totalSlides = slides.length;
    const slideInterval = 60000; // 1 minute per slide

    function changeSlide(n) {
        slides[currentSlide].classList.remove('active');
        dots[currentSlide].classList.remove('active');
        currentSlide = (n + totalSlides) % totalSlides;
        slides[currentSlide].classList.add('active');
        dots[currentSlide].classList.add('active');
    }

    function nextSlide() { changeSlide(currentSlide + 1); }
    let slideTimer = setInterval(nextSlide, slideInterval);

    // Reset timer on manual dot click
    dots.forEach((dot, index) => {
        dot.addEventListener('click', () => {
            clearInterval(slideTimer);
            changeSlide(index);
            slideTimer = setInterval(nextSlide, slideInterval);
        });
    });

    // --- SWIPER FOR TIPS ---
    new Swiper('.tips-swiper', {

        slidesPerView: 1, spaceBetween: 30, pagination: { el: '.swiper-pagination', clickable: true },
        breakpoints: {
            768: { slidesPerView: 2 },
            1024: { slidesPerView: 3 }
        }
    });

    // --- SWEETALERT SUCCESS/ERROR MESSAGES ---
    <?php if (isset($success_msg)): ?> 
        Swal.fire({ title: 'Success!', text: '<?php echo $success_msg; ?>', icon: 'success', confirmButtonColor: '#e74c3c' });
    <?php endif; ?>
    <?php if (isset($error_msg)): ?>
        Swal.fire({ title: 'Error!', text: '<?php echo $error_msg; ?>', icon: 'error', confirmButtonColor: '#e74c3c' });
    <?php endif; ?>
</script>

<?php include 'includes/footer.php'; ?>