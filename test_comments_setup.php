<?php
/**
 * FURRYMART - Reel Comments System Test File
 * This file checks if the comment system is properly configured
 * Access: http://localhost/FURRYMART/test_comments_setup.php
 */

session_start();
include 'db.php';

echo "<!DOCTYPE html>
<html>
<head>
    <title>FurryMart - Comment System Test</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #0f172a; color: #fff; padding: 40px; }
        .container { max-width: 800px; margin: 0 auto; }
        h1 { color: #518992; border-bottom: 3px solid #518992; padding-bottom: 10px; }
        .test-item { background: #1e293b; padding: 20px; margin: 15px 0; border-radius: 10px; border-left: 4px solid #518992; }
        .success { border-left-color: #22c55e; }
        .error { border-left-color: #f87171; }
        .warning { border-left-color: #fbbf24; }
        .status { font-weight: bold; font-size: 18px; margin-bottom: 10px; }
        .status i { margin-right: 8px; }
        .success .status { color: #22c55e; }
        .error .status { color: #f87171; }
        .warning .status { color: #fbbf24; }
        .details { color: #94a3b8; font-size: 14px; line-height: 1.6; }
        .code { background: #0f172a; padding: 10px; border-radius: 5px; font-family: 'Courier New', monospace; margin-top: 10px; }
        a.btn { display: inline-block; background: #518992; color: #fff; padding: 12px 24px; border-radius: 8px; text-decoration: none; margin-top: 20px; font-weight: bold; }
        a.btn:hover { background: #6ba3ac; }
    </style>
    <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css'>
</head>
<body>
<div class='container'>
    <h1><i class='fas fa-vial'></i> FurryMart Comment System Test</h1>";

// Test 1: Database Connection
echo "<div class='test-item " . ($conn ? "success" : "error") . "'>
        <div class='status'>" . ($conn ? "<i class='fas fa-check-circle'></i> Database Connection" : "<i class='fas fa-times-circle'></i> Database Connection Failed") . "</div>
        <div class='details'>" . ($conn ? "Successfully connected to the 'furrymart' database." : "Could not connect to database. Check your db.php file.") . "</div>
      </div>";

// Test 2: Comments Table Exists
$tableExists = false;
$tableError = "";
if ($conn) {
    $result = mysqli_query($conn, "SHOW TABLES LIKE 'reel_comments'");
    $tableExists = mysqli_num_rows($result) > 0;
    if (!$tableExists) {
        $tableError = "Table 'reel_comments' does not exist.";
    }
}

echo "<div class='test-item " . ($tableExists ? "success" : "error") . "'>
        <div class='status'>" . ($tableExists ? "<i class='fas fa-check-circle'></i> Comments Table Exists" : "<i class='fas fa-times-circle'></i> Comments Table Missing") . "</div>
        <div class='details'>" . ($tableExists ? "The 'reel_comments' table is properly created." : $tableError . " Please import reel_comments_table.sql") . "</div>
      </div>";

// Test 3: Comments Table Structure
if ($tableExists) {
    $columns = mysqli_query($conn, "DESCRIBE reel_comments");
    $columnCount = mysqli_num_rows($columns);
    $requiredColumns = ['id', 'reel_id', 'user_id', 'username', 'comment', 'created_at'];
    $missingColumns = [];
    
    $existingColumns = [];
    while ($col = mysqli_fetch_assoc($columns)) {
        $existingColumns[] = $col['Field'];
    }
    
    foreach ($requiredColumns as $req) {
        if (!in_array($req, $existingColumns)) {
            $missingColumns[] = $req;
        }
    }
    
    $structureValid = empty($missingColumns);
    
    echo "<div class='test-item " . ($structureValid ? "success" : "error") . "'>
            <div class='status'>" . ($structureValid ? "<i class='fas fa-check-circle'></i> Table Structure" : "<i class='fas fa-times-circle'></i> Table Structure Issue") . "</div>
            <div class='details'>" . ($structureValid ? "All required columns are present: " . implode(', ', $existingColumns) : "Missing columns: " . implode(', ', $missingColumns)) . "</div>
          </div>";
}

// Test 4: Handler File Exists
$handlerExists = file_exists(__DIR__ . '/reel_comment_handler.php');
echo "<div class='test-item " . ($handlerExists ? "success" : "error") . "'>
        <div class='status'>" . ($handlerExists ? "<i class='fas fa-check-circle'></i> Handler File" : "<i class='fas fa-times-circle'></i> Handler File Missing") . "</div>
        <div class='details'>" . ($handlerExists ? "reel_comment_handler.php is present and accessible." : "reel_comment_handler.php is missing.") . "</div>
      </div>";

// Test 5: Session Check
$sessionActive = isset($_SESSION);
$userLoggedIn = isset($_SESSION['user_id']);
echo "<div class='test-item " . ($sessionActive ? ($userLoggedIn ? "success" : "warning") : "error") . "'>
        <div class='status'>" . ($sessionActive ? ($userLoggedIn ? "<i class='fas fa-check-circle'></i> User Session" : "<i class='fas fa-exclamation-triangle'></i> Not Logged In") : "<i class='fas fa-times-circle'></i> Session Error") . "</div>
        <div class='details'>";
        
if (!$sessionActive) {
    echo "Session is not active.";
} elseif (!$userLoggedIn) {
    echo "Session is active but no user is logged in. You need to login to test the comment system.";
} else {
    $username = isset($_SESSION['first_name']) ? $_SESSION['first_name'] : (isset($_SESSION['email']) ? explode('@', $_SESSION['email'])[0] : 'Unknown');
    echo "User logged in as: <strong>" . htmlspecialchars($username) . "</strong> (ID: " . $_SESSION['user_id'] . ")";
}

echo "</div></div>";

// Test 6: pet_moods Table (for reels)
$reelsTableExists = false;
if ($conn) {
    $result = mysqli_query($conn, "SHOW TABLES LIKE 'pet_moods'");
    $reelsTableExists = mysqli_num_rows($result) > 0;
}

if ($reelsTableExists) {
    $reelsCount = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM pet_moods"))['count'];
    echo "<div class='test-item " . ($reelsCount > 0 ? "success" : "warning") . "'>
            <div class='status'>" . ($reelsCount > 0 ? "<i class='fas fa-check-circle'></i> Reels Data" : "<i class='fas fa-exclamation-triangle'></i> No Reels") . "</div>
            <div class='details'>" . ($reelsCount > 0 ? "Found {$reelsCount} reel(s) in the pet_moods table." : "No reels found. Add some reels to test comments.") . "</div>
          </div>";
} else {
    echo "<div class='test-item error'>
            <div class='status'><i class='fas fa-times-circle'></i> Reels Table Missing</div>
            <div class='details'>The 'pet_moods' table does not exist. Create it to add reels.</div>
          </div>";
}

// Test 7: Existing Comments
if ($tableExists) {
    $commentsCount = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM reel_comments"))['count'];
    echo "<div class='test-item " . ($commentsCount >= 0 ? "success" : "error") . "'>
            <div class='status'><i class='fas fa-info-circle'></i> Existing Comments</div>
            <div class='details'>Currently there are <strong>{$commentsCount}</strong> comment(s) in the database.</div>
          </div>";
}

// Overall Status
$allGood = $conn && $tableExists && $handlerExists && ($structureValid ?? false);

echo "<div class='test-item " . ($allGood ? "success" : ($conn ? "warning" : "error")) . "'>
        <div class='status'>" . ($allGood ? "<i class='fas fa-check-circle'></i> Overall Status: READY" : "<i class='fas fa-exclamation-triangle'></i> Overall Status: " . ($conn ? "NEEDS ATTENTION" : "CRITICAL ISSUES")) . "</div>
        <div class='details'>";

if ($allGood) {
    echo "All systems are ready! You can now use the comment feature on reels.";
    if (!$userLoggedIn) {
        echo "<br><strong>Note:</strong> Login to test commenting functionality.";
    }
} else {
    echo "Please fix the issues above before using the comment system.";
    if (!$tableExists) {
        echo "<div class='code'>
        <strong>To fix:</strong> Import reel_comments_table.sql in phpMyAdmin:<br>
        1. Open phpMyAdmin (http://localhost/phpmyadmin)<br>
        2. Select 'furrymart' database<br>
        3. Click 'Import' tab<br>
        4. Choose 'reel_comments_table.sql'<br>
        5. Click 'Go'
        </div>";
    }
}

echo "</div></div>";

echo "
    <div style='margin-top: 30px; text-align: center;'>
        <a href='pet_feelings.php' class='btn'><i class='fas fa-play'></i> Go to Pet Feelings Page</a>
        " . (!$userLoggedIn ? "<a href='login.php' class='btn'><i class='fas fa-sign-in-alt'></i> Login</a>" : "") . "
        <a href='javascript:location.reload()' class='btn'><i class='fas fa-sync'></i> Re-test</a>
    </div>
</div>
</body>
</html>";

if ($conn) mysqli_close($conn);
?>
