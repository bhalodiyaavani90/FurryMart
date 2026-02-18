<?php
require_once '../db.php'; 

// --- 1. HANDLE ACTION LOGIC (MUST BE BEFORE HEADER) ---
if (isset($_GET['approve'])) {
    $id = (int)$_GET['approve'];
    mysqli_query($conn, "UPDATE customer_feedbacks SET status='approved' WHERE id = $id");
    header("Location: admin_manage_feedback.php?msg=approved");
    exit();
}

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    mysqli_query($conn, "DELETE FROM customer_feedbacks WHERE id = $id");
    header("Location: admin_manage_feedback.php?msg=deleted");
    exit();
}

// --- 2. INCLUDE THE PROFESSIONAL HEADER ---
include('includes/header.php'); 

$msg_type = ""; $msg_text = "";
if(isset($_GET['msg'])){
    if($_GET['msg'] == "approved"){ $msg_type = "success"; $msg_text = "Feedback Signature Approved & Live!"; }
    if($_GET['msg'] == "deleted"){ $msg_type = "success"; $msg_text = "Feedback Record Purged Successfully."; }
}

// --- 3. FILTER LOGIC ---
$where_clause = "WHERE 1=1";
$active_filter = 'all';
if(isset($_GET['rating']) && $_GET['rating'] != 'all') {
    $rating_val = (int)$_GET['rating'];
    $where_clause .= " AND rating = $rating_val";
    $active_filter = $rating_val;
}

$result = mysqli_query($conn, "SELECT * FROM customer_feedbacks $where_clause ORDER BY status DESC, id DESC");
?>

