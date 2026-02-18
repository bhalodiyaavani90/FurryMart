<?php
include "db.php";
if (session_status() === PHP_SESSION_NONE) { session_start(); }

header('Content-Type: application/json');

if(!isset($_GET['id'])) {
    echo json_encode(['success' => false, 'message' => 'Product ID required']);
    exit;
}

$product_id = intval($_GET['id']);

// Fetch product details with primary variant
$sql = "SELECT p.*, v.price, v.mrp, v.weight_size, v.stock_qty, v.id as variant_id 
        FROM products p 
        INNER JOIN product_variants v ON p.id = v.product_id 
        WHERE p.id = $product_id AND p.is_available = 1 
        LIMIT 1";

$result = mysqli_query($conn, $sql);

if(!$result || mysqli_num_rows($result) == 0) {
    echo json_encode(['success' => false, 'message' => 'Product not found']);
    exit;
}

$product = mysqli_fetch_assoc($result);

// Fetch all variants for this product
$variants_sql = "SELECT id, weight_size, price, mrp, stock_qty 
                 FROM product_variants 
                 WHERE product_id = $product_id 
                 ORDER BY price ASC";
$variants_result = mysqli_query($conn, $variants_sql);

$variants = [];
while($variant = mysqli_fetch_assoc($variants_result)) {
    $variants[] = $variant;
}

$product['variants'] = $variants;

echo json_encode([
    'success' => true,
    'product' => $product
]);
?>
