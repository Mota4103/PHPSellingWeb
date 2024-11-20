<!DOCTYPE html>
<html lang="en">
<?php
require_once("connection.php");
session_start();

function debug_to_console($data) {
    $output = $data;
    if (is_array($output)) {
        $output = implode(',', $output);
    }
    echo "<script>console.log('Debug: " . $output . "' );</script>";
}

$error = null;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $type = $_POST['type'];
    $username = $_POST['username'];
    $password = $_POST['password'];

    try {
        // Validate user type
        if (in_array($type, ["admin", "seller", "staff"])) {
            $query = "";
            $idField = "";

            // Define table, id fields based on user type
            if ($type === "admin") {
                $query = "SELECT * FROM admin WHERE admin_username = ? AND admin_password = ?";
                $idField = "admin_ID";
            } elseif ($type === "seller") {
                $query = "SELECT * FROM seller WHERE seller_username = ? AND seller_password = ?";
                $idField = "seller_ID";
            } elseif ($type === "staff") {
                $query = "SELECT * FROM staff WHERE staff_username = ? AND staff_password = ?";
                $idField = "staff_id";
            }

            // Prepare and execute the SQL statement
            $stmt = $db->prepare($query);
            $stmt->bind_param("ss", $username, $password);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows === 1) {
                $list = $result->fetch_assoc();
                $_SESSION["usertype"] = $type;
                $_SESSION["id"] = $list[$idField];
                $_SESSION["unique_id"] = $type === "staff" ? $list["staff_id"] : null;

                $path = "/~thursday2024/d6/Final%20Project/home";
                header("Location: " . $path);
                exit;
            } else {
                $error = "Invalid Credentials.";
            }
        } else {
            $error = "Invalid User Type.";
        }
    } catch (Exception $e) {
        $error = "An error occurred: " . $e->getMessage();
    }
}
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen">
    <div class="bg-white shadow-md rounded-lg p-8 max-w-md w-full">
        <h2 class="text-2xl font-bold text-center mb-6">Login</h2>
        <?php if($error): ?>
            <h2 class="text-lg font-bold text-center mb-2 text-red-600">Error: <?php echo $error; ?></h2>
        <?php endif; ?>
        <form method="POST" action="">
            <div class="mb-4">
                <label for="userType" class="block text-sm font-medium text-gray-700">User Type</label>
                <select id="userType" name="type" required class="mt-1 block w-full p-2 border border-gray-300 rounded-md focus:ring focus:ring-blue-300">
                    <option value="">Select user type</option>
                    <option value="admin">Admin</option>
                    <option value="staff">Staff</option>
                    <option value="seller">Seller</option>
                </select>
            </div>
            <div class="mb-4">
                <label for="name" class="block text-sm font-medium text-gray-700">Username</label>
                <input type="text" id="name" name="username" required class="mt-1 block w-full p-2 border border-gray-300 rounded-md focus:ring focus:ring-blue-300" placeholder="John Doe">
            </div>
            <div class="mb-4">
                <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                <input type="password" id="password" name="password" required class="mt-1 block w-full p-2 border border-gray-300 rounded-md focus:ring focus:ring-blue-300" placeholder="••••••••">
            </div>
            <div class="mb-6">
                <button type="submit" class="w-full bg-blue-600 text-white font-semibold py-2 rounded-md hover:bg-blue-700 transition duration-200">Login</button>
            </div>
            <p class="text-center text-sm text-gray-600">
                Don't have an account? <a href="../register" class="text-blue-600 hover:underline">Sign up</a>
            </p>
        </form>
    </div>
</body>
</html>
