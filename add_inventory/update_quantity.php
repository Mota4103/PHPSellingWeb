<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Update Product Quantity</title>
</head>
<?php
// Connect to the database
require("connections.php");

// Retrieve the product ID from the GET request
$product_id = isset($_GET['product_id']) ? (int)$_GET['product_id'] : 0;

// Initialize variables
$current_quantity = 0;

// Check if a valid product ID is provided
if ($product_id > 0) {
    // Fetch the current quantity for the product
    $query = "SELECT product_quantity FROM product WHERE product_ID='$product_id'";
    $result = mysqli_query($db, $query);
    
    if ($row = mysqli_fetch_assoc($result)) {
        $current_quantity = $row['product_quantity'];
    } else {
        echo "<p style='color: red;'>Product not found.</p>";
    }
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve form data
    $quantity_to_modify = (int)$_POST['quantity_to_modify'];
    $operation = $_POST['operation'];
    $product_id = (int)$_POST['product_id'];

    // Check if the product exists in the database
    $query = "SELECT product_quantity FROM product WHERE product_ID='$product_id'";
    $result = mysqli_query($db, $query);

    if ($row = mysqli_fetch_assoc($result)) {
        // Calculate the new quantity based on the operation
        if ($operation === 'add') {
            $new_quantity = $row['product_quantity'] + $quantity_to_modify;
        } elseif ($operation === 'subtract') {
            $new_quantity = $row['product_quantity'] - $quantity_to_modify;
        }

        // Update the product quantity in the database
        $update_query = "UPDATE product SET product_quantity='$new_quantity' WHERE product_ID='$product_id'";
        if (mysqli_query($db, $update_query)) {
            // Success - Redirect to inventory.php
            header("Location: ../inventory");
            exit(); // Make sure to call exit after the header redirect
        } else {
            echo "<p style='color: red;'>Error updating quantity: " . mysqli_error($db) . "</p>";
        }
    } else {
        echo "<p style='color: red;'>Product not found.</p>";
    }
}
?>
<body>
<h2>Update Product Quantity</h2><br>

<form action="update_quantity.php" method="post">
    <table border="0">
        <tr>
            <td>Product ID</td>
            <td><input type="number" name="product_id" value="<?php echo $product_id; ?>" readonly required></td>
        </tr>
        <tr>
            <td>Current Quantity</td>
            <td><input type="number" value="<?php echo $current_quantity; ?>" readonly></td>
        </tr>
        <tr>
            <td>Quantity to Modify</td>
            <td><input type="number" name="quantity_to_modify" required></td>
        </tr>
    </table>
    
    <!-- Add and Subtract buttons with different operations -->
    <button type="submit" name="operation" value="add">+</button>
    <button type="submit" name="operation" value="subtract">-</button>    
</form>
<br>
<a href="inventory.php">
    <button>Back to Inventory</button>
</a>

</body>
</html>

