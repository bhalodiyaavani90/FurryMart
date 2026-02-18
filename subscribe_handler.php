<?php
include "db.php";

if(isset($_POST['email'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);

    // Check if already subscribed
    $check = mysqli_query($conn, "SELECT id FROM newsletter_subscribers WHERE email = '$email'");
    if(mysqli_num_rows($check) > 0) {
        echo 'exists';
    } else {
        $sql = "INSERT INTO newsletter_subscribers (email) VALUES ('$email')";
        if(mysqli_query($conn, $sql)) {
            echo 'success';
        } else {
            echo 'error';
        }
    }
}
?>