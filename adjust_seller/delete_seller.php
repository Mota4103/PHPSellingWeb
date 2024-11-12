<?php
require_once("../connection.php");

$seller_username = $_POST['seller_username'];

// Insert data into orders table
$query = "DELETE FROM seller WHERE seller_username='$seller_username'";

if (mysqli_query($db, $query)) {
    echo "Order added successfully!";
    header('Location: ../adjust_seller');
    exit();
} else {
    echo "Error: " . mysqli_error($db);
    
}

mysqli_close($db);
?>