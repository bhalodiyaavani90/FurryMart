<?php
session_start();
require_once('../db.php'); // DB Connection

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

$msg = "";

// --- 1. DELETE DOCTOR LOGIC ---
if(isset($_GET['delete_id'])){
    $id = mysqli_real_escape_string($conn, $_GET['delete_id']);
    $res = mysqli_query($conn, "SELECT image FROM veterinarians WHERE id = '$id'");
    if($row = mysqli_fetch_assoc($res)){
        $file_path = "../assets/images/docs/" . $row['image'];
        if(file_exists($file_path) && $row['image'] != ""){ unlink($file_path); }
    }
    mysqli_query($conn, "DELETE FROM veterinarians WHERE id = '$id'");
    header("Location: manage_vet.php?msg=DoctorDeleted");
    exit();
}

// --- 2. ADD DOCTOR LOGIC ---
if(isset($_POST['add_doc'])){
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $spec = mysqli_real_escape_string($conn, $_POST['spec']);
    $exp  = mysqli_real_escape_string($conn, $_POST['exp']);
    $target_dir = "../assets/images/docs/";
    if (!is_dir($target_dir)) { mkdir($target_dir, 0777, true); }
    $img_name = time() . "_" . $_FILES['image']['name'];
    $tmp_name = $_FILES['image']['tmp_name'];

    if(move_uploaded_file($tmp_name, $target_dir . $img_name)){
        $sql = "INSERT INTO veterinarians (name, specialization, experience, image) VALUES ('$name', '$spec', '$exp', '$img_name')";
        if(mysqli_query($conn, $sql)){ 
            $msg = "Success: Professional profile added."; 
        }
    }
}

include('includes/header.php');
 
?>

