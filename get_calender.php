<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once 'database.php'; // Include your database connection class

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $databaseService = new Database();
    $conn = $databaseService->getConnection();

    $response = array();

    // SQL query to fetch dates and their availability
    $sql ="SELECT *FROM calender_table";
    //  "SELECT date, is_available FROM dates ORDER BY date ASC";

    try {
        // Prepare the query
        $stmt = $conn->prepare($sql);

        // Execute the query
        $stmt->execute();

        // Fetch all rows as an associative array
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Check if results are empty
        if (empty($result)) {
            $response["message"] = "No dates found.";
        } else {
            $response = $result; // Assign the fetched data to the response
        }

        // Encode and return JSON response
        echo json_encode($response, JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK);

    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        echo json_encode(["error" => "Database error"]);
    }
} else {
    // Handle invalid request methods
    echo json_encode(["error" => "Invalid request method. Use GET."]);
}

?>
