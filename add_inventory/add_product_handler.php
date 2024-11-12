<?php
session_start();
require("../connection.php");

if (!isset($_SESSION['seller_ID'])) {
    header("Location: ../login.php");
    exit();
}

$seller_ID = $_SESSION['seller_ID'];
$product_name = $_POST['product_name'];
$product_quantity = $_POST['product_quantity'];
$product_price = $_POST['product_price'];
$product_note = $_POST['product_note'];
$product_type = $_POST['product_type'] == "add_new_type" ? $_POST['new_type'] : $_POST['product_type'];
$product_sold = 0;
$last_update_time = date("Y-m-d H:i:s");

// Prepare the statement to avoid SQL injection
$stmt = $db->prepare("INSERT INTO product (product_name, product_quantity, product_price, product_note, product_type, product_sold, product_update_time, seller_ID) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("sidsisis", $product_name, $product_quantity, $product_price, $product_note, $product_type, $product_sold, $last_update_time, $seller_ID);

if ($stmt->execute()) {
    header("Location: ../inventory");
    exit();
} else {
    echo "Error inserting product: " . $stmt->error;
}

$stmt->close();
$db->close();
?>
