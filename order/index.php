<!DOCTYPE html>
<?php
session_start();
require_once("../connection.php");

// Fetch filters from session
$filters = $_SESSION['order_filters'] ?? [];

// Get sorting parameters
$sort_column = $_GET['sort'] ?? 'customer_name'; // Default sorting by customer_name
$sort_order = $_GET['order'] ?? 'ASC'; // Default sorting order

// Sanitize sorting inputs
$allowed_columns = ['customer_name', 'customer_channel', 'customer_address', 'total_order_price', 'orders_paid', 'orders_paid_status', 'orders_date'];
$allowed_order = ['ASC', 'DESC'];

if (!in_array($sort_column, $allowed_columns)) {
    $sort_column = 'customer_name';
}

if (!in_array($sort_order, $allowed_order)) {
    $sort_order = 'ASC';
}

// Fetch user ID
$user_id = isset($_SESSION["id"]) ? intval($_SESSION["id"]) : null;
if (!$user_id) {
    header('Location: ../login');
    die();
}

// Build query with filters and sorting
$query = "SELECT o.orders_ID, o.customer_name, o.customer_channel, o.customer_address, o.orders_paid, o.orders_paid_status, o.orders_date,
                 SUM(r.orders_quantity * p.product_price) AS total_order_price
          FROM orders o
          LEFT JOIN relation r ON o.orders_ID = r.orders_ID
          LEFT JOIN product p ON r.product_ID = p.product_ID
          WHERE o.orders_seller = $user_id";
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

if (!empty($filter_conditions)) {
    $query .= " AND " . implode(' AND ', $filter_conditions);
}

// Add sorting to the query
$query .= " GROUP BY o.orders_ID ORDER BY $sort_column $sort_order";

$result = mysqli_query($db, $query);
if (!$result) {
    die('Query failed: ' . mysqli_error($db));
}

$orders = [];
while ($row = mysqli_fetch_assoc($result)) {
    $orders[] = $row;
}

// Toggle sort order for next click
$next_order = ($sort_order === 'ASC') ? 'DESC' : 'ASC';

// Function to display sorting icon
function get_sort_icon($column) {
    global $sort_column, $sort_order;
    if ($sort_column === $column) {
        return $sort_order === 'ASC' ? '▲' : '▼';
    }
    return '⇅'; // Default icon for unsorted columns
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

        <!-- Buttons for Filter, Add Order, and Back to Home -->
        <a href="../filter_order" class="mb-6 inline-block">
            <button class="px-6 py-2 bg-blue-500 text-white rounded shadow hover:bg-blue-600 text-sm font-medium">
                Filter Orders
            </button>
        </a>

        <a href="../add_order" class="mb-6 inline-block">
            <button class="px-6 py-2 bg-green-500 text-white rounded shadow hover:bg-green-600 text-sm font-medium">
                Add Order
            </button>
        </a>

        <a href="../home" class="mb-6 inline-block">
            <button class="px-6 py-2 bg-gray-500 text-white rounded shadow hover:bg-gray-600 text-sm font-medium">
                Back to Home
            </button>
        </a>

        <!-- Orders Table -->
        <table class="table-auto w-full bg-white shadow-md rounded">
            <thead>
                <tr class="text-left bg-gray-200">
                    <th class="py-2 px-4"><a href="?sort=customer_name&order=<?= $next_order ?>">Customer Name <?= get_sort_icon('customer_name') ?></a></th>
                    <th class="py-2 px-4"><a href="?sort=customer_channel&order=<?= $next_order ?>">Channel <?= get_sort_icon('customer_channel') ?></a></th>
                    <th class="py-2 px-4"><a href="?sort=customer_address&order=<?= $next_order ?>">Address <?= get_sort_icon('customer_address') ?></a></th>
                    <th class="py-2 px-4"><a href="?sort=total_order_price&order=<?= $next_order ?>">Price <?= get_sort_icon('total_order_price') ?></a></th>
                    <th class="py-2 px-4"><a href="?sort=orders_paid&order=<?= $next_order ?>">Paid <?= get_sort_icon('orders_paid') ?></a></th>
                    <th class="py-2 px-4"><a href="?sort=orders_paid_status&order=<?= $next_order ?>">Paid Status <?= get_sort_icon('orders_paid_status') ?></a></th>
                    <th class="py-2 px-4"><a href="?sort=orders_date&order=<?= $next_order ?>">Date <?= get_sort_icon('orders_date') ?></a></th>
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
    </div>
</body>
</html>
