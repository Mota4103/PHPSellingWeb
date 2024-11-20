<!DOCTYPE html>
<?php
session_start();
require_once("../connection.php");

// Fetch filters and sorting options from session or default settings
$filters = $_SESSION['order_filters'] ?? [];
$sort_column = $_GET['sort_column'] ?? 'orders_date';
$sort_order = $_GET['sort_order'] ?? 'ASC';

// Determine toggle logic for sort order
if (isset($_SESSION['sort_column']) && $_SESSION['sort_column'] === $sort_column) {
    $sort_order = ($_SESSION['sort_order'] === 'ASC') ? 'DESC' : 'ASC';
} else {
    $sort_order = 'ASC';
}
$_SESSION['sort_column'] = $sort_column;
$_SESSION['sort_order'] = $sort_order;

// Fetch user information
$user_id = isset($_SESSION["id"]) ? intval($_SESSION["id"]) : null;
$type = $_SESSION["usertype"] ?? null;

if (!$user_id) {
    header('Location: ../login');
    die();
}

// Determine seller_id based on user type
$seller_id = $user_id;
if ($type === "staff") {
    // Fetch the seller_ID associated with the staff member
    $query = "SELECT seller_ID FROM staff WHERE staff_ID = $user_id";
    $seller_result = mysqli_query($db, $query);
    if (!$seller_result || mysqli_num_rows($seller_result) == 0) {
        die("Staff member not found or not assigned to any seller.");
    }
    $seller_id_row = mysqli_fetch_assoc($seller_result);
    $seller_id = $seller_id_row['seller_ID'];
}

// Build query with filters and dynamic sorting
$query = "
    SELECT o.orders_ID, o.customer_name, o.customer_channel, o.customer_address, o.orders_paid, 
           o.orders_paid_status, o.orders_date, total_order_price
    FROM orders o
    LEFT JOIN (
        SELECT r.orders_ID, SUM(r.orders_quantity * p.product_price) AS total_order_price
        FROM relation2 r
        JOIN production p ON r.product_ID = p.product_ID
        GROUP BY r.orders_ID
    ) AS order_totals ON o.orders_ID = order_totals.orders_ID
    WHERE o.orders_seller = $seller_id";

$filter_conditions = [];

// Apply filter conditions based on session variables
if (!empty($filters['customer_name'])) {
    $customer_name = mysqli_real_escape_string($db, $filters['customer_name']);
    $filter_conditions[] = "o.customer_name LIKE '%$customer_name%'";
}

if (!empty($filters['customer_channel'])) {
    $customer_channel = mysqli_real_escape_string($db, $filters['customer_channel']);
    $filter_conditions[] = "o.customer_channel LIKE '%$customer_channel%'";
}

if (!empty($filters['customer_address'])) {
    $customer_address = mysqli_real_escape_string($db, $filters['customer_address']);
    $filter_conditions[] = "o.customer_address LIKE '%$customer_address%'";
}

if (!empty($filters['orders_paid_status'])) {
    $orders_paid_status = mysqli_real_escape_string($db, $filters['orders_paid_status']);
    $filter_conditions[] = "o.orders_paid_status = '$orders_paid_status'";
}

if (!empty($filters['min_paid'])) {
    $min_paid = mysqli_real_escape_string($db, $filters['min_paid']);
    $filter_conditions[] = "o.orders_paid >= $min_paid";
}

if (!empty($filters['max_paid'])) {
    $max_paid = mysqli_real_escape_string($db, $filters['max_paid']);
    $filter_conditions[] = "o.orders_paid <= $max_paid";
}

if (!empty($filters['min_price'])) {
    $min_price = mysqli_real_escape_string($db, $filters['min_price']);
    $filter_conditions[] = "total_order_price >= $min_price";
}

if (!empty($filters['max_price'])) {
    $max_price = mysqli_real_escape_string($db, $filters['max_price']);
    $filter_conditions[] = "total_order_price <= $max_price";
}

if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
    $start_date = mysqli_real_escape_string($db, $filters['start_date']);
    $end_date = mysqli_real_escape_string($db, $filters['end_date']);
    $filter_conditions[] = "o.orders_date BETWEEN '$start_date' AND '$end_date'";
}

// Combine all conditions
if (!empty($filter_conditions)) {
    $query .= " AND " . implode(' AND ', $filter_conditions);
}

$query .= " ORDER BY $sort_column $sort_order";


$result = mysqli_query($db, $query);
if (!$result) {
    die('Query failed: ' . mysqli_error($db));
}

$orders = [];
while ($row = mysqli_fetch_assoc($result)) {
    $orders[] = $row;
}
?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order List</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="max-w-7xl mx-auto p-6">
        <h1 class="text-3xl font-semibold mb-6">Order List</h1>

        <!-- Buttons for Filter Orders and Back to Home -->
        <a href="../filter_order" class="mb-6 inline-block">
            <button class="px-6 py-2 bg-blue-500 text-white rounded shadow hover:bg-blue-600 text-sm font-medium">
                Filter Orders
            </button>
        </a>

        <!-- Only display Add Order button if the user is not a staff member -->
        <?php if ($type != "staff"): ?>
            <a href="../add_order" class="mb-6 inline-block">
                <button class="px-6 py-2 bg-green-500 text-white rounded shadow hover:bg-green-600 text-sm font-medium">
                    Add Order
                </button>
            </a>
        <?php endif; ?>

        <a href="../home" class="mb-6 inline-block">
            <button class="px-6 py-2 bg-gray-500 text-white rounded shadow hover:bg-gray-600 text-sm font-medium">
                Back to Home
            </button>
        </a>

        <!-- Orders Table -->
        <?php if (count($orders) > 0): ?>
            <table class="table-auto w-full bg-white shadow-md rounded">
                <thead>
                    <tr class="text-left bg-gray-200">
                        <?php
                        // Column headers with sorting links, without icons
                        $columns = [
                            'customer_name' => 'Customer Name',
                            'customer_channel' => 'Channel',
                            'customer_address' => 'Address',
                            'total_order_price' => 'Price',
                            'orders_paid' => 'Paid',
                            'orders_paid_status' => 'Paid Status',
                            'orders_date' => 'Date'
                        ];
                        foreach ($columns as $col => $title):
                        ?>
                        <th class="py-2 px-4">
                            <a href="?sort_column=<?= $col ?>&sort_order=<?= $sort_order ?>">
                                <?= $title ?>
                            </a>
                        </th>
                        <?php endforeach; ?>
                        <th class="py-2 px-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                    <tr>
                        <td class="py-2 px-4"><?= htmlspecialchars($order['customer_name']) ?></td>
                        <td class="py-2 px-4"><?= htmlspecialchars($order['customer_channel']) ?></td>
                        <td class="py-2 px-4"><?= htmlspecialchars($order['customer_address']) ?></td>
                        <td class="py-2 px-4"><?= htmlspecialchars(number_format($order['total_order_price'], 2)) ?></td>
                        <td class="py-2 px-4"><?= htmlspecialchars($order['orders_paid']) ?></td>
                        <td class="py-2 px-4"><?= htmlspecialchars($order['orders_paid_status']) ?></td>
                        <td class="py-2 px-4"><?= htmlspecialchars($order['orders_date']) ?></td>
                        <td class="py-2 px-4">
                            <a href="../order_details?order_id=<?= htmlspecialchars($order['orders_ID']) ?>" class="text-blue-500 hover:text-blue-700">Edit</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="mt-6 text-gray-600">No orders found matching the current filters.</p>
        <?php endif; ?>
    </div>
</body>
</html>
