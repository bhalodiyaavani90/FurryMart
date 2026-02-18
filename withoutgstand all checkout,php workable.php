checkout
<?php
session_start();
include 'db.php';

// Check if cart is empty
if(empty($_SESSION['cart'])) {
    header("Location: cart.php");
    exit();
}

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
    
    // Calculate total
    $total = 0;
    foreach($_SESSION['cart'] as $item) {
        $total += $item['price'] * $item['qty'];
    }
    
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
            '$transaction_id', '$total', 'Pending', '$payment_status')";
    
    if(mysqli_query($conn, $sql)) {
        $order_id = mysqli_insert_id($conn);
        
        // Insert order items
        foreach($_SESSION['cart'] as $item) {
            $product_name = mysqli_real_escape_string($conn, $item['name']);
            $product_image = mysqli_real_escape_string($conn, $item['image']);
            $price = $item['price'];
            $qty = $item['qty'];
            $variant_id = $item['variant_id'] ?? 0;
            $weight_size = mysqli_real_escape_string($conn, $item['weight_size'] ?? '');
            $subtotal = $price * $qty;
            
            $item_sql = "INSERT INTO order_items (order_id, variant_id, product_name, product_image, 
                        price, quantity, weight_size, subtotal) 
                        VALUES ($order_id, $variant_id, '$product_name', '$product_image', 
                        $price, $qty, '$weight_size', $subtotal)";
            mysqli_query($conn, $item_sql);
        }
        
        // Add to status history
        mysqli_query($conn, "INSERT INTO order_status_history (order_id, status, comment) 
                            VALUES ($order_id, 'Pending', 'Order placed successfully')");
        
        // Store order details for popup (don't clear cart yet for display)
        $_SESSION['last_order'] = [
            'order_reference' => $order_reference,
            'order_id' => $order_id,
            'customer_name' => $customer_name,
            'user_email' => $user_email,
            'customer_phone' => $customer_phone,
            'payment_method' => $payment_method,
            'total' => $total,
            'items' => $_SESSION['cart'],
            'order_date' => date('d M Y, h:i A')
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
            grid-column: 1 / -1;
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
            body * {
                visibility: hidden !important;
            }
            #orderReceipt, #orderReceipt * {
                visibility: visible !important;
            }
            #orderReceipt {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                padding: 20px;
            }
            .success-modal {
                position: static !important;
                background: white !important;
                backdrop-filter: none !important;
            }
            .modal-content {
                box-shadow: none !important;
                max-width: 100% !important;
                padding: 0 !important;
            }
            .modal-buttons, .btn-modal, .success-icon, h2, .order-number, .btn-continue {
                display: none !important;
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
                    <input type="text" name="name" required placeholder="John Doe">
                </div>
                <div class="form-group">
                    <label>Email Address <span>*</span></label>
                    <input type="email" name="email" required placeholder="john@example.com" 
                           value="<?php echo $_SESSION['user_email'] ?? ''; ?>">
                </div>
                <div class="form-group">
                    <label>Phone Number <span>*</span></label>
                    <input type="tel" name="phone" required placeholder="+91 9876543210">
                </div>
                <div class="form-group">
                    <label>Date of Birth</label>
                    <input type="date" name="dob">
                </div>
            </div>
            
            <!-- Shipping Address -->
            <div class="section-title">
                <i class="fas fa-map-marker-alt"></i> Shipping Address
            </div>
            <div class="form-grid">
                <div class="form-group full-width">
                    <label>Street Address <span>*</span></label>
                    <textarea name="address" required placeholder="House No, Building, Street"></textarea>
                </div>
                <div class="form-group">
                    <label>City <span>*</span></label>
                    <select name="city" id="citySelect" required onchange="updateState()">
                        <option value="">Select City</option>
                        <optgroup label="Gujarat">
                            <option value="Ahmedabad" data-state="Gujarat">Ahmedabad</option>
                            <option value="Surat" data-state="Gujarat">Surat</option>
                            <option value="Vadodara" data-state="Gujarat">Vadodara</option>
                            <option value="Rajkot" data-state="Gujarat">Rajkot</option>
                            <option value="Morbi" data-state="Gujarat">Morbi</option>
                            <option value="Gandhinagar" data-state="Gujarat">Gandhinagar</option>
                            <option value="Bhavnagar" data-state="Gujarat">Bhavnagar</option>
                        </optgroup>
                        <optgroup label="Maharashtra">
                            <option value="Mumbai" data-state="Maharashtra">Mumbai</option>
                            <option value="Pune" data-state="Maharashtra">Pune</option>
                            <option value="Nagpur" data-state="Maharashtra">Nagpur</option>
                            <option value="Nashik" data-state="Maharashtra">Nashik</option>
                            <option value="Thane" data-state="Maharashtra">Thane</option>
                            <option value="Aurangabad" data-state="Maharashtra">Aurangabad</option>
                        </optgroup>
                        <optgroup label="Karnataka">
                            <option value="Bangalore" data-state="Karnataka">Bangalore</option>
                            <option value="Mysore" data-state="Karnataka">Mysore</option>
                            <option value="Mangalore" data-state="Karnataka">Mangalore</option>
                            <option value="Hubli" data-state="Karnataka">Hubli</option>
                        </optgroup>
                        <optgroup label="Tamil Nadu">
                            <option value="Chennai" data-state="Tamil Nadu">Chennai</option>
                            <option value="Coimbatore" data-state="Tamil Nadu">Coimbatore</option>
                            <option value="Madurai" data-state="Tamil Nadu">Madurai</option>
                            <option value="Tiruchirappalli" data-state="Tamil Nadu">Tiruchirappalli</option>
                        </optgroup>
                        <optgroup label="Rajasthan">
                            <option value="Jaipur" data-state="Rajasthan">Jaipur</option>
                            <option value="Jodhpur" data-state="Rajasthan">Jodhpur</option>
                            <option value="Udaipur" data-state="Rajasthan">Udaipur</option>
                            <option value="Kota" data-state="Rajasthan">Kota</option>
                        </optgroup>
                        <optgroup label="Delhi">
                            <option value="New Delhi" data-state="Delhi">New Delhi</option>
                            <option value="Delhi" data-state="Delhi">Delhi</option>
                        </optgroup>
                        <optgroup label="West Bengal">
                            <option value="Kolkata" data-state="West Bengal">Kolkata</option>
                            <option value="Howrah" data-state="West Bengal">Howrah</option>
                            <option value="Durgapur" data-state="West Bengal">Durgapur</option>
                        </optgroup>
                        <optgroup label="Uttar Pradesh">
                            <option value="Lucknow" data-state="Uttar Pradesh">Lucknow</option>
                            <option value="Kanpur" data-state="Uttar Pradesh">Kanpur</option>
                            <option value="Agra" data-state="Uttar Pradesh">Agra</option>
                            <option value="Varanasi" data-state="Uttar Pradesh">Varanasi</option>
                            <option value="Noida" data-state="Uttar Pradesh">Noida</option>
                        </optgroup>
                        <optgroup label="Telangana">
                            <option value="Hyderabad" data-state="Telangana">Hyderabad</option>
                            <option value="Warangal" data-state="Telangana">Warangal</option>
                        </optgroup>
                        <optgroup label="Punjab">
                            <option value="Chandigarh" data-state="Punjab">Chandigarh</option>
                            <option value="Ludhiana" data-state="Punjab">Ludhiana</option>
                            <option value="Amritsar" data-state="Punjab">Amritsar</option>
                        </optgroup>
                        <optgroup label="Haryana">
                            <option value="Gurgaon" data-state="Haryana">Gurgaon</option>
                            <option value="Faridabad" data-state="Haryana">Faridabad</option>
                        </optgroup>
                        <optgroup label="Kerala">
                            <option value="Kochi" data-state="Kerala">Kochi</option>
                            <option value="Thiruvananthapuram" data-state="Kerala">Thiruvananthapuram</option>
                            <option value="Kozhikode" data-state="Kerala">Kozhikode</option>
                        </optgroup>
                    </select>
                </div>
                <div class="form-group">
                    <label>State <span>*</span></label>
                    <input type="text" name="state" id="stateInput" required placeholder="State will auto-fill" readonly style="background: #f8fafc; cursor: not-allowed;">
                </div>
                <div class="form-group">
                    <label>ZIP Code <span>*</span></label>
                    <input type="text" name="zip" required placeholder="400001">
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
        $total = 0;
        if(isset($_SESSION['cart']) && !empty($_SESSION['cart'])):
            foreach($_SESSION['cart'] as $item): 
                $subtotal = $item['price'] * $item['qty'];
                $total += $subtotal;
        ?>
        <div class="order-item">
            <img src="uploads/products/<?php echo $item['image']; ?>" alt="<?php echo $item['name']; ?>">
            <div class="order-item-info">
                <div class="order-item-name"><?php echo $item['name']; ?></div>
                <div class="order-item-price">₹<?php echo number_format($item['price'], 2); ?> × <?php echo $item['qty']; ?></div>
                <div class="order-item-qty">Subtotal: ₹<?php echo number_format($subtotal, 2); ?></div>
            </div>
        </div>
        <?php 
            endforeach;
        endif;
        ?>
        
        <div class="summary-totals">
            <div class="summary-row">
                <span>Subtotal:</span>
                <span>₹<?php echo number_format($total, 2); ?></span>
            </div>
            <div class="summary-row">
                <span>Shipping:</span>
                <span style="color: var(--success);">FREE</span>
            </div>
            <div class="summary-row total">
                <span>Total:</span>
                <span>₹<?php echo number_format($total, 2); ?></span>
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
        
        <div id="orderReceipt" style="background: #fff; padding: 30px; border-radius: 15px; margin: 20px 0; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
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
                        <tr style="background: #f0f9ff; border-top: 3px solid var(--primary);">
                            <td colspan="3" style="padding: 15px 12px; text-align: right; font-weight: 800; font-size: 16px; color: var(--navy);">
                                TOTAL AMOUNT:
                            </td>
                            <td style="padding: 15px 12px; text-align: right; font-weight: 900; font-size: 20px; color: var(--primary);">
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
        
        <button class="btn-modal btn-continue" onclick="clearOrderAndRedirect()" style="margin-top: 15px; background: white; color: var(--primary); border: 2px solid var(--primary); font-weight: 800;">
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

// Print Receipt Function
function printReceipt() {
    window.print();
}

// Download PDF Function
function downloadPDF() {
    const receipt = document.getElementById('orderReceipt');
    
    html2canvas(receipt, {
        scale: 2,
        logging: false,
        backgroundColor: '#ffffff'
    }).then(canvas => {
        const imgData = canvas.toDataURL('image/png');
        const pdf = new jspdf.jsPDF({
            orientation: 'portrait',
            unit: 'mm',
            format: 'a4'
        });
        
        const imgWidth = 210;
        const imgHeight = (canvas.height * imgWidth) / canvas.width;
        
        pdf.addImage(imgData, 'PNG', 0, 0, imgWidth, imgHeight);
        pdf.save('FURRYMART_Order_<?php echo isset($order) ? $order["order_reference"] : "Receipt"; ?>.pdf');
    });
}
</script>

<?php include 'includes/footer.php'; ?>
</body>
</html>
