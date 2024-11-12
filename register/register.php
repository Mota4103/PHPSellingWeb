<?php
require_once("../connection.php");

$type = $_POST['type'];
$username = $_POST['username'];
$password = $_POST['password'];
$confirmPassword = $_POST['confirmPassword'];

if ($password !== $confirmPassword) {
  echo "Passwords do not match!";
  exit;
}
if($type=="admin"){
  $query= "INSERT INTO board(message, writer) VALUE ('$message', '$writer')";
}else if($type=="seller"){
  $query= "INSERT INTO seller(seller_ID, seller_username,seller_password) VALUE (NULL, $username,$password)";
}else{
  echo "Type do not match!";
  exit;
}

mysqli_query($db,$query);
echo $query;

?>
