<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once 'database.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    $databaseService = new Database();
    $conn = $databaseService->getConnection();

    $response = array();

    // Base URL for images (make sure to adjust it based on where your icons are located)
    $baseUrl = "http://192.168.29.60/courierservicephp/image/"; // Replace with the actual base URL where icons are stored

    // SQL query
    $sql = "SELECT * FROM package_details";

    try {
        // Prepare the query 
        $stmt = $conn->prepare($sql);
        
        // Execute the query
        $stmt->execute();
        
        // Fetch all rows as an associative array
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Check if results are empty
        if (empty($result)) {
            $response["message"] = "No records found.";
        } else {
            // Iterate over the results and add the full image URL
            foreach ($result as &$row) {
                // Assuming 'icon' is the column that stores the image filename
                $row['icon_url'] = $baseUrl . $row['icon']; // Append the base URL to the icon file name
            }
            $response = $result;
        }
        
        // Encode and return JSON response
        echo json_encode($response, JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK);

    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        echo json_encode(["error" => "Database error"]);
    }
}
?>
