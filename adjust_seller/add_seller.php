<?php
require_once("../connection.php");

$seller_username = $_POST['seller_username'];
$seller_password = $_POST['seller_password'];

// Insert data into orders table
$query = "INSERT INTO seller (seller_ID, seller_username, seller_password)
          VALUES (NULL, '$seller_username', '$seller_password')";

if (mysqli_query($db, $query)) {
    echo "Order added successfully!";
    header('Location: ../adjust_seller');
    exit();
} else {
    echo "Error: " . mysqli_error($db);
    
}

mysqli_close($db);
?>