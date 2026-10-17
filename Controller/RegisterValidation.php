<?php
/*
 * Controller/RegisterValidation.php
 * Included at the top of View/register.php (class pattern).
 * Validates the form, hashes the password, inserts the user,
 * then redirects to the login page.
 */
include "../Model/db.php";
include "csrf.php";

$message = "";
$database = new db();
$connection = $database->connection();
$specialties = $database->getSpecialties($connection);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $postText = function ($key) {
        $value = $_POST[$key] ?? "";
        return is_string($value) ? trim($value) : "";
    };
    $name      = $postText("name");
    $email     = $postText("email");
    $phone     = $postText("phone");
    $role      = $postText("role");
    $specialty = $postText("specialty");
    $passwordValue = $_POST["password"] ?? "";
    $confirmValue = $_POST["confirm"] ?? "";
    $password = is_string($passwordValue) ? $passwordValue : "";
    $confirm  = is_string($confirmValue) ? $confirmValue : "";

    // ---- server-side (PHP) validation ----
    $valid = true;
    if (strlen($name) < 5)                { $message .= "Name must be at least 5 characters. ";  $valid = false; }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) { $message .= "Enter a valid email. "; $valid = false; }
    if (!preg_match("/^[0-9]{11}$/D", $phone)) { $message .= "Phone must be exactly 11 digits. "; $valid = false; }
    if ($role != "patient" && $role != "doctor") { $message .= "Please choose a role. ";        $valid = false; }
    if (strlen($password) < 5)            { $message .= "Password must be at least 5 characters. "; $valid = false; }
    if ($password !== $confirm)           { $message .= "Passwords do not match. ";             $valid = false; }
    if (($_POST["terms"] ?? "") !== "1") { $message .= "You must accept the Terms & Conditions. "; $valid = false; }
    if (!csrf_is_valid($_POST["csrf_token"] ?? null)) { $message .= "Your request could not be verified. "; $valid = false; }
    if ($role === "doctor") {
        $specialtyResult = $database->getSpecialtyByName($connection, $specialty);
        if ($specialty === "" || !$specialtyResult || $specialtyResult->num_rows !== 1) {
            $message .= "Choose a specialty from the managed list. ";
            $valid = false;
        }
    }

    if ($valid) {
        // stop duplicate emails
        $check = $database->getUserByEmail($connection, $email);
        if ($check && $check->num_rows > 0) {
            $message = "This email is already registered.";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT); // Basic Web Security

            // doctors need admin approval, so they start as 'pending'
            if ($role == "doctor") {
                $status        = "pending";
                $specialtyToDb = $specialty;
            } else {
                $status        = "active";
                $specialtyToDb = null;
            }

            $result = $database->signup($connection, $name, $email, $hash, $phone, $role, $specialtyToDb, $status);
            if ($result) {
                header("Location: ../View/login.php?registered=1");
                exit();
            } else {
                $message = "Something went wrong. Please try again.";
            }
        }
    }
}
?>
