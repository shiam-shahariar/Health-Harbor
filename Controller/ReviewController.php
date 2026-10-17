<?php
/*
 * Controller/ReviewController.php
 * Patient submits a rating + review for a doctor they visited.
 */
include "../Model/db.php";
include "csrf.php";
if (empty($_SESSION["logged_in"]) || $_SESSION["role"] !== "patient") {
    header("Location: ../View/login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $patient_id = (int)$_SESSION["user_id"];
    $database   = new db();
    $connection = $database->connection();
    $doctorValue = $_POST["rdoctor"] ?? "";
    $doctor_id = is_string($doctorValue) && ctype_digit($doctorValue) ? (int)$doctorValue : 0;
    $ratingValue = $_POST["rating"] ?? "";
    $rating = is_string($ratingValue) ? (int)$ratingValue : 0;
    $reviewValue = $_POST["review"] ?? "";
    $review = is_string($reviewValue) ? trim($reviewValue) : "";
    if (csrf_is_valid($_POST["csrf_token"] ?? null) && $doctor_id > 0
        && is_string($ratingValue) && preg_match("/^[1-5]$/D", $ratingValue)
        && $database->hasCompletedAppointment($connection, $patient_id, $doctor_id)) {
        $saved = $database->insertReview($connection, $patient_id, $doctor_id, $rating, $review);
        header("Location: ../View/rate_doctor.php".($saved ? "?saved=1" : "?error=save"));
    } else {
        header("Location: ../View/rate_doctor.php?error=invalid");
    }
    exit();
}
header("Location: ../View/rate_doctor.php?error=invalid");
exit();
?>
