<?php
session_start();
include 'db.php';

// Set timezone to India Standard Time
date_default_timezone_set('Asia/Kolkata');

// Fetch user details from database if logged in
$user_data = [];
if(isset($_SESSION['email']) || isset($_SESSION['user_email'])) {
    $user_email = isset($_SESSION['email']) ? $_SESSION['email'] : $_SESSION['user_email'];
    $user_email = mysqli_real_escape_string($conn, $user_email);
    
    $user_query = "SELECT first_name, middle_name, last_name, email, mobile_number, dob, age, caste, address, city, state, zip FROM users WHERE email = '$user_email' LIMIT 1";
    $user_result = mysqli_query($conn, $user_query);
    
    if($user_result && mysqli_num_rows($user_result) > 0) {
        $user_data = mysqli_fetch_assoc($user_result);
        
        // Combine first_name, middle_name, last_name for full name
        $full_name = trim($user_data['first_name']);
        if(!empty($user_data['middle_name'])) {
            $full_name .= ' ' . trim($user_data['middle_name']);
        }
        if(!empty($user_data['last_name'])) {
            $full_name .= ' ' . trim($user_data['last_name']);
        }
        $user_data['full_name'] = $full_name;
    }
}

// Check if cart is empty
if(empty($_SESSION['cart'])) {
    header("Location: cart.php");
    exit();
}

// Calculate cart totals (same logic as cart.php)

$subtotal = 0;
if (!empty($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $subtotal += (float)$item['price'] * (int)$item['qty'];
    }
}

// Shipping logic: Free shipping above ₹499
$free_shipping_threshold = 499;
$shipping_charges = 0;
if ($subtotal > 0 && $subtotal < $free_shipping_threshold) {
    $shipping_charges = 40; // ₹40 shipping fee
}

// Calculate how much more needed for free shipping
$remaining_for_free_shipping = max(0, $free_shipping_threshold - $subtotal);
$free_shipping_progress = min(100, ($subtotal / $free_shipping_threshold) * 100);

// GST calculation (18%)
$gst_rate = 0.18;
$gst_amount = ($subtotal + $shipping_charges) * $gst_rate;

// Grand total
$grand_total = $subtotal + $shipping_charges + $gst_amount;

// Process Order Submission
if(isset($_POST['place_order'])) {
    
    // Generate unique order number
    $order_reference = 'ORD' . time() . rand(1000, 9999);
    
    // Sanitize inputs
    $user_email = mysqli_real_escape_string($conn, $_POST['email']);
    $customer_name = mysqli_real_escape_string($conn, $_POST['name']);
    $customer_phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $customer_dob = mysqli_real_escape_string($conn, $_POST['dob']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $city = mysqli_real_escape_string($conn, $_POST['city']);
    $state = mysqli_real_escape_string($conn, $_POST['state']);
    $zip = mysqli_real_escape_string($conn, $_POST['zip']);
    $payment_method = mysqli_real_escape_string($conn, $_POST['payment_method']);
    
    // Payment specific fields
    $upi_id = ($payment_method == 'UPI') ? mysqli_real_escape_string($conn, $_POST['upi_id']) : NULL;
    $card_last4 = ($payment_method == 'Card') ? substr($_POST['card_number'], -4) : NULL;
    $transaction_id = 'TXN' . time() . rand(100, 999);
    
    // Use the pre-calculated grand_total (includes subtotal + shipping + GST)
    $total_amount = $grand_total;
    
    // Determine payment status based on payment method
    // UPI and Card payments are considered paid immediately
    // COD payments remain pending until delivery
    $payment_status = ($payment_method == 'UPI' || $payment_method == 'Card') ? 'Paid' : 'Pending';
    
    // Insert order
    $sql = "INSERT INTO orders (order_reference, user_email, customer_name, customer_phone, customer_dob, 
            shipping_address, city, state, zip_code, payment_method, upi_id, card_last4, transaction_id, 
            total_amount, order_status, payment_status) 
            VALUES ('$order_reference', '$user_email', '$customer_name', '$customer_phone', '$customer_dob',
            '$address', '$city', '$state', '$zip', '$payment_method', '$upi_id', '$card_last4', 
            '$transaction_id', '$total_amount', 'Pending', '$payment_status')";
    
    if(mysqli_query($conn, $sql)) {
        $order_id = mysqli_insert_id($conn);
        
        // Insert order items
        foreach($_SESSION['cart'] as $cart_key => $item) {
            $product_name = mysqli_real_escape_string($conn, $item['name']);
            $product_image = mysqli_real_escape_string($conn, $item['image']);
            $price = $item['price'];
            $qty = $item['qty'];
            // Get variant_id from multiple possible sources for backwards compatibility
            $variant_id = $item['variant_id'] ?? $item['id'] ?? $cart_key ?? 0;
            $weight_size = mysqli_real_escape_string($conn, $item['weight_size'] ?? $item['weight'] ?? '');
            $item_subtotal = $price * $qty;
            
            $item_sql = "INSERT INTO order_items (order_id, variant_id, product_name, product_image, 
                        price, quantity, weight_size, subtotal) 
                        VALUES ($order_id, $variant_id, '$product_name', '$product_image', 
                        $price, $qty, '$weight_size', $item_subtotal)";
            mysqli_query($conn, $item_sql);
            
            // AUTOMATIC STOCK REDUCTION - Decrease stock quantity when order is placed
            if($variant_id > 0) {
                $reduce_stock_sql = "UPDATE product_variants 
                                    SET stock_qty = GREATEST(stock_qty - $qty, 0) 
                                    WHERE id = $variant_id";
                mysqli_query($conn, $reduce_stock_sql);
            }
        }
        
        // Add to status history
        mysqli_query($conn, "INSERT INTO order_status_history (order_id, status, comment) 
                            VALUES ($order_id, 'Pending', 'Order placed successfully')");
        
        // Get current timestamp in India timezone
        $current_time = date('d M Y, h:i A');
        
        // Store order details for popup (don't clear cart yet for display)
        $_SESSION['last_order'] = [
            'order_reference' => $order_reference,
            'order_id' => $order_id,
            'customer_name' => $customer_name,
            'user_email' => $user_email,
            'customer_phone' => $customer_phone,
            'payment_method' => $payment_method,
            'subtotal' => $subtotal,
            'shipping_charges' => $shipping_charges,
            'gst_amount' => $gst_amount,
            'total' => $total_amount,
            'items' => $_SESSION['cart'],
            'order_date' => $current_time
        ];
        
        // Clear cart
        unset($_SESSION['cart']);
        
        // Set success flag and stay on same page
        $_SESSION['order_success'] = true;
        echo "<script>
            window.addEventListener('load', function() {
                document.getElementById('loadingOverlay').classList.remove('active');
                document.getElementById('successModal').classList.add('active');
            });
        </script>";
    } else {
        $error = "Order placement failed. Please try again.";
    }
}

