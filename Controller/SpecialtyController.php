<?php
/*
 * Controller/SpecialtyController.php
 *  - POST                -> add or delete a specialty
 */
include "../Model/db.php";
include "csrf.php";
if (empty($_SESSION["logged_in"]) || $_SESSION["role"] !== "admin") {
    header("Location: ../View/login.php");
    exit();
}

$database   = new db();
$connection = $database->connection();

if ($_SERVER["REQUEST_METHOD"] === "POST" && csrf_is_valid($_POST["csrf_token"] ?? null)) {
    $error = false;
    if (($_POST["action"] ?? "") === "delete") {
        $id = (int)($_POST["id"] ?? 0);
        $deleted = $id > 0 && $database->deleteSpecialty($connection, $id);
        $error = !$deleted;
    } else {
        $name = trim($_POST["specname"] ?? "");
        $added = $name !== "" && $database->addSpecialty($connection, $name);
        $error = !$added;
    }
    header("Location: ../View/manage_specialties.php".($error ? "?error=action" : "?updated=1"));
    exit();
}

header("Location: ../View/manage_specialties.php?error=csrf");
exit();
?>
