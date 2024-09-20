<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/notifications.css">
    <link rel="icon" href= "images/logo.png" type="image/x-icon">
    <title>Forgot Password</title>
</head>

<body>
    <div class="container">

        <?php
        include("php/config.php");

        ?>

        <div class="heading">
            <h1>Change Your Password</h1>
        </div>
        <form id="password_form" method="post" action="forgot_password.php">
            <div class="field input">
                <input type="hidden" name="password_token" value="<?php if(isset($_GET['token'])){
                    echo $_GET['token'];}?>">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" placeholder="Enter your Registered Email" value="<?php if(isset($_GET['email'])){
                    echo $_GET['email'];}?>" required>
            </div>
            <div class="field input">
                <label for="new_password">New Password</label>
                <input type="password" name="new_password" id="new_password" placeholder="Enter New Password" required>
            </div>
            <div class="field input">
                <label for="confirm_password">Confirm Password</label>
                <input type="password" name="confirm_password" id="confirm_password" placeholder="Confirm New Password" required>
                <span id="password_error" style="color: red;"></span>
            </div>
            <div class="field">
                <input type="submit" name="update_password" value="Update Password" class="submit_btn">
            </div>
        </form>

        <div id="toastbox"></div>
    </div>
    <script src="js/notifications.js"></script>
    <script src="https://kit.fontawesome.com/7d550adf0b.js" crossorigin="anonymous"></script>
    <script>
    // Function to validate password match
    function validatePassword() {
        var newPassword = document.getElementById('new_password').value;
        var confirmPassword = document.getElementById('confirm_password').value;
        var passwordError = document.getElementById('password_error');

        if (newPassword !== confirmPassword) {
            passwordError.textContent = '*Passwords do not match';
            return false;
        } else {
            passwordError.textContent = '';
            return true;
        }
    }

    // Event listeners for keyup events on password fields

    document.getElementById('confirm_password').addEventListener('keyup', validatePassword);

    </script>

</body>

</html>