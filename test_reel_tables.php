<?php
/**
 * Quick test to check if reel_likes and reel_saves tables exist
 */
include 'db.php';

echo "<h2>Database Tables Check</h2>";
echo "<style>body{font-family:Arial;padding:20px;} .success{color:green;} .error{color:red;} .info{color:blue;}</style>";

// Check if reel_likes table exists
$check_likes = mysqli_query($conn, "SHOW TABLES LIKE 'reel_likes'");
if (mysqli_num_rows($check_likes) > 0) {
    echo "<p class='success'>✓ Table 'reel_likes' exists</p>";
    
    // Check structure
    $structure = mysqli_query($conn, "DESCRIBE reel_likes");
    echo "<p class='info'>Columns: ";
    $cols = [];
    while ($row = mysqli_fetch_assoc($structure)) {
        $cols[] = $row['Field'];
    }
    echo implode(', ', $cols) . "</p>";
} else {
    echo "<p class='error'>✗ Table 'reel_likes' does NOT exist</p>";
    echo "<p class='info'>👉 Please run the SQL file: <strong>reel_likes_saves.sql</strong> in phpMyAdmin</p>";
}

// Check if reel_saves table exists
$check_saves = mysqli_query($conn, "SHOW TABLES LIKE 'reel_saves'");
if (mysqli_num_rows($check_saves) > 0) {
    echo "<p class='success'>✓ Table 'reel_saves' exists</p>";
    
    // Check structure
    $structure = mysqli_query($conn, "DESCRIBE reel_saves");
    echo "<p class='info'>Columns: ";
    $cols = [];
    while ($row = mysqli_fetch_assoc($structure)) {
        $cols[] = $row['Field'];
    }
    echo implode(', ', $cols) . "</p>";
} else {
    echo "<p class='error'>✗ Table 'reel_saves' does NOT exist</p>";
    echo "<p class='info'>👉 Please run the SQL file: <strong>reel_likes_saves.sql</strong> in phpMyAdmin</p>";
}

// Additional checks
echo "<hr>";
echo "<h3>Setup Instructions:</h3>";
echo "<ol>";
echo "<li>Open <strong>phpMyAdmin</strong> (usually at <a href='http://localhost/phpmyadmin' target='_blank'>http://localhost/phpmyadmin</a>)</li>";
echo "<li>Select your <strong>furrymart</strong> database</li>";
echo "<li>Click on the <strong>SQL</strong> tab</li>";
echo "<li>Open the file <strong>c:\\xampp\\htdocs\\FURRYMART\\reel_likes_saves.sql</strong></li>";
echo "<li>Copy all the SQL code and paste it into phpMyAdmin</li>";
echo "<li>Click <strong>Go</strong> to execute</li>";
echo "<li>Refresh this page to verify the tables were created</li>";
echo "</ol>";

echo "<p><a href='pet_feelings.php'>← Back to Pet Feelings</a></p>";
?>
