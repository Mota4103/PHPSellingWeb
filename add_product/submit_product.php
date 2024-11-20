<?php
require_once("../connection.php");
session_start();

function debug_to_console($data) {
    $output = is_array($data) ? implode(',', $data) : $data;
    echo "<script>console.log('Debug: " . addslashes($output) . "');</script>";
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ensure session ID and form inputs are valid
    if (!isset($_SESSION['id']) || !isset($_POST['order_id']) || !isset($_POST['product_name']) || !isset($_POST['quantity'])) {
        echo "<p>Invalid input. Please try again.</p>";
        exit();
    }

    $order_id = intval($_POST['order_id']);
    $product_ID = intval($_POST['product_id']);
    $seller_id = intval($_SESSION['id']);
    $product_name = mysqli_real_escape_string($db, $_POST['product_name']);
    $product_quantity = intval($_POST['quantity']);

    // Retrieve the product ID based on the product name and seller ID
    $product_query = "SELECT product_ID FROM production WHERE product_name = '$product_name' AND seller_ID = $seller_id";
    debug_to_console($product_query);
    
    $product_result = mysqli_query($db, $product_query);

    if ($product_result && mysqli_num_rows($product_result) > 0) {
        $product_data = mysqli_fetch_assoc($product_result);

        debug_to_console($product_ID);
        // Insert the new product into the order-product relationship table
        $insert_query = "INSERT INTO relation2 (orders_ID, product_ID, orders_quantity) VALUES ($order_id, $product_ID, $product_quantity)";

        if (mysqli_query($db, $insert_query)) {
            echo "<p>Product added successfully.</p>";
        } else {
            echo "<p>Error: Could not add product. " . mysqli_error($db) . "</p>";
        }

        // Optionally, redirect back to the add_product page
        header("Location: ../add_product?order_id=$order_id");
        exit();
    } else {
        echo "<p>Error: Product not found for the current seller.</p>";
    }
} else {
    echo "<p>Invalid request method.</p>";
}
?>
