<?php
$required_role = "admin";
include "../Controller/auth.php";
include "../Controller/csrf.php";
include "../Model/db.php";
$database   = new db();
$connection = $database->connection();
$users      = $database->getAllUsers($connection);

function ubadge($s) {
    if ($s === "active")  return "ok";
    if ($s === "pending") return "wait";
    return "no"; // inactive / rejected
}
?>
<!DOCTYPE html>
<html lang="en-US">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Health Harbor | Manage Users</title>
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
        <h1>Manage Users</h1>
        <p class="lead">Activate or deactivate an account, or remove it entirely.</p>

        <table class="data">
            <tr><th>Name</th><th>Role</th><th>Email</th><th>Status</th><th>Actions</th></tr>
            <?php if ($users && $users->num_rows > 0): ?>
                <?php while ($u = $users->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($u["name"]); ?></td>
                        <td><?php echo htmlspecialchars($u["role"]); ?></td>
                        <td><?php echo htmlspecialchars($u["email"]); ?></td>
                        <td><span class="badge <?php echo ubadge($u["status"]); ?>"><?php echo htmlspecialchars($u["status"]); ?></span></td>
                        <td>
                            <form method="post" action="../Controller/AdminController.php" style="display:inline">
                                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token()); ?>">
                                <input type="hidden" name="id" value="<?php echo (int)$u["id"]; ?>">
                            <?php if ($u["status"] === "active"): ?>
                                <button class="btn small" type="submit" name="action" value="deactivate">Deactivate</button>
                            <?php else: ?>
                                <button class="btn small" type="submit" name="action" value="activate">Activate</button>
                            <?php endif; ?>
                            <button class="btn small btn-danger" type="submit" name="action" value="remove" onclick="return confirm('Remove this user permanently?')">Remove</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="5" class="empty">No users found.</td></tr>
            <?php endif; ?>
        </table>
    </div>
    <?php include "footer.php"; ?>
</body>
</html>
