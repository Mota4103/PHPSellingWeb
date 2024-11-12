<?php
require_once("../connection.php");

$staff_username = $_POST['staff_username'];

// Insert data into orders table
$query = "DELETE FROM staff WHERE staff_username='$staff_username'";

if (mysqli_query($db, $query)) {
    echo "Order added successfully!";
    header('Location: ../adjust_staff');
    exit();
} else {
    echo "Error: " . mysqli_error($db);
    
}

mysqli_close($db);
?>