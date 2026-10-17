<?php $required_role = "admin"; include "../Controller/auth.php"; ?>
<!DOCTYPE html>
<html lang="en-US">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Health Harbor | Admin Dashboard</title>
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
        <h1>Admin Dashboard</h1>
        <p class="lead">Welcome, <?php echo htmlspecialchars($_SESSION["name"]); ?>. Oversee the platform.</p>
        <div class="cards">
            <a class="card" href="verify_doctors.php"><h3>✅ Verify Doctors</h3><p>Approve or reject new doctor accounts.</p></a>
            <a class="card" href="manage_specialties.php"><h3>🩺 Specialties</h3><p>Add, edit, or delete specialties.</p></a>
            <a class="card" href="view_appointments.php"><h3>📊 Appointments &amp; Stats</h3><p>Monitor all bookings.</p></a>
            <a class="card" href="manage_users.php"><h3>👥 Users</h3><p>Manage patient and doctor accounts.</p></a>
        </div>
    </div>
    <?php include "footer.php"; ?>
</body>
</html>
