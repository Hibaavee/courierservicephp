<?php
include_once 'database.php';
// Create database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Read raw POST data
    $data = json_decode(file_get_contents("php://input"), true);

    // Check if the required data is present
    $orderid = isset($data['orderid']) ? $data['orderid'] : null;
    $name = isset($data['name']) ? $data['name'] : null;
    $username = isset($data['username']) ? $data['username'] : null;
    $mobile_no = isset($data['mobile_no']) ? $data['mobile_no'] : null;
    $address = isset($data['address']) ? $data['address'] : null;
    $area = isset($data['area']) ? $data['area'] : null;
    $pincode = isset($data['pincode']) ? $data['pincode'] : null;

    // Validate that required fields are not empty or null
    if (empty($orderid) || empty($name) || empty($username) || empty($mobile_no) || empty($address) || empty($area) || empty($pincode)) {
        echo json_encode(["error" => "All fields are required and cannot be empty"]);
        exit;
    }

    if ($orderid === null) {
        echo json_encode(["error" => "Order ID cannot be null"]);
        exit;
    }

    $databaseService = new Database();
    $conn = $databaseService->getConnection();

    // SQL query to insert delivery address
    $insertSql = "INSERT INTO pickup_address (orderid, name, username, mobile_no, address, area, pincode) 
                  VALUES (:orderid, :name, :username, :mobile_no, :address, :area, :pincode)";

    try {
        // Prepare the insert query
        $insertStmt = $conn->prepare($insertSql);

        // Bind parameters
        $insertStmt->bindParam(':orderid', $orderid, PDO::PARAM_INT);
        $insertStmt->bindParam(':name', $name);
        $insertStmt->bindParam(':username', $username);
        $insertStmt->bindParam(':mobile_no', $mobile_no, PDO::PARAM_STR);  // Change this to PARAM_STR
        $insertStmt->bindParam(':address', $address);
        $insertStmt->bindParam(':area', $area);
        $insertStmt->bindParam(':pincode', $pincode, PDO::PARAM_INT);

        // Execute the insert query
        if ($insertStmt->execute()) {
            echo json_encode([
                "message" => "pickup address added successfully."
            ], JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK);
        } else {
            echo json_encode(["error" => "Failed to add pickup address."], JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK);
        }
    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        echo json_encode(["error" => "Database error: " . $e->getMessage()]);
    }
}
?>