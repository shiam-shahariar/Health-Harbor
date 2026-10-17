<?php
// Destroy the session and clear the remember-me cookie, then show a message.
session_start();
session_unset();
session_destroy();
setcookie("remember_email", "", time() - 3600, "/");
?>
<!DOCTYPE html>
<html lang="en-US">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Health Harbor | Logout</title>
    <link rel="stylesheet" href="../Design/Style.css">
</head>
<body>
    <div class="header">
        <h2>Health Harbor</h2>
        <p>Thoughtful care, seamlessly booked.</p>
    </div>

    <div class="topnav">
      <a href="index.php">Home</a>
      <a href="login.php">Login</a>
      <a href="register.php">Register</a>
    </div>

    <div class="container">
        <h1>You have been logged out</h1>
        <p class="lead">Your session has ended and the remember-me cookie was cleared.</p>
        <div class="actions">
            <a class="btn" href="login.php">Login again</a>
            <a class="btn btn-alt" href="index.php">Home</a>
        </div>
    </div>
    <?php include "footer.php"; ?>
</body>
</html>
