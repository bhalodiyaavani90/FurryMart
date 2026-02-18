<?php
/*
 * FurryMart Contact Page - Enhanced Interactive Version
 * 
 * Features:
 * 1. WhatsApp Card - Direct click-to-chat with pre-filled message
 * 2. Email Card - Opens email client with pre-filled subject and body
 * 3. Phone Card - Direct click-to-call functionality
 * 4. Location Card - Opens interactive Google Maps modal
 * 
 * All cards are fully clickable with:
 * - Hover animations (scale, rotate, shine effect)
 * - Floating animation when visible
 * - Pulse effect on icons
 * - Click hint tooltips
 * - Smooth transitions
 * 
 * Map Modal Features:
 * - Full-screen interactive Google Maps
 * - Close on ESC key or outside click
 * - Smooth open/close animations
 * - Responsive design
 */

// Include database connection (Ensure path is correct)
include 'db.php'; 
session_start(); 

// MATCHING YOUR GROOMING LOGIC: Check for 'user_id' instead of 'user_logged_in'
$isLoggedIn = isset($_SESSION['user_id']);

// --- Form Submission Handler ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_contact'])) {
    // Sanitize and retrieve form data
    $first_name = mysqli_real_escape_string($conn, $_POST['first_name']);
    $last_name  = mysqli_real_escape_string($conn, $_POST['last_name']);
    $mobile     = mysqli_real_escape_string($conn, $_POST['mobile']);
    $email      = mysqli_real_escape_string($conn, $_POST['email']);
    $subject    = mysqli_real_escape_string($conn, $_POST['subject']);
    $message    = mysqli_real_escape_string($conn, $_POST['message']);
    $user_id    = $_SESSION['user_id'];

    // Insert into contact_queries table
    $sql = "INSERT INTO contact_queries (user_id, first_name, last_name, mobile, email, subject, message) 
            VALUES ('$user_id', '$first_name', '$last_name', '$mobile', '$email', '$subject', '$message')";

    if (mysqli_query($conn, $sql)) {
        $success_msg = "Pawsome! Your query has been successfully submitted.";
    } else {
        $error_msg = "Error: " . mysqli_error($conn);
    }
}
?>

<?php include 'includes/header.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>

