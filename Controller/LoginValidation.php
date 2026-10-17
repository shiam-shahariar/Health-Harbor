<?php
/*
 * Controller/LoginValidation.php
 * Included at the top of View/login.php (class pattern).
 * Checks the email + password, creates the session, sets the
 * remember-me cookie, and redirects to the correct dashboard.
 */
include "../Model/db.php";
session_start();

$message = "";
$email   = "";

// pre-fill the email box if a remember-me cookie exists
if (isset($_COOKIE["remember_email"])) {
    $email = $_COOKIE["remember_email"];
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email    = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";
    $remember = isset($_POST["remember"]) && $_POST["remember"] == "1";

    // ---- server-side (PHP) validation ----
    $valid = true;
    if (strpos($email, "@") === false) { $message .= "Enter a valid email. ";                 $valid = false; }
    if (strlen($password) < 5)         { $message .= "Password must be at least 5 characters. "; $valid = false; }

    if ($valid) {
        $database   = new db();
        $connection = $database->connection();
        $result     = $database->getUserByEmail($connection, $email);

        if ($result && $result->num_rows == 1) {
            $user = $result->fetch_assoc();

            if (password_verify($password, $user["password"])) {
                // doctors must be approved by the admin first
                if ($user["role"] !== "admin" && $user["status"] !== "active") {
                    $message = $user["role"] === "doctor"
                        ? "Your doctor account is waiting for admin approval."
                        : "This account is inactive. Contact an administrator.";
                } else {
                    // ---- create the session ----
                    $_SESSION["logged_in"] = true;
                    $_SESSION["user_id"]   = $user["id"];
                    $_SESSION["name"]      = $user["name"];
                    $_SESSION["role"]      = $user["role"];

                    // ---- remember-me cookie ----
                    if ($remember) {
                        setcookie("remember_email", $email, time() + 60*60*24*7, "/");
                    } else {
                        setcookie("remember_email", "", time() - 3600, "/");
                    }

                    // ---- redirect by role ----
                    if ($user["role"] == "admin") {
                        header("Location: ../View/admin_dashboard.php");
                    } else if ($user["role"] == "doctor") {
                        header("Location: ../View/doctor_dashboard.php");
                    } else {
                        header("Location: ../View/patient_dashboard.php");
                    }
                    exit();
                }
            } else {
                $message = "Incorrect email or password.";
            }
        } else {
            $message = "Incorrect email or password.";
        }
    }
}
?>
