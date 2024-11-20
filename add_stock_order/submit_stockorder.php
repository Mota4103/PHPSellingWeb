<?php
require_once("../connection.php");
session_start();

// Debugging function to output data to the console
function debug_to_console($data) {
    $output = is_array($data) ? implode(',', $data) : $data;
    echo "<script>console.log('Debug: " . $output . "');</script>";
}

// Check if the form is submitted via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get seller ID from session
    $seller_id = $_SESSION['id'];

    // Get form data
    $product_id = trim($_POST['product_id']) ?? null;
    $type = $_POST['type'] ?? null;
    $quantity = $_POST['quantity'] ?? null;
    $stockorder_date = $_POST['stockorder_date'] ?? null;

    // Debugging: Log form data to console
    debug_to_console("Product ID: " . $product_id);
    debug_to_console("Type: " . $type);
    debug_to_console("Quantity: " . $quantity);
    debug_to_console("Stock Order Date: " . $stockorder_date);

    // Check if all required fields are provided
    if ($product_id && $type && $quantity && $stockorder_date) {
        // Verify that the product exists in the database
        $verify_query = "SELECT * FROM production WHERE product_ID = ?";
        
        // Prepare the statement to prevent SQL injection
        if ($verify_stmt = mysqli_prepare($db, $verify_query)) {
            // Bind the product ID as an integer
            mysqli_stmt_bind_param($verify_stmt, "i", $product_id);
            mysqli_stmt_execute($verify_stmt);
            $verify_result = mysqli_stmt_get_result($verify_stmt);

            if (mysqli_num_rows($verify_result) === 0) {
                $_SESSION['error'] = "Product not found in the database.";
                debug_to_console("Product not found in the database.");
                mysqli_stmt_close($verify_stmt);
                exit();
            }
            mysqli_stmt_close($verify_stmt);
        } else {
            $_SESSION['error'] = "Error: Could not prepare the verification SQL statement.";
            debug_to_console("Error: Could not prepare the verification SQL statement.");
            exit();
        }

        // Prepare the SQL query for inserting the stock order
        $query = "INSERT INTO stockorder2 (stockorder_ID, type, quantity, stockorder_date, product_ID) 
                  VALUES (NULL, ?, ?, ?, ?)";

        // Prepare the statement
        if ($stmt = mysqli_prepare($db, $query)) {
            // Bind parameters: 'type' and 'stockorder_date' as strings, 'quantity' and 'product_id' as integers
            mysqli_stmt_bind_param($stmt, "sisi", $type, $quantity, $stockorder_date, $product_id);

            // Execute the statement
            if (mysqli_stmt_execute($stmt)) {
                $_SESSION['message'] = "Stock order added successfully!";
                debug_to_console("Stock order added successfully!");
            } else {
                $_SESSION['error'] = "Error executing statement: " . mysqli_stmt_error($stmt);
                debug_to_console("Error executing statement: " . mysqli_stmt_error($stmt));
            }

            // Close the statement
            mysqli_stmt_close($stmt);
        } else {
            $_SESSION['error'] = "Error: Could not prepare the insert SQL statement.";
            debug_to_console("Error: Could not prepare the insert SQL statement.");
        }
    } else {
        $_SESSION['error'] = "Please fill in all required fields.";
        debug_to_console("Please fill in all required fields.");
    }

    // Redirect back to the stock order list or other relevant page
    header("Location: ../stock_details?product_ID=$product_id");
    exit();
}
?>
