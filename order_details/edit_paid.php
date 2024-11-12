<?php
require_once("../connection.php");
session_start();

// Get order ID from URL
$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;

// Fetch existing payment details for the order
$order_query = "SELECT orders_paid, orders_paid_status FROM orders WHERE orders_ID = $order_id";
$order_result = mysqli_query($db, $order_query);
$order = mysqli_fetch_assoc($order_result);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $orders_paid = floatval($_POST['orders_paid']);
    $orders_paid_status = mysqli_real_escape_string($db, $_POST['orders_paid_status']);

    // Update payment information in the database
    $update_query = "UPDATE orders SET orders_paid = $orders_paid, orders_paid_status = '$orders_paid_status' WHERE orders_ID = $order_id";
    mysqli_query($db, $update_query);

    header("Location: ../product?order_id=$order_id");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Payment for Order #<?php echo $order_id; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 p-10">
    <div class="max-w-md mx-auto bg-white p-6 rounded-lg shadow-lg">
        <h2 class="text-2xl font-semibold mb-4">Edit Payment for Order #<?php echo $order_id; ?></h2>

        <?php if ($order): ?>
            <form action="" method="POST">
                <!-- Payment Amount Input -->
                <div class="mb-4">
                    <label for="orders_paid" class="block text-sm font-medium text-gray-700">Payment Amount</label>
                    <input type="number" step="0.01" id="orders_paid" name="orders_paid" 
                           value="<?php echo htmlspecialchars($order['orders_paid']); ?>" 
                           class="mt-1 block w-full p-2 border border-gray-300 rounded-md" required>
                </div>

                <!-- Payment Status Select -->
                <div class="mb-4">
                    <label for="orders_paid_status" class="block text-sm font-medium text-gray-700">Payment Status</label>
                    <select id="orders_paid_status" name="orders_paid_status" 
                            class="mt-1 block w-full p-2 border border-gray-300 rounded-md">
                        <option value="Paid" <?php if ($order['orders_paid_status'] === 'Paid') echo 'selected'; ?>>Paid</option>
                        <option value="Pending" <?php if ($order['orders_paid_status'] === 'Pending') echo 'selected'; ?>>Pending</option>
                    </select>
                </div>

                <button type="submit" class="w-full py-2 bg-blue-600 text-white font-semibold rounded-md hover:bg-blue-700">
                    Update Payment
                </button>
            </form>

            <a href="../order_details?order_id=<?php echo $order_id; ?>" class="mt-4 inline-block text-blue-600 hover:underline">Back to Order Details</a>
        <?php else: ?>
            <p class="text-red-600">Order not found.</p>
        <?php endif; ?>
    </div>
</body>
</html>
