<?php
/*
 * Controller/NotesController.php
 * Doctor saves prescription/consultation notes for one of their
 * appointments (this also marks the appointment Completed).
 */
include "../Model/db.php";
include "csrf.php";
if (empty($_SESSION["logged_in"]) || $_SESSION["role"] !== "doctor") {
    header("Location: ../View/login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && csrf_is_valid($_POST["csrf_token"] ?? null)) {
    $doctor_id = (int)$_SESSION["user_id"];
    $appt      = (int)($_POST["appt"] ?? 0);
    $notes     = trim($_POST["notes"] ?? "");
    if ($appt > 0 && $notes !== "") {
        $database   = new db();
        $connection = $database->connection();
        $saved = $database->saveNotes($connection, $appt, $doctor_id, $notes);
        header("Location: ../View/write_notes.php".($saved ? "?saved=1" : "?error=transition"));
        exit();
    }
}
header("Location: ../View/write_notes.php?error=invalid");
exit();
?>
