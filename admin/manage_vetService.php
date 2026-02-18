<?php
session_start();
require_once('../db.php'); // DB Connection

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

$msg = "";

// --- 1. DELETE LOGIC ---
if(isset($_GET['delete_id'])){
    $id = mysqli_real_escape_string($conn, $_GET['delete_id']);
    if(mysqli_query($conn, "DELETE FROM vet_services WHERE id = '$id'")){
        header("Location: manage_vetService.php?msg=Deleted");
        exit();
    }
}

// --- 2. ADD/UPDATE LOGIC ---
if(isset($_POST['save_service'])){
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $desc  = mysqli_real_escape_string($conn, $_POST['description']);
    $icon  = mysqli_real_escape_string($conn, $_POST['icon']);
    $s_id  = mysqli_real_escape_string($conn, $_POST['service_id']);

    if(!empty($s_id)){
        $sql = "UPDATE vet_services SET title='$title', description='$desc', icon='$icon' WHERE id='$s_id'";
    } else {
        $sql = "INSERT INTO vet_services (title, description, icon) VALUES ('$title', '$desc', '$icon')";
    }
    if(mysqli_query($conn, $sql)){
        $msg = "<div class='alert-proper success'><i class='fas fa-check-circle'></i> Database Updated Successfully!</div>";
    }
}

// --- 3. EDIT DATA FETCH ---
$edit = ['id'=>'','title'=>'','description'=>'','icon'=>''];
if(isset($_GET['edit_id'])){
    $res = mysqli_query($conn, "SELECT * FROM vet_services WHERE id = '".mysqli_real_escape_string($conn, $_GET['edit_id'])."'");
    if($row = mysqli_fetch_assoc($res)){ $edit = $row; }
}

include('includes/header.php'); 
?>

