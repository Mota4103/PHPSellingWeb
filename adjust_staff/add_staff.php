<?php
require_once("../connection.php");

$seller_id = explode(' ',$_POST['seller_id'],0)[0];
$staff_username = $_POST['staff_username'];
$staff_password = $_POST['staff_password'];

// Insert data into orders table
$query = "INSERT INTO staff (staff_id, staff_username, staff_password, seller_id)
          VALUES (NULL, '$staff_username', '$staff_password', '$seller_id')";

if (mysqli_query($db, $query)) {
    echo "Order added successfully!";
    header('Location: ../adjust_staff');
    exit();
} else {
    echo "Error: " . mysqli_error($db);
    
}

mysqli_close($db);
?>