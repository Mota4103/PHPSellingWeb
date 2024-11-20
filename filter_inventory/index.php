<?php 
session_start();
require_once("../connection.php");

// Fetch user ID and type from session
$seller_id = $_SESSION['id'];
$user_type = $_SESSION['usertype'] ?? null;

// Adjust seller ID for staff members
if ($user_type === "staff") {
    $staff_query = "SELECT seller_id FROM staff WHERE staff_ID = $seller_id";
    $staff_result = mysqli_query($db, $staff_query);
    if ($staff_result && mysqli_num_rows($staff_result) > 0) {
        $staff_data = mysqli_fetch_assoc($staff_result);
        $seller_id = $staff_data['seller_id'];
    }
}

// Fetch all distinct product names for the seller
$product_names = [];
$query = "SELECT DISTINCT product_name FROM production WHERE seller_ID = '$seller_id'";
$result = mysqli_query($db, $query);
while ($row = mysqli_fetch_assoc($result)) {
    $product_names[] = $row['product_name'];
}

// Fetch product types for filter dropdown
$product_types = [];
$product_type_query = "SELECT DISTINCT product_type FROM production WHERE seller_ID = '$seller_id'";
$product_type_result = mysqli_query($db, $product_type_query);
while ($row = mysqli_fetch_assoc($product_type_result)) {
    $product_types[] = $row['product_type'];
}

// Handle filter persistence with session
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['reset_filters'])) {
        unset($_SESSION['inventory_filters']);
        header("Location: ../filter_inventory");
        exit;
    } else {
        $_SESSION['inventory_filters'] = [
            'product_name' => $_GET['product_name'] ?? '',
            'product_type' => $_GET['product_type'] ?? '',
            'min_quantity' => $_GET['min_quantity'] ?? '',
            'max_quantity' => $_GET['max_quantity'] ?? '',
            'min_price' => $_GET['min_price'] ?? '',
            'max_price' => $_GET['max_price'] ?? '',
            'min_sold' => $_GET['min_sold'] ?? '',
            'max_sold' => $_GET['max_sold'] ?? '',
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
    <title>Filter Inventory</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 flex justify-center p-6 min-h-screen">
    <div class="w-full max-w-3xl">
        <h2 class="text-2xl font-semibold text-gray-700 mb-6">Filter Inventory</h2>

        <!-- Button to View Inventory -->
        <a href="../inventory" class="mb-6 inline-block">
            <button class="px-6 py-2 bg-blue-500 text-white rounded shadow hover:bg-blue-600 text-sm font-medium">
                View Inventory
            </button>
        </a>

        <!-- Filter Form -->
        <form action="../filter_inventory" method="get" class="bg-white p-6 rounded shadow-lg mb-8 border border-gray-300">
            <div class="grid grid-cols-2 gap-4">
                
                <!-- Product Name Field with JavaScript Suggestions -->
                <div>
                    <label class="block text-sm text-gray-700">Product Name:</label>
                    <input type="text" name="product_name" id="product_name" value="<?= htmlspecialchars($_SESSION['inventory_filters']['product_name'] ?? '') ?>"
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded" onkeyup="showSuggestions(this.value)">

                    <!-- Suggestions Dropdown -->
                    <ul id="suggestions" class="bg-white border border-gray-300 rounded mt-1 hidden"></ul>
                </div>

                <!-- Product Type Dropdown -->
                <div>
                    <label class="block text-sm text-gray-700">Product Type:</label>
                    <select name="product_type" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded">
                        <option value="">All Types</option>
                        <?php foreach ($product_types as $type): ?>
                            <option value="<?= htmlspecialchars($type) ?>" <?= ($_SESSION['inventory_filters']['product_type'] ?? '') == $type ? 'selected' : '' ?>>
                                <?= htmlspecialchars($type) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Min Quantity -->
                <div>
                    <label class="block text-sm text-gray-700">Min Quantity:</label>
                    <input type="number" name="min_quantity" value="<?= htmlspecialchars($_SESSION['inventory_filters']['min_quantity'] ?? '') ?>"
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded">
                </div>

                <!-- Max Quantity -->
                <div>
                    <label class="block text-sm text-gray-700">Max Quantity:</label>
                    <input type="number" name="max_quantity" value="<?= htmlspecialchars($_SESSION['inventory_filters']['max_quantity'] ?? '') ?>"
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded">
                </div>

                <!-- Min Price -->
                <div>
                    <label class="block text-sm text-gray-700">Min Price:</label>
                    <input type="number" step="0.01" name="min_price" value="<?= htmlspecialchars($_SESSION['inventory_filters']['min_price'] ?? '') ?>"
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded">
                </div>

                <!-- Max Price -->
                <div>
                    <label class="block text-sm text-gray-700">Max Price:</label>
                    <input type="number" step="0.01" name="max_price" value="<?= htmlspecialchars($_SESSION['inventory_filters']['max_price'] ?? '') ?>"
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded">
                </div>

                <!-- Min Sold -->
                <div>
                    <label class="block text-sm text-gray-700">Min Sold:</label>
                    <input type="number" name="min_sold" value="<?= htmlspecialchars($_SESSION['inventory_filters']['min_sold'] ?? '') ?>"
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded">
                </div>

                <!-- Max Sold -->
                <div>
                    <label class="block text-sm text-gray-700">Max Sold:</label>
                    <input type="number" name="max_sold" value="<?= htmlspecialchars($_SESSION['inventory_filters']['max_sold'] ?? '') ?>"
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded">
                </div>

                <!-- Date Range -->
                <div>
                    <label class="block text-sm text-gray-700">Update Time (Range):</label>
                    <div class="flex space-x-2">
                        <input type="date" name="start_date" value="<?= htmlspecialchars($_SESSION['inventory_filters']['start_date'] ?? '') ?>"
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded">
                        <input type="date" name="end_date" value="<?= htmlspecialchars($_SESSION['inventory_filters']['end_date'] ?? '') ?>"
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded">
                    </div>
                </div>
            </div>
            <div class="flex items-center space-x-4 mt-6">
                <input type="submit" value="Apply Filter" class="px-4 py-2 bg-blue-500 text-white rounded shadow hover:bg-blue-600">
                <input type="submit" value="Reset Filter" name="reset_filters" class="px-4 py-2 bg-gray-300 text-gray-700 rounded shadow hover:bg-gray-400">
            </div>
        </form>
    </div>

    <script>
        const productNames = <?php echo json_encode($product_names); ?>;

        function showSuggestions(input) {
            const suggestions = document.getElementById("suggestions");
            suggestions.innerHTML = ""; // Clear previous suggestions
            if (input.length === 0) {
                suggestions.classList.add("hidden");
                return;
            }

            // Filter product names
            const matches = productNames.filter(name => name.toLowerCase().includes(input.toLowerCase()));
            
            // Show suggestions
            if (matches.length > 0) {
                suggestions.classList.remove("hidden");
                matches.forEach(match => {
                    const item = document.createElement("li");
                    item.classList.add("px-3", "py-2", "hover:bg-gray-100", "cursor-pointer");
                    item.innerText = match;
                    item.onclick = () => {
                        document.getElementById("product_name").value = match;
                        suggestions.classList.add("hidden");
                    };
                    suggestions.appendChild(item);
                });
            } else {
                suggestions.classList.add("hidden");
            }
        }
    </script>
</body>
</html>
