<?php
session_start();
include("php/config.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$task_id = $_POST['task_id'];

$title = mysqli_real_escape_string($conn, $_POST['title']);
$description = mysqli_real_escape_string($conn, $_POST['description']);
$category = mysqli_real_escape_string($conn, $_POST['category']);
$deadline = mysqli_real_escape_string($conn, $_POST['deadline']);

$update_query = "UPDATE tasks SET title = '$title', description = '$description', category = '$category', deadline = '$deadline' WHERE id = '$task_id' AND user_id = '$user_id'";
$update_result = mysqli_query($conn, $update_query);

if ($update_result) {
    echo "success";
} else {
    http_response_code(500);
    echo "error";
}
?>
