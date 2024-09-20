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

        ?>


        <div class="task_container">
            <a href="home.php">
                <button class="task_btn">
                    <i class="fa-solid fa-arrow-left"></i> Back to Task List
                </button>
            </a>
            <h1 class="task_heading">Edit Task Details</h1>
            <div class="show_task">
                <label for="title">Title: </label>
                <input type="text" id="title" name="title" value="<?php echo $tasks['title']; ?>">
            </div>
            <div class="show_task">
                <label for="deadline">Deadline:</label>
                <input type="date" id="deadline" name="deadline" value="<?php echo date('Y-m-d', strtotime($tasks['deadline'])); ?>">
            </div>
            <div class="show_task">
                <label for="description">Description:</label>
                <textarea name="description" id="description"><?php echo $tasks['description']; ?></textarea>
            </div>
            <div class="show_task">
                <label for="category">Category:</label>
                <select id="categorySelect" name="categorySelect">
                    <option value="work" <?php echo $tasks['category'] === 'work' ? 'selected' : ''; ?>>Work</option>
                    <option value="personal" <?php echo $tasks['category'] === 'personal' ? 'selected' : ''; ?>>Personal</option>
                </select>
            </div>



            <form method="post" class="task_edit_buttons" id="taskForm">
                <input type="hidden" name="task_id" value="<?php echo $task_id; ?>">
                <input class="task_btn" type="button" name="update" id="updateTask" value="Update Task">
            </form>
        </div>
    </div>
    <div id="toastbox"></div>
    <script src="js/notifications.js"></script>
    <script src="https://kit.fontawesome.com/7d550adf0b.js" crossorigin="anonymous"></script>
    <script>
        document.getElementById('updateTask').addEventListener('click', function(event) {
            event.preventDefault(); // Prevent the default form submission
            const taskID = document.querySelector('input[name="task_id"]').value;
            const title = document.getElementById('title').value;
            const description = document.getElementById('description').value;
            const category = document.getElementById('categorySelect').value;
            const deadline = document.getElementById('deadline').value;

            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'updated_task.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onload = function() {
                if (xhr.status === 200) {
                    showToast('<i class="fa-solid fa-circle-check"></i>Task Updated Successfully');
                } else {
                    showToast('<i class="fa-solid fa-circle-xmark"></i>Error updating task');
                }
            };
            xhr.send('task_id=' + taskID + '&title=' + title + '&description=' + description + '&category=' + category + '&deadline=' + deadline);
        });
    </script>
</body>

</html>