<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Order</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="flex items-center justify-center min-h-screen bg-gray-100">
    <?php
        require_once("../connection.php");
        session_start();

        // Retrieve the current seller's ID from the session
        $seller_id = $_SESSION['id'];

        // Query to get Customer Channels associated with this seller
        $channel_query = "SELECT DISTINCT customer_channel FROM orders WHERE orders_seller = $seller_id";
        $channel_result = mysqli_query($db, $channel_query);

        // Query to get Paid Status options associated with this seller
        $paid_status_query = "SELECT DISTINCT orders_paid_status FROM orders WHERE orders_seller = $seller_id";
        $paid_status_result = mysqli_query($db, $paid_status_query);
    ?>

    <!-- Form Container -->
    <div class="bg-white rounded-lg p-8 w-full max-w-3xl shadow-md">
        <h2 class="text-2xl font-bold text-center text-gray-700 mb-6">Order Details</h2>
        
        <form action="submit_form.php" method="POST">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                <!-- Customer Name Input -->
                <div>
                    <label for="customer-name" class="block text-sm font-medium text-gray-700">Customer Name</label>
                    <input type="text" id="customer-name" name="customer_name" placeholder="Enter Customer Name" class="mt-1 block w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500" required>
                </div>

                <!-- Customer Address Input -->
                <div>
                    <label for="customer-address" class="block text-sm font-medium text-gray-700">Customer Address</label>
                    <input type="text" id="customer-address" name="customer_address" placeholder="Enter Customer Address" class="mt-1 block w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500" required>
                </div>

                <!-- Customer Channel Dropdown -->
                <div>
                    <label for="customer-channel" class="block text-sm font-medium text-gray-700">Customer Channel</label>
                    <select id="customer-channel" name="customer_channel" class="mt-1 block w-full p-3 border border-gray-300 rounded-md bg-white focus:outline-none focus:ring-blue-500 focus:border-blue-500" required>
                        <option value="" disabled selected>Select Channel</option>
                        <?php
                            while ($channel = mysqli_fetch_assoc($channel_result)) {
                                echo "<option value='{$channel['customer_channel']}'>{$channel['customer_channel']}</option>";
                            }
                        ?>
                        <option value="add_new">Add New Channel</option>
                    </select>
                    <input type="text" id="new-channel" name="new_channel" placeholder="Enter new channel" class="mt-2 block w-full p-3 border border-gray-300 rounded-md bg-white" style="display: none;">
                </div>

                <!-- Paid Status Dropdown -->
                <div>
                    <label for="paid-status" class="block text-sm font-medium text-gray-700">Paid Status</label>
                    <select id="paid-status" name="paid_status" class="mt-1 block w-full p-3 border border-gray-300 rounded-md bg-white focus:outline-none focus:ring-blue-500 focus:border-blue-500" required>
                        <option value="" disabled selected>Select Paid Status</option>
                        <?php
                            while ($status = mysqli_fetch_assoc($paid_status_result)) {
                                echo "<option value='{$status['orders_paid_status']}'>{$status['orders_paid_status']}</option>";
                            }
                        ?>
                        <option value="add_new">Add New Paid Status</option>
                    </select>
                    <input type="text" id="new-paid-status" name="new_paid_status" placeholder="Enter new paid status" class="mt-2 block w-full p-3 border border-gray-300 rounded-md bg-white" style="display: none;">
                </div>

                <!-- Order Date Input -->
                <div class="md:col-span-2">
                    <label for="order-date" class="block text-sm font-medium text-gray-700">Order Date</label>
                    <input type="date" id="order-date" name="order_date" class="mt-1 block w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500" required>
                </div>
                
                <!-- Paid Amount Input -->
                <div class="md:col-span-2">
                    <label for="paid-amount" class="block text-sm font-medium text-gray-700">Paid Amount</label>
                    <input type="number" id="paid-amount" name="orders_paid" placeholder="Enter Paid Amount" class="mt-1 block w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500" required>
                </div>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="mt-6 w-full py-3 bg-blue-600 text-white font-semibold rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50">
                Submit
            </button>

            <!-- Back Button -->
            <a href="../order" class="mt-4 block text-center text-blue-600 hover:text-blue-800 underline">
                Back to Order Main Page
            </a>
        </form>
    </div>

    <script>
        // Show or hide the new channel input based on the selection
        document.getElementById('customer-channel').addEventListener('change', function() {
            var newChannelInput = document.getElementById('new-channel');
            newChannelInput.style.display = this.value === 'add_new' ? 'block' : 'none';
        });

        // Show or hide the new paid status input based on the selection
        document.getElementById('paid-status').addEventListener('change', function() {
            var newPaidStatusInput = document.getElementById('new-paid-status');
            newPaidStatusInput.style.display = this.value === 'add_new' ? 'block' : 'none';
        });
    </script>
</body>
</html>
