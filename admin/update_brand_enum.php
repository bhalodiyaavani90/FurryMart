<?php
/**
 * Update Brand Category ENUM - Add New Categories
 */
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['admin_id'])) { header("Location: login.php"); exit(); }
include '../db.php';

echo "<!DOCTYPE html><html><head><title>Update Brand Categories ENUM</title>";
echo "<style>body{font-family:Arial;padding:40px;background:#f5f5f5;max-width:900px;margin:0 auto;}";
echo ".box{background:white;padding:30px;margin:20px 0;border-radius:12px;box-shadow:0 2px 10px rgba(0,0,0,0.1);}";
echo ".success{background:#d4edda;color:#155724;padding:15px;border-radius:8px;margin:10px 0;border-left:4px solid #28a745;}";
echo ".warning{background:#fff3cd;color:#856404;padding:15px;border-radius:8px;margin:10px 0;border-left:4px solid #ffc107;}";
echo ".error{background:#f8d7da;color:#721c24;padding:15px;border-radius:8px;margin:10px 0;border-left:4px solid #dc3545;}";
echo "h1{color:#0f1c3f;font-size:28px;}h2{color:#518992;font-size:20px;}";
echo "button{background:#518992;color:white;padding:15px 30px;border:none;border-radius:8px;cursor:pointer;margin:10px 0;font-size:16px;font-weight:bold;}";
echo "button:hover{background:#3d6a72;}";
echo "code{background:#f4f4f4;padding:3px 8px;border-radius:4px;font-family:monospace;color:#c7254e;}";
echo ".category-list{display:grid;grid-template-columns:1fr 1fr;gap:10px;margin:15px 0;}";
echo ".cat-item{background:#f8f9fa;padding:10px;border-radius:6px;border-left:3px solid #518992;}";
echo "</style></head><body>";

echo "<h1>🔧 Update Brand Categories ENUM</h1>";
echo "<div class='box'>";

// Check current ENUM values
$result = mysqli_query($conn, "SHOW COLUMNS FROM brands LIKE 'category'");
$row = mysqli_fetch_assoc($result);
$current_enum = $row['Type'];

echo "<h2>Current ENUM Values:</h2>";
echo "<code>" . htmlspecialchars($current_enum) . "</code>";

echo "<h2 style='margin-top:30px;'>New Categories to Add:</h2>";
echo "<div class='category-list'>";
echo "<div class='cat-item'>✅ Food (existing)</div>";
echo "<div class='cat-item'>✅ Grooming (existing)</div>";
echo "<div class='cat-item'>✅ Accessories (existing)</div>";
echo "<div class='cat-item'>🆕 Clothing (new)</div>";
echo "<div class='cat-item'>🆕 Beds and Mats (new)</div>";
echo "<div class='cat-item'>🆕 Bowls (new)</div>";
echo "<div class='cat-item'>🆕 Litter (new)</div>";
echo "</div>";

// If update button is clicked
if(isset($_POST['update_enum'])) {
    echo "<div class='warning'>⏳ Updating database structure...</div>";
    
    $sql = "ALTER TABLE brands MODIFY COLUMN category ENUM('Food', 'Grooming', 'Accessories', 'Clothing', 'Beds and Mats', 'Bowls', 'Litter') NOT NULL DEFAULT 'Food'";
    
    if(mysqli_query($conn, $sql)) {
        echo "<div class='success'>";
        echo "<strong>✅ SUCCESS!</strong><br>";
        echo "The category ENUM has been updated successfully!<br>";
        echo "You can now use all 7 categories: Food, Grooming, Accessories, Clothing, Beds and Mats, Bowls, and Litter.";
        echo "</div>";
        
        // Verify the change
        $verify = mysqli_query($conn, "SHOW COLUMNS FROM brands LIKE 'category'");
        $verify_row = mysqli_fetch_assoc($verify);
        echo "<h2>✅ Verified New ENUM Values:</h2>";
        echo "<code>" . htmlspecialchars($verify_row['Type']) . "</code>";
        
        echo "<br><br><a href='admin_brands.php' style='background:#28a745;color:white;padding:12px 25px;text-decoration:none;border-radius:8px;display:inline-block;'>Go to Brands Page →</a>";
        
    } else {
        echo "<div class='error'>";
        echo "<strong>❌ ERROR!</strong><br>";
        echo "Failed to update: " . mysqli_error($conn);
        echo "</div>";
    }
} else {
    echo "<form method='POST' onsubmit='return confirm(\"Are you sure you want to update the database structure?\");'>";
    echo "<button type='submit' name='update_enum'>🚀 Update Database ENUM Now</button>";
    echo "</form>";
    echo "<p style='color:#6c757d;font-size:14px;'><strong>Note:</strong> This will modify the database structure to allow the new categories. This is safe and won't affect existing data.</p>";
}

echo "</div>";
echo "<br><a href='admin_brands.php' style='color:#518992;text-decoration:none;font-weight:bold;'>← Back to Brands</a>";
echo "</body></html>";
?>
