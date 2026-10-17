<?php $required_role = "patient"; include "../Controller/auth.php"; ?>
<!DOCTYPE html>
<html lang="en-US">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Health Harbor | Patient Dashboard</title>
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
        <h1>Patient Dashboard</h1>
        <p class="lead">Welcome back, <?php echo htmlspecialchars($_SESSION["name"]); ?>! What would you like to do today?</p>
        <div class="cards">
            <a class="card" href="search_doctors.php"><h3>🔎 Search Doctors</h3><p>Find a doctor by specialty.</p></a>
            <a class="card" href="book_appointment.php"><h3>📅 Book Appointment</h3><p>Reserve an available time slot.</p></a>
            <a class="card" href="my_appointments.php"><h3>🗂️ My Appointments</h3><p>View or cancel your bookings.</p></a>
            <a class="card" href="rate_doctor.php"><h3>⭐ Rate a Doctor</h3><p>Leave a review after a visit.</p></a>
        </div>
    </div>
    <?php include "footer.php"; ?>
</body>
</html>
