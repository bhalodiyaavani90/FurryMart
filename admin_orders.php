<?php
session_start();
include 'db.php';

// Set timezone to India Standard Time
date_default_timezone_set('Asia/Kolkata');

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit();
}

// Handle order status update
if (isset($_POST['update_status'])) {
    $order_id = mysqli_real_escape_string($conn, $_POST['order_id']);
    $new_status = mysqli_real_escape_string($conn, $_POST['order_status']);
    $comment = mysqli_real_escape_string($conn, $_POST['comment']);
    
    // Update order status
    $update_sql = "UPDATE orders SET order_status = '$new_status' WHERE id = $order_id";
    
    if (mysqli_query($conn, $update_sql)) {
        // Get order details
        $order_query = mysqli_query($conn, "SELECT payment_method, payment_status FROM orders WHERE id = $order_id");
        $order_data = mysqli_fetch_assoc($order_query);
        
        // Update payment status for COD orders when delivered
        if ($order_data['payment_method'] == 'COD' && $new_status == 'Delivered') {
            mysqli_query($conn, "UPDATE orders SET payment_status = 'Paid' WHERE id = $order_id");
        }
        
        // Add to status history
        $history_sql = "INSERT INTO order_status_history (order_id, status, comment) 
                       VALUES ($order_id, '$new_status', '$comment')";
        mysqli_query($conn, $history_sql);
        
        $success = "Order status updated successfully!";
    } else {
        $error = "Failed to update status: " . mysqli_error($conn);
    }
}

// Handle payment status update
if (isset($_POST['update_payment_status'])) {
    $order_id = mysqli_real_escape_string($conn, $_POST['order_id']);
    $payment_status = mysqli_real_escape_string($conn, $_POST['payment_status']);
    
    $update_sql = "UPDATE orders SET payment_status = '$payment_status' WHERE id = $order_id";
    
    if (mysqli_query($conn, $update_sql)) {
        $success = "Payment status updated successfully!";
    } else {
        $error = "Failed to update payment status: " . mysqli_error($conn);
    }
}

// Fetch all orders
$filter_status = isset($_GET['status']) ? mysqli_real_escape_string($conn, $_GET['status']) : '';
$filter_payment = isset($_GET['payment']) ? mysqli_real_escape_string($conn, $_GET['payment']) : '';
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';

$where_clauses = [];
if ($filter_status) $where_clauses[] = "o.order_status = '$filter_status'";
if ($filter_payment) $where_clauses[] = "o.payment_status = '$filter_payment'";
if ($search) $where_clauses[] = "(o.order_reference LIKE '%$search%' OR o.customer_name LIKE '%$search%' OR o.user_email LIKE '%$search%')";

$where_sql = !empty($where_clauses) ? 'WHERE ' . implode(' AND ', $where_clauses) : '';

$orders_sql = "SELECT o.*, COUNT(oi.id) as item_count 
               FROM orders o 
               LEFT JOIN order_items oi ON o.id = oi.order_id 
               $where_sql
               GROUP BY o.id 
               ORDER BY o.order_date DESC";
$orders_result = mysqli_query($conn, $orders_sql);

// Get statistics
$stats_sql = "SELECT 
    COUNT(*) as total_orders,
    SUM(CASE WHEN order_status = 'Pending' THEN 1 ELSE 0 END) as pending_orders,
    SUM(CASE WHEN order_status = 'Delivered' THEN 1 ELSE 0 END) as delivered_orders,
    SUM(CASE WHEN payment_status = 'Paid' THEN 1 ELSE 0 END) as paid_orders,
    SUM(CASE WHEN payment_status = 'Pending' THEN 1 ELSE 0 END) as payment_pending,
    SUM(total_amount) as total_revenue,
    SUM(CASE WHEN payment_status = 'Paid' THEN total_amount ELSE 0 END) as collected_revenue
