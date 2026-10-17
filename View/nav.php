<?php $navRole = $_SESSION["role"] ?? ""; ?>
<div class="topnav">
<?php if ($navRole === "patient"): ?>
      <a href="patient_dashboard.php">Dashboard</a>
      <a href="search_doctors.php">Search Doctors</a>
      <a href="book_appointment.php">Book Appointment</a>
      <a href="my_appointments.php">My Appointments</a>
      <a href="rate_doctor.php">Rate Doctor</a>
<?php elseif ($navRole === "doctor"): ?>
      <a href="doctor_dashboard.php">Dashboard</a>
      <a href="doctor_appointments.php">Appointments</a>
      <a href="manage_availability.php">Availability</a>
      <a href="write_notes.php">Prescription Notes</a>
      <a href="doctor_reviews.php">Reviews</a>
<?php elseif ($navRole === "admin"): ?>
      <a href="admin_dashboard.php">Dashboard</a>
      <a href="verify_doctors.php">Verify Doctors</a>
      <a href="manage_specialties.php">Specialties</a>
      <a href="view_appointments.php">Appointments &amp; Stats</a>
      <a href="manage_users.php">Users</a>
<?php endif; ?>
      <a href="profile.php">Profile</a>
      <a href="change_password.php">Change Password</a>
      <a class="right" href="logout.php">Logout</a>
</div>
