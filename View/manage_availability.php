<?php
$required_role = "doctor";
include "../Controller/auth.php";
include "../Controller/csrf.php";
include "../Model/db.php";
$database   = new db();
$connection = $database->connection();
$slots      = $database->getDoctorSlots($connection, (int)$_SESSION["user_id"]);
?>
<!DOCTYPE html>
<html lang="en-US">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Health Harbor | Manage Availability</title>
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
        <h1>Manage Availability / Slots</h1>
        <?php if (isset($_GET["error"])): ?>
            <p class="error"><?php echo $_GET["error"] === "booked" ? "A booked slot cannot be deleted." : "The availability action was invalid or could not be completed."; ?></p>
        <?php endif; ?>
        <?php if (isset($_GET["added"])): ?><p class="lead">Availability added.</p><?php endif; ?>
        <?php if (isset($_GET["deleted"])): ?><p class="lead">Availability deleted.</p><?php endif; ?>

        <form method="post" action="../Controller/AvailabilityController.php" onsubmit="return validateAvailability()">
            <fieldset>
                <legend>Add a Time Slot</legend>
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token()); ?>">
                <table>
                    <tr>
                        <td><label for="adate">Date:</label></td>
                        <td><input type="date" id="adate" name="adate"></td>
                        <td><p class="error" id="adateError"></p></td>
                    </tr>
                    <tr>
                        <td><label for="start">Start Time:</label></td>
                        <td><input type="time" id="start" name="start"></td>
                        <td><p class="error" id="startError"></p></td>
                    </tr>
                    <tr>
                        <td><label for="end">End Time:</label></td>
                        <td><input type="time" id="end" name="end"></td>
                        <td><p class="error" id="endError"></p></td>
                    </tr>
                    <tr>
                        <td colspan="3"><input class="btn" type="submit" value="Add Slot"></td>
                    </tr>
                </table>
            </fieldset>
        </form>

        <h3>My Current Slots</h3>
        <table class="data">
            <tr><th>Date</th><th>Start</th><th>End</th><th>Booking</th><th>Action</th></tr>
            <?php if ($slots && $slots->num_rows > 0): ?>
                <?php while ($s = $slots->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($s['slot_date']); ?></td>
                        <td><?php echo htmlspecialchars(substr($s['start_time'], 0, 5)); ?></td>
                        <td><?php echo htmlspecialchars(substr($s['end_time'], 0, 5)); ?></td>
                        <td><?php echo $s["is_booked"] ? "Booked" : "Available"; ?></td>
                        <td>
                            <?php if (!$s["is_booked"]): ?>
                                <form method="post" action="../Controller/AvailabilityController.php" style="display:inline" onsubmit="return confirm('Delete this slot?')">
                                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token()); ?>">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?php echo (int)$s['id']; ?>">
                                    <button class="btn small btn-danger" type="submit">Delete</button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="5" class="empty">No slots added yet.</td></tr>
            <?php endif; ?>
        </table>
    </div>
    <?php include "footer.php"; ?>
</body>
</html>
