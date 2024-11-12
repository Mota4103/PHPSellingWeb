<?php
require_once("../connection.php");
session_start();

function debug_to_console($data) {
    $output = $data;
    if (is_array($output)) {
        $output = implode(',', $output);
    }
    echo "<script>console.log('Debug: " . $output . "');</script>";
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $order_id = intval($_POST['order_id']);
    $seller_id = intval($_SESSION['id']);  // Ensure session ID is an integer for security
    $product_name = mysqli_real_escape_string($db, $_POST['product_name']);  // Escape special characters in product name
    $product_quantity = intval($_POST['quantity']);  // Ensure quantity is an integer

    // Retrieve the product ID based on the product name
    $product_query = "SELECT product_ID FROM product WHERE product_name = '$product_name' AND seller_ID = $seller_id";
    $product_result = mysqli_query($db, $product_query);

    debug_to_console($product_query);

    if ($product_result && mysqli_num_rows($product_result) > 0) {
        $product_data = mysqli_fetch_assoc($product_result);
        $product_ID = $product_data['product_ID'];

        // Insert the new product into the order-product relationship table
        $insert_query = "INSERT INTO relation (relation_ID, orders_id, product_ID, orders_quantity) 
                         VALUES (NULL, $order_id, $product_ID, $product_quantity)";

        if (mysqli_query($db, $insert_query)) {
            echo "<p>Product added successfully.</p>";
        } else {
            echo "<p>Error: Could not add product.</p>";
        }

        // Redirect back to add_product.php
        header("Location: ../add_product?order_id=$order_id");
        exit();
    } else {
        echo "<p>Error: Product not found.</p>";
    }
} else {
    echo "<p>Invalid request.</p>";
}
?>
