<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    http_response_code(403);
    die(json_encode(['error' => 'Unauthorized']));
}

include '../db.php';

$order_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($order_id <= 0) {
    http_response_code(400);
    die(json_encode(['error' => 'Invalid order ID']));
}

// Fetch order details
$order_sql = "SELECT * FROM orders WHERE id = $order_id";
$order_result = mysqli_query($conn, $order_sql);

if (!$order_result || mysqli_num_rows($order_result) == 0) {
    http_response_code(404);
    die(json_encode(['error' => 'Order not found']));
}

$order = mysqli_fetch_assoc($order_result);

// Format order date
$order['order_date'] = date('d M Y, h:i A', strtotime($order['order_date']));

// Fetch order items
$items_sql = "SELECT * FROM order_items WHERE order_id = $order_id";
$items_result = mysqli_query($conn, $items_sql);

$items = [];
while ($item = mysqli_fetch_assoc($items_result)) {
    $items[] = $item;
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode([
    'order' => $order,
    'items' => $items
]);
?>
