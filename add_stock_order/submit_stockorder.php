<?php
require_once("../connection.php");
session_start();

// Check if the form is submitted via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get seller ID from session
    $seller_id = $_SESSION['id'];

    // Get form data
    $product_id = $_POST['product_id'] ?? null;
    $type = $_POST['type'] ?? null;
    $quantity = $_POST['quantity'] ?? null;
    $stockorder_date = $_POST['stockorder_date'] ?? null;

    // Check if all required fields are provided
    if ($product_id && $type && $quantity && $stockorder_date) {
        // Prepare a simple insert query
        $query = "INSERT INTO stockorder (stockorder_ID,type, quantity, stockorder_date, product_ID) 
            VALUES (NULL,'$type', '$quantity', '$stockorder_date', '$product_id')";

        // Execute the query
        if (mysqli_query($db, $query)) {
            // Success message
            $_SESSION['message'] = "Stock order added successfully!";
        } else {
            // Error message
            $_SESSION['error'] = "Error: Could not add the stock order. Please try again.";
        }
    } else {
        $_SESSION['error'] = "Please fill in all required fields.";
    }

    // Redirect back to the stock order list
    header("Location: ../stock_details?product_ID=$product_id");
    exit();
}
?>
