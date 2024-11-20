<?php
require_once("../connection.php");
session_start();

// Get the filters from the GET parameters
$seller_id = $_SESSION['id'];
$user_type = $_SESSION['usertype'] ?? null;
$customer_channel = isset($_GET['customer_channel']) ? $_GET['customer_channel'] : '';
$orders_paid_status = isset($_GET['orders_paid_status']) ? $_GET['orders_paid_status'] : '';
$min_price = isset($_GET['min_price']) ? floatval($_GET['min_price']) : 0;
$max_price = isset($_GET['max_price']) ? floatval($_GET['max_price']) : PHP_INT_MAX;

// Adjust seller_id for staff users
if ($user_type === "staff") {
    $staff_query = "SELECT seller_id FROM staff WHERE staff_ID = $seller_id";
    $staff_result = mysqli_query($db, $staff_query);
    if ($staff_result && mysqli_num_rows($staff_result) > 0) {
        $staff_data = mysqli_fetch_assoc($staff_result);
        $seller_id = $staff_data['seller_id'];
    }
}

// Construct the base query
$base_query = "
    SELECT o.orders_ID, o.customer_name, o.customer_channel, o.orders_paid_status, o.orders_paid, 
           o.orders_date, SUM(r.orders_quantity * p.product_price) AS total_order_price
    FROM orders o
    LEFT JOIN relation2 r ON o.orders_ID = r.orders_ID
    LEFT JOIN production p ON r.product_ID = p.product_ID
    WHERE o.orders_seller = '$seller_id'";

// Apply filters if they are set
if (!empty($customer_channel)) {
    $base_query .= " AND o.customer_channel = '" . mysqli_real_escape_string($db, $customer_channel) . "'";
}

if (!empty($orders_paid_status)) {
    $base_query .= " AND o.orders_paid_status = '" . mysqli_real_escape_string($db, $orders_paid_status) . "'";
}

if ($min_price > 0) {
    $base_query .= " AND SUM(r.orders_quantity * p.product_price) >= $min_price";
}

if ($max_price < PHP_INT_MAX) {
    $base_query .= " AND SUM(r.orders_quantity * p.product_price) <= $max_price";
}

// Finalize query with grouping and execute
$base_query .= " GROUP BY o.orders_ID";
$result = mysqli_query($db, $base_query);

// Render results
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Filtered Orders</title>
</head>
<body>
    <form method="get" action="filtered_form.php">
        <!-- Existing filters (Channel, Paid Status, Min/Max Price) -->
        <!-- Channel Filter -->
        <!-- Paid Status Filter -->
        <!-- Min Price, Max Price Filters -->
        <button type="submit">Apply Filter</button>
    </form>

    <!-- Display the Results Table -->
    <?php if (mysqli_num_rows($result) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Customer Name</th>
                    <th>Channel</th>
                    <th>Paid Status</th>
                    <th>Payment</th>
                    <th>Order Date</th>
                    <th>Total Order Price</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['orders_ID']); ?></td>
                        <td><?php echo htmlspecialchars($row['customer_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['customer_channel']); ?></td>
                        <td><?php echo htmlspecialchars($row['orders_paid_status']); ?></td>
                        <td><?php echo htmlspecialchars($row['orders_paid']); ?></td>
                        <td><?php echo htmlspecialchars($row['orders_date']); ?></td>
                        <td><?php echo htmlspecialchars(number_format($row['total_order_price'], 2)); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No orders found matching the filter criteria.</p>
    <?php endif; ?>
</body>
</html>
