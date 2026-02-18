<?php
session_start();
if (!isset($_SESSION['admin_id'])) { header("Location: login.php"); exit(); }
include '../db.php';

// Delete Order
if(isset($_POST['delete_order'])) {
    $order_id = (int)$_POST['order_id'];
    
    // Delete order (cascade will handle order_items and order_status_history)
    $delete_sql = "DELETE FROM orders WHERE id=$order_id";
    if(mysqli_query($conn, $delete_sql)) {
        $_SESSION['success_msg'] = "Order deleted successfully!";
    } else {
        $_SESSION['error_msg'] = "Failed to delete order!";
    }
    header("Location: admin_orders.php");
    exit();
}

// Update Order Status
if(isset($_POST['update_status'])) {
    $order_id = (int)$_POST['order_id'];
    $new_status = mysqli_real_escape_string($conn, $_POST['status']);
    $comment = mysqli_real_escape_string($conn, $_POST['comment']);
    
    // Fetch current order details to check payment method
    $order_check = mysqli_query($conn, "SELECT payment_method, payment_status FROM orders WHERE id=$order_id");
    $order_data = mysqli_fetch_assoc($order_check);
    
    $update_sql = "UPDATE orders SET order_status='$new_status' WHERE id=$order_id";
    if(mysqli_query($conn, $update_sql)) {
        // Add to history
        mysqli_query($conn, "INSERT INTO order_status_history (order_id, status, comment, changed_by) 
                            VALUES ($order_id, '$new_status', '$comment', '{$_SESSION['admin_name']}')");
        
        // If delivered, update delivery date
        if($new_status == 'Delivered') {
            mysqli_query($conn, "UPDATE orders SET delivery_date=NOW() WHERE id=$order_id");
            
            // Auto-update payment status to Paid for COD orders
            if($order_data['payment_method'] == 'COD' && $order_data['payment_status'] != 'Paid') {
                mysqli_query($conn, "UPDATE orders SET payment_status='Paid' WHERE id=$order_id");
                $_SESSION['success_msg'] = "Order status updated to Delivered and payment marked as Paid (COD)!";
            } else {
                $_SESSION['success_msg'] = "Order status updated successfully!";
            }
        } else {
            $_SESSION['success_msg'] = "Order status updated successfully!";
        }
    }
    header("Location: admin_orders.php" . ($filter ? "?status=$filter" : ""));
    exit();
}

// Fetch all orders with payment_status
$filter = isset($_GET['status']) ? mysqli_real_escape_string($conn, $_GET['status']) : '';
$sql = "SELECT o.*, COUNT(oi.id) as item_count, 
        COALESCE(o.payment_status, 'Pending') as payment_status
        FROM orders o 
        LEFT JOIN order_items oi ON o.id = oi.order_id";
if($filter) {
    $sql .= " WHERE o.order_status = '$filter'";
}
$sql .= " GROUP BY o.id ORDER BY o.order_date DESC";
$orders = mysqli_query($conn, $sql);

include('includes/header.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Management - FurryMart Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <style>
        :root {
            --primary: #518992;
            --navy: #0f1c3f;
            --success: #22c55e;
            --warning: #f59e0b;
            --danger: #ef4444;
            --gray: #94a3b8;
            --bg: #f8fafc;
        }
        
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: var(--bg);
            color: var(--navy);
        }
        
        .admin-wrapper {
            padding: 40px 5%;
            max-width: 1600px;
            margin: auto;
        }
        
        /* Header */
        .dash-header {
            background: linear-gradient(135deg, var(--navy) 0%, var(--primary) 100%);
            color: white;
            padding: 40px 50px;
            border-radius: 35px;
            margin-bottom: 40px;
            box-shadow: 0 20px 50px rgba(15,28,63,0.1);
        }
        .dash-header h2 {
            margin: 0;
            font-size: 32px;
            font-weight: 900;
        }
        .dash-header p {
            opacity: 0.85;
            font-size: 14px;
            margin: 5px 0 0;
        }
        
        /* Status Filter Tabs */
        .status-tabs {
            display: flex;
            gap: 15px;
            margin-bottom: 30px;
            flex-wrap: wrap;
        }
        .status-tab {
            padding: 12px 35px;
            border-radius: 15px;
            text-decoration: none;
            font-weight: 800;
            font-size: 13px;
            text-transform: uppercase;
            transition: 0.3s;
            border: 2px solid #e2e8f0;
            background: white;
            color: var(--navy);
        }
        .status-tab:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0,0,0,0.1);
        }
        .status-tab.active {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
        }
        
        /* Orders Table */
        .orders-table {
            background: white;
            border-radius: 30px;
            overflow-x: auto;
            width: 100%;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
        }
        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            min-width: 1200px;
        }
        thead {
            background: linear-gradient(135deg, var(--navy) 0%, var(--primary) 100%);
            color: white;
        }
        thead th {
            padding: 22px 20px;
            text-align: left;
            font-size: 12px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            white-space: nowrap;
            border-bottom: 3px solid rgba(255,255,255,0.1);
        }
        thead th:first-child {
            border-top-left-radius: 30px;
            padding-left: 30px;
        }
        thead th:last-child {
            border-top-right-radius: 30px;
            padding-right: 30px;
        }
        tbody tr {
            border-bottom: 1px solid #e2e8f0;
            transition: all 0.3s ease;
        }
        tbody tr:hover {
            background: #f8fafc;
            box-shadow: inset 3px 0 0 var(--primary);
        }
        tbody tr:last-child {
            border-bottom: none;
        }
        tbody td {
            padding: 22px 20px;
            font-size: 14px;
            vertical-align: middle;
        }
        tbody td:first-child {
            padding-left: 30px;
            font-weight: 700;
            color: var(--navy);
        }
        tbody td:last-child {
            padding-right: 30px;
        }
        tbody tr:last-child td:first-child {
            border-bottom-left-radius: 30px;
        }
        tbody tr:last-child td:last-child {
            border-bottom-right-radius: 30px;
        }
        
        /* Status Badges */
        .status-badge {
            display: inline-block;
            padding: 6px 14px;
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
        .status-badge.cancelled { background: #fee2e2; color: #991b1b; }
        
        /* Payment Status Badges */
        .payment-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 15px;
            font-size: 10px;
            font-weight: 800;
            text-transform: uppercase;
        }
        .payment-badge.paid { background: #d1fae5; color: #065f46; }
        .payment-badge.payment-pending { background: #fef3c7; color: #92400e; }
        .payment-badge.failed { background: #fee2e2; color: #991b1b; }
        
        /* Action Buttons */
        .btn-view, .btn-edit, .btn-delete {
            padding: 10px 12px;
            color: white;
            text-decoration: none;
            border-radius: 10px;
            font-weight: 700;
            font-size: 14px;
            transition: 0.3s;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            border: none;
        }
        .btn-view {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .btn-view:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
        }
        .btn-edit {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }
        .btn-edit:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(240, 147, 251, 0.4);
        }
        .btn-delete {
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5a6f 100%);
        }
        .btn-delete:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(255, 107, 107, 0.4);
        }
        .btn-disabled {
            background: linear-gradient(135deg, #d1d5db 0%, #9ca3af 100%) !important;
            cursor: not-allowed !important;
            opacity: 0.5 !important;
            pointer-events: none !important;
        }
        .btn-disabled:hover {
            transform: none !important;
            box-shadow: none !important;
        }
        .btn-disabled {
            background: linear-gradient(135deg, #d1d5db 0%, #9ca3af 100%);
            cursor: not-allowed;
            opacity: 0.5;
        }
        .btn-disabled:hover {
            transform: none;
            box-shadow: none;
        }
        
        /* Modal */
        .modal {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(15, 28, 63, 0.9);
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
            max-width: 800px;
            width: 100%;
            border-radius: 30px;
            padding: 40px;
            max-height: 90vh;
            overflow-y: auto;
            animation: zoomIn 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }
        @keyframes zoomIn {
            from { opacity: 0; transform: scale(0.5); }
            to { opacity: 1; transform: scale(1); }
        }
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #f1f5f9;
        }
        .modal-header h3 {
            font-size: 24px;
            font-weight: 900;
            color: var(--navy);
        }
        .modal-close {
            font-size: 28px;
            cursor: pointer;
            color: var(--gray);
            transition: 0.3s;
        }
        .modal-close:hover {
            color: var(--danger);
            transform: rotate(90deg);
        }
        
        .order-detail-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 30px;
        }
        .detail-item {
            padding: 15px;
            background: #f8fafc;
            border-radius: 12px;
        }
        .detail-label {
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            color: var(--gray);
            margin-bottom: 5px;
        }
        .detail-value {
            font-size: 14px;
            font-weight: 700;
            color: var(--navy);
        }
        
        .status-update-form {
            background: #f8fafc;
            padding: 25px;
            border-radius: 20px;
            margin-top: 30px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            font-size: 12px;
            font-weight: 800;
            text-transform: uppercase;
            color: var(--gray);
            margin-bottom: 8px;
        }
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            font-family: inherit;
            font-size: 14px;
            font-weight: 600;
        }
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: var(--primary);
        }
        .btn-update-status {
            width: 100%;
            padding: 14px;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 12px;
            font-weight: 800;
            font-size: 14px;
            cursor: pointer;
            transition: 0.3s;
        }
        .btn-update-status:hover {
            background: var(--navy);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0,0,0,0.2);
        }
        
        /* SweetAlert2 Custom Styles */
        .swal-wide {
            font-family: 'Plus Jakarta Sans', sans-serif !important;
        }
        .swal-btn-confirm, .swal-btn-cancel {
            font-weight: 800 !important;
            border-radius: 12px !important;
            padding: 12px 24px !important;
        }
        
        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 80px 20px;
            color: var(--gray);
        }
        .empty-state i {
            font-size: 64px;
            margin-bottom: 20px;
            opacity: 0.3;
        }
    </style>