include 'includes/header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - FurryMart</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    
    <style>
        :root {
            --primary: #518992;
            --navy: #0f1c3f;
            --accent: #f87171;
            --success: #22c55e;
            --gray: #94a3b8;
            --bg: #f8fafc;
        }
        
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: var(--bg); color: var(--navy); }
        
        /* Hero Section */
        .hero-checkout {
            height: 300px;
            background: linear-gradient(135deg, var(--navy) 0%, var(--primary) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            position: relative;
            overflow: hidden;
            margin-bottom: 50px;
        }
        .hero-checkout::before {
            content: '';
            position: absolute;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 1px, transparent 1px);
            background-size: 50px 50px;
            animation: drift 20s linear infinite;
        }
        @keyframes drift {
            0% { transform: translate(0, 0); }
            100% { transform: translate(50px, 50px); }
        }
        .hero-checkout h1 {
            font-size: 48px;
            font-weight: 900;
            z-index: 2;
            text-shadow: 0 4px 20px rgba(0,0,0,0.3);
        }
        
        /* Main Container */
        .checkout-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px 80px;
            display: grid;
            grid-template-columns: 1.5fr 1fr;
            gap: 40px;
        }
        
        /* Form Section */
        .checkout-form {
            background: white;
            padding: 40px;
            border-radius: 30px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.05);
            animation: slideInLeft 0.6s ease;
        }
        @keyframes slideInLeft {
            from { opacity: 0; transform: translateX(-30px); }
            to { opacity: 1; transform: translateX(0); }
        }
        
        .section-title {
            font-size: 20px;
            font-weight: 800;
            color: var(--navy);
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid #f1f5f9;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .section-title i {
            color: var(--primary);
        }
        
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 25px;
        }
        .form-group {
            display: flex;
            flex-direction: column;
        }
        .form-group.full-width {
            grid-column: 1 / -1;
        }
        .form-group label {
            font-size: 12px;
            font-weight: 800;
            text-transform: uppercase;
            color: var(--gray);
            margin-bottom: 8px;
            letter-spacing: 0.5px;
        }
        .form-group label span {
            color: var(--accent);
        }
        .form-group input,
        .form-group textarea,
        .form-group select {
            padding: 14px 18px;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 600;
            font-family: inherit;
            transition: all 0.3s;
            background: #fafafa;
        }
        .form-group select {
            cursor: pointer;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%23518992' d='M6 9L1 4h10z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 15px center;
            padding-right: 40px;
        }
        .form-group select optgroup {
            font-weight: 800;
            color: var(--navy);
            background: #f8fafc;
            padding: 5px;
        }
        .form-group select option {
            padding: 10px;
            font-weight: 600;
        }
        .form-group input:focus,
        .form-group textarea:focus,
        .form-group select:focus {
            outline: none;
            border-color: var(--primary);
            background: white;
            box-shadow: 0 0 0 4px rgba(81, 137, 146, 0.1);
        }
        .form-group textarea {
            resize: vertical;
            min-height: 80px;
        }
        
        /* Payment Methods */
        .payment-methods {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            margin-bottom: 25px;
        }
        .payment-option {
            position: relative;
        }
        .payment-option input[type="radio"] {
            position: absolute;
            opacity: 0;
        }
        .payment-option label {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
            padding: 20px;
            border: 2px solid #e2e8f0;
            border-radius: 15px;
            cursor: pointer;
            transition: all 0.3s;
            background: white;
        }
        .payment-option input:checked + label {
            border-color: var(--primary);
            background: rgba(81, 137, 146, 0.05);
            transform: scale(1.05);
        }
        .payment-option label i {
            font-size: 32px;
            color: var(--primary);
        }
        .payment-option label span {
            font-size: 13px;
            font-weight: 800;
            text-transform: uppercase;
        }
        
        /* Conditional Payment Fields */
        .payment-fields {
            display: none;
            animation: fadeIn 0.4s ease;
        }
        .payment-fields.active {
            display: block;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        /* Order Summary */
        .order-summary {
            background: white;
            padding: 35px;
            border-radius: 30px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.05);
            height: fit-content;
            position: sticky;
            top: 20px;
            animation: slideInRight 0.6s ease;
        }
        @keyframes slideInRight {
            from { opacity: 0; transform: translateX(30px); }
            to { opacity: 1; transform: translateX(0); }
        }
        
        .order-item {
            display: flex;
            gap: 15px;
            padding: 15px 0;
            border-bottom: 1px solid #f1f5f9;
        }
        .order-item img {
            width: 70px;
            height: 70px;
            object-fit: cover;
            border-radius: 12px;
        }
        .order-item-info {
            flex: 1;
        }
        .order-item-name {
            font-size: 14px;
            font-weight: 700;
            color: var(--navy);
            margin-bottom: 5px;
        }
        .order-item-price {
            font-size: 13px;
            color: var(--gray);
        }
        .order-item-qty {
            font-size: 12px;
            font-weight: 700;
            color: var(--primary);
        }
        
        .summary-totals {
            margin-top: 25px;
            padding-top: 20px;
            border-top: 2px solid #f1f5f9;
        }
        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 12px;
            font-size: 14px;
        }
        .summary-row.total {
            font-size: 22px;
            font-weight: 900;
            color: var(--navy);
            margin-top: 15px;
            padding-top: 15px;
            border-top: 2px solid var(--primary);
        }
        
        /* Submit Button */
        .btn-place-order {
            width: 100%;
            padding: 18px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--navy) 100%);
            color: white;
            border: none;
            border-radius: 15px;
            font-size: 16px;
            font-weight: 900;
            text-transform: uppercase;
            cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 8px 25px rgba(81, 137, 146, 0.3);
            margin-top: 25px;
            letter-spacing: 1px;
        }
        .btn-place-order:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 35px rgba(81, 137, 146, 0.4);
        }
        .btn-place-order:active {
            transform: translateY(0);
        }
        
        /* Success Modal */
        .success-modal {
            position: fixed;
            inset: 0;
            background: rgba(15, 28, 63, 0.95);
            backdrop-filter: blur(10px);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 10000;
            padding: 20px;
        }
        .success-modal.active {
            display: flex !important;
        }
        .modal-content {
            background: white;
            max-width: 600px;
            width: 100%;
            border-radius: 30px;
            padding: 40px;
            animation: zoomIn 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            max-height: 90vh;
            overflow-y: auto;
        }
        
        /* Scrollbar styling */
        .modal-content::-webkit-scrollbar {
            width: 8px;
        }
        .modal-content::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 10px;
        }
        .modal-content::-webkit-scrollbar-thumb {
            background: var(--primary);
            border-radius: 10px;
        }
        @keyframes zoomIn {
            from { opacity: 0; transform: scale(0.5); }
            to { opacity: 1; transform: scale(1); }
        }
        .success-icon {
            width: 80px;
            height: 80px;
            background: var(--success);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            animation: bounce 1s infinite;
        }
        .success-icon i {
            font-size: 40px;
            color: white;
        }
        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }
        .modal-content h2 {
            text-align: center;
            font-size: 28px;
            font-weight: 900;
            color: var(--navy);
            margin-bottom: 10px;
        }
        .order-number {
            text-align: center;
            color: var(--primary);
            font-weight: 800;
            font-size: 18px;
            margin-bottom: 30px;
        }
        .modal-buttons {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-top: 30px;
        }
        .btn-modal {
            padding: 14px;
            border: none;
            border-radius: 12px;
            font-weight: 800;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 14px;
            text-transform: uppercase;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        .btn-print {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        }
        .btn-pdf {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
            box-shadow: 0 4px 15px rgba(240, 147, 251, 0.4);
        }
        .btn-continue {
            background: #f1f5f9;
            color: var(--navy);
            margin-top: 15px;
            width: 50%;
            margin-left: auto;
            margin-right: auto;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        .btn-modal:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.2);
        }
        .btn-modal:active {
            transform: translateY(-1px);
        }
        
        @media (max-width: 992px) {
            .checkout-container {
                grid-template-columns: 1fr;
            }
            .form-grid {
                grid-template-columns: 1fr;
            }
        }
        
        /* Loading Spinner Overlay */
        .loading-overlay {
            position: fixed;
            inset: 0;
            background: rgba(15, 28, 63, 0.95);
            backdrop-filter: blur(10px);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            flex-direction: column;
            gap: 20px;
        }
        .loading-overlay.active {
            display: flex;
        }
        .spinner {
            width: 80px;
            height: 80px;
            border: 8px solid rgba(255, 255, 255, 0.2);
            border-top: 8px solid var(--primary);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        .loading-text {
            color: white;
            font-size: 20px;
            font-weight: 800;
            text-align: center;
        }
        
        @media print {
            @page {
                size: A4 portrait;
                margin: 5mm;
            }
            
            html, body {
                width: 210mm !important;
                height: 297mm !important;
                margin: 0 !important;
                padding: 0 !important;
                overflow: hidden !important;
            }
            
            body * {
                visibility: hidden !important;
            }
            
            #orderReceipt, #orderReceipt * {
                visibility: visible !important;
            }
            
            #orderReceipt {
                position: fixed !important;
                left: 5mm !important;
                top: 5mm !important;
                width: 200mm !important;
                max-width: 200mm !important;
                height: auto !important;
                max-height: 287mm !important;
                padding: 15px !important; /* Increased padding for better spacing with border */
                margin: 0 !important;
                box-shadow: none !important;
                border-radius: 0 !important;
                transform: scale(0.90);
                transform-origin: top left;
                page-break-after: avoid !important;
                page-break-before: avoid !important;
                page-break-inside: avoid !important;
                overflow: hidden !important;
                /* Add elegant border for printed page */
                border: 3px solid #518992 !important; /* Outer border - FurryMart color */
                box-shadow: inset 0 0 0 2px #f8f9fa !important; /* Inner border effect */
            }
            
            .success-modal {
                position: static !important;
                background: white !important;
                backdrop-filter: none !important;
                height: 297mm !important;
                width: 210mm !important;
                overflow: hidden !important;
                page-break-after: avoid !important;
                max-height: 297mm !important;
            }
            
            .modal-content {
                box-shadow: none !important;
                max-width: 100% !important;
                width: 100% !important;
                padding: 0 !important;
                margin: 0 !important;
                background: white !important;
                max-height: 297mm !important;
                height: auto !important;
                overflow: hidden !important;
                page-break-after: avoid !important;
            }
            
            /* Hide all modal decorations */
            .modal-buttons, .btn-modal, .success-icon, 
            .modal-content > h2, .order-number, .btn-continue {
                display: none !important;
                visibility: hidden !important;
            }
            
            /* Prevent page breaks everywhere */
            #orderReceipt,
            #orderReceipt *,
            #orderReceipt table,
            #orderReceipt table tbody,
            #orderReceipt table tfoot,
            #orderReceipt > div {
                page-break-inside: avoid !important;
                page-break-after: avoid !important;
                page-break-before: avoid !important;
            }
            
            /* Optimize spacing for single page */
            #orderReceipt h1 {
                font-size: 20px !important;
                margin: 0 0 5px 0 !important;
                padding: 0 !important;
            }
            
            #orderReceipt h4 {
                font-size: 11px !important;
                margin-bottom: 6px !important;
                padding-bottom: 4px !important;
            }
            
            #orderReceipt p,
            #orderReceipt td,
            #orderReceipt th,
            #orderReceipt span,
            #orderReceipt strong {
                font-size: 9px !important;
                line-height: 1.1 !important;
            }
            
            #orderReceipt table {
                margin-bottom: 6px !important;
            }
            
            #orderReceipt table td,
            #orderReceipt table th {
                padding: 3px 5px !important;
            }
            
            /* Make product images smaller */
            #orderReceipt table img {
                width: 30px !important;
                height: 30px !important;
            }
            
            /* Reduce margins and padding throughout */
            #orderReceipt > div:first-child {
                margin-bottom: 10px !important;
                padding-bottom: 6px !important;
            }
            
            #orderReceipt > div {
                margin-bottom: 6px !important;
                padding: 8px !important;
            }
            
            /* Compact grid layout */
            #orderReceipt div[style*="grid-template-columns"] {
                gap: 6px !important;
                padding: 10px !important;
                margin-bottom: 10px !important;
            }
            
            /* Compact security message */
            #orderReceipt div[style*="linear-gradient(135deg, #667eea"] {
                padding: 6px !important;
                margin-top: 8px !important;
                margin-bottom: 0 !important;
            }
            
            /* Force single page - critical rules */
            body::after {
                content: '';
                display: block;
                page-break-after: avoid !important;
            }
            
            html::after {
                content: '';
                display: block;
                page-break-after: avoid !important;
            }
        }
    </style>