<style>
    /* Professional Center-Aligned Layout */
    .dashboard-container {
        max-width: 1550px;
        margin: 0 auto;
        padding: 40px;
    }

    .page-head {
        margin-bottom: 50px;
        padding-left: 10px;
    }

    .page-head h1 { font-size: 2.2rem; font-weight: 800; color: #1e293b; letter-spacing: -0.5px; }
    .page-head p { color: #64748b; font-size: 1rem; margin-top: 5px; }

    .manage-layout {
        display: grid;
        grid-template-columns: 400px 1fr;
        gap: 50px;
        align-items: start;
    }

    /* Spacious Form Card */
    .form-sidebar {
        background: #ffffff;
        border-radius: 24px;
        padding: 40px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 10px 30px rgba(0,0,0,0.02);
        position: sticky;
        top: 110px;
    }

    .form-sidebar h3 { font-size: 1.3rem; font-weight: 800; color: #1e293b; margin-bottom: 30px; display: flex; align-items: center; gap: 12px; }
    .form-sidebar h3 i { color: #518992; }

    .form-group { margin-bottom: 25px; }
    .form-label { display: block; font-size: 0.75rem; font-weight: 800; color: #94a3b8; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 12px; }
    
    .form-input-premium { 
        width: 100%; padding: 16px; border-radius: 14px; border: 1.5px solid #edf2f7; 
        background: #f8fafc; font-size: 0.95rem; color: #1e293b; transition: 0.3s ease;
    }
    .form-input-premium:focus { border-color: #518992; background: #fff; outline: none; box-shadow: 0 0 0 4px rgba(81, 137, 146, 0.1); }

    .btn-save-vet {
        width: 100%; background: #518992; color: white; border: none; padding: 18px; border-radius: 14px;
        font-weight: 800; font-size: 1rem; cursor: pointer; transition: 0.3s; margin-top: 10px;
    }
    .btn-save-vet:hover { transform: translateY(-3px); box-shadow: 0 10px 25px rgba(81, 137, 146, 0.2); }

    /* --- LINEAR DATA GRID (TABLE REPLACEMENT) --- */
    .grid-header {
        display: grid;
        grid-template-columns: 2.5fr 1.8fr 1fr 130px;
        padding: 0 40px 15px;
        font-size: 0.75rem;
        font-weight: 800;
        color: #94a3b8;
        text-transform: uppercase;
        letter-spacing: 1.5px;
    }

    .doctor-row-card {
        background: #ffffff;
        border-radius: 20px;
        border: 1px solid #f1f5f9;
        padding: 22px 40px;
        margin-bottom: 18px;
        display: grid;
        grid-template-columns: 2.5fr 1.8fr 1fr 130px; /* Perfectly Aligned Columns */
        align-items: center;
        transition: 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .doctor-row-card:hover {
        transform: scale(1.01);
        border-color: #518992;
        box-shadow: 0 15px 40px rgba(0,0,0,0.04);
        background: #fcfdfe;
    }

    /* Identity Section */
    .identity-box { display: flex; align-items: center; gap: 20px; }
    .identity-img {
        width: 65px;
        height: 65px;
        border-radius: 16px;
        overflow: hidden;
        border: 3px solid #fff;
        box-shadow: 0 5px 15px rgba(0,0,0,0.08);
    }
    .identity-img img { width: 100%; height: 100%; object-fit: cover; }

    .identity-text b { display: block; font-size: 1.15rem; color: #1e293b; margin-bottom: 3px; }
    .identity-text span { font-size: 0.85rem; color: #94a3b8; font-weight: 700; }

    /* Expertise Column (No Circles) */
    .expertise-badge {
        font-size: 0.95rem;
        font-weight: 700;
        color: #518992;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .expertise-badge i { opacity: 0.6; font-size: 0.85rem; }

    .experience-label {
        color: #475569;
        font-size: 1rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    /* Linear Action Buttons */
    .action-button-line { display: flex; justify-content: flex-end; gap: 12px; }
    .btn-ui-action {
        width: 45px;
        height: 45px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        transition: 0.3s;
        font-size: 1.1rem;
    }
    .edit-btn { background: #f0f9ff; color: #0ea5e9; border: 1px solid #e0f2fe; }
    .edit-btn:hover { background: #0ea5e9; color: #fff; transform: translateY(-3px); }
    
    .del-btn { background: #fff1f2; color: #f43f5e; border: 1px solid #ffe4e6; }
    .del-btn:hover { background: #f43f5e; color: #fff; transform: translateY(-3px); }

    @media (max-width: 1250px) {
        .manage-layout { grid-template-columns: 1fr; }
        .form-sidebar { position: static; margin-bottom: 40px; }
    }
</style>

<main class="dashboard-container">
    <div class="page-head">
        <h1>Veterinary Specialist Directory</h1>
        <p>A comprehensive overview of active specialists and medical staff members.</p>
    </div>

    <div class="manage-layout">
        <div class="form-sidebar">
            <h3><i class="fas fa-user-plus"></i> Add Specialist</h3>
            
            <?php if($msg): ?>
                <div style="background: #f0fdf4; color: #166534; padding: 18px; border-radius: 12px; margin-bottom: 25px; font-weight: 700; border: 1.5px solid #bbf7d0; text-align:center;">
                    <i class="fas fa-check-circle"></i> <?php echo $msg; ?>
                </div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label class="form-label">Specialist Name</label>
                    <input type="text" name="name" class="form-input-premium" placeholder="e.g. Dr. Tanvi Shah" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Medical Specialization</label>
                    <input type="text" name="spec" class="form-input-premium" placeholder="e.g. Soft Tissue Surgery" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Experience</label>
                    <input type="text" name="exp" class="form-input-premium" placeholder="e.g. 12+ Years" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Profile Photo</label>
                    <input type="file" name="image" class="form-input-premium" style="padding: 12px;" required>
                </div>
                
                <button type="submit" name="add_doc" class="btn-save-vet">Add to Directory</button>
            </form>
        </div>

        <div class="directory-list">
            <div class="grid-header">
                <div>Specialist Detail</div>
                <div>Medical Expertise</div>
                <div>Exp Level</div>
                <div style="text-align: right;">Quick Actions</div>
            </div>

            <?php 
            $docs = mysqli_query($conn, "SELECT * FROM veterinarians ORDER BY id DESC");
            if(mysqli_num_rows($docs) > 0):
                while($d = mysqli_fetch_assoc($docs)):
            ?>
            <div class="doctor-row-card">
                <div class="identity-box">
                    <div class="identity-img">
                        <img src="../assets/images/docs/<?php echo $d['image']; ?>" onerror="this.src='../assets/images/default-vet.png'">
                    </div>
                    <div class="identity-text">
                        <b><?php echo $d['name']; ?></b>
                        <span>REG ID: #<?php echo $d['id']; ?></span>
                    </div>
                </div>

                <div class="expertise-badge">
                    <i class="fas fa-stethoscope"></i>
                    <?php echo $d['specialization']; ?>
                </div>

                <div class="experience-label">
                    <i class="fas fa-award"></i> <?php echo $d['experience']; ?>
                </div>

                <div class="action-button-line">
                    <a href="edit_vet.php?id=<?php echo $d['id']; ?>" class="btn-ui-action edit-btn" title="Modify Record">
                        <i class="fas fa-user-edit"></i>
                    </a>
                    <a href="manage_vet.php?delete_id=<?php echo $d['id']; ?>" 
                       class="btn-ui-action del-btn" 
                       title="Remove Record"
                       onclick="return confirm('Confirm permanent removal of Dr. <?php echo addslashes($d['name']); ?>?')">
                        <i class="fas fa-user-minus"></i>
                    </a>
                </div>
            </div>
            <?php endwhile; else: ?>
                <div class="form-sidebar" style="text-align:center; padding: 100px;">
                    <p style="color: #94a3b8; font-weight: 700;">No specialist records currently active.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</main>

<?php include('includes/footer.php'); ?>