</head>
<body>

<main class="admin-wrapper">
    <div class="dash-header">
        <h2><i class="fas fa-shopping-cart"></i> Order Management</h2>
        <p>Manage and track all customer orders</p>
    </div>
    
    <?php if(isset($success_msg)): ?>
    <script>
        Swal.fire({
            title: 'Success!',
            text: '<?php echo $success_msg; ?>',
            icon: 'success',
            confirmButtonColor: '#518992'
        });
    </script>
    <?php endif; ?>
    
    <!-- Status Filter Tabs -->
    <div class="status-tabs">
        <a href="admin_orders.php" class="status-tab <?php echo !$filter ? 'active' : ''; ?>">
            <i class="fas fa-list"></i> All Orders
        </a>
        <a href="?status=Pending" class="status-tab <?php echo $filter=='Pending' ? 'active' : ''; ?>">
            <i class="fas fa-clock"></i> Pending
        </a>
        <a href="?status=Confirmed" class="status-tab <?php echo $filter=='Confirmed' ? 'active' : ''; ?>">
            <i class="fas fa-check-circle"></i> Confirmed
        </a>
        <a href="?status=Processing" class="status-tab <?php echo $filter=='Processing' ? 'active' : ''; ?>">
            <i class="fas fa-cog"></i> Processing
        </a>
        <a href="?status=Shipped" class="status-tab <?php echo $filter=='Shipped' ? 'active' : ''; ?>">
            <i class="fas fa-shipping-fast"></i> Shipped
        </a>
        <a href="?status=Out for Delivery" class="status-tab <?php echo $filter=='Out for Delivery' ? 'active' : ''; ?>">
            <i class="fas fa-truck"></i> Out for Delivery
        </a>
        <a href="?status=Delivered" class="status-tab <?php echo $filter=='Delivered' ? 'active' : ''; ?>">
            <i class="fas fa-check-double"></i> Delivered
        </a>
    </div>
    
    <!-- Orders Table -->
    <div class="orders-table">
        <?php if(mysqli_num_rows($orders) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Order #</th>
                    <th>Customer</th>
                    <th>Date</th>
                    <th>Items</th>
                    <th>Total</th>
                    <th>Payment Method</th>
                    <th>Payment Status</th>
                    <th>Order Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while($order = mysqli_fetch_assoc($orders)): 
                    $status_class = strtolower(str_replace(' ', '-', $order['order_status']));
                ?>
                <tr>
                    <td><strong><?php echo $order['order_reference']; ?></strong></td>
                    <td>
                        <div style="font-weight: 700;"><?php echo $order['customer_name']; ?></div>
                        <div style="font-size: 12px; color: var(--gray);"><?php echo $order['user_email']; ?></div>
                    </td>
                    <td><?php echo date('d M Y, h:i A', strtotime($order['order_date'])); ?></td>
                    <td><?php echo $order['item_count']; ?> item(s)</td>
                    <td><strong>₹<?php echo number_format($order['total_amount'], 2); ?></strong></td>
                    <td>
                        <span class="status-badge" style="background: #e0e7ff; color: #4338ca;">
                            <?php echo $order['payment_method']; ?>
                        </span>
                    </td>
                    <td>
                        <?php 
                        $payment_status = $order['payment_status'];
                        $payment_class = strtolower($payment_status == 'Pending' ? 'payment-pending' : $payment_status);
                        $payment_icon = $payment_status == 'Paid' ? '✓' : ($payment_status == 'Failed' ? '✗' : '⏱');
                        ?>
                        <span class="payment-badge <?php echo $payment_class; ?>">
                            <?php echo $payment_icon; ?> <?php echo $payment_status; ?>
                        </span>
                    </td>
                    <td>
                        <span class="status-badge <?php echo $status_class; ?>">
                            <?php echo $order['order_status']; ?>
                        </span>
                    </td>
                    <td>
                        <div style="display: flex; gap: 8px; align-items: center; justify-content: flex-start;">
                            <!-- Update Button with View Details -->
                            <button class="btn-edit" onclick="viewAndUpdateOrder(<?php echo $order['id']; ?>, '<?php echo $order['order_status']; ?>', '<?php echo addslashes($order['order_reference']); ?>', '<?php echo $order['payment_method']; ?>', '<?php echo $payment_status; ?>')" title="View Details & Update">
                                <i class="fas fa-edit"></i>
                            </button>
                            
                            <!-- Delete Button -->
                            <button class="btn-delete" onclick="deleteOrder(<?php echo $order['id']; ?>, '<?php echo $order['order_reference']; ?>')" title="Delete Order">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <?php else: ?>
        <div class="empty-state">
            <i class="fas fa-box-open"></i>
            <h3>No Orders Found</h3>
            <p>No orders match the selected filter.</p>
        </div>
        <?php endif; ?>
    </div>
</main>

<!-- Combined Order Details & Update Modal -->
<div id="orderModal" class="modal">
    <div class="modal-content" style="max-width: 900px;">
        <div class="modal-header">
            <h3><i class="fas fa-file-invoice"></i> <span id="modalTitle">Order Details</span></h3>
            <span class="modal-close" onclick="closeModal()">&times;</span>
        </div>
        <div id="modalBody" style="padding: 30px;"></div>
    </div>
</div>

<script>
// Combined View and Update Order
function viewAndUpdateOrder(orderId, currentStatus, orderRef, paymentMethod, paymentStatus) {
    // Determine if order is locked (Delivered or Cancelled)
    const isLocked = (currentStatus === 'Delivered' || currentStatus === 'Cancelled');
    const disabledAttr = isLocked ? 'disabled' : '';
    const lockedNote = isLocked ? `<div style="background: #fee2e2; color: #991b1b; padding: 15px; border-radius: 12px; margin-bottom: 20px; font-weight: 700; text-align: center;"><i class="fas fa-lock"></i> Order status is locked and cannot be changed (${currentStatus})</div>` : '';
    
    // Fetch order details
    fetch(`get_order_details.php?id=${orderId}`)
        .then(res => res.json())
        .then(data => {
            let itemsHTML = '';
            data.items.forEach(item => {
                itemsHTML += `
                    <tr>
                        <td style="padding: 12px;">
                            <img src="../uploads/products/${item.product_image}" style="width: 50px; height: 50px; object-fit: cover; border-radius: 8px; margin-right: 10px;">
                            <strong>${item.product_name}</strong><br>
                            <small style="color: var(--gray);">${item.weight_size || 'N/A'}</small>
                        </td>
                        <td style="text-align: center; padding: 12px;">${item.quantity}</td>
                        <td style="text-align: right; padding: 12px;">₹${parseFloat(item.price).toFixed(2)}</td>
                        <td style="text-align: right; padding: 12px; font-weight: 800;">₹${parseFloat(item.subtotal).toFixed(2)}</td>
                    </tr>
                `;
            });
            
            const paymentIcon = paymentStatus === 'Paid' ? '✓' : (paymentStatus === 'Failed' ? '✗' : '⏱');
            const paymentColor = paymentStatus === 'Paid' ? '#22c55e' : (paymentStatus === 'Failed' ? '#ef4444' : '#f59e0b');
            
            const html = `
                ${lockedNote}
                
                <!-- Order Information -->
                <div style="background: #f8fafc; padding: 20px; border-radius: 15px; margin-bottom: 25px;">
                    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 15px; margin-bottom: 15px;">
                        <div>
                            <strong style="color: var(--gray); font-size: 12px;">ORDER #</strong>
                            <div style="font-size: 18px; font-weight: 900; color: var(--primary);">${orderRef}</div>
                        </div>
                        <div>
                            <strong style="color: var(--gray); font-size: 12px;">DATE</strong>
                            <div style="font-weight: 700;">${data.order.order_date}</div>
                        </div>
                    </div>
                    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 15px;">
                        <div>
                            <strong style="color: var(--gray); font-size: 12px;">CUSTOMER</strong>
                            <div style="font-weight: 700;">${data.order.customer_name}</div>
                            <div style="font-size: 12px; color: var(--gray);">${data.order.user_email}</div>
                            <div style="font-size: 12px; color: var(--gray);">${data.order.customer_phone}</div>
                        </div>
                        <div>
                            <strong style="color: var(--gray); font-size: 12px;">SHIPPING ADDRESS</strong>
                            <div style="font-weight: 600; font-size: 13px;">${data.order.shipping_address}<br>${data.order.city}, ${data.order.state} - ${data.order.zip_code}</div>
                        </div>
                    </div>
                </div>
                
                <!-- Payment Info -->
                <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 15px; margin-bottom: 25px;">
                    <div style="background: #f8fafc; padding: 15px; border-radius: 12px;">
                        <strong style="color: var(--gray); font-size: 12px;">PAYMENT METHOD</strong>
                        <div style="font-weight: 800; font-size: 16px; color: var(--navy);">${paymentMethod}</div>
                    </div>
                    <div style="background: #f8fafc; padding: 15px; border-radius: 12px;">
                        <strong style="color: var(--gray); font-size: 12px;">PAYMENT STATUS</strong>
                        <div style="font-weight: 800; font-size: 16px; color: ${paymentColor};">${paymentIcon} ${paymentStatus}</div>
                    </div>
                </div>
                
                <!-- Order Items -->
                <div style="margin-bottom: 25px;">
                    <h4 style="font-size: 16px; font-weight: 900; margin-bottom: 15px; color: var(--navy);"><i class="fas fa-shopping-bag"></i> Order Items</h4>
                    <table style="width: 100%; border-collapse: collapse; border: 2px solid #e2e8f0; border-radius: 12px; overflow: hidden;">
                        <thead style="background: var(--navy); color: white;">
                            <tr>
                                <th style="text-align: left; padding: 12px; font-size: 11px;">PRODUCT</th>
                                <th style="text-align: center; padding: 12px; font-size: 11px;">QTY</th>
                                <th style="text-align: right; padding: 12px; font-size: 11px;">PRICE</th>
                                <th style="text-align: right; padding: 12px; font-size: 11px;">SUBTOTAL</th>
                            </tr>
                        </thead>
                        <tbody style="background: white;">
                            ${itemsHTML}
                            <tr style="background: #f8fafc; font-weight: 900;">
                                <td colspan="3" style="text-align: right; padding: 15px; font-size: 16px;">TOTAL:</td>
                                <td style="text-align: right; padding: 15px; color: var(--primary); font-size: 18px;">₹${parseFloat(data.order.total_amount).toFixed(2)}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <!-- Update Status Form -->
                <form method="POST" style="background: #f0f9ff; padding: 20px; border-radius: 15px; border: 2px solid #bfdbfe;">
                    <h4 style="font-size: 16px; font-weight: 900; margin-bottom: 15px; color: var(--navy);"><i class="fas fa-edit"></i> Update Order Status</h4>
                    <input type="hidden" name="order_id" value="${orderId}">
                    
                    <div style="margin-bottom: 15px;">
                        <label style="display: block; font-weight: 800; margin-bottom: 8px; color: var(--navy); font-size: 13px;">New Status</label>
                        <select name="status" required ${disabledAttr} style="width: 100%; padding: 12px; border: 2px solid #e2e8f0; border-radius: 12px; font-family: 'Plus Jakarta Sans'; font-weight: 700; font-size: 14px; background: white;">
                            <option value="">-- Select Status --</option>
                            <option value="Pending" ${currentStatus === 'Pending' ? 'selected' : ''}>Pending</option>
                            <option value="Confirmed" ${currentStatus === 'Confirmed' ? 'selected' : ''}>Confirmed</option>
                            <option value="Processing" ${currentStatus === 'Processing' ? 'selected' : ''}>Processing</option>
                            <option value="Shipped" ${currentStatus === 'Shipped' ? 'selected' : ''}>Shipped</option>
                            <option value="Out for Delivery" ${currentStatus === 'Out for Delivery' ? 'selected' : ''}>Out for Delivery</option>
                            <option value="Delivered" ${currentStatus === 'Delivered' ? 'selected' : ''}>Delivered</option>
                            <option value="Cancelled" ${currentStatus === 'Cancelled' ? 'selected' : ''}>Cancelled</option>
                        </select>
                    </div>
                    
                    <div style="margin-bottom: 15px;">
                        <label style="display: block; font-weight: 800; margin-bottom: 8px; color: var(--navy); font-size: 13px;">Comment (Optional)</label>
                        <textarea name="comment" rows="3" ${disabledAttr} placeholder="Add a note about this status change..." style="width: 100%; padding: 12px; border: 2px solid #e2e8f0; border-radius: 12px; font-family: 'Plus Jakarta Sans'; font-size: 14px; resize: vertical;"></textarea>
                    </div>
                    
                    ${!isLocked ? `<button type="submit" name="update_status" class="btn-update-status" style="width: 100%; background: var(--primary); color: white; padding: 15px; border: none; border-radius: 12px; font-weight: 800; font-size: 15px; cursor: pointer; transition: 0.3s;"><i class="fas fa-save"></i> Update Order Status</button>` : ''}
                    
                    ${paymentMethod === 'COD' && currentStatus !== 'Delivered' ? `<div style="background: #fef3c7; color: #92400e; padding: 12px; border-radius: 10px; margin-top: 15px; font-size: 12px; font-weight: 700; text-align: center;"><i class="fas fa-info-circle"></i> COD payment will automatically mark as "Paid" when order is delivered</div>` : ''}
                </form>
            `;
            
            document.getElementById('modalTitle').textContent = `Order #${orderRef}`;
            document.getElementById('modalBody').innerHTML = html;
            document.getElementById('orderModal').classList.add('active');
        })
        .catch(err => {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'Failed to load order details'
            });
        });
}

