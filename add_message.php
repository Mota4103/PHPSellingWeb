<?php
require_once("connection.php");

$message = $_POST['message'];
$writer = $_POST['writer'];

$query="INSERT INTO board(message, writer) VALUE ('$message', '$writer')";
mysqli_query($db,$query);
echo $query;
?>