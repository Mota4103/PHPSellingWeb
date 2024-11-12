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

    if (isset($_GET['logout'])) {
        logout();
        $path = "/~thursday2024/d6/Final%20Project/login";
        header("Location: " . $path);
        exit;
    }

    // Admin Filter Inventory Template
    $filterInventoryAdmin = '
    <body class="bg-gray-100 flex items-center justify-center min-h-screen">
        <div class="w-full max-w-md p-8 bg-white text-center rounded-lg shadow-lg">
            <h1 class="text-4xl font-bold text-gray-800 mb-8">Filter Inventory (Admin)</h1>
            <a href="../generate_inventory_report">
                <button class="px-6 py-3 bg-blue-500 text-white rounded-lg">Generate Inventory Report</button>
            </a>
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

    // Staff Filter Inventory Template
    $filterInventoryStaff = '
    <body class="bg-gray-100 flex flex-col items-center justify-center h-screen">
        <h1 class="text-4xl font-semibold mb-10">Filter Inventory (Staff)</h1>
        <a href="../filter_options">
            <button class="px-6 py-3 bg-blue-500 text-white rounded-lg">View Filter Options</button>
        </a>
        <!-- Logout Icon -->
        <div class="mt-5">
            <img src="https://img.icons8.com/ios/452/exit.png" alt="Logout" class="w-8 h-8 cursor-pointer" onclick="window.location.href=\'?logout=true\'">
        </div>
    </body>';

    // Seller Filter Inventory Template
    $filterInventorySeller = '
    <body class="bg-gray-100 flex flex-col items-center justify-center h-screen">
        <h1 class="text-4xl font-semibold mb-10">Filter Inventory (Seller)</h1>
        <a href="../filter_options">
            <button class="px-6 py-3 bg-blue-500 text-white rounded-lg">View Filter Options</button>
        </a>
        <!-- Logout Icon -->
        <div class="mt-5">
            <img src="https://img.icons8.com/ios/452/exit.png" alt="Logout" class="w-8 h-8 cursor-pointer" onclick="window.location.href=\'?logout=true\'">
        </div>
    </body>';
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Filter Inventory</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<?php
    if (isset($_SESSION['usertype'])) {
        if ($_SESSION['usertype'] == "staff") {
            echo $filterInventoryStaff;
        } elseif ($_SESSION['usertype'] == "seller") {
            echo $filterInventorySeller;
        } elseif ($_SESSION['usertype'] == "admin") {
            echo $filterInventoryAdmin;
        } else {
            echo "No User Type Found";
        }
    }
?>
</html>
