<?php
session_start();
include "../db.php";
if(!isset($_SESSION['admin_id'])) { header("Location: login.php"); exit(); }

$id = mysqli_real_escape_string($conn, $_GET['id']);
$msg = "";

// 1. FETCH CURRENT DATA
$res = mysqli_query($conn, "SELECT * FROM veterinarians WHERE id = '$id'");
$row = mysqli_fetch_assoc($res);

// 2. UPDATE LOGIC
if(isset($_POST['update_doc'])){
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $spec = mysqli_real_escape_string($conn, $_POST['spec']);
    $exp  = mysqli_real_escape_string($conn, $_POST['exp']);
    
    $image_query = "";
    
    // Handle Image Upload if a new file is selected
    if($_FILES['image']['name'] != ""){
        $target_dir = "../assets/images/docs/";
        
        // Ensure directory exists
        if (!is_dir($target_dir)) { mkdir($target_dir, 0777, true); }

        $img_name = time() . "_" . $_FILES['image']['name'];
        $tmp_name = $_FILES['image']['tmp_name'];
        $destination = $target_dir . $img_name;
        
        if(move_uploaded_file($tmp_name, $destination)){
            // Delete the old physical file to save space
            if($row['image'] != "" && file_exists($target_dir . $row['image'])){
                unlink($target_dir . $row['image']);
            }
            $image_query = ", image='$img_name'";
        }
    }

    $sql = "UPDATE veterinarians SET name='$name', specialization='$spec', experience='$exp' $image_query WHERE id = '$id'";
    
    if(mysqli_query($conn, $sql)){
        echo "<script>alert('Profile Updated Successfully!'); window.location='manage_vet.php';</script>";
        exit();
    }
}

include "includes/header.php";
?>

<style>
    :root { --primary: #00bcd4; --text-dark: #1e293b; --radius: 12px; }
    
    .edit-wrapper { 
        padding: 50px 20px; 
        display: flex; 
        justify-content: center; 
        background: #f8fafc; 
        min-height: 85vh; 
    }
    
    .edit-card { 
        background: white; 
        padding: 35px; 
        border-radius: var(--radius); 
        box-shadow: 0 15px 35px rgba(0,0,0,0.1); 
        width: 100%; 
        max-width: 550px; 
        border-top: 6px solid var(--primary);
    }

    .edit-card h3 { margin-top: 0; color: var(--text-dark); border-bottom: 1px solid #eee; padding-bottom: 15px; margin-bottom: 25px; }

    .form-group { margin-bottom: 20px; }
    .form-group label { display: block; font-size: 14px; font-weight: 600; margin-bottom: 8px; color: #64748b; }
    
    .form-control { 
        width: 100%; padding: 12px 15px; border: 1px solid #e2e8f0; border-radius: 8px; 
        box-sizing: border-box; transition: 0.3s; font-size: 15px;
    }
    .form-control:focus { border-color: var(--primary); outline: none; box-shadow: 0 0 0 3px rgba(0,188,212,0.1); }

    .img-preview-box { 
        display: flex; 
        align-items: center; 
        gap: 20px; 
        background: #f1f5f9; 
        padding: 15px; 
        border-radius: 10px; 
        margin-bottom: 10px; 
    }
    .current-img { width: 80px; height: 80px; border-radius: 50%; object-fit: cover; border: 3px solid var(--primary); }

    .btn-container { display: flex; gap: 15px; margin-top: 30px; }
    
    .btn-update { 
        flex: 2; background: var(--primary); color: white; padding: 15px; 
        border: none; border-radius: 8px; font-weight: 700; cursor: pointer; transition: 0.3s;
    }
    .btn-update:hover { background: #00acc1; transform: translateY(-2px); }

    .btn-cancel { 
        flex: 1; background: #f1f5f9; color: #64748b; padding: 15px; 
        border: none; border-radius: 8px; font-weight: 700; text-decoration: none; text-align: center; transition: 0.3s;
    }
    .btn-cancel:hover { background: #e2e8f0; }
</style>

<main class="dashboard-content">
    <div class="edit-wrapper">
        <div class="edit-card">
            <h3>Update Doctor Profile</h3>
            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label>Doctor Full Name</label>
                    <input type="text" name="name" class="form-control" value="<?php echo $row['name']; ?>" required>
                </div>
                
                <div class="form-group">
                    <label>Specialization / Degree</label>
                    <input type="text" name="spec" class="form-control" value="<?php echo $row['specialization']; ?>" required>
                </div>
                
                <div class="form-group">
                    <label>Experience</label>
                    <input type="text" name="exp" class="form-control" value="<?php echo $row['experience']; ?>" required>
                </div>
                
                <div class="form-group">
                    <label>Profile Image</label>
                    <div class="img-preview-box">
                        <img src="../assets/images/docs/<?php echo $row['image']; ?>" class="current-img">
                        <span style="font-size: 13px; color: #64748b;">Current Profile Photo</span>
                    </div>
                    <input type="file" name="image" class="form-control">
                    <small style="color: #94a3b8; display:block; margin-top:5px;">Leave empty to keep current photo</small>
                </div>

                <div class="btn-container">
                    <a href="manage_vet.php" class="btn-cancel">Cancel</a>
                    <button type="submit" name="update_doc" class="btn-update">Save Changes</button>
                </div>