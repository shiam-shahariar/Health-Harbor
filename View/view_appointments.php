<?php
$required_role = "admin";
include "../Controller/auth.php";
include "../Model/db.php";
$database   = new db();
$connection = $database->connection();
$stats      = $database->getStats($connection);
$appts      = $database->getAllAppointments($connection);

function abadge($s) {
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
    <title>Health Harbor | Appointments &amp; Stats</title>
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
        <h1>All Appointments &amp; Statistics</h1>

        <div class="stats">
            <div class="stat"><span class="num"><?php echo (int)$stats["appointments"]; ?></span><span class="label">Total Appointments</span></div>
            <div class="stat"><span class="num"><?php echo (int)$stats["doctors"]; ?></span><span class="label">Doctors</span></div>
            <div class="stat"><span class="num"><?php echo (int)$stats["patients"]; ?></span><span class="label">Patients</span></div>
            <div class="stat"><span class="num"><?php echo (int)$stats["specialties"]; ?></span><span class="label">Specialties</span></div>
        </div>

        <h3>All Appointments</h3>
        <table class="data">
            <tr><th>Patient</th><th>Doctor</th><th>Specialty</th><th>Date</th><th>Time</th><th>Status</th></tr>
            <?php if ($appts && $appts->num_rows > 0): ?>
                <?php while ($a = $appts->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($a["patient_name"]); ?></td>
                        <td><?php echo htmlspecialchars($a["doctor_name"]); ?></td>
                        <td><?php echo htmlspecialchars($a["specialty"] ?? ""); ?></td>
                        <td><?php echo htmlspecialchars($a["appt_date"]); ?></td>
                        <td><?php echo htmlspecialchars($a["appt_time"]); ?></td>
                        <td><span class="badge <?php echo abadge($a["status"]); ?>"><?php echo htmlspecialchars($a["status"]); ?></span></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="6" class="empty">No appointments yet.</td></tr>
            <?php endif; ?>
        </table>
    </div>
    <?php include "footer.php"; ?>
</body>
</html>
