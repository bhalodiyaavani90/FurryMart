<?php
include "db.php";
if (session_status() === PHP_SESSION_NONE) { session_start(); }

header('Content-Type: application/json');

// CHANGED: We now check for 'email' to match your login.php
if (!isset($_SESSION['email'])) {
    echo json_encode(['status' => 'not_logged_in']);
    exit;
}

$user_email = $_SESSION['email']; // Map the session to our variable
$product_id = isset($_GET['product_id']) ? intval($_GET['product_id']) : 0;

if ($product_id > 0) {
    // Check if it exists in the database
    $check_query = "SELECT id FROM wishlist WHERE user_email = '$user_email' AND product_id = $product_id";
    $check_res = mysqli_query($conn, $check_query);

    if (mysqli_num_rows($check_res) > 0) {
        // REMOVE logic
        mysqli_query($conn, "DELETE FROM wishlist WHERE user_email = '$user_email' AND product_id = $product_id");
        echo json_encode(['status' => 'removed']);
    } else {
        // ADD logic
        mysqli_query($conn, "INSERT INTO wishlist (user_email, product_id) VALUES ('$user_email', $product_id)");
        echo json_encode(['status' => 'added']);
    }
}
?>