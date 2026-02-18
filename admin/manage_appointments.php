<?php
session_start();
require_once('../db.php'); // DB Connection

// Check admin session
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

/** 1. HANDLE ACTIONS (Status Update & Delete) **/

// Handle Status Update
if(isset($_GET['update_id']) && isset($_GET['new_status'])){
    $id = intval($_GET['update_id']);
    $status = mysqli_real_escape_string($conn, $_GET['new_status']);
    mysqli_query($conn, "UPDATE vet_appointments SET status='$status' WHERE id=$id");
    header("Location: manage_appointments.php?msg=Status Updated Successfully");
}

// Handle Delete
if(isset($_GET['delete_id'])){
    $id = intval($_GET['delete_id']);
    mysqli_query($conn, "DELETE FROM vet_appointments WHERE id=$id");
    header("Location: manage_appointments.php?msg=Appointment Deleted");
}

/** 2. FETCH APPOINTMENTS **/
$query = "SELECT * FROM vet_appointments ORDER BY id DESC";
$result = mysqli_query($conn, $query);

include('includes/header.php'); 
?>

<style>
    /* Professional Table Container */
    .manage-container {
        background: var(--white);
        border-radius: var(--radius);
        box-shadow: var(--shadow);
        border: 1px solid #f1f5f9;
        overflow: hidden;
    }

    .manage-header {
        padding: 25px 30px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 1px solid #f1f5f9;
    }

    .manage-header h2 { font-size: 1.4rem; font-weight: 800; color: var(--text-main); }

    /* Modern Table UI */
    .data-table { width: 100%; border-collapse: collapse; }
    .data-table th { 
        text-align: left; padding: 18px 20px; background: #f8fafc; 
        font-size: 0.75rem; font-weight: 700; color: var(--text-muted); 
        text-transform: uppercase; letter-spacing: 1px;
    }
    .data-table td { padding: 20px; border-bottom: 1px solid #f1f5f9; font-size: 0.92rem; vertical-align: middle; }
    .data-table tr:hover { background-color: #fcfdfe; }

    /* Status Pill Logic */
    .status-pill {
        padding: 6px 14px;
        border-radius: 50px;
        font-size: 0.75rem;
        font-weight: 800;
        text-transform: uppercase;
        display: inline-block;
    }
    .status-pending { background: #fff7ed; color: #c2410c; border: 1px solid #fed7aa; }
    .status-confirmed { background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0; }
    .status-cancelled { background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; }

    /* Action Buttons */
    .action-btn {
        padding: 8px 12px;
        border-radius: 8px;
        font-size: 0.85rem;
        text-decoration: none;
        font-weight: 700;
        transition: 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .btn-confirm { background: #f0fdf4; color: #166534; }
    .btn-confirm:hover { background: #166534; color: white; }

    .btn-edit { background: #eff6ff; color: #1d4ed8; }
    .btn-edit:hover { background: #1d4ed8; color: white; }

    /* Disabled Style for Confirmed Rows */
    .btn-disabled {
        background: #f1f5f9 !important;
        color: #94a3b8 !important;
        cursor: not-allowed !important;
        pointer-events: none; /* Prevents clicking */
    }

    .btn-delete { background: #fef2f2; color: #991b1b; }
    .btn-delete:hover { background: #991b1b; color: white; }

    .msg-alert { padding: 15px 25px; background: #ecfdf5; color: #065f46; border-radius: 10px; margin-bottom: 25px; font-weight: 600; border: 1px solid #a7f3d0; }
</style>

<main class="dashboard-content">

    <?php if(isset($_GET['msg'])) echo "<div class='msg-alert'><i class='fas fa-check-circle'></i> ".htmlspecialchars($_GET['msg'])."</div>"; ?>

    <div class="manage-container">
        <div class="manage-header">
            <h2>Appointments Management</h2>
            <div style="display: flex; gap: 10px;">
                <button onclick="window.print()" class="btn-logout" style="background: var(--bg-light); color: var(--text-main); padding: 8px 15px; border-radius: 8px; border: 1px solid #e2e8f0; cursor: pointer;"><i class="fas fa-print"></i> Export</button>
            </div>
        </div>

        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Parent & Pet</th>
                    <th>Date & Time</th>
                    <th>Service</th>
                    <th>Status</th>
                    <th>Quick Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if(mysqli_num_rows($result) > 0): ?>
                    <?php while($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td style="color: var(--text-muted);">#<?php echo $row['id']; ?></td>
                        <td>
                            <b style="color: var(--text-main);"><?php echo $row['pet_parent_name']; ?></b><br>
                            <small style="color: var(--text-muted);"><?php echo $row['pet_name']; ?> (<?php echo $row['pet_type']; ?>)</small>
                        </td>
                        <td>
                            <i class="far fa-calendar-alt" style="color: var(--primary);"></i> <?php echo date('M d, Y', strtotime($row['appointment_date'])); ?><br>
                            <i class="far fa-clock" style="color: var(--text-muted);"></i> <?php echo $row['appointment_time']; ?>
                        </td>
                        <td>
                            <span style="font-weight: 600; color: var(--text-main);">
                                <?php 
                                    $sid = $row['service_id'];
                                    // Error suppressed in case table doesn't exist yet
                                    $s_query = mysqli_query($conn, "SELECT title FROM vet_services WHERE id=$sid");
                                    if($s_query) {
                                        $s = mysqli_fetch_assoc($s_query);
                                        echo $s['title'] ?? "General Checkup";
                                    } else {
                                        echo "General Checkup";
                                    }
                                ?>
                            </span>
                        </td>
                        <td>
                            <span class="status-pill status-<?php echo strtolower($row['status']); ?>">
                                <?php echo $row['status']; ?>
                            </span>
                        </td>
                        <td>
                            <div style="display: flex; gap: 8px;">
                                <?php if($row['status'] == 'Pending'): ?>
                                    <a href="manage_appointments.php?update_id=<?php echo $row['id']; ?>&new_status=Confirmed" class="action-btn btn-confirm" title="Confirm">
                                        <i class="fas fa-check"></i>
                                    </a>
                                <?php endif; ?>

                                <?php if($row['status'] == 'Confirmed'): ?>
                                    <a href="javascript:void(0)" class="action-btn btn-edit btn-disabled" title="Confirmed Appointments Cannot Be Edited">
                                        <i class="fas fa-pen"></i>
                                    </a>
                                <?php else: ?>
                                    <a href="edit_appointment.php?id=<?php echo $row['id']; ?>" class="action-btn btn-edit" title="Edit">
                                        <i class="fas fa-pen"></i>
                                    </a>
                                <?php endif; ?>

                                <a href="manage_appointments.php?delete_id=<?php echo $row['id']; ?>" class="action-btn btn-delete" title="Delete" onclick="return confirm('Permanently delete this appointment?')">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="6" style="text-align: center; padding: 50px; color: var(--text-muted);">No appointments found in database.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</main>

<?php include('includes/footer.php'); ?>