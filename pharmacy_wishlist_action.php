<?php
include "db.php";
if (session_status() === PHP_SESSION_NONE) { session_start(); }

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_email'])) {
    echo json_encode(['status' => 'not_logged_in', 'message' => 'Please login to add items to wishlist']);
    exit;
}

$user_email = mysqli_real_escape_string($conn, $_SESSION['user_email']);
$product_id = intval($_GET['product_id']);

// Check if product exists in wishlist
$check = mysqli_query($conn, "SELECT id FROM wishlist WHERE user_email = '$user_email' AND product_id = $product_id");

if (mysqli_num_rows($check) > 0) {
    // Remove from wishlist
    mysqli_query($conn, "DELETE FROM wishlist WHERE user_email = '$user_email' AND product_id = $product_id");
    echo json_encode(['status' => 'removed', 'message' => 'Removed from wishlist']);
} else {
    // Add to wishlist
    $insert = mysqli_query($conn, "INSERT INTO wishlist (user_email, product_id, added_date) VALUES ('$user_email', $product_id, NOW())");
    
    if ($insert) {
        echo json_encode(['status' => 'added', 'message' => 'Added to wishlist']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to add to wishlist']);
    }
}
exit;
?>
