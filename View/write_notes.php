<?php
$required_role = "doctor";
include "../Controller/auth.php";
include "../Controller/csrf.php";
include "../Model/db.php";
$database   = new db();
$connection = $database->connection();
$appts      = $database->getDoctorNotableAppointments($connection, (int)$_SESSION["user_id"]);
?>
<!DOCTYPE html>
<html lang="en-US">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Health Harbor | Prescription Notes</title>
    <link rel="stylesheet" href="../Design/Style.css">
    <script src="../Javascript/Script.js"></script>
</head>
<body>
    <div class="header">
        <h2>Health Harbor</h2>
        <p>Thoughtful care, seamlessly booked.</p>
    </div>
    <?php include "nav.php"; ?>

    <div class="container">
        <h1>Write Prescription Notes</h1>

        <?php if (isset($_GET["saved"])): ?>
            <p class="lead" style="color:#2b8a3e;">Notes saved. The appointment is now marked Completed.</p>
        <?php endif; ?>
        <?php if (isset($_GET["error"])): ?><p class="error">Notes could not be saved for that appointment.</p><?php endif; ?>

        <form method="post" action="../Controller/NotesController.php" onsubmit="return validateNotes()">
            <fieldset>
                <legend>Consultation Notes</legend>
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token()); ?>">
                <table>
                    <tr>
                        <td><label for="appt">Appointment:</label></td>
                        <td>
                            <select id="appt" name="appt">
                                <option value="">-- Select appointment --</option>
                                <?php if ($appts) while ($a = $appts->fetch_assoc()): ?>
                                    <option value="<?php echo $a['id']; ?>">
                                        <?php echo htmlspecialchars($a['patient_name']); ?> — <?php echo htmlspecialchars($a['appt_date']); ?> (<?php echo htmlspecialchars($a['status']); ?>)
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </td>
                        <td><p class="error" id="apptError"></p></td>
                    </tr>
                    <tr>
                        <td><label for="notes">Notes / Prescription:</label></td>
                        <td><textarea id="notes" name="notes" rows="6" cols="30" style="resize:none"></textarea></td>
                        <td><p class="error" id="notesError"></p></td>
                    </tr>
                    <tr>
                        <td colspan="3"><input class="btn" type="submit" value="Save Notes"></td>
                    </tr>
                </table>
            </fieldset>
        </form>
    </div>
    <?php include "footer.php"; ?>
</body>
</html>
