<?php
session_start();
include 'db.php';

// Set timezone to match your location
date_default_timezone_set('Asia/Kolkata');

header('Content-Type: application/json');

$action = isset($_POST['action']) ? $_POST['action'] : '';
$reel_id = isset($_POST['reel_id']) ? intval($_POST['reel_id']) : 0;

// Check if user is logged in (only for add_comment action)
if ($action === 'add_comment' && !isset($_SESSION['user_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Please login to FurryMart to comment on reels',
        'requiresLogin' => true
    ]);
    exit;
}

// Get user info
$user_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;
$username = isset($_SESSION['first_name']) ? $_SESSION['first_name'] : (isset($_SESSION['email']) ? explode('@', $_SESSION['email'])[0] : 'User');

if ($action === 'add_comment') {
    $comment = isset($_POST['comment']) ? trim($_POST['comment']) : '';
    
    if (empty($comment)) {
        echo json_encode([
            'success' => false,
            'message' => 'Comment cannot be empty'
        ]);
        exit;
    }
    
    if (strlen($comment) > 500) {
        echo json_encode([
            'success' => false,
            'message' => 'Comment is too long (max 500 characters)'
        ]);
        exit;
    }
    
    if ($reel_id <= 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Invalid reel ID'
        ]);
        exit;
    }
    
    // Sanitize comment
    $comment = mysqli_real_escape_string($conn, $comment);
    $username = mysqli_real_escape_string($conn, $username);
    
    // Insert comment
    $stmt = $conn->prepare("INSERT INTO reel_comments (reel_id, user_id, username, comment) VALUES (?, ?, ?, ?)");
    if (!$stmt) {
        echo json_encode([
            'success' => false,
            'message' => 'Database error: ' . $conn->error
        ]);
        exit;
    }
    
    $stmt->bind_param("iiss", $reel_id, $user_id, $username, $comment);
    
    if ($stmt->execute()) {
        // Get the comment count
        $count_query = $conn->query("SELECT COUNT(*) as count FROM reel_comments WHERE reel_id = $reel_id");
        $count_data = $count_query->fetch_assoc();
        
        echo json_encode([
            'success' => true,
            'message' => 'Comment posted successfully!',
            'comment_count' => intval($count_data['count'])
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to add comment: ' . $stmt->error
        ]);
    }
    
    $stmt->close();
}

elseif ($action === 'get_comments') {
    if ($reel_id <= 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Invalid reel ID'
        ]);
        exit;
    }
    
    $stmt = $conn->prepare("SELECT id, user_id, username, comment, created_at FROM reel_comments WHERE reel_id = ? ORDER BY created_at DESC LIMIT 50");
    if (!$stmt) {
        echo json_encode([
            'success' => false,
            'message' => 'Database error: ' . $conn->error
        ]);
        exit;
    }
    
    $stmt->bind_param("i", $reel_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    // Get current user's ID if logged in
    $current_user_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;
    
    $comments = [];
    while ($row = $result->fetch_assoc()) {
        $is_own_comment = ($current_user_id > 0 && intval($row['user_id']) === $current_user_id);
        
        $comments[] = [
            'id' => intval($row['id']),
            'user_id' => intval($row['user_id']),
            'username' => htmlspecialchars($row['username']),
            'comment' => htmlspecialchars($row['comment']),
            'time' => time_elapsed_string($row['created_at']),
            'is_own' => $is_own_comment
        ];
    }
    
    // Get total count
    $count_stmt = $conn->prepare("SELECT COUNT(*) as total FROM reel_comments WHERE reel_id = ?");
    $count_stmt->bind_param("i", $reel_id);
    $count_stmt->execute();
    $count_result = $count_stmt->get_result();
    $count_data = $count_result->fetch_assoc();
    $total_comments = intval($count_data['total']);
    $count_stmt->close();
    
    echo json_encode([
        'success' => true,
        'comments' => $comments,
        'comment_count' => $total_comments
    ]);
    
    $stmt->close();
}

elseif ($action === 'get_count') {
    if ($reel_id <= 0) {
        echo json_encode([
            'success' => true,
            'count' => 0
        ]);
        exit;
    }
    
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM reel_comments WHERE reel_id = ?");
    if (!$stmt) {
        echo json_encode([
            'success' => true,
            'count' => 0
        ]);
        exit;
    }
    
    $stmt->bind_param("i", $reel_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();
    
    echo json_encode([
        'success' => true,
        'count' => intval($data['count'])
    ]);
    
    $stmt->close();
}

else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid action'
    ]);
}

$conn->close();

// Helper function to format time with proper timezone handling
function time_elapsed_string($datetime, $full = false) {
    // Set timezone to match database
    date_default_timezone_set('Asia/Kolkata');
    
    // Convert to timestamp
    $time_ago = strtotime($datetime);
    $current_time = time();
    $time_difference = $current_time - $time_ago;
    
    // If negative or zero, it's just now
    if ($time_difference <= 5) {
        return 'just now';
    }
    
    $seconds = $time_difference;
    $minutes = round($seconds / 60);
    $hours = round($seconds / 3600);
    $days = round($seconds / 86400);
    $weeks = round($seconds / 604800);
    $months = round($seconds / 2629440);
    $years = round($seconds / 31553280);
    
    if ($seconds <= 60) {
        return $seconds == 1 ? "1 second ago" : "$seconds seconds ago";
    } else if ($minutes <= 60) {
        return $minutes == 1 ? "1 minute ago" : "$minutes minutes ago";
    } else if ($hours <= 24) {
        return $hours == 1 ? "1 hour ago" : "$hours hours ago";
    } else if ($days <= 7) {
        return $days == 1 ? "1 day ago" : "$days days ago";
    } else if ($weeks <= 4.3) {
        return $weeks == 1 ? "1 week ago" : "$weeks weeks ago";
    } else if ($months <= 12) {
        return $months == 1 ? "1 month ago" : "$months months ago";
    } else {
        return $years == 1 ? "1 year ago" : "$years years ago";
    }
}
?>
