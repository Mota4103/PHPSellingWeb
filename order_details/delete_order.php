<?php
session_start();
require_once("../connection.php");

if (isset($_GET['order_id'])) {
    $order_id = intval($_GET['order_id']);

    // First, delete related entries in the `relation` table to avoid foreign key constraint issues
    $delete_relation_query = "DELETE FROM relation WHERE orders_ID = $order_id";
    mysqli_query($db, $delete_relation_query);

    // Now delete the order from the `orders` table
    $delete_order_query = "DELETE FROM orders WHERE orders_ID = $order_id";
    if (mysqli_query($db, $delete_order_query)) {
        $_SESSION['message'] = "Order #$order_id was successfully deleted.";
    } else {
        $_SESSION['error'] = "Error deleting order: " . mysqli_error($db);
    }
}

// Redirect back to the order list page
header("Location: ../order");
exit;
?>