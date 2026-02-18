<?php
// Fix life_stage ENUM to include Kitten for Cat products
include "../db.php";

echo "<h2>Fixing life_stage column to support Kitten...</h2>";

// Alter the products table to allow 'Kitten' in life_stage ENUM
$sql = "ALTER TABLE products MODIFY COLUMN life_stage ENUM('Puppy', 'Kitten', 'Adult', 'Senior') NOT NULL";

if(mysqli_query($conn, $sql)) {
    echo "<p style='color: green; font-size: 18px;'>✅ SUCCESS! life_stage column now supports: Puppy, Kitten, Adult, Senior</p>";
    echo "<p>You can now add Cat products with 'Kitten' life stage!</p>";
    echo "<p><a href='admin_products.php'>← Go back to Products</a></p>";
} else {
    echo "<p style='color: red;'>❌ ERROR: " . mysqli_error($conn) . "</p>";
    echo "<p>Please run this SQL manually in phpMyAdmin:</p>";
    echo "<pre style='background: #f5f5f5; padding: 15px; border-radius: 5px;'>";
    echo "ALTER TABLE products MODIFY COLUMN life_stage ENUM('Puppy', 'Kitten', 'Adult', 'Senior') NOT NULL;";
    echo "</pre>";
}

mysqli_close($conn);
?>
