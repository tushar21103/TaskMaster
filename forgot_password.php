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

        use PHPMailer\PHPMailer\PHPMailer;
        use PHPMailer\PHPMailer\SMTP;
        use PHPMailer\PHPMailer\Exception;

        // Load Composer's autoloader
        require 'vendor/autoload.php';

        function send_password_reset($userName, $userEmail, $token)
        {
            $mail = new PHPMailer(true);

            try {
                //Server settings
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = 'pmelody2103@gmail.com'; // Replace with your Gmail email
                $mail->Password   = 'sero jljl vahn hben'; // Replace with your Gmail password
                $mail->SMTPSecure = 'ssl';
                $mail->Port       = 465;

                //Recipients
                $mail->setFrom('pmelody2103@gmail.com', $userName);
                $mail->addAddress($userEmail);

                // Content
                $mail->isHTML(true);
                $mail->Subject = 'Password Reset Request';
                $mail->Body    = "
                    <h2>Hello $userName</h2><br>
                    <h3>You are receiving this email because we received a password reset request from your account.</h3><br><br>
                    <a href='http://localhost/taskmaster/change_password.php?token=$token&email=$userEmail'>Click Here to reset Password</a>";

                $mail->send();
                echo "<script>window.onload = function() { showToast('<i class=\'fa-solid fa-circle-check\'></i>Mail Sent Successfully'); };</script>";
            } catch (Exception $e) {
                echo "<script>window.onload = function() { showToast('<i class=\'fa-solid fa-circle-xmark\'></i>Message could not be sent. Mailer Error: {$mail->ErrorInfo}'); };</script>";
            }
        }

        // Process form submission
        if (isset($_POST['send_reset_code'])) {
            $email = mysqli_real_escape_string($conn, $_POST['email']);
            $token = md5(rand()); // Generate a random token
            

            $email_query = mysqli_query($conn, "SELECT * FROM users WHERE email = '$email'");
            if (mysqli_num_rows($email_query) > 0) {
                $user = mysqli_fetch_assoc($email_query);
                $userName = $user['username'];
                $userEmail = $user['email'];

                // Update the token in the database
                $update_token = "UPDATE users SET password_reset_token = '$token' WHERE email = '$userEmail'";
                $update_token_query = mysqli_query($conn, $update_token);

                if ($update_token_query) {
                    send_password_reset($userName, $userEmail, $token);
                    echo "<script>
                        alert('Mail Sent Successfully...');
                        window.location.href = 'forgot_password.php?status=success';
                    </script>";
                    exit(); // Ensure script does not continue
                } else {
                    echo "<script>window.onload = function() { showToast('<i class=\'fa-solid fa-circle-xmark\'></i>Error updating token'); };</script>";
                }
            } else {
                echo "<script>window.onload = function() { showToast('<i class=\'fa-solid fa-circle-xmark\'></i>Error! This Email ID is not registered'); };</script>";
            }
        }
        ?>

        <div class="heading">
            <h1>Forgot Password</h1>
        </div>
        <form id="password_form" method="post" action="forgot_password.php">
            <div class="field input">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" placeholder="Enter your Registered Email" required>
            </div>
            <div class="field">
                <input type="submit" name="send_reset_code" value="Send Reset Code" class="submit_btn">
            </div>
        </form>
        <div id="toastbox"></div>
    </div>

    <?php
    if (isset($_POST['update_password'])) {
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $newPassword = mysqli_real_escape_string($conn, $_POST['new_password']);
        $confirmPassword = mysqli_real_escape_string($conn, $_POST['confirm_password']);
        $token = mysqli_real_escape_string($conn, $_POST['password_token']);

        if (!empty($token)) {
            if (!empty($email) && !empty($newPassword) && !empty($confirmPassword)) {
                $checkToken = "SELECT password_reset_token FROM users WHERE password_reset_token = '$token'";
                $checkToken_query = mysqli_query($conn, $checkToken);
                if (mysqli_num_rows($checkToken_query) > 0) {
                    if ($newPassword == $confirmPassword) {
                        $NEW_Password = password_hash($_POST['new_password'], PASSWORD_BCRYPT);
                        $update_password = "UPDATE users SET password = '$NEW_Password', password_reset_token = NULL WHERE password_reset_token = '$token'";
                        $updatePassword_query = mysqli_query($conn, $update_password);
                        if ($updatePassword_query) {
                            echo "<script>
                        alert('Password Updated Successfully...');
                        window.location.href = 'login.php';
                    </script>";
                            exit();
                        } else {
                            echo "<script>window.onload = function() { showToast('<i class=\'fa-solid fa-circle-xmark\'></i>Error! Password is not Updated'); };</script>";
                            header("Location: change_password.php?token=$token&email=$email");
                            exit();
                        }
                    } else {
                        echo "<script>window.onload = function() { showToast('<i class=\'fa-solid fa-circle-xmark\'></i>Error! Password does not match'); };</script>";
                        header("Location: change_password.php?token=$token&email=$email");
                        exit();
                    }
                } else {
                    echo "<script>window.onload = function() { showToast('<i class=\'fa-solid fa-circle-xmark\'></i>Error! Invalid Token'); };</script>";
                    header("Location: change_password.php?token=$token&email=$email");
                    exit();
                }
            } else {
                echo "<script>window.onload = function() { showToast('<i class=\'fa-solid fa-circle-xmark\'></i>Error! Empty Fields'); };</script>";
                header("Location: change_password.php?token=$token&email=$email");
                exit();
            }
        } else {
            echo "<script>window.onload = function() { showToast('<i class=\'fa-solid fa-circle-xmark\'></i>Error! Empty token'); };</script>";
            header("Location: change_password.php?token=$token&email=$email");
            exit();
        }
    }
    ?>





    <script src="js/notifications.js"></script>
    <script src="js/script.js"></script>
    <script src="https://kit.fontawesome.com/7d550adf0b.js" crossorigin="anonymous"></script>

</body>

</html>