</head>
<body>

<!-- Loading Overlay -->
<div class="loading-overlay" id="loadingOverlay">
    <div class="spinner"></div>
    <div class="loading-text">
        <div style="font-size: 24px; margin-bottom: 10px;">🛡️ Processing Your Order...</div>
        <div style="font-size: 14px; font-weight: 600; opacity: 0.8;">Please wait while we securely process your payment</div>
    </div>
</div>

<!-- Hero Section -->
<div class="hero-checkout animate__animated animate__fadeIn">
    <h1><i class="fas fa-shopping-bag"></i> Secure Checkout</h1>
</div>

<!-- Main Checkout Container -->
<div class="checkout-container">
    <!-- Left: Checkout Form -->
    <div class="checkout-form">
        <form method="POST" id="checkoutForm">
            <!-- Personal Information -->
            <div class="section-title">
                <i class="fas fa-user-circle"></i> Personal Information
            </div>
            <div class="form-grid">
                <div class="form-group">
                    <label>Full Name <span>*</span></label>
                    <input type="text" name="name" required placeholder="John Doe" 
                           value="<?php echo !empty($user_data['full_name']) ? htmlspecialchars($user_data['full_name']) : ''; ?>">
                </div>
                <div class="form-group">
                    <label>Email Address <span>*</span></label>
                    <input type="email" name="email" required placeholder="john@example.com" 
                           value="<?php echo !empty($user_data['email']) ? htmlspecialchars($user_data['email']) : ($_SESSION['user_email'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label>Phone Number <span>*</span></label>
                    <input type="tel" name="phone" required placeholder="+91 9876543210" 
                           value="<?php echo !empty($user_data['mobile_number']) ? htmlspecialchars($user_data['mobile_number']) : ''; ?>">
                </div>
                <div class="form-group">
                    <label>Date of Birth</label>
                    <input type="date" name="dob" 
                           value="<?php echo !empty($user_data['dob']) ? htmlspecialchars($user_data['dob']) : ''; ?>">
                </div>
            </div>
            
            <!-- Shipping Address -->
            <div class="section-title">
                <i class="fas fa-map-marker-alt"></i> Shipping Address
            </div>
            <div class="form-grid">
                <div class="form-group full-width">
                    <label>Street Address <span>*</span></label>
                    <textarea name="address" required placeholder="House No, Building, Street"><?php echo !empty($user_data['address']) ? htmlspecialchars($user_data['address']) : ''; ?></textarea>
                </div>
                <div class="form-group">
                    <label>City <span>*</span></label>
                    <select name="city" id="citySelect" required onchange="updateState()">
                        <option value="">Select City</option>
                        <optgroup label="Gujarat">
                            <option value="Ahmedabad" data-state="Gujarat" <?php echo (!empty($user_data['city']) && $user_data['city']=='Ahmedabad') ? 'selected' : ''; ?>>Ahmedabad</option>
                            <option value="Surat" data-state="Gujarat" <?php echo (!empty($user_data['city']) && $user_data['city']=='Surat') ? 'selected' : ''; ?>>Surat</option>
                            <option value="Vadodara" data-state="Gujarat" <?php echo (!empty($user_data['city']) && $user_data['city']=='Vadodara') ? 'selected' : ''; ?>>Vadodara</option>
                            <option value="Rajkot" data-state="Gujarat" <?php echo (!empty($user_data['city']) && $user_data['city']=='Rajkot') ? 'selected' : ''; ?>>Rajkot</option>
                            <option value="Morbi" data-state="Gujarat" <?php echo (!empty($user_data['city']) && $user_data['city']=='Morbi') ? 'selected' : ''; ?>>Morbi</option>
                            <option value="Gandhinagar" data-state="Gujarat" <?php echo (!empty($user_data['city']) && $user_data['city']=='Gandhinagar') ? 'selected' : ''; ?>>Gandhinagar</option>
                            <option value="Bhavnagar" data-state="Gujarat" <?php echo (!empty($user_data['city']) && $user_data['city']=='Bhavnagar') ? 'selected' : ''; ?>>Bhavnagar</option>
                        </optgroup>
                        <optgroup label="Maharashtra">
                            <option value="Mumbai" data-state="Maharashtra" <?php echo (!empty($user_data['city']) && $user_data['city']=='Mumbai') ? 'selected' : ''; ?>>Mumbai</option>
                            <option value="Pune" data-state="Maharashtra" <?php echo (!empty($user_data['city']) && $user_data['city']=='Pune') ? 'selected' : ''; ?>>Pune</option>
                            <option value="Nagpur" data-state="Maharashtra" <?php echo (!empty($user_data['city']) && $user_data['city']=='Nagpur') ? 'selected' : ''; ?>>Nagpur</option>
                            <option value="Nashik" data-state="Maharashtra" <?php echo (!empty($user_data['city']) && $user_data['city']=='Nashik') ? 'selected' : ''; ?>>Nashik</option>
                            <option value="Thane" data-state="Maharashtra" <?php echo (!empty($user_data['city']) && $user_data['city']=='Thane') ? 'selected' : ''; ?>>Thane</option>
                            <option value="Aurangabad" data-state="Maharashtra" <?php echo (!empty($user_data['city']) && $user_data['city']=='Aurangabad') ? 'selected' : ''; ?>>Aurangabad</option>
                        </optgroup>
                        <optgroup label="Karnataka">
                            <option value="Bangalore" data-state="Karnataka" <?php echo (!empty($user_data['city']) && $user_data['city']=='Bangalore') ? 'selected' : ''; ?>>Bangalore</option>
                            <option value="Mysore" data-state="Karnataka" <?php echo (!empty($user_data['city']) && $user_data['city']=='Mysore') ? 'selected' : ''; ?>>Mysore</option>
                            <option value="Mangalore" data-state="Karnataka" <?php echo (!empty($user_data['city']) && $user_data['city']=='Mangalore') ? 'selected' : ''; ?>>Mangalore</option>
                            <option value="Hubli" data-state="Karnataka" <?php echo (!empty($user_data['city']) && $user_data['city']=='Hubli') ? 'selected' : ''; ?>>Hubli</option>
                        </optgroup>
                        <optgroup label="Tamil Nadu">
                            <option value="Chennai" data-state="Tamil Nadu" <?php echo (!empty($user_data['city']) && $user_data['city']=='Chennai') ? 'selected' : ''; ?>>Chennai</option>
                            <option value="Coimbatore" data-state="Tamil Nadu" <?php echo (!empty($user_data['city']) && $user_data['city']=='Coimbatore') ? 'selected' : ''; ?>>Coimbatore</option>
                            <option value="Madurai" data-state="Tamil Nadu" <?php echo (!empty($user_data['city']) && $user_data['city']=='Madurai') ? 'selected' : ''; ?>>Madurai</option>
                            <option value="Tiruchirappalli" data-state="Tamil Nadu" <?php echo (!empty($user_data['city']) && $user_data['city']=='Tiruchirappalli') ? 'selected' : ''; ?>>Tiruchirappalli</option>
                        </optgroup>
                        <optgroup label="Rajasthan">
                            <option value="Jaipur" data-state="Rajasthan" <?php echo (!empty($user_data['city']) && $user_data['city']=='Jaipur') ? 'selected' : ''; ?>>Jaipur</option>
                            <option value="Jodhpur" data-state="Rajasthan" <?php echo (!empty($user_data['city']) && $user_data['city']=='Jodhpur') ? 'selected' : ''; ?>>Jodhpur</option>
                            <option value="Udaipur" data-state="Rajasthan" <?php echo (!empty($user_data['city']) && $user_data['city']=='Udaipur') ? 'selected' : ''; ?>>Udaipur</option>
                            <option value="Kota" data-state="Rajasthan" <?php echo (!empty($user_data['city']) && $user_data['city']=='Kota') ? 'selected' : ''; ?>>Kota</option>
                        </optgroup>
                        <optgroup label="Delhi">
                            <option value="New Delhi" data-state="Delhi" <?php echo (!empty($user_data['city']) && $user_data['city']=='New Delhi') ? 'selected' : ''; ?>>New Delhi</option>
                            <option value="Delhi" data-state="Delhi" <?php echo (!empty($user_data['city']) && $user_data['city']=='Delhi') ? 'selected' : ''; ?>>Delhi</option>
                        </optgroup>
                        <optgroup label="West Bengal">
                            <option value="Kolkata" data-state="West Bengal" <?php echo (!empty($user_data['city']) && $user_data['city']=='Kolkata') ? 'selected' : ''; ?>>Kolkata</option>
                            <option value="Howrah" data-state="West Bengal" <?php echo (!empty($user_data['city']) && $user_data['city']=='Howrah') ? 'selected' : ''; ?>>Howrah</option>
                            <option value="Durgapur" data-state="West Bengal" <?php echo (!empty($user_data['city']) && $user_data['city']=='Durgapur') ? 'selected' : ''; ?>>Durgapur</option>
                        </optgroup>
                        <optgroup label="Uttar Pradesh">
                            <option value="Lucknow" data-state="Uttar Pradesh" <?php echo (!empty($user_data['city']) && $user_data['city']=='Lucknow') ? 'selected' : ''; ?>>Lucknow</option>
                            <option value="Kanpur" data-state="Uttar Pradesh" <?php echo (!empty($user_data['city']) && $user_data['city']=='Kanpur') ? 'selected' : ''; ?>>Kanpur</option>
                            <option value="Agra" data-state="Uttar Pradesh" <?php echo (!empty($user_data['city']) && $user_data['city']=='Agra') ? 'selected' : ''; ?>>Agra</option>
                            <option value="Varanasi" data-state="Uttar Pradesh" <?php echo (!empty($user_data['city']) && $user_data['city']=='Varanasi') ? 'selected' : ''; ?>>Varanasi</option>
                            <option value="Noida" data-state="Uttar Pradesh" <?php echo (!empty($user_data['city']) && $user_data['city']=='Noida') ? 'selected' : ''; ?>>Noida</option>
                        </optgroup>
                        <optgroup label="Telangana">
                            <option value="Hyderabad" data-state="Telangana" <?php echo (!empty($user_data['city']) && $user_data['city']=='Hyderabad') ? 'selected' : ''; ?>>Hyderabad</option>
                            <option value="Warangal" data-state="Telangana" <?php echo (!empty($user_data['city']) && $user_data['city']=='Warangal') ? 'selected' : ''; ?>>Warangal</option>
                        </optgroup>
                        <optgroup label="Punjab">
                            <option value="Chandigarh" data-state="Punjab" <?php echo (!empty($user_data['city']) && $user_data['city']=='Chandigarh') ? 'selected' : ''; ?>>Chandigarh</option>
                            <option value="Ludhiana" data-state="Punjab" <?php echo (!empty($user_data['city']) && $user_data['city']=='Ludhiana') ? 'selected' : ''; ?>>Ludhiana</option>
                            <option value="Amritsar" data-state="Punjab" <?php echo (!empty($user_data['city']) && $user_data['city']=='Amritsar') ? 'selected' : ''; ?>>Amritsar</option>
                        </optgroup>
                        <optgroup label="Haryana">
                            <option value="Gurgaon" data-state="Haryana" <?php echo (!empty($user_data['city']) && $user_data['city']=='Gurgaon') ? 'selected' : ''; ?>>Gurgaon</option>
                            <option value="Faridabad" data-state="Haryana" <?php echo (!empty($user_data['city']) && $user_data['city']=='Faridabad') ? 'selected' : ''; ?>>Faridabad</option>
                        </optgroup>
                        <optgroup label="Kerala">
                            <option value="Kochi" data-state="Kerala" <?php echo (!empty($user_data['city']) && $user_data['city']=='Kochi') ? 'selected' : ''; ?>>Kochi</option>
                            <option value="Thiruvananthapuram" data-state="Kerala" <?php echo (!empty($user_data['city']) && $user_data['city']=='Thiruvananthapuram') ? 'selected' : ''; ?>>Thiruvananthapuram</option>
                            <option value="Kozhikode" data-state="Kerala" <?php echo (!empty($user_data['city']) && $user_data['city']=='Kozhikode') ? 'selected' : ''; ?>>Kozhikode</option>
                        </optgroup>
                    </select>
                </div>
                <div class="form-group">
                    <label>State <span>*</span></label>
                    <input type="text" name="state" id="stateInput" required placeholder="State will auto-fill" readonly style="background: #f8fafc; cursor: not-allowed;" value="<?php echo !empty($user_data['state']) ? htmlspecialchars($user_data['state']) : ''; ?>">
                </div>
                <div class="form-group">
                    <label>ZIP Code <span>*</span></label>
                    <input type="text" name="zip" required placeholder="400001" value="<?php echo !empty($user_data['zip']) ? htmlspecialchars($user_data['zip']) : ''; ?>">
                </div>
                <div class="form-group">
                    <label>Country</label>
                    <input type="text" name="country" value="India" readonly>
                </div>
            </div>
            
            <!-- Payment Method -->
            <div class="section-title">
                <i class="fas fa-credit-card"></i> Payment Method
            </div>
            <div class="payment-methods">
                <div class="payment-option">
                    <input type="radio" name="payment_method" id="cod" value="COD" required>
                    <label for="cod">
                        <i class="fas fa-money-bill-wave"></i>
                        <span>Cash on Delivery</span>
                    </label>
                </div>
                <div class="payment-option">
                    <input type="radio" name="payment_method" id="upi" value="UPI">
                    <label for="upi">
                        <i class="fab fa-google-pay"></i>
                        <span>UPI Payment</span>
                    </label>
                </div>
                <div class="payment-option">
                    <input type="radio" name="payment_method" id="card" value="Card">
                    <label for="card">
                        <i class="fas fa-credit-card"></i>
                        <span>Card Payment</span>
                    </label>
                </div>
            </div>
            
            <!-- UPI Fields -->
            <div id="upi-fields" class="payment-fields">
                <div class="form-group">
                    <label>UPI ID <span>*</span></label>
                    <input type="text" name="upi_id" placeholder="yourname@upi">
                </div>
            </div>
            
            <!-- Card Fields -->
            <div id="card-fields" class="payment-fields">
                <div class="form-grid">
                    <div class="form-group full-width">
                        <label>Card Number <span>*</span></label>
                        <input type="text" name="card_number" placeholder="1234 5678 9012 3456" maxlength="19">
                    </div>
                    <div class="form-group">
                        <label>Expiry Date <span>*</span></label>
                        <input type="text" name="card_expiry" placeholder="MM/YY" maxlength="5">
                    </div>
                    <div class="form-group">
                        <label>CVV <span>*</span></label>
                        <input type="text" name="card_cvv" placeholder="123" maxlength="3">
                    </div>
                </div>
            </div>
            
            <button type="submit" name="place_order" class="btn-place-order">
                <i class="fas fa-lock"></i> Place Order Securely
            </button>
        </form>
    </div>
    
    <!-- Right: Order Summary -->
    <div class="order-summary">
        <div class="section-title">
            <i class="fas fa-receipt"></i> Order Summary
        </div>
        
        <?php 
        $cart_total = 0;
        if(isset($_SESSION['cart']) && !empty($_SESSION['cart'])):
            foreach($_SESSION['cart'] as $item): 
                $item_subtotal = $item['price'] * $item['qty'];
                $cart_total += $item_subtotal;
        ?>
        <div class="order-item">
            <img src="uploads/products/<?php echo $item['image']; ?>" alt="<?php echo $item['name']; ?>">
            <div class="order-item-info">
                <div class="order-item-name"><?php echo $item['name']; ?></div>
                <div class="order-item-price">₹<?php echo number_format($item['price'], 2); ?> × <?php echo $item['qty']; ?></div>
                <div class="order-item-qty">Subtotal: ₹<?php echo number_format($item_subtotal, 2); ?></div>
            </div>
        </div>
        <?php 
            endforeach;
        endif;
        ?>
        
        <div class="summary-totals">
            <div class="summary-row">
                <span><i class="fas fa-shopping-bag" style="color: var(--primary); margin-right: 8px;"></i>Subtotal:</span>
                <span style="font-weight: 800;">₹<?php echo number_format($cart_total, 2); ?></span>
            </div>
            <div class="summary-row">
                <span><i class="fas fa-shipping-fast" style="color: var(--primary); margin-right: 8px;"></i>Shipping:</span>
                <?php if ($shipping_charges == 0): ?>
                    <span style="color: var(--success); font-weight: 900;">
                        FREE <span style="background: rgba(34, 197, 94, 0.1); padding: 3px 8px; border-radius: 6px; font-size: 10px; margin-left: 5px;">SAVED ₹40</span>
                    </span>
                <?php else: ?>
                    <span style="color: var(--accent); font-weight: 800;">₹<?php echo number_format($shipping_charges, 2); ?></span>
                <?php endif; ?>
            </div>
            <div class="summary-row">
                <span><i class="fas fa-receipt" style="color: var(--primary); margin-right: 8px;"></i>GST (18%):</span>
                <span style="font-weight: 800;">₹<?php echo number_format($gst_amount, 2); ?></span>
            </div>
            <div class="summary-row total">
                <span>Grand Total:</span>
                <span>₹<?php echo number_format($grand_total, 2); ?></span>
            </div>
        </div>
    </div>
