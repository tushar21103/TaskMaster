<?php
include("php/config.php");
// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
$user_id = $_SESSION['user_id'];
$userData = "SELECT * FROM users WHERE id = '$user_id'";
$result = mysqli_query($conn, $userData);
if ($result) {
    if (mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        $pfp_id_query = "SELECT profile_photo_ID FROM users WHERE id = '$user_id'";
        $pfp_result = mysqli_query($conn, $pfp_id_query);
        if ($pfp_result) {
            $pfp_row = mysqli_fetch_assoc($pfp_result);
            $pfp_id = $pfp_row['profile_photo_ID'];
            echo "<script> 
                        window.onload = function() {
                            var navPfp = document.querySelector('#nav_pfp');
                            if (parseInt($pfp_id) === 1) {
                                navPfp.src = 'images/pfp/male.jpg';
                            } else if (parseInt($pfp_id) === 0) {
                                navPfp.src = 'images/pfp/female.jpg';
                            } else {
                                navPfp.src = 'images/pfp/no_pfp.png'; // default image
                            }
                        };
                    </script>";
        } else {
            echo "<script>window.onload = function() { showToast('<i class=\'fa-solid fa-circle-xmark\'></i>Error! Failed to retrieve profile photo ID'); };</script>";
        }
    } else {
        echo "<script>window.onload = function() { showToast('<i class=\'fa-solid fa-circle-xmark\'></i>Error! User not found'); };</script>";
        exit();
    }
} else {
    echo "<script>window.onload = function() { showToast('<i class=\'fa-solid fa-circle-exclamation\'></i>Invalid Username'); };</script>";
}
?>