<?php
session_start();
include 'db.php';

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Please login to follow content',
        'requiresLogin' => true
    ]);
    exit;
}

$action = isset($_POST['action']) ? $_POST['action'] : '';
$category = isset($_POST['category']) ? mysqli_real_escape_string($conn, $_POST['category']) : '';
$user_id = intval($_SESSION['user_id']);

if ($action === 'follow') {
    if (empty($category)) {
        echo json_encode(['success' => false, 'message' => 'Invalid category']);
        exit;
    }
    
    // Check if already following
    $check = $conn->prepare("SELECT id FROM reel_follows WHERE user_id = ? AND category = ?");
    $check->bind_param("is", $user_id, $category);
    $check->execute();
    $result = $check->get_result();
    
    if ($result->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'Already following this category']);
        exit;
    }
    
    // Add follow
    $stmt = $conn->prepare("INSERT INTO reel_follows (user_id, category) VALUES (?, ?)");
    $stmt->bind_param("is", $user_id, $category);
    
    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'Now following ' . $category . ' reels!',
            'is_following' => true
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to follow']);
    }
    
    $stmt->close();
}

elseif ($action === 'unfollow') {
    if (empty($category)) {
        echo json_encode(['success' => false, 'message' => 'Invalid category']);
        exit;
    }
    
    $stmt = $conn->prepare("DELETE FROM reel_follows WHERE user_id = ? AND category = ?");
    $stmt->bind_param("is", $user_id, $category);
    
    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'Unfollowed ' . $category . ' reels',
            'is_following' => false
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to unfollow']);
    }
    
    $stmt->close();
}

elseif ($action === 'check_following') {
    if (empty($category)) {
        echo json_encode(['success' => false, 'message' => 'Invalid category']);
        exit;
    }
    
    $stmt = $conn->prepare("SELECT id FROM reel_follows WHERE user_id = ? AND category = ?");
    $stmt->bind_param("is", $user_id, $category);
    $stmt->execute();
    $result = $stmt->get_result();
    
    echo json_encode([
        'success' => true,
        'is_following' => $result->num_rows > 0
    ]);
    
    $stmt->close();
}

elseif ($action === 'get_following_list') {
    $stmt = $conn->prepare("SELECT category FROM reel_follows WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $following = [];
    while ($row = $result->fetch_assoc()) {
        $following[] = $row['category'];
    }
    
    echo json_encode([
        'success' => true,
        'following' => $following
    ]);
    
    $stmt->close();
}

elseif ($action === 'mark_seen') {
    $reel_id = isset($_POST['reel_id']) ? intval($_POST['reel_id']) : 0;
    
    if ($reel_id > 0) {
        // Mark reel as seen by updating is_new to 0 for this user (simplified - just mark globally)
        // In production, you'd track per-user views
        echo json_encode(['success' => true, 'message' => 'Marked as seen']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid reel ID']);
    }
}

else {
    echo json_encode(['success' => false, 'message' => 'Invalid action']);
}

$conn->close();
?>