</div>

<!-- Success Modal -->
<?php if(isset($_SESSION['order_success']) && isset($_SESSION['last_order'])): 
    $order = $_SESSION['last_order'];
    unset($_SESSION['order_success']); // Clear flag after displaying
?>
<div class="success-modal" id="successModal">
    <div class="modal-content animate__animated animate__zoomIn">
        <div class="success-icon animate__animated animate__bounceIn animate__delay-1s">
            <i class="fas fa-check-circle"></i>
        </div>
        <h2 class="animate__animated animate__fadeInUp animate__delay-1s">🎉 Order Securely Placed!</h2>
        <p class="order-number animate__animated animate__fadeInUp animate__delay-1s">Order #<?php echo $order['order_reference']; ?></p>
        
        <div id="orderReceipt" style="background: #fff; padding: 30px; border-radius: 15px; margin: 20px auto; box-shadow: 0 4px 15px rgba(0,0,0,0.1); max-width: 700px;">
            <div style="text-align: center; margin-bottom: 25px; border-bottom: 3px solid var(--primary); padding-bottom: 15px;">
                <h1 style="color: var(--primary); margin: 0; font-size: 32px; font-weight: 900;">🐾 FURRYMART</h1>
                <p style="margin: 5px 0 0 0; color: var(--gray); font-size: 13px;">Your Trusted Pet Store</p>
            </div>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 25px; background: #f8fafc; padding: 20px; border-radius: 12px;">
                <div>
                    <strong style="color: var(--navy); font-size: 12px;">ORDER REFERENCE:</strong>
                    <p style="margin: 5px 0 0 0; font-weight: 800; font-size: 16px; color: var(--primary);"><?php echo $order['order_reference']; ?></p>
                </div>
                <div>
                    <strong style="color: var(--navy); font-size: 12px;">ORDER DATE:</strong>
                    <p style="margin: 5px 0 0 0; font-weight: 700;"><?php echo $order['order_date']; ?></p>
                </div>
                <div>
                    <strong style="color: var(--navy); font-size: 12px;">CUSTOMER:</strong>
                    <p style="margin: 5px 0 0 0; font-weight: 700;"><?php echo $order['customer_name']; ?></p>
                </div>
                <div>
                    <strong style="color: var(--navy); font-size: 12px;">EMAIL:</strong>
                    <p style="margin: 5px 0 0 0; font-weight: 700;"><?php echo $order['user_email']; ?></p>
                </div>
                <div>
                    <strong style="color: var(--navy); font-size: 12px;">PHONE:</strong>
                    <p style="margin: 5px 0 0 0; font-weight: 700;"><?php echo $order['customer_phone']; ?></p>
                </div>
                <div>
                    <strong style="color: var(--navy); font-size: 12px;">PAYMENT METHOD:</strong>
                    <p style="margin: 5px 0 0 0; font-weight: 700;"><?php echo $order['payment_method']; ?></p>
                </div>
                <div>
                    <strong style="color: var(--navy); font-size: 12px;">PAYMENT STATUS:</strong>
                    <?php if($order['payment_method'] == 'UPI' || $order['payment_method'] == 'Card'): ?>
                    <p style="margin: 5px 0 0 0; font-weight: 800; color: #22c55e;">✓ PAID</p>
                    <?php else: ?>
                    <p style="margin: 5px 0 0 0; font-weight: 800; color: #f59e0b;">⏱ PAY ON DELIVERY</p>
                    <?php endif; ?>
                </div>
            </div>
            
            <div style="margin-bottom: 20px;">
                <h4 style="font-size: 14px; font-weight: 800; margin-bottom: 15px; color: var(--navy); border-bottom: 2px solid #e2e8f0; padding-bottom: 10px;">
                    <i class="fas fa-shopping-bag"></i> ORDER ITEMS
                </h4>
                
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background: #f8fafc; border-bottom: 2px solid #e2e8f0;">
                            <th style="text-align: left; padding: 12px; font-size: 12px; color: var(--gray);">PRODUCT</th>
                            <th style="text-align: center; padding: 12px; font-size: 12px; color: var(--gray);">QTY</th>
                            <th style="text-align: right; padding: 12px; font-size: 12px; color: var(--gray);">PRICE</th>
                            <th style="text-align: right; padding: 12px; font-size: 12px; color: var(--gray);">SUBTOTAL</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($order['items'] as $item): ?>
                        <tr style="border-bottom: 1px solid #e2e8f0;">
                            <td style="padding: 15px 12px;">
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <img src="uploads/products/<?php echo $item['image']; ?>" 
                                         style="width: 50px; height: 50px; object-fit: cover; border-radius: 8px; border: 2px solid #e2e8f0;">
                                    <span style="font-weight: 700; font-size: 14px;"><?php echo $item['name']; ?></span>
                                </div>
                            </td>
                            <td style="text-align: center; padding: 15px 12px; font-weight: 700;"><?php echo $item['qty']; ?></td>
                            <td style="text-align: right; padding: 15px 12px;">₹<?php echo number_format($item['price'], 2); ?></td>
                            <td style="text-align: right; padding: 15px 12px; font-weight: 800;">₹<?php echo number_format($item['price'] * $item['qty'], 2); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <!-- Subtotal Row -->
                        <tr style="border-top: 2px solid #e2e8f0;">
                            <td colspan="3" style="padding: 12px; text-align: right; font-weight: 700; font-size: 14px; color: var(--gray);">
                                <i class="fas fa-shopping-bag" style="margin-right: 5px; color: var(--primary);"></i> SUBTOTAL:
                            </td>
                            <td style="padding: 12px; text-align: right; font-weight: 800; font-size: 16px;">
                                ₹<?php echo number_format($order['subtotal'], 2); ?>
                            </td>
                        </tr>
                        
                        <!-- Shipping Row -->
                        <tr>
                            <td colspan="3" style="padding: 12px; text-align: right; font-weight: 700; font-size: 14px; color: var(--gray);">
                                <i class="fas fa-shipping-fast" style="margin-right: 5px; color: var(--primary);"></i> SHIPPING CHARGES:
                            </td>
                            <td style="padding: 12px; text-align: right; font-weight: 800; font-size: 16px;">
                                <?php if ($order['shipping_charges'] == 0): ?>
                                    <span style="color: #22c55e;">FREE</span>
                                    <span style="background: rgba(34, 197, 94, 0.1); color: #22c55e; padding: 3px 8px; border-radius: 6px; font-size: 10px; margin-left: 5px; font-weight: 900;">SAVED ₹40</span>
                                <?php else: ?>
                                    <span style="color: #ef4444;">₹<?php echo number_format($order['shipping_charges'], 2); ?></span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        
                        <!-- GST Row -->
                        <tr>
                            <td colspan="3" style="padding: 12px; text-align: right; font-weight: 700; font-size: 14px; color: var(--gray);">
                                <i class="fas fa-receipt" style="margin-right: 5px; color: var(--primary);"></i> GST (18%):
                            </td>
                            <td style="padding: 12px; text-align: right; font-weight: 800; font-size: 16px;">
                                ₹<?php echo number_format($order['gst_amount'], 2); ?>
                            </td>
                        </tr>
                        
                        <!-- Grand Total Row -->
                        <tr style="background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%); border-top: 3px solid var(--primary);">
                            <td colspan="3" style="padding: 18px 12px; text-align: right; font-weight: 900; font-size: 18px; color: var(--navy);">
                                <i class="fas fa-money-check-alt" style="margin-right: 8px; color: var(--primary);"></i> GRAND TOTAL:
                            </td>
                            <td style="padding: 18px 12px; text-align: right; font-weight: 900; font-size: 24px; color: var(--primary);">
                                ₹<?php echo number_format($order['total'], 2); ?>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            
            <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 15px; border-radius: 10px; text-align: center; margin-top: 20px;">
                <p style="margin: 0; font-size: 13px;">
                    <i class="fas fa-shield-alt"></i> <strong>Secure Transaction</strong> - Your order has been safely processed and confirmed
                </p>
            </div>
            
            <p style="text-align: center; color: var(--gray); font-size: 12px; margin-top: 20px; padding-top: 20px; border-top: 2px dashed #e2e8f0;">
                <i class="fas fa-envelope"></i> Confirmation email sent | <i class="fas fa-headset"></i> Need help? Contact support@furrymart.com
            </p>
        </div>
        
        <div class="modal-buttons animate__animated animate__fadeInUp animate__delay-2s">
            <button class="btn-modal btn-print" onclick="printReceipt()" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; color: white; font-weight: 800;">
                <i class="fas fa-print"></i> Print Bill
            </button>
            <button class="btn-modal btn-pdf" onclick="downloadPDF()" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); border: none; color: white; font-weight: 800;">
                <i class="fas fa-file-pdf"></i> Save as PDF
            </button>
        </div>
        
        <button class="btn-modal btn-continue" onclick="clearOrderAndRedirect()" style="background: white; color: var(--primary); border: 2px solid var(--primary); font-weight: 800;">
            <i class="fas fa-home"></i> Continue Shopping
        </button>
    </div>
