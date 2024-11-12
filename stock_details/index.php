<?php
require_once("../connection.php");
session_start();

// Get the product_ID from the URL
$product_ID = isset($_GET['product_ID']) ? intval($_GET['product_ID']) : 0;

// Fetch product details from the database
$product_query = "
    SELECT 
        product_ID, 
        product_name, 
        product_price, 
        product_type, 
        product_sold, 
        product_note, 
        seller_ID
    FROM 
        product 
    WHERE 
        product_ID = $product_ID";
$product_result = mysqli_query($db, $product_query);

if (!$product_result) {
    die("Error fetching product details: " . mysqli_error($db));
}

$product = mysqli_fetch_assoc($product_result);

// Fetch combined stock and order details for the product, ordered by date
$combined_query = "
    SELECT 
        'Order' AS type, 
        o.orders_ID AS id, 
        -r.orders_quantity AS quantity, 
        o.orders_date AS date
    FROM 
        orders o
    JOIN 
        relation r ON o.orders_ID = r.orders_ID
    WHERE 
        r.product_ID = $product_ID
    UNION ALL
    SELECT 
        so.type, 
        so.stockorder_ID AS id, 
        so.quantity, 
        so.stockorder_date
    FROM 
        stockorder so
    WHERE 
        so.product_ID = $product_ID
    ORDER BY date DESC";
$combined_result = mysqli_query($db, $combined_query);

if (!$combined_result) {
    die("Error fetching stock and order details: " . mysqli_error($db));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Product #<?php echo $product_ID; ?> Details</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 p-10">
    <div class="max-w-4xl mx-auto bg-white p-6 rounded-lg shadow-lg">
        <h2 class="text-2xl font-semibold mb-4">Product #<?php echo $product_ID; ?> Details</h2>

        <?php if ($product): ?>
            <p><strong>Product Name:</strong> <?php echo htmlspecialchars($product['product_name']); ?></p>
            <p><strong>Product Type:</strong> <?php echo htmlspecialchars($product['product_type']); ?></p>
            <p><strong>Price:</strong> <?php echo htmlspecialchars($product['product_price']); ?></p>
            <p><strong>Quantity Sold:</strong> <?php echo htmlspecialchars($product['product_sold']); ?></p>
            <p><strong>Seller ID:</strong> <?php echo htmlspecialchars($product['seller_ID']); ?></p>
            <p><strong>Note:</strong> <?php echo htmlspecialchars($product['product_note']); ?></p>

            <h3 class="mt-6 text-xl font-semibold">Stock Details</h3>
            <?php if (mysqli_num_rows($combined_result) > 0): ?>
                <table class="mt-4 w-full border-collapse border border-gray-300">
                    <thead class="bg-gray-200">
                        <tr>
                            <th class="border border-gray-300 px-4 py-2">Type</th>
                            <th class="border border-gray-300 px-4 py-2">ID</th>
                            <th class="border border-gray-300 px-4 py-2">Quantity</th>
                            <th class="border border-gray-300 px-4 py-2">Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($entry = mysqli_fetch_assoc($combined_result)): ?>
                            <tr>
                                <td class="border border-gray-300 px-4 py-2"><?php echo htmlspecialchars($entry['type']); ?></td>
                                <td class="border border-gray-300 px-4 py-2"><?php echo htmlspecialchars($entry['id']); ?></td>
                                <td class="border border-gray-300 px-4 py-2"><?php echo htmlspecialchars($entry['quantity']); ?></td>
                                <td class="border border-gray-300 px-4 py-2"><?php echo htmlspecialchars($entry['date']); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="mt-4 text-gray-600">No stock records or orders for this product.</p>
            <?php endif; ?>

            <a href="../inventory" class="mt-6 inline-block bg-blue-600 text-white px-4 py-2 rounded-md">
                Back to Inventory
            </a>
            <a href="../add_stock_order?product_ID=<?php echo $product_ID; ?>" class="mt-4 inline-block bg-green-500 text-white px-4 py-2 rounded-md">
                Add New Stock
            </a>
        <?php else: ?>
            <p class="text-red-600">Product not found.</p>
        <?php endif; ?>
    </div>
</body>
</html>
