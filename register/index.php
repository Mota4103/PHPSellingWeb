<!DOCTYPE html>
<html lang="en">
  
<?php
require_once("../connection.php");
function debug_to_console($data) {
  $output = $data;
  if (is_array($output))
      $output = implode(',', $output);

  echo "<script>console.log('Debug Objects: ' . $output . '' );</script>";
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $type = $_POST['type'];
  $username = $_POST['username'];
  $password = $_POST['password'];
  $confirmPassword = $_POST['confirmPassword'];
  if ($password !== $confirmPassword) {
    $error = "Passwords do not match!";
  }
  if($type=="admin"){
    $query= "INSERT INTO board(message, writer) VALUES ('$message', '$writer')";
  }else if($type=="seller"){
    $query= "INSERT INTO seller(seller_ID, seller_username,seller_password) VALUES (NULL, '$username','$password')";
  }else{
    $error = "Type do not match!";
  }
  $queryCheck= "SELECT * FROM seller WHERE seller_username = $username";
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
?>


<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup Page</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen">
    <div class="bg-white shadow-md rounded-lg p-8 max-w-md w-full">
        <h2 class="text-2xl font-bold text-center mb-6">Sign Up</h2>
        <?php if(isset($error) && $error != null){
            echo "<h2 class='text-lg font-bold text-center mb-2 text-red-600'>Error: $error</h2>";
          }?>
        <form method="POST" action="">
            <div class="mb-4">
                <label for="userType" class="block text-sm font-medium text-gray-700">User Type</label>
                <select id="userType" name="type" required class="mt-1 block w-full p-2 border border-gray-300 rounded-md focus:ring focus:ring-blue-300">
                    <option value="">Select user type</option>
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
            <div class="mb-4">
                <label for="confirmPassword" class="block text-sm font-medium text-gray-700">Confirm Password</label>
                <input type="password" id="confirmPassword" name="confirmPassword" required class="mt-1 block w-full p-2 border border-gray-300 rounded-md focus:ring focus:ring-blue-300" placeholder="••••••••">
            </div>
            <div class="mb-6">
                <button type="submit" class="w-full bg-blue-600 text-white font-semibold py-2 rounded-md hover:bg-blue-700 transition duration-200">Sign Up</button>
            </div>
            <p class="text-center text-sm text-gray-600">
                Already have an account? <a href="../login" class="text-blue-600 hover:underline">Log in</a>
            </p>
        </form>
    </div>
</body>
</html>