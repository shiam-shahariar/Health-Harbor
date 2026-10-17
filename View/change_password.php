<?php
include "../Controller/auth.php";
include "../Controller/PasswordController.php";
?>
<!DOCTYPE html>
<html lang="en-US">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Health Harbor | Change Password</title>
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
        <h1>Change Password</h1>

        <?php if (!empty($message)): ?>
            <p class="lead" style="color:<?php echo (strpos($message,'success')!==false)?'#2b8a3e':'#c92a2a'; ?>;"><?php echo htmlspecialchars($message); ?></p>
        <?php endif; ?>

        <form method="post" action="" onsubmit="return validatePassword()">
            <fieldset>
                <legend>Update Password</legend>
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token()); ?>">
                <table>
                    <tr>
                        <td><label for="current">Current Password:</label></td>
                        <td><input type="password" id="current" name="current"></td>
                        <td><p class="error" id="currentError"></p></td>
                    </tr>
                    <tr>
                        <td><label for="newpass">New Password:</label></td>
                        <td><input type="password" id="newpass" name="newpass"></td>
                        <td><p class="error" id="newpassError"></p></td>
                    </tr>
                    <tr>
                        <td><label for="confirmpass">Confirm New Password:</label></td>
                        <td><input type="password" id="confirmpass" name="confirmpass"></td>
                        <td><p class="error" id="confirmpassError"></p></td>
                    </tr>
                    <tr>
                        <td colspan="3"><input class="btn" type="submit" value="Update Password"></td>
                    </tr>
                </table>
            </fieldset>
        </form>
    </div>
    <?php include "footer.php"; ?>
</body>
</html>
