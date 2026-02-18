<?php
include "db.php";
if (session_status() === PHP_SESSION_NONE) { session_start(); }

header('Content-Type: application/json');

// Check if user is logged in
if(!isset($_SESSION['email'])) {
    echo json_encode(['success' => false, 'message' => 'Please login to book a party']);
    exit;
}

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get user info from database
    $user_email = $_SESSION['email'];
    $user_query = mysqli_query($conn, "SELECT first_name, last_name FROM users WHERE email = '$user_email'");
    $user_data = mysqli_fetch_assoc($user_query);
    $user_name = $user_data['first_name'] . ' ' . $user_data['last_name'];
    
    $pet_name = mysqli_real_escape_string($conn, $_POST['pet_name']);
    $pet_type = mysqli_real_escape_string($conn, $_POST['pet_type']);
    $party_date = mysqli_real_escape_string($conn, $_POST['party_date']);
    $party_time = mysqli_real_escape_string($conn, $_POST['party_time']);
    $guest_count = (int)$_POST['guest_count'];
    $party_package = mysqli_real_escape_string($conn, $_POST['party_package']);
    $special_requests = mysqli_real_escape_string($conn, $_POST['special_requests']);
    $contact_phone = mysqli_real_escape_string($conn, $_POST['contact_phone']);
    
    // Validate date (must be at least 7 days in future)
    $booking_date = strtotime($party_date);
    $min_date = strtotime('+7 days');
    
    if($booking_date < $min_date) {
        echo json_encode(['success' => false, 'message' => 'Party must be booked at least 7 days in advance']);
        exit;
    }
    
    // Check if user already has a pending booking for same date
    $check_query = "SELECT id FROM birthday_bookings 
                    WHERE user_email = '$user_email' 
                    AND party_date = '$party_date' 
                    AND status != 'rejected'";
    $check_result = mysqli_query($conn, $check_query);
    
    if(mysqli_num_rows($check_result) > 0) {
        echo json_encode(['success' => false, 'message' => 'You already have a booking for this date. Please choose a different date.']);
        exit;
    }
    
    // Insert booking
    $insert_query = "INSERT INTO birthday_bookings 
                    (user_email, user_name, pet_name, pet_type, party_date, party_time, guest_count, party_package, special_requests, contact_phone, status, booking_date) 
                    VALUES 
                    ('$user_email', '$user_name', '$pet_name', '$pet_type', '$party_date', '$party_time', $guest_count, '$party_package', '$special_requests', '$contact_phone', 'pending', NOW())";
    
    if(mysqli_query($conn, $insert_query)) {
        $booking_id = mysqli_insert_id($conn);
        echo json_encode([
            'success' => true,
            'booking_id' => $booking_id,
            'message' => '🎉 Party Booked Successfully!',
            'details' => [
                'pet_name' => $pet_name,
                'pet_type' => $pet_type,
                'party_date' => date('F d, Y', strtotime($party_date)),
                'party_time' => $party_time,
                'guest_count' => $guest_count,
                'party_package' => $party_package,
                'status' => 'Pending Approval'
            ]
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Something went wrong. Error: ' . mysqli_error($conn)]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>
