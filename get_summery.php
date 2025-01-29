<?php

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Debug start of the script
echo json_encode(["debug" => "Script started"]);

// Set headers for CORS and JSON response
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

// Include database file
include_once 'database.php';
echo json_encode(["debug" => "Headers and database file included"]);

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    echo json_encode(["debug" => "GET request detected"]);

    // Initialize database connection
    try {
        $databaseService = new Database();
        $conn = $databaseService->getConnection();

        if ($conn) {
            echo json_encode(["debug" => "Database connection established"]);
        } else {
            echo json_encode(["error" => "Database connection failed"]);
            exit;
        }
    } catch (PDOException $e) {
        echo json_encode(["error" => "Database connection error", "details" => $e->getMessage()]);
        exit;
    }

    // Prepare response array
    $response = array();

    // Get the order ID from query parameters
    $order_id = isset($_GET['orderid']) ? intval($_GET['orderid']) : 0;
    echo json_encode(["debug" => "Order ID received", "order_id" => $order_id]);

    if ($order_id > 0) {
        try {
            echo json_encode(["debug" => "Valid order ID, proceeding with queries"]);

            // Fetch pickup address
            $pickupQuery = "SELECT name, address, area, pincode FROM pickup_address WHERE orderid = :orderid LIMIT 1";
            $pickupStmt = $conn->prepare($pickupQuery);
            $pickupStmt->bindParam(':orderid', $order_id, PDO::PARAM_INT);
            $pickupStmt->execute();
            $pickupResult = $pickupStmt->fetch(PDO::FETCH_ASSOC);
            $response['pickup_address'] = $pickupResult ?: null;
            echo json_encode(["debug" => "Pickup address fetched", "pickup_address" => $pickupResult]);

            // Fetch delivery address
            $deliveryQuery = "SELECT name, address, area, pincode FROM delivery_address WHERE orderid = :orderid LIMIT 1";
            $deliveryStmt = $conn->prepare($deliveryQuery);
            $deliveryStmt->bindParam(':orderid', $order_id, PDO::PARAM_INT);
            $deliveryStmt->execute();
            $deliveryResult = $deliveryStmt->fetch(PDO::FETCH_ASSOC);
            $response['delivery_address'] = $deliveryResult ?: null;
            echo json_encode(["debug" => "Delivery address fetched", "delivery_address" => $deliveryResult]);

            // Fetch package details
            $packageQuery = "SELECT size, range FROM package_size WHERE package_id = :orderid LIMIT 1";
            $packageStmt = $conn->prepare($packageQuery);
            $packageStmt->bindParam(':orderid', $order_id, PDO::PARAM_INT);
            $packageStmt->execute();
            $packageResult = $packageStmt->fetch(PDO::FETCH_ASSOC);
            $response['package_details'] = $packageResult ?: null;
            echo json_encode(["debug" => "Package details fetched", "package_details" => $packageResult]);

            // Fetch schedule details
            $scheduleQuery = "SELECT date, time_slot FROM calender_table WHERE orderid = :orderid LIMIT 1";
            $scheduleStmt = $conn->prepare($scheduleQuery);
            $scheduleStmt->bindParam(':orderid', $order_id, PDO::PARAM_INT);
            $scheduleStmt->execute();
            $scheduleResult = $scheduleStmt->fetch(PDO::FETCH_ASSOC);
            $response['calender_table'] = $scheduleResult ?: null;
            echo json_encode(["debug" => "Schedule details fetched", "schedule_details" => $scheduleResult]);

            // Return the complete response
            echo json_encode($response, JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK);

        } catch (PDOException $e) {
            // Log error and return a response with error details
            error_log("Database error: " . $e->getMessage());
            echo json_encode(["error" => "Database query error", "details" => $e->getMessage()]);
        }
    } else {
        // Handle invalid or missing order ID
        echo json_encode(["error" => "Invalid or missing order ID"]);
    }
} else {
    // Handle invalid HTTP request method
    echo json_encode(["error" => "Invalid request method. Only GET is allowed."]);
}

?>
