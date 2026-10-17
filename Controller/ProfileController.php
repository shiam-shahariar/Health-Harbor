<?php
/*
 * Controller/ProfileController.php
 * Included at the top of View/profile.php.
 *  - POST                -> update the profile or delete the account
 * Provides $user (current data) and $message to the view.
 */
include "../Model/db.php";
include "csrf.php";
if (empty($_SESSION["logged_in"])) {
    header("Location: ../View/login.php");
    exit();
}

$uid        = (int)$_SESSION["user_id"];
$database   = new db();
$connection = $database->connection();
$message    = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!csrf_is_valid($_POST["csrf_token"] ?? null)) {
        $message = "Your request could not be verified. Please reload and try again.";
    } else if (($_POST["action"] ?? "") === "delete") {
        if ($database->deleteUser($connection, $uid)) {
            session_unset();
            session_destroy();
            setcookie("remember_email", "", time() - 3600, "/");
            header("Location: ../View/register.php");
            exit();
        }
        $message = "The account could not be deleted.";
    } else {
        $name      = trim($_POST["pname"] ?? "");
        $phone     = trim($_POST["pphone"] ?? "");
        $specialty = trim($_POST["pspecialty"] ?? "");
        $isDoctor  = $_SESSION["role"] === "doctor";
        $specialtyResult = $isDoctor && $specialty !== ""
            ? $database->getSpecialtyByName($connection, $specialty) : false;
        $validSpecialty = !$isDoctor || ($specialtyResult && $specialtyResult->num_rows > 0);
        if (strlen($name) < 5) {
            $message = "Name must be at least 5 characters.";
        } else if (!$validSpecialty) {
            $message = "Choose a specialty from the managed list.";
        } else {
            $database->updateProfile($connection, $uid, $name, $phone, $isDoctor ? $specialty : null);
            $_SESSION["name"] = $name;
            $message = "Profile updated successfully.";
        }
    }
}

// load current data for the form
$user = $database->getUserById($connection, $uid)->fetch_assoc();
$specialties = $_SESSION["role"] === "doctor" ? $database->getSpecialties($connection) : false;
?>
