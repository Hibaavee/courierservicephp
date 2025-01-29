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
if ( isset($input['username']) &&isset($input['comments'])) {
    
    $username = $input['username'] ?? null;
  
    $comments = $input['comments'] ?? null;
    // Use prepared statements to prevent SQL injection
    $query = "INSERT INTO feedback (username, comments) VALUES ( :username, :comments)";
    $stmt = $conn->prepare($query);

    try {
        // Bind parameters
       
        $stmt->bindParam(':username', $username);
    

        $stmt->bindParam(':comments', $comments);
        // Execute query
        if ($stmt->execute()) {
            echo json_encode(["status" => "success", "msg" => "success"]);
        } else {
            echo json_encode(["status" => "error", "msg" => "Failed"]);
        }
    } catch (PDOException $e) {
        echo json_encode(["status" => "error", "msg" => $e->getMessage()]);
    }
} else {
    echo json_encode(["status" => "error", "msg" => "Missing required fields."]);
}
?>