function closeModal() {
    document.getElementById('orderModal').classList.remove('active');
}

// Delete Order
function deleteOrder(orderId, orderRef) {
    Swal.fire({
        title: 'Delete Order?',
        html: `Are you sure you want to delete order <strong>${orderRef}</strong>?<br><span style="color: #ef4444;">This action cannot be undone!</span>`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: '<i class="fas fa-trash"></i> Yes, Delete It',
        cancelButtonText: 'Cancel',
        customClass: {
            popup: 'swal-wide',
            confirmButton: 'swal-btn-confirm',
            cancelButton: 'swal-btn-cancel'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.innerHTML = `<input type="hidden" name="delete_order" value="1"><input type="hidden" name="order_id" value="${orderId}">`;
            document.body.appendChild(form);
            form.submit();
        }
    });
}

// Close modals on outside click
window.onclick = function(event) {
    const orderModal = document.getElementById('orderModal');
    const statusModal = document.getElementById('statusModal');
    if (event.target == orderModal) {
        closeModal();
    }
    if (event.target == statusModal) {
        closeStatusModal();
    }
}

// Show success/error messages
<?php if(isset($_SESSION['success_msg'])): ?>
Swal.fire({
    icon: 'success',
    title: 'Success!',
    text: '<?php echo $_SESSION['success_msg']; ?>',
    timer: 2000,
    showConfirmButton: false
});
<?php unset($_SESSION['success_msg']); endif; ?>

<?php if(isset($_SESSION['error_msg'])): ?>
Swal.fire({
    icon: 'error',
    title: 'Error!',
    text: '<?php echo $_SESSION['error_msg']; ?>',
    timer: 2000,
    showConfirmButton: false
});
<?php unset($_SESSION['error_msg']); endif; ?>
</script>

<?php include('includes/footer.php'); ?>
</body>
</html>

