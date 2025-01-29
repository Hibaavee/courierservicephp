<?php

// Allow cross-origin requests and set content type to JSON
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once 'database.php'; // Include your database connection file

// Check if the request method is GET
if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    // Create an instance of the Database class and get the connection
    $databaseService = new Database();
    $conn = $databaseService->getConnection();

    $response = array();

    // Check if 'Km' parameter is passed in the GET request
    if (isset($_GET['Km'])) {
        $Km = floatval($_GET['Km']); // Convert the distance to float

        // SQL query to fetch the price where Km <= provided distance
        // The query will return the price for the closest Km <= provided distance
        $sql = "SELECT price FROM distance_table WHERE Km <= :Km ORDER BY Km DESC LIMIT 1";

        try {
            // Prepare the SQL statement
            $stmt = $conn->prepare($sql);
            
            // Bind the 'Km' parameter to the query
            $stmt->bindParam(':Km', $Km, PDO::PARAM_STR);

            // Execute the query
            $stmt->execute();

            // Fetch the result
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            // Check if a price is found
            if ($result) {
                $response["price"] = $result['price'];  // Return the price
            } else {
                $response["message"] = "No price found for the given distance.";
                $response["price"] = 0.0; // Default price if no match is found
            }
        } catch (PDOException $e) {
            // Log database error and return a response
            error_log("Database error: " . $e->getMessage());
            $response["error"] = "Database error occurred.";
        }
    } else {
        $response["error"] = "Distance (Km) parameter is missing.";
    }

    // Encode the response as JSON and return it
    echo json_encode($response, JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK);
}

?>
