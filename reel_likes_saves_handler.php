<?php
session_start();
include 'db.php';

header('Content-Type: application/json');

/**
 * FURRYMART - Reel Likes and Saves Handler
 * Manages user-specific likes and saves for mood reels
 */

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in', 'requiresLogin' => true]);
    exit;
}

$user_id = intval($_SESSION['user_id']);
$action = isset($_POST['action']) ? $_POST['action'] : '';

switch ($action) {
    case 'get_user_data':
        // Fetch user's likes and saves
        getUserLikesAndSaves($conn, $user_id);
        break;
        
    case 'toggle_like':
        // Toggle like for a reel
        $reel_id = isset($_POST['reel_id']) ? intval($_POST['reel_id']) : 0;
        if ($reel_id > 0) {
            toggleLike($conn, $user_id, $reel_id);
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid reel ID']);
        }
        break;
        
    case 'toggle_save':
        // Toggle save for a reel
        $reel_id = isset($_POST['reel_id']) ? intval($_POST['reel_id']) : 0;
        if ($reel_id > 0) {
            toggleSave($conn, $user_id, $reel_id);
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid reel ID']);
        }
        break;
        
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        break;
}

/**
 * Get user's likes and saves from database
 */
function getUserLikesAndSaves($conn, $user_id) {
    try {
        // Fetch likes
        $likes_query = "SELECT reel_id FROM reel_likes WHERE user_id = ?";
        $stmt = mysqli_prepare($conn, $likes_query);
        
        if (!$stmt) {
            // Table might not exist
            echo json_encode([
                'success' => false, 
                'message' => 'Database table not found. Please run reel_likes_saves.sql first!',
                'error' => mysqli_error($conn)
            ]);
            return;
        }
        
        mysqli_stmt_bind_param($stmt, "i", $user_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        $likes = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $likes[] = intval($row['reel_id']);
        }
        mysqli_stmt_close($stmt);
        
        // Fetch saves
        $saves_query = "SELECT reel_id FROM reel_saves WHERE user_id = ?";
        $stmt = mysqli_prepare($conn, $saves_query);
        
        if (!$stmt) {
            echo json_encode([
                'success' => false, 
                'message' => 'Database table not found. Please run reel_likes_saves.sql first!',
                'error' => mysqli_error($conn)
            ]);
            return;
        }
        
        mysqli_stmt_bind_param($stmt, "i", $user_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        $saves = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $saves[] = intval($row['reel_id']);
        }
        mysqli_stmt_close($stmt);
        
        echo json_encode([
            'success' => true,
            'likes' => $likes,
            'saves' => $saves,
            'like_count' => count($likes),
            'save_count' => count($saves)
        ]);
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Database error: ' . $e->getMessage()
        ]);
    }
}

/**
 * Toggle like status for a reel
 */
function toggleLike($conn, $user_id, $reel_id) {
    try {
        // Check if already liked
        $check_query = "SELECT id FROM reel_likes WHERE user_id = ? AND reel_id = ?";
        $stmt = mysqli_prepare($conn, $check_query);
        
        if (!$stmt) {
            echo json_encode([
                'success' => false, 
                'message' => 'Database table not found. Please run reel_likes_saves.sql first!',
                'error' => mysqli_error($conn)
            ]);
            return;
        }
        
        mysqli_stmt_bind_param($stmt, "ii", $user_id, $reel_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if (mysqli_num_rows($result) > 0) {
            // Already liked, remove it
            mysqli_stmt_close($stmt);
            
            $delete_query = "DELETE FROM reel_likes WHERE user_id = ? AND reel_id = ?";
            $stmt = mysqli_prepare($conn, $delete_query);
            mysqli_stmt_bind_param($stmt, "ii", $user_id, $reel_id);
            $success = mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            
            if ($success) {
                echo json_encode(['success' => true, 'action' => 'removed', 'message' => 'Like removed']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to remove like: ' . mysqli_error($conn)]);
            }
        } else {
            // Not liked yet, add it
            mysqli_stmt_close($stmt);
            
            $insert_query = "INSERT INTO reel_likes (user_id, reel_id) VALUES (?, ?)";
            $stmt = mysqli_prepare($conn, $insert_query);
            mysqli_stmt_bind_param($stmt, "ii", $user_id, $reel_id);
            $success = mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            
            if ($success) {
                echo json_encode(['success' => true, 'action' => 'added', 'message' => 'Reel liked']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to add like: ' . mysqli_error($conn)]);
            }
        }
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Database error: ' . $e->getMessage()
        ]);
    }
}

/**
 * Toggle save status for a reel
 */
function toggleSave($conn, $user_id, $reel_id) {
    try {
        // Check if already saved
        $check_query = "SELECT id FROM reel_saves WHERE user_id = ? AND reel_id = ?";
        $stmt = mysqli_prepare($conn, $check_query);
        
        if (!$stmt) {
            echo json_encode([
                'success' => false, 
                'message' => 'Database table not found. Please run reel_likes_saves.sql first!',
                'error' => mysqli_error($conn)
            ]);
            return;
        }
        
        mysqli_stmt_bind_param($stmt, "ii", $user_id, $reel_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if (mysqli_num_rows($result) > 0) {
            // Already saved, remove it
            mysqli_stmt_close($stmt);
            
            $delete_query = "DELETE FROM reel_saves WHERE user_id = ? AND reel_id = ?";
            $stmt = mysqli_prepare($conn, $delete_query);
            mysqli_stmt_bind_param($stmt, "ii", $user_id, $reel_id);
            $success = mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            
            if ($success) {
                echo json_encode(['success' => true, 'action' => 'removed', 'message' => 'Save removed']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to remove save: ' . mysqli_error($conn)]);
            }
        } else {
            // Not saved yet, add it
            mysqli_stmt_close($stmt);
            
            $insert_query = "INSERT INTO reel_saves (user_id, reel_id) VALUES (?, ?)";
            $stmt = mysqli_prepare($conn, $insert_query);
            mysqli_stmt_bind_param($stmt, "ii", $user_id, $reel_id);
            $success = mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            
            if ($success) {
                echo json_encode(['success' => true, 'action' => 'added', 'message' => 'Reel saved']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to save reel: ' . mysqli_error($conn)]);
            }
        }
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Database error: ' . $e->getMessage()
        ]);
    }
}
?>
