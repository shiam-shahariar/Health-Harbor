<?php
$required_role = "admin";
include "../Controller/auth.php";
include "../Controller/csrf.php";
include "../Model/db.php";
$database   = new db();
$connection = $database->connection();
$specs      = $database->getSpecialties($connection);
?>
<!DOCTYPE html>
<html lang="en-US">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Health Harbor | Manage Specialties</title>
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
        <h1>Manage Specialties</h1>
        <?php if (isset($_GET["error"])): ?><p class="error">That action could not be completed. A specialty in use cannot be removed.</p><?php endif; ?>

        <form method="post" action="../Controller/SpecialtyController.php" onsubmit="return validateSpecialty()">
            <fieldset>
                <legend>Add Specialty</legend>
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token()); ?>">
                <table>
                    <tr>
                        <td><label for="specname">Specialty Name:</label></td>
                        <td><input type="text" id="specname" name="specname"></td>
                        <td><input class="btn" type="submit" value="Add"></td>
                    </tr>
                    <tr><td colspan="3"><p class="error" id="specnameError"></p></td></tr>
                </table>
            </fieldset>
        </form>

        <h3>Existing Specialties</h3>
        <table class="data">
            <tr><th>Specialty</th><th>Action</th></tr>
            <?php if ($specs && $specs->num_rows > 0): ?>
                <?php while ($s = $specs->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($s['name']); ?></td>
                        <td>
                            <form method="post" action="../Controller/SpecialtyController.php" style="display:inline" onsubmit="return confirm('Delete this specialty?')">
                                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token()); ?>">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?php echo (int)$s['id']; ?>">
                                <button class="btn small btn-danger" type="submit">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="2" class="empty">No specialties yet.</td></tr>
            <?php endif; ?>
        </table>
    </div>
    <?php include "footer.php"; ?>
</body>
</html>
