<?php
include "db.php";
if (session_status() === PHP_SESSION_NONE) { session_start(); }

$wish_count = 0;
if (isset($_SESSION['email'])) {
    $email = $_SESSION['email'];
    $res = mysqli_query($conn, "SELECT COUNT(*) as total FROM wishlist WHERE user_email = '$email'");
    $data = mysqli_fetch_assoc($res);
    $wish_count = $data['total'];
}

echo json_encode(['wishlist' => $wish_count]);
?>