<?php
session_start();
require_once('../db.php');

// 1. Security Check
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// 2. Fetch User Data
if (isset($_GET['id'])) {
    $id = mysqli_real_escape_string($conn, $_GET['id']);
    $user_query = mysqli_query($conn, "SELECT * FROM users WHERE id = '$id'");
    $user = mysqli_fetch_assoc($user_query);

    if (!$user) {
        die("User not found.");
    }
} else {
    header("Location: admin_users.php");
    exit();
}

// 3. Update Logic
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize all editable fields
    $f_name  = mysqli_real_escape_string($conn, $_POST['first_name']);
    $m_name  = mysqli_real_escape_string($conn, $_POST['middle_name']);
    $l_name  = mysqli_real_escape_string($conn, $_POST['last_name']);
    $mobile  = mysqli_real_escape_string($conn, $_POST['mobile_number']);
    $age     = mysqli_real_escape_string($conn, $_POST['age']);
    $caste   = mysqli_real_escape_string($conn, $_POST['caste']);
    $pets    = mysqli_real_escape_string($conn, $_POST['pets']);
    $city    = mysqli_real_escape_string($conn, $_POST['city']);
    $state   = mysqli_real_escape_string($conn, $_POST['state']);
    $zip     = mysqli_real_escape_string($conn, $_POST['zip']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);

    // Handle Password Update (Only change if a new password is typed)
    if (!empty($_POST['password'])) {
        $new_pass = password_hash($_POST['password'], PASSWORD_BCRYPT);
        $pass_update = ", password='$new_pass'";
    } else {
        $pass_update = "";
    }

    $sql = "UPDATE users SET 
            first_name='$f_name', 
            middle_name='$m_name', 
            last_name='$l_name', 
            mobile_number='$mobile', 
            age='$age', 
            caste='$caste', 
            pets='$pets', 
            city='$city', 
            state='$state', 
            zip='$zip', 
            address='$address' 
            $pass_update 
            WHERE id='$id'";

    if (mysqli_query($conn, $sql)) {
        header("Location: admin_users.php?msg=UserUpdated");
        exit();
    } else {
        $error = "Update failed: " . mysqli_error($conn);
    }
}

include('includes/header.php');
?>

