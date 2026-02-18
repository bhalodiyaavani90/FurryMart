<?php
// 1. START SESSIONS AND INCLUDES (Logic only, no HTML)
session_start();
include '../db.php';
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// 2. HANDLE DATA UPDATES (Must happen before any HTML output)
if (isset($_POST['update_status'])) {
    $id = mysqli_real_escape_string($conn, $_POST['query_id']);
    $type = mysqli_real_escape_string($conn, $_POST['query_type']);
    $new_status = mysqli_real_escape_string($conn, $_POST['status']);
    
    // Update based on type
    if ($type === 'contact') {
        $update_sql = "UPDATE contact_queries SET status = '$new_status' WHERE id = '$id'";
    } elseif ($type === 'callback') {
        $update_sql = "UPDATE callback_requests SET status = '$new_status' WHERE id = '$id'";
    } elseif ($type === 'quick') {
        $update_sql = "UPDATE quick_queries SET status = '$new_status' WHERE id = '$id'";
    }
    
    mysqli_query($conn, $update_sql);
    header("Location: admin_queries.php?updated=1&filter=" . (isset($_POST['current_filter']) ? $_POST['current_filter'] : 'all'));
    exit();
}

// 3. HANDLE DELETE (Must happen before any HTML output)
if (isset($_GET['delete_id']) && isset($_GET['type'])) {
    $id = mysqli_real_escape_string($conn, $_GET['delete_id']);
    $type = mysqli_real_escape_string($conn, $_GET['type']);
    
    if ($type === 'contact') {
        mysqli_query($conn, "DELETE FROM contact_queries WHERE id = '$id'");
    } elseif ($type === 'callback') {
        mysqli_query($conn, "DELETE FROM callback_requests WHERE id = '$id'");
    } elseif ($type === 'quick') {
        mysqli_query($conn, "DELETE FROM quick_queries WHERE id = '$id'");
    }
    
    header("Location: admin_queries.php?deleted=1&filter=" . (isset($_GET['filter']) ? $_GET['filter'] : 'all'));
    exit();
}

// 4. GET FILTER PARAMETER
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';

// 5. FETCH DATA BASED ON FILTER
$all_requests = [];

if ($filter === 'all' || $filter === 'callback') {
    $callback_query = mysqli_query($conn, "SELECT *, 'callback' as type FROM callback_requests ORDER BY created_at DESC");
    if ($callback_query) {
        while ($row = mysqli_fetch_assoc($callback_query)) {
            $all_requests[] = $row;
        }
    }
}

if ($filter === 'all' || $filter === 'quick') {
    $quick_query = mysqli_query($conn, "SELECT *, 'quick' as type FROM quick_queries ORDER BY created_at DESC");
    if ($quick_query) {
        while ($row = mysqli_fetch_assoc($quick_query)) {
            $all_requests[] = $row;
        }
    }
}

if ($filter === 'all' || $filter === 'contact') {
    $contact_query = mysqli_query($conn, "SELECT *, 'contact' as type FROM contact_queries ORDER BY created_at DESC");
    if ($contact_query) {
        while ($row = mysqli_fetch_assoc($contact_query)) {
            $all_requests[] = $row;
        }
    }
}

// Sort all requests by created_at DESC
usort($all_requests, function($a, $b) {
    return strtotime($b['created_at']) - strtotime($a['created_at']);
});

// Count totals
$callback_count_query = mysqli_query($conn, "SELECT COUNT(*) as count FROM callback_requests");
$total_callback = $callback_count_query ? mysqli_fetch_assoc($callback_count_query)['count'] : 0;

$quick_count_query = mysqli_query($conn, "SELECT COUNT(*) as count FROM quick_queries");
$total_quick = $quick_count_query ? mysqli_fetch_assoc($quick_count_query)['count'] : 0;

$contact_count_query = mysqli_query($conn, "SELECT COUNT(*) as count FROM contact_queries");
$total_contact = $contact_count_query ? mysqli_fetch_assoc($contact_count_query)['count'] : 0;

$total_all = $total_callback + $total_quick + $total_contact;

