<?php
require_once("../connection.php");
session_start();

$seller_id = $_SESSION['id'];

// Base query
$query = "SELECT * FROM orders WHERE orders_seller = $seller_id";

// Add filter conditions if they are provided
$filters = [];
if (!empty($_GET['customer_name'])) {
    $customer_name = mysqli_real_escape_string($db, $_GET['customer_name']);
    $filters[] = "customer_name LIKE '%$customer_name%'";
}
if (!empty($_GET['customer_channel'])) {
    $customer_channel = mysqli_real_escape_string($db, $_GET['customer_channel']);
    $filters[] = "customer_channel = '$customer_channel'";
}
if (!empty($_GET['orders_paid_status'])) {
    $orders_paid_status = mysqli_real_escape_string($db, $_GET['orders_paid_status']);
    $filters[] = "orders_paid_status = '$orders_paid_status'";
}
if (!empty($_GET['min_paid'])) {
    $min_paid = (int)$_GET['min_paid'];
    $filters[] = "orders_paid >= $min_paid";
}
if (!empty($_GET['max_paid'])) {
    $max_paid = (int)$_GET['max_paid'];
    $filters[] = "orders_paid <= $max_paid";
}
if (!empty($_GET['customer_address'])) {
    $customer_address = mysqli_real_escape_string($db, $_GET['customer_address']);
    $filters[] = "orders_address LIKE '%$customer_address%'";
}
if (!empty($_GET['start_date'])) {
    $start_date = mysqli_real_escape_string($db, $_GET['start_date']);
    $filters[] = "orders_date >= '$start_date'";
}
if (!empty($_GET['end_date'])) {
    $end_date = mysqli_real_escape_string($db, $_GET['end_date']);
    $filters[] = "orders_date <= '$end_date'";
}

// Combine filters with 'AND'
if (count($filters) > 0) {
    $query .= " AND " . implode(" AND ", $filters);
}

// Execute the query
$result = mysqli_query($db, $query);

// Return HTML for the filtered table rows
$output = '';
while ($list = mysqli_fetch_assoc($result)) {
    $output .= "<tr>
                    <td>{$list['orders_ID']}</td>
                    <td>{$list['customer_name']}</td>
                    <td>{$list['customer_channel']}</td>
                    <td>{$list['customer_address']}</td>
                    <td>{$list['orders_paid_status']}</td>
                    <td>{$list['orders_paid']}</td>
                    <td>{$list['orders_date']}</td>
                    <td>{$list['orders_seller']}</td>
                </tr>";
}
echo $output;
$db->close();
?>
