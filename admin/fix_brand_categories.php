<?php
/**
 * ONE-TIME FIX: Update brands without categories
 */
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['admin_id'])) { header("Location: login.php"); exit(); }
include '../db.php';

echo "<!DOCTYPE html><html><head><title>Fix Brand Categories</title>";
echo "<style>body{font-family:Arial;padding:40px;background:#f5f5f5;}";
echo ".brand-item{background:white;padding:20px;margin:10px 0;border-radius:8px;border-left:4px solid #518992;}";
echo ".success{color:green;}.warning{color:orange;}.error{color:red;}";
echo "h1{color:#0f1c3f;}button{background:#518992;color:white;padding:10px 20px;border:none;border-radius:5px;cursor:pointer;margin:10px 0;}";
echo "</style></head><body>";

echo "<h1>🔧 Brand Category Fixer</h1>";
echo "<p>This page will help fix brands that don't have categories assigned.</p>";

// Check for brands without categories
$query = "SELECT * FROM brands WHERE category IS NULL OR category = ''";
$result = mysqli_query($conn, $query);
$count = mysqli_num_rows($result);

if($count == 0) {
    echo "<div class='success'>✅ All brands have categories! No fixes needed.</div>";
} else {
    echo "<div class='warning'>⚠️ Found $count brands without categories</div>";
    
    // If fix button is clicked
    if(isset($_POST['fix_all'])) {
        $fixed = 0;
        mysqli_query($conn, "UPDATE brands SET category='Food' WHERE brand_name IN ('Royal Canin', 'Pedigree', 'Orijen', 'Applod', 'Sheba', 'Fresh For Paws', 'Whiskas')");
        $fixed += mysqli_affected_rows($conn);
        
        mysqli_query($conn, "UPDATE brands SET category='Grooming' WHERE brand_name IN ('Hachi Wilson', 'Pet Head', 'Tail Over Paws')");
        $fixed += mysqli_affected_rows($conn);
        
        mysqli_query($conn, "UPDATE brands SET category='Accessories' WHERE brand_name IN ('Joyser', 'Petova', 'Neater Pets')");
        $fixed += mysqli_affected_rows($conn);
        
        echo "<div class='success'>✅ Fixed $fixed brands!</div>";
        echo "<script>setTimeout(function(){ window.location.href='admin_brands.php'; }, 2000);</script>";
    } else {
        // Show brands that need fixing
        echo "<h3>Brands needing categories:</h3>";
        mysqli_data_seek($result, 0);
        while($brand = mysqli_fetch_assoc($result)) {
            echo "<div class='brand-item'>";
            echo "<strong>" . $brand['brand_name'] . "</strong> (ID: " . $brand['id'] . ")<br>";
            echo "<small>Current category: <span class='error'>" . ($brand['category'] ?: 'EMPTY') . "</span></small>";
            echo "</div>";
        }
        
        echo "<form method='POST'>";
        echo "<button type='submit' name='fix_all'>🔧 Auto-Fix All Categories</button>";
        echo "</form>";
        echo "<p><small>This will assign appropriate categories based on brand names.</small></p>";
    }
}

echo "<br><a href='admin_brands.php' style='color:#518992;text-decoration:none;'>← Back to Brands</a>";
echo "</body></html>";
?>
