<?php
$required_role = "patient";
include "../Controller/auth.php";
include "../Controller/csrf.php";
include "../Model/db.php";
$database   = new db();
$connection = $database->connection();
$appts      = $database->getPatientAppointments($connection, (int)$_SESSION["user_id"]);

function badge_class($s) {
    if ($s === "Accepted" || $s === "Confirmed") return "ok";
    if ($s === "Completed") return "done";
    if ($s === "Pending")   return "wait";
    return "no"; // Rejected / Cancelled
}
?>
<!DOCTYPE html>
<html lang="en-US">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Health Harbor | My Appointments</title>
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
        <h1>My Appointments</h1>
        <p class="lead">The status updates automatically when the doctor accepts, rejects, or completes your appointment.</p>
        <?php if (isset($_GET["booked"])): ?><p class="lead">Your appointment was booked successfully.</p><?php endif; ?>
        <?php if (isset($_GET["cancelled"])): ?><p class="lead">Your appointment was cancelled.</p><?php endif; ?>
        <?php if (isset($_GET["error"])): ?><p class="error">That action could not be completed. The appointment may no longer be cancellable.</p><?php endif; ?>

        <ul class="appt-list">
            <?php if ($appts && $appts->num_rows > 0): ?>
                <?php while ($a = $appts->fetch_assoc()): $s = $a['status']; ?>
                    <li>
                        <div class="appt-main">
                            <strong><?php echo htmlspecialchars($a['doctor_name']); ?></strong><?php echo $a['specialty'] ? " — ".htmlspecialchars($a['specialty']) : ""; ?>
                            <div class="appt-meta">📅 <?php echo htmlspecialchars($a['appt_date']); ?> &nbsp; 🕒 <?php echo htmlspecialchars($a['appt_time']); ?></div>
                        </div>
                        <div class="appt-side">
                            <span class="badge <?php echo badge_class($s); ?>"><?php echo htmlspecialchars($s); ?></span>
                            <?php if ($s === "Pending" || $s === "Accepted"): ?>
                                <form method="post" action="../Controller/BookingController.php" style="display:inline">
                                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token()); ?>">
                                    <input type="hidden" name="action" value="cancel">
                                    <input type="hidden" name="id" value="<?php echo (int)$a['id']; ?>">
                                    <button class="btn small btn-danger" type="submit">Cancel</button>
                                </form>
                            <?php elseif ($s === "Completed"): ?>
                                <a class="btn small" href="rate_doctor.php">Rate</a>
                            <?php endif; ?>
                        </div>
                    </li>
                <?php endwhile; ?>
            <?php else: ?>
                <li class="empty">You have no appointments yet. <a href="book_appointment.php">Book one now</a>.</li>
            <?php endif; ?>
        </ul>
    </div>
    <?php include "footer.php"; ?>
</body>
</html>
