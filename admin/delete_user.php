<?php
session_start();
require_once('../db.php');

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "DELETE FROM users WHERE id = $id";

    if (mysqli_query($conn, $sql)) {
        header("Location: admin_users.php?msg=Deleted");
    } else {
        echo "Error deleting record: " . mysqli_error($conn);
    }
}
?>