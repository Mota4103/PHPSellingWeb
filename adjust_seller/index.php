<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Filter Inventory</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 flex justify-center p-6 min-h-screen">
<div class="w-full max-w-3xl">
    <h2 class="text-2xl font-semibold text-gray-700 mb-6">Adjust Seller</h2>

    <!-- Back to Home Button -->
    <a href="../home" class="mb-6 inline-block">
        <button class="px-6 py-2 bg-blue-500 text-white rounded shadow hover:bg-blue-600 text-sm font-medium">
            BACK
        </button>
    </a>

    <form action="add_seller.php" method="POST" class="bg-white p-6 rounded shadow-lg mb-8 border border-gray-300">
        <div class="grid gap-4">
            <div>
                <label class="block text-sm text-gray-700">Username:</label>
                <input type="text" name="seller_username" value="<?= htmlspecialchars($_SESSION['filters']['product_name'] ?? '') ?>"
                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>

            <div>
                <label class="block text-sm text-gray-700">Password:</label>
                <input type="text" name="seller_password" value="<?= htmlspecialchars($_SESSION['filters']['product_name'] ?? '') ?>"
                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>

        <div class="flex items-center space-x-4 mt-6">
            <input type="submit" value="Add Seller" class="px-4 py-2 bg-blue-500 text-white rounded shadow hover:bg-blue-600">
        </div>
        </div>
    </form>
    
    <form action="delete_seller.php" method="post" class="bg-white p-6 rounded shadow-lg mb-8 border border-gray-300">
        <div class="grid gap-4">

            <!-- Product Type Dropdown -->
            <div>
                <label class="block text-sm text-gray-700">Staff:</label>
                <select name="seller_username" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <?php
                    require("../connection.php");

                    // Fetch distinct product types based on seller ID from session
                    $query = "SELECT DISTINCT seller_username FROM seller";
                    $result = mysqli_query($db, $query);

                    if ($result) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            $type = htmlspecialchars($row['seller_username']);
                            // Check if type is selected to keep selection after submission
                            $selected = (isset($_SESSION['filters']['seller_username']) && $_SESSION['filters']['seller_username'] === $type) ? 'selected' : '';
                            echo "<option value='$type' $selected>$type</option>";
                        }
                    }
                    else {
                        echo "<option disabled>Please log in to select a username</option>";
                    }
                    ?>
                </select>
            </div>

        <div class="flex items-center space-x-4 mt-6">
            <input type="submit" value="Delete Seller" class="px-4 py-2 bg-blue-500 text-white rounded shadow hover:bg-blue-600">
        </div>
    </form>
</div>
</body>
</html>
