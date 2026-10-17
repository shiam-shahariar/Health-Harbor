<?php
/*
 * Controller/AppointmentController.php
 * Doctor changes an appointment's status using a CSRF-protected POST.
 */
include "../Model/db.php";
include "csrf.php";

// only a logged-in doctor
if (empty($_SESSION["logged_in"]) || $_SESSION["role"] !== "doctor") {
    header("Location: ../View/login.php");
    exit();
}

$doctor_id = (int)$_SESSION["user_id"];
$action    = $_POST["action"] ?? "";
$id        = (int)($_POST["id"] ?? 0);

$map = array("accept" => "Accepted", "reject" => "Rejected", "complete" => "Completed");

if ($_SERVER["REQUEST_METHOD"] === "POST" && csrf_is_valid($_POST["csrf_token"] ?? null)
    && $id > 0 && isset($map[$action])) {
    $database   = new db();
    $connection = $database->connection();
    $updated = $database->setAppointmentStatus($connection, $id, $doctor_id, $map[$action]);
    header("Location: ../View/doctor_appointments.php".($updated ? "?updated=1" : "?error=transition"));
    exit();
}

header("Location: ../View/doctor_appointments.php?error=csrf");
exit();
?>
