<?php
/*
 * Controller/PasswordController.php
 * Included at the top of View/change_password.php.
 * Verifies the current password, then updates to the new one.
 */
include "../Model/db.php";
include "csrf.php";
if (empty($_SESSION["logged_in"])) {
    header("Location: ../View/login.php");
    exit();
}

$uid     = (int)$_SESSION["user_id"];
$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $currentValue = $_POST["current"] ?? "";
    $newpassValue = $_POST["newpass"] ?? "";
    $confirmValue = $_POST["confirmpass"] ?? "";
    $current = is_string($currentValue) ? $currentValue : "";
    $newpass = is_string($newpassValue) ? $newpassValue : "";
    $confirm = is_string($confirmValue) ? $confirmValue : "";

    $database   = new db();
    $connection = $database->connection();
    $user       = $database->getUserById($connection, $uid)->fetch_assoc();

    if (!csrf_is_valid($_POST["csrf_token"] ?? null)) {
        $message = "Your request could not be verified. Please reload and try again.";
    } else if (!$user || !password_verify($current, $user["password"])) {
        $message = "Your current password is incorrect.";
    } else if (strlen($newpass) < 5) {
        $message = "New password must be at least 5 characters.";
    } else if ($newpass !== $confirm) {
        $message = "New passwords do not match.";
    } else {
        $database->updatePassword($connection, $uid, password_hash($newpass, PASSWORD_DEFAULT));
        $message = "Password updated successfully.";
    }
}
?>
