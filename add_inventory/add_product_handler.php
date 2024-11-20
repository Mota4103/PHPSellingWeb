<?php
session_start();
require("../connection.php");

// Check if user is logged in and has a valid session
if (isset($_SESSION['usertype'])) {
    if (!isset($_SESSION['id'])) {
        header("Location: ../login");
        exit();
    }
}

$seller_ID = $_SESSION['id'];

// Retrieve and sanitize form inputs
$product_name = $_POST['product_name']; // Assuming this is an integer
$product_price = floatval($_POST['product_price']); // Assuming this can be a decimal
$product_note = $_POST['product_note'];
$product_type = $_POST['product_type'] == "add_new_type" ? $_POST['new_type'] : $_POST['product_type'];


// Prepare the statement to avoid SQL injection
$stmt = $db->prepare("INSERT INTO production (product_name,  product_price, product_note, product_type,seller_ID) VALUES ( ?,  ?, ?, ?, ?)");

// Check if the prepare statement was successful
if ($stmt === false) {
    die("Error preparing statement: " . $db->error);
}

// Bind parameters
$stmt->bind_param("sdssi", $product_name,  $product_price, $product_note, $product_type, $seller_ID);

// Execute the statement
if ($stmt->execute()) {
    header("Location: ../inventory");
    exit();
} else {
    echo "Error inserting product: " . $stmt->error;
}

// Close the statement and database connection
$stmt->close();
$db->close();
?>
