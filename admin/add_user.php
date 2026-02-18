<?php
session_start();
require_once('../db.php');

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

$msg = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect all fields
    $first_name    = mysqli_real_escape_string($conn, $_POST['first_name']);
    $middle_name   = mysqli_real_escape_string($conn, $_POST['middle_name']);
    $last_name     = mysqli_real_escape_string($conn, $_POST['last_name']);
    $dob           = mysqli_real_escape_string($conn, $_POST['dob']);
    $mobile_number = mysqli_real_escape_string($conn, $_POST['mobile_number']);
    $age           = mysqli_real_escape_string($conn, $_POST['age']);
    $caste         = mysqli_real_escape_string($conn, $_POST['caste']);
    $email         = mysqli_real_escape_string($conn, $_POST['email']);
    
    // FIX: Hash the password before saving
    $plain_password = $_POST['password'];
    $hashed_password = password_hash($plain_password, PASSWORD_BCRYPT);
    
    $pets          = mysqli_real_escape_string($conn, $_POST['pets']);
    $city          = mysqli_real_escape_string($conn, $_POST['city']);
    $state         = mysqli_real_escape_string($conn, $_POST['state']);
    $zip           = mysqli_real_escape_string($conn, $_POST['zip']);
    $address       = mysqli_real_escape_string($conn, $_POST['address']);

    $sql = "INSERT INTO users (first_name, middle_name, last_name, dob, mobile_number, age, caste, email, password, pets, city, state, zip, address) 
            VALUES ('$first_name', '$middle_name', '$last_name', '$dob', '$mobile_number', '$age', '$caste', '$email', '$hashed_password', '$pets', '$city', '$state', '$zip', '$address')";

    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('User Added Successfully with Secure Password!'); window.location.href='admin_users.php';</script>";
    } else {
        $msg = "Error: " . mysqli_error($conn);
    }
}

include('includes/header.php');
?>

<style>
    :root { --primary: #00bcd4; --text-dark: #1e293b; --radius: 12px; }
    .form-card { background: white; padding: 45px; border-radius: 16px; box-shadow: 0 10px 25px rgba(0,0,0,0.05); max-width: 950px; margin: 20px auto; border-top: 6px solid var(--primary); }
    .form-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 25px; }
    .section-title { grid-column: span 2; margin: 35px 0 10px 0; padding-bottom: 12px; border-bottom: 1px solid #f1f5f9; color: var(--primary); font-size: 1.1rem; font-weight: 700; text-transform: uppercase; }
    .form-group { display: flex; flex-direction: column; gap: 8px; }
    .full-width { grid-column: span 2; }
    label { font-size: 0.88rem; font-weight: 600; color: var(--text-dark); }

    /* Professional textbox spacing */
    input, select, textarea { width: 100%; height: 50px; padding: 0 18px; border: 1px solid #e2e8f0; border-radius: var(--radius); font-size: 0.95rem; background-color: #fcfcfc; transition: all 0.3s ease; box-sizing: border-box; }
    textarea { height: auto; padding: 15px 18px; line-height: 1.6; }
    input:focus { outline: none; border-color: var(--primary); background-color: #fff; box-shadow: 0 0 0 4px rgba(0, 188, 212, 0.1); }

    .btn-add { width: 100%; height: 55px; background: var(--primary); color: white; border: none; border-radius: var(--radius); font-size: 1.1rem; font-weight: 700; cursor: pointer; transition: 0.3s; margin-top: 30px; }
</style>

<main class="dashboard-content">
    <div class="form-card">
        <h2>Add New User</h2>
        <form method="POST">
            <div class="form-grid">
                <div class="section-title">01. Personal Identity</div>
                <div class="form-group"><label>First Name</label><input type="text" name="first_name" required></div>
                <div class="form-group"><label>Middle Name</label><input type="text" name="middle_name"></div>
                <div class="full-width"><label>Last Name</label><input type="text" name="last_name" required></div>

                <div class="section-title">02. Personal Details</div>
                <div class="form-group"><label>Date of Birth</label><input type="date" name="dob" required></div>
                <div class="form-group"><label>Age</label><input type="number" name="age" required></div>
                <div class="form-group"><label>Mobile Number</label><input type="text" name="mobile_number" required></div>
                <div class="form-group"><label>Caste</label><input type="text" name="caste"></div>

                <div class="section-title">03. Account & Security</div>
                <div class="form-group"><label>Email Address</label><input type="email" name="email" required></div>
                <div class="form-group"><label>Password</label><input type="password" name="password" required></div>
                <div class="full-width"><label>Pets Owned</label><select name="pets"><option value="None">None</option><option value="Dog">Dog</option><option value="Cat">Cat</option></select></div>

                <div class="section-title">04. Location Details</div>
                <div class="form-group"><label>City</label><input type="text" name="city" required></div>
                <div class="form-group"><label>State</label><input type="text" name="state" required></div>
                <div class="full-width"><label>Zip Code</label><input type="text" name="zip" required></div>
                <div class="full-width"><label>Full Address</label><textarea name="address" rows="3" required></textarea></div>
            </div>
            <button type="submit" class="btn-add">Save Secure User</button>
        </form>
    </div>
</main>
<?php include('includes/footer.php'); ?>