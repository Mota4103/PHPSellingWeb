<?php
session_start();
require_once("../connection.php");

// AJAX handler to fetch products based on seller_username
if (isset($_POST['fetch_products']) && !empty($_POST['seller_username'])) {
    $seller_username = mysqli_real_escape_string($db, $_POST['seller_username']);
    
    // Temporarily set seller_id for testing
    $seller_id = 1; // Use an actual seller ID that exists in your database for this test

    $product_query = "SELECT * FROM product WHERE seller_ID = $seller_id ORDER BY product_name ASC";
    $product_result = mysqli_query($db, $product_query);

    // Display the table if the product query is successful
    if ($product_result && mysqli_num_rows($product_result) > 0) {
        // Start the table based on ../inventory layout
        echo '<table class="table-auto w-full bg-white shadow-md rounded mt-6">
                <thead>
                    <tr class="text-left bg-gray-200">
                        <th class="py-2 px-4">Product Name</th>
                        <th class="py-2 px-4">Product Type</th>
                        <th class="py-2 px-4">Quantity</th>
                        <th class="py-2 px-4">Price</th>
                        <th class="py-2 px-4">Sold</th>
                        <th class="py-2 px-4">Update Time</th>
                        <th class="py-2 px-4">Actions</th>
                    </tr>
                </thead>
                <tbody>';
        
        // Display each product in table rows
        while ($product = mysqli_fetch_assoc($product_result)) {
            echo '<tr>
                    <td class="py-2 px-4">' . htmlspecialchars($product['product_name']) . '</td>
                    <td class="py-2 px-4">' . htmlspecialchars($product['product_type']) . '</td>
                    <td class="py-2 px-4">' . htmlspecialchars($product['product_quantity']) . '</td>
                    <td class="py-2 px-4">' . htmlspecialchars($product['product_price']) . '</td>
                    <td class="py-2 px-4">' . htmlspecialchars($product['product_sold']) . '</td>
                    <td class="py-2 px-4">' . htmlspecialchars($product['product_update_time']) . '</td>
                    <td class="py-2 px-4">
                        <button onclick="deleteProduct(' . $product['product_ID'] . ')" class="px-4 py-1 bg-red-500 text-white rounded hover:bg-red-600">Delete</button>
                    </td>
                  </tr>';
        }
        
        // End the table
        echo '</tbody></table>';
    } else {
        echo "<p>No products found for this seller ID.</p>";
    }
    exit;
}

// AJAX handler for deleting a product
if (isset($_POST['delete']) && !empty($_POST['product_ID'])) {
    $product_id = intval($_POST['product_ID']);
    $delete_query = "DELETE FROM product WHERE product_ID = $product_id";
    mysqli_query($db, $delete_query);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Delete Inventory</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body class="bg-gray-100">
    <div class="max-w-7xl mx-auto p-6">
        <h1 class="text-3xl font-semibold mb-6">Delete Inventory</h1>

        <!-- Back to Home Button -->
        <a href="../home" class="mb-6 inline-block">
            <button class="px-6 py-2 bg-blue-500 text-white rounded shadow hover:bg-blue-600">
                Back to Home
            </button>
        </a>

        <!-- Search by seller_username -->
        <form id="searchForm" class="mb-6">
            <label class="block text-sm font-medium text-gray-700">Enter Seller Username:</label>
            <div class="flex items-center mt-1">
                <input type="text" id="seller_username" name="seller_username"
                       class="flex-1 px-4 py-2 border border-gray-300 rounded-l focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                       placeholder="Seller Username" required>
                <button type="button" id="searchBtn"
                        class="px-4 py-2 bg-blue-500 text-white rounded-r hover:bg-blue-600">Search</button>
            </div>
        </form>

        <!-- Products Display Area -->
        <div id="productTable"></div>
    </div>

    <script>
        // AJAX function to fetch and display products
        $('#searchBtn').on('click', function() {
            const sellerUsername = $('#seller_username').val();
            $.post('delete_inventory.php', { fetch_products: true, seller_username: sellerUsername }, function(data) {
                $('#productTable').html(data); // Display product table below search box
            });
        });

        // Function to delete a product
        function deleteProduct(productID) {
            if (confirm('Are you sure you want to delete this product?')) {
                $.post('delete_inventory.php', { delete: true, product_ID: productID }, function() {
                    $('#searchBtn').click(); // Refresh product table after deletion
                });
            }
        }
    </script>
</body>
</html>

