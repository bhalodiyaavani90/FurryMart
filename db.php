<?php
$conn = mysqli_connect("localhost","root","","furrymart");
if(!$conn){
    die("Database Connection Failed");
}

// Set MySQL timezone to India Standard Time
mysqli_query($conn, "SET time_zone = '+05:30'");

?>
