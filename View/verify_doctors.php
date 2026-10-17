<?php
include "../Model/db.php";
include "../Controller/csrf.php";

// only an admin can open this page
if (!isset($_SESSION["role"]) || $_SESSION["role"] != "admin") {
    header("Location: login.php");
    exit();
}

$database   = new db();
$connection = $database->connection();
$pending    = $database->getPendingDoctors($connection);
?>
<!DOCTYPE html>
<html lang="en-US">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Health Harbor | Verify Doctors</title>
    <link rel="stylesheet" href="../Design/Style.css">
    <script src="../Javascript/Script.js"></script>
</head>
<body>
    <div class="header">
        <h2>Health Harbor</h2>
        <p>Thoughtful care, seamlessly booked.</p>
    </div>

    <div class="topnav">
      <a href="admin_dashboard.php">Dashboard</a>
      <a href="verify_doctors.php">Verify Doctors</a>
      <a href="manage_specialties.php">Specialties</a>
      <a href="view_appointments.php">Appointments &amp; Stats</a>
      <a href="manage_users.php">Users</a>
      <a href="profile.php">Profile</a>
      <a href="change_password.php">Change Password</a>
      <a class="right" href="logout.php">Logout</a>
    </div>

    <div class="container">
        <h1>Verify / Approve Doctors</h1>
        <p class="lead">Approve a doctor to let them log in, or reject to block the account.</p>

        <table class="data">
            <tr><th>Name</th><th>Specialty</th><th>Email</th><th>Status</th><th>Actions</th></tr>
            <?php if ($pending && $pending->num_rows > 0): ?>
                <?php while ($row = $pending->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row["name"]); ?></td>
                        <td><?php echo htmlspecialchars($row["specialty"] ?? ""); ?></td>
                        <td><?php echo htmlspecialchars($row["email"]); ?></td>
                        <td><span class="badge wait">Pending</span></td>
                        <td>
                            <form method="post" action="../Controller/AdminController.php" style="display:inline">
                                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token()); ?>">
                                <input type="hidden" name="id" value="<?php echo (int)$row["id"]; ?>">
                                <button class="btn small" type="submit" name="action" value="approve">Approve</button>
                                <button class="btn small btn-danger" type="submit" name="action" value="reject">Reject</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="5" class="empty">No doctors are waiting for approval.</td></tr>
            <?php endif; ?>
        </table>
    </div>
    <?php include "footer.php"; ?>
</body>
</html>
