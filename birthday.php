<?php 
include "db.php";
if (session_status() === PHP_SESSION_NONE) { session_start(); }

// Check if user is logged in
$is_logged_in = isset($_SESSION['email']);
?>

<?php include "includes/header.php"; ?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
    :root {
        --pink: #ec4899;
        --purple: #8b5cf6;
        --navy: #0f172a;
        --primary: #518992;
    }
    
    body { 
        background: #f8fafc;
        overflow-x: hidden;
    }

    /* Hero Section with Video */
    .birthday-hero {
        position: relative;
        background: linear-gradient(135deg, #ec4899 0%, #8b5cf6 100%);
        padding: 100px 5% 120px;
        overflow: hidden;
        color: white;
        min-height: 600px;
        display: flex;
        align-items: center;
    }

    .hero-video-bg {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        opacity: 0.8;
        filter: brightness(0.8);
        z-index: 0;
    }

    .hero-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(255, 255, 255, 0.2);
        z-index: 1;
    }

    .birthday-hero::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg width="100" height="100" xmlns="http://www.w3.org/2000/svg"><text x="10" y="50" font-size="40" fill="rgba(255,255,255,0.05)">🎈</text><text x="60" y="80" font-size="30" fill="rgba(255,255,255,0.05)">🎂</text></svg>') repeat;
        animation: float 30s linear infinite;
        z-index: 2;
    }

    @keyframes float {
        from { background-position: 0 0; }
        to { background-position: 100px 100px; }
    }

    .hero-content {
        position: relative;
        z-index: 3;
        max-width: 1200px;
        margin: 0 auto;
        text-align: center;
    }

    .hero-emoji {
        font-size: 100px;
        animation: bounce 2s infinite;
        display: inline-block;
        margin-bottom: 20px;
    }

    @keyframes bounce {
        0%, 100% { transform: translateY(0) scale(1); }
        50% { transform: translateY(-30px) scale(1.1); }
    }

    .hero-title {
        font-size: 64px;
        font-weight: 900;
        margin-bottom: 20px;
        text-shadow: 0 10px 30px rgba(0,0,0,0.3);
        line-height: 1.2;
    }

    .hero-subtitle {
        font-size: 24px;
        font-weight: 600;
        margin-bottom: 30px;
        opacity: 0.95;
    }

    .hero-description {
        font-size: 18px;
        max-width: 800px;
        margin: 0 auto 40px;
        line-height: 1.8;
        opacity: 0.9;
    }

    .hero-stats {
        display: flex;
        justify-content: center;
        gap: 50px;
        margin-top: 50px;
        flex-wrap: wrap;
    }

    .stat-item {
        text-align: center;
    }

    .stat-number {
        font-size: 48px;
        font-weight: 900;
        display: block;
        margin-bottom: 8px;
    }

    .stat-label {
        font-size: 16px;
        opacity: 0.9;
        font-weight: 600;
    }

    /* Features Section */
    .features-section {
        padding: 80px 5%;
        background: white;
        position: relative;
        margin-top: -50px;
        z-index: 2;
    }

    .section-container {
        max-width: 1200px;
        margin: 0 auto;
    }

    .section-header {
        text-align: center;
        margin-bottom: 60px;
    }

    .section-title {
        font-size: 42px;
        font-weight: 900;
        background: linear-gradient(135deg, var(--pink), var(--purple));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        margin-bottom: 15px;
    }

    .section-subtitle {
        font-size: 18px;
        color: #64748b;
        font-weight: 600;
    }

    /* Gallery Section */
    .gallery-section {
        padding: 80px 5%;
        background: linear-gradient(135deg, #fdf2f8, #f3e8ff);
    }

    .gallery-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 25px;
        margin-top: 40px;
    }

    .gallery-item {
        position: relative;
        border-radius: 25px;
        overflow: hidden;
        aspect-ratio: 1;
        cursor: pointer;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }

    .gallery-item:hover {
        transform: translateY(-10px) scale(1.02);
        box-shadow: 0 20px 50px rgba(236, 72, 153, 0.3);
    }

    .gallery-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }

    .gallery-item:hover img {
        transform: scale(1.1);
    }

    .gallery-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(135deg, rgba(236, 72, 153, 0.9), rgba(139, 92, 246, 0.9));
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.4s;
        color: white;
        font-size: 24px;
        font-weight: 800;
    }

    .gallery-item:hover .gallery-overlay {
        opacity: 1;
    }

    /* How It Works Section */
    .how-it-works {
        padding: 80px 5%;
        background: white;
    }

    .steps-container {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 40px;
        margin-top: 50px;
    }

    .step-card {
        text-align: center;
        padding: 40px 30px;
        background: linear-gradient(135deg, #fdf2f8, #f3e8ff);
        border-radius: 25px;
        position: relative;
        transition: all 0.3s;
        border: 3px solid transparent;
    }

    .step-card:hover {
        border-color: var(--pink);
        transform: translateY(-10px);
        box-shadow: 0 15px 40px rgba(236, 72, 153, 0.2);
    }

    .step-number {
        width: 70px;
        height: 70px;
        background: linear-gradient(135deg, var(--pink), var(--purple));
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 32px;
        font-weight: 900;
        margin: 0 auto 25px;
        box-shadow: 0 10px 30px rgba(236, 72, 153, 0.4);
    }

    .step-title {
        font-size: 22px;
        font-weight: 800;
        color: var(--navy);
        margin-bottom: 15px;
    }

    .step-desc {
        font-size: 15px;
        color: #64748b;
        line-height: 1.6;
    }

    /* Party Packages Section */
    .packages-section {
        padding: 80px 5%;
        background: linear-gradient(135deg, #f8fafc, #e0f2fe);
    }

    .packages-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 30px;
        margin-top: 50px;
    }

    .package-card {
        background: white;
        border-radius: 30px;
        padding: 40px 30px 30px;
        text-align: center;
        position: relative;
        border: 3px solid #f1f5f9;
        transition: all 0.4s;
        overflow: hidden;
        height: 650px;
        display: flex;
        flex-direction: column;
    }

    .package-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 5px;
        background: linear-gradient(135deg, var(--pink), var(--purple));
        transform: scaleX(0);
        transition: transform 0.4s;
    }

    .package-card:hover::before {
        transform: scaleX(1);
    }

    .package-card:hover {
        border-color: var(--purple);
        transform: translateY(-10px);
        box-shadow: 0 20px 50px rgba(139, 92, 246, 0.3);
    }

    .package-icon {
        font-size: 60px;
        margin-bottom: 20px;
        display: block;
    }

    .package-name {
        font-size: 28px;
        font-weight: 900;
        background: linear-gradient(135deg, var(--pink), var(--purple));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        margin-bottom: 15px;
    }

    .package-price {
        font-size: 42px;
        font-weight: 900;
        color: var(--navy);
        margin-bottom: 25px;
    }

    .package-price span {
        font-size: 18px;
        color: #64748b;
        font-weight: 600;
    }

    .package-features {
        list-style: none;
        padding: 0;
        margin: 20px 0;
        text-align: left;
        flex: 1;
        overflow-y: auto;
        max-height: 280px;
        padding-right: 10px;
    }
    
    .package-features::-webkit-scrollbar {
        width: 6px;
    }
    
    .package-features::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 10px;
    }
    
    .package-features::-webkit-scrollbar-thumb {
        background: linear-gradient(135deg, var(--pink), var(--purple));
        border-radius: 10px;
    }
    
    .package-features::-webkit-scrollbar-thumb:hover {
        background: linear-gradient(135deg, #db2777, #7c3aed);
    }

    .package-features li {
        padding: 12px 0;
        color: #334155;
        font-weight: 600;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .package-features li i {
        color: var(--pink);
        font-size: 18px;
    }

    .package-btn {
        width: 100%;
        padding: 15px;
        background: linear-gradient(135deg, var(--pink), var(--purple));
        color: white;
        border: none;
        border-radius: 15px;
        font-weight: 800;
        font-size: 16px;
        cursor: pointer;
        transition: all 0.3s;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-top: auto;
        flex-shrink: 0;
    }

    .package-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 30px rgba(236, 72, 153, 0.5);
    }

    /* Testimonials Section */
    .testimonials-section {
        padding: 80px 5%;
        background: white;
    }

    .testimonials-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
        gap: 30px;
        margin-top: 50px;
    }

    .testimonial-card {
        background: linear-gradient(135deg, #fdf2f8, #f3e8ff);
        padding: 40px;
        border-radius: 25px;
        position: relative;
        transition: all 0.3s;
    }

    .testimonial-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 40px rgba(139, 92, 246, 0.2);
    }

    .quote-icon {
        font-size: 50px;
        color: var(--pink);
        opacity: 0.3;
        margin-bottom: 20px;
    }

    .testimonial-text {
        font-size: 16px;
        line-height: 1.8;
        color: #334155;
        font-style: italic;
        margin-bottom: 25px;
    }

    .testimonial-author {
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .author-avatar {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--pink), var(--purple));
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 24px;
        font-weight: 900;
    }

    .author-info h4 {
        margin: 0 0 5px 0;
        font-weight: 800;
        color: var(--navy);
    }

    .author-info p {
        margin: 0;
        color: #64748b;
        font-size: 14px;
    }

    .rating {
        color: #fbbf24;
        font-size: 18px;
        margin-top: 10px;
    }

    /* FAQ Section */
    .faq-section {
        padding: 80px 5%;
        background: linear-gradient(135deg, #fdf2f8, #f3e8ff);
    }

    .faq-container {
        max-width: 900px;
        margin: 50px auto 0;
    }

    .faq-item {
        background: white;
        border-radius: 20px;
        margin-bottom: 20px;
        overflow: hidden;
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        transition: all 0.3s;
    }

    .faq-item:hover {
        box-shadow: 0 10px 30px rgba(236, 72, 153, 0.15);
    }

    .faq-question {
        padding: 25px 30px;
        cursor: pointer;
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-weight: 800;
        color: var(--navy);
        font-size: 18px;
        transition: all 0.3s;
    }

    .faq-question:hover {
        color: var(--pink);
    }

    .faq-question i {
        font-size: 20px;
        transition: transform 0.3s;
    }

    .faq-item.active .faq-question i {
        transform: rotate(180deg);
    }

    .faq-answer {
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.4s ease;
        padding: 0 30px;
    }

    .faq-item.active .faq-answer {
        max-height: 500px;
        padding: 0 30px 25px;
    }

    .faq-answer p {
        color: #64748b;
        line-height: 1.8;
        font-size: 15px;
    }

    .features-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 35px;
        margin-bottom: 60px;
    }

    .feature-card {
        background: linear-gradient(135deg, #fdf4ff 0%, #fae8ff 100%);
        padding: 40px 30px;
        border-radius: 25px;
        text-align: center;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        border: 2px solid transparent;
    }

    .feature-card:hover {
        transform: translateY(-15px) scale(1.05);
        box-shadow: 0 20px 50px rgba(139, 92, 246, 0.3);
        border-color: var(--purple);
    }

    .feature-icon {
        font-size: 60px;
        margin-bottom: 20px;
        display: inline-block;
        animation: bounce 3s infinite;
    }

    .feature-title {
        font-size: 22px;
        font-weight: 800;
        color: var(--navy);
        margin-bottom: 12px;
    }

    .feature-desc {
        font-size: 15px;
        color: #64748b;
        line-height: 1.7;
    }

    /* Booking Section */
    .booking-section {
        padding: 80px 5%;
        background: linear-gradient(135deg, #fdf4ff 0%, #e0e7ff 100%);
    }

    .booking-container {
        max-width: 800px;
        margin: 0 auto;
        background: white;
        padding: 60px;
        border-radius: 35px;
        box-shadow: 0 30px 80px rgba(0,0,0,0.1);
        position: relative;
        overflow: hidden;
    }

    .booking-container::before {
        content: '🎊';
        position: absolute;
        font-size: 200px;
        opacity: 0.05;
        top: -50px;
        right: -50px;
        transform: rotate(-15deg);
    }

    .form-header {
        text-align: center;
        margin-bottom: 40px;
    }

    .form-title {
        font-size: 36px;
        font-weight: 900;
        background: linear-gradient(135deg, var(--pink), var(--purple));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        margin-bottom: 10px;
    }

    .form-subtitle {
        font-size: 16px;
        color: #64748b;
    }

    .form-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 25px;
        margin-bottom: 25px;
    }

    .form-group {
        display: flex;
        flex-direction: column;
    }

    .form-group.full-width {
        grid-column: 1 / -1;
    }

    .form-label {
        font-size: 14px;
        font-weight: 700;
        color: var(--navy);
        margin-bottom: 10px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .form-label i {
        color: var(--pink);
    }

    .form-input {
        padding: 15px 20px;
        border: 2px solid #e2e8f0;
        border-radius: 15px;
        font-size: 15px;
        font-weight: 600;
        transition: all 0.3s;
        background: #f8fafc;
    }

    .form-input:focus {
        outline: none;
        border-color: var(--purple);
        background: white;
        box-shadow: 0 0 0 4px rgba(139, 92, 246, 0.1);
    }

    .form-select {
        padding: 15px 20px;
        border: 2px solid #e2e8f0;
        border-radius: 15px;
        font-size: 15px;
        font-weight: 600;
        background: #f8fafc;
        cursor: pointer;
        transition: all 0.3s;
    }

    .form-select:focus {
        outline: none;
        border-color: var(--purple);
        background: white;
        box-shadow: 0 0 0 4px rgba(139, 92, 246, 0.1);
    }

    .form-textarea {
        padding: 15px 20px;
        border: 2px solid #e2e8f0;
        border-radius: 15px;
        font-size: 15px;
        font-weight: 600;
        background: #f8fafc;
        resize: vertical;
        min-height: 120px;
        font-family: inherit;
        transition: all 0.3s;
    }

    .form-textarea:focus {
        outline: none;
        border-color: var(--purple);
        background: white;
        box-shadow: 0 0 0 4px rgba(139, 92, 246, 0.1);
    }

    .submit-btn {
        width: 100%;
        padding: 20px;
        background: linear-gradient(135deg, var(--pink), var(--purple));
        color: white;
        border: none;
        border-radius: 15px;
        font-size: 18px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 1px;
        cursor: pointer;
        transition: all 0.3s;
        box-shadow: 0 10px 30px rgba(139, 92, 246, 0.4);
        margin-top: 20px;
    }

    .submit-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 15px 40px rgba(139, 92, 246, 0.6);
    }

    .submit-btn:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        transform: none;
    }

    /* Login Required Overlay */
    .login-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(255, 255, 255, 0.98);
        backdrop-filter: blur(10px);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        z-index: 10;
        border-radius: 35px;
    }

    .login-icon {
        font-size: 80px;
        margin-bottom: 25px;
        background: linear-gradient(135deg, var(--pink), var(--purple));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .login-title {
        font-size: 32px;
        font-weight: 900;
        color: var(--navy);
        margin-bottom: 15px;
    }

    .login-text {
        font-size: 16px;
        color: #64748b;
        margin-bottom: 30px;
        max-width: 400px;
        text-align: center;
    }

    .login-btn {
        padding: 15px 40px;
        background: linear-gradient(135deg, var(--pink), var(--purple));
        color: white;
        text-decoration: none;
        border-radius: 50px;
        font-weight: 800;
        font-size: 16px;
        transition: all 0.3s;
        box-shadow: 0 10px 30px rgba(139, 92, 246, 0.4);
    }

    .login-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 15px 40px rgba(139, 92, 246, 0.6);
    }

    /* Success Message */
    .success-message {
        display: none;
        background: linear-gradient(135deg, #10b981, #059669);
        color: white;
        padding: 20px 30px;
        border-radius: 15px;
        margin-bottom: 30px;
        text-align: center;
        font-weight: 700;
        animation: slideDown 0.5s ease;
    }

    @keyframes slideDown {
        from { transform: translateY(-20px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }

    .error-message {
        display: none;
        background: linear-gradient(135deg, #ef4444, #dc2626);
        color: white;
        padding: 20px 30px;
        border-radius: 15px;
        margin-bottom: 30px;
        text-align: center;
        font-weight: 700;
        animation: slideDown 0.5s ease;
    }

    /* Success Modal */
    .success-modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.8);
        backdrop-filter: blur(10px);
        z-index: 10000;
        align-items: center;
        justify-content: center;
        animation: fadeIn 0.3s;
    }

    .success-modal.show {
        display: flex;
    }

    .modal-content {
        background: white;
        border-radius: 35px;
        padding: 50px 40px;
        max-width: 600px;
        width: 90%;
        text-align: center;
        position: relative;
        animation: slideUp 0.5s;
        box-shadow: 0 30px 80px rgba(0,0,0,0.3);
    }

    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    @keyframes slideUp {
        from {
            opacity: 0;
            transform: translateY(50px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .modal-icon {
        font-size: 100px;
        margin-bottom: 25px;
        animation: bounce 1s;
    }

    .modal-title {
        font-size: 42px;
        font-weight: 900;
        background: linear-gradient(135deg, var(--pink), var(--purple));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        margin-bottom: 15px;
    }

    .modal-subtitle {
        font-size: 18px;
        color: #64748b;
        margin-bottom: 35px;
        font-weight: 600;
    }

    .booking-details {
        background: linear-gradient(135deg, #fdf2f8, #f3e8ff);
        border-radius: 25px;
        padding: 30px;
        margin: 30px 0;
        text-align: left;
    }

    .detail-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 15px 0;
        border-bottom: 2px solid rgba(139, 92, 246, 0.1);
    }

    .detail-row:last-child {
        border-bottom: none;
    }

    .detail-label {
        font-weight: 700;
        color: #64748b;
        font-size: 14px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .detail-label i {
        color: var(--purple);
        font-size: 18px;
    }

    .detail-value {
        font-weight: 800;
        color: var(--navy);
        font-size: 16px;
    }

    .status-badge-modal {
        display: inline-block;
        padding: 8px 20px;
        background: linear-gradient(135deg, #fbbf24, #f59e0b);
        color: white;
        border-radius: 25px;
        font-weight: 800;
        font-size: 13px;
        text-transform: uppercase;
    }

    .modal-close-btn {
        width: 100%;
        padding: 18px;
        background: linear-gradient(135deg, var(--pink), var(--purple));
        color: white;
        border: none;
        border-radius: 15px;
        font-size: 16px;
        font-weight: 800;
        cursor: pointer;
        transition: all 0.3s;
        margin-top: 20px;
    }

    .modal-close-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 30px rgba(139, 92, 246, 0.5);
    }

    .modal-note {
        margin-top: 20px;
        padding: 20px;
        background: #f1f5f9;
        border-radius: 15px;
        font-size: 14px;
        color: #64748b;
        line-height: 1.6;
    }

    @media (max-width: 768px) {
        .hero-title { font-size: 40px; }
        .hero-subtitle { font-size: 18px; }
        .form-grid { grid-template-columns: 1fr; }
        .booking-container { padding: 40px 25px; }
        .features-grid { grid-template-columns: 1fr; }
    }
</style>

<!-- Hero Section -->
<div class="birthday-hero animate__animated animate__fadeIn">
    <!-- Video Background - Add your video here -->
    <video class="hero-video-bg" autoplay muted loop playsinline>
        <source src="uploads/birthday-video.mp4" type="video/mp4">
        <!-- Fallback for browsers that don't support video -->
    </video>
    <div class="hero-overlay"></div>
    
    <div class="hero-content">
        <div class="hero-emoji">🎂</div>
        <h1 class="hero-title animate__animated animate__zoomIn">Birthday Party Paradise!</h1>
        <p class="hero-subtitle animate__animated animate__fadeInUp animate__delay-1s">
            🎈 Make Your Pet's Special Day Unforgettable 🎉
        </p>
        <p class="hero-description animate__animated animate__fadeInUp animate__delay-1s">
            Celebrate your furry friend's birthday in style at FurryMart! We offer the perfect venue for an amazing pet birthday party with all the fun, treats, and memories your pet deserves. Book your exclusive party package today!
        </p>
        
        <div class="hero-stats animate__animated animate__fadeInUp animate__delay-2s">
            <div class="stat-item">
                <span class="stat-number">500+</span>
                <span class="stat-label">Happy Parties</span>
            </div>
            <div class="stat-item">
                <span class="stat-number">1000+</span>
                <span class="stat-label">Joyful Pets</span>
            </div>
            <div class="stat-item">
                <span class="stat-number">5⭐</span>
                <span class="stat-label">Perfect Rating</span>
            </div>
        </div>
    </div>
</div>

<!-- Features Section -->
<div class="features-section">
    <div class="section-container">
        <div class="section-header animate__animated animate__fadeInUp">
            <h2 class="section-title">🎊 What's Included</h2>
            <p class="section-subtitle">Everything you need for a pawsome celebration!</p>
        </div>
        
        <div class="features-grid">
            <div class="feature-card animate__animated animate__fadeInUp animate__delay-1s">
                <div class="feature-icon">🎪</div>
                <h3 class="feature-title">Exclusive Venue</h3>
                <p class="feature-desc">Beautifully decorated party space with pet-friendly setup and safety measures</p>
            </div>
            
            <div class="feature-card animate__animated animate__fadeInUp animate__delay-1s">
                <div class="feature-icon">🎂</div>
                <h3 class="feature-title">Pet-Friendly Cake</h3>
                <p class="feature-desc">Delicious, healthy birthday cake specially made for your furry friends</p>
            </div>
            
            <div class="feature-card animate__animated animate__fadeInUp animate__delay-1s">
                <div class="feature-icon">🎁</div>
                <h3 class="feature-title">Party Favors</h3>
                <p class="feature-desc">Fun toys and treats for all attending pets to take home</p>
            </div>
            
            <div class="feature-card animate__animated animate__fadeInUp animate__delay-2s">
                <div class="feature-icon">📸</div>
                <h3 class="feature-title">Photo Booth</h3>
                <p class="feature-desc">Professional pet photography with fun props and instant prints</p>
            </div>
            
            <div class="feature-card animate__animated animate__fadeInUp animate__delay-2s">
                <div class="feature-icon">🎮</div>
                <h3 class="feature-title">Games & Activities</h3>
                <p class="feature-desc">Exciting games and activities to keep all pets entertained</p>
            </div>
            
            <div class="feature-card animate__animated animate__fadeInUp animate__delay-2s">
                <div class="feature-icon">👨‍⚕️</div>
                <h3 class="feature-title">Vet On-Site</h3>
                <p class="feature-desc">Professional veterinarian available throughout the party for safety</p>
            </div>
        </div>
    </div>
</div>

<!-- Booking Section -->
<div class="booking-section">
    <div class="section-container">
        <div class="booking-container">
            <?php if(!$is_logged_in): ?>
            <div class="login-overlay animate__animated animate__fadeIn">
                <div class="login-icon">
                    <i class="fas fa-lock"></i>
                </div>
                <h3 class="login-title">Login Required</h3>
                <p class="login-text">
                    Please login to your FurryMart account to book an amazing birthday party for your pet!
                </p>
                <a href="login.php" class="login-btn">
                    <i class="fas fa-sign-in-alt"></i> Login Now
                </a>
            </div>
            <?php endif; ?>
            
            <div class="form-header">
                <h2 class="form-title">🎉 Book Your Party</h2>
                <p class="form-subtitle">Fill in the details and we'll make it magical!</p>
            </div>
            
            <div id="successMessage" class="success-message"></div>
            <div id="errorMessage" class="error-message"></div>
            
            <form id="birthdayForm" method="POST">
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-paw"></i> Pet's Name *
                        </label>
                        <input type="text" name="pet_name" class="form-input" placeholder="Enter pet's name" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-dog"></i> Pet Type *
                        </label>
                        <select name="pet_type" class="form-select" required>
                            <option value="">Choose Pet Type</option>
                            <option value="Dog">🐕 Dog</option>
                            <option value="Cat">🐱 Cat</option>
                            <option value="Bird">🦜 Bird</option>
                            <option value="Other">🐾 Other</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-calendar-alt"></i> Party Date *
                        </label>
                        <input type="date" name="party_date" class="form-input" min="<?php echo date('Y-m-d', strtotime('+7 days')); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-clock"></i> Party Time *
                        </label>
                        <select name="party_time" class="form-select" required>
                            <option value="">Select Time</option>
                            <option value="10:00 AM - 12:00 PM">10:00 AM - 12:00 PM</option>
                            <option value="12:00 PM - 02:00 PM">12:00 PM - 02:00 PM</option>
                            <option value="02:00 PM - 04:00 PM">02:00 PM - 04:00 PM</option>
                            <option value="04:00 PM - 06:00 PM">04:00 PM - 06:00 PM</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-users"></i> Number of Pets *
                        </label>
                        <input type="number" name="guest_count" class="form-input" placeholder="How many pets?" min="1" max="20" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-phone"></i> Contact Phone *
                        </label>
                        <input type="tel" name="contact_phone" class="form-input" placeholder="+91 XXXXX XXXXX" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-gift"></i> Party Package *
                        </label>
                        <select name="party_package" id="party_package" class="form-select" required>
                            <option value="">Choose Package</option>
                            <option value="Basic Bash - ₹2,499">🎈 Basic Bash - ₹2,499</option>
                            <option value="Premium Party - ₹4,999">🎉 Premium Party - ₹4,999</option>
                            <option value="Royal Celebration - ₹7,999">👑 Royal Celebration - ₹7,999</option>
                        </select>
                    </div>
                    
                    <div class="form-group full-width">
                        <label class="form-label">
                            <i class="fas fa-comments"></i> Special Requests
                        </label>
                        <textarea name="special_requests" class="form-textarea" placeholder="Any allergies, dietary restrictions, or special requests for the party..."></textarea>
                    </div>
                </div>
                
                <button type="submit" class="submit-btn" <?php echo !$is_logged_in ? 'disabled' : ''; ?>>
                    <i class="fas fa-check-circle"></i> Book My Party
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Gallery Section -->
<div class="gallery-section">
    <div class="section-container">
        <div class="section-header animate__animated animate__fadeInUp">
            <h2 class="section-title">📸 Party Gallery</h2>
            <p class="section-subtitle">Memorable moments from our amazing celebrations!</p>
        </div>
        
        <div class="gallery-grid">
            <div class="gallery-item animate__animated animate__fadeInUp">
                <img src="uploads/gallery/party1.jpg" alt="Birthday Party 1">
                <div class="gallery-overlay">
                    <i class="fas fa-search-plus"></i>
                </div>
            </div>
            <div class="gallery-item animate__animated animate__fadeInUp animate__delay-1s">
                <img src="uploads/gallery/party2.jpg" alt="Birthday Party 2">
                <div class="gallery-overlay">
                    <i class="fas fa-search-plus"></i>
                </div>
            </div>
            <div class="gallery-item animate__animated animate__fadeInUp animate__delay-2s">
                <img src="uploads/gallery/party3.jpg" alt="Birthday Party 3">
                <div class="gallery-overlay">
                    <i class="fas fa-search-plus"></i>
                </div>
            </div>
            <div class="gallery-item animate__animated animate__fadeInUp">
                <img src="uploads/gallery/party4.jpg" alt="Birthday Party 4">
                <div class="gallery-overlay">
                    <i class="fas fa-search-plus"></i>
                </div>
            </div>
            <div class="gallery-item animate__animated animate__fadeInUp animate__delay-1s">
                <img src="uploads/gallery/party5.jpg" alt="Birthday Party 5">
                <div class="gallery-overlay">
                    <i class="fas fa-search-plus"></i>
                </div>
            </div>
            <div class="gallery-item animate__animated animate__fadeInUp animate__delay-2s">
                <img src="uploads/gallery/party6.jpg" alt="Birthday Party 6">
                <div class="gallery-overlay">
                    <i class="fas fa-search-plus"></i>
                </div>
            </div>
            <div class="gallery-item animate__animated animate__fadeInUp animate__delay-2s">
                <img src="uploads/gallery/party7.jpg" alt="Birthday Party 6">
                <div class="gallery-overlay">
                    <i class="fas fa-search-plus"></i>
                </div>
            </div>  
            <div class="gallery-item animate__animated animate__fadeInUp animate__delay-2s">
                <img src="uploads/gallery/party8.jpg" alt="Birthday Party 6">
                <div class="gallery-overlay">
                    <i class="fas fa-search-plus"></i>
                </div>
            </div>  
        </div>
    </div>
</div>

<!-- How It Works Section -->
<div class="how-it-works">
    <div class="section-container">
        <div class="section-header animate__animated animate__fadeInUp">
            <h2 class="section-title">🎯 How It Works</h2>
            <p class="section-subtitle">Simple steps to an amazing party!</p>
        </div>
        
        <div class="steps-container">
            <div class="step-card animate__animated animate__fadeInUp">
                <div class="step-number">1</div>
                <h3 class="step-title">Choose Your Date</h3>
                <p class="step-desc">Select your preferred party date and time. We recommend booking at least 7 days in advance for the best experience.</p>
            </div>
            
            <div class="step-card animate__animated animate__fadeInUp animate__delay-1s">
                <div class="step-number">2</div>
                <h3 class="step-title">Fill the Form</h3>
                <p class="step-desc">Provide your pet's details and any special requests. Our team will customize everything to make it perfect.</p>
            </div>
            
            <div class="step-card animate__animated animate__fadeInUp animate__delay-2s">
                <div class="step-number">3</div>
                <h3 class="step-title">Get Confirmation</h3>
                <p class="step-desc">Receive booking confirmation within 24 hours. We'll send you all the details via email and SMS.</p>
            </div>
            
            <div class="step-card animate__animated animate__fadeInUp animate__delay-3s">
                <div class="step-number">4</div>
                <h3 class="step-title">Celebrate!</h3>
                <p class="step-desc">Show up on party day and enjoy! We handle everything from decorations to entertainment and cleanup.</p>
            </div>
        </div>
    </div>
</div>

<!-- Party Packages Section -->
<div class="packages-section">
    <div class="section-container">
        <div class="section-header animate__animated animate__fadeInUp">
            <h2 class="section-title">🎁 Party Packages</h2>
            <p class="section-subtitle">Choose the perfect package for your celebration!</p>
        </div>
        
        <div class="packages-grid">
            <div class="package-card animate__animated animate__fadeInUp">
                <span class="package-icon">🎈</span>
                <h3 class="package-name">Basic Bash</h3>
                <div class="package-price">₹2,499 <span>/ party</span></div>
                <ul class="package-features">
                    <li><i class="fas fa-check-circle"></i> 2 Hours Party Time</li>
                    <li><i class="fas fa-check-circle"></i> Pet-Friendly Cake</li>
                    <li><i class="fas fa-check-circle"></i> Basic Decorations</li>
                    <li><i class="fas fa-check-circle"></i> Up to 5 Pets</li>
                    <li><i class="fas fa-check-circle"></i> Party Favors</li>
                </ul>
                <button class="package-btn" onclick="selectPackage('Basic Bash - ₹2,499')">Book Now</button>
            </div>
            
            <div class="package-card animate__animated animate__fadeInUp animate__delay-1s">
                <span class="package-icon">🎉</span>
                <h3 class="package-name">Premium Party</h3>
                <div class="package-price">₹4,999 <span>/ party</span></div>
                <ul class="package-features">
                    <li><i class="fas fa-check-circle"></i> 3 Hours Party Time</li>
                    <li><i class="fas fa-check-circle"></i> Custom Pet Cake</li>
                    <li><i class="fas fa-check-circle"></i> Premium Decorations</li>
                    <li><i class="fas fa-check-circle"></i> Up to 10 Pets</li>
                    <li><i class="fas fa-check-circle"></i> Photo Booth</li>
                    <li><i class="fas fa-check-circle"></i> Party Games</li>
                    <li><i class="fas fa-check-circle"></i> Vet On-Site</li>
                </ul>
                <button class="package-btn" onclick="selectPackage('Premium Party - ₹4,999')">Book Now</button>
            </div>
            
            <div class="package-card animate__animated animate__fadeInUp animate__delay-2s">
                <span class="package-icon">👑</span>
                <h3 class="package-name">Royal Celebration</h3>
                <div class="package-price">₹7,999 <span>/ party</span></div>
                <ul class="package-features">
                    <li><i class="fas fa-check-circle"></i> 4 Hours Party Time</li>
                    <li><i class="fas fa-check-circle"></i> Luxury Pet Cake</li>
                    <li><i class="fas fa-check-circle"></i> Exclusive Venue Setup</li>
                    <li><i class="fas fa-check-circle"></i> Up to 20 Pets</li>
                    <li><i class="fas fa-check-circle"></i> Professional Photography</li>
                    <li><i class="fas fa-check-circle"></i> All Games & Activities</li>
                    <li><i class="fas fa-check-circle"></i> Vet & Groomer On-Site</li>
                    <li><i class="fas fa-check-circle"></i> Premium Party Favors</li>
                    <li><i class="fas fa-check-circle"></i> Free Home Delivery</li>
                </ul>
                <button class="package-btn" onclick="selectPackage('Royal Celebration - ₹7,999')">Book Now</button>
            </div>
        </div>
    </div>
</div>

<!-- Testimonials Section -->
<div class="testimonials-section">
    <div class="section-container">
        <div class="section-header animate__animated animate__fadeInUp">
            <h2 class="section-title">💬 Happy Pet Parents</h2>
            <p class="section-subtitle">See what our customers say about their experience!</p>
        </div>
        
        <div class="testimonials-grid">
            <div class="testimonial-card animate__animated animate__fadeInUp">
                <div class="quote-icon">"</div>
                <p class="testimonial-text">
                    "Absolutely amazing experience! My golden retriever Max had the best birthday ever. The team was so professional and caring. Highly recommend FurryMart for pet parties!"
                </p>
                <div class="testimonial-author">
                    <div class="author-avatar">S</div>
                    <div class="author-info">
                        <h4>Sarah Johnson</h4>
                        <p>Max's Mom</p>
                        <div class="rating">⭐⭐⭐⭐⭐</div>
                    </div>
                </div>
            </div>
            
            <div class="testimonial-card animate__animated animate__fadeInUp animate__delay-1s">
                <div class="quote-icon">"</div>
                <p class="testimonial-text">
                    "The decorations were stunning and the pet-friendly cake was a huge hit! All the dogs had so much fun. Worth every penny. Thank you FurryMart team!"
                </p>
                <div class="testimonial-author">
                    <div class="author-avatar">R</div>
                    <div class="author-info">
                        <h4>Raj Patel</h4>
                        <p>Bruno's Dad</p>
                        <div class="rating">⭐⭐⭐⭐⭐</div>
                    </div>
                </div>
            </div>
            
            <div class="testimonial-card animate__animated animate__fadeInUp animate__delay-2s">
                <div class="quote-icon">"</div>
                <p class="testimonial-text">
                    "Best decision ever! My cat Luna's first birthday was magical. The team handled everything perfectly. The photos came out beautiful too. 10/10 experience!"
                </p>
                <div class="testimonial-author">
                    <div class="author-avatar">P</div>
                    <div class="author-info">
                        <h4>Priya Sharma</h4>
                        <p>Luna's Mom</p>
                        <div class="rating">⭐⭐⭐⭐⭐</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- FAQ Section -->
<div class="faq-section">
    <div class="section-container">
        <div class="section-header animate__animated animate__fadeInUp">
            <h2 class="section-title">❓ Frequently Asked Questions</h2>
            <p class="section-subtitle">Got questions? We've got answers!</p>
        </div>
        
        <div class="faq-container">
            <div class="faq-item animate__animated animate__fadeInUp">
                <div class="faq-question" onclick="toggleFaq(this)">
                    <span>How far in advance should I book?</span>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="faq-answer">
                    <p>We recommend booking at least 7 days in advance to ensure availability and proper preparation. However, we can accommodate last-minute bookings based on venue availability.</p>
                </div>
            </div>
            
            <div class="faq-item animate__animated animate__fadeInUp animate__delay-1s">
                <div class="faq-question" onclick="toggleFaq(this)">
                    <span>What is included in the party package?</span>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="faq-answer">
                    <p>Each package includes venue decoration, pet-friendly birthday cake, party favors, games and activities, and on-site vet supervision. Premium packages include additional features like professional photography and extended party time.</p>
                </div>
            </div>
            
            <div class="faq-item animate__animated animate__fadeInUp animate__delay-2s">
                <div class="faq-question" onclick="toggleFaq(this)">
                    <span>Can I bring my own decorations or food?</span>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="faq-answer">
                    <p>Yes! While we provide complete party setups, you're welcome to bring additional decorations or treats. Just let us know in advance so we can coordinate everything perfectly.</p>
                </div>
            </div>
            
            <div class="faq-item animate__animated animate__fadeInUp animate__delay-3s">
                <div class="faq-question" onclick="toggleFaq(this)">
                    <span>Is veterinary supervision mandatory?</span>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="faq-answer">
                    <p>Yes, for safety reasons, a licensed veterinarian is present during all parties to ensure all pets are safe and comfortable throughout the celebration.</p>
                </div>
            </div>
            
            <div class="faq-item animate__animated animate__fadeInUp animate__delay-4s">
                <div class="faq-question" onclick="toggleFaq(this)">
                    <span>What if I need to cancel or reschedule?</span>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="faq-answer">
                    <p>You can reschedule up to 48 hours before the party date at no extra cost. Cancellations made 7+ days in advance receive a full refund, while cancellations within 7 days receive a 50% refund.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Success Modal -->
<div id="successModal" class="success-modal">
    <div class="modal-content">
        <div class="modal-icon">🎉</div>
        <h2 class="modal-title">Booking Confirmed!</h2>
        <p class="modal-subtitle">Your party request has been submitted successfully</p>
        
        <div class="booking-details">
            <div class="detail-row">
                <span class="detail-label"><i class="fas fa-paw"></i> Pet Name</span>
                <span class="detail-value" id="modal_pet_name"></span>
            </div>
            <div class="detail-row">
                <span class="detail-label"><i class="fas fa-dog"></i> Pet Type</span>
                <span class="detail-value" id="modal_pet_type"></span>
            </div>
            <div class="detail-row">
                <span class="detail-label"><i class="fas fa-calendar-alt"></i> Party Date</span>
                <span class="detail-value" id="modal_party_date"></span>
            </div>
            <div class="detail-row">
                <span class="detail-label"><i class="fas fa-clock"></i> Party Time</span>
                <span class="detail-value" id="modal_party_time"></span>
            </div>
            <div class="detail-row">
                <span class="detail-label"><i class="fas fa-users"></i> Guest Count</span>
                <span class="detail-value" id="modal_guest_count"></span>
            </div>
            <div class="detail-row">
                <span class="detail-label"><i class="fas fa-gift"></i> Party Package</span>
                <span class="detail-value" id="modal_party_package" style="color: var(--pink); font-weight: 900;"></span>
            </div>
            <div class="detail-row">
                <span class="detail-label"><i class="fas fa-info-circle"></i> Status</span>
                <span class="status-badge-modal" id="modal_status"></span>
            </div>
        </div>
        
        <div class="modal-note">
            <i class="fas fa-bell"></i> Our team will review your booking and send confirmation within 24 hours via email. Keep an eye on your inbox!
        </div>
        
        <button class="modal-close-btn" onclick="closeSuccessModal()">
            <i class="fas fa-check"></i> Awesome, Got It!
        </button>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
function closeSuccessModal() {
    $('#successModal').removeClass('show');
}

$(document).ready(function() {
    $('#birthdayForm').on('submit', function(e) {
        e.preventDefault();
        
        const submitBtn = $(this).find('.submit-btn');
        const originalText = submitBtn.html();
        
        // Disable button and show loading
        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Booking...');
        
        $.ajax({
            url: 'birthday_booking.php',
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                if(response.success) {
                    // Hide error message
                    $('#errorMessage').fadeOut();
                    
                    // Populate modal with booking details
                    $('#modal_pet_name').text(response.details.pet_name);
                    $('#modal_pet_type').text(response.details.pet_type);
                    $('#modal_party_date').text(response.details.party_date);
                    $('#modal_party_time').text(response.details.party_time);
                    $('#modal_guest_count').text(response.details.guest_count + ' Pets');
                    $('#modal_party_package').text(response.details.party_package);
                    $('#modal_status').text(response.details.status);
                    
                    // Show modal
                    $('#successModal').addClass('show');
                    
                    // Reset form
                    $('#birthdayForm')[0].reset();
                } else {
                    $('#errorMessage').html('<i class="fas fa-exclamation-circle"></i> ' + response.message).fadeIn();
                    $('#successMessage').fadeOut();
                }
                
                // Re-enable button
                submitBtn.prop('disabled', false).html(originalText);
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
                console.error('Response:', xhr.responseText);
                $('#errorMessage').html('<i class="fas fa-exclamation-circle"></i> Something went wrong. Please try again.').fadeIn();
                $('#successMessage').fadeOut();
                submitBtn.prop('disabled', false).html(originalText);
            }
        });
    });
    
    // Prevent past dates
    const today = new Date().toISOString().split('T')[0];
    $('input[name="party_date"]').attr('min', today);
    
    // Close modal on background click
    $('#successModal').on('click', function(e) {
        if(e.target.id === 'successModal') {
            closeSuccessModal();
        }
    });
});

// FAQ Toggle Function
function toggleFaq(element) {
    const faqItem = element.parentElement;
    const isActive = faqItem.classList.contains('active');
    
    // Close all other FAQs
    document.querySelectorAll('.faq-item').forEach(item => {
        item.classList.remove('active');
    });
    
    // Toggle current FAQ
    if(!isActive) {
        faqItem.classList.add('active');
    }
}

// Package Selection Function
function selectPackage(packageName) {
    // Set the package in the dropdown
    const packageSelect = document.getElementById('party_package');
    if(packageSelect) {
        packageSelect.value = packageName;
        
        // Highlight the selected package
        packageSelect.style.borderColor = '#ec4899';
        packageSelect.style.background = 'linear-gradient(135deg, #fdf2f8, #f3e8ff)';
        
        setTimeout(() => {
            packageSelect.style.borderColor = '';
            packageSelect.style.background = '';
        }, 2000);
    }
    
    // Smooth scroll to booking form
    const bookingSection = document.querySelector('.booking-section');
    if(bookingSection) {
        bookingSection.scrollIntoView({
            behavior: 'smooth',
            block: 'start'
        });
    }
}
</script>

<?php include "includes/footer.php"; ?>

