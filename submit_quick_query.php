<?php
/**
 * FurryMart Quick Query Handler (Email Support)
 * This file handles AJAX submissions for quick email queries
 */

session_start();
include 'db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get POST data
    $name = isset($_POST['name']) ? mysqli_real_escape_string($conn, trim($_POST['name'])) : '';
    $email = isset($_POST['email']) ? mysqli_real_escape_string($conn, trim($_POST['email'])) : '';
    $message = isset($_POST['message']) ? mysqli_real_escape_string($conn, trim($_POST['message'])) : '';
    $user_id = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : NULL;
    
    // Validation
    if (empty($name) || empty($email) || empty($message)) {
        echo json_encode([
            'success' => false,
            'message' => 'Please fill in all fields.'
        ]);
        exit;
    }
    
    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode([
            'success' => false,
            'message' => 'Please provide a valid email address.'
        ]);
        exit;
    }
    
    // Insert into database
    if ($user_id) {
        $sql = "INSERT INTO quick_queries (user_id, name, email, message, status) 
                VALUES ($user_id, '$name', '$email', '$message', 'unread')";
    } else {
        $sql = "INSERT INTO quick_queries (name, email, message, status) 
                VALUES ('$name', '$email', '$message', 'unread')";
    }
    
    if (mysqli_query($conn, $sql)) {
        echo json_encode([
            'success' => true,
            'message' => 'Query submitted successfully! We will respond to your email shortly.'
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
