<!DOCTYPE html>
<?php
require_once("../connection.php");
session_start();

$product_ID = isset($_GET['product_ID']) ? intval($_GET['product_ID']) : 0;
$seller_id = $_SESSION['id'];

// Fetch the selected product details for the seller
$product_query = "SELECT product_name FROM product WHERE product_ID = $product_ID AND seller_ID = $seller_id";
$product_result = mysqli_query($db, $product_query);
$product = mysqli_fetch_assoc($product_result);

// If no valid product is found, redirect back with an error
if (!$product) {
    $_SESSION['error'] = "Product not found or not accessible.";
    header("Location: ../stock_details");
    exit();
}
?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add to Stock Order</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 p-10">
    <div class="max-w-md mx-auto bg-white p-6 rounded-lg shadow-lg">
        <h2 class="text-2xl font-semibold mb-4">Add to Stock Order</h2>

        <!-- Display Selected Product Information -->
        <div class="mb-4">
            <p><strong>Product ID:</strong> <?php echo htmlspecialchars($product_ID); ?></p>
            <p><strong>Product Name:</strong> <?php echo htmlspecialchars($product['product_name']); ?></p>
        </div>

        <form action="./submit_stockorder.php" method="POST">
            <!-- Hidden Product ID Field -->
            <input type="hidden" name="product_id" value="<?php echo $product_ID; ?>">

            <!-- Type Selection -->
            <div class="mb-4">
                <label for="type" class="block text-sm font-medium text-gray-700">Type</label>
                <select id="type" name="type" 
                        class="mt-1 block w-full p-2 border border-gray-300 rounded-md" required>
                    <option value="New Stock">New Stock</option>
                    <option value="Expired">Expired</option>
                    <option value="Damaged">Damaged</option>
                    <option value="Returned">Returned</option>
                    <option value="Other">Other</option>
                </select>
            </div>

            <!-- Quantity Input -->
            <div class="mb-4">
                <label for="quantity" class="block text-sm font-medium text-gray-700">Quantity</label>
                <input type="number" id="quantity" name="quantity" 
                       class="mt-1 block w-full p-2 border border-gray-300 rounded-md" 
                       placeholder="Enter quantity" required>
            </div>

            <!-- Date Input -->
            <div class="mb-4">
                <label for="stockorder_date" class="block text-sm font-medium text-gray-700">Date</label>
                <input type="date" id="stockorder_date" name="stockorder_date" 
                       class="mt-1 block w-full p-2 border border-gray-300 rounded-md" required>
            </div>

            <button type="submit" class="w-full py-2 bg-blue-600 text-white font-semibold rounded-md hover:bg-blue-700">
                Add to Stock Order
            </button>
        </form>

        <a href="../stock_details?product_ID=<?php echo $product_ID; ?>" class="mt-4 inline-block text-blue-600 hover:underline">Back to Stock Order List</a>
    </div>
</body>
</html>
