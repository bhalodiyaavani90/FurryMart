<?php
session_start();
include 'db.php';

$order_id = (int)$_GET['id'];
$payment_status = isset($_GET['payment_status']) ? $_GET['payment_status'] : 'Pending';
$payment_method = isset($_GET['payment_method']) ? $_GET['payment_method'] : 'Unknown';

// Fetch order details with payment status
$order_sql = "SELECT *, COALESCE(payment_status, 'Pending') as payment_status FROM orders WHERE id = $order_id";
$order = mysqli_fetch_assoc(mysqli_query($conn, $order_sql));

// Override with passed parameters if available
if(isset($_GET['payment_status'])) {
    $payment_status = $_GET['payment_status'];
} else {
    $payment_status = $order['payment_status'];
}
if(isset($_GET['payment_method'])) {
    $payment_method = $_GET['payment_method'];
} else {
    $payment_method = $order['payment_method'];
}

// Fetch order status history
$history_sql = "SELECT * FROM order_status_history WHERE order_id = $order_id ORDER BY changed_at ASC";
$history = mysqli_query($conn, $history_sql);

$is_delivered = $order['order_status'] == 'Delivered';

// Define status flow
$statuses = [
    'Pending' => ['icon' => 'clock', 'label' => 'Order Placed'],
    'Confirmed' => ['icon' => 'check-circle', 'label' => 'Order Confirmed'],
    'Processing' => ['icon' => 'cog', 'label' => 'Processing'],
    'Shipped' => ['icon' => 'truck', 'label' => 'Shipped'],
    'Out for Delivery' => ['icon' => 'shipping-fast', 'label' => 'Out for Delivery'],
    'Delivered' => ['icon' => 'check-double', 'label' => 'Delivered']
];

$current_status = $order['order_status'];
$status_reached = false;
?>

<div style="padding: 20px;">
    <!-- Payment Status Header -->
    <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 20px; border-radius: 15px; margin-bottom: 20px; color: white; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);">
        <div>
            <h4 style="margin: 0 0 10px 0; font-size: 14px; opacity: 0.9;">💳 Payment Method</h4>
            <div style="font-size: 18px; font-weight: 900;"><?php echo htmlspecialchars($payment_method); ?></div>
        </div>
        <div style="padding: 10px 20px; border-radius: 25px; font-size: 13px; font-weight: 900; display: flex; align-items: center; gap: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.2); <?php echo $payment_status == 'Paid' ? 'background: #22c55e;' : 'background: #f59e0b;'; ?>">
            <i class="fas <?php echo $payment_status == 'Paid' ? 'fa-check-circle' : 'fa-clock'; ?>"></i>
            <?php echo $payment_status; ?>
        </div>
    </div>
    
    <!-- Order Header -->
    <div style="background: linear-gradient(135deg, #e0f2fe 0%, #dbeafe 100%); padding: 25px; border-radius: 20px; margin-bottom: 30px; text-align: center;">
        <h4 style="font-size: 18px; font-weight: 800; color: var(--navy); margin-bottom: 10px;">
            Order #<?php echo $order['order_reference']; ?>
        </h4>
        <p style="color: var(--gray); font-size: 14px; margin-bottom: 15px;">
            Placed on <?php echo date('d M Y, h:i A', strtotime($order['order_date'])); ?>
        </p>
        <span class="status-badge <?php echo strtolower(str_replace(' ', '-', $order['order_status'])); ?>">
            <?php echo $order['order_status']; ?>
        </span>
    </div>

    <?php if($is_delivered): ?>
    <!-- Delivered Message -->
    <div class="delivered-message animate__animated animate__bounceIn">
        <i class="fas fa-check-circle"></i>
        <h3>Order Delivered Successfully!</h3>
        <p>Your order was delivered on <?php echo date('d M Y, h:i A', strtotime($order['delivery_date'])); ?></p>
        <p style="margin-top: 10px; font-size: 14px;">Thank you for shopping with FurryMart! 🐾</p>
    </div>
    <?php else: ?>
    <div style="text-align: center; padding: 20px; background: #fef3c7; border-radius: 15px; margin-bottom: 30px;">
        <i class="fas fa-truck" style="font-size: 32px; color: #92400e; margin-bottom: 10px;"></i>
        <p style="color: #92400e; font-weight: 700;">Your order is on its way!</p>
    </div>
    <?php endif; ?>

    <!-- Order Timeline -->
    <div class="order-timeline">
        <?php 
        foreach($statuses as $status => $data):
            // Check if this status has been reached
            $reached = false;
            $is_active = false;
            $timestamp = null;
            $comment = null;
            
            // Search in history
            mysqli_data_seek($history, 0); // Reset pointer
            while($h = mysqli_fetch_assoc($history)) {
                if($h['status'] == $status) {
                    $reached = true;
                    $timestamp = $h['changed_at'];
                    $comment = $h['comment'];
                    break;
                }
            }
            
            // Check if it's the current status
            $is_active = ($status == $current_status);
            
            // Determine class
            $class = '';
            if($reached) $class = 'completed';
            if($is_active) $class = 'active';
        ?>
        <div class="timeline-item <?php echo $class; ?> animate__animated animate__fadeInLeft" style="animation-delay: <?php echo array_search($status, array_keys($statuses)) * 0.1; ?>s;">
            <div class="timeline-dot">
                <i class="fas fa-<?php echo $data['icon']; ?>"></i>
            </div>
            <div class="timeline-content">
                <div class="timeline-title"><?php echo $data['label']; ?></div>
                <?php if($timestamp): ?>
                <div class="timeline-date">
                    <i class="fas fa-calendar"></i> <?php echo date('d M Y, h:i A', strtotime($timestamp)); ?>
                </div>
                <?php if($comment): ?>
                <div style="margin-top: 8px; font-size: 13px; color: var(--gray); font-style: italic;">
                    "<?php echo $comment; ?>"
                </div>
                <?php endif; ?>
                <?php else: ?>
                <div class="timeline-date" style="color: #cbd5e1;">
                    <i class="fas fa-hourglass-half"></i> Pending
                </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Shipping Info -->
    <div style="background: #f8fafc; padding: 20px; border-radius: 15px; margin-top: 30px;">
        <h4 style="font-size: 14px; font-weight: 800; margin-bottom: 15px; color: var(--navy);">
            <i class="fas fa-map-marker-alt"></i> Delivery Address
        </h4>
        <p style="margin: 0; color: var(--gray); line-height: 1.6;">
            <strong><?php echo $order['customer_name']; ?></strong><br>
            <?php echo $order['shipping_address']; ?><br>
            <?php echo $order['city']; ?>, <?php echo $order['state']; ?> - <?php echo $order['zip_code']; ?><br>
            <?php echo $order['country']; ?><br>
            <i class="fas fa-phone"></i> <?php echo $order['customer_phone']; ?>
        </p>
    </div>
</div>

<style>
.status-badge {
    display: inline-block;
    padding: 8px 16px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 800;
    text-transform: uppercase;
}
.status-badge.pending { background: #fef3c7; color: #92400e; }
.status-badge.confirmed { background: #dbeafe; color: #1e40af; }
.status-badge.processing { background: #e0e7ff; color: #4338ca; }
.status-badge.shipped { background: #fce7f3; color: #9f1239; }
.status-badge.out-for-delivery { background: #fef9c3; color: #854d0e; }
.status-badge.delivered { background: #d1fae5; color: #065f46; }
</style>