</div>

<!-- Add jsPDF Library -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

<script>
// Clear order session and redirect
function clearOrderAndRedirect() {
    fetch('clear_order_session.php')
        .then(() => window.location.href = 'index.php');
}
</script>

<?php endif; ?>

<script>
// Payment Method Toggle
document.querySelectorAll('input[name="payment_method"]').forEach(radio => {
    radio.addEventListener('change', function() {
        // Hide all payment fields
        document.querySelectorAll('.payment-fields').forEach(field => {
            field.classList.remove('active');
            field.querySelectorAll('input').forEach(input => {
                input.removeAttribute('required');
            });
        });
        
        // Show selected payment fields
        if(this.value === 'UPI') {
            const upiFields = document.getElementById('upi-fields');
            upiFields.classList.add('active');
            upiFields.querySelector('input').setAttribute('required', 'required');
        } else if(this.value === 'Card') {
            const cardFields = document.getElementById('card-fields');
            cardFields.classList.add('active');
            cardFields.querySelectorAll('input').forEach(input => {
                input.setAttribute('required', 'required');
            });
        }
    });
});

// Card number formatting
document.querySelector('input[name="card_number"]')?.addEventListener('input', function(e) {
    let value = e.target.value.replace(/\s/g, '');
    let formattedValue = value.match(/.{1,4}/g)?.join(' ') || value;
    e.target.value = formattedValue;
});

