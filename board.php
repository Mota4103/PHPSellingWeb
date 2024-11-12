<!DOCTYPE html>
<html>
<meta charset="utf-8">
<head>

</head> 

<body>

<?php
require_once("connection.php");

$query="SELECT * FROM `board`";
$result=mysqli_query($db,$query);

while($list=mysqli_fetch_array($result))
{
    $id=$list['id'];
    $message=$list['message'];
    $writer=$list['writer'];
    $write_date=$list['write_date'];

    echo "<br>
        $id $message<br>
       <br>
       From $writer  @ $write_date <br> 
      *************** <br>
    ";

}

?>

POST NEW message  
<form method="POST" action="add_message.php">
ข้อความ <input type="text" name="message"> <br>
จาก <input type="text" name="writer"> <br>

<input type="submit" value="ส่ง"></input>
</form>

</body>

</html>