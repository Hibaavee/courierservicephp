<?php
include 'database.php';

$databaseService = new Database();
$conn = $databaseService->getConnection();

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get orderid from request
$orderid = isset($_GET['orderid']) ? intval($_GET['orderid']) : 0;

if ($orderid > 0) {
    // Fetch pickup address
    $pickup_sql = "SELECT * FROM pickup_address WHERE orderid = ?";
    $pickup_stmt = $conn->prepare($pickup_sql);
    $pickup_stmt->bind_param("i", $orderid);
    $pickup_stmt->execute();
    $pickup_result = $pickup_stmt->get_result();
    $pickup_data = $pickup_result->fetch_assoc();

    // Fetch delivery address
    $delivery_sql = "SELECT * FROM delivery_address WHERE orderid = ?";
    $delivery_stmt = $conn->prepare($delivery_sql);
    $delivery_stmt->bind_param("i", $orderid);
    $delivery_stmt->execute();
    $delivery_result = $delivery_stmt->get_result();
    $delivery_data = $delivery_result->fetch_assoc();

    // Response
    echo json_encode([
        "status" => "success",
        "pickup_address" => $pickup_data,
        "delivery_address" => $delivery_data,
    ]);
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Invalid order ID",
    ]);
}


$conn->close();
?>