<style>
    :root {
        --primary: #00bcd4;
        --text-dark: #1e293b;
        --text-muted: #64748b;
        --radius: 12px;
    }

    .form-card {
        background: #fff;
        padding: 40px;
        border-radius: 16px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.05);
        max-width: 900px;
        margin: 20px auto;
        border-top: 6px solid var(--primary);
    }

    .form-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 25px;
    }

    .section-title {
        grid-column: span 2;
        margin: 30px 0 10px 0;
        padding-bottom: 10px;
        border-bottom: 1px solid #f1f5f9;
        color: var(--primary);
        font-size: 1rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .form-group { display: flex; flex-direction: column; gap: 8px; }
    .full-width { grid-column: span 2; }
    
    label { font-size: 0.85rem; font-weight: 600; color: var(--text-dark); }

    input, select, textarea {
        width: 100%;
        height: 50px;
        padding: 0 18px;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        font-size: 0.95rem;
        background-color: #fcfcfc;
        transition: all 0.3s ease;
        box-sizing: border-box;
    }

    textarea { height: auto; padding: 15px 18px; }

    input:focus, select:focus, textarea:focus {
        outline: none;
        border-color: var(--primary);
        background-color: #fff;
        box-shadow: 0 0 0 4px rgba(0, 188, 212, 0.1);
    }

    /* Styling for Read-Only fields */
    input[readonly] { background-color: #f1f5f9; color: #94a3b8; cursor: not-allowed; }

    .btn-update {
        width: 100%;
        height: 55px;
        background: var(--primary);
        color: white;
        border: none;
        border-radius: 10px;
        font-size: 1.1rem;
        font-weight: 700;
        cursor: pointer;
        transition: 0.3s;
        margin-top: 20px;
    }

    .btn-update:hover { background: #00acc1; transform: translateY(-1px); }
</style>



<main class="dashboard-content">
    <div class="form-card">
        <h2 style="margin-bottom: 5px;">Edit Profile: <?php echo $user['first_name'] . " " . $user['last_name']; ?></h2>
        <p style="color: var(--text-muted); margin-bottom: 30px;">Update user information below. Email and DOB are fixed.</p>

        <?php if(isset($error)) echo "<p style='color:red;'>$error</p>"; ?>

        <form method="POST">
            <div class="form-grid">
                
                <div class="section-title">01. Personal Identity</div>
                <div class="form-group">
                    <label>First Name</label>
                    <input type="text" name="first_name" value="<?php echo $user['first_name']; ?>" required>
                </div>
                <div class="form-group">
                    <label>Middle Name</label>
                    <input type="text" name="middle_name" value="<?php echo $user['middle_name']; ?>">
                </div>
                <div class="full-width">
                    <label>Last Name</label>
                    <input type="text" name="last_name" value="<?php echo $user['last_name']; ?>" required>
                </div>

                <div class="section-title">02. Account & Fixed Info</div>
                <div class="form-group">
                    <label>Email Address (Read Only)</label>
                    <input type="email" value="<?php echo $user['email']; ?>" readonly>
                </div>
                <div class="form-group">
                    <label>Date of Birth (Read Only)</label>
                    <input type="text" value="<?php echo $user['dob']; ?>" readonly>
                </div>
                <div class="form-group">
                    <label>Mobile Number</label>
                    <input type="text" name="mobile_number" value="<?php echo $user['mobile_number']; ?>" required>
                </div>
                <div class="form-group">
                    <label>Update Password (Leave blank to keep current)</label>
                    <input type="password" name="password" placeholder="••••••••">
                </div>

                <div class="section-title">03. Profile Details</div>
                <div class="form-group">
                    <label>Age</label>
                    <input type="number" name="age" value="<?php echo $user['age']; ?>" required>
                </div>
                <div class="form-group">
                    <label>Caste / Community</label>
                    <input type="text" name="caste" value="<?php echo $user['caste']; ?>">
                </div>
                <div class="full-width">
                    <label>Pets Owned</label>
                    <select name="pets">
                        <option value="None" <?php if($user['pets'] == 'None') echo 'selected'; ?>>None</option>
                        <option value="Dog" <?php if($user['pets'] == 'Dog') echo 'selected'; ?>>Dog</option>
                        <option value="Cat" <?php if($user['pets'] == 'Cat') echo 'selected'; ?>>Cat</option>
                        <option value="Bird" <?php if($user['pets'] == 'Bird') echo 'selected'; ?>>Bird</option>
                        <option value="Multiple" <?php if($user['pets'] == 'Multiple') echo 'selected'; ?>>Multiple Pets</option>
                    </select>
                </div>

                <div class="section-title">04. Location Details</div>
                <div class="form-group">
                    <label>City</label>
                    <input type="text" name="city" value="<?php echo $user['city']; ?>" required>
                </div>
                <div class="form-group">
                    <label>State</label>
                    <input type="text" name="state" value="<?php echo $user['state']; ?>" required>
                </div>
                <div class="full-width">
                    <label>Zip Code</label>
                    <input type="text" name="zip" value="<?php echo $user['zip']; ?>" required>
                </div>
                <div class="full-width">
                    <label>Full Residential Address</label>
                    <textarea name="address" rows="3" required><?php echo $user['address']; ?></textarea>
                </div>

            </div>

            <button type="submit" class="btn-update">Update User Details</button>
            <a href="admin_users.php" style="display:block; text-align:center; margin-top:20px; color:var(--text-muted); text-decoration:none; font-size:0.9rem;">Cancel and Go Back</a>
        </form>
    </div>
</main>

<?php include('includes/footer.php'); ?>