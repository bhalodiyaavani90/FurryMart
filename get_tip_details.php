<?php
include 'db.php'; // Adjust path to your db connection

if(isset($_GET['id'])) {
    $id = mysqli_real_escape_string($conn, $_GET['id']);
    
    $sql = "SELECT * FROM expert_tips WHERE id = '$id'";
    $result = mysqli_query($conn, $sql);
    
    if($row = mysqli_fetch_assoc($result)) {
        // Return data as JSON
        echo json_encode($row);
    } else {
        echo json_encode(null);
    }
}
?>