<?php
$required_role = "doctor";
include "../Controller/auth.php";
include "../Controller/csrf.php";
include "../Model/db.php";
$database   = new db();
$connection = $database->connection();
$appts      = $database->getDoctorAppointments($connection, (int)$_SESSION["user_id"]);

function badge_class($s) {
    if ($s === "Accepted" || $s === "Confirmed") return "ok";
    if ($s === "Completed") return "done";
    if ($s === "Pending")   return "wait";
    return "no";
}
?>
<!DOCTYPE html>
<html lang="en-US">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Health Harbor | Doctor Appointments</title>
    <link rel="stylesheet" href="../Design/Style.css">
    <script src="../Javascript/Script.js"></script>
</head>
<body>
    <div class="header">
        <h2>Health Harbor</h2>
        <p>Thoughtful care, seamlessly booked.</p>
    </div>
    <div class="topnav">
      <a href="doctor_dashboard.php">Dashboard</a>
      <a href="doctor_appointments.php">Appointments</a>
      <a href="manage_availability.php">Availability</a>
      <a href="write_notes.php">Prescription Notes</a>
      <a href="doctor_reviews.php">Reviews</a>
      <a href="profile.php">Profile</a>
      <a href="change_password.php">Change Password</a>
      <a class="right" href="logout.php">Logout</a>
    </div>

    <div class="container">
        <h1>Appointment Requests</h1>
        <p class="lead">Accept, reject, or complete a request. Your choice instantly updates what the patient sees.</p>
        <?php if (isset($_GET["error"])): ?><p class="error">That appointment transition is no longer allowed.</p><?php endif; ?>
        <?php if (isset($_GET["updated"])): ?><p class="lead">Appointment status updated.</p><?php endif; ?>

        <ul class="appt-list">
            <?php if ($appts && $appts->num_rows > 0): ?>
                <?php while ($a = $appts->fetch_assoc()): $s = $a['status']; ?>
                    <li>
                        <div class="appt-main">
                            <strong><?php echo htmlspecialchars($a['patient_name']); ?></strong>
                            <div class="appt-meta">📅 <?php echo htmlspecialchars($a['appt_date']); ?> &nbsp; 🕒 <?php echo htmlspecialchars($a['appt_time']); ?><?php echo $a['reason'] ? " &nbsp;·&nbsp; ".htmlspecialchars($a['reason']) : ""; ?></div>
                        </div>
                        <div class="appt-side">
                            <span class="badge <?php echo badge_class($s); ?>"><?php echo htmlspecialchars($s); ?></span>
                            <?php if ($s === "Pending"): ?>
                                <form method="post" action="../Controller/AppointmentController.php" style="display:inline">
                                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token()); ?>">
                                    <input type="hidden" name="id" value="<?php echo (int)$a['id']; ?>">
                                    <button class="btn small" type="submit" name="action" value="accept">Accept</button>
                                    <button class="btn small btn-danger" type="submit" name="action" value="reject">Reject</button>
                                </form>
                            <?php elseif ($s === "Accepted"): ?>
                                <form method="post" action="../Controller/AppointmentController.php" style="display:inline">
                                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token()); ?>">
                                    <input type="hidden" name="id" value="<?php echo (int)$a['id']; ?>">
                                    <button class="btn small" type="submit" name="action" value="complete">Complete</button>
                                </form>
                                <a class="btn small" href="write_notes.php">Write Notes</a>
                            <?php endif; ?>
                        </div>
                    </li>
                <?php endwhile; ?>
            <?php else: ?>
                <li class="empty">No appointment requests yet.</li>
            <?php endif; ?>
        </ul>
    </div>
    <?php include "footer.php"; ?>
</body>
</html>
