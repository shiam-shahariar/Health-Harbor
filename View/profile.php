<?php
include "../Controller/auth.php";
include "../Controller/ProfileController.php";
?>
<!DOCTYPE html>
<html lang="en-US">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Health Harbor | Profile</title>
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
        <h1>Manage Profile</h1>

        <?php if (!empty($message)): ?>
            <p class="lead" style="color:#2b8a3e;"><?php echo htmlspecialchars($message); ?></p>
        <?php endif; ?>

        <form method="post" action="" onsubmit="return validateProfile()">
            <fieldset>
                <legend>My Information</legend>
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token()); ?>">
                <table>
                    <tr>
                        <td><label for="pname">Full Name:</label></td>
                        <td><input type="text" id="pname" name="pname" value="<?php echo htmlspecialchars($user["name"]); ?>"></td>
                        <td><p class="error" id="pnameError"></p></td>
                    </tr>
                    <tr>
                        <td><label>Email:</label></td>
                        <td><input type="text" value="<?php echo htmlspecialchars($user["email"]); ?>" readonly></td>
                        <td><span class="appt-meta">Email can't be changed</span></td>
                    </tr>
                    <tr>
                        <td><label for="pphone">Phone:</label></td>
                        <td><input type="text" id="pphone" name="pphone" value="<?php echo htmlspecialchars($user["phone"] ?? ""); ?>"></td>
                        <td></td>
                    </tr>
                    <?php if ($user["role"] === "doctor"): ?>
                        <tr>
                            <td><label for="pspecialty">Specialty:</label></td>
                            <td>
                                <select id="pspecialty" name="pspecialty">
                                    <option value="">-- Select specialty --</option>
                                    <?php if ($specialties) while ($specialtyOption = $specialties->fetch_assoc()): ?>
                                        <option value="<?php echo htmlspecialchars($specialtyOption["name"]); ?>" <?php echo $user["specialty"] === $specialtyOption["name"] ? "selected" : ""; ?>>
                                            <?php echo htmlspecialchars($specialtyOption["name"]); ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </td>
                            <td><span class="appt-meta">Role: Doctor</span></td>
                        </tr>
                    <?php endif; ?>
                    <tr>
                        <td colspan="3">
                            <input class="btn" type="submit" value="Save Changes">
                        </td>
                    </tr>
                </table>
            </fieldset>
        </form>
        <?php if ($user["role"] !== "admin"): ?>
            <form method="post" action="../Controller/ProfileController.php" onsubmit="return confirm('Delete your account permanently? This cannot be undone.')">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token()); ?>">
                <input type="hidden" name="action" value="delete">
                <button class="btn btn-danger" type="submit">Delete Account</button>
            </form>
        <?php endif; ?>
    </div>
    <?php include "footer.php"; ?>
</body>
</html>