<style>
    :root { --pm-accent: #FF8E72; --pm-blue: #AEC6CF; --pm-dark: #1e293b; }
    body { background: #FFF9F5; font-family: 'Segoe UI', sans-serif; }


    /* Hero Video (Audio On) */
    .hero-video-container {
        position: relative; height: 500px; overflow: hidden; display: flex; align-items: center; justify-content: center;
    }
    .hero-video-container video {
        position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);
        min-width: 100%; min-height: 100%; object-fit: cover; z-index: -1;
    }
    .overlay-dark { position: absolute; top:0; left:0; width:100%; height:100%; background: rgba(0,0,0,0.4); }

    /* Form Card Styling with Hanging Title */
    .form-card {
        background: white; 
        max-width: 900px; 
        margin: -100px auto 80px;
        border-radius: 40px; 
        padding: 0; 
        box-shadow: 0 30px 80px rgba(0,0,0,0.15);
        position: relative; 
        z-index: 10;
        overflow: hidden;
    }

    /* Hanging Title Border Effect */
    .form-title-section {
        background: linear-gradient(135deg, #FF8E72 0%, #ff7b5a 100%);
        padding: 40px 50px;
        position: relative;
        overflow: hidden;
    }
    
    .form-title-section::before {
        content: '';
        position: absolute;
        top: 0;
        left: -50%;
        width: 200%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
        animation: shimmer 3s infinite;
    }
    
    @keyframes shimmer {
        0% { transform: translateX(-100%); }
        100% { transform: translateX(100%); }
    }
    
    .form-title-section h2 {
        margin: 0;
        color: white;
        font-size: 2.5rem;
        font-weight: 900;
        text-align: center;
        text-shadow: 0 4px 10px rgba(0,0,0,0.2);
        position: relative;
        z-index: 1;
    }
    
    .form-title-section h2 i {
        margin-right: 15px;
        animation: bounce 2s infinite;
    }
    
    @keyframes bounce {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-10px); }
    }
    
    .form-title-section p {
        margin: 10px 0 0;
        color: rgba(255,255,255,0.95);
        font-size: 1.1rem;
        text-align: center;
        position: relative;
        z-index: 1;
    }
    
    /* Decorative Border Hanging Effect */
    .form-title-section::after {
        content: '';
        position: absolute;
        bottom: -20px;
        left: 50%;
        transform: translateX(-50%);
        width: 80%;
        height: 40px;
        background: linear-gradient(135deg, #FF8E72 0%, #ff7b5a 100%);
        border-radius: 0 0 30px 30px;
        box-shadow: 0 10px 30px rgba(255, 142, 114, 0.4);
    }

    /* Form Content Area */
    .form-content {
        padding: 60px 50px 50px;
    }

    /* Form Fields with Floating Labels */
    .form-grid { 
        display: grid; 
        grid-template-columns: 1fr 1fr; 
        gap: 25px; 
    }
    
    .full-row { 
        grid-column: 1 / -1; 
    }
    
    .form-group {
        position: relative;
    }
    
    .form-group label {
        display: block;
        margin-bottom: 10px;
        font-weight: 700;
        color: #5A5A5A;
        font-size: 0.95rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .form-group label i {
        color: #FF8E72;
        margin-right: 8px;
    }
    
    .form-control {
        width: 100%; 
        padding: 16px 20px; 
        border: 2px solid #e2e8f0; 
        border-radius: 15px; 
        font-size: 1rem;
        font-family: 'Segoe UI', sans-serif;
        transition: all 0.3s ease;
        background: #f8fafc;
    }
    
    .form-control:focus {
        outline: none;
        border-color: #FF8E72;
        background: white;
        box-shadow: 0 0 0 4px rgba(255, 142, 114, 0.1);
        transform: translateY(-2px);
    }
    
    .form-control:hover {
        border-color: #AEC6CF;
    }
    
    textarea.form-control {
        resize: vertical;
        min-height: 120px;
    }
    
    /* Submit Button */
    .btn-submit {
        width: 100%; 
        background: linear-gradient(135deg, #FF8E72 0%, #ff7b5a 100%);
        color: white; 
        padding: 20px; 
        border: none;
        border-radius: 50px; 
        font-size: 1.2rem; 
        font-weight: 800; 
        cursor: pointer; 
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        box-shadow: 0 10px 30px rgba(255, 142, 114, 0.3);
        text-transform: uppercase;
        letter-spacing: 1px;
    }
    
    .btn-submit:hover { 
        background: linear-gradient(135deg, #ff7b5a 0%, #FF8E72 100%);
        transform: translateY(-5px) scale(1.02);
        box-shadow: 0 15px 40px rgba(255, 142, 114, 0.5);
    }
    
    .btn-submit:active {
        transform: translateY(-2px) scale(0.98);
    }
    
    .btn-submit i {
        margin-left: 10px;
        transition: transform 0.3s ease;
    }
    
    .btn-submit:hover i {
        transform: translateX(5px);
    }

    /* Custom SweetAlert Button Styles */
    .btn-call-confirm {
        background: linear-gradient(135deg, #f43f5e, #dc2626) !important;
        color: white !important;
        border: none !important;
        padding: 15px 35px !important;
        border-radius: 50px !important;
        font-size: 1.1rem !important;
        font-weight: 700 !important;
        cursor: pointer !important;
        transition: all 0.3s ease !important;
        box-shadow: 0 5px 15px rgba(244, 63, 94, 0.3) !important;
        margin: 10px 8px !important;
    }
    
    .btn-call-confirm:hover {
        background: linear-gradient(135deg, #dc2626, #f43f5e) !important;
        transform: translateY(-2px) !important;
        box-shadow: 0 8px 20px rgba(244, 63, 94, 0.4) !important;
    }
    
    .btn-call-cancel {
        background: #e2e8f0 !important;
        color: #64748b !important;
        border: none !important;
        padding: 15px 35px !important;
        border-radius: 50px !important;
        font-size: 1.1rem !important;
        font-weight: 700 !important;
        cursor: pointer !important;
        transition: all 0.3s ease !important;
        margin: 10px 8px !important;
    }
    
    .btn-call-cancel:hover {
        background: #cbd5e1 !important;
        transform: translateY(-2px) !important;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .form-grid {
            grid-template-columns: 1fr;
        }
        
        .form-title-section h2 {
            font-size: 2rem;
        }
        
        .form-content {
            padding: 40px 30px 30px;
        }
    }

    /* Lock Overlay Style (Matching Grooming) */
    .lock-box {
        background: #0f172a; color: white; padding: 50px; border-radius: 35px;
        text-align: center; box-shadow: 0 30px 60px rgba(0,0,0,0.4); max-width: 500px;
    }
    .staggered-contact-section {
    padding: 80px 0;
    background-color: #fff9f5; /* Matching FurryMart bg */
}

/* Section Header - Centered */
.section-header-top {
    text-align: center; /* This centers the heading */
    margin-bottom: 50px;
    max-width: 800px;
    margin-left: auto;
    margin-right: auto;
}
.section-header-top h2 {
    font-size: 2.2rem;
    color: #5A5A5A; /* FurryMart dark text */
    margin-bottom: 15px;
    font-weight: 700;
}
.section-header-top p {
    color: #888;
    line-height: 1.6;
}

/* Grid Layout */
.colorful-cards-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 30px;
    margin-top: 50px;
}

/* Card Styling */
.colorful-card {
    background: #fff;
    padding: 35px 25px;
    border-radius: 30px;
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    box-shadow: 0 10px 30px rgba(0,0,0,0.05);
    /* More complex transition for a "bouncy" feel */
    transition: all 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    border: 3px solid transparent;
    opacity: 0; /* Hidden for animation */
    cursor: pointer;
    text-decoration: none;
    color: inherit;
    position: relative;
    overflow: hidden;
}

/* Shine effect on hover */
.colorful-card::before {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: linear-gradient(
        45deg,
        transparent,
        transparent 40%,
        rgba(255, 255, 255, 0.3) 50%,
        transparent 60%,
        transparent
    );
    transform: translateX(-100%) translateY(-100%) rotate(45deg);
    transition: transform 0.6s ease;
}

.colorful-card:hover::before {
    transform: translateX(100%) translateY(100%) rotate(45deg);
}

.colorful-card:active {
    transform: translateY(-12px) scale(1.02);
}

/* Dynamic Hover Effect */
.colorful-card:hover {
    /* Scale, rotate, and translate for more movement */
    transform: translateY(-15px) scale(1.05) rotate(1deg);
    box-shadow: 0 20px 40px rgba(0,0,0,0.15);
    z-index: 1; /* Bring to front */
}

.colorful-card:active {
    transform: translateY(-12px) scale(1.02);
}

/* Icon Wrap */
.card-icon-wrap {
    width: 70px;
    height: 70px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 30px;
    color: #fff;
    margin-bottom: 20px;
    box-shadow: 0 8px 15px rgba(0,0,0,0.1);
    transition: transform 0.3s ease;
    position: relative;
}

/* Pulse animation for icons */
.card-icon-wrap::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 100%;
    height: 100%;
    border-radius: 50%;
    border: 2px solid currentColor;
    opacity: 0;
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% {
        transform: translate(-50%, -50%) scale(1);
        opacity: 1;
    }
    100% {
        transform: translate(-50%, -50%) scale(1.5);
        opacity: 0;
    }
}

/* Icon Animation on Card Hover */
.colorful-card:hover .card-icon-wrap {
    transform: scale(1.15) rotate(-5deg);
    animation: wiggle 0.5s ease;
}

@keyframes wiggle {
    0%, 100% { transform: scale(1.15) rotate(-5deg); }
    25% { transform: scale(1.15) rotate(-8deg); }
    75% { transform: scale(1.15) rotate(-2deg); }
}

/* Color Themes */
.card-whatsapp .card-icon-wrap { background: #B0E57C; } /* FurryMart green */
.card-whatsapp:hover { border-color: #B0E57C; }

.card-email .card-icon-wrap { background: #AEC6CF; } /* FurryMart blue */
.card-email:hover { border-color: #AEC6CF; }

.card-call .card-icon-wrap { background: #F4C2C2; } /* FurryMart pink */
.card-call:hover { border-color: #F4C2C2; }

.card-address .card-icon-wrap { background: #C3B1E1; } /* FurryMart purple */
.card-address:hover { border-color: #C3B1E1; }

/* Text Styling */
.card-details h4 { 
    font-size: 1.4rem; 
    color: #5A5A5A; 
    margin-bottom: 10px; 
    font-weight: 800;
    transition: color 0.3s ease;
}

.card-details a, .card-details p { 
    text-decoration: none; 
    color: #64748b; 
    font-weight: 500; 
    font-size: 1rem; 
    line-height: 1.5;
    transition: all 0.3s ease;
}

.colorful-card:hover .card-details h4 {
    transform: scale(1.05);
}

.colorful-card:hover .card-details p {
    transform: translateY(-2px);
}

.working-hours { 
    font-size: 0.85rem !important; 
    color: #94a3b8 !important; 
    margin-top: 5px; 
}

/* Click hint badge */
.colorful-card::after {
    content: 'Click to connect';
    position: absolute;
    bottom: 10px;
    left: 50%;
    transform: translateX(-50%) translateY(10px);
    background: rgba(0, 0, 0, 0.8);
    color: white;
    padding: 5px 15px;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    opacity: 0;
    pointer-events: none;
    transition: all 0.3s ease;
    white-space: nowrap;
}

.colorful-card:hover::after {
    opacity: 1;
    transform: translateX(-50%) translateY(0);
}

/* Map Modal */
.map-modal {
    display: none;
    position: fixed;
    z-index: 9999;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.8);
    backdrop-filter: blur(5px);
    animation: fadeIn 0.3s ease;
}

.map-modal-content {
    position: relative;
    background: white;
    margin: 3% auto;
    width: 90%;
    max-width: 1200px;
    height: 85vh;
    border-radius: 25px;
    overflow: hidden;
    box-shadow: 0 30px 90px rgba(0,0,0,0.5);
    animation: slideDown 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

@keyframes slideDown {
    from { transform: translateY(-100px); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
}

.map-modal-header {
    background: linear-gradient(135deg, #C3B1E1, #9b87c0);
    padding: 25px 30px;
    color: white;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.map-modal-header h3 {
    margin: 0;
    font-size: 1.8rem;
    font-weight: 800;
}

.map-modal-header h3 i {
    margin-right: 12px;
}

.close-modal {
    background: rgba(255, 255, 255, 0.2);
    border: none;
    color: white;
    font-size: 28px;
    width: 45px;
    height: 45px;
    border-radius: 50%;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
}

.close-modal:hover {
    background: rgba(255, 255, 255, 0.3);
    transform: rotate(90deg);
}

.map-container {
    width: 100%;
    height: calc(100% - 80px);
}

.map-container iframe {
    width: 100%;
    height: 100%;
    border: none;
}

@media (max-width: 768px) {
    .map-modal-content {
        width: 95%;
        height: 80vh;
        margin: 10% auto;
    }
    
    .map-modal-header h3 {
        font-size: 1.4rem;
    }
    
    .colorful-cards-grid {
        grid-template-columns: 1fr;
        gap: 20px;
    }
    
    .colorful-card {
        padding: 30px 20px;
    }
    
    .card-details h4 {
        font-size: 1.2rem;
    }
}

/* Floating animation for cards */
@keyframes float {
    0%, 100% {
        transform: translateY(0px);
    }
    50% {
        transform: translateY(-10px);
    }
}

.colorful-card.is-visible {
    animation: float 3s ease-in-out infinite;
}

.colorful-card:nth-child(1).is-visible {
    animation-delay: 0s;
}

.colorful-card:nth-child(2).is-visible {
    animation-delay: 0.2s;
}

.colorful-card:nth-child(3).is-visible {
    animation-delay: 0.4s;
}

.colorful-card:nth-child(4).is-visible {
    animation-delay: 0.6s;
}

/* Visible class for JS */
.is-visible { opacity: 1 !important; }
.disclaimer-brand-section {
    padding: 100px 0;
    background: linear-gradient(to bottom, #fff9f5, #fff);
}

/* The Glowing Border Wrapper */
.theory-card-wrapper {
    position: relative;
    padding: 4px; /* Space for the border */
    background: linear-gradient(135deg, #FF8E72, #AEC6CF, #B0E57C, #FF8E72);
    background-size: 300% 300%;
    border-radius: 40px;
    animation: gradient-border 6s ease infinite;
    max-width: 1000px;
    margin: 0 auto;
}

/* The Internal Card */
.theory-card {
    background: #ffffff;
    padding: 60px;
    border-radius: 36px;
    display: flex;
    flex-direction: column;
    gap: 40px;
    text-align: center;
}

/* Brand Theory Styling */
.brand-theory h3 {
    font-size: 2.2rem;
    color: #5A5A5A;
    margin-bottom: 20px;
    font-weight: 800;
}
.brand-theory h3 i {
    color: #FF8E72;
    margin-right: 10px;
}
.brand-theory p {
    font-size: 1.15rem;
    color: #64748b;
    line-height: 1.8;
}

/* Decorative Divider */
.theory-divider {
    height: 2px;
    width: 100px;
    background: #eee;
    margin: 0 auto;
    position: relative;
}
.theory-divider::after {
    content: '♥';
    position: absolute;
    top: 50%; left: 50%;
    transform: translate(-50%, -50%);
    background: #fff;
    padding: 0 10px;
    color: #FF8E72;
}

/* Disclaimer Styling */
.disclaimer-title {
    color: #e74c3c; /* Alert Red */
    font-size: 1.5rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 2px;
    margin-bottom: 15px;
}
.disclaimer-text {
    font-size: 1rem;
    color: #94a3b8;
    font-style: italic;
    line-height: 1.6;
    max-width: 800px;
    margin: 0 auto;
}

.verified-tag {
    margin-top: 25px;
    display: inline-flex;
    align-items: center;
    gap: 10px;
    padding: 10px 25px;
    background: #f0fdf4;
    color: #166534;
    border-radius: 50px;
    font-weight: 700;
    font-size: 0.9rem;
    border: 1px solid #bbf7d0;
}

/* Animated Border Animation */
@keyframes gradient-border {
    0% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
}

@media (max-width: 768px) {
    .theory-card { padding: 30px; }
    .brand-theory h3 { font-size: 1.8rem; }
}

    .btn-call-confirm,
    .btn-call-cancel {
        padding: 12px 30px;
        border-radius: 10px;
        font-size: 1rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        margin: 0 8px;
    }

    .btn-call-confirm {
        background: linear-gradient(135deg, #f43f5e 0%, #ec4899 100%);
        color: white;
        border: none;
    }

    .btn-call-confirm:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(244, 63, 94, 0.4);
    }

    .btn-call-cancel {
        background: #e2e8f0;
        color: #64748b;
        border: none;
    }

    .btn-call-cancel:hover {
        background: #cbd5e1;
        transform: translateY(-2px);
    }
</style>
<div class="header-spacer"></div>

<section class="hero-video-container">
    <video id="heroVideo" autoplay loop playsinline>
        <source src="uploads/furrymart3.mp4" type="video/mp4">
    </video>
    <div class="overlay-dark"></div>
</section>

<section class="container" style="position: relative;">
    <div class="form-card animate__animated animate__fadeInUp" 
         style="<?php echo !$isLoggedIn ? 'filter: blur(10px); pointer-events: none; opacity: 0.7;' : ''; ?>">
        
        <!-- Hanging Title Section -->
        <div class="form-title-section">
            <h2><i class="fas fa-envelope-open-text"></i>Get In Touch</h2>
            <p>We'd love to hear from you! Fill out the form below and we'll get back to you shortly.</p>
        </div>
        
        <!-- Form Content -->
        <div class="form-content">
            <form method="POST" id="contactForm">
                <div class="form-grid">
                    <div class="form-group">
                        <label><i class="fas fa-user"></i>First Name*</label>
                        <input type="text" name="first_name" class="form-control" placeholder="Enter your first name" required>
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-user"></i>Last Name*</label>
                        <input type="text" name="last_name" class="form-control" placeholder="Enter your last name" required>
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-phone"></i>Mobile Number*</label>
                        <input type="tel" name="mobile" class="form-control" placeholder="Enter your mobile number" required>
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-envelope"></i>Email Address*</label>
                        <input type="email" name="email" class="form-control" placeholder="Enter your email address" required>
                    </div>
                    <div class="form-group full-row">
                        <label><i class="fas fa-tag"></i>Subject*</label>
                        <input type="text" name="subject" class="form-control" placeholder="What is this regarding?" required>
                    </div>
                    <div class="form-group full-row">
                        <label><i class="fas fa-comment-dots"></i>How can FurryMart help you?*</label>
                        <textarea name="message" class="form-control" placeholder="Tell us more about your query..." required></textarea>
                    </div>
                    <div class="full-row">
                        <button type="submit" name="submit_contact" class="btn-submit">Submit Query <i class="fas fa-paper-plane"></i></button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <?php if(!$isLoggedIn): ?>
    <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: 100; display: flex; align-items: center; justify-content: center;">
         <div class="lock-box animate__animated animate__zoomIn">
            <i class="fas fa-lock" style="font-size: 4rem; color: #e74c3c; margin-bottom: 25px;"></i>
            <h3>Access Restricted</h3>
            <p style="color: #cbd5e1; margin-bottom: 30px;">Please log in to your FurryMart account to submit queries and get help.</p>
            <a href="login.php" class="btn-submit" style="text-decoration: none; display: block;">Login to Unlock</a>
         </div>
    </div>
    <?php endif; ?>
</section>
<section class="staggered-contact-section section-padding">
    <div class="container">
        <div class="section-header-top animate-on-scroll" data-animation="animate__fadeInDown">
            <h2>Get in Touch Directly</h2>
            <p>Our friendly team is here to help you and your pet 24/7!</p>
        </div>

        <div class="colorful-cards-grid">
            <!-- WhatsApp Card -->
            <a href="https://api.whatsapp.com/send?phone=919925708543&text=Hello!%20Welcome%20to%20FurryMart%20%F0%9F%90%BE%0A%0AI%27m%20your%20FurryMart%20Assistant.%20How%20can%20I%20help%20you%20and%20your%20furry%20friend%20today%3F%0A%0AYou%20can%20ask%20me%20about%3A%0A%F0%9F%9B%92%20Products%20%26%20Services%0A%F0%9F%93%A6%20Order%20Status%0A%F0%9F%92%B0%20Pricing%20%26%20Offers%0A%F0%9F%93%8D%20Store%20Locations%0A%F0%9F%8E%82%20Birthday%20Parties%0A%E2%9C%A8%20Grooming%20Services%0A%0APlease%20type%20your%20query%20below%20%F0%9F%91%87" 
               target="_blank"
               class="colorful-card card-whatsapp animate-on-scroll" 
               data-animation="animate__zoomIn" 
               data-delay="0.1s">
                <div class="card-icon-wrap">
                    <i class="fab fa-whatsapp"></i>
                </div>
                <div class="card-details">
                    <h4>WhatsApp Bot</h4>
                    <p style="color: #25D366; font-weight: 600;">+91 99257 08543</p>
                    <p style="font-size: 0.85rem; margin-top: 8px;">Instant Automated Response!</p>
                </div>
            </a>

            <!-- Email Card -->
            <div onclick="openEmailModal()" 
                 class="colorful-card card-email animate-on-scroll" 
                 data-animation="animate__zoomIn" 
                 data-delay="0.3s">
                <div class="card-icon-wrap">
                    <i class="far fa-envelope"></i>
                </div>
                <div class="card-details">
                    <h4>Email Support</h4>
                    <p style="color: #0ea5e9; font-weight: 600;">customercare@furrymart.com</p>
                    <p style="font-size: 0.85rem; margin-top: 8px;">Click to send email</p>
                </div>
            </div>

            <!-- Phone Call Card -->
            <div onclick="openCallModal()" 
                 class="colorful-card card-call animate-on-scroll" 
                 data-animation="animate__zoomIn" 
                 data-delay="0.5s">
                <div class="card-icon-wrap">
                    <i class="fas fa-phone-alt"></i>
                </div>
                <div class="card-details">
                    <h4>Request For call Us Now</h4>
                    <p style="color: #f43f5e; font-weight: 600;">+91 99257 08543</p>
                    <p class="working-hours">Click to initiate call</p>
                </div>
            </div>

            <!-- Location/Map Card -->
            <div class="colorful-card card-address animate-on-scroll" 
                 data-animation="animate__zoomIn" 
                 data-delay="0.7s"
                 onclick="openMapModal()">
                <div class="card-icon-wrap">
                    <i class="fas fa-map-marker-alt"></i>
                </div>
                <div class="card-details">
                    <h4>Visit Us</h4>
                    <p>FurryMart HQ, Pastel Plaza</p>
                    <p style="font-size: 0.85rem; margin-top: 8px; color: #8b5cf6; font-weight: 600;">
                        <i class="fas fa-map"></i> Click to view map
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Map Modal -->
<div id="mapModal" class="map-modal">
    <div class="map-modal-content animate__animated">
        <div class="map-modal-header">
            <h3><i class="fas fa-map-marked-alt"></i> FurryMart Location</h3>
            <button class="close-modal" onclick="closeMapModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="map-container">
            <!-- Google Maps Embed - Replace with actual coordinates -->
            <iframe 
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3504.7352771896377!2d77.24926931508056!3d28.549325982445943!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x390ce3e564c2d1e1%3A0x8e3f5a8cb2a8c3f8!2sNehru%20Place%2C%20New%20Delhi%2C%20Delhi!5e0!3m2!1sen!2sin!4v1234567890123!5m2!1sen!2sin" 
                allowfullscreen="" 
                loading="lazy" 
                referrerpolicy="no-referrer-when-downgrade">
            </iframe>
        </div>
    </div>
</div>

<!-- Email Modal -->
<div id="emailModal" class="map-modal" style="display: none;">
    <div class="map-modal-content animate__animated" style="height: auto; max-width: 600px;">
        <div class="map-modal-header" style="background: linear-gradient(135deg, #AEC6CF, #8db3c7);">
            <h3><i class="far fa-envelope"></i> Send Email to FurryMart</h3>
            <button class="close-modal" onclick="closeEmailModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div style="padding: 40px;">
            <form id="emailForm" onsubmit="sendEmailAjax(event)">
                <div style="margin-bottom: 20px;">
                    <label style="display: block; font-weight: 700; margin-bottom: 8px; color: #1e293b;">
                        <i class="fas fa-user"></i> Your Name *
                    </label>
                    <input type="text" id="email_name" name="name" required
                           style="width: 100%; padding: 12px 15px; border: 2px solid #e2e8f0; border-radius: 10px; font-size: 1rem;"
                           placeholder="Enter your name">
                </div>
                <div style="margin-bottom: 20px;">
                    <label style="display: block; font-weight: 700; margin-bottom: 8px; color: #1e293b;">
                        <i class="fas fa-envelope"></i> Your Email *
                    </label>
                    <input type="email" id="email_address" name="email" required
                           style="width: 100%; padding: 12px 15px; border: 2px solid #e2e8f0; border-radius: 10px; font-size: 1rem;"
                           placeholder="Enter your email">
                </div>
                <div style="margin-bottom: 25px;">
                    <label style="display: block; font-weight: 700; margin-bottom: 8px; color: #1e293b;">
                        <i class="fas fa-message"></i> Your Message *
                    </label>
                    <textarea id="email_message" name="message" required rows="5"
                              style="width: 100%; padding: 12px 15px; border: 2px solid #e2e8f0; border-radius: 10px; font-size: 1rem; resize: vertical;"
                              placeholder="Tell us how we can help you..."></textarea>
                </div>
                <button type="submit" id="emailSubmitBtn"
                        style="width: 100%; padding: 15px; background: linear-gradient(135deg, #0ea5e9, #0284c7); color: white; border: none; border-radius: 50px; font-size: 1.1rem; font-weight: 700; cursor: pointer; transition: all 0.3s ease;">
                    <i class="fas fa-paper-plane"></i> Send Email
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Call Modal -->
<div id="callModal" class="map-modal" style="display: none;">
    <div class="map-modal-content animate__animated" style="height: auto; max-width: 500px; max-height: 90vh; overflow-y: auto;">
        <div class="map-modal-header" style="background: linear-gradient(135deg, #f43f5e, #dc2626);">
            <h3><i class="fas fa-headset"></i> Request Free Callback</h3>
            <button class="close-modal" onclick="closeCallModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div style="padding: 30px 25px; text-align: center;">
            <!-- Phone Icon & Header -->
            <div style="font-size: 3.5rem; margin-bottom: 15px;">
                <i class="fas fa-phone-volume" style="color: #f43f5e; animation: pulse 2s infinite;"></i>
            </div>
            <h4 style="color: #1e293b; margin-bottom: 10px; font-size: 1.4rem; font-weight: 800;">
                We'll Call You Back! 📞
            </h4>
            <p style="color: #64748b; font-size: 0.95rem; line-height: 1.6; margin-bottom: 20px;">
                Enter your details below. Our customer care team will call you within <strong style="color: #f43f5e;">2-3 minutes</strong>. It's completely FREE!
            </p>
            
            <!-- How It Works -->
            <div style="background: linear-gradient(135deg, #f0fdf4, #dcfce7); padding: 20px; border-radius: 15px; margin-bottom: 20px; border: 2px solid #86efac; text-align: left;">
                <p style="color: #1e293b; font-size: 0.9rem; font-weight: 700; margin-bottom: 12px;">
                    <i class="fas fa-check-circle" style="color: #10b981;"></i> 100% Free Service:
                </p>
                <ol style="color: #15803d; font-size: 0.85rem; line-height: 1.8; margin: 0; padding-left: 20px;">
                    <li>Fill your name & phone number below</li>
                    <li>Click "Request Callback" button</li>
                    <li><strong>We'll call you within 2-3 minutes</strong></li>
                    <li>No charges, no hidden costs!</li>
                </ol>
            </div>
            
            <!-- Call Form -->
            <form id="callForm" onsubmit="initiateCallAjax(event)" style="margin-bottom: 20px;">
                <div style="margin-bottom: 15px; text-align: left;">
                    <label style="display: block; font-weight: 700; margin-bottom: 8px; color: #1e293b; font-size: 0.95rem;">
                        <i class="fas fa-user" style="color: #0ea5e9;"></i> Your Name *
                    </label>
                    <input type="text" id="user_name" name="user_name" required
                           style="width: 100%; padding: 15px; border: 2px solid #e2e8f0; border-radius: 12px; font-size: 1rem;"
                           placeholder="Enter your name">
                </div>
                <div style="margin-bottom: 15px; text-align: left;">
                    <label style="display: block; font-weight: 700; margin-bottom: 8px; color: #1e293b; font-size: 0.95rem;">
                        <i class="fas fa-mobile-alt" style="color: #f43f5e;"></i> Your Phone Number *
                    </label>
                    <input type="tel" id="user_phone" name="user_phone" required
                           pattern="[0-9]{10}"
                           maxlength="10"
                           style="width: 100%; padding: 15px; border: 2px solid #e2e8f0; border-radius: 12px; font-size: 1.1rem; text-align: center; font-weight: 700; letter-spacing: 2px;"
                           placeholder="9876543210">
                    <small style="color: #94a3b8; font-size: 0.8rem; display: block; margin-top: 5px;">
                        <i class="fas fa-shield-alt" style="color: #10b981;"></i> Your info is safe & will never be shared
                    </small>
                </div>
                <button type="submit" id="callSubmitBtn"
                        style="width: 100%; padding: 18px; background: linear-gradient(135deg, #10b981, #059669); color: white; border: none; border-radius: 50px; font-size: 1.2rem; font-weight: 900; cursor: pointer; transition: all 0.3s ease; box-shadow: 0 10px 30px rgba(16, 185, 129, 0.4); text-transform: uppercase; letter-spacing: 1px;">
                    <i class="fas fa-headset" style="margin-right: 8px;"></i>
                    Request FREE Callback
                    <i class="fas fa-arrow-right" style="margin-left: 8px;"></i>
                </button>
            </form>
            
            <!-- Business Hours -->
            <div style="padding: 15px; background: #f8fafc; border-radius: 12px; border: 1px solid #e2e8f0;">
                <p style="font-size: 0.9rem; color: #64748b; margin-bottom: 5px;">
                    <i class="fas fa-clock" style="color: #0ea5e9;"></i> <strong>Available</strong>
                </p>
                <p style="font-size: 0.85rem; color: #64748b; margin: 0;">
                    Monday - Saturday: 9:30 AM - 6:30 PM
                </p>
            </div>
        </div>
    </div>
</div>

<style>
@keyframes ring {
    0%, 100% { transform: rotate(0deg); }
    10%, 30% { transform: rotate(-10deg); }
    20%, 40% { transform: rotate(10deg); }
}
</style>

<section class="disclaimer-brand-section section-padding">
    <div class="container">
        <div class="theory-card-wrapper animate-on-scroll" data-animation="animate__fadeInUp">
            <div class="theory-card">
                <div class="brand-theory">
                    <h3><i class="fas fa-paw"></i> The FurryMart Philosophy</h3>
                    <p>
                        At <strong>FurryMart</strong>, we believe that pets aren't just animals; they are cherished family members who deserve the absolute best. Our mission is to bridge the gap between premium pet care and accessibility. Every product and service we offer is vetted by experts to ensure your furry friends lead a healthy, joyful, and dignified life. From specialized grooming to nutritional excellence, we are committed to being your partner in every wag, purr, and chirp.
                    </p>
                </div>

                <div class="theory-divider"></div>

                <div class="disclaimer-content">
                    <h4 class="disclaimer-title">Official Disclaimer</h4>
                    <p class="disclaimer-text">
                        "Watch out!— To maintain the highest security for our FurryMart Parents, please do not share sensitive bank details, UPI pins, CVV, or OTPs with anyone claiming to be a FurryMart representative. We will never ask for this information over the phone or email. Beware of fraudulent numbers and phishing attempts. For any legitimate issues, please only use the verified contact methods listed on this official website."
                    </p>
                    <div class="verified-tag">
                        <i class="fas fa-shield-alt"></i> Verified FurryMart Safety Protocol
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<script>
    // Browser Autoplay Fix for Audio
    const video = document.getElementById('heroVideo');
    window.addEventListener('load', () => {
        video.play().catch(() => {
            video.muted = true; // Play muted if blocked
            video.play();
            document.addEventListener('click', () => { video.muted = false; }, { once: true });
        });
    });

  </script>
  <script>
    // Success Popup with Superb Animation
    <?php if (isset($success_msg)): ?> 
        Swal.fire({
            title: 'Pawsome!',
            text: '<?php echo $success_msg; ?>',
            icon: 'success',
            iconColor: '#FF8E72',
            confirmButtonColor: '#FF8E72',
            showClass: {
                popup: 'animate__animated animate__backInDown' // Smooth bounce down
            },
            hideClass: {
                popup: 'animate__animated animate__fadeOutUp' // Smooth fade out
            }
        });
    <?php endif; ?>
</script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    const observerOptions = {
        threshold: 0.15 
    };

    const contactObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const el = entry.target;
                const anim = el.getAttribute('data-animation');
                const del = el.getAttribute('data-delay') || '0s';

                el.style.animationDelay = del;
                el.classList.add('animate__animated', anim, 'is-visible');
                
                // Unobserve after animating
                contactObserver.unobserve(el);
            }
        });
    }, observerOptions);

    // Watch all elements with the animation class
    document.querySelectorAll('.animate-on-scroll').forEach(item => {
        contactObserver.observe(item);
    });
});

// Email Modal Functions
function openEmailModal() {
    const modal = document.getElementById('emailModal');
    modal.style.display = 'block';
    document.body.style.overflow = 'hidden';
    const modalContent = modal.querySelector('.map-modal-content');
    modalContent.classList.add('animate__zoomIn');
}

function closeEmailModal() {
    const modal = document.getElementById('emailModal');
    const modalContent = modal.querySelector('.map-modal-content');
    modalContent.classList.remove('animate__zoomIn');
    modalContent.classList.add('animate__zoomOut');
    setTimeout(() => {
        modal.style.display = 'none';
        document.body.style.overflow = '';
        modalContent.classList.remove('animate__zoomOut');
        document.getElementById('emailForm').reset();
    }, 300);
}

// Call Modal Functions
function openCallModal() {
    const modal = document.getElementById('callModal');
    modal.style.display = 'block';
    document.body.style.overflow = 'hidden';
    const modalContent = modal.querySelector('.map-modal-content');
    modalContent.classList.add('animate__zoomIn');
}

function closeCallModal() {
    const modal = document.getElementById('callModal');
    const modalContent = modal.querySelector('.map-modal-content');
    modalContent.classList.remove('animate__zoomIn');
    modalContent.classList.add('animate__zoomOut');
    setTimeout(() => {
        modal.style.display = 'none';
        document.body.style.overflow = '';
        modalContent.classList.remove('animate__zoomOut');
        document.getElementById('callForm').reset();
    }, 300);
}

// Send Quick Query via AJAX
function sendEmailAjax(event) {
    event.preventDefault();
    
    const submitBtn = document.getElementById('emailSubmitBtn');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';
    submitBtn.disabled = true;
    
    const formData = new FormData(document.getElementById('emailForm'));
    
    fetch('submit_quick_query.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                title: 'Query Submitted! 📧',
                html: data.message,
                icon: 'success',
                iconColor: '#0ea5e9',
                confirmButtonColor: '#0ea5e9',
                confirmButtonText: 'Great!',
                showClass: {
                    popup: 'animate__animated animate__zoomIn'
                }
            });
            closeEmailModal();
        } else {
            Swal.fire({
                title: 'Oops!',
                text: data.message,
                icon: 'error',
                confirmButtonColor: '#f43f5e'
            });
        }
    })
    .catch(error => {
        Swal.fire({
            title: 'Error!',
            text: 'Failed to submit query. Please try WhatsApp or call us.',
            icon: 'error',
            confirmButtonColor: '#f43f5e'
        });
    })
    .finally(() => {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
}

// Request FREE Callback
function initiateCallAjax(event) {
    event.preventDefault();
    
    const userName = document.getElementById('user_name').value.trim();
    const userPhone = document.getElementById('user_phone').value;
    const submitBtn = document.getElementById('callSubmitBtn');
    const originalText = submitBtn.innerHTML;
    
    // Validate
    if (!userName || userName.length < 2) {
        Swal.fire({
            title: 'Name Required',
            text: 'Please enter your name',
            icon: 'error',
            confirmButtonColor: '#10b981'
        });
        return;
    }
    
    if (!userPhone || userPhone.length !== 10) {
        Swal.fire({
            title: 'Invalid Number',
            text: 'Please enter a valid 10-digit mobile number',
            icon: 'error',
            confirmButtonColor: '#10b981'
        });
        return;
    }
    
    // Show processing state
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending Request...';
    submitBtn.disabled = true;
    
    // Send to backend
    const formData = new FormData();
    formData.append('user_name', userName);
    formData.append('user_phone', userPhone);
    
    fetch('submit_callback_request.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                title: 'Request Received! ✅',
                html: `
                    <div style="text-align: center; padding: 25px;">
                        <i class="fas fa-check-circle" style="color: #10b981; font-size: 4rem; margin-bottom: 20px; animation: pulse 2s infinite;"></i>
                        <h3 style="color: #1e293b; font-size: 1.3rem; font-weight: 800; margin-bottom: 15px;">
                            We'll Call You Shortly!
                        </h3>
                        <div style="background: #f0fdf4; padding: 20px; border-radius: 12px; border: 2px solid #86efac; margin: 20px 0;">
                            <p style="color: #64748b; font-size: 1rem; line-height: 1.8; margin: 0;">
                                <i class="fas fa-user" style="color: #0ea5e9;"></i> Name: <strong style="color: #1e293b;">${userName}</strong><br>
                                <i class="fas fa-mobile-alt" style="color: #10b981; margin-top: 10px;"></i> Phone: <strong style="color: #10b981;">+91 ${userPhone}</strong>
                            </p>
                        </div>
                        <div style="background: #fff9f5; padding: 15px; border-radius: 10px; border-left: 4px solid #f59e0b; margin-top: 20px;">
                            <p style="font-size: 0.95rem; color: #92400e; line-height: 1.6; margin: 0;">
                                <i class="fas fa-clock" style="color: #f59e0b;"></i> <strong>Please keep your phone ready!</strong><br>
                                <span style="font-size: 0.85rem; color: #78350f;">Our customer care team will call you within 2-3 minutes</span>
                            </p>
                        </div>
                        <p style="font-size: 0.9rem; color: #94a3b8; margin-top: 20px;">
                            <i class="fas fa-shield-alt" style="color: #10b981;"></i> Your information is safe with us
                        </p>
                    </div>
                `,
                icon: 'success',
                iconColor: '#10b981',
                confirmButtonColor: '#10b981',
                confirmButtonText: '<i class="fas fa-check"></i> Got It!',
                showClass: {
                    popup: 'animate__animated animate__bounceIn'
                },
                allowOutsideClick: false
            });
            closeCallModal();
            document.getElementById('callForm').reset();
        } else {
            Swal.fire({
                title: 'Oops!',
                html: data.message || 'Unable to process request. Please try again.',
                icon: 'error',
                confirmButtonColor: '#f43f5e',
                footer: '<a href="tel:+919925708543" style="color: #f43f5e; font-weight: 600;"><i class="fas fa-phone"></i> Or call us directly: +91 99257 08543</a>'
            });
        }
    })
    .catch(error => {
        Swal.fire({
            title: 'Connection Error',
            text: 'Unable to send request. Please try again or call us directly.',
            icon: 'error',
            confirmButtonColor: '#f43f5e',
            footer: '<a href="tel:+919925708543" style="color: #f43f5e; font-weight: 600;"><i class="fas fa-phone"></i> Call: +91 99257 08543</a>'
        });
    })
    .finally(() => {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
}

// Map Modal Functions
function openMapModal() {
    const modal = document.getElementById('mapModal');
    modal.style.display = 'block';
    document.body.style.overflow = 'hidden'; // Prevent background scrolling
    
    // Add animation class
    const modalContent = modal.querySelector('.map-modal-content');
    modalContent.classList.add('animate__zoomIn');
}

function closeMapModal() {
    const modal = document.getElementById('mapModal');
    const modalContent = modal.querySelector('.map-modal-content');
    
    // Add exit animation
    modalContent.classList.remove('animate__zoomIn');
    modalContent.classList.add('animate__zoomOut');
    
    setTimeout(() => {
        modal.style.display = 'none';
        document.body.style.overflow = ''; // Restore scrolling
        modalContent.classList.remove('animate__zoomOut');
    }, 300);
}

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('mapModal');
    if (event.target == modal) {
        closeMapModal();
    }
}

// Close modal with Escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        const modal = document.getElementById('mapModal');
        if (modal.style.display === 'block') {
            closeMapModal();
        }
    }
});
</script>

<?php include 'includes/footer.php'; ?>