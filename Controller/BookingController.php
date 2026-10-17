<?php
/*
 * Controller/BookingController.php
 *  - POST  -> create a new appointment or cancel an eligible appointment
 */
include "../Model/db.php";
include "csrf.php";

// only a logged-in patient
if (empty($_SESSION["logged_in"]) || $_SESSION["role"] !== "patient") {
    header("Location: ../View/login.php");
    exit();
}

$patient_id = (int)$_SESSION["user_id"];
$database   = new db();
$connection = $database->connection();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!csrf_is_valid($_POST["csrf_token"] ?? null)) {
        header("Location: ../View/my_appointments.php?error=csrf");
        exit();
    }

    if (($_POST["action"] ?? "") === "cancel") {
        $id = (int)($_POST["id"] ?? 0);
        $cancelled = $id > 0 && $database->cancelAppointment($connection, $id, $patient_id);
        header("Location: ../View/my_appointments.php".($cancelled ? "?cancelled=1" : "?error=cancel"));
        exit();
    }

    $doctorValue = $_POST["doctor_id"] ?? "";
    $slotValue = $_POST["slot_id"] ?? "";
    $dateValue = $_POST["date"] ?? "";
    $reasonValue = $_POST["reason"] ?? "";
    $doctor_id = is_string($doctorValue) && ctype_digit($doctorValue) ? (int)$doctorValue : 0;
    $slot_id = is_string($slotValue) && ctype_digit($slotValue) ? (int)$slotValue : 0;
    $date = is_string($dateValue) ? trim($dateValue) : "";
    $reason = is_string($reasonValue) ? trim($reasonValue) : "";
    $validDate = DateTime::createFromFormat("!Y-m-d", $date);
    if ($doctor_id > 0 && $slot_id > 0 && $validDate
        && $validDate->format("Y-m-d") === $date && $date >= date("Y-m-d")) {
        $booked = $database->insertAppointment($connection, $patient_id, $doctor_id, $slot_id, $date, $reason);
        header("Location: ../View/my_appointments.php".($booked ? "?booked=1" : "?error=unavailable"));
    } else {
        header("Location: ../View/book_appointment.php?error=invalid");
    }
    exit();
}

header("Location: ../View/patient_dashboard.php");
exit();
?>
