<?php
session_start();
include("php/config.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$task_id = $_POST['task_id'];

$complete_query = "UPDATE tasks SET status = 'Completed' WHERE id = '$task_id' AND user_id = '$user_id'";
$complete_result = mysqli_query($conn, $complete_query);

if ($complete_result) {
    echo "success";
} else {
    http_response_code(500);
    echo "error";
}
?>