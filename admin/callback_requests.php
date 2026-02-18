<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Callback Requests - FurryMart Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f8f9fa; padding: 20px; }
        .header { background: linear-gradient(135deg, #f43f5e, #dc2626); color: white; padding: 20px; border-radius: 10px; margin-bottom: 20px; text-align: center; }
        .stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-bottom: 20px; }
        .stat-card { background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); text-align: center; }
        .stat-number { font-size: 2rem; font-weight: bold; color: #f43f5e; }
        .stat-label { color: #64748b; font-size: 0.9rem; margin-top: 5px; }
        .requests-container { background: white; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); overflow: hidden; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #1e293b; color: white; padding: 15px; text-align: left; font-weight: 600; }
        td { padding: 15px; border-bottom: 1px solid #e2e8f0; }
        tr:hover { background: #f8fafc; }
        .status-pending { background: #fef3c7; color: #92400e; padding: 5px 10px; border-radius: 20px; font-size: 0.85rem; font-weight: 600; }
        .status-completed { background: #d1fae5; color: #065f46; padding: 5px 10px; border-radius: 20px; font-size: 0.85rem; font-weight: 600; }
        .phone-number { font-weight: 700; color: #f43f5e; font-size: 1.1rem; letter-spacing: 1px; }
        .call-btn { background: linear-gradient(135deg, #10b981, #059669); color: white; border: none; padding: 8px 15px; border-radius: 20px; cursor: pointer; font-weight: 600; text-decoration: none; display: inline-block; }
        .call-btn:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(16, 185, 129, 0.3); }
        .mark-done-btn { background: #64748b; color: white; border: none; padding: 6px 12px; border-radius: 15px; cursor: pointer; font-size: 0.85rem; margin-left: 5px; }
        .empty-state { padding: 60px 20px; text-align: center; color: #94a3b8; }
        .refresh-btn { background: #0ea5e9; color: white; border: none; padding: 10px 20px; border-radius: 20px; cursor: pointer; font-weight: 600; margin-bottom: 15px; }
    </style>
</head>
<body>
    <?php
    require_once '../db.php';
    
    // Handle mark as completed
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mark_done'])) {
        $id = intval($_POST['request_id']);
        $stmt = $conn->prepare("UPDATE callback_requests SET status = 'completed', called_at = NOW(), called_by = 'Admin' WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
        header("Location: callback_requests.php");
        exit;
    }
    
    // Get stats
    $stats = $conn->query("SELECT 
        COUNT(*) as total,
        SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
        SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed,
        SUM(CASE WHEN DATE(request_time) = CURDATE() THEN 1 ELSE 0 END) as today
        FROM callback_requests")->fetch_assoc();
    
    // Get recent requests
    $requests = $conn->query("SELECT * FROM callback_requests ORDER BY request_time DESC LIMIT 50");
    ?>
    
    <div class="header">
        <h1><i class="fas fa-headset"></i> FurryMart Callback Requests</h1>
        <p>Manage customer callback requests in real-time</p>
    </div>
    
    <div class="stats">
        <div class="stat-card">
            <div class="stat-number" style="color: #f59e0b;"><?php echo $stats['pending']; ?></div>
            <div class="stat-label"><i class="fas fa-clock"></i> Pending</div>
        </div>
        <div class="stat-card">
            <div class="stat-number" style="color: #10b981;"><?php echo $stats['completed']; ?></div>
            <div class="stat-label"><i class="fas fa-check-circle"></i> Completed</div>
        </div>
        <div class="stat-card">
            <div class="stat-number" style="color: #0ea5e9;"><?php echo $stats['today']; ?></div>
            <div class="stat-label"><i class="fas fa-calendar-day"></i> Today</div>
        </div>
        <div class="stat-card">
            <div class="stat-number" style="color: #6366f1;"><?php echo $stats['total']; ?></div>
            <div class="stat-label"><i class="fas fa-phone"></i> Total</div>
        </div>
    </div>
    
    <button class="refresh-btn" onclick="location.reload()">
        <i class="fas fa-sync-alt"></i> Refresh
    </button>
    
    <div class="requests-container">
        
        <?php if ($requests->num_rows > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Phone Number</th>
                    <th>Request Time</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $requests->fetch_assoc()): ?>
                <tr>
                    <td><strong>#<?php echo $row['id']; ?></strong></td>
                    <td><i class="fas fa-user" style="color: #0ea5e9;"></i> <?php echo htmlspecialchars($row['customer_name']); ?></td>
                    <td class="phone-number">
                        <i class="fas fa-mobile-alt"></i> +91 <?php echo htmlspecialchars($row['customer_phone']); ?>
                    </td>
                    <td>
                        <i class="fas fa-clock" style="color: #64748b;"></i> 
                        <?php echo date('d M Y, h:i A', strtotime($row['request_time'])); ?>
                    </td>
                    <td>
                        <?php if($row['status'] === 'pending'): ?>
                            <span class="status-pending"><i class="fas fa-hourglass-half"></i> Pending</span>
                        <?php else: ?>
                            <span class="status-completed"><i class="fas fa-check"></i> Completed</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="tel:+91<?php echo $row['customer_phone']; ?>" class="call-btn">
                            <i class="fas fa-phone"></i> Call Now
                        </a>
                        <?php if($row['status'] === 'pending'): ?>
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="request_id" value="<?php echo $row['id']; ?>">
                            <button type="submit" name="mark_done" class="mark-done-btn">
                                <i class="fas fa-check"></i> Mark Done
                            </button>
                        </form>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <?php else: ?>
        <div class="empty-state">
            <i class="fas fa-inbox" style="font-size: 4rem; margin-bottom: 15px; opacity: 0.3;"></i>
            <h3>No Callback Requests Yet</h3>
            <p>When customers request callbacks, they'll appear here</p>
        </div>
        <?php endif; ?>
    </div>
    
    <script>
        // Auto-refresh every 10 seconds for new requests
        setTimeout(function() {
            location.reload();
        }, 10000);
    </script>
</body>
</html>