// Expiry date formatting
document.querySelector('input[name="card_expiry"]')?.addEventListener('input', function(e) {
    let value = e.target.value.replace(/\D/g, '');
    if (value.length >= 2) {
        value = value.slice(0, 2) + '/' + value.slice(2, 4);
    }
    e.target.value = value;
});

// Form validation and loading
document.getElementById('checkoutForm').addEventListener('submit', function(e) {
    const paymentMethod = document.querySelector('input[name="payment_method"]:checked');
    if(!paymentMethod) {
        e.preventDefault();
        alert('Please select a payment method');
        return;
    }
    
    // Show loading overlay
    document.getElementById('loadingOverlay').classList.add('active');
});

// Auto-fill State based on City selection
function updateState() {
    const citySelect = document.getElementById('citySelect');
    const stateInput = document.getElementById('stateInput');
    const selectedOption = citySelect.options[citySelect.selectedIndex];
    
    if(selectedOption && selectedOption.value) {
        const state = selectedOption.getAttribute('data-state');
        stateInput.value = state || '';
        
        // Add visual feedback
        stateInput.style.background = '#dcfce7';
        stateInput.style.color = '#15803d';
        stateInput.style.fontWeight = '700';
        
        setTimeout(() => {
            stateInput.style.background = '#f8fafc';
            stateInput.style.color = '';
        }, 1000);
    } else {
        stateInput.value = '';
    }
}