<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
    :root { --primary: #518992; --navy: #0f1c3f; --glass: rgba(255, 255, 255, 0.95); --star-red: #f43f5e; }
    body { font-family: 'Plus Jakarta Sans', sans-serif; background: #f4f7fa; color: var(--navy); }

    .dashboard-container { padding: 40px; animation: fadeIn 0.8s ease; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }

    .premium-card {
        background: var(--glass);
        backdrop-filter: blur(25px);
        border-radius: 35px;
        border: 2px solid #ffffff;
        box-shadow: 0 25px 50px -12px rgba(15, 28, 63, 0.1);
        padding: 45px;
    }

    /* RATING FILTER STYLE */
    .filter-pod { display: flex; background: #fff; border: 1px solid #eef2f6; border-radius: 50px; padding: 5px; gap: 5px; box-shadow: 0 4px 15px rgba(0,0,0,0.03); }
    .filter-btn { 
        border: none; background: none; padding: 10px 20px; border-radius: 50px; 
        font-weight: 800; font-size: 0.85rem; color: #94a3b8; transition: 0.3s; cursor: pointer; text-decoration: none;
    }
    .filter-btn.active { background: var(--primary); color: #fff; box-shadow: 0 5px 15px rgba(81, 137, 146, 0.3); }
    .filter-btn:hover:not(.active) { background: #f8fafc; color: var(--navy); }

    /* PERFECTED STAR STYLING */
    .stars-horizontal { color: var(--star-red); display: flex; gap: 4px; font-size: 13px; align-items: center; }
    .stars-horizontal i { transition: 0.3s ease; }
    .pro-table tr:hover .stars-horizontal i { transform: scale(1.1); }

    .pro-table { border-collapse: separate; border-spacing: 0 15px; width: 100%; }
    .pro-table tr { background: white; border-radius: 25px; transition: 0.4s; }
    .pro-table tr:hover { transform: scale(1.01) translateX(10px); box-shadow: 0 15px 35px rgba(0,0,0,0.05); }
    
    .pro-table td { padding: 25px; border: none; vertical-align: middle; }
    .pro-table td:first-child { border-radius: 25px 0 0 25px; padding-left: 30px; }
    .pro-table td:last-child { border-radius: 0 25px 25px 0; padding-right: 30px; }

    .avatar-pod {
        width: 55px; height: 55px; border-radius: 18px;
        background: var(--primary); color: white;
        display: flex; align-items: center; justify-content: center;
        font-weight: 800; font-size: 1.2rem; box-shadow: 0 8px 15px rgba(81, 137, 146, 0.2);
    }

    .cmd-icon {
        width: 44px; height: 44px; display: inline-flex;
        align-items: center; justify-content: center;
        border-radius: 14px; margin: 0 5px; transition: 0.3s;
        border: none; cursor: pointer; text-decoration: none;
    }
    .h-approve { background: #f0fdf4; color: #16a34a; }
    .h-approve:hover { background: #16a34a; color: white; }
    .h-delete { background: #fff1f2; color: #f43f5e; }
    .h-delete:hover { background: #f43f5e; color: white; }

    .status-badge { padding: 8px 16px; border-radius: 50px; font-weight: 800; font-size: 0.75rem; text-transform: uppercase; }
    .bg-approved { background: #dcfce7; color: #166534; }
    .bg-pending { background: #fef9c3; color: #854d0e; }
</style>

<div class="dashboard-container">
    <div class="premium-card">
        <div class="d-flex justify-content-between align-items-center mb-5">
            <div>
                <h4 class="fw-800 m-0"><i class="fas fa-comments text-primary me-2"></i> FEEDBACK REGISTRY</h4>
                <p class="text-muted small fw-600 mt-1">Moderate real stories from your pet parents.</p>
            </div>
            
            <div class="filter-pod">
                <a href="?rating=all" class="filter-btn <?php echo $active_filter == 'all' ? 'active' : ''; ?>">All</a>
                <a href="?rating=5" class="filter-btn <?php echo $active_filter == '5' ? 'active' : ''; ?>">5 ★</a>
                <a href="?rating=4" class="filter-btn <?php echo $active_filter == '4' ? 'active' : ''; ?>">4 ★</a>
                <a href="?rating=3" class="filter-btn <?php echo $active_filter == '3' ? 'active' : ''; ?>">3 ★</a>
                <a href="?rating=2" class="filter-btn <?php echo $active_filter == '2' ? 'active' : ''; ?>">2 ★</a>
                <a href="?rating=1" class="filter-btn <?php echo $active_filter == '1' ? 'active' : ''; ?>">1 ★</a>
            </div>
        </div>
        
        <div class="table-responsive">
            <table class="pro-table">
                <thead>
                    <tr style="font-size: 0.72rem; font-weight: 800; text-transform: uppercase; color: #64748b; letter-spacing: 2px;">
                        <th style="padding-left: 30px;">Identity</th>
                        <th>Parent Details</th>
                        <th>Rating Status</th> <th>Story Snippet</th>
                        <th>Status</th>
                        <th class="text-end" style="padding-right: 30px;">Command</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(mysqli_num_rows($result) > 0): ?>
                        <?php while($row = mysqli_fetch_assoc($result)): 
                            $initial = strtoupper(substr($row['user_name'], 0, 1));
                            $rating = (int)$row['rating'];
                        ?>
                        <tr>
                            <td style="padding-left: 30px;">
                                <div class="avatar-pod"><?php echo $initial; ?></div>
                            </td>
                            <td>
                                <div class="fw-800 text-navy mb-1"><?php echo htmlspecialchars($row['user_name']); ?></div>
                                <div class="small fw-600 text-muted"><?php echo htmlspecialchars($row['user_email']); ?></div>
                            </td>
                            <td>
                                <div class="stars-horizontal">
                                    <?php 
                                    for($i=1; $i<=5; $i++){ 
                                        echo $i <= $rating ? '<i class="fas fa-star"></i>' : '<i class="far fa-star" style="color:#cbd5e1;"></i>'; 
                                    } 
                                    ?>
                                </div>
                            </td>
                            <td>
                                <div style="max-width: 250px; font-size: 0.85rem; color: #64748b; line-height: 1.5;" class="text-truncate">
                                    "<?php echo htmlspecialchars($row['feedback_text']); ?>"
                                </div>
                            </td>
                            <td>
                                <span class="status-badge bg-<?php echo $row['status']; ?>">
                                    <?php echo $row['status']; ?>
                                </span>
                            </td>
                            <td class="text-end" style="padding-right: 30px;">
                                <?php if($row['status'] == 'pending'): ?>
                                    <a href="?approve=<?php echo $row['id']; ?>" class="cmd-icon h-approve shadow-sm" title="Authorize Review">
                                        <i class="fas fa-check-circle"></i>
                                    </a>
                                <?php endif; ?>
                                <button class="cmd-icon h-delete shadow-sm" title="Purge Record" onclick="confirmPurge(<?php echo $row['id']; ?>)">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <i class="fas fa-search-minus text-muted mb-3" style="font-size: 2rem;"></i>
                                <p class="fw-800 text-muted">No records found matching this rating criteria.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
// Alert Logic
<?php if($msg_type != ""): ?>
Swal.fire({ icon: '<?php echo $msg_type; ?>', title: 'SYSTEM LOG', text: '<?php echo $msg_text; ?>', timer: 2500, showConfirmButton: false, toast: true, position: 'top-end' });
<?php endif; ?>

function confirmPurge(id) {
    Swal.fire({
        title: 'PURGE STORY?',
        text: "This record will be permanently erased.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#0f1c3f',
        confirmButtonText: 'CONFIRM'
    }).then((result) => { if (result.isConfirmed) window.location.href = 'admin_manage_feedback.php?delete=' + id; });
}
</script>

<?php include('includes/footer.php'); ?>