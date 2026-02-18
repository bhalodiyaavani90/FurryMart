<?php
// TEST DATABASE SETUP - Check if all tables exist
include 'db.php';

echo "<h1>FurryMart Database Setup Test</h1>";
echo "<hr>";

// Test 1: Check if callback_requests table exists
echo "<h2>Test 1: Callback Requests Table</h2>";
$test1 = mysqli_query($conn, "SHOW TABLES LIKE 'callback_requests'");
if (mysqli_num_rows($test1) > 0) {
    echo "✅ <strong style='color:green'>callback_requests table EXISTS</strong><br>";
    
    // Check structure
    $structure = mysqli_query($conn, "DESCRIBE callback_requests");
    echo "Table structure:<br>";
    echo "<pre>";
    $has_user_id = false;
    while ($row = mysqli_fetch_assoc($structure)) {
        if ($row['Field'] === 'user_id') {
            $has_user_id = true;
        }
        echo $row['Field'] . " - " . $row['Type'] . "\n";
    }
    echo "</pre>";
    
    // Check if user_id column exists
    if (!$has_user_id) {
        echo "⚠️ <strong style='color:orange'>WARNING: Missing 'user_id' column!</strong><br>";
        echo "<p style='color:red; font-weight:bold'>This is why you're getting the error!</p>";
    } else {
        echo "✅ <strong style='color:green'>user_id column exists</strong><br>";
    }
    
    // Count records
    $count_result = mysqli_query($conn, "SELECT COUNT(*) as total FROM callback_requests");
    $count = mysqli_fetch_assoc($count_result)['total'];
    echo "Total records: <strong>$count</strong><br>";
    
} else {
    echo "❌ <strong style='color:red'>callback_requests table DOES NOT EXIST</strong><br>";
    echo "<p style='color:red'>You need to run the SQL setup!</p>";
}

echo "<hr>";

// Test 2: Check if quick_queries table exists
echo "<h2>Test 2: Quick Queries Table</h2>";
$test2 = mysqli_query($conn, "SHOW TABLES LIKE 'quick_queries'");
if (mysqli_num_rows($test2) > 0) {
    echo "✅ <strong style='color:green'>quick_queries table EXISTS</strong><br>";
    
    // Check structure
    $structure = mysqli_query($conn, "DESCRIBE quick_queries");
    echo "Table structure:<br>";
    echo "<pre>";
    $has_user_id = false;
    while ($row = mysqli_fetch_assoc($structure)) {
        if ($row['Field'] === 'user_id') {
            $has_user_id = true;
        }
        echo $row['Field'] . " - " . $row['Type'] . "\n";
    }
    echo "</pre>";
    
    // Check if user_id column exists
    if (!$has_user_id) {
        echo "⚠️ <strong style='color:orange'>WARNING: Missing 'user_id' column!</strong><br>";
        echo "<p style='color:red; font-weight:bold'>This is why you're getting the error!</p>";
    } else {
        echo "✅ <strong style='color:green'>user_id column exists</strong><br>";
    }
    
    // Count records
    $count_result = mysqli_query($conn, "SELECT COUNT(*) as total FROM quick_queries");
    $count = mysqli_fetch_assoc($count_result)['total'];
    echo "Total records: <strong>$count</strong><br>";
    
} else {
    echo "❌ <strong style='color:red'>quick_queries table DOES NOT EXIST</strong><br>";
    echo "<p style='color:red'>You need to run the SQL setup!</p>";
}

echo "<hr>";

// Test 3: Check if contact_queries table exists
echo "<h2>Test 3: Contact Forms Table</h2>";
$test3 = mysqli_query($conn, "SHOW TABLES LIKE 'contact_queries'");
if (mysqli_num_rows($test3) > 0) {
    echo "✅ <strong style='color:green'>contact_queries table EXISTS</strong><br>";
    
    // Count records
    $count_result = mysqli_query($conn, "SELECT COUNT(*) as total FROM contact_queries");
    $count = mysqli_fetch_assoc($count_result)['total'];
    echo "Total records: <strong>$count</strong><br>";
    
} else {
    echo "❌ <strong style='color:red'>contact_queries table DOES NOT EXIST</strong><br>";
}

echo "<hr>";

// Test 4: Test a callback insert
echo "<h2>Test 4: Try Adding a Test Callback Request</h2>";
if (mysqli_num_rows($test1) > 0) {
    $test_insert = mysqli_query($conn, "INSERT INTO callback_requests (name, phone, status) VALUES ('Test User', '9999999999', 'pending')");
    if ($test_insert) {
        $test_id = mysqli_insert_id($conn);
        echo "✅ <strong style='color:green'>Successfully inserted test record with ID: $test_id</strong><br>";
        
        // Delete test record
        mysqli_query($conn, "DELETE FROM callback_requests WHERE id = '$test_id'");
        echo "✅ Test record deleted<br>";
    } else {
        echo "❌ <strong style='color:red'>Failed to insert: " . mysqli_error($conn) . "</strong><br>";
    }
} else {
    echo "⚠️ Skipped (table doesn't exist)<br>";
}

echo "<hr>";

// Test 5: Test a quick query insert
echo "<h2>Test 5: Try Adding a Test Quick Query</h2>";
if (mysqli_num_rows($test2) > 0) {
    $test_insert = mysqli_query($conn, "INSERT INTO quick_queries (name, email, message, status) VALUES ('Test User', 'test@example.com', 'Test message', 'unread')");
    if ($test_insert) {
        $test_id = mysqli_insert_id($conn);
        echo "✅ <strong style='color:green'>Successfully inserted test record with ID: $test_id</strong><br>";
        
        // Delete test record
        mysqli_query($conn, "DELETE FROM quick_queries WHERE id = '$test_id'");
        echo "✅ Test record deleted<br>";
    } else {
        echo "❌ <strong style='color:red'>Failed to insert: " . mysqli_error($conn) . "</strong><br>";
    }
} else {
    echo "⚠️ Skipped (table doesn't exist)<br>";
}

echo "<hr>";
echo "<h2>Summary & Fix</h2>";
$tables_exist = (mysqli_num_rows($test1) > 0) && (mysqli_num_rows($test2) > 0) && (mysqli_num_rows($test3) > 0);

// Check if user_id columns exist
$callback_has_user_id = false;
$quick_has_user_id = false;

if (mysqli_num_rows($test1) > 0) {
    $check = mysqli_query($conn, "SHOW COLUMNS FROM callback_requests WHERE Field = 'user_id'");
    $callback_has_user_id = (mysqli_num_rows($check) > 0);
}

if (mysqli_num_rows($test2) > 0) {
    $check = mysqli_query($conn, "SHOW COLUMNS FROM quick_queries WHERE Field = 'user_id'");
    $quick_has_user_id = (mysqli_num_rows($check) > 0);
}

$user_id_missing = $tables_exist && (!$callback_has_user_id || !$quick_has_user_id);

if ($tables_exist && !$user_id_missing) {
    echo "<p style='color:green; font-size:20px; font-weight:bold'>✅ ALL TABLES ARE CORRECT! Your database is ready!</p>";
    echo "<p>Everything should work now. Try submitting a callback or query!</p>";
} elseif ($user_id_missing) {
    echo "<p style='color:orange; font-size:20px; font-weight:bold'>⚠️ TABLES EXIST BUT user_id COLUMN IS MISSING!</p>";
    echo "<div style='background:#fff3cd; border:3px solid #ffc107; padding:20px; border-radius:8px; margin:20px 0;'>";
    echo "<h3 style='color:#856404;'>🔧 Quick Fix - Add user_id Column:</h3>";
    echo "<p style='color:#856404;'><strong>This is causing your 'Unknown column user_id' error!</strong></p>";
    echo "<ol style='color:#856404;'>";
    echo "<li>Go to <a href='http://localhost/phpmyadmin' target='_blank' style='color:#0056b3;'>phpMyAdmin</a></li>";
    echo "<li>Click '<strong>furrymart</strong>' database</li>";
    echo "<li>Click '<strong>SQL</strong>' tab</li>";
    echo "<li>Copy and paste this SQL:</li>";
    echo "</ol>";
    echo "<textarea style='width:100%; height:180px; font-family:monospace; padding:10px; border:2px solid #ffc107;'>";
    echo "ALTER TABLE `callback_requests` \n";
    echo "ADD COLUMN `user_id` int(11) DEFAULT NULL AFTER `id`,\n";
    echo "ADD KEY `user_id` (`user_id`);\n\n";
    echo "ALTER TABLE `quick_queries` \n";
    echo "ADD COLUMN `user_id` int(11) DEFAULT NULL AFTER `id`,\n";
    echo "ADD KEY `user_id` (`user_id`);";
    echo "</textarea>";
    echo "<p style='color:#856404; font-weight:bold; margin-top:10px;'>5. Click 'GO' button</p>";
    echo "<p style='color:#28a745; font-weight:bold; margin-top:15px;'>✅ This will fix the error and everything will work!</p>";
    echo "</div
echo "<hr>";
echo "<h2>Summary</h2>";
$all_good = (mysqli_num_rows($test1) > 0) && (mysqli_num_rows($test2) > 0) && (mysqli_num_rows($test3) > 0);

if ($all_good) {
    echo "<p style='color:green; font-size:20px; font-weight:bold'>✅ ALL TABLES EXIST! Your database is ready!</p>";
    echo "<p>If you're still getting errors, check:</p>";
    echo "<ul>";
    echo "<li>Your submit_callback_request.php file</li>";
    echo "<li>Your submit_quick_query.php file</li>";
    echo "<li>Browser console for JavaScript errors</li>";
    echo "</ul>";
} else {
    echo "<p style='color:red; font-size:20px; font-weight:bold'>❌ TABLES ARE MISSING!</p>";
    echo "<h3>Next Steps:</h3>";
    echo "<ol>";
    echo "<li>Go to <a href='http://localhost/phpmyadmin' target='_blank'>phpMyAdmin</a></li>";
    echo "<li>Click 'furrymart' database</li>";
    echo "<li>Click 'SQL' tab</li>";
    echo "<li>Copy and paste this SQL code:</li>";
    echo "</ol>";
    echo "<textarea style='width:100%; height:300px; font-family:monospace; padding:10px;'>";
    echo file_get_contents('RUN_THIS_SQL_NOW.sql');
    echo "</textarea>";
    echo "<p><strong>Then click GO button in phpMyAdmin!</strong></p>";
}

mysqli_close($conn);
?>
