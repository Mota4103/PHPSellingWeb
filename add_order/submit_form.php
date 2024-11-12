<?php
require_once("../connection.php");
session_start();

$seller_id = $_SESSION['id'];
$customer_name = $_POST['customer_name'];
$customer_address = $_POST['customer_address'];
$customer_channel = $_POST['customer_channel'];
$order_date = $_POST['order_date'];
$orders_paid = $_POST['orders_paid'];  // Capture the entered paid amount

// Handle new channel if added
if ($customer_channel === "add_new" && !empty($_POST['new_channel'])) {
    $customer_channel = $_POST['new_channel'];
}

// Handle new paid status if added
$paid_status = $_POST['paid_status'];
if ($paid_status === "add_new" && !empty($_POST['new_paid_status'])) {
    $paid_status = $_POST['new_paid_status'];
}

// Insert data into orders table
$query = "INSERT INTO orders (orders_seller, customer_name, customer_address, customer_channel, orders_date, orders_paid, orders_paid_status)
          VALUES ('$seller_id', '$customer_name', '$customer_address', '$customer_channel', '$order_date', '$orders_paid', '$paid_status')";

if (mysqli_query($db, $query)) {
    echo "Order added successfully!";
    header('Location: ../order');
    exit();
} else {
    echo "Error: " . mysqli_error($db);
    
}

mysqli_close($db);
?>
