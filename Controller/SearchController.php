<?php
/*
 * Controller/SearchController.php
 * AJAX endpoint. Receives a search term (POST "q") and returns a
 * JSON array of matching approved doctors.
 */
include "../Model/db.php";
if (session_status() === PHP_SESSION_NONE) session_start();
header("Content-Type: application/json");

// Only logged-in users may search; failures always use a JSON response.
if (empty($_SESSION["logged_in"])) {
    http_response_code(401);
    echo json_encode(array("error" => "Please sign in to search for doctors."));
    exit();
}

try {
    $q = $_POST["q"] ?? "";
    if (!is_string($q)) {
        http_response_code(400);
        echo json_encode(array("error" => "Invalid search query."));
        exit();
    }
    $database   = new db();
    $connection = $database->connection();
    $result     = $database->searchDoctors($connection, $q);
    if (!$result) {
        error_log("Doctor search query failed: ".$connection->error);
        http_response_code(500);
        echo json_encode(array("error" => "Doctor search is temporarily unavailable."));
        exit();
    }
    $doctors = array();
    while ($row = $result->fetch_assoc()) {
        $doctors[] = $row;
    }
    echo json_encode($doctors);
} catch (mysqli_sql_exception $e) {
    error_log($e->getMessage());
    http_response_code(500);
    echo json_encode(array("error" => "Doctor search is temporarily unavailable."));
} catch (RuntimeException $e) {
    error_log($e->getMessage());
    http_response_code(500);
    echo json_encode(array("error" => "Doctor search is temporarily unavailable."));
}
?>
