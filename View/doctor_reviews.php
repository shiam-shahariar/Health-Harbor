<?php
$required_role = "doctor";
include "../Controller/auth.php";
include "../Model/db.php";
$database   = new db();
$connection = $database->connection();
$reviews    = $database->getDoctorReviews($connection, (int)$_SESSION["user_id"]);

// compute average for the header
$all = array(); $sum = 0;
if ($reviews) { while ($row = $reviews->fetch_assoc()) { $all[] = $row; $sum += (int)$row["rating"]; } }
$count = count($all);
$avg   = $count > 0 ? round($sum / $count, 1) : 0;
?>
<!DOCTYPE html>
<html lang="en-US">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Health Harbor | My Reviews</title>
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
        <h1>My Reviews</h1>
        <?php if ($count > 0): ?>
            <p class="lead">Average rating: <strong><?php echo $avg; ?> ⭐</strong> from <?php echo $count; ?> review<?php echo $count==1?"":"s"; ?>.</p>
            <ul class="appt-list">
                <?php foreach ($all as $r): ?>
                    <li>
                        <div class="appt-main">
                            <strong><?php echo str_repeat("⭐", (int)$r["rating"]); ?></strong> (<?php echo (int)$r["rating"]; ?>/5)
                            <div class="appt-meta"><?php echo htmlspecialchars($r["review"]); ?></div>
                            <div class="appt-meta">— <?php echo htmlspecialchars($r["patient_name"]); ?>, <?php echo htmlspecialchars($r["created_at"]); ?></div>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p class="empty">You have no reviews yet.</p>
        <?php endif; ?>
    </div>
    <?php include "footer.php"; ?>
</body>
</html>
