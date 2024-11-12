<!DOCTYPE html>
<html lang="en">
<?php
require_once("../connection.php");
function debug_to_console($data) {
    $output = $data;
    if (is_array($output))
        $output = implode(',', $output);

    echo "<script>console.log('Debug Objects: " . $output . "' );</script>";
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $username = $_POST['username'];
  $password = $_POST['password'];
  if(!isset($_SESSION["id"])||!isset($_SESSION["usertype"])) {
    $path = "/~thursday2024/d6/Final%20Project/login";
    header("Location: ".$path);
    exit;
  } else {
    $id = $_SESSION["id"];
    $queryCheck= "SELECT * FROM seller WHERE seller_username = $username";
    $query = "INSERT INTO staff(staff_id, staff_username,staff_password,seller_id) VALUES (NULL, '$username','$password',$id)";
    $result2 = mysqli_query($db,$queryCheck);
    $result = mysqli_query($db,$query);
    if($result2){
        $list = mysqli_fetch_array($result2);
        if(!$list){
        if (!mysqli_query($db,$query) && !isset($error)) {
            $error = mysqli_error($db);
        }
        }else{
        $error = "Username already exists";
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
            <h2 class="text-2xl font-bold">Add new staff</h2>
            <button class="text-gray-500 hover:text-gray-700" onclick="window.location.href='../home'">&times;</button>
        </div>

        <!-- Add Staff Form -->
        <form action="" method="POST">
            <!-- Username Field -->
            <div class="mb-4">
                <label for="username" class="block text-sm font-medium text-gray-700">Username</label>
                <div class="flex">
                    <input type="text" id="username" name="username" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Enter username">
                    <button type="button" class="ml-2 bg-gray-200 hover:bg-gray-300 text-gray-700 px-3 py-2 rounded-md shadow-sm">Auto Generate</button>
                </div>
            </div>

            <!-- Password Field -->
            <div class="mb-4">
                <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                <div class="flex">
                    <input type="password" id="password" name="password" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Enter password">
                    <button type="button" class="ml-2 bg-gray-200 hover:bg-gray-300 text-gray-700 px-3 py-2 rounded-md shadow-sm">Auto Generate</button>
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
