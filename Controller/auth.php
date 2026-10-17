<?php
/*
 * Controller/auth.php
 * Include this at the VERY TOP of any protected View page:
 *     <?php $required_role = "patient"; include "../Controller/auth.php"; ?>
 * Leave $required_role unset to allow any logged-in user.
 * (Redirect paths are relative to the View/ folder, where the pages live.)
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// must be logged in
if (empty($_SESSION["logged_in"])) {
    header("Location: login.php");
    exit();
}

// optional role restriction
if (isset($required_role) && $_SESSION["role"] !== $required_role) {
    if ($_SESSION["role"] === "admin") {
        header("Location: admin_dashboard.php");
    } else if ($_SESSION["role"] === "doctor") {
        header("Location: doctor_dashboard.php");
    } else {
        header("Location: patient_dashboard.php");
    }
    exit();
}
?>
