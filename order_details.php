<?php
session_start();
include 'db.php';

// Set timezone to India Standard Time
date_default_timezone_set('Asia/Kolkata');

$order_id = (int)$_GET['id'];

// Fetch order details with payment status
$order_sql = "SELECT *, COALESCE(payment_status, 'Pending') as payment_status FROM orders WHERE id = $order_id";
$order = mysqli_fetch_assoc(mysqli_query($conn, $order_sql));

// Fetch order items with product_id
$items_sql = "SELECT oi.*, oi.product_id FROM order_items oi WHERE oi.order_id = $order_id";
$items_result = mysqli_query($conn, $items_sql);

if (!$items_result) {
    die("Error fetching order items: " . mysqli_error($conn));
}
?>

<div style="padding: 20px;">
    <!-- Payment Status Banner -->
    <div style="background: linear-gradient(135deg, <?php echo $order['payment_status'] == 'Paid' ? '#22c55e, #16a34a' : '#f59e0b, #d97706'; ?>); padding: 15px 20px; border-radius: 15px; margin-bottom: 20px; color: white; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 8px 20px rgba(0,0,0,0.2);">
        <div style="display: flex; align-items: center; gap: 12px;">
            <i class="fas <?php echo $order['payment_status'] == 'Paid' ? 'fa-check-circle' : 'fa-clock'; ?>" style="font-size: 24px;"></i>
            <div>
                <div style="font-size: 12px; opacity: 0.9; margin-bottom: 3px;">Payment Status</div>
                <div style="font-size: 18px; font-weight: 900;"><?php echo $order['payment_status']; ?></div>
            </div>
        </div>
        <div style="text-align: right;">
            <div style="font-size: 11px; opacity: 0.9; margin-bottom: 3px;">Payment Method</div>
            <div style="font-size: 14px; font-weight: 900;"><?php echo $order['payment_method']; ?></div>
        </div>
    </div>
    
    <!-- Order Header -->
    <div style="background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%); padding: 25px; border-radius: 20px; margin-bottom: 30px;">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div>
                <strong style="color: var(--gray); font-size: 12px; text-transform: uppercase;">Order Reference</strong>
                <p style="margin: 5px 0 0 0; font-weight: 900; font-size: 20px; color: var(--primary);">
                    <?php echo $order['order_reference']; ?>
                </p>
            </div>
            <div>
                <strong style="color: var(--gray); font-size: 12px; text-transform: uppercase;">Order Date</strong>
                <p style="margin: 5px 0 0 0; font-weight: 700; font-size: 16px;">
                    <?php echo date('d M Y, h:i A', strtotime($order['order_date'])); ?>
                </p>
            </div>
            <div>
                <strong style="color: var(--gray); font-size: 12px; text-transform: uppercase;">Order Status</strong>
                <p style="margin: 5px 0 0 0;">
                    <span class="status-badge <?php echo strtolower(str_replace(' ', '-', $order['order_status'])); ?>">
                        <?php echo $order['order_status']; ?>
                    </span>
                </p>
            </div>
            <div>
                <strong style="color: var(--gray); font-size: 12px; text-transform: uppercase;">Total Amount</strong>
                <p style="margin: 5px 0 0 0; font-weight: 900; font-size: 18px; color: #22c55e;">
                    ₹<?php echo number_format($order['total_amount'], 2); ?>
                </p>
            </div>
        </div>
    </div>

    <!-- Order Items -->
    <div style="margin-bottom: 30px;">
        <h4 style="font-size: 16px; font-weight: 800; margin-bottom: 20px; color: var(--navy); border-bottom: 2px solid #e2e8f0; padding-bottom: 10px;">
            <i class="fas fa-shopping-bag"></i> Order Items
        </h4>
        
        
        <?php 
        // Loop through order items
        while($item = mysqli_fetch_assoc($items_result)): 
        ?>
        <div style="display: flex; gap: 20px; padding: 20px; background: #f8fafc; border-radius: 15px; margin-bottom: 15px; align-items: center;">
            
            <img src="uploads/products/<?php echo $item['product_image']; ?>" 
                 style="width: 80px; height: 80px; object-fit: cover; border-radius: 12px; border: 2px solid #e2e8f0;">
            <div style="flex: 1;">
                <div style="font-weight: 800; font-size: 16px; margin-bottom: 8px; color: var(--navy);">
                    <?php echo $item['product_name']; ?>
                </div>
                <div style="display: flex; gap: 20px; font-size: 14px; color: var(--gray); margin-bottom: 10px;">
                    <span><i class="fas fa-tag"></i> ₹<?php echo number_format($item['price'], 2); ?></span>
                    <span><i class="fas fa-times"></i> <?php echo $item['quantity']; ?></span>
                    <?php if($item['weight_size']): ?>
                    <span><i class="fas fa-weight"></i> <?php echo $item['weight_size']; ?></span>
                    <?php endif; ?>
                </div>
            </div>
            <div style="text-align: right;">
                <strong style="font-size: 18px; font-weight: 900; color: var(--navy);">
                    ₹<?php echo number_format($item['subtotal'], 2); ?>
                </strong>
            </div>
        </div>
        <?php endwhile; ?>
    </div>

    <?php
    // Calculate subtotal from order items
    mysqli_data_seek($items_result, 0); // Reset pointer to beginning
    $subtotal = 0;
    while($item = mysqli_fetch_assoc($items_result)) {
        $subtotal += $item['subtotal'];
    }
    mysqli_data_seek($items_result, 0); // Reset again for any future use
    
    // Calculate shipping (same logic as checkout)
    $free_shipping_threshold = 499;
    $shipping_charges = 0;
    if ($subtotal > 0 && $subtotal < $free_shipping_threshold) {
        $shipping_charges = 40;
    }
    
    // Calculate GST (18%)
    $gst_rate = 0.18;
    $gst_amount = ($subtotal + $shipping_charges) * $gst_rate;
    
    // Grand total should match what's in database
    $calculated_total = $subtotal + $shipping_charges + $gst_amount;
    ?>
    
    <!-- Price Summary -->
    <div style="background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); padding: 25px; border-radius: 20px; margin-bottom: 30px; border: 2px solid #fbbf24;">
        <div style="display: flex; justify-content: space-between; margin-bottom: 15px; font-size: 16px; align-items: center;">
            <span style="color: #78350f; font-weight: 700; display: flex; align-items: center; gap: 8px;">
                <i class="fas fa-shopping-bag" style="color: #f59e0b;"></i> Subtotal:
            </span>
            <span style="color: #78350f; font-weight: 800;">₹<?php echo number_format($subtotal, 2); ?></span>
        </div>
        <div style="display: flex; justify-content: space-between; margin-bottom: 15px; font-size: 16px; align-items: center;">
            <span style="color: #78350f; font-weight: 700; display: flex; align-items: center; gap: 8px;">
                <i class="fas fa-shipping-fast" style="color: #f59e0b;"></i> Shipping:
            </span>
            <?php if ($shipping_charges == 0): ?>
                <span style="color: #059669; font-weight: 900;">
                    FREE <span style="background: rgba(5, 150, 105, 0.15); padding: 3px 8px; border-radius: 6px; font-size: 10px; margin-left: 5px;">SAVED ₹40</span>
                </span>
            <?php else: ?>
                <span style="color: #dc2626; font-weight: 800;">₹<?php echo number_format($shipping_charges, 2); ?></span>
            <?php endif; ?>
        </div>
        <div style="display: flex; justify-content: space-between; margin-bottom: 15px; font-size: 16px; align-items: center;">
            <span style="color: #78350f; font-weight: 700; display: flex; align-items: center; gap: 8px;">
                <i class="fas fa-receipt" style="color: #f59e0b;"></i> GST (18%):
            </span>
            <span style="color: #78350f; font-weight: 800;">₹<?php echo number_format($gst_amount, 2); ?></span>
        </div>
        <div style="border-top: 3px solid #fbbf24; padding-top: 15px; display: flex; justify-content: space-between; font-size: 20px; align-items: center;">
            <span style="color: #78350f; font-weight: 900; display: flex; align-items: center; gap: 8px;">
                <i class="fas fa-money-check-alt" style="color: #f59e0b;"></i> Grand Total:
            </span>
            <span style="color: #78350f; font-weight: 900;">₹<?php echo number_format($order['total_amount'], 2); ?></span>
        </div>
    </div>

    <!-- Customer & Shipping Info -->
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
        <div style="background: #f8fafc; padding: 20px; border-radius: 15px;">
            <h4 style="font-size: 14px; font-weight: 800; margin-bottom: 15px; color: var(--navy);">
                <i class="fas fa-user"></i> Customer Information
            </h4>
            <p style="margin: 0; color: var(--gray); line-height: 1.8; font-size: 14px;">
                <strong style="color: var(--navy);">Name:</strong> <?php echo $order['customer_name']; ?><br>
                <strong style="color: var(--navy);">Email:</strong> <?php echo $order['user_email']; ?><br>
                <strong style="color: var(--navy);">Phone:</strong> <?php echo $order['customer_phone']; ?>
            </p>
        </div>
        
        <div style="background: #f8fafc; padding: 20px; border-radius: 15px;">
            <h4 style="font-size: 14px; font-weight: 800; margin-bottom: 15px; color: var(--navy);">
                <i class="fas fa-map-marker-alt"></i> Shipping Address
            </h4>
            <p style="margin: 0; color: var(--gray); line-height: 1.6; font-size: 14px;">
                <?php echo $order['shipping_address']; ?><br>
                <?php echo $order['city']; ?>, <?php echo $order['state']; ?><br>
                <?php echo $order['zip_code']; ?>, <?php echo $order['country']; ?>
            </p>
        </div>
    </div>

    <?php if($order['order_status'] == 'Delivered'): ?>
    <div style="background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%); padding: 25px; border-radius: 20px; text-align: center; margin-top: 30px; border: 2px solid #065f46;">
        <i class="fas fa-check-circle" style="font-size: 48px; color: #065f46; margin-bottom: 15px;"></i>
        <h3 style="font-size: 22px; font-weight: 900; color: #065f46; margin-bottom: 10px;">
            Order Delivered Successfully!
        </h3>
        <p style="color: #047857; font-weight: 600; margin: 0;">
            Delivered on <?php echo date('d M Y, h:i A', strtotime($order['delivery_date'])); ?>
        </p>
    </div>
    <?php endif; ?>
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


