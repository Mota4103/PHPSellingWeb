<!DOCTYPE html>
<html lang="en">
<?php
session_start();
require_once("../connection.php");

function debug_to_console($data) {
    $output = $data;
    if (is_array($output)) {
        $output = implode(',', $output);
    }
    echo "<script>console.log('Debug Objects: " . $output . "' );</script>";
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve username and password from the form
    $username = mysqli_real_escape_string($db, $_POST['username']);
    $password = mysqli_real_escape_string($db, $_POST['password']);

    // Check if the user is logged in with session ID and usertype
    if (!isset($_SESSION["id"]) || !isset($_SESSION["usertype"])) {
        $path = "/~thursday2024/d6/Final%20Project/login";
        header("Location: " . $path);
        exit;
    } else {
        $id = $_SESSION["id"];

        // Check if the username already exists
        $queryCheck = "SELECT * FROM staff WHERE staff_username = '$username'";
        $resultCheck = mysqli_query($db, $queryCheck);

        if (mysqli_num_rows($resultCheck) > 0) {
            $error = "Username already exists";
        } else {
            // Insert new staff record
            $query = "INSERT INTO staff (staff_id, staff_username, staff_password, seller_id) VALUES (NULL, '$username', '$password', $id)";
            $result = mysqli_query($db, $query);

            if (!$result) {
                $error = "Failed to add staff: " . mysqli_error($db);
            } else {
                $success = "Staff added successfully!";
            }
        }
    }
}
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Staff</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen">
    <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-md">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold">Add New Staff</h2>
            <button class="text-gray-500 hover:text-gray-700" onclick="window.location.href='../home'">&times;</button>
        </div>

        <!-- Display success or error message -->
        <?php if (isset($success)): ?>
            <p class="mb-4 text-green-600"><?= $success ?></p>
        <?php elseif (isset($error)): ?>
            <p class="mb-4 text-red-600"><?= $error ?></p>
        <?php endif; ?>

        <!-- Add Staff Form -->
        <form action="" method="POST">
            <!-- Username Field -->
            <div class="mb-4">
                <label for="username" class="block text-sm font-medium text-gray-700">Username</label>
                <div class="flex">
                    <input type="text" id="username" name="username" required class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Enter username">
                </div>
            </div>

            <!-- Password Field -->
            <div class="mb-4">
                <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                <div class="flex">
                    <input type="password" id="password" name="password" required class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Enter password">
                    
                </div>
            </div>

            <!-- Add Button -->
            <div class="flex justify-end">
                <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-5 py-2 rounded-md flex items-center shadow-md">
                    &#9776; Add
                </button>
            </div>
        </form>
    </div>
</body>
</html>