// Auto-trigger updateState on page load if city is pre-selected
window.addEventListener('DOMContentLoaded', function() {
    const citySelect = document.getElementById('citySelect');
    if(citySelect && citySelect.value) {
        updateState();
    }
});

// Print Receipt Function
function printReceipt() {
    // Ensure modal is fully rendered before printing
    setTimeout(() => {
        window.print();
    }, 100);
}

// Download PDF Function - v2.1 - Fixed Duplication Issue
function downloadPDF() {
    const receipt = document.getElementById('orderReceipt');
    
    // Store original styles
    const originalStyles = {
        maxHeight: receipt.style.maxHeight,
        overflow: receipt.style.overflow,
        maxWidth: receipt.style.maxWidth,
        width: receipt.style.width
    };
    
    // Prepare receipt for capture - use smaller width to fit better
    receipt.style.maxHeight = 'none';
    receipt.style.overflow = 'visible';
    receipt.style.width = '700px';
    receipt.style.maxWidth = '700px';
    
    // Small delay to let styles apply
    setTimeout(() => {
        html2canvas(receipt, {
            scale: 1.5,
            logging: false,
            backgroundColor: '#ffffff',
            useCORS: true,
            allowTaint: true,
            width: 700,
            windowWidth: 700
        }).then(canvas => {
            // Restore original styles immediately
            receipt.style.maxHeight = originalStyles.maxHeight;
            receipt.style.overflow = originalStyles.overflow;
            receipt.style.maxWidth = originalStyles.maxWidth;
            receipt.style.width = originalStyles.width;
            
            // Create PDF
            const pdf = new jspdf.jsPDF({
                orientation: 'portrait',
                unit: 'mm',
                format: 'a4',
                compress: true
            });
            
            // Page dimensions
            const pdfWidth = 210; // A4 width
            const pdfHeight = 297; // A4 height
            const borderMargin = 10; // Border position (10mm from edge)
            const contentMargin = 25; // Content margin (25mm from edge - much larger space inside border)
            const contentWidth = pdfWidth - (2 * contentMargin); // Content area width
            const contentHeight = pdfHeight - (2 * contentMargin); // Content area height
            
            // Calculate image dimensions to fit on one page
            let imgWidth = contentWidth;
            let imgHeight = (canvas.height * imgWidth) / canvas.width;
            
            // If content is too tall, scale it down to fit
            if (imgHeight > contentHeight) {
                imgHeight = contentHeight;
                imgWidth = (canvas.width * imgHeight) / canvas.height;
            }
            
            // Center the image if it's smaller than page width
            const xOffset = contentMargin + (contentWidth - imgWidth) / 2;
            
            // Add page borders FIRST (so content appears on top)
            pdf.setDrawColor(81, 137, 146); // FurryMart primary color (#518992)
            pdf.setLineWidth(0.5); // Border thickness
            pdf.rect(borderMargin, borderMargin, pdfWidth - (2 * borderMargin), pdfHeight - (2 * borderMargin)); // Outer border
            
            // Add inner border for double border effect
            pdf.setDrawColor(200, 200, 200); // Light gray for inner border
            pdf.setLineWidth(0.2);
            pdf.rect(borderMargin + 2, borderMargin + 2, pdfWidth - (2 * (borderMargin + 2)), pdfHeight - (2 * (borderMargin + 2))); // Inner border
            
            // Convert image to data URL
            const imgData = canvas.toDataURL('image/png', 1.0);
            
            // Add image on single page - always fit to one page (content goes AFTER borders)
            pdf.addImage(imgData, 'PNG', xOffset, contentMargin, imgWidth, imgHeight);
            
            // Save PDF
            const fileName = 'FURRYMART_Order_<?php echo isset($order) ? $order["order_reference"] : "Receipt"; ?>.pdf';
            pdf.save(fileName);
            
        }).catch(error => {
            console.error('PDF Error:', error);
            alert('Failed to generate PDF. Please try the Print option instead.');
            
            // Restore styles on error
            receipt.style.maxHeight = originalStyles.maxHeight;
            receipt.style.overflow = originalStyles.overflow;
            receipt.style.maxWidth = originalStyles.maxWidth;
            receipt.style.width = originalStyles.width;
        });
    }, 150);
}
</script>

<?php include 'includes/footer.php'; ?>
</body>
</html>
