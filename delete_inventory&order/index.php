<form action="delete_inventory.php" method="post" class="bg-white p-6 rounded shadow-lg mb-8 border border-gray-300">
        <div class="grid gap-4">

            <!-- Product Type Dropdown -->
            <div>
                <label class="block text-sm text-gray-700">Staff:</label>
                <select name="seller_username" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <?php
                    require("../connection.php");

                    // Fetch distinct product types based on seller ID from session
                    $query = "SELECT DISTINCT seller_username, DISTINCT seller_ID FROM seller";
                    $result = mysqli_query($db, $query);

                    if ($result) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            $type = htmlspecialchars($row['seller_username']);
                            $type2 = htmlspecialchars($row['seller_ID']);

                            // Check if type is selected to keep selection after submission
                            $selected = (isset($_SESSION['filters']['seller_username']) && $_SESSION['filters']['seller_username'] === $type) ? 'selected' : '';
                            echo "<option value='$type' $selected>$type2 - $type</option>";
                        }
                    }
                    else {
                        echo "<option disabled>Please log in to select a username</option>";
                    }
                    ?>
                </select>
            </div>

        <div class="flex items-center space-x-4 mt-6">
            <input type="submit" value="Search" class="px-4 py-2 bg-blue-500 text-white rounded shadow hover:bg-blue-600">
        </div>
    </form>