<?php
$required_role = "patient";
include "../Controller/auth.php";
include "../Model/db.php";
$database   = new db();
$connection = $database->connection();
$doctors    = $database->getActiveDoctors($connection);
$doctorValue = $_GET["doctor_id"] ?? "";
$dateValue = $_GET["date"] ?? "";
$preselect = is_string($doctorValue) && ctype_digit($doctorValue) ? (int)$doctorValue : 0;
$date = is_string($dateValue) ? trim($dateValue) : "";
$validDate  = DateTime::createFromFormat("!Y-m-d", $date);
$slots      = false;
if ($preselect > 0 && $validDate && $validDate->format("Y-m-d") === $date && $date >= date("Y-m-d")) {
    $slots = $database->getAvailableSlots($connection, $preselect, $date);
}
include "../Controller/csrf.php";
?>
<!DOCTYPE html>
<html lang="en-US">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Health Harbor | Book Appointment</title>
    <link rel="stylesheet" href="../Design/Style.css">
    <script src="../Javascript/Script.js"></script>
</head>
<body>
    <div class="header">
        <h2>Health Harbor</h2>
        <p>Thoughtful care, seamlessly booked.</p>
    </div>
    <div class="topnav">
      <a href="patient_dashboard.php">Dashboard</a>
      <a href="search_doctors.php">Search Doctors</a>
      <a href="book_appointment.php">Book Appointment</a>
      <a href="my_appointments.php">My Appointments</a>
      <a href="rate_doctor.php">Rate Doctor</a>
      <a href="profile.php">Profile</a>
      <a href="change_password.php">Change Password</a>
      <a class="right" href="logout.php">Logout</a>
    </div>

    <div class="container">
        <h1>Book Appointment</h1>
        <form method="get" action="book_appointment.php">
            <fieldset>
                <legend>Find Available Times</legend>
                <table>
                    <tr>
                        <td><label for="doctor_id">Doctor:</label></td>
                        <td>
                            <select id="doctor_id" name="doctor_id">
                                <option value="">-- Select a doctor --</option>
                                <?php if ($doctors) while ($d = $doctors->fetch_assoc()): ?>
                                    <option value="<?php echo $d['id']; ?>" <?php echo ($preselect == $d['id']) ? "selected" : ""; ?>>
                                        <?php echo htmlspecialchars($d['name']); ?><?php echo $d['specialty'] ? " — ".htmlspecialchars($d['specialty']) : ""; ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </td>
                        <td><p class="error" id="doctorError"></p></td>
                    </tr>
                    <tr>
                        <td><label for="date">Date:</label></td>
                        <td><input type="date" id="date" name="date" min="<?php echo date("Y-m-d"); ?>" value="<?php echo htmlspecialchars($date); ?>"></td>
                        <td><p class="error" id="dateError"></p></td>
                    </tr>
                    <tr><td colspan="3"><input class="btn" type="submit" value="Show Available Slots"></td></tr>
                </table>
            </fieldset>
        </form>
        <?php if (isset($_GET["error"])): ?>
            <p class="error">The booking details were invalid or the selected slot is no longer available.</p>
        <?php endif; ?>
        <?php if ($slots): ?>
            <?php if ($slots->num_rows > 0): ?>
                <form method="post" action="../Controller/BookingController.php" onsubmit="return validateBooking()">
                    <fieldset>
                        <legend>Appointment Details</legend>
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token()); ?>">
                        <input type="hidden" name="doctor_id" value="<?php echo $preselect; ?>">
                        <input type="hidden" name="date" value="<?php echo htmlspecialchars($date); ?>">
                        <table>
                            <tr>
                                <td><label for="slot">Available Slot:</label></td>
                                <td>
                                    <select id="slot" name="slot_id">
                                        <option value="">-- Select a slot --</option>
                                        <?php while ($slot = $slots->fetch_assoc()): ?>
                                            <option value="<?php echo (int)$slot["id"]; ?>">
                                                <?php echo htmlspecialchars(substr($slot["start_time"], 0, 5)." - ".substr($slot["end_time"], 0, 5)); ?>
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                </td>
                                <td><p class="error" id="slotError"></p></td>
                            </tr>
                            <tr>
                                <td><label for="reason">Reason:</label></td>
                                <td><textarea id="reason" name="reason" rows="3" cols="24" style="resize:none"></textarea></td>
                                <td></td>
                            </tr>
                            <tr><td colspan="3"><input class="btn" type="submit" value="Confirm Booking"></td></tr>
                        </table>
                    </fieldset>
                </form>
            <?php else: ?>
                <p class="empty">No available slots for this doctor and date.</p>
            <?php endif; ?>
        <?php endif; ?>
    </div>
    <?php include "footer.php"; ?>
</body>
</html>