FROM orders";
$stats_result = mysqli_query($conn, $stats_sql);
$stats = mysqli_fetch_assoc($stats_result);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Orders - FurryMart Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        :root {
            --primary: #518992;
            --navy: #0f1c3f;
            --success: #22c55e;
            --warning: #f59e0b;
            --danger: #ef4444;
            --info: #3b82f6;
        }
        
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            background: #f8fafc;
            color: var(--navy);
        }
        
        .admin-header {
            background: linear-gradient(135deg, var(--navy), var(--primary));
            color: white;
            padding: 30px 40px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }
        .admin-header h1 {
            font-size: 32px;
            font-weight: 900;
            margin-bottom: 10px;
        }
        .admin-header p {
            opacity: 0.9;
        }
        
        .container {
            max-width: 1600px;
            margin: 0 auto;
            padding: 40px;
        }
        
        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }
        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 20px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            border-left: 5px solid;
        }
        .stat-card.primary { border-color: var(--primary); }
        .stat-card.success { border-color: var(--success); }
        .stat-card.warning { border-color: var(--warning); }
        .stat-card.info { border-color: var(--info); }
        .stat-number {
            font-size: 32px;
            font-weight: 900;
            color: var(--navy);
            margin-bottom: 5px;
        }
        .stat-label {
            color: #64748b;
            font-size: 14px;
            font-weight: 600;
        }
        
        /* Filters */
        .filters {
            background: white;
            padding: 25px;
            border-radius: 20px;
            margin-bottom: 30px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            align-items: end;
        }
        .filter-group {
            flex: 1;
            min-width: 200px;
        }
        .filter-group label {
            display: block;
            font-size: 12px;
            font-weight: 700;
            color: #64748b;
            margin-bottom: 8px;
            text-transform: uppercase;
        }
        .filter-group select,
        .filter-group input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 600;
        }
        .filter-btn {
            padding: 12px 30px;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 12px;
            font-weight: 700;
            cursor: pointer;
            transition: 0.3s;
        }
        .filter-btn:hover {
            background: var(--navy);
        }
        
        /* Orders Table */
        .orders-table {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        thead {
            background: linear-gradient(135deg, var(--navy), var(--primary));
            color: white;
        }
        th {
            padding: 20px;
            text-align: left;
            font-weight: 800;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        td {
            padding: 20px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 14px;
        }
        tr:hover {
            background: #f8fafc;
        }
        
        /* Status Badges */
        .badge {
            padding: 6px 12px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            display: inline-block;
        }
        .badge-pending { background: #fef3c7; color: #92400e; }
        .badge-confirmed { background: #dbeafe; color: #1e40af; }
        .badge-processing { background: #e0e7ff; color: #4338ca; }
        .badge-shipped { background: #fce7f3; color: #9f1239; }
        .badge-delivered { background: #d1fae5; color: #065f46; }
        .badge-cancelled { background: #fee2e2; color: #991b1b; }
        .badge-paid { background: #d1fae5; color: #065f46; }
        .badge-payment-pending { background: #fef3c7; color: #92400e; }
        .badge-failed { background: #fee2e2; color: #991b1b; }
        
        /* Action Buttons */
        .btn-action {
            padding: 8px 15px;
            border: none;
            border-radius: 10px;
            font-size: 12px;
            font-weight: 700;
            cursor: pointer;
            transition: 0.3s;
            color: white;
            margin-right: 5px;
        }
        .btn-edit { background: var(--info); }
        .btn-edit:hover { background: #2563eb; }
        .btn-view { background: var(--primary); }
        .btn-view:hover { background: var(--navy); }
        
        /* Modal */
        .modal {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.8);
            z-index: 1000;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .modal.active { display: flex; }
        .modal-content {
            background: white;
            border-radius: 25px;
            padding: 40px;
            max-width: 600px;
            width: 100%;
            max-height: 90vh;
            overflow-y: auto;
        }
        .modal-title {
            font-size: 24px;
            font-weight: 900;
            color: var(--navy);
            margin-bottom: 25px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            font-weight: 700;
            margin-bottom: 8px;
            color: var(--navy);
        }
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            font-size: 14px;
            font-family: inherit;
        }
        .form-actions {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
            margin-top: 25px;
        }
        .btn-submit {
            padding: 12px 30px;
            background: var(--success);
            color: white;
            border: none;
            border-radius: 12px;
            font-weight: 700;
            cursor: pointer;
        }
        .btn-cancel {
            padding: 12px 30px;
            background: #94a3b8;
            color: white;
            border: none;
            border-radius: 12px;
            font-weight: 700;
            cursor: pointer;
        }
    </style>
</head>
<body>

<div class="admin-header">
    <h1><i class="fas fa-box"></i> Order Management</h1>
    <p>Manage all customer orders and payment status</p>
</div>

<div class="container">
    <?php if (isset($success)): ?>
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: '<?php echo $success; ?>',
            timer: 2000,
            showConfirmButton: false
        });
    </script>
    <?php endif; ?>
    
    <?php if (isset($error)): ?>
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: '<?php echo $error; ?>'
        });
    </script>
    <?php endif; ?>
    
    <!-- Statistics -->
    <div class="stats-grid">
        <div class="stat-card primary">
            <div class="stat-number"><?php echo $stats['total_orders']; ?></div>
            <div class="stat-label">Total Orders</div>
        </div>
        <div class="stat-card warning">
            <div class="stat-number"><?php echo $stats['pending_orders']; ?></div>
            <div class="stat-label">Pending Orders</div>
        </div>
        <div class="stat-card success">
            <div class="stat-number"><?php echo $stats['delivered_orders']; ?></div>
            <div class="stat-label">Delivered</div>
        </div>
        <div class="stat-card success">
            <div class="stat-number"><?php echo $stats['paid_orders']; ?></div>
            <div class="stat-label">Paid Orders</div>
        </div>
        <div class="stat-card warning">
            <div class="stat-number"><?php echo $stats['payment_pending']; ?></div>
            <div class="stat-label">Payment Pending</div>
        </div>
        <div class="stat-card info">
            <div class="stat-number">₹<?php echo number_format($stats['total_revenue'], 2); ?></div>
            <div class="stat-label">Total Revenue</div>
        </div>
        <div class="stat-card success">
            <div class="stat-number">₹<?php echo number_format($stats['collected_revenue'], 2); ?></div>
            <div class="stat-label">Collected</div>
        </div>
    </div>
    
    <!-- Filters -->
    <form class="filters" method="GET">
        <div class="filter-group">
            <label>Search</label>
            <input type="text" name="search" placeholder="Order #, Name, Email..." value="<?php echo htmlspecialchars($search); ?>">
        </div>
        <div class="filter-group">
            <label>Order Status</label>
            <select name="status">
                <option value="">All Statuses</option>
                <option value="Pending" <?php echo $filter_status == 'Pending' ? 'selected' : ''; ?>>Pending</option>
                <option value="Confirmed" <?php echo $filter_status == 'Confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                <option value="Processing" <?php echo $filter_status == 'Processing' ? 'selected' : ''; ?>>Processing</option>
                <option value="Shipped" <?php echo $filter_status == 'Shipped' ? 'selected' : ''; ?>>Shipped</option>
                <option value="Out for Delivery" <?php echo $filter_status == 'Out for Delivery' ? 'selected' : ''; ?>>Out for Delivery</option>
                <option value="Delivered" <?php echo $filter_status == 'Delivered' ? 'selected' : ''; ?>>Delivered</option>
                <option value="Cancelled" <?php echo $filter_status == 'Cancelled' ? 'selected' : ''; ?>>Cancelled</option>
            </select>
        </div>
        <div class="filter-group">
            <label>Payment Status</label>
            <select name="payment">
                <option value="">All Payments</option>
                <option value="Paid" <?php echo $filter_payment == 'Paid' ? 'selected' : ''; ?>>Paid</option>
                <option value="Pending" <?php echo $filter_payment == 'Pending' ? 'selected' : ''; ?>>Pending</option>
                <option value="Failed" <?php echo $filter_payment == 'Failed' ? 'selected' : ''; ?>>Failed</option>
            </select>
        </div>
        <button type="submit" class="filter-btn"><i class="fas fa-filter"></i> Filter</button>
    </form>
    
    <!-- Orders Table -->
    <div class="orders-table">
        <table>
            <thead>
                <tr>
                    <th>Order #</th>
                    <th>Customer</th>
                    <th>Date</th>
                    <th>Items</th>
                    <th>Amount</th>
                    <th>Payment Method</th>
                    <th>Payment Status</th>
                    <th>Order Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($orders_result && mysqli_num_rows($orders_result) > 0): ?>
                    <?php while($order = mysqli_fetch_assoc($orders_result)): 
                        $status_class = 'badge-' . strtolower(str_replace(' ', '-', $order['order_status']));
                        $payment_class = 'badge-' . ($order['payment_status'] == 'Pending' ? 'payment-pending' : strtolower($order['payment_status']));
                    ?>
                    <tr>
                        <td><strong><?php echo $order['order_reference']; ?></strong></td>
                        <td>
                            <div><?php echo $order['customer_name']; ?></div>
                            <small style="color: #64748b;"><?php echo $order['user_email']; ?></small>
                        </td>
                        <td><?php echo date('d M Y, h:i A', strtotime($order['order_date'])); ?></td>
                        <td><?php echo $order['item_count']; ?> items</td>
                        <td><strong>₹<?php echo number_format($order['total_amount'], 2); ?></strong></td>
                        <td><span class="badge badge-info"><?php echo $order['payment_method']; ?></span></td>
                        <td>
                            <span class="badge <?php echo $payment_class; ?>">
                                <?php echo $order['payment_status']; ?>
                            </span>
                        </td>
                        <td><span class="badge <?php echo $status_class; ?>"><?php echo $order['order_status']; ?></span></td>
                        <td>
                            <button class="btn-action btn-edit" onclick="updateOrderStatus(<?php echo $order['id']; ?>, '<?php echo $order['order_status']; ?>', '<?php echo $order['payment_status']; ?>', '<?php echo $order['payment_method']; ?>')">
                                <i class="fas fa-edit"></i> Update
                            </button>
                            <a href="admin_order_details.php?id=<?php echo $order['id']; ?>" class="btn-action btn-view">
                                <i class="fas fa-eye"></i> View
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="9" style="text-align: center; padding: 40px; color: #94a3b8;">
                            <i class="fas fa-inbox" style="font-size: 48px; margin-bottom: 15px; display: block;"></i>
                            No orders found
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Update Status Modal -->
<div class="modal" id="updateModal">
    <div class="modal-content">
        <h2 class="modal-title"><i class="fas fa-edit"></i> Update Order</h2>
        <form method="POST">
            <input type="hidden" name="order_id" id="modal_order_id">
            
            <div class="form-group">
                <label>Order Status</label>
                <select name="order_status" id="modal_order_status" required>
                    <option value="Pending">Pending</option>
                    <option value="Confirmed">Confirmed</option>
                    <option value="Processing">Processing</option>
                    <option value="Shipped">Shipped</option>
                    <option value="Out for Delivery">Out for Delivery</option>
                    <option value="Delivered">Delivered</option>
                    <option value="Cancelled">Cancelled</option>
                </select>
            </div>
            
            <div class="form-group">
                <label>Payment Status</label>
                <select name="payment_status" id="modal_payment_status" required>
                    <option value="Pending">Pending</option>
                    <option value="Paid">Paid</option>
                    <option value="Failed">Failed</option>
                </select>
                <small style="color: #64748b; display: block; margin-top: 5px;" id="payment_note"></small>
            </div>
            
            <div class="form-group">
                <label>Comment (Optional)</label>
                <textarea name="comment" rows="3" placeholder="Add a note..."></textarea>
            </div>
            
            <div class="form-actions">
                <button type="button" class="btn-cancel" onclick="closeModal()">Cancel</button>
                <button type="submit" name="update_status" class="btn-submit">
                    <i class="fas fa-save"></i> Update Order
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function updateOrderStatus(orderId, currentStatus, paymentStatus, paymentMethod) {
    document.getElementById('modal_order_id').value = orderId;
    document.getElementById('modal_order_status').value = currentStatus;
    document.getElementById('modal_payment_status').value = paymentStatus;
    
    // Show payment note based on payment method
    const note = document.getElementById('payment_note');
    if (paymentMethod === 'COD') {
        note.textContent = '⚠ COD payment will auto-mark as Paid when order is Delivered';
        note.style.color = '#f59e0b';
    } else {
        note.textContent = `✓ ${paymentMethod} payment - manually update if needed`;
        note.style.color = '#22c55e';
    }
    
    document.getElementById('updateModal').classList.add('active');
}

function closeModal() {
    document.getElementById('updateModal').classList.remove('active');
}

// Close modal on outside click
document.getElementById('updateModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeModal();
    }
});
</script>

</body>
</html>
