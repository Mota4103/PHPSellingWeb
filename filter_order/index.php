<?php
session_start();
require_once("../connection.php");

$seller_id = $_SESSION['id'];

// Fetch channels for the filter dropdown
$channels = [];
$query = "SELECT DISTINCT customer_channel FROM orders WHERE orders_seller = '$seller_id'";
$result = mysqli_query($db, $query);
while ($row = mysqli_fetch_assoc($result)) {
    $channels[] = $row['customer_channel'];
}

// Handle filter persistence with session
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['reset_filters'])) {
        // Clear filters from the session if reset is requested
        unset($_SESSION['order_filters']);
        header("Location: ../filter_order");
        exit;
    } else {
        // Store current filters in session
        $_SESSION['order_filters'] = [
            'customer_name' => $_GET['customer_name'] ?? '',
            'customer_channel' => $_GET['customer_channel'] ?? '',
            'customer_address' => $_GET['customer_address'] ?? '',
            'min_paid' => $_GET['min_paid'] ?? '',
            'max_paid' => $_GET['max_paid'] ?? '',
            'orders_paid_status' => $_GET['orders_paid_status'] ?? '',
            'start_date' => $_GET['start_date'] ?? '',
            'end_date' => $_GET['end_date'] ?? ''
        ];
    }
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Filter Orders</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 flex justify-center p-6 min-h-screen">
    <div class="w-full max-w-3xl">
        <h2 class="text-2xl font-semibold text-gray-700 mb-6">Filter Orders</h2>

        <!-- Button to View Orders -->
        <a href="../order" class="mb-6 inline-block">
            <button class="px-6 py-2 bg-blue-500 text-white rounded shadow hover:bg-blue-600 text-sm font-medium">
                View Orders
            </button>
        </a>

        <!-- Filter Form -->
        <form action="../filter_order" method="get" class="bg-white p-6 rounded shadow-lg mb-8 border border-gray-300">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm text-gray-700">Customer Name:</label>
                    <input type="text" name="customer_name" value="<?= htmlspecialchars($_SESSION['order_filters']['customer_name'] ?? '') ?>"
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded">
                </div>
                <div>
                    <label class="block text-sm text-gray-700">Channel:</label>
                    <select name="customer_channel" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded">
                        <option value="">All</option>
                        <?php foreach ($channels as $channel): ?>
                            <option value="<?= htmlspecialchars($channel) ?>" <?= ($_SESSION['order_filters']['customer_channel'] ?? '') == $channel ? 'selected' : '' ?>>
                                <?= htmlspecialchars($channel) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm text-gray-700">Customer Address:</label>
                    <input type="text" name="customer_address" value="<?= htmlspecialchars($_SESSION['order_filters']['customer_address'] ?? '') ?>"
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded">
                </div>
                <div>
                    <label class="block text-sm text-gray-700">Minimum Paid:</label>
                    <input type="number" name="min_paid" value="<?= htmlspecialchars($_SESSION['order_filters']['min_paid'] ?? '') ?>"
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded">
                </div>
                <div>
                    <label class="block text-sm text-gray-700">Maximum Paid:</label>
                    <input type="number" name="max_paid" value="<?= htmlspecialchars($_SESSION['order_filters']['max_paid'] ?? '') ?>"
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded">
                </div>
                <div>
                    <label class="block text-sm text-gray-700">Paid Status:</label>
                    <select name="orders_paid_status" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded">
                        <option value="">All</option>
                        <option value="Paid" <?= ($_SESSION['order_filters']['orders_paid_status'] ?? '') == 'Paid' ? 'selected' : '' ?>>Paid</option>
                        <option value="Unpaid" <?= ($_SESSION['order_filters']['orders_paid_status'] ?? '') == 'Unpaid' ? 'selected' : '' ?>>Unpaid</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm text-gray-700">Start Date:</label>
                    <input type="date" name="start_date" value="<?= htmlspecialchars($_SESSION['order_filters']['start_date'] ?? '') ?>"
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded">
                </div>
                <div>
                    <label class="block text-sm text-gray-700">End Date:</label>
                    <input type="date" name="end_date" value="<?= htmlspecialchars($_SESSION['order_filters']['end_date'] ?? '') ?>"
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded">
                </div>
            </div>
            <div class="flex items-center space-x-4 mt-6">
                <input type="submit" value="Apply Filter" class="px-4 py-2 bg-blue-500 text-white rounded shadow">
                <input type="submit" name="reset_filters" value="Reset Filter" class="px-4 py-2 bg-gray-300 text-gray-700 rounded shadow">
            </div>
        </form>
    </div>
</body>
</html>

