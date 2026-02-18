<?php
include "db.php";

header('Content-Type: application/json');

$query = isset($_GET['query']) ? mysqli_real_escape_string($conn, $_GET['query']) : '';

if(strlen($query) < 2) {
    echo json_encode([]);
    exit;
}

$results = [];

// Search in regular products
$product_sql = "SELECT p.id, p.name, p.brand_name, p.base_image, v.price, 'product' as type
                FROM products p 
                INNER JOIN product_variants v ON p.id = v.product_id
                WHERE p.is_available = 1 
                AND (p.name LIKE '%$query%' 
                     OR p.brand_name LIKE '%$query%' 
                     OR p.description LIKE '%$query%')
                GROUP BY p.id
                ORDER BY 
                    CASE 
                        WHEN p.name LIKE '$query%' THEN 1
                        WHEN p.name LIKE '%$query%' THEN 2
                        WHEN p.brand_name LIKE '$query%' THEN 3
                        ELSE 4
                    END
                LIMIT 8";

$product_result = mysqli_query($conn, $product_sql);
while($row = mysqli_fetch_assoc($product_result)) {
    $results[] = [
        'id' => $row['id'],
        'name' => $row['name'],
        'brand' => $row['brand_name'],
        'image' => 'uploads/products/' . $row['base_image'],
        'price' => number_format($row['price']),
        'type' => 'product',
        'url' => '#'  // Will open quick view
    ];
}

// Search in pharmacy products
$pharmacy_sql = "SELECT id, product_name as name, brand, image_url, price, 'pharmacy' as type
                 FROM pharmacy_products
                 WHERE status = 'active'
                 AND (product_name LIKE '%$query%' 
                      OR brand LIKE '%$query%' 
                      OR description LIKE '%$query%'
                      OR category LIKE '%$query%')
                 ORDER BY 
                    CASE 
                        WHEN product_name LIKE '$query%' THEN 1
                        WHEN product_name LIKE '%$query%' THEN 2
                        WHEN brand LIKE '$query%' THEN 3
                        ELSE 4
                    END
                 LIMIT 5";

$pharmacy_result = mysqli_query($conn, $pharmacy_sql);
if($pharmacy_result) {
    while($row = mysqli_fetch_assoc($pharmacy_result)) {
        $results[] = [
            'id' => $row['id'],
            'name' => $row['name'],
            'brand' => $row['brand'] ?? 'Pharmacy',
            'image' => $row['image_url'] ? 'uploads/pharmacy/' . $row['image_url'] : 'uploads/default-pharmacy.jpg',
            'price' => number_format($row['price']),
            'type' => 'pharmacy',
            'url' => 'pharmacy.php?product_id=' . $row['id']
        ];
    }
}

echo json_encode($results);
?>