// 6. NOW START THE VISUAL OUTPUT
include 'includes/header.php'; 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        :root {
            --unread: #ff7675; --read: #74b9ff; --pending: #fdcb6e; --in-process: #e17055; 
            --resolved: #55efc4; --completed: #55efc4; --cancelled: #636e72;
            --pm-accent: #FF8E72;
        }
        body { background: #f8f9fa; }
        .admin-card { background: white; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); padding: 30px; margin-top: 20px; }
        
        /* Filter Toggle Buttons */
        .filter-tabs {
            display: flex;
            gap: 15px;
            margin-bottom: 30px;
            flex-wrap: wrap;
        }
        
        .filter-btn {
            padding: 12px 25px;
            border: 2px solid #e2e8f0;
            background: white;
            color: #64748b;
            border-radius: 50px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .filter-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .filter-btn.active {
            background: linear-gradient(135deg, #FF8E72, #ff7b5a);
            color: white;
            border-color: #FF8E72;
        }
        
        .filter-btn .count {
            background: rgba(0,0,0,0.15);
            padding: 2px 10px;
            border-radius: 20px;
            font-size: 0.85rem;
        }
        
        .filter-btn.active .count {
            background: rgba(255,255,255,0.3);
        }
        
        .table-custom { width: 100%; border-collapse: separate; border-spacing: 0 10px; }
        .table-custom th { padding: 15px; color: #888; text-align: left; border-bottom: 2px solid #eee; }
        .q-row { background: #fff; transition: 0.3s; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.03); }
        .q-row:hover { transform: scale(1.005); box-shadow: 0 5px 15px rgba(0,0,0,0.08); }
        .q-cell { padding: 20px; border-top: 1px solid #f9f9f9; }
        
        /* Status Badges */
        .badge { padding: 5px 12px; border-radius: 20px; font-size: 0.8rem; font-weight: 700; color: white; display: inline-block; }
        .unread { background: var(--unread); }
        .read { background: var(--read); }
        .pending { background: var(--pending); }
        .in_process { background: var(--in-process); }
        .in-progress { background: var(--in-process); }
        .resolved { background: var(--resolved); }
        .completed { background: var(--completed); }
        .cancelled { background: var(--cancelled); }
        
        /* Type Badges */
        .type-badge {
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 700;
            display: inline-block;
            margin-bottom: 8px;
        }
        
        .type-callback {
            background: linear-gradient(135deg, #f43f5e, #ec4899);
            color: white;
        }
        
        .type-quick {
            background: linear-gradient(135deg, #0ea5e9, #0284c7);
            color: white;
        }
        
        .type-contact {
            background: linear-gradient(135deg, #8b5cf6, #7c3aed);
            color: white;
        }

        .btn-delete { color: #ff7675; cursor: pointer; font-size: 1.2rem; transition: 0.3s; }
        .btn-delete:hover { color: #d63031; transform: rotate(15deg); }
        
        select.status-select { padding: 8px; border-radius: 10px; border: 1px solid #ddd; outline: none; }
        select.status-select:disabled { opacity: 0.5; cursor: not-allowed; background: #f1f5f9; }
        
        .btn-save { border:none; background:none; color:#518992; cursor:pointer; font-size: 1.1rem; transition: 0.2s; }
        .btn-save:hover:not(:disabled) { color: var(--pm-accent); transform: scale(1.2); }
        .btn-save:disabled { opacity: 0.4; cursor: not-allowed; }
        
        .stats-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }
        
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #94a3b8;
        }
        
        .empty-state i {
            font-size: 4rem;
            margin-bottom: 20px;
            opacity: 0.5;
        }
    </style>
</head>
<body>

<div class="admin-card animate__animated animate__fadeIn">
    <div class="stats-row">
        <h2><i class="fas fa-headset" style="color: var(--pm-accent);"></i> Support Management</h2>
        <div style="text-align: right;">
            <span style="background: linear-gradient(135deg, #FF8E72, #ff7b5a); color: white; padding: 8px 20px; border-radius: 50px; font-size: 0.95rem; font-weight: 700;">
                <?php 
                    if ($filter === 'all') echo "Total Requests: <strong>$total_all</strong>";
                    elseif ($filter === 'callback') echo "Callbacks: <strong>$total_callback</strong>";
                    elseif ($filter === 'quick') echo "Quick Queries: <strong>$total_quick</strong>";
                    elseif ($filter === 'contact') echo "Contact Forms: <strong>$total_contact</strong>";
                ?>
            </span>
        </div>
    </div>
    
    <!-- Filter Toggle Buttons -->
    <div class="filter-tabs">
        <a href="admin_queries.php?filter=all" class="filter-btn <?php echo $filter === 'all' ? 'active' : ''; ?>">
            <i class="fas fa-grip-horizontal"></i>
            All Requests
            <span class="count"><?php echo $total_all; ?></span>
        </a>
        <a href="admin_queries.php?filter=callback" class="filter-btn <?php echo $filter === 'callback' ? 'active' : ''; ?>">
            <i class="fas fa-phone-volume"></i>
            Call Requests
            <span class="count"><?php echo $total_callback; ?></span>
        </a>
        <a href="admin_queries.php?filter=quick" class="filter-btn <?php echo $filter === 'quick' ? 'active' : ''; ?>">
            <i class="fas fa-bolt"></i>
            Quick Queries
            <span class="count"><?php echo $total_quick; ?></span>
        </a>
        <a href="admin_queries.php?filter=contact" class="filter-btn <?php echo $filter === 'contact' ? 'active' : ''; ?>">
            <i class="fas fa-file-alt"></i>
            Contact Forms
            <span class="count"><?php echo $total_contact; ?></span>
        </a>
    </div>

    <table class="table-custom">
        <thead>
            <tr>
                <th>Type</th>
                <th>Date</th>
                <th>User Details</th>
                <th>Subject & Message</th>
                <th>Manage Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if(count($all_requests) > 0): ?>
                <?php foreach($all_requests as $row): ?>
                <tr class="q-row animate__animated animate__fadeInUp">
                    <!-- Type Badge -->
                    <td class="q-cell">
                        <?php if($row['type'] === 'callback'): ?>
                            <div class="type-badge type-callback">
                                <i class="fas fa-phone"></i> Callback
                            </div>
                        <?php elseif($row['type'] === 'quick'): ?>
                            <div class="type-badge type-quick">
                                <i class="fas fa-bolt"></i> Quick
                            </div>
                        <?php else: ?>
                            <div class="type-badge type-contact">
                                <i class="fas fa-file-alt"></i> Form
                            </div>
                        <?php endif; ?>
                    </td>
                    
                    <!-- Date -->
                    <td class="q-cell" style="font-size: 0.9rem; color: #999;">
                        <?php echo date('d M, Y', strtotime($row['created_at'])); ?><br>
                        <small><?php echo date('h:i A', strtotime($row['created_at'])); ?></small>
                    </td>
                    
                    <!-- User Details -->
                    <td class="q-cell">
                        <?php if($row['type'] === 'callback'): ?>
                            <strong><?php echo htmlspecialchars($row['name']); ?></strong><br>
                            <small style="color: #f43f5e; font-weight: 600;">
                                <i class="fas fa-phone"></i> <?php echo htmlspecialchars($row['phone']); ?>
                            </small>
                        <?php elseif($row['type'] === 'quick'): ?>
                            <strong><?php echo htmlspecialchars($row['name']); ?></strong><br>
                            <small style="color: #0ea5e9; font-weight: 600;">
                                <i class="fas fa-envelope"></i> <?php echo htmlspecialchars($row['email']); ?>
                            </small>
                        <?php else: ?>
                            <strong><?php echo htmlspecialchars($row['first_name'] . " " . $row['last_name']); ?></strong><br>
                            <small style="color: #666;">
                                <i class="fas fa-phone"></i> <?php echo htmlspecialchars($row['mobile']); ?><br>
                                <i class="fas fa-envelope"></i> <?php echo htmlspecialchars($row['email']); ?>
                            </small>
                        <?php endif; ?>
                    </td>
                    
                    <!-- Subject & Message -->
                    <td class="q-cell">
                        <?php if($row['type'] === 'callback'): ?>
                            <strong style="color: #f43f5e;">Callback Request</strong>
                            <p style="font-size: 0.85rem; color: #555; margin-top: 5px;">
                                Customer requested a callback
                            </p>
                        <?php elseif($row['type'] === 'quick'): ?>
                            <strong style="color: #0ea5e9;">Quick Query</strong>
                            <p style="font-size: 0.85rem; color: #555; max-width: 300px; margin-top: 5px;">
                                <?php echo htmlspecialchars(substr($row['message'], 0, 100)) . (strlen($row['message']) > 100 ? '...' : ''); ?>
                            </p>
                        <?php else: ?>
                            <strong style="color: var(--pm-accent);"><?php echo htmlspecialchars($row['subject']); ?></strong>
                            <p style="font-size: 0.85rem; color: #555; max-width: 300px; margin-top: 5px;">
                                <?php echo htmlspecialchars(substr($row['message'], 0, 100)) . (strlen($row['message']) > 100 ? '...' : ''); ?>
                            </p>
                        <?php endif; ?>
                    </td>
                    
                    <!-- Manage Status -->
                    <td class="q-cell">
                        <?php 
                            $is_resolved = ($row['status'] === 'resolved' || $row['status'] === 'completed');
                        ?>
                        <form method="POST" style="display: flex; gap: 8px; align-items: center;">
                            <input type="hidden" name="query_id" value="<?php echo $row['id']; ?>">
                            <input type="hidden" name="query_type" value="<?php echo $row['type']; ?>">
                            <input type="hidden" name="current_filter" value="<?php echo $filter; ?>">
                            
                            <select name="status" class="status-select" <?php echo $is_resolved ? 'disabled' : ''; ?>>
                                <?php if($row['type'] === 'callback'): ?>
                                    <option value="pending" <?php if($row['status']=='pending') echo 'selected'; ?>>Pending</option>
                                    <option value="in_progress" <?php if($row['status']=='in_progress' || $row['status']=='in-progress') echo 'selected'; ?>>In Progress</option>
                                    <option value="completed" <?php if($row['status']=='completed') echo 'selected'; ?>>Completed</option>
                                    <option value="cancelled" <?php if($row['status']=='cancelled') echo 'selected'; ?>>Cancelled</option>
                                <?php else: ?>
                                    <option value="unread" <?php if($row['status']=='unread') echo 'selected'; ?>>Unread</option>
                                    <option value="read" <?php if($row['status']=='read') echo 'selected'; ?>>Read</option>
                                    <option value="pending" <?php if($row['status']=='pending') echo 'selected'; ?>>Pending</option>
                                    <option value="in_process" <?php if($row['status']=='in_process' || $row['status']=='in-process') echo 'selected'; ?>>In Progress</option>
                                    <option value="resolved" <?php if($row['status']=='resolved') echo 'selected'; ?>>Resolved</option>
                                <?php endif; ?>
                            </select>
                            
                            <button type="submit" name="update_status" class="btn-save" title="Update Status" <?php echo $is_resolved ? 'disabled' : ''; ?>>
                                <i class="fas fa-save"></i>
                            </button>
                        </form>
                        <div class="badge <?php echo str_replace('_', '-', $row['status']); ?>" style="margin-top: 8px;">
                            <?php echo strtoupper(str_replace(['_', '-'], ' ', $row['status'])); ?>
                        </div>
                    </td>
                    
                    <!-- Actions -->
                    <td class="q-cell">
                        <a href="admin_queries.php?delete_id=<?php echo $row['id']; ?>&type=<?php echo $row['type']; ?>&filter=<?php echo $filter; ?>" 
                           onclick="return confirm('Paws for a second! Are you sure you want to delete this permanently?')" 
                           class="btn-delete">
                            <i class="fas fa-trash-alt"></i>
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" class="empty-state">
                        <i class="fas fa-folder-open"></i>
                        <h3 style="margin: 10px 0;">No Requests Found</h3>
                        <p>No <?php echo $filter === 'all' ? '' : $filter; ?> requests in the database yet.</p>
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script>
// Show success message if updated or deleted
<?php if(isset($_GET['updated'])): ?>
    Swal.fire({
        title: 'Updated!',
        text: 'Status has been updated successfully.',
        icon: 'success',
        iconColor: '#FF8E72',
        confirmButtonColor: '#FF8E72',
        timer: 2000,
        showConfirmButton: false
    });
<?php endif; ?>

<?php if(isset($_GET['deleted'])): ?>
    Swal.fire({
        title: 'Deleted!',
        text: 'Request has been deleted successfully.',
        icon: 'success',
        iconColor: '#FF8E72',
        confirmButtonColor: '#FF8E72',
        timer: 2000,
        showConfirmButton: false
    });
<?php endif; ?>
</script>

</body>
</html>