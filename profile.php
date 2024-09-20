<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/notifications.css">
    <link rel="stylesheet" href="css/header.css">
    <link rel="icon" href= "images/logo.png" type="image/x-icon">
    <title>TaskMaster</title>
</head>

<body>
    <nav class="navbar">
        <div class="logo">
            <a href="home.php"><img  src="images/logo.png" alt=""> </a>
        </div>
        <div class="icons">
            <a href="home.php">Home</a>
            <a href="about.php">About</a>
            <div class="dropdown">
                <button class="dropbtn">
                    <img id="nav_pfp" src="images/pfp/no_pfp.png" alt="">
                    <i class="fa-solid fa-angle-down"></i>
                </button>
                <div id="myDropdown" class="dropdown-content">
                    <a href="profile.php">Edit Profile</a>
                    <a href="logout.php">Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="container">

        <?php
        session_start();
        include("php/config.php");

        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            header("Location: login.php");
            exit();
        }
        $user_id = $_SESSION['user_id'];

        //fetch current data
        $userData = "SELECT * FROM users WHERE id = '$user_id'";
        $result = mysqli_query($conn, $userData);
        if ($result) {
            if (mysqli_num_rows($result) > 0) {
                $user = mysqli_fetch_assoc($result);
            } else {
                echo "<script>window.onload = function() { showToast('<i class=\'fa-solid fa-circle-xmark\'></i>Error! User not found'); };</script>";
                exit();
            }
        } else {
            echo "<script>window.onload = function() { showToast('<i class=\'fa-solid fa-circle-exclamation\'></i>Invalid Username'); };</script>";
        }

        // update
        if (isset($_POST['save'])) {
            $name = mysqli_real_escape_string($conn, $_POST['username']);
            $age = mysqli_real_escape_string($conn, $_POST['userage']);
            $phoneNo = mysqli_real_escape_string($conn, $_POST['usermobile']);
            $pfpID = mysqli_real_escape_string($conn, $_POST['profile_picture_id']);
            $user_id = $_SESSION['user_id'];
            $update_query = "UPDATE users SET name = '$name', age = '$age', mobile_number = '$phoneNo', profile_photo_ID = '$pfpID' WHERE id = '$user_id'";


            if (mysqli_query($conn, $update_query)) {
                $userData = "SELECT * FROM users WHERE id = '$user_id'";
                $result = mysqli_query($conn, $userData);
                if ($result) {
                    if (mysqli_num_rows($result) > 0) {
                        $user = mysqli_fetch_assoc($result);
                    }
                }
                echo "<script>window.onload = function() { showToast('<i class=\'fa-solid fa-circle-check\'></i>Updated Successfully'); };</script>";
            } else {
                echo "<script>window.onload = function() { showToast('<i class=\'fa-solid fa-circle-exclamation\'></i>Invalid Username'); };</script>";
            }
        }
        ?>

        <form id="profile_form" method="post">

            <div class="profile_info profile_img">
                <label for="profile_picture_id">Profile Picture</label><br>
                <div class="profile_btns">
                    <button type='button' class="pfp" id="pfp_male" onclick="selectProfilePicture(1)">
                        <div class="pfp-img-wrapper">
                            <img src="images/pfp/male.jpg" alt="Profile Picture" id="img_male">
                        </div>
                    </button>
                    <button type='button' class="pfp" id="pfp_female" onclick="selectProfilePicture(0)">
                        <div class="pfp-img-wrapper">
                            <img src="images/pfp/female.jpg" alt="Profile Picture" id="img_female">
                        </div>
                    </button>
                </div>
                <input type="hidden" name="profile_picture_id" id="profile_picture_id" value="<?php echo $user['profile_photo_ID']; ?>">
                <br>
            </div>
            <div class="profile_info">
                <label for="username">Name</label>
                <input type="text" name="username" id="username" placeholder="Enter your Name" value="<?php echo $user['name']; ?>">
            </div>
            <div class="profile_info">
                <label for="userage">Age</label>
                <input type="number" name="userage" id="userage" placeholder="Enter your Age" value="<?php echo $user['age']; ?>">
            </div>
            <div class="profile_info">
                <label for="usermobile">Phone Number</label>
                <input type="tel" name="usermobile" id="usermobile" placeholder="Enter your Phone Number" pattern="[0-9]{10}" value="<?php echo $user['mobile_number']; ?>">
            </div>
            <div>
                <input type="submit" name="save" value="Save" class="submit_btn">
            </div>

        </form>
        <div>
            <form id="logout_form" method="post" action="logout.php">
                <input type="hidden" name="logout" value="Logout" class="submit_btn">
            </form>
        </div>
        <div id="toastbox"></div>
    </div>
    <script src="js/notifications.js"></script>
    <script src="https://kit.fontawesome.com/7d550adf0b.js" crossorigin="anonymous"></script>

    <script>
        const pfpMale = document.querySelector("#pfp_male");
        const pfpFemale = document.querySelector("#pfp_female");
        const pfpID = document.querySelector('#profile_picture_id');
        const navPfp = document.querySelector('#nav_pfp');         

        if (parseInt(pfpID.value) === 1) {
            pfpFemale.style.border = "none";
            pfpMale.style.border = "5px solid #e20404";
            navPfp.src = 'images/pfp/male.jpg'

        }
        if (parseInt(pfpID.value) === 0) {
            pfpMale.style.border = "none";
            pfpFemale.style.border = "5px solid #e20404";
            navPfp.src = 'images/pfp/female.jpg'
        }

        pfpMale.addEventListener("click", () => {
            pfpFemale.style.border = "none";
            pfpMale.style.border = "5px solid #e20404";
        });

        pfpFemale.addEventListener("click", () => {
            pfpMale.style.border = "none";
            pfpFemale.style.border = "5px solid #e20404";
        });

        function selectProfilePicture(id) {
            document.getElementById("profile_picture_id").value = id;

        }
    </script>
</body>

</html>