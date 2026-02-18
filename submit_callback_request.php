<?php
/**
 * FurryMart Callback Request Handler
 * This file handles AJAX submissions for callback requests
 */

session_start();
include 'db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get POST data
    $name = isset($_POST['user_name']) ? mysqli_real_escape_string($conn, trim($_POST['user_name'])) : '';
    $phone = isset($_POST['user_phone']) ? mysqli_real_escape_string($conn, trim($_POST['user_phone'])) : '';
    $user_id = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : NULL;
    
    // Validation
    if (empty($name) || empty($phone)) {
        echo json_encode([
            'success' => false,
            'message' => 'Please provide both name and phone number.'
        ]);
        exit;
    }
    
    // Validate phone number (10 digits)
    if (!preg_match('/^[0-9]{10}$/', $phone)) {
        echo json_encode([
            'success' => false,
            'message' => 'Please provide a valid 10-digit phone number.'
        ]);
        exit;
    }
    
    // Insert into database
    if ($user_id) {
        $sql = "INSERT INTO callback_requests (user_id, name, phone, status) 
                VALUES ($user_id, '$name', '$phone', 'pending')";
    } else {
        $sql = "INSERT INTO callback_requests (name, phone, status) 
                VALUES ('$name', '$phone', 'pending')";
    }
    
    if (mysqli_query($conn, $sql)) {
        echo json_encode([
            'success' => true,
            'message' => 'Callback request submitted successfully! We will call you within 2-3 minutes.'
        ]);
    } else {
        // Check if table doesn't exist
        $error = mysqli_error($conn);
        if (strpos($error, "doesn't exist") !== false) {
            echo json_encode([
                'success' => false,
                'message' => 'Database tables not created yet. Please ask admin to run the SQL setup first.'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Database error: ' . $error
            ]);
        }
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method.'
    ]);
}
?>
