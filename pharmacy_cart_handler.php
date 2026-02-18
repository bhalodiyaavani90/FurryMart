<?php
include "db.php";
if (session_status() === PHP_SESSION_NONE) { session_start(); }

// Handle Add to Cart for Pharmacy Products
if (isset($_POST['action']) && $_POST['action'] === 'add') {
    $product_id = intval($_POST['product_id']);
    $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;

    // Initialize cart if not exists
    if (!isset($_SESSION['pharmacy_cart'])) {
        $_SESSION['pharmacy_cart'] = [];
    }

    // Fetch product details
    $q = mysqli_query($conn, "SELECT * FROM pharmacy_products WHERE id = $product_id AND is_available = 1");
    
    if ($row = mysqli_fetch_assoc($q)) {
        // Check if product already in cart
        if (isset($_SESSION['pharmacy_cart'][$product_id])) {
            $_SESSION['pharmacy_cart'][$product_id]['qty'] += $quantity;
        } else {
            $_SESSION['pharmacy_cart'][$product_id] = [
                'id' => $product_id,
                'product_name' => $row['product_name'],
                'brand_name' => $row['brand_name'],
                'price' => $row['price'],
                'image' => $row['image'],
                'size_options' => $row['size_options'],
                'category' => $row['category'],
                'qty' => $quantity
            ];
        }
        
        echo json_encode(['status' => 'success', 'message' => 'Product added to cart']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Product not found']);
    }
    exit;
}

// Handle Update Quantity
if (isset($_POST['action']) && $_POST['action'] === 'update') {
    $product_id = intval($_POST['product_id']);
    $quantity = intval($_POST['quantity']);
    
    if (isset($_SESSION['pharmacy_cart'][$product_id])) {
        if ($quantity > 0) {
            $_SESSION['pharmacy_cart'][$product_id]['qty'] = $quantity;
            echo json_encode(['status' => 'success', 'message' => 'Cart updated']);
        } else {
            unset($_SESSION['pharmacy_cart'][$product_id]);
            echo json_encode(['status' => 'success', 'message' => 'Item removed']);
        }
    }
    exit;
}

// Handle Remove from Cart
if (isset($_POST['action']) && $_POST['action'] === 'remove') {
    $product_id = intval($_POST['product_id']);
    
    if (isset($_SESSION['pharmacy_cart'][$product_id])) {
        unset($_SESSION['pharmacy_cart'][$product_id]);
        echo json_encode(['status' => 'success', 'message' => 'Item removed from cart']);
    }
    exit;
}

// Get Cart Count
if (isset($_GET['action']) && $_GET['action'] === 'count') {
    $count = 0;
    if (isset($_SESSION['pharmacy_cart'])) {
        foreach ($_SESSION['pharmacy_cart'] as $item) {
            $count += $item['qty'];
        }
    }
    echo json_encode(['count' => $count]);
    exit;
}
?>
