<?php include "../Controller/LoginValidation.php"; ?>
<!DOCTYPE html>
<html lang="en-US">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Health Harbor | Login</title>
    <link rel="stylesheet" href="../Design/Style.css">
    <script src="../Javascript/Script.js"></script>
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
        <h1>Login</h1>

        <?php if (isset($_GET["registered"])): ?>
            <p class="lead" style="color:#2b8a3e;">Registration successful! Please log in.</p>
        <?php endif; ?>

        <?php if (!empty($message)): ?>
            <p class="error"><?php echo $message; ?></p>
        <?php endif; ?>

        <form method="post" action="" onsubmit="return validateLogin()">
            <fieldset>
                <legend>Account Login</legend>
                <table>
                    <tr>
                        <td><label for="email">Email:</label></td>
                        <td><input type="text" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>"></td>
                        <td><p class="error" id="emailError"></p></td>
                    </tr>
                    <tr>
                        <td><label for="password">Password:</label></td>
                        <td><input type="password" id="password" name="password"></td>
                        <td><p class="error" id="passwordError"></p></td>
                    </tr>
                    <tr>
                        <td colspan="3">
                            <input type="checkbox" id="remember" name="remember" value="1">
                            <label for="remember">Remember Me</label>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3"><input class="btn" type="submit" value="Login"></td>
                    </tr>
                </table>
            </fieldset>
        </form>
        <p>New here? <a href="register.php">Create an account</a>.</p>
    </div>
    <?php include "footer.php"; ?>
</body>
</html>
