<?php
include '../db.php';
session_start(); // Initialize session at the absolute top

// 1. SESSION SECURITY CHECK (Must be before any output)
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}
// Include your admin session check here if needed

// --- Logic: Handle Status Updates ---
if (isset($_POST['update_status'])) {
    $id = (int)$_POST['booking_id'];
    $new_status = mysqli_real_escape_string($conn, $_POST['new_status']);
    mysqli_query($conn, "UPDATE appoint_grooming SET status='$new_status' WHERE id=$id");
}

// --- Logic: Handle Deletion ---
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    mysqli_query($conn, "DELETE FROM appoint_grooming WHERE id=$id");
    header("Location: admin_grooming.php?msg=deleted");
}

// --- Logic: Fetch Stats ---
$total = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM appoint_grooming"))['count'];
$pending = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM appoint_grooming WHERE status='pending'"))['count'];
$confirmed = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM appoint_grooming WHERE status='confirmed'"))['count'];

include 'includes/header.php'; // Assuming admin header

?>

<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
    :root { 
        --primary:  #00bcd4;; 
        --navy: #0f1c3f; 
        --teal: #518992; 
        --success: #10b981;
        --warning: #f59e0b;
        --glass: rgba(255, 255, 255, 0.95);
    }
    body { font-family: 'Plus Jakarta Sans', sans-serif; background: #f4f7fa; color: var(--navy); }

    /* --- DASHBOARD HEADER --- */
    .admin-hero {
        padding: 60px 5% 140px;
        background: radial-gradient(circle at top right, #1a3a5f, var(--navy));
        color: #fff;
        text-align: left;
        clip-path: polygon(0 0, 100% 0, 100% 85%, 0 100%);
    }

    /* --- STATS GRID --- */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 25px;
        margin-top: -80px;
        padding: 0 5%;
    }
    .stat-card {
        background: #fff; padding: 30px; border-radius: 25px;
        box-shadow: 0 15px 35px rgba(15, 28, 63, 0.08);
        transition: 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        border: 1px solid #fff;
    }
    .stat-card:hover { transform: translateY(-10px); border-color: var(--teal); }
    .stat-card h3 { font-size: 2.5rem; margin: 10px 0; font-weight: 800; }
    .stat-card p { color: var(--teal); font-weight: 700; text-transform: uppercase; letter-spacing: 1px; font-size: 0.8rem; }

    /* --- DATA TABLE CONTAINER --- */
    .data-matrix-container {
        margin: 50px 5% 100px;
        background: var(--glass);
        border-radius: 40px;
        padding: 40px;
        box-shadow: 0 40px 100px rgba(0,0,0,0.05);
        border: 2px solid #fff;
        animation: fadeInUp 0.8s ease;
    }

    .matrix-table { width: 100%; border-collapse: separate; border-spacing: 0 15px; }
    .matrix-table th { padding: 15px 25px; text-align: left; color: #64748b; font-weight: 800; text-transform: uppercase; font-size: 0.75rem; }
    .matrix-row { 
        background: #fff; transition: 0.3s; 
        box-shadow: 0 5px 15px rgba(0,0,0,0.02);
    }
    .matrix-row:hover { background: #f8fafc; transform: scale(1.01); }
    .matrix-row td { padding: 20px 25px; border-top: 1px solid #f1f5f9; border-bottom: 1px solid #f1f5f9; }
    .matrix-row td:first-child { border-left: 1px solid #f1f5f9; border-radius: 20px 0 0 20px; }
    .matrix-row td:last-child { border-right: 1px solid #f1f5f9; border-radius: 0 20px 20px 0; }

    /* --- STATUS BADGES --- */
    .badge {
        padding: 6px 16px; border-radius: 50px; font-size: 0.7rem; font-weight: 800; text-transform: uppercase;
    }
    .status-pending { background: #fffbeb; color: var(--warning); }
    .status-confirmed { background: #ecfdf5; color: var(--success); }
    .status-completed { background: #eff6ff; color: #3b82f6; }
    .status-cancelled { background: #fef2f2; color: var(--primary); }

    /* --- ACTION BUTTONS --- */
    .btn-action {
        width: 40px; height: 40px; border-radius: 12px; border: none; cursor: pointer;
        transition: 0.3s; margin-right: 5px; display: inline-flex; align-items: center; justify-content: center;
    }
    .btn-view { background: #f0f7f8; color: var(--teal); }
    .btn-view:hover { background: var(--teal); color: #fff; }
    .btn-delete { background: #fff1f2; color: var(--primary); }
    .btn-delete:hover { background: var(--primary); color: #fff; }
</style>

<section class="admin-hero">
    <div class="animate__animated animate__fadeInLeft">
        <p style="font-weight: 800; color: var(--teal);">MISSION CONTROL</p>
        <h1 style="font-size: 3.5rem; font-weight: 900; letter-spacing: -2px;">Grooming Deployments</h1>
    </div>
</section>

<div class="stats-grid">
    <div class="stat-card animate__animated animate__zoomIn" style="animation-delay: 0.1s;">
        <p>Total Missions</p>
        <h3><?php echo $total; ?></h3>
    </div>
    <div class="stat-card animate__animated animate__zoomIn" style="animation-delay: 0.2s;">
        <p>Awaiting Authorization</p>
        <h3 style="color: var(--warning);"><?php echo $pending; ?></h3>
    </div>
    <div class="stat-card animate__animated animate__zoomIn" style="animation-delay: 0.3s;">
        <p>Active Deployments</p>
        <h3 style="color: var(--success);"><?php echo $confirmed; ?></h3>
    </div>
</div>

<div class="data-matrix-container">
    <table class="matrix-table">
        <thead>
            <tr>
                <th>Owner & Pet</th>
                <th>Service Mode</th>
                <th>Deployment Date</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $res = mysqli_query($conn, "SELECT * FROM appoint_grooming ORDER BY booking_date DESC");
            while($row = mysqli_fetch_assoc($res)):
                $status_class = 'status-' . $row['status'];
            ?>
            <tr class="matrix-row">
                <td>
                    <div style="font-weight: 800; color: var(--navy);"><?php echo $row['owner_name']; ?></div>
                    <div style="font-size: 0.8rem; color: var(--teal); font-weight: 600;"><?php echo $row['pet_type']; ?> (<?php echo $row['pet_breed']; ?>)</div>
                </td>
                <td>
                    <div style="font-weight: 700;"><?php echo $row['service_type']; ?></div>
                    <div style="font-size: 0.75rem; color: #94a3b8;"><?php echo $row['package_type']; ?> Package</div>
                </td>
                <td>
                    <div style="font-weight: 700;"><?php echo date('M d, Y', strtotime($row['preferred_date'])); ?></div>
                    <div style="font-size: 0.75rem; color: #94a3b8;"><?php echo date('h:i A', strtotime($row['preferred_time'])); ?></div>
                </td>
                <td>
                    <span class="badge <?php echo $status_class; ?>"><?php echo $row['status']; ?></span>
                </td>
                <td>
                    <button class="btn-action btn-view" title="View Full Intel" 
                            onclick="viewBookingIntel(<?php echo htmlspecialchars(json_encode($row)); ?>)">
                        <i class="fas fa-expand-alt"></i>
                    </button>
                    
                    <button class="btn-action btn-view" style="color: var(--warning); background: #fffbeb;" title="Change Status"
                            onclick="changeBookingStatus(<?php echo $row['id']; ?>, '<?php echo $row['status']; ?>')">
                        <i class="fas fa-sync-alt"></i>
                    </button>

                    <a href="?delete=<?php echo $row['id']; ?>" class="btn-action btn-delete" 
                       onclick="return confirm('Abort Mission? This cannot be undone.')">
                        <i class="fas fa-trash-alt"></i>
                    </a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<script>
/**
 * CINEMATIC INTEL MODAL
 * Displays full pet report including addresses and age.
 */
function viewBookingIntel(data) {
    Swal.fire({
        html: `
            <div style="text-align:left; padding: 10px;">
                <div style="display:flex; align-items:center; gap:20px; margin-bottom:30px; border-bottom:1px solid #f1f5f9; padding-bottom:25px;">
                    <div style="width:70px; height:70px; border-radius:50%; background:var(--teal); color:#fff; display:flex; align-items:center; justify-content:center; font-size:28px; font-weight:800;">${data.pet_type[0]}</div>
                    <div>
                        <h4 style="margin:0; font-weight:800; color:var(--navy); font-size:1.4rem;">${data.owner_name}</h4>
                        <small style="color:#64748b; font-weight:700; font-size:14px; text-transform:uppercase;">Parent Intel • ID #${data.id}</small>
                    </div>
                </div>
                
                <div style="display:grid; grid-template-columns: 1fr 1fr; gap:20px; margin-bottom:30px;">
                    <div style="background:#f8fafc; padding:20px; border-radius:20px;">
                        <p style="margin:0; font-size:11px; font-weight:800; color:var(--teal);">PET BIO</p>
                        <p style="margin:5px 0 0; font-weight:700;">${data.pet_breed} (${data.pet_age})</p>
                    </div>
                    <div style="background:#f8fafc; padding:20px; border-radius:20px;">
                        <p style="margin:0; font-size:11px; font-weight:800; color:var(--teal);">CONTACT LINKS</p>
                        <p style="margin:5px 0 0; font-weight:700;">${data.owner_phone}</p>
                    </div>
                </div>

                <div style="background:#f8fafc; padding:25px; border-radius:25px; margin-bottom:20px; border-left:8px solid var(--teal);">
                    <p style="margin:0; font-size:11px; font-weight:800; color:var(--teal);">DEPLOYMENT TERRITORY</p>
                    <p style="margin:10px 0 0; font-weight:600; line-height:1.6;">${data.owner_address}</p>
                </div>

                <div style="text-align:center; padding-top:15px;">
                    <span class="badge status-${data.status}" style="font-size:14px; padding:10px 30px;">MISSION STATUS: ${data.status}</span>
                </div>
            </div>`,
        showConfirmButton: false,
        showCloseButton: true,
        width: '750px',
        showClass: { popup: 'animate__animated animate__bounceIn' }
    });
}

/**
 * STATUS CONFIGURATION ENGINE
 */
function changeBookingStatus(id, current) {
    Swal.fire({
        title: 'Update Mission Status',
        input: 'select',
        inputOptions: {
            'pending': 'Pending',
            'confirmed': 'Confirmed',
            'completed': 'Completed',
            'cancelled': 'Cancelled'
        },
        inputValue: current,
        showCancelButton: true,
        confirmButtonColor: '#0f1c3f',
        confirmButtonText: 'Update Intelligence'
    }).then((result) => {
        if (result.value) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.innerHTML = `
                <input type="hidden" name="update_status" value="1">
                <input type="hidden" name="booking_id" value="${id}">
                <input type="hidden" name="new_status" value="${result.value}">
            `;
            document.body.appendChild(form);
            form.submit();
        }
    });
}
</script>

<?php include 'includes/footer.php'; ?>