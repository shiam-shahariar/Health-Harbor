<?php $required_role = "patient"; include "../Controller/auth.php"; ?>
<!DOCTYPE html>
<html lang="en-US">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Health Harbor | Search Doctors</title>
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
        <h1>Search Doctors</h1>
        <p class="lead">Type a doctor's name or a specialty — results load live (AJAX) without reloading the page.</p>

        <fieldset>
            <legend>Find a Doctor</legend>
            <table>
                <tr>
                    <td><label for="q">Search:</label></td>
                    <td><input type="text" id="q" name="q" onkeyup="searchDoctors()" placeholder="e.g. Cardiology or Rahman"></td>
                </tr>
            </table>
        </fieldset>

        <!-- AJAX fills this container with a JSON-driven result table -->
        <div id="searchResults"></div>
    </div>
    <?php include "footer.php"; ?>

    <script> searchDoctors(); </script>
</body>
</html>
