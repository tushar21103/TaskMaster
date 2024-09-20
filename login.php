<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/notifications.css">
    <link rel="icon" href= "images/logo.png" type="image/x-icon">
    <title>Register</title>
</head>

<body>
    <div class="container">

        <?php
        include("php/config.php");

        // Signup logic
        if (isset($_POST['signup'])) {
            $username = mysqli_real_escape_string($conn, $_POST['username']);
            $email = mysqli_real_escape_string($conn, $_POST['email']);
            $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

            // Verify unique email
            $verify_email = mysqli_query($conn, "SELECT email FROM users WHERE email = '$email'");
            $verify_username = mysqli_query($conn, "SELECT username FROM users WHERE username = '$username'");
            if (mysqli_num_rows($verify_email) != 0) {
                echo "<script>window.onload = function() { showToast('<i class=\'fa-solid fa-circle-exclamation\'></i>Email already exists'); };</script>";
            } else if (mysqli_num_rows($verify_username) != 0) {
                echo "<script>window.onload = function() { showToast('<i class=\'fa-solid fa-circle-exclamation\'></i>Username is already taken'); };</script>";
            } else {
                $insert_query = "INSERT INTO users (username, email, password) VALUES ('$username', '$email', '$password')";
                if (mysqli_query($conn, $insert_query)) {
                    echo "<script>window.onload = function() { showToast('<i class=\'fa-solid fa-circle-check\'></i>Registration Successfull'); };</script>";
                } else {
                    echo "<script>window.onload = function() { showToast('<i class=\'fa-solid fa-circle-xmark\'></i>Error'); };</script>";
                }
            }
        }


        // Login logic
        session_start(); // Start session

        if (isset($_POST['login'])) {
            $username = mysqli_real_escape_string($conn, $_POST['username']);
            $password = $_POST['password'];

            $login_query = mysqli_query($conn, "SELECT * FROM users WHERE username = '$username'");
            if (mysqli_num_rows($login_query) > 0) {
                $user = mysqli_fetch_assoc($login_query);
                if (password_verify($password, $user['password'])) {
                    $_SESSION['user_id'] = $user['id'];
                    echo "<script>window.onload = function() { showToast('<i class=\'fa-solid fa-circle-check\'></i>Login Successfull'); };</script>";
                    header("Location: home.php");
                    exit();
                } else {
                    echo "<script>window.onload = function() { showToast('<i class=\'fa-solid fa-circle-xmark\'></i>Incorrect Password'); };</script>";
                }
            } else {
                echo "<script>window.onload = function() { showToast('<i class=\'fa-solid fa-circle-exclamation\'></i>Invalid Username'); };</script>";
            }
        }

        ?>

        <div class="login_signup">
            <button class="login_btn active">Login</button>
            <button class="signup_btn">Sign Up</button>
        </div>
        <form id="signup_form" method="post">
            <div class="field input">
                <label for="username">Username</label>
                <input type="text" name="username" id="username" placeholder="Enter your username" required>
            </div>
            <div class="field input">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" placeholder="Enter your email" required>
            </div>
            <div class="field input">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" placeholder="Enter your password" required>
            </div>
            <div class="field">
                <input type="submit" name="signup" value="Sign Up" class="submit_btn">
            </div>
        </form>
        <form id="login_form" method="post">
            <div class="field input">
                <label for="username">Username</label>
                <input type="text" name="username" id="username" placeholder="Enter your username" required>
            </div>
            <div class="field input">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" placeholder="Enter your password" required>
            </div>
            <div class="forgotpass">
                <a href="forgot_password.php">Forgot Your Password?</a>
            </div>
            <div class="field">
                <input type="submit" name="login" value="Login" class="submit_btn">
            </div>
        </form>
        <div id="toastbox"></div>
    </div>
    <script src="js/notifications.js"></script>
    <script src="js/script.js"></script>
    <script src="https://kit.fontawesome.com/7d550adf0b.js" crossorigin="anonymous"></script>

</body>

</html>