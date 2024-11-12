<!DOCTYPE html>
<html lang="en">
<?php
    session_start();

    function redirect($url) {
        header('Location: ' . $url);
        die();
    }

    function logout() {
        unset($_SESSION["usertype"]);
        unset($_SESSION["id"]);
    }

    function debug_to_console($data) {
        $output = is_array($data) ? implode(',', $data) : $data;
        echo "<script>console.log('Debug: " . $output . "');</script>";
    }

    if (isset($_GET['logout'])) {
        logout();
        $path = "/~thursday2024/d6/Final%20Project/login";
        header("Location: " . $path);
        exit;
    }
?>

<?php
    // Admin body template
    $bodyAdmin = '
    <body class="bg-gray-100 flex items-center justify-center min-h-screen">
        <div class="w-full max-w-md p-8 bg-white text-center rounded-lg shadow-lg">
            <h1 class="text-4xl font-bold text-gray-800 mb-8">Home</h1>
            <div class="grid grid-cols-2 gap-4">
                <!-- Adjust Seller Button -->
                <a href="../adjust_seller">
                    <div class="bg-gray-200 rounded-lg p-6 flex items-center justify-center">
                        <button class="text-gray-800 font-semibold text-lg">Adjust Seller</button>
                    </div>
                </a>
                <!-- Adjust Staff Button -->
                <a href="../adjust_staff">
                    <div class="bg-gray-200 rounded-lg p-6 flex items-center justify-center">
                        <button class="text-gray-800 font-semibold text-lg">Adjust Staff</button>
                    </div>
                </a>
                <!-- Delete Inventory & Order Button -->
                <div class="flex justify-center col-span-2">
                    <a href="../delete_inventory&order" class="block w-full">
                        <div class="bg-gray-200 rounded-lg p-6 flex items-center justify-center">
                            <button class="text-gray-800 font-semibold text-lg">Delete Inventory & Order</button>
                        </div>
                    </a>
                </div>
            </div>
            <!-- Logout Button -->
            <div class="mt-8">
                <button onclick="window.location.href=\'?logout=true\'">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-gray-800" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1m0-10V5m0 14h6" />
                    </svg>
                </button>
            </div>
        </div>
    </body>';

    // Staff body template
    $bodyStaff = '
    <body class="bg-gray-100 flex flex-col items-center justify-center h-screen">
        <h1 class="text-4xl font-semibold mb-10">Home</h1>
        <div class="flex justify-center space-x-10">
            <!-- Inventory Button -->
            <div class="bg-white p-6 rounded-lg shadow-lg text-center cursor-pointer" onclick="window.location.href=\'../inventory\'">
                <img src="https://img.icons8.com/ios/452/inventory.png" alt="Inventory" class="mx-auto w-16 h-16 mb-4">
                <p class="text-lg font-semibold">Inventory</p>
            </div>
            <!-- Order Button -->
            <div class="bg-white p-6 rounded-lg shadow-lg text-center cursor-pointer" onclick="window.location.href=\'../order\'">
                <img src="https://img.icons8.com/ios/452/order-history.png" alt="Order" class="mx-auto w-16 h-16 mb-4">
                <p class="text-lg font-semibold">Order</p>
            </div>
        </div>
        <!-- Logout Icon -->
        <div class="mt-5">
            <img src="https://img.icons8.com/ios/452/exit.png" alt="Logout" class="w-8 h-8 cursor-pointer" onclick="window.location.href=\'?logout=true\'">
        </div>
    </body>';

    // Seller body template
    $bodySeller = '
    <body class="bg-gray-100 flex flex-col items-center justify-center h-screen">
        <h1 class="text-4xl font-semibold mb-10">Home</h1>
        <div class="flex justify-center space-x-10">
            <!-- Inventory Button -->
            <div class="bg-white p-6 rounded-lg shadow-lg text-center cursor-pointer" onclick="window.location.href=\'../inventory\'">
                <img src="https://img.icons8.com/ios-filled/50/000000/warehouse.png" alt="Inventory" class="mx-auto w-16 h-16 mb-4">
                <p class="text-lg font-semibold">Inventory</p>
            </div>
            <!-- Order Button -->
            <div class="bg-white p-6 rounded-lg shadow-lg text-center cursor-pointer" onclick="window.location.href=\'../order\'">
                <img src="https://img.icons8.com/ios/452/order-history.png" alt="Order" class="mx-auto w-16 h-16 mb-4">
                <p class="text-lg font-semibold">Order</p>
            </div>
        </div>
        <!-- Add Staff Button -->
        <button class="mt-10 px-5 py-3 bg-blue-500 text-white font-semibold rounded-lg hover:bg-blue-600" onclick="window.location.href=\'../add_staff\'">
            + Add Staff
        </button>
        <!-- Logout Icon -->
        <div class="mt-5">
            <img src="https://img.icons8.com/ios/452/exit.png" alt="Logout" class="w-8 h-8 cursor-pointer" onclick="window.location.href=\'?logout=true\'">
        </div>
    </body>';
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<?php
    debug_to_console($_SESSION["usertype"]);
    if (isset($_SESSION['usertype'])) {
        if ($_SESSION['usertype'] == "staff") {
            echo $bodyStaff;
        } elseif ($_SESSION['usertype'] == "seller") {
            echo $bodySeller;
        } elseif ($_SESSION['usertype'] == "admin") {
            echo $bodyAdmin;
        } else {
            echo "No User Type Found";
        }
    }
?>
</html>
