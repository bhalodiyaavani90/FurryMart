<?php 
include "db.php"; 
include "includes/header.php"; 

$show_popup = false;
$user_name_entered = "";

if(isset($_POST['submit_feedback'])){
    $user_name_entered = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $rating = (int)$_POST['rating'];
    $text = mysqli_real_escape_string($conn, $_POST['message']);

    $sql = "INSERT INTO customer_feedbacks (user_name, user_email, rating, feedback_text, status) 
            VALUES ('$user_name_entered', '$email', '$rating', '$text', 'pending')";
    if(mysqli_query($conn, $sql)){
        $show_popup = true;
    }
}
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
    :root { 
        --primary: #518992; 
        --navy: #0f1c3f; 
        --star: #f43f5e; 
        --glass: rgba(255, 255, 255, 0.98);
        --h-gap: 60px; /* Perfect horizontal space between textboxes */
        --v-gap: 40px; /* Perfect vertical space between rows */
    }

    body { background: #f4f7fa; font-family: 'Plus Jakarta Sans', sans-serif; color: var(--navy); }

    /* Animated Hero Background */
    .hero-glow-section { 
        padding: 120px 10% 200px; 
        background: radial-gradient(circle at top right, #1a3a5f, #0f1c3f); 
        text-align: center; 
        color: white;
        clip-path: polygon(0 0, 100% 0, 100% 85%, 0 100%);
    }

    .hero-glow-section h1 { font-size: 3.8rem; font-weight: 800; letter-spacing: -2px; margin-bottom: 20px; }

    /* MATHEMATICALLY PERFECT FORM CONTAINER */
    .master-form-container {
        max-width: 1100px; /* Slightly wider for better field spread */
        margin: -140px auto 100px;
        background: var(--glass);
        backdrop-filter: blur(20px);
        padding: 80px;
        border-radius: 45px;
        box-shadow: 0 40px 100px rgba(15, 28, 63, 0.15);
        border: 2px solid #ffffff;
        position: relative;
        animation: zoomInSoft 0.8s cubic-bezier(0.34, 1.56, 0.64, 1);
    }

    @keyframes zoomInSoft { from { opacity: 0; transform: scale(0.95) translateY(40px); } to { opacity: 1; transform: scale(1) translateY(0); } }

    /* GRID WITH INCREASED HORIZONTAL GAP */
    .feedback-form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr; /* Balanced Horizontal Columns */
        column-gap: var(--h-gap); /* Precise horizontal space between textboxes */
        row-gap: var(--v-gap);    /* Precise vertical space between rows */
    }

    .grid-full-width { grid-column: span 2; }

    .label-alpha { 
        font-size: 0.75rem; font-weight: 800; text-transform: uppercase; 
        letter-spacing: 2px; color: #64748b; margin-bottom: 15px; display: block; 
    }

    .input-kinetic {
        width: 100%; padding: 20px 25px; border: 2px solid #eef2f6; 
        border-radius: 22px; font-size: 1.05rem; font-weight: 600; 
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1); 
        outline: none; background: #fdfdfe;
    }

    .input-kinetic:focus { 
        border-color: var(--primary); 
        box-shadow: 0 15px 35px rgba(81, 137, 146, 0.15); 
        transform: translateY(-5px); 
    }

    /* Professional Star Logic */
    .star-rating-box { 
        display: flex; flex-direction: row-reverse; justify-content: center; 
        gap: 15px; background: #f8fafc; padding: 25px; border-radius: 30px; border: 1px dashed #cbd5e1;
    }
    .star-rating-box label { font-size: 50px; color: #d1d5db; cursor: pointer; transition: 0.3s; }
    .star-rating-box input { display: none; }
    .star-rating-box input:checked ~ label, 
    .star-rating-box label:hover, 
    .star-rating-box label:hover ~ label { color: var(--star); transform: scale(1.2) rotate(8deg); text-shadow: 0 0 20px rgba(244, 63, 94, 0.3); }

    /* HIGH-IMPACT KINETIC BUTTON */
    .btn-holographic {
        width: 100%; background: var(--navy); color: white; border: none; 
        border-radius: 25px; padding: 24px; font-weight: 800; text-transform: uppercase; 
        letter-spacing: 4px; font-size: 1.2rem; cursor: pointer; 
        transition: all 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        box-shadow: 0 20px 40px rgba(15, 28, 63, 0.25);
        margin-top: 20px;
    }

    .btn-holographic:hover { 
        transform: translateY(-10px) scale(1.02); 
        background: var(--primary); 
        box-shadow: 0 25px 50px rgba(81, 137, 146, 0.4); 
    }

    /* Entrance Delays for Staggered Animation */
    .stagger-1 { animation: fadeInUp 0.5s ease both 0.2s; }
    .stagger-2 { animation: fadeInUp 0.5s ease both 0.3s; }
    .stagger-3 { animation: fadeInUp 0.5s ease both 0.4s; }
    .stagger-4 { animation: fadeInUp 0.5s ease both 0.5s; }
    .stagger-5 { animation: fadeInUp 0.5s ease both 0.6s; }

    @media (max-width: 991px) { 
        .feedback-form-grid { grid-template-columns: 1fr; } 
        .grid-full-width { grid-column: span 1; } 
        .master-form-container { padding: 40px 30px; }
    }
</style>

<section class="hero-glow-section">
    <div class="animate__animated animate__fadeInDown">
        <h1>Voices of FurryMart</h1>
        <p style="font-weight: 600; opacity: 0.8;">Join our hall of fame by sharing your verified experience.</p>
    </div>
</section>

<div class="container">
    <div class="master-form-container">
        <div style="text-align: center; margin-bottom: 60px;">
            <div class="animate__animated animate__fadeIn" style="display:inline-block; padding: 15px 30px; background: #f0f7f8; border-radius: 50px; color: var(--primary); font-weight: 800; font-size: 0.8rem; letter-spacing: 2px;">
                <i class="fas fa-shield-alt me-2"></i> SECURE FEEDBACK CHANNEL
            </div>
            <h2 class="animate__animated animate__fadeInUp" style="font-weight: 800; margin-top: 25px; font-size: 2.2rem;">Detailed Parent Report</h2>
        </div>

        <form method="POST" id="feedbackFormMain">
            <div class="feedback-form-grid">
                <div class="form-group stagger-1">
                    <label class="label-alpha">Parent Identity</label>
                    <input type="text" name="name" class="input-kinetic" placeholder="Enter Full Name" required>
                </div>
                
                <div class="form-group stagger-2">
                    <label class="label-alpha">Communication Link (Email)</label>
                    <input type="email" name="email" class="input-kinetic" placeholder="Enter Email Address" required>
                </div>
                
                <div class="grid-full-width text-center stagger-3">
                    <label class="label-alpha">Experience Calibrator (Star Rating)</label>
                    <div class="star-rating-box">
                        <input type="radio" name="rating" value="5" id="p5" required><label for="p5">★</label>
                        <input type="radio" name="rating" value="4" id="p4"><label for="p4">★</label>
                        <input type="radio" name="rating" value="3" id="p3"><label for="p3">★</label>
                        <input type="radio" name="rating" value="2" id="p2"><label for="p2">★</label>
                        <input type="radio" name="rating" value="1" id="p1"><label for="p1">★</label>
                    </div>
                </div>

                <div class="grid-full-width stagger-4">
                    <label class="label-alpha">Detailed Furry Story</label>
                    <textarea name="message" class="input-kinetic" rows="6" placeholder="Document your journey with us..." required></textarea>
                </div>
            </div>

            <div class="stagger-5">
                <button type="submit" name="submit_feedback" class="btn-holographic">
                    Authorize & Deploy Feedback
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// --- ULTRA-ANIMATED SUCCESS POPUP ENGINE ---
<?php if($show_popup): ?>
    Swal.fire({
        title: 'Transmission Successful!',
        text: 'Hello <?php echo $user_name_entered; ?>! Your feedback has been successfully logged into the FurryMart matrix.',
        icon: 'success',
        background: '#fff',
        confirmButtonColor: '#0f1c3f',
        confirmButtonText: 'RETURN TO MISSION CONTROL',
        showClass: {
            popup: 'animate__animated animate__bounceIn'
        },
        hideClass: {
            popup: 'animate__animated animate__fadeOutDown'
        },
        customClass: {
            title: 'fw-800',
            popup: 'border-radius-40 shadow-ultra'
        },
        backdrop: `rgba(15, 28, 63, 0.4)`
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = 'index.php';
        }
    });
<?php endif; ?>
</script>

<?php include "includes/footer.php"; ?>