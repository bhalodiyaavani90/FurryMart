<?php
session_start();
include 'db.php';

// Set timezone to India Standard Time
date_default_timezone_set('Asia/Kolkata');

// Check if user is logged in
if (!isset($_SESSION['email'])) {
    header("Location: login.php?redirect=my-orders.php");
    exit();
}

$user_email = mysqli_real_escape_string($conn, $_SESSION['email']);

// Fetch user's orders with error handling including payment_status
$orders_sql = "SELECT o.*, COUNT(oi.id) as item_count,
               COALESCE(o.payment_status, 'Pending') as payment_status,
               (SELECT comment FROM order_status_history 
                WHERE order_id = o.id AND status = 'Cancelled' 
                ORDER BY changed_at DESC LIMIT 1) as cancellation_comment
               FROM orders o 
               LEFT JOIN order_items oi ON o.id = oi.order_id 
               WHERE o.user_email = '$user_email' 
               GROUP BY o.id 
               ORDER BY o.order_date DESC";
$orders_result = mysqli_query($conn, $orders_sql);

if (!$orders_result) {
    die("Database error: " . mysqli_error($conn));
}
?>

<?php include "includes/header.php"; ?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
        :root {
            --primary: #518992;
            --navy: #0f1c3f;
            --accent: #f87171;
            --success: #22c55e;
            --warning: #f59e0b;
            --danger: #ef4444;
            --gray: #94a3b8;
            --bg: #f8fafc;
        }
        
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: var(--bg); color: var(--navy); }
        
        /* Hero Section */
        .hero-orders {
            height: 400px;
            background: linear-gradient(rgba(15, 28, 63, 0.7), rgba(81, 137, 146, 0.7)), 
                        url('uploads/herobanner.jpg') center/cover no-repeat;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            position: relative;
            overflow: hidden;
            margin-bottom: 50px;
        }
        .hero-orders::before {
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
        .hero-content {
            text-align: center;
            z-index: 2;
        }
        .hero-content h1 {
            font-size: 56px;
            font-weight: 900;
            margin-bottom: 15px;
            text-shadow: 0 4px 20px rgba(0,0,0,0.3);
        }
        .hero-content p {
            font-size: 18px;
            opacity: 0.9;
            font-weight: 600;
        }
        
        /* Main Container */
        .orders-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 30px 80px;
        }
        
        .page-title {
            font-size: 32px;
            font-weight: 900;
            color: var(--navy);
            margin-bottom: 10px;
        }
        .page-subtitle {
            color: var(--gray);
            margin-bottom: 40px;
            font-size: 16px;
        }
        
        /* Orders Grid */
        .orders-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(400px, 1fr));
            gap: 30px;
        }
        
        /* Order Card */
        .order-card {
            background: white;
            border-radius: 25px;
            padding: 30px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.05);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        .order-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 50px rgba(0,0,0,0.1);
        }
        .order-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 5px;
            height: 100%;
            background: var(--primary);
        }
        
        .order-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 20px;
            padding-bottom: 20px;
            border-bottom: 2px solid #f1f5f9;
        }
        .order-number {
            font-size: 18px;
            font-weight: 900;
            color: var(--navy);
        }
        .order-date {
            font-size: 13px;
            color: var(--gray);
            margin-top: 5px;
        }
        
        /* Status Badge */
        .status-badge {
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .status-badge.pending { background: #fef3c7; color: #92400e; }
        .status-badge.confirmed { background: #dbeafe; color: #1e40af; }
        .status-badge.processing { background: #e0e7ff; color: #4338ca; }
        .status-badge.shipped { background: #fce7f3; color: #9f1239; }
        .status-badge.out-for-delivery { background: #fef9c3; color: #854d0e; }
        .status-badge.delivered { background: #d1fae5; color: #065f46; }
        .status-badge.cancelled { background: #fee2e2; color: #991b1b; }
        
        /* Payment Status Badge - Enhanced */
        .payment-badge {
            padding: 8px 14px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            border: 2px solid;
        }
        .payment-badge.paid { 
            background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%); 
            color: #065f46;
            border-color: #34d399;
        }
        .payment-badge.pending { 
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); 
            color: #92400e;
            border-color: #fbbf24;
        }
        .payment-badge.failed { 
            background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%); 
            color: #991b1b;
            border-color: #f87171;
        }
        .payment-badge i {
            font-size: 13px;
            animation: badgePulse 2s infinite;
        }
        @keyframes badgePulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.6; }
        }
                /* Delivered Order Card - Green Border */
        .order-card.delivered {
            border: 3px solid var(--success) !important;
            background: linear-gradient(to bottom, #d1fae5, white) !important;
            position: relative;
        }
        .order-card.delivered::before {
            background: var(--success) !important;
        }
        .order-card.delivered::after {
            content: '✓ DELIVERED';
            position: absolute;
            top: 20px;
            right: 20px;
            background: var(--success);
            color: white;
            padding: 8px 16px;
            border-radius: 25px;
            font-size: 11px;
            font-weight: 900;
            letter-spacing: 1px;
            box-shadow: 0 4px 15px rgba(34, 197, 94, 0.4);
            animation: bounceIn 0.6s;
            z-index: 10;
        }
                /* Delivered Order Card - Green Border */
        .order-card.delivered {
            border: 3px solid var(--success) !important;
            background: linear-gradient(to bottom, #d1fae5, white) !important;
            position: relative;
        }
        .order-card.delivered::before {
            background: var(--success) !important;
        }
        .order-card.delivered::after {
            content: '✓ DELIVERED';
            position: absolute;
            top: 20px;
            right: 20px;
            background: var(--success);
            color: white;
            padding: 8px 16px;
            border-radius: 25px;
            font-size: 11px;
            font-weight: 900;
            letter-spacing: 1px;
            box-shadow: 0 4px 15px rgba(34, 197, 94, 0.4);
            animation: bounceIn 0.6s;
            z-index: 10;
        }
        
        /* Cancelled Order Card - Red Border */
        .order-card.cancelled {
            border: 3px solid var(--danger) !important;
            background: linear-gradient(to bottom, #fee2e2, white) !important;
            position: relative;
        }
        .order-card.cancelled::before {
            background: var(--danger) !important;
        }
        .order-card.cancelled::after {
            content: '✖ CANCELLED';
            position: absolute;
            top: 20px;
            right: 20px;
            background: var(--danger);
            color: white;
            padding: 8px 16px;
            border-radius: 25px;
            font-size: 11px;
            font-weight: 900;
            letter-spacing: 1px;
            box-shadow: 0 4px 15px rgba(239, 68, 68, 0.4);
            animation: shake 0.5s;
            z-index: 10;
        }
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }
        @keyframes bounceIn {
            0% { transform: scale(0); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }
        
        .cancellation-note {
            background: #fff;
            border: 2px solid #fca5a5;
            border-left: 5px solid var(--danger);
            padding: 18px 20px;
            border-radius: 15px;
            margin-top: 15px;
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.08);
        }
        .cancellation-note-header {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 12px;
            color: #991b1b;
        }
        .cancellation-note-header i {
            font-size: 18px;
            color: var(--danger);
        }
        .cancellation-note-header strong {
            font-size: 13px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .cancellation-note-body {
            font-size: 14px;
            color: #7f1d1d;
            font-weight: 600;
            line-height: 1.6;
            padding-left: 28px;
        }
        
        .order-info {
            margin-bottom: 20px;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 12px;
            font-size: 14px;
        }
        .info-label {
            color: var(--gray);
            font-weight: 600;
        }
        .info-value {
            font-weight: 800;
            color: var(--navy);
        }
        
        .order-actions {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }
        .btn-action {
            padding: 12px 20px;
            border: none;
            border-radius: 12px;
            font-weight: 800;
            font-size: 13px;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            text-decoration: none;
        }
        .btn-track {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        .btn-track:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
        }
        .btn-track:disabled {
            background: #e2e8f0;
            color: #94a3b8;
            cursor: not-allowed;
            transform: none;
        }
        .btn-pdf {
            background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
            color: white;
        }
        .btn-pdf:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(34, 197, 94, 0.4);
        }
        .btn-details {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
        }
        .btn-details:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(240, 147, 251, 0.4);
        }
        .btn-shop-new {
            background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
            color: white;
        }
        .btn-shop-new:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(34, 197, 94, 0.4);
        }
        
        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 100px 20px;
            grid-column: 1 / -1;
        }
        .empty-state i {
            font-size: 80px;
            color: var(--gray);
            opacity: 0.3;
            margin-bottom: 20px;
        }
        .empty-state h3 {
            font-size: 24px;
            font-weight: 900;
            color: var(--navy);
            margin-bottom: 10px;
        }
        .empty-state p {
            color: var(--gray);
            margin-bottom: 30px;
        }
        .btn-shop {
            display: inline-block;
            padding: 15px 30px;
            background: var(--primary);
            color: white;
            text-decoration: none;
            border-radius: 15px;
            font-weight: 800;
            transition: 0.3s;
        }
        .btn-shop:hover {
            background: var(--navy);
            transform: translateY(-2px);
        }
        
        /* Modal */
        .modal {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(15, 28, 63, 0.95);
            backdrop-filter: blur(10px);
            z-index: 10000;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .modal.active {
            display: flex;
        }
        .modal-content {
            background: white;
            max-width: 900px;
            width: 100%;
            border-radius: 30px;
            max-height: 90vh;
            overflow-y: auto;
            position: relative;
        }
        .modal-header {
            padding: 30px;
            background: linear-gradient(135deg, var(--navy) 0%, var(--primary) 100%);
            color: white;
            border-radius: 30px 30px 0 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .modal-header h3 {
            font-size: 24px;
            font-weight: 900;
        }
        .modal-close {
            font-size: 32px;
            cursor: pointer;
            transition: 0.3s;
            color: white;
        }
        .modal-close:hover {
            transform: rotate(90deg);
        }
        .modal-body {
            padding: 40px;
        }
        
        /* Order Timeline */
        .order-timeline {
            position: relative;
            padding-left: 40px;
            margin: 40px 0;
        }
        .order-timeline::before {
            content: '';
            position: absolute;
            left: 15px;
            top: 0;
            bottom: 0;
            width: 4px;
            background: #e2e8f0;
        }
        .timeline-item {
            position: relative;
            margin-bottom: 30px;
            padding-bottom: 30px;
        }
        .timeline-item:last-child {
            margin-bottom: 0;
            padding-bottom: 0;
        }
        .timeline-dot {
            position: absolute;
            left: -32px;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: white;
            border: 4px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            z-index: 2;
        }
        .timeline-item.active .timeline-dot {
            background: var(--primary);
            border-color: var(--primary);
            color: white;
            box-shadow: 0 0 0 8px rgba(81, 137, 146, 0.2);
        }
        .timeline-item.completed .timeline-dot {
            background: var(--success);
            border-color: var(--success);
            color: white;
        }
        .timeline-content {
            background: #f8fafc;
            padding: 20px;
            border-radius: 15px;
        }
        .timeline-title {
            font-weight: 800;
            font-size: 16px;
            color: var(--navy);
            margin-bottom: 5px;
        }
        .timeline-date {
            font-size: 13px;
            color: var(--gray);
        }
        
        /* Delivered Message */
        .delivered-message {
            background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
            padding: 30px;
            border-radius: 20px;
            text-align: center;
            margin: 30px 0;
            border: 3px solid #065f46;
        }
        .delivered-message i {
            font-size: 60px;
            color: #065f46;
            margin-bottom: 15px;
        }
        .delivered-message h3 {
            font-size: 28px;
            font-weight: 900;
            color: #065f46;
            margin-bottom: 10px;
        }
        .delivered-message p {
            color: #047857;
            font-weight: 600;
        }
        
        /* Filter Section - Compact Design */
        .filters-container {
            background: white;
            padding: 20px 25px;
            border-radius: 15px;
            margin-bottom: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.06);
        }
        .filters-wrapper {
            display: flex;
            align-items: center;
            gap: 20px;
            flex-wrap: wrap;
        }
        .filter-group {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .filter-label {
            font-size: 12px;
            font-weight: 800;
            color: var(--gray);
            white-space: nowrap;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .filter-label i {
            color: var(--primary);
            font-size: 13px;
        }
        .filter-select {
            padding: 8px 35px 8px 15px;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 700;
            color: var(--navy);
            background: white;
            cursor: pointer;
            transition: all 0.3s ease;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%23518992' d='M6 9L1 4h10z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 12px center;
            min-width: 160px;
        }
        .filter-select:hover {
            border-color: var(--primary);
            background-color: #f0f9ff;
        }
        .filter-select:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(81, 137, 146, 0.1);
        }
        .filter-reset-btn {
            padding: 8px 16px;
            background: linear-gradient(135deg, #ef4444, #dc2626);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 12px;
            font-weight: 800;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 6px;
            margin-left: auto;
        }
        .filter-reset-btn:hover {
            background: linear-gradient(135deg, #dc2626, #b91c1c);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
        }
        .filter-results {
            font-size: 12px;
            font-weight: 700;
            color: var(--primary);
            white-space: nowrap;
            padding: 8px 16px;
            background: #f0f9ff;
            border-radius: 10px;
        }
        .filter-results span {
            font-weight: 900;
            font-size: 14px;
        }
        
        @media (max-width: 768px) {
            .orders-grid {
                grid-template-columns: 1fr;
            }
            .hero-content h1 {
                font-size: 36px;
            }
            .filters-wrapper {
                flex-direction: column;
                align-items: stretch;
            }
            .filter-group {
                flex-direction: column;
                align-items: stretch;
            }
            .filter-select {
                min-width: 100%;
            }
            .filter-reset-btn {
                margin-left: 0;
                justify-content: center;
            }
        }
    </style>

<!-- Hero Section -->
<div class="hero-orders animate__animated animate__fadeIn">
    <div class="hero-content">
        <h1 class="animate__animated animate__fadeInDown">📦 My Orders</h1>
        <p class="animate__animated animate__fadeInUp animate__delay-1s">Track and manage your orders</p>
    </div>
</div>

<!-- Main Container -->
<div class="orders-container">
    <!-- Compact Filters Section -->
    <div class="filters-container animate__animated animate__fadeIn">
        <div class="filters-wrapper">
            <!-- Status Filter -->
            <div class="filter-group">
                <label class="filter-label">
                    <i class="fas fa-filter"></i>
                    Status:
                </label>
                <select class="filter-select" id="statusFilter" onchange="filterOrders('status', this.value)">
                    <option value="all">All Orders</option>
                    <option value="Pending">⏱️ Pending</option>
                    <option value="Confirmed">✅ Confirmed</option>
                    <option value="Processing">⚙️ Processing</option>
                    <option value="Shipped">🚚 Shipped</option>
                    <option value="Out for Delivery">🚀 Out for Delivery</option>
                    <option value="Delivered">✔️ Delivered</option>
                    <option value="Cancelled">❌ Cancelled</option>
                </select>
            </div>
            
            <!-- Month Filter -->
            <div class="filter-group">
                <label class="filter-label">
                    <i class="fas fa-calendar-alt"></i>
                    Month:
                </label>
                <select class="filter-select" id="monthFilter" onchange="filterOrders('month', this.value)">
                    <option value="all">All Months</option>
                    <?php
                    // Get unique months from orders
                    $months_sql = "SELECT DISTINCT DATE_FORMAT(order_date, '%Y-%m') as order_month, 
                                          DATE_FORMAT(order_date, '%M %Y') as month_display 
                                   FROM orders 
                                   WHERE user_email = '$user_email' 
                                   ORDER BY order_month DESC";
                    $months_result = mysqli_query($conn, $months_sql);
                    while($month = mysqli_fetch_assoc($months_result)):
                    ?>
                    <option value="<?php echo $month['order_month']; ?>">
                        📅 <?php echo $month['month_display']; ?>
                    </option>
                    <?php endwhile; ?>
                </select>
            </div>
            
            <!-- Results Counter -->
            <div class="filter-results">
                <span id="resultCount"><?php echo mysqli_num_rows($orders_result); ?></span> orders
            </div>
            
            <!-- Reset Button -->
            <button class="filter-reset-btn" onclick="resetFilters()">
                <i class="fas fa-redo"></i> Reset
            </button>
        </div>
    </div>
    
    <div class="orders-grid">
        <?php if($orders_result && mysqli_num_rows($orders_result) > 0): ?>
            <?php while($order = mysqli_fetch_assoc($orders_result)): 
                $status_class = strtolower(str_replace(' ', '-', $order['order_status']));
                $is_delivered = $order['order_status'] == 'Delivered';
                $is_cancelled = $order['order_status'] == 'Cancelled';
                $card_class = $is_cancelled ? 'cancelled' : ($is_delivered ? 'delivered' : '');
                $order_month = date('Y-m', strtotime($order['order_date']));
            ?>
            <div class="order-card <?php echo $card_class; ?> animate__animated animate__fadeInUp" 
                 data-status="<?php echo $order['order_status']; ?>" 
                 data-month="<?php echo $order_month; ?>">
                <div class="order-header">
                    <div>
                        <div class="order-number">Order #<?php echo $order['order_reference']; ?></div>
                        <div class="order-date"><?php echo date('d M Y, h:i A', strtotime($order['order_date'])); ?></div>
                    </div>
                    <div style="display: flex; flex-direction: column; gap: 8px; align-items: flex-end;">
                        <span class="status-badge <?php echo $status_class; ?>">
                            <?php echo $order['order_status']; ?>
                        </span>
                        <?php 
                        $payment_status = isset($order['payment_status']) ? $order['payment_status'] : 'Pending';
                        $payment_class = strtolower($payment_status);
                        $payment_icon_class = $payment_status == 'Paid' ? 'fa-check-circle' : ($payment_status == 'Failed' ? 'fa-times-circle' : 'fa-clock');
                        ?>
                        <span class="payment-badge <?php echo $payment_class; ?>">
                            <i class="fas <?php echo $payment_icon_class; ?>"></i> <?php echo $payment_status; ?>
                        </span>
                    </div>
                </div>
                
                <div class="order-info">
                    <div class="info-row">
                        <span class="info-label"><i class="fas fa-box"></i> Items:</span>
                        <span class="info-value"><?php echo $order['item_count']; ?> item(s)</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label"><i class="fas fa-wallet"></i> Total:</span>
                        <span class="info-value">₹<?php echo number_format($order['total_amount'], 2); ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label"><i class="fas fa-credit-card"></i> Payment:</span>
                        <span class="info-value"><?php echo $order['payment_method']; ?></span>
                    </div>
                </div>
                
                <?php if($is_cancelled && !empty($order['cancellation_comment'])): ?>
                <div class="cancellation-note">
                    <div class="cancellation-note-header">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>Cancellation Reason</strong>
                    </div>
                    <div class="cancellation-note-body">
                        <?php echo htmlspecialchars($order['cancellation_comment']); ?>
                    </div>
                </div>
                <?php endif; ?>
                
                <div class="order-actions">
                    <?php if($is_cancelled): ?>
                        <!-- Cancelled Order - Show Continue Shopping -->
                        <a href="index.php" class="btn-action btn-shop-new">
                            <i class="fas fa-shopping-cart"></i> Continue Shopping
                        </a>
                        <button class="btn-action btn-details" onclick="viewOrderDetails(<?php echo $order['id']; ?>)">
                            <i class="fas fa-receipt"></i> View Details
                        </button>
                    <?php elseif($is_delivered): ?>
                        <!-- Delivered Order - Show PDF Download -->
                        <a href="generate_receipt_pdf.php?id=<?php echo $order['id']; ?>" class="btn-action btn-pdf">
                            <i class="fas fa-file-pdf"></i> Download Receipt
                        </a>
                        <button class="btn-action btn-details" onclick="viewOrderDetails(<?php echo $order['id']; ?>)">
                            <i class="fas fa-receipt"></i> View Details
                        </button>
                    <?php else: ?>
                        <!-- Active Order - Show Track Order -->
                        <button class="btn-action btn-track" 
                                onclick="trackOrder(<?php echo $order['id']; ?>, '<?php echo $order['order_status']; ?>', '<?php echo $payment_status; ?>', '<?php echo $order['payment_method']; ?>', false)">
                            <i class="fas fa-map-marker-alt"></i> Track Order
                        </button>
                        <button class="btn-action btn-details" onclick="viewOrderDetails(<?php echo $order['id']; ?>)">
                            <i class="fas fa-receipt"></i> View Details
                        </button>
                    <?php endif; ?>
                </div>
            </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="empty-state animate__animated animate__fadeIn">
                <i class="fas fa-shopping-bag"></i>
                <h3>No Orders Yet</h3>
                <p>You haven't placed any orders. Start shopping now!</p>
                <a href="index.php" class="btn-shop">
                    <i class="fas fa-shopping-cart"></i> Start Shopping
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Track Order Modal -->
<div id="trackModal" class="modal">
    <div class="modal-content animate__animated animate__zoomIn">
        <div class="modal-header">
            <h3><i class="fas fa-route"></i> Order Tracking</h3>
            <span class="modal-close" onclick="closeTrackModal()">&times;</span>
        </div>
        <div class="modal-body" id="trackModalBody">
            <!-- Content loaded via AJAX -->
        </div>
    </div>
</div>

<!-- Order Details Modal -->
<div id="detailsModal" class="modal">
    <div class="modal-content animate__animated animate__zoomIn">
        <div class="modal-header">
            <h3><i class="fas fa-file-invoice"></i> Order Details</h3>
            <span class="modal-close" onclick="closeDetailsModal()">&times;</span>
        </div>
        <div class="modal-body" id="detailsModalBody">
            <!-- Content loaded via AJAX -->
        </div>
    </div>
</div>

<script>
// Filter functionality
let activeFilters = {
    status: 'all',
    month: 'all'
};

function filterOrders(filterType, value) {
    // Update active filter
    activeFilters[filterType] = value;
    
    // Apply filters
    applyFilters();
}

function applyFilters() {
    const orderCards = document.querySelectorAll('.order-card');
    let visibleCount = 0;
    
    orderCards.forEach(card => {
        const cardStatus = card.getAttribute('data-status');
        const cardMonth = card.getAttribute('data-month');
        
        let showCard = true;
        
        // Check status filter
        if(activeFilters.status !== 'all' && cardStatus !== activeFilters.status) {
            showCard = false;
        }
        
        // Check month filter
        if(activeFilters.month !== 'all' && cardMonth !== activeFilters.month) {
            showCard = false;
        }
        
        // Show/hide card with animation
        if(showCard) {
            card.style.display = 'block';
            card.classList.add('animate__fadeIn');
            visibleCount++;
        } else {
            card.style.display = 'none';
            card.classList.remove('animate__fadeIn');
        }
    });
    
    // Update results counter
    document.getElementById('resultCount').textContent = visibleCount;
    
    // Show empty state if no results
    const emptyState = document.querySelector('.empty-state');
    if(emptyState) {
        emptyState.style.display = visibleCount === 0 ? 'block' : 'none';
    }
}

function resetFilters() {
    activeFilters = {
        status: 'all',
        month: 'all'
    };
    
    // Reset dropdowns
    document.getElementById('statusFilter').value = 'all';
    document.getElementById('monthFilter').value = 'all';
    
    // Show all orders
    applyFilters();
}

// Track Order with Payment Status
function trackOrder(orderId, status, paymentStatus, paymentMethod, isDelivered) {
    fetch(`track_order.php?id=${orderId}&payment_status=${paymentStatus}&payment_method=${paymentMethod}`)
        .then(res => res.text())
        .then(html => {
            document.getElementById('trackModalBody').innerHTML = html;
            document.getElementById('trackModal').classList.add('active');
            
            // Show delivered message if status is delivered
            if(isDelivered) {
                setTimeout(() => {
                    Swal.fire({
                        icon: 'success',
                        title: '🎉 Order Delivered!',
                        html: '<p style="font-size: 16px; font-weight: 600;">Your order has been successfully delivered!</p>',
                        confirmButtonColor: '#22c55e',
                        confirmButtonText: 'Great!'
                    });
                }, 500);
            }
        });
}

function closeTrackModal() {
    document.getElementById('trackModal').classList.remove('active');
}

// View Order Details
function viewOrderDetails(orderId) {
    fetch(`order_details.php?id=${orderId}`)
        .then(res => res.text())
        .then(html => {
            document.getElementById('detailsModalBody').innerHTML = html;
            document.getElementById('detailsModal').classList.add('active');
        });
}

function closeDetailsModal() {
    document.getElementById('detailsModal').classList.remove('active');
}

// Close modals on outside click
window.onclick = function(event) {
    const trackModal = document.getElementById('trackModal');
    const detailsModal = document.getElementById('detailsModal');
    if (event.target == trackModal) {
        closeTrackModal();
    }
    if (event.target == detailsModal) {
        closeDetailsModal();
    }
}
</script>

<?php include "includes/footer.php"; ?>
