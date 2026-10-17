<?php
/*
 * Controller/AvailabilityController.php
 *  - POST                -> add or delete a time slot
 */
include "../Model/db.php";
include "csrf.php";
if (empty($_SESSION["logged_in"]) || $_SESSION["role"] !== "doctor") {
    header("Location: ../View/login.php");
    exit();
}

$doctor_id  = (int)$_SESSION["user_id"];
$database   = new db();
$connection = $database->connection();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!csrf_is_valid($_POST["csrf_token"] ?? null)) {
        header("Location: ../View/manage_availability.php?error=csrf");
        exit();
    }
    if (($_POST["action"] ?? "") === "delete") {
        $idValue = $_POST["id"] ?? "";
        $id = is_string($idValue) && ctype_digit($idValue) ? (int)$idValue : 0;
        try {
            $deleted = $id > 0 && $database->deleteSlot($connection, $id, $doctor_id);
            header("Location: ../View/manage_availability.php".($deleted ? "?deleted=1" : "?error=delete"));
        } catch (mysqli_sql_exception $e) {
            error_log($e->getMessage());
            header("Location: ../View/manage_availability.php?error=booked");
        }
        exit();
    }

    $dateValue = $_POST["adate"] ?? "";
    $startValue = $_POST["start"] ?? "";
    $endValue = $_POST["end"] ?? "";
    $date = is_string($dateValue) ? trim($dateValue) : "";
    $start = is_string($startValue) ? trim($startValue) : "";
    $end = is_string($endValue) ? trim($endValue) : "";
    $validDate = DateTime::createFromFormat("!Y-m-d", $date);
    $validTime = preg_match("/^(?:[01][0-9]|2[0-3]):[0-5][0-9]$/D", $start)
        && preg_match("/^(?:[01][0-9]|2[0-3]):[0-5][0-9]$/D", $end);
    if ($validDate && $validDate->format("Y-m-d") === $date && $date >= date("Y-m-d")
        && $validTime && $end > $start) {
        try {
            $added = $database->addSlot($connection, $doctor_id, $date, $start, $end);
            header("Location: ../View/manage_availability.php".($added ? "?added=1" : "?error=duplicate"));
        } catch (mysqli_sql_exception $e) {
            error_log($e->getMessage());
            header("Location: ../View/manage_availability.php?error=duplicate");
        }
    } else {
        header("Location: ../View/manage_availability.php?error=invalid");
    }
    exit();
}

header("Location: ../View/manage_availability.php");
exit();
?>
