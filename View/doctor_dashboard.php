<?php $required_role = "doctor"; include "../Controller/auth.php"; ?>
<!DOCTYPE html>
<html lang="en-US">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Health Harbor | Doctor Dashboard</title>
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
        <h1>Doctor Dashboard</h1>
        <p class="lead">Welcome, <?php echo htmlspecialchars($_SESSION["name"]); ?>. Manage your schedule and patient appointments.</p>
        <div class="cards">
            <a class="card" href="doctor_appointments.php"><h3>📋 Appointments</h3><p>Accept, reject, or complete requests.</p></a>
            <a class="card" href="manage_availability.php"><h3>🕒 Availability</h3><p>Set and manage your time slots.</p></a>
            <a class="card" href="write_notes.php"><h3>📝 Prescription Notes</h3><p>Write notes for completed visits.</p></a>
        </div>
    </div>
    <?php include "footer.php"; ?>
</body>
</html>
