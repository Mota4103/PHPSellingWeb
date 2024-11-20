<?php 
session_start();
require_once("../connection.php");

// Fetch filters and sorting options from session or default settings
$filters = $_SESSION['inventory_filters'] ?? [];
$sort_column = $_GET['sort_column'] ?? 'product_name';
$sort_order = $_GET['sort_order'] ?? 'ASC';

// Determine toggle logic for sort order
if (isset($_SESSION['sort_column']) && $_SESSION['sort_column'] === $sort_column) {
    $sort_order = ($_SESSION['sort_order'] === 'ASC') ? 'DESC' : 'ASC';
} else {
    $sort_order = 'ASC';
}
$_SESSION['sort_column'] = $sort_column;
$_SESSION['sort_order'] = $sort_order;

// Check for user ID
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

// Main query with subquery to calculate `product_quantity`
$subquery = "
    SELECT 
        p.product_ID, 
        p.product_name, 
        p.product_type, 
        p.product_price, 
        GREATEST(COALESCE(MAX(o.orders_date), '0000-00-00'), COALESCE(MAX(so.stockorder_date), '0000-00-00')) AS product_update_time,
        COALESCE(SUM(so.quantity), 0) - COALESCE(SUM(o.orders_quantity), 0) AS product_quantity,
        COALESCE(SUM(o.orders_quantity), 0) AS product_sold
    FROM 
        production p
    LEFT JOIN 
        (SELECT product_ID, quantity, stockorder_date FROM stockorder2) AS so 
        ON p.product_ID = so.product_ID
    LEFT JOIN 
        (SELECT r.product_ID, r.orders_quantity AS quantity, r.orders_quantity, o.orders_date FROM relation2 r 
         JOIN orders o ON o.orders_ID = r.orders_ID) AS o
        ON p.product_ID = o.product_ID
    WHERE 
        p.seller_ID = $seller_id
    GROUP BY p.product_ID";

// Apply filter conditions in an outer query
$query = "SELECT * FROM ($subquery) AS inventory WHERE 1=1";

// Applying filters if they are set and not empty for numeric values
if (!empty($filters['product_name'])) {
    $product_name = mysqli_real_escape_string($db, $filters['product_name']);
    $query .= " AND product_name LIKE '%$product_name%'";
}

if (!empty($filters['product_type'])) {
    $product_type = mysqli_real_escape_string($db, $filters['product_type']);
    $query .= " AND product_type LIKE '%$product_type%'";
}

if (isset($filters['min_quantity']) && $filters['min_quantity'] !== '') {
    $min_quantity = intval($filters['min_quantity']);
    $query .= " AND product_quantity >= $min_quantity";
}

if (isset($filters['max_quantity']) && $filters['max_quantity'] !== '') {
    $max_quantity = intval($filters['max_quantity']);
    $query .= " AND product_quantity <= $max_quantity";
}

if (isset($filters['min_price']) && $filters['min_price'] !== '') {
    $min_price = floatval($filters['min_price']);
    $query .= " AND product_price >= $min_price";
}

if (isset($filters['max_price']) && $filters['max_price'] !== '') {
    $max_price = floatval($filters['max_price']);
    $query .= " AND product_price <= $max_price";
}

if (isset($filters['min_sold']) && $filters['min_sold'] !== '') {
    $min_sold = intval($filters['min_sold']);
    $query .= " AND product_sold >= $min_sold";
}

if (isset($filters['max_sold']) && $filters['max_sold'] !== '') {
    $max_sold = intval($filters['max_sold']);
    $query .= " AND product_sold <= $max_sold";
}

// Add sorting
$query .= " ORDER BY $sort_column $sort_order";

$result = mysqli_query($db, $query);
if (!$result) {
    die('Query failed: ' . mysqli_error($db));
}

$products = [];
while ($row = mysqli_fetch_assoc($result)) {
    $products[] = $row;
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Inventory</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="max-w-7xl mx-auto p-6">
        <h1 class="text-3xl font-semibold mb-6">Product Inventory</h1>

        <a href="../filter_inventory" class="mb-6 inline-block">
            <button class="px-6 py-2 bg-blue-500 text-white rounded shadow hover:bg-blue-600 text-sm font-medium">
                Filter Inventory
            </button>
        </a>

        <?php if ($type != "staff"): ?>
            <a href="../add_inventory" class="mb-6 inline-block">
                <button class="px-6 py-2 bg-green-500 text-white rounded shadow hover:bg-green-600 text-sm font-medium">
                    Add Product
                </button>
            </a>
        <?php endif; ?>

        <a href="../home" class="mb-6 inline-block">
            <button class="px-6 py-2 bg-gray-500 text-white rounded shadow hover:bg-gray-600 text-sm font-medium">
                Back to Home
            </button>
        </a>

        <?php if (count($products) > 0): ?>
            <table class="table-auto w-full bg-white shadow-md rounded">
                <thead>
                    <tr class="text-left bg-gray-200">
                        <?php
                        $columns = [
                            'product_name' => 'Product Name',
                            'product_type' => 'Product Type',
                            'product_quantity' => 'Quantity',
                            'product_price' => 'Price',
                            'product_sold' => 'Sold',
                            'product_update_time' => 'Update Time'
                        ];
                        foreach ($columns as $col => $title):
                        ?>
                        <th class="py-2 px-4">
                            <a href="?sort_column=<?= $col ?>&sort_order=<?= $sort_order ?>">
                                <?= $title ?>
                            </a>
                        </th>
                        <?php endforeach; ?>
                        <?php if ($type != "staff"): ?>
                            <th class="py-2 px-4">Actions</th>
                        <?php endif; ?>
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
                        <?php if ($type != "staff"): ?>
                            <td class="py-2 px-4">
                                <a href="../stock_details?product_ID=<?= htmlspecialchars($product['product_ID']) ?>" class="text-blue-500 hover:text-blue-700">Edit</a>
                            </td>
                        <?php endif; ?>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="mt-6 text-gray-600">No products found matching the current filters.</p>
        <?php endif; ?>
    </div>
</body>
</html>
