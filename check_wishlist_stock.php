<?php
session_start();
include "db.php";

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['email'])) {
    echo json_encode(['status' => 'not_logged_in', 'restocked_items' => []]);
    exit();
}

$user_email = mysqli_real_escape_string($conn, $_SESSION['email']);

// Get wishlist items that are now in stock
// We check products that:
// 1. Are in user's wishlist
// 2. Currently have stock > 0
// 3. User hasn't been notified about recently (within last 24 hours)

$sql = "SELECT DISTINCT p.id, p.name, p.base_image, v.stock_qty, v.price
        FROM wishlist w
        INNER JOIN products p ON w.product_id = p.id
        INNER JOIN product_variants v ON p.id = v.product_id
        WHERE w.user_email = '$user_email'
        AND v.stock_qty > 0
        AND NOT EXISTS (
            SELECT 1 FROM stock_notifications sn
            WHERE sn.user_email = '$user_email'
            AND sn.product_id = p.id
            AND sn.notified_at > DATE_SUB(NOW(), INTERVAL 24 HOUR)
        )
        LIMIT 5";

$result = mysqli_query($conn, $sql);
$restocked_items = [];

if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $restocked_items[] = $row;
        
        // Mark as notified
        $product_id = $row['id'];
        mysqli_query($conn, "INSERT INTO stock_notifications (user_email, product_id) 
                            VALUES ('$user_email', $product_id)
                            ON DUPLICATE KEY UPDATE notified_at = NOW()");
    }
}

echo json_encode([
    'status' => 'success',
    'restocked_items' => $restocked_items,
    'count' => count($restocked_items)
]);
?>
