<!DOCTYPE html>
<?php
require_once("../connection.php");
session_start();

// Get the order_id from the URL
$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;

// Fetch order details with customer information and total order price
$order_query = "
    SELECT 
        o.orders_ID, 
        o.customer_name, 
        o.customer_channel, 
        o.customer_address, 
        o.orders_paid, 
        o.orders_paid_status, 
        o.orders_date, 
        SUM(r.orders_quantity * p.product_price) AS total_order_price
    FROM 
        orders o
    LEFT JOIN 
        relation r ON o.orders_ID = r.orders_ID
    LEFT JOIN 
        product p ON r.product_ID = p.product_ID
    WHERE 
        o.orders_ID = $order_id
    GROUP BY 
        o.orders_ID";
$order_result = mysqli_query($db, $order_query);
$order = mysqli_fetch_assoc($order_result);

// Fetch products assigned to this order, with total price per product
$product_query = "
    SELECT 
        p.product_ID, 
        p.product_name, 
        r.orders_quantity, 
        p.product_price, 
        (r.orders_quantity * p.product_price) AS total_price_per_product
    FROM 
        product p
    JOIN 
        relation r ON p.product_ID = r.product_ID
    WHERE 
        r.orders_ID = $order_id";
$product_result = mysqli_query($db, $product_query);

?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order #<?php echo $order_id; ?> Details</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 p-10">
    <div class="max-w-4xl mx-auto bg-white p-6 rounded-lg shadow-lg">
        <h2 class="text-2xl font-semibold mb-4">Order #<?php echo $order_id; ?> Details</h2>

        <?php if ($order): ?>
            <p><strong>Customer Name:</strong> <?php echo htmlspecialchars($order['customer_name']); ?></p>
            <p><strong>Channel:</strong> <?php echo htmlspecialchars($order['customer_channel']); ?></p>
            <p><strong>Address:</strong> <?php echo htmlspecialchars($order['customer_address']); ?></p>
            <p><strong>Order Date:</strong> <?php echo htmlspecialchars($order['orders_date']); ?></p>
            <p><strong>Paid Status:</strong> <?php echo htmlspecialchars($order['orders_paid_status']); ?></p>
            <p><strong>Payment Amount:</strong> <?php echo htmlspecialchars($order['orders_paid']); ?></p>
            
            <h3 class="mt-6 text-xl font-semibold">Assigned Products</h3>
            <?php if (mysqli_num_rows($product_result) > 0): ?>
                <table class="mt-4 w-full border-collapse border border-gray-300">
                    <thead class="bg-gray-200">
                        <tr>
                            <th class="border border-gray-300 px-4 py-2">Product Name</th>
                            <th class="border border-gray-300 px-4 py-2">Quantity</th>
                            <th class="border border-gray-300 px-4 py-2">Price</th>
                            <th class="border border-gray-300 px-4 py-2">Total Price</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($product = mysqli_fetch_assoc($product_result)): ?>
                            <tr>
                                <td class="border border-gray-300 px-4 py-2"><?php echo htmlspecialchars($product['product_name']); ?></td>
                                <td class="border border-gray-300 px-4 py-2"><?php echo htmlspecialchars($product['orders_quantity']); ?></td>
                                <td class="border border-gray-300 px-4 py-2"><?php echo htmlspecialchars($product['product_price']); ?></td>
                                <td class="border border-gray-300 px-4 py-2"><?php echo htmlspecialchars(number_format($product['total_price_per_product'], 2)); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
                
                <!-- Display Total Order Price -->
                <p class="mt-4 text-xl font-semibold">Total Order Price: <?php echo number_format($order['total_order_price'], 2); ?></p>
            <?php else: ?>
                <p class="mt-4 text-gray-600">No products assigned to this order yet.</p>
            <?php endif; ?>

            <a href="../add_product?order_id=<?php echo $order_id; ?>" class="mt-6 inline-block bg-blue-600 text-white px-4 py-2 rounded-md">
                Add Product to Order
            </a>
            <!-- Go Back Link -->
            <a href="../order" class="mt-4 inline-block bg-gray-600 text-white px-4 py-2 rounded-md">
                Go Back 
            </a>
            <a href="./edit_paid.php?order_id=<?php echo $order_id; ?>" class="mt-4 inline-block bg-yellow-500 text-white px-4 py-2 rounded-md">
                Edit Payment
            </a>
            <a href="./delete_order.php?order_id=<?php echo $order_id; ?>" 
                onclick="return confirm('Are you sure you want to delete this order? This action cannot be undone.');"
                class="mt-4 inline-block bg-red-600 text-white px-4 py-2 rounded-md">
                Delete Order
            </a>
        <?php else: ?>
            <p class="text-red-600">Order not found.</p>
        <?php endif; ?>
    </div>
</body>
</html>