<style>
    /* --- CENTERED MASTER LAYOUT --- */
    body { background-color: #f8fafc; color: #1e293b; overflow-x: hidden; }
    
    /* Centers the entire content on the page */
    .master-wrapper { 
        max-width: 1480px; 
        margin: 0 auto; 
        padding: 40px 20px;
    }

    .header-box { text-align: center; margin-bottom: 50px; }
    .header-box h1 { font-size: 2.5rem; font-weight: 800; color: #0f172a; letter-spacing: -1px; }

    /* Responsive Grid */
    .layout-grid { 
        display: grid; 
        grid-template-columns: 380px minmax(0, 1fr); 
        gap: 40px; 
        align-items: start; 
    }

    /* --- SIDEBAR FORM --- */
    .sticky-form-card { 
        background: #fff; border-radius: 20px; padding: 35px; 
        border: 2px solid #e2e8f0; position: sticky; top: 100px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.02);
    }
    .sticky-form-card h3 { font-size: 1.3rem; margin-bottom: 25px; color: #334155; display: flex; align-items: center; gap: 10px; }
    
    .form-group { margin-bottom: 20px; text-align: left; }
    .form-label { display: block; font-size: 0.75rem; font-weight: 800; color: #64748b; text-transform: uppercase; margin-bottom: 8px; }
    .input-proper { width: 100%; padding: 14px; border-radius: 10px; border: 2px solid #edf2f7; background: #f8fafc; font-size: 0.95rem; box-sizing: border-box; }
    .input-proper:focus { border-color: #518992; background: #fff; outline: none; box-shadow: 0 0 0 4px rgba(81, 137, 146, 0.1); }

    .btn-update-db { width: 100%; background: #518992; color: white; border: none; padding: 18px; border-radius: 12px; font-weight: 800; cursor: pointer; transition: 0.3s; text-transform: uppercase; }

    /* --- FULL-WIDTH TABLE WITH PROPER BORDERS --- */
    .table-outer-card { 
        background: #fff; 
        border-radius: 16px; 
        border: 2px solid #e2e8f0; /* Darker Outer Border */
        overflow: hidden; 
        box-shadow: 0 10px 30px rgba(0,0,0,0.02); 
    }
    
    .service-table { width: 100%; border-collapse: collapse; table-layout: fixed; }
    
    .service-table thead { background: #f8fafc; }
    .service-table th { 
        padding: 20px; 
        font-size: 0.7rem; 
        font-weight: 800; 
        color: #64748b; 
        text-transform: uppercase; 
        border-bottom: 2px solid #e2e8f0; 
        border-right: 1px solid #e2e8f0; /* Column separator */
        text-align: left;
    }
    
    .service-table td { 
        padding: 20px; 
        border-bottom: 1px solid #e2e8f0; /* Row separator */
        border-right: 1px solid #e2e8f0; 
        vertical-align: middle;
        overflow: hidden;
    }
    .service-table td:last-child, .service-table th:last-child { border-right: none; }

    /* Strict 2-Line Limit with Word Break */
    .desc-truncate { 
        font-size: 0.9rem; 
        color: #475569; 
        line-height: 1.5; 
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-overflow: ellipsis;
        word-break: break-all; /* Fixes "AAAAAAA" text from breaking layout */
    }

    .icon-box { width: 45px; height: 45px; background: rgba(81, 137, 146, 0.1); border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.3rem; color: #518992; border: 1px solid rgba(81, 137, 146, 0.2); }
    .title-box b { display: block; font-size: 0.95rem; color: #0f172a; margin-bottom: 2px; }
    .title-box span { font-size: 0.75rem; color: #94a3b8; font-weight: 700; }

    .action-group { display: flex; gap: 8px; justify-content: center; }
    .btn-t { width: 36px; height: 36px; border-radius: 8px; display: flex; align-items: center; justify-content: center; text-decoration: none; transition: 0.3s; }
    .edit-t { background: #f0f9ff; color: #0ea5e9; border: 1px solid #e0f2fe; }
    .del-t { background: #fff1f2; color: #f43f5e; border: 1px solid #ffe4e6; }
    .btn-t:hover { transform: scale(1.1); background: #000; color: #fff; }

    .alert-proper { padding: 18px; border-radius: 12px; margin-bottom: 30px; font-weight: 700; border: 1px solid; text-align: center; }
    .alert-proper.success { background: #f0fdf4; color: #166534; border-color: #bbf7d0; }

    @media (max-width: 1100px) { .layout-grid { grid-template-columns: 1fr; } }
</style>

<main class="master-wrapper">
    <div class="header-box">
        <h1>Medical Services Directory</h1>
        <p>Professional management of active clinical service categories.</p>
    </div>

    <div class="layout-grid">
        <div class="sticky-form-card">
            <h3><i class="fas fa-edit"></i> <?php echo (!empty($edit['id'])) ? "Edit Record" : "Add Service"; ?></h3>
            <form method="POST">
                <input type="hidden" name="service_id" value="<?php echo $edit['id']; ?>">
                
                <div class="form-group">
                    <label class="form-label">Service Title</label>
                    <input type="text" name="title" class="input-proper" value="<?php echo $edit['title']; ?>" placeholder="Surgery" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Icon Class (FontAwesome)</label>
                    <input type="text" name="icon" class="input-proper" value="<?php echo $edit['icon']; ?>" placeholder="fas fa-pills" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Service Summary</label>
                    <textarea name="description" class="input-proper" rows="5" required><?php echo $edit['description']; ?></textarea>
                </div>

                <button type="submit" name="save_service" class="btn-update-db">Confirm & Save</button>
                <?php if(!empty($edit['id'])): ?>
                    <a href="manage_vetService.php" style="display:block; text-align:center; margin-top:15px; color:#64748b; text-decoration:none; font-size:0.8rem; font-weight:700;">Discard Edits</a>
                <?php endif; ?>
            </form>
        </div>

        <div class="listing-container">
            <?php echo $msg; ?>
            
            <div class="table-outer-card">
                <table class="service-table">
                    <thead>
                        <tr>
                            <th style="width: 70px;">Icon</th>
                            <th style="width: 180px;">Service Detail</th>
                            <th>Description (2-Line Limit)</th>
                            <th style="width: 110px; text-align: center;">Control</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $srv = mysqli_query($conn, "SELECT * FROM vet_services ORDER BY id DESC");
                        while($row = mysqli_fetch_assoc($srv)):
                        ?>
                        <tr>
                            <td align="center">
                                <div class="icon-box"><i class="<?php echo $row['icon']; ?>"></i></div>
                            </td>
                            <td>
                                <div class="title-box">
                                    <b><?php echo $row['title']; ?></b>
                                    <span>Ref: #SV-0<?php echo $row['id']; ?></span>
                                </div>
                            </td>
                            <td>
                                <div class="desc-truncate"><?php echo $row['description']; ?></div>
                            </td>
                            <td>
                                <div class="action-group">
                                    <a href="manage_vetService.php?edit_id=<?php echo $row['id']; ?>" class="btn-t edit-t" title="Edit">
                                        <i class="fas fa-pencil-alt"></i>
                                    </a>
                                    <a href="manage_vetService.php?delete_id=<?php echo $row['id']; ?>" class="btn-t del-t" 
                                       onclick="return confirm('Permanently remove this medical service?')" title="Delete">
                                        <i class="fas fa-minus"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<?php include('includes/footer.php'); ?>