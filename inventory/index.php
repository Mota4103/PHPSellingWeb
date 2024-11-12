<!DOCTYPE html>
<?php
session_start();
require_once("../connection.php");

// Fetch filters from session
$filters = $_SESSION['filters'] ?? [];

// Get sorting parameters
$sort_column = $_GET['sort'] ?? 'product_name'; // Default sorting by product_name
$sort_order = $_GET['order'] ?? 'ASC'; // Default sorting order

// Sanitize sorting inputs
$allowed_columns = ['product_name', 'product_type', 'product_quantity', 'product_price', 'product_sold', 'product_update_time'];
$allowed_order = ['ASC', 'DESC'];

if (!in_array($sort_column, $allowed_columns)) {
    $sort_column = 'product_name';
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

// Main query to retrieve product information with calculated quantity and total sold
$query = "
    SELECT 
        p.product_ID, 
        p.product_name, 
        p.product_type, 
        p.product_price, 
        GREATEST(COALESCE(MAX(o.orders_date), '0000-00-00'), COALESCE(MAX(so.stockorder_date), '0000-00-00')) AS product_update_time,
        COALESCE(SUM(so.quantity), 0) - COALESCE(SUM(o.quantity), 0) AS product_quantity,
        COALESCE(SUM(o.orders_quantity), 0) AS product_sold
    FROM 
        product p
    LEFT JOIN 
        (SELECT product_ID, quantity, stockorder_date FROM stockorder) AS so 
        ON p.product_ID = so.product_ID
    LEFT JOIN 
        (SELECT r.product_ID, r.orders_quantity AS quantity, r.orders_quantity, o.orders_date FROM relation r 
         JOIN orders o ON o.orders_ID = r.orders_ID) AS o
        ON p.product_ID = o.product_ID
    WHERE 
        p.seller_ID = $user_id";

// Apply filter conditions based on session variables
$filter_conditions = [];
if (!empty($filters['product_name'])) {
    $product_name = mysqli_real_escape_string($db, $filters['product_name']);
    $filter_conditions[] = "p.product_name LIKE '%$product_name%'";
}
if (!empty($filters['product_type'])) {
    $product_type = mysqli_real_escape_string($db, $filters['product_type']);
    $filter_conditions[] = "p.product_type LIKE '%$product_type%'";
}
if (!empty($filters['min_quantity'])) {
    $min_quantity = mysqli_real_escape_string($db, $filters['min_quantity']);
    $filter_conditions[] = "product_quantity >= $min_quantity";
}
if (!empty($filters['max_quantity'])) {
    $max_quantity = mysqli_real_escape_string($db, $filters['max_quantity']);
    $filter_conditions[] = "product_quantity <= $max_quantity";
}
if (!empty($filters['min_price'])) {
    $min_price = mysqli_real_escape_string($db, $filters['min_price']);
    $filter_conditions[] = "p.product_price >= $min_price";
}
if (!empty($filters['max_price'])) {
    $max_price = mysqli_real_escape_string($db, $filters['max_price']);
    $filter_conditions[] = "p.product_price <= $max_price";
}
if (!empty($filters['min_sold'])) {
    $min_sold = mysqli_real_escape_string($db, $filters['min_sold']);
    $filter_conditions[] = "product_sold >= $min_sold";
}
if (!empty($filters['max_sold'])) {
    $max_sold = mysqli_real_escape_string($db, $filters['max_sold']);
    $filter_conditions[] = "product_sold <= $max_sold";
}
if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
    $start_date = mysqli_real_escape_string($db, $filters['start_date']);
    $end_date = mysqli_real_escape_string($db, $filters['end_date']);
    $filter_conditions[] = "product_update_time BETWEEN '$start_date' AND '$end_date'";
}

// Append filter conditions if any
if (!empty($filter_conditions)) {
    $query .= " AND " . implode(' AND ', $filter_conditions);
}

// Group by product ID to calculate the total quantity and total sold per product
$query .= " GROUP BY p.product_ID";

// Add sorting to the query
$query .= " ORDER BY $sort_column $sort_order";

// Execute the query
$result = mysqli_query($db, $query);
if (!$result) {
    die('Query failed: ' . mysqli_error($db));
}

$products = [];
while ($row = mysqli_fetch_assoc($result)) {
    $products[] = $row;
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
    <title>Inventory</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="max-w-7xl mx-auto p-6">
        <h1 class="text-3xl font-semibold mb-6">Product Inventory</h1>

        <!-- Buttons for Filter, Add Product, and Back to Home -->
        <a href="../filter_inventory" class="mb-6 inline-block">
            <button class="px-6 py-2 bg-blue-500 text-white rounded shadow hover:bg-blue-600 text-sm font-medium">
                Filter Inventory
            </button>
        </a>

        <a href="../add_inventory" class="mb-6 inline-block">
            <button class="px-6 py-2 bg-green-500 text-white rounded shadow hover:bg-green-600 text-sm font-medium">
                Add Product
            </button>
        </a>

        <a href="../home" class="mb-6 inline-block">
            <button class="px-6 py-2 bg-gray-500 text-white rounded shadow hover:bg-gray-600 text-sm font-medium">
                Back to Home
            </button>
        </a>

        <!-- Inventory Table -->
        <table class="table-auto w-full bg-white shadow-md rounded">
            <thead>
                <tr class="text-left bg-gray-200">
                    <th class="py-2 px-4"><a href="?sort=product_name&order=<?= $next_order ?>">Product Name <?= get_sort_icon('product_name') ?></a></th>
                    <th class="py-2 px-4"><a href="?sort=product_type&order=<?= $next_order ?>">Product Type <?= get_sort_icon('product_type') ?></a></th>
                    <th class="py-2 px-4"><a href="?sort=product_quantity&order=<?= $next_order ?>">Quantity <?= get_sort_icon('product_quantity') ?></a></th>
                    <th class="py-2 px-4"><a href="?sort=product_price&order=<?= $next_order ?>">Price <?= get_sort_icon('product_price') ?></a></th>
                    <th class="py-2 px-4"><a href="?sort=product_sold&order=<?= $next_order ?>">Sold <?= get_sort_icon('product_sold') ?></a></th>
                    <th class="py-2 px-4"><a href="?sort=product_update_time&order=<?= $next_order ?>">Update Time <?= get_sort_icon('product_update_time') ?></a></th>
                    <th class="py-2 px-4">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $product): ?>
                <tr>
                    <td class="py-2 px-4"><?= htmlspecialchars($product['product_name']) ?></td>
                    <td class="py-2 px-4"><?= htmlspecialchars($product['product_type']) ?></td>
                    <td class="py-2 px-4"><?= htmlspecialchars($product['product_quantity']) ?></td>
                    <td class="py-2 px-4"><?= htmlspecialchars($product['product_price']) ?></td>
                    <td class="py-2 px-4"><?= htmlspecialchars($product['product_sold']) ?></td>
                    <td class="py-2 px-4"><?= htmlspecialchars($product['product_update_time']) ?></td>
                    <td class="py-2 px-4">
                        <a href="../stock_details?product_ID=<?= htmlspecialchars($product['product_ID']) ?>" class="text-blue-500 hover:text-blue-700">View</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
