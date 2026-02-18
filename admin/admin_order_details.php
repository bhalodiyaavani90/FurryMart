<?php
session_start();
if (!isset($_SESSION['admin_id'])) { exit('Unauthorized'); }
include '../db.php';

$order_id = (int)$_GET['id'];

// Fetch order details
$order_sql = "SELECT * FROM orders WHERE id = $order_id";
$order = mysqli_fetch_assoc(mysqli_query($conn, $order_sql));

// Fetch order items
$items_sql = "SELECT * FROM order_items WHERE order_id = $order_id";
$items = mysqli_query($conn, $items_sql);

// Fetch status history
$history_sql = "SELECT * FROM order_status_history WHERE order_id = $order_id ORDER BY changed_at DESC";
$history = mysqli_query($conn, $history_sql);
?>

<div class="order-detail-grid">
    <div class="detail-item">
        <div class="detail-label">Order Number</div>
        <div class="detail-value"><?php echo $order['order_reference']; ?></div>
    </div>
    <div class="detail-item">
        <div class="detail-label">Order Date</div>
        <div class="detail-value"><?php echo date('d M Y, h:i A', strtotime($order['order_date'])); ?></div>
    </div>
    <div class="detail-item">
        <div class="detail-label">Customer Name</div>
        <div class="detail-value"><?php echo $order['customer_name']; ?></div>
    </div>
    <div class="detail-item">
        <div class="detail-label">Email</div>
        <div class="detail-value"><?php echo $order['user_email']; ?></div>
    </div>
    <div class="detail-item">
        <div class="detail-label">Phone</div>
        <div class="detail-value"><?php echo $order['customer_phone']; ?></div>
    </div>
    <div class="detail-item">
        <div class="detail-label">Payment Method</div>
        <div class="detail-value"><?php echo $order['payment_method']; ?></div>
    </div>
</div>

<div style="background: #f8fafc; padding: 20px; border-radius: 15px; margin-bottom: 20px;">
    <h4 style="font-size: 14px; font-weight: 800; margin-bottom: 15px; color: var(--navy);">
        <i class="fas fa-map-marker-alt"></i> Shipping Address
    </h4>
    <p style="margin: 0; color: var(--gray); line-height: 1.6;">
        <?php echo $order['shipping_address']; ?><br>
        <?php echo $order['city']; ?>, <?php echo $order['state']; ?> - <?php echo $order['zip_code']; ?><br>
        <?php echo $order['country']; ?>
    </p>
</div>

<div style="margin-bottom: 20px;">
    <h4 style="font-size: 14px; font-weight: 800; margin-bottom: 15px; color: var(--navy);">
        <i class="fas fa-box"></i> Order Items
    </h4>
    <?php while($item = mysqli_fetch_assoc($items)): ?>
    <div style="display: flex; gap: 15px; padding: 12px; background: #f8fafc; border-radius: 12px; margin-bottom: 10px;">
        <img src="../uploads/products/<?php echo $item['product_image']; ?>" 
             style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px;">
        <div style="flex: 1;">
            <div style="font-weight: 700; margin-bottom: 4px;"><?php echo $item['product_name']; ?></div>
            <div style="font-size: 12px; color: var(--gray);">
                ₹<?php echo number_format($item['price'], 2); ?> × <?php echo $item['quantity']; ?> = 
                <strong style="color: var(--navy);">₹<?php echo number_format($item['subtotal'], 2); ?></strong>
            </div>
        </div>
    </div>
    <?php endwhile; ?>
    
    <div style="text-align: right; margin-top: 15px; padding-top: 15px; border-top: 2px solid #e2e8f0;">
        <span style="font-size: 18px; font-weight: 900; color: var(--navy);">
            Total: ₹<?php echo number_format($order['total_amount'], 2); ?>
        </span>
    </div>
</div>

<?php if(mysqli_num_rows($history) > 0): ?>
<div style="margin-bottom: 20px;">
    <h4 style="font-size: 14px; font-weight: 800; margin-bottom: 15px; color: var(--navy);">
        <i class="fas fa-history"></i> Status History
    </h4>
    <?php while($h = mysqli_fetch_assoc($history)): ?>
    <div style="padding: 12px; background: #f8fafc; border-left: 4px solid var(--primary); border-radius: 8px; margin-bottom: 8px;">
        <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
            <strong style="color: var(--primary);"><?php echo $h['status']; ?></strong>
            <span style="font-size: 11px; color: var(--gray);">
                <?php echo date('d M Y, h:i A', strtotime($h['changed_at'])); ?>
            </span>
        </div>
        <?php if($h['comment']): ?>
        <div style="font-size: 12px; color: var(--gray);"><?php echo $h['comment']; ?></div>
        <?php endif; ?>
        <div style="font-size: 11px; color: var(--gray); margin-top: 4px;">
            <i class="fas fa-user"></i> <?php echo $h['changed_by']; ?>
        </div>
    </div>
    <?php endwhile; ?>
</div>
<?php endif; ?>

<!-- Update Status Form -->
<div class="status-update-form">
    <h4 style="font-size: 14px; font-weight: 800; margin-bottom: 20px; color: var(--navy);">
        <i class="fas fa-edit"></i> Update Order Status
    </h4>
    <form method="POST" action="admin_orders.php">
        <input type="hidden" name="order_id" value="<?php echo $order_id; ?>">
        
        <div class="form-group">
            <label>New Status</label>
            <select name="status" required>
                <option value="">-- Select Status --</option>
                <option value="Confirmed" <?php echo $order['order_status']=='Confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                <option value="Processing" <?php echo $order['order_status']=='Processing' ? 'selected' : ''; ?>>Processing</option>
                <option value="Shipped" <?php echo $order['order_status']=='Shipped' ? 'selected' : ''; ?>>Shipped</option>
                <option value="Out for Delivery" <?php echo $order['order_status']=='Out for Delivery' ? 'selected' : ''; ?>>Out for Delivery</option>
                <option value="Delivered" <?php echo $order['order_status']=='Delivered' ? 'selected' : ''; ?>>Delivered</option>
                <option value="Cancelled" <?php echo $order['order_status']=='Cancelled' ? 'selected' : ''; ?>>Cancelled</option>
            </select>
        </div>
        
        <div class="form-group">
            <label>Comment (Optional)</label>
            <textarea name="comment" rows="3" placeholder="Add a note about this status change..."></textarea>
        </div>
        
        <button type="submit" name="update_status" class="btn-update-status">
            <i class="fas fa-save"></i> Update Status
        </button>
    </form>
</div>
