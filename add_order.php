<?php
include 'database.php';

// Get data from POST request
$username = $_POST['username'];
$package_id = $_POST['package_id'];
$size_id = $_POST['size_id'];
$content_id = $_POST['content_id'];
$price = $_POST['price'];
$pickup_date = $_POST['pickup_date'];
$delivery_date = $_POST['delivery_date'];
$amount_safety = $_POST['amount_safety'];
$is_safe = $_POST['is_safe'];
$delivery_status = $_POST['delivery_status'];
$deliverytype_id = $_POST['deliverytype_id'];
$delivery_id = $_POST['delivery_id'];
$pickup_id = $_POST['pickup_id'];

// Prepare and execute the query
$query = "INSERT INTO ordertable (username, package_id, size_id, content_id, price, pickup_date, delivery_date, 
          amount_safety, is_safe, delivery_status, deliverytype_id, delivery_id, pickup_id) 
          VALUES (:username, :package_id, :size_id, :content_id, :price, :pickup_date, :delivery_date, 
          :amount_safety, :is_safe, :delivery_status, :deliverytype_id, :delivery_id, :pickup_id)";

$stmt = $pdo->prepare($query);
$stmt->bindParam(':username', $username);
$stmt->bindParam(':package_id', $package_id);
$stmt->bindParam(':size_id', $size_id);
$stmt->bindParam(':content_id', $content_id);
$stmt->bindParam(':price', $price);
$stmt->bindParam(':pickup_date', $pickup_date);
$stmt->bindParam(':delivery_date', $delivery_date);
$stmt->bindParam(':amount_safety', $amount_safety);
$stmt->bindParam(':is_safe', $is_safe);
$stmt->bindParam(':delivery_status', $delivery_status);
$stmt->bindParam(':deliverytype_id', $deliverytype_id);
$stmt->bindParam(':delivery_id', $delivery_id);
$stmt->bindParam(':pickup_id', $pickup_id);

if ($stmt->execute()) {
    echo "Order added successfully.";
} else {
    echo "Error adding order.";
}
?>
