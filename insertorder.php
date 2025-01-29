<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

include 'database.php';

// Decode JSON input
$input = json_decode(file_get_contents('php://input'), true);

// Log the input data for debugging
error_log("Received Data: " . print_r($input, true));

// Check if the input is null after decoding
if ($input === null) {
    echo json_encode(["status" => "error", "msg" => "JSON decode error."]);
    exit;
}

$database = new Database();
$conn = $database->getConnection();

// Check if connection was successful
if ($conn == null) {
    echo json_encode(["status" => "error", "msg" => "Database connection failed."]);
    exit;
}

// Extract input fields
$username = $input['username'] ?? null;
$package_id = $input['package_id'] ?? null;
$size_id = $input['size_id'] ?? null;
$content_id = $input['content_id'] ?? null;
$price = $input['price'] ?? null;
$pickup_date = $input['pickup_date'] ?? null;
$delivery_date = $input['delivery_date'] ?? null;
$amount_safety = $input['amount_safety'] ?? null;
$is_safe = $input['is_safe'] ?? null;
$delivery_status = $input['delivery_status'] ?? null;
$payment = $input['payment'] ?? null;

// Pickup address fields
$pickup_name = $input['pickup_name'] ?? null;
$pickup_mobile_no = $input['pickup_mobile_no'] ?? null;
$pickup_address = $input['pickup_address'] ?? null;
$pickup_area = $input['pickup_area'] ?? null;
$pickup_pincode = $input['pickup_pincode'] ?? null;

// Delivery address fields
$delivery_name = $input['delivery_name'] ?? null;
$delivery_mobile_no = $input['delivery_mobile_no'] ?? null;
$delivery_address = $input['delivery_address'] ?? null;
$delivery_area = $input['delivery_area'] ?? null;
$delivery_pincode = $input['delivery_pincode'] ?? null;

// Validate required fields
$missingFields = [];
if ($username === null) $missingFields[] = 'username';
if ($package_id === null) $missingFields[] = 'package_id';
if ($size_id === null) $missingFields[] = 'size_id';
if ($content_id === null) $missingFields[] = 'content_id';
if ($price === null) $missingFields[] = 'price';
if ($pickup_date === null) $missingFields[] = 'pickup_date';
if ($delivery_date === null) $missingFields[] = 'delivery_date';
if ($pickup_name === null) $missingFields[] = 'pickup_name';
if ($pickup_mobile_no === null) $missingFields[] = 'pickup_mobile_no';
if ($pickup_address === null) $missingFields[] = 'pickup_address';
if ($pickup_area === null) $missingFields[] = 'pickup_area';
if ($pickup_pincode === null) $missingFields[] = 'pickup_pincode';
if ($delivery_name === null) $missingFields[] = 'delivery_name';
if ($delivery_mobile_no === null) $missingFields[] = 'delivery_mobile_no';
if ($delivery_address === null) $missingFields[] = 'delivery_address';
if ($delivery_area === null) $missingFields[] = 'delivery_area';
if ($delivery_pincode === null) $missingFields[] = 'delivery_pincode';

if (!empty($missingFields)) {
    echo json_encode(["status" => "error", "msg" => "Missing fields: " . implode(', ', $missingFields)]);
    exit;
}
// Extract only the date part from the pickup_date and delivery_date
if (isset($pickup_date)) {
    // Convert to 'YYYY-MM-DD' format if needed
    $pickup_date = substr($pickup_date, 0, 10); // Get only the date part
}

if (isset($delivery_date)) {
    // Convert to 'YYYY-MM-DD' format if needed
    $delivery_date = substr($delivery_date, 0, 10); // Get only the date part
}


// Begin transaction
$conn->beginTransaction();

try {
    // Insert into ordertable
    $orderQuery = "
        INSERT INTO ordertable (
           username, package_id, size_id, content_id, price, 
            pickup_date, delivery_date, amount_safety, is_safe, 
            delivery_status, payment
        ) VALUES (
           :username, :package_id, :size_id, :content_id, :price, 
            :pickup_date, :delivery_date, :amount_safety, :is_safe, 
            :delivery_status, :payment
        )
    ";

    $stmt = $conn->prepare($orderQuery);
    $stmt->execute([
        ':username' => $username,
        ':package_id' => $package_id,
        ':size_id' => $size_id,
        ':content_id' => $content_id,
        ':price' => $price,
        ':pickup_date' => $pickup_date,
        ':delivery_date' => $delivery_date,
        ':amount_safety' => $amount_safety,
        ':is_safe' => $is_safe,
        ':delivery_status' => $delivery_status,
        ':payment' => $payment
    ]);

    $orderId = $conn->lastInsertId(); // Get the last inserted order ID

    // Insert into pickup_address
    $pickupQuery = "
        INSERT INTO pickup_address (
            orderid, name, username, mobile_no, address, area, pincode
        ) VALUES (
            :orderid, :name, :username, :mobile_no, :address, :area, :pincode
        )
    ";

    $stmt = $conn->prepare($pickupQuery);
    $stmt->execute([
        ':orderid' => $orderId,
        ':name' => $pickup_name,
        ':username' => $username,
        ':mobile_no' => $pickup_mobile_no,
        ':address' => $pickup_address,
        ':area' => $pickup_area,
        ':pincode' => $pickup_pincode
    ]);

    // Insert into delivery_address
    $deliveryQuery = "
        INSERT INTO delivery_address (
            orderid, name, username, mobile_no, address, area, pincode
        ) VALUES (
            :orderid, :name, :username, :mobile_no, :address, :area, :pincode
        )
    ";

    $stmt = $conn->prepare($deliveryQuery);
    $stmt->execute([
        ':orderid' => $orderId,
        ':name' => $delivery_name,
        ':username' => $username,
        ':mobile_no' => $delivery_mobile_no,
        ':address' => $delivery_address,
        ':area' => $delivery_area,
        ':pincode' => $delivery_pincode
    ]);

    // Commit transaction
    $conn->commit();

    echo json_encode(["status" => "success", "msg" => "Order and addresses created successfully."]);

} catch (PDOException $e) {
    $conn->rollBack(); // Rollback transaction on error
    echo json_encode(["status" => "error", "msg" => $e->getMessage()]);
}
?>
