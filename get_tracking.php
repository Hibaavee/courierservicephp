<?php
header("Content-Type: application/json");

// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Include your database connection class
require_once 'database.php';

// Database connection
$database = new Database();
$conn = $database->getConnection();

// Check if the connection is successful
if (!$conn) {
    die(json_encode(["error" => "Database connection failed"]));
}

// Get the order ID from the POST request
$order_id = isset($_POST['order_id']) ? $_POST['order_id'] : '';

if (empty($order_id)) {
    http_response_code(400);
    echo json_encode(["error" => "Order ID is required"]);
    exit();
}

try {
    // Prepare the SQL query
    $query = "SELECT delivery_status FROM ordertable WHERE order_id = :order_id";
    $stmt = $conn->prepare($query);
    $stmt->bindValue(':order_id', $order_id, PDO::PARAM_INT);

    // Execute the query
    $stmt->execute();

    // Fetch the result
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($order) {
        echo json_encode([
            "order_id" => $order_id,
            "delivery_status" => $order['delivery_status']
        ]);
    } else {
        http_response_code(404);
        echo json_encode(["error" => "Order not found"]);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => "Internal Server Error", "message" => $e->getMessage()]);
}
?>
