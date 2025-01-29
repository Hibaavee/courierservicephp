<?php
include 'database.php';

// Decode JSON input
$input = json_decode(file_get_contents('php://input'), true);

// Log the input data to check
error_log("Received Data: " . print_r($input, true));

// Check if the data is null after decoding
if ($input === null) {
    echo json_encode(["status" => "error", "msg" => "JSON decode error."]);
    exit;
}

$database = new Database();
$conn = $database->getConnection();

// Check if connection was successful
if ($conn == null) {
    echo json_encode(["status" => "error", "msg" => "Connection failed."]);
    exit;
}

// Validate input
if ( isset($input['first_name']) && isset($input['last_name'])&&isset($input['email'])) {
    
    $first_name = $input['first_name'] ?? null;
    $last_name = $input['last_name'] ?? null;
    $email = $input['email'] ?? null;
    // Use prepared statements to prevent SQL injection
    $query = "INSERT INTO register (first_name, last_name,email) VALUES ( :first_name, :last_name,:email)";
    $stmt = $conn->prepare($query);

    try {
        // Bind parameters
       
        $stmt->bindParam(':first_name', $first_name);
        $stmt->bindParam(':last_name', $last_name);

        $stmt->bindParam(':email', $email);
        // Execute query
        if ($stmt->execute()) {
            echo json_encode(["status" => "success", "msg" => "Profile created successfully."]);
        } else {
            echo json_encode(["status" => "error", "msg" => "Failed to create profile."]);
        }
    } catch (PDOException $e) {
        echo json_encode(["status" => "error", "msg" => $e->getMessage()]);
    }
} else {
    echo json_encode(["status" => "error", "msg" => "Missing required fields."]);
}
?>
