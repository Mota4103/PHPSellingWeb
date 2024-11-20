<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        function toggleNewTypeInput() {
            const newTypeContainer = document.getElementById("new_type_container");
            const productTypeSelect = document.getElementById("product_type");
            if (productTypeSelect.value === "add_new_type") {
                newTypeContainer.style.display = "block";
            } else {
                newTypeContainer.style.display = "none";
            }
        }
    </script>
</head>
<body class="flex items-center justify-center min-h-screen bg-gray-100">

    <div class="bg-white rounded-lg p-8 w-full max-w-3xl shadow-md">
        <h2 class="text-2xl font-bold text-center text-gray-700 mb-6">Add Product</h2>

        <form action="./add_product_handler.php" method="post">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                <!-- Product Name -->
                <div>
                    <label for="product_name" class="block text-sm font-medium text-gray-700">Product Name</label>
                    <input type="text" id="product_name" name="product_name" placeholder="Enter Product Name" class="mt-1 block w-full p-3 border border-gray-300 rounded-md" required>
                </div>

                <!-- Product Price -->
                <div>
                    <label for="product_price" class="block text-sm font-medium text-gray-700">Product Price</label>
                    <input type="number" step="0.01" id="product_price" name="product_price" placeholder="Enter Price" class="mt-1 block w-full p-3 border border-gray-300 rounded-md" required>
                </div>

                <!-- Product Note -->
                <div>
                    <label for="product_note" class="block text-sm font-medium text-gray-700">Product Note</label>
                    <input type="text" id="product_note" name="product_note" placeholder="Additional Notes" class="mt-1 block w-full p-3 border border-gray-300 rounded-md">
                </div>

                <!-- Product Type Dropdown and Add New Type -->
                <div>
                    <label for="product_type" class="block text-sm font-medium text-gray-700">Product Type</label>
                    <select id="product_type" name="product_type" onchange="toggleNewTypeInput()" class="mt-1 block w-full p-3 border border-gray-300 rounded-md" required>
                        <option value="" disabled selected>Select Type</option>
                        <?php
                        require("../connection.php");
                        session_start();

                        if (isset($_SESSION['id'])) {
                            $seller_ID = mysqli_real_escape_string($db, $_SESSION['id']);
                            $query = "SELECT DISTINCT product_type FROM production WHERE seller_ID = '$seller_ID'";
                            $result = mysqli_query($db, $query);

                            if (!$result || mysqli_num_rows($result) == 0) {
                                echo "<option disabled>No product types found</option>";
                            } else {
                                while ($row = mysqli_fetch_assoc($result)) {
                                    echo "<option value='" . htmlspecialchars($row['product_type']) . "'>" . htmlspecialchars($row['product_type']) . "</option>";
                                }
                            }
                        } else {
                            echo "<option disabled>Please log in to select a product type</option>";
                        }
                        ?>
                        <option value="add_new_type">Add New Type</option>
                    </select>
                    
                    <!-- New type input, initially hidden -->
                    <div id="new_type_container" style="display: none;" class="mt-3">
                        <label for="new_type" class="block text-sm font-medium text-gray-700">New Type</label>
                        <input type="text" id="new_type" name="new_type" placeholder="Enter New Type" class="w-full p-3 border border-gray-300 rounded-md">
                    </div>
                </div>
            </div>

            <button type="submit" class="mt-6 w-full py-3 bg-blue-600 text-white font-semibold rounded-md hover:bg-blue-700">Add Product</button>
        </form>

        <div class="text-center mt-4">
            <a href="../inventory" class="text-blue-500 hover:underline">Back to Inventory</a>
        </div>
    </div>

</body>
</html>
