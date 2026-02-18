<?php
session_start();
require_once('../db.php'); // DB Connection

// Check admin session
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// 1. FETCH APPOINTMENT DATA
if(isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $query = "SELECT * FROM vet_appointments WHERE id = $id";
    $result = mysqli_query($conn, $query);
    $apt = mysqli_fetch_assoc($result);

    if(!$apt) {
        header("Location: manage_appointments.php?msg=Record Not Found");
        exit();
    }
} else {
    header("Location: manage_appointments.php");
    exit();
}

// 2. HANDLE FORM SUBMISSION (ONLY STATUS UPDATE)
if(isset($_POST['update_appointment'])) {
    $status = mysqli_real_escape_string($conn, $_POST['status']);

    // Only the status is updated in the database
    $updateSql = "UPDATE vet_appointments SET status = '$status' WHERE id = $id";

    if(mysqli_query($conn, $updateSql)) {
        header("Location: manage_appointments.php?msg=Appointment Status Updated Successfully");
        exit();
    }
}

include('includes/header.php'); // Your high-end header
?>

<style>
    /* Professional Form Styling */
    .edit-container {
        max-width: 800px;
        margin: 0 auto;
        background: var(--white);
        border-radius: var(--radius);
        box-shadow: var(--shadow);
        border: 1px solid #f1f5f9;
        overflow: hidden;
    }

    .edit-header {
        padding: 25px 35px;
        background: #f8fafc;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .edit-header h2 { font-size: 1.3rem; font-weight: 800; color: var(--text-main); }
    .btn-back { color: var(--text-muted); text-decoration: none; font-size: 1.1rem; transition: 0.3s; }
    .btn-back:hover { color: var(--primary); }

    .edit-form { padding: 35px; }
    
    .form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 25px;
        margin-bottom: 30px;
    }

    .form-group { display: flex; flex-direction: column; gap: 8px; }
    .form-group.full { grid-column: span 2; }

    .form-group label {
        font-size: 0.85rem;
        font-weight: 700;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .form-group input, .form-group select {
        padding: 14px 18px;
        border: 1.5px solid #e2e8f0;
        border-radius: 10px;
        font-size: 0.95rem;
        color: var(--text-main);
        background: #fcfdfe;
        transition: 0.3s;
    }

    /* Styling for disabled fields */
    .form-group input:disabled {
        background-color: #f1f5f9;
        color: #94a3b8;
        cursor: not-allowed;
        border-style: dashed;
    }

    /* Highlighting the Status dropdown */
    .status-highlight {
        border: 2px solid var(--primary) !important;
        background: #fff !important;
        font-weight: 700 !important;
    }

    .form-group input:focus, .form-group select:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 4px rgba(81, 137, 146, 0.1);
    }

    .form-actions {
        display: flex;
        gap: 15px;
        border-top: 1.5px solid #f8fafc;
        padding-top: 30px;
    }

    .btn-save {
        background: var(--primary);
        color: white;
        padding: 14px 30px;
        border: none;
        border-radius: 10px;
        font-weight: 700;
        cursor: pointer;
        transition: 0.3s;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .btn-save:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(81, 137, 146, 0.3); }

    .btn-cancel {
        background: #f1f5f9;
        color: var(--text-muted);
        padding: 14px 30px;
        text-decoration: none;
        border-radius: 10px;
        font-weight: 700;
        transition: 0.3s;
    }
    .btn-cancel:hover { background: #e2e8f0; color: var(--text-dark); }
</style>

<main class="dashboard-content">
    <div class="edit-container">
        <div class="edit-header">
            <a href="manage_appointments.php" class="btn-back"><i class="fas fa-arrow-left"></i></a>
            <h2>Manage Appointment Status <span style="color: var(--primary);">#<?php echo $apt['id']; ?></span></h2>
        </div>

        <form method="POST" class="edit-form">
            <div class="form-grid">
                <div class="form-group">
                    <label>Pet Parent Name</label>
                    <input type="text" value="<?php echo $apt['pet_parent_name']; ?>" disabled>
                </div>

                <div class="form-group">
                    <label>Pet Name</label>
                    <input type="text" value="<?php echo $apt['pet_name']; ?>" disabled>
                </div>

                <div class="form-group">
                    <label>Preferred Date</label>
                    <input type="date" value="<?php echo $apt['appointment_date']; ?>" disabled>
                </div>

                <div class="form-group">
                    <label>Time Slot</label>
                    <input type="time" value="<?php echo $apt['appointment_time']; ?>" disabled>
                </div>

                <div class="form-group full">
                    <label style="color: var(--primary);">Update Appointment Status</label>
                    <select name="status" class="status-highlight">
                        <option value="Pending" <?php if($apt['status'] == 'Pending') echo 'selected'; ?>>Pending</option>
                        <option value="Confirmed" <?php if($apt['status'] == 'Confirmed') echo 'selected'; ?>>Confirmed</option>
                        <option value="Cancelled" <?php if($apt['status'] == 'Cancelled') echo 'selected'; ?>>Cancelled</option>
                    </select>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" name="update_appointment" class="btn-save">
                    <i class="fas fa-save"></i> Save Status Update
                </button>
                <a href="manage_appointments.php" class="btn-cancel">Cancel</a>
            </div>
        </form>
    </div>
</main>

<?php include('includes/footer.php'); ?>