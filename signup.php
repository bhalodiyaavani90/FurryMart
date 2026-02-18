<?php
include "db.php"; 

if(isset($_POST['signup'])){
    // Sanitize all 14 fields
    $fname   = mysqli_real_escape_string($conn, $_POST['first_name']);
    $mname   = mysqli_real_escape_string($conn, $_POST['middle_name']);
    $lname   = mysqli_real_escape_string($conn, $_POST['last_name']);
    $dob     = mysqli_real_escape_string($conn, $_POST['dob']);
    $mobile  = mysqli_real_escape_string($conn, $_POST['mobile_number']);
    $age     = mysqli_real_escape_string($conn, $_POST['age']);
    $caste   = mysqli_real_escape_string($conn, $_POST['caste']);
    $email   = mysqli_real_escape_string($conn, $_POST['email']);
    $pets    = mysqli_real_escape_string($conn, $_POST['pets']);
    $city    = mysqli_real_escape_string($conn, $_POST['city']);
    $state   = mysqli_real_escape_string($conn, $_POST['state']);
    $zip     = mysqli_real_escape_string($conn, $_POST['zip']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    
    // Secure Password Hashing
    $pass = password_hash($_POST['password'], PASSWORD_BCRYPT);

    $query = "INSERT INTO users(first_name, middle_name, last_name, dob, mobile_number, age, caste, pets, address, state, city, zip, email, password) 
              VALUES('$fname', '$mname', '$lname', '$dob', '$mobile', '$age', '$caste', '$pets', '$address', '$state', '$city', '$zip', '$email', '$pass')";

    if(mysqli_query($conn, $query)){
        header("Location: login.php?msg=Account created successfully!");
        exit();
    } else {
        $error = "Registration failed: " . mysqli_error($conn);
    }
}
?>

<?php include "includes/header.php"; ?>

<style>
    :root {
        --primary: #518992ff;
        --text-dark: #1e293b;
        --text-muted: #64748b;
        --bg-light: #f8fafc;
        --white: #ffffff;
        --radius: 10px;
        --shadow: 0 10px 25px rgba(0,0,0,0.05);
    }

    body { background-color: var(--bg-light); font-family: 'Segoe UI', sans-serif; }

    .signup-wrapper { padding: 50px 20px; }

    .form-card {
        background: var(--white);
        padding: 45px; /* Increased internal card padding */
        border-radius: 16px;
        box-shadow: var(--shadow);
        max-width: 950px; /* Slightly wider for better 2-column balance */
        margin: 0 auto;
        border-top: 6px solid var(--primary);
    }

    .form-header { text-align: center; margin-bottom: 40px; }
    .form-header h2 { color: var(--text-dark); font-size: 30px; font-weight: 700; margin-bottom: 8px; }

    /* GRID SPACING: Fixed 25px gap between all modules */
    .form-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 25px; 
    }

    /* SECTION TITLE SPACING: Distinct block separation */
    .section-title {
        grid-column: span 2;
        margin: 35px 0 10px 0; /* More top margin to separate blocks */
        padding-bottom: 12px;
        border-bottom: 1px solid #f1f5f9;
        color: var(--primary);
        font-size: 1.1rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .full-width { grid-column: span 2; }

    /* LABEL SPACING: Gap between text and textbox */
    .form-group { 
        display: flex; 
        flex-direction: column; 
        gap: 8px; 
    }

    label {
        font-size: 0.88rem;
        font-weight: 600;
        color: var(--text-dark);
        padding-left: 2px;
    }

    /* INTERNAL TEXTBOX SPACING: Symmetrical height and padding */
    input, select, textarea {
        width: 100%;
        height: 50px; /* Fixed height for symmetry across all types */
        padding: 0 18px; /* Internal horizontal breathing room */
        border: 1px solid #e2e8f0;
        border-radius: var(--radius);
        font-size: 0.95rem;
        background-color: #fcfcfc;
        transition: all 0.3s ease;
        box-sizing: border-box;
    }

    /* Textarea adjustment for multiline */
    textarea {
        height: auto;
        padding: 15px 18px; 
        line-height: 1.6;
    }

    input:focus, select:focus, textarea:focus {
        outline: none;
        border-color: var(--primary);
        background-color: #fff;
        box-shadow: 0 0 0 4px rgba(0, 188, 212, 0.1);
    }

    .signup-btn {
        width: 100%;
        height: 55px; /* Taller, professional button */
        background: var(--primary);
        color: white;
        border: none;
        border-radius: var(--radius);
        font-size: 1.1rem;
        font-weight: 700;
        cursor: pointer;
        transition: 0.3s;
        margin-top: 30px;
        letter-spacing: 0.5px;
    }

    .signup-btn:hover { background: #00acc1; transform: translateY(-1px); box-shadow: 0 5px 15px rgba(0, 188, 212, 0.3); }

    .login-link { text-align: center; margin-top: 25px; color: var(--text-muted); font-size: 14px; }
    .login-link a { color: var(--primary); text-decoration: none; font-weight: 700; }

    @media (max-width: 768px) {
        .form-grid { grid-template-columns: 1fr; }
        .section-title, .full-width { grid-column: span 1; }
        .form-card { padding: 30px 20px; }
    }
</style>

<div class="signup-wrapper">
    <div class="form-card">
        <div class="form-header">
            <h2>Join FurryMart Family 🐾</h2>
            <p>Enter your details below to create your professional profile.</p>
        </div>

        <?php if(isset($error)): ?>
            <div style="background: #fef2f2; color: #991b1b; padding: 15px; border-radius: 8px; margin-bottom: 25px; text-align: center; font-size: 14px; border: 1px solid #fee2e2;">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form action="signup.php" method="POST">
            <div class="form-grid">
                
                <div class="section-title">01. Personal Identity</div>
                <div class="form-group">
                    <label>First Name</label>
                    <input type="text" name="first_name" placeholder="Required" required>
                </div>
                <div class="form-group">
                    <label>Middle Name</label>
                    <input type="text" name="middle_name" placeholder="Optional">
                </div>
                <div class="full-width">
                    <label>Last Name</label>
                    <input type="text" name="last_name" placeholder="Required" required>
                </div>

                <div class="section-title">02. Personal Details</div>
                <div class="form-group">
                    <label>Date of Birth</label>
                    <input type="date" name="dob" required>
                </div>
                <div class="form-group">
                    <label>Age</label>
                    <input type="number" name="age" placeholder="e.g. 24" required>
                </div>
                <div class="form-group">
                    <label>Mobile Number</label>
                    <input type="text" name="mobile_number" placeholder="10-digit number" required>
                </div>
                <div class="form-group">
                    <label>Caste / Community</label>
                    <input type="text" name="caste" placeholder="Optional">
                </div>

                <div class="section-title">03. Account & Security</div>
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="email" placeholder="name@example.com" required>
                </div>
                <div class="form-group">
                    <label>Create Password</label>
                    <input type="password" name="password" placeholder="Min. 8 characters" required>
                </div>
                <div class="full-width">
                    <label>Pets Currently Owned</label>
                    <select name="pets">
                        <option value="None">I don't have a pet</option>
                        <option value="Dog">Dog</option>
                        <option value="Cat">Cat</option>
                        <option value="Bird">Bird</option>
                        <option value="Multiple">Multiple Pets</option>
                    </select>
                </div>

                <div class="section-title">04. Location Details</div>
                <div class="form-group">
                    <label>City</label>
                    <input type="text" name="city" required>
                </div>
                <div class="form-group">
                    <label>State</label>
                    <input type="text" name="state" required>
                </div>
                <div class="full-width">
                    <label>Postal / Zip Code</label>
                    <input type="text" name="zip" required>
                </div>
                <div class="full-width">
                    <label>Full Residential Address</label>
                    <textarea name="address" rows="3" placeholder="Apartment, Street, Landmark, etc." required></textarea>
                </div>

            </div>

            <button type="submit" name="signup" class="signup-btn">Complete Registration</button>
        </form>

        <div class="login-link">
            Already have an account? <a href="login.php">Log in here</a>
        </div>
    </div>
</div>

<?php include "includes/footer.php"; ?>