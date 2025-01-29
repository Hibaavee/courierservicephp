<?php

// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

// header("Access-Control-Allow-Origin: *");
// header("Content-Type: application/json; charset=UTF-8");

// include_once 'database.php';

// if ($_SERVER['REQUEST_METHOD'] === 'POST') {
//     $username = isset($_POST['username']) ? $_POST['username'] : '';

//     if (empty($username)) {
//         echo json_encode(["error" => "Invalid or missing username"]);
//         exit;
//     }

//     $databaseService = new Database();
//     $conn = $databaseService->getConnection();

//     $response = [];

//     // $sql = "SELECT order_id, package_id, size_id, content_id, price, pickup_date, 
//     //                delivery_date, delivery_status, payment 
//     //         FROM ordertable
//     //         WHERE username = :username";
//    $sql = "SELECT 
//     o.order_id, 
//     o.package_id, 
//     o.size_id, 
//     o.content_id, 
//     o.price, 
//     o.pickup_date, 
//     o.delivery_date, 
//     o.delivery_status, 
//     o.payment,
//     c.content AS package_content  -- Join package content details
// FROM 
//     ordertable o
// LEFT JOIN 
//     package_content c ON o.content_id = c.c_id  -- Join on content_id from ordertable and c_id from package_content
// WHERE 
//     o.username = :username";

           

//     try {
//         $stmt = $conn->prepare($sql);
//         $stmt->bindParam(':username', $username, PDO::PARAM_STR);
//         $stmt->execute();
//         $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

//         if (empty($result)) {
//             $response["message"] = "No orders found for this user.";
//         } else {
//             foreach ($result as &$row) {
//                 $row['pickup_date'] = date("Y-m-d", strtotime($row['pickup_date']));
//                 $row['delivery_date'] = date("Y-m-d", strtotime($row['delivery_date']));
//             }
//             $response = $result;
//         }

//         echo json_encode($response, JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK);

//     } catch (PDOException $e) {
//         error_log("Database error: " . $e->getMessage());
//         echo json_encode(["error" => "Database error"]);
//     }
// } else {
//     http_response_code(405);
//     echo json_encode(["error" => "Invalid request method"]);
// }



ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once 'database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = isset($_POST['username']) ? $_POST['username'] : '';

    if (empty($username)) {
        echo json_encode(["error" => "Invalid or missing username"]);
        exit;
    }

    $databaseService = new Database();
    $conn = $databaseService->getConnection();

    $response = [];

    // SQL Query to fetch the order details and format the dates in the SQL query
    $sql = "SELECT 
                o.order_id, 
                o.package_id, 
                o.size_id, 
                o.content_id, 
                o.price, 
                DATE(o.pickup_date) AS pickup_date,  -- Format date in SQL
                DATE(o.delivery_date) AS delivery_date,  -- Format date in SQL
                o.delivery_status, 
                o.payment,
                c.content AS package_content  -- Join package content details
            FROM 
                ordertable o
            LEFT JOIN 
                package_content c ON o.content_id = c.c_id  -- Join on content_id from ordertable and c_id from package_content
            WHERE 
                o.username = :username";

    try {
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($result)) {
            $response["message"] = "No orders found for this user.";
        } else {
            // Loop through the results and format any additional data if needed
            foreach ($result as &$row) {
                // The pickup_date and delivery_date are already formatted in SQL, so no need for PHP date formatting
                // Optionally, you can still format the date in PHP if necessary (optional)
                // $row['pickup_date'] = date("Y-m-d", strtotime($row['pickup_date']));
                // $row['delivery_date'] = date("Y-m-d", strtotime($row['delivery_date']));
            }
            $response = $result;
        }

        echo json_encode($response, JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK);

    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        echo json_encode(["error" => "Database error"]);
    }
} else {
    http_response_code(405);
    echo json_encode(["error" => "Invalid request method"]);
}




?>