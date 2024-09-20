<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/notifications.css">
    <link rel="stylesheet" href="css/home.css">
    <link rel="icon" href= "images/logo.png" type="image/x-icon">
    <title>TaskMaster</title>
</head>

<body>
    <nav class="navbar">
        <div class="logo">
            <a href="home.php"><img src="images/logo.png" alt=""> </a>
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
        include("header.php");

        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            header("Location: login.php");
            exit();
        }
        $user_id = $_SESSION['user_id'];
        if (isset($_POST['task_id'])) {
            $task_id = $_POST['task_id'];
        }
        // Fetch current data from users table

        $taskData = "SELECT * FROM tasks WHERE id = '$task_id' AND user_id = '$user_id'";
        $result = mysqli_query($conn, $taskData);
        if ($result && mysqli_num_rows($result) > 0) {
            $tasks = mysqli_fetch_assoc($result);
        } else {
            echo "<script>window.onload = function() { showToast('<i class=\'fa-solid fa-circle-xmark\'></i>Error! Task not found'); };</script>";
            exit();
        }

        //delete selected task
        if (isset($_POST['delete'])) {
            $task_id = $_POST['task_id'];
            $delete_query = "DELETE FROM tasks WHERE id = '$task_id' AND user_id = '$user_id'";
            // echo $delete_query;
            $delete_result = mysqli_query($conn, $delete_query);
            if ($delete_result) {
                echo "<script>window.onload = function() { showToast('<i class=\'fa-solid fa-circle-check\'></i>Task Deleted Successfully'); };</script>";
                header('Location: home.php');
            } else {
                echo "<script>window.onload = function() { showToast('<i class=\'fa-solid fa-circle-xmark\'></i>Error'); };</script>";
            }
        }



        ?>
        <div class="task_container">
            <a href="home.php">
                <button class="task_btn">
                    <i class="fa-solid fa-arrow-left"></i> Back to Task List
                </button>
            </a>
            <h1 class="task_heading">Task Details</h1>
            <div class="show_task">
                <label for="title">Title: </label>
                <input type="text" id="title" name="title" value="<?php echo $tasks['title']; ?>" readonly>
            </div>
            <div class="show_task">
                <label for="deadline">Deadline:</label>
                <input type="date" id="deadline" name="deadline" value="<?php echo date('Y-m-d', strtotime($tasks['deadline'])); ?>" readonly>
            </div>
            <div class="show_task">
                <label for="description">Description:</label>
                <textarea name="description" id="description" readonly><?php echo $tasks['description']; ?></textarea>
            </div>
            <div class="show_task">
                <label for="category">Status:</label>
                <input type="text" id="status" name="status" value="<?php echo $tasks['status']; ?>" readonly>
            </div>
            <div class="show_task">
                <label for="category">Category:</label>
                <input type="text" id="categoryText" name="category" value="<?php echo $tasks['category']; ?>" readonly>
            </div>



            <form method="post" class="task_edit_buttons" id="taskForm">
                <input type="hidden" name="task_id" value="<?php echo $task_id; ?>">
                <input class="task_btn" type="button" name="complete" id="completeTaskBtn" value="Mark as Completed">
                <input class="task_btn" type="button" data-task-id="<?php echo $task_id; ?>" id="editTaskBtn" value="Edit Task">
                <input class="task_btn" type="submit" name="delete" value="Delete Task">
            </form>
        </div>
    </div>
    <div id="toastbox"></div>

    <script src="js/notifications.js"></script>
    <script src="https://kit.fontawesome.com/7d550adf0b.js" crossorigin="anonymous"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            //status color
            const statusValue = document.querySelector('#status');
            if (statusValue.value === "completed") {
                statusValue.style.color = "green";
                document.getElementById('completeTaskBtn').style.display = "none";
            } else {
                statusValue.style.color = "red";
            }

            //textarea height
            function autoResizeTextarea(textarea) {
                textarea.style.height = 'auto'; // Reset height
                textarea.style.height = textarea.scrollHeight + 'px'; // Set new height based on content
            }
            const textareas = document.querySelectorAll('.show_task textarea');
            textareas.forEach(textarea => {
                autoResizeTextarea(textarea);
                textarea.addEventListener('input', () => autoResizeTextarea(textarea));
            });

            //complete task
            document.getElementById('completeTaskBtn').addEventListener('click', function() {
                const taskID = document.querySelector('input[name="task_id"]').value;
                const xhr = new XMLHttpRequest();
                xhr.open('POST', 'complete_task.php', true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.onload = function() {
                    if (xhr.status === 200) {
                        console.log('Response:', xhr.responseText); // Debugging log
                        document.getElementById('status').value = 'completed';
                        document.getElementById('status').style.color = 'green';
                        showToast('<i class="fa-solid fa-circle-check"></i>Task Completed Successfully');
                    } else {
                        showToast('<i class="fa-solid fa-circle-xmark"></i>Error');
                    }
                };
                xhr.send('task_id=' + encodeURIComponent(taskID));
                document.getElementById('completeTaskBtn').style.display = "none";
            });
            document.getElementById('editTaskBtn').addEventListener('click', function() {
                const taskID = this.getAttribute('data-task-id');
                submitTaskID(taskID);
            });
        });
        //edit task
        function submitTaskID(taskID) {
            // Create a form element
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = 'edit_task.php'; // Point to the task details page

            // Create a hidden input field for the task ID
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'task_id';
            input.value = taskID;

            // Append the input to the form
            form.appendChild(input);

            // Append the form to the body and submit
            document.body.appendChild(form);
            form.submit();
        }
    </script>
</body>

</html>