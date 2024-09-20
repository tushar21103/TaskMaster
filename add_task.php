<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/notifications.css">
    <!-- <link rel="stylesheet" href="css/style.css"> -->
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



        // Input data to tasks table
        if (isset($_POST['add'])) {
            $title = mysqli_real_escape_string($conn, $_POST['title']);
            $description = nl2br(mysqli_real_escape_string($conn, $_POST['description']));
            $status = mysqli_real_escape_string($conn, $_POST['status']);
            $category = mysqli_real_escape_string($conn, $_POST['category']);
            $deadline = mysqli_real_escape_string($conn, $_POST['deadline']);
            $user_id = $_SESSION['user_id'];

            // Prepare SQL statement
            $insert_query = "INSERT INTO tasks (user_id, title, description, status, category, deadline) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $insert_query);

            // Bind parameters
            mysqli_stmt_bind_param($stmt, "isssss", $user_id, $title, $description, $status, $category, $deadline);

            // Execute statement
            if (mysqli_stmt_execute($stmt)) {
                // Set session variable for successful task addition
                $_SESSION['task_added'] = true;

                // Close statement
                mysqli_stmt_close($stmt);

                // Display toast notification
                echo "<script>
                window.onload = function() {
                    showToast('<i class=\'fa-solid fa-circle-check\'></i> Task Added Successfully');
                    setTimeout(function() {
                        window.location.href = 'add_task.php';
                    }, 2500); // Redirect after 5 seconds
                };
              </script>";
            } else {
                echo "<script>window.onload = function() { showToast('<i class=\'fa-solid fa-circle-exclamation\'></i> Error adding task'); };</script>";
            }
        }
        ?>
        <div class="task_container">
            <a href="home.php">
                <button class="task_btn" style="margin: 1rem 0 1rem 1rem">
                    <i class="fa-solid fa-arrow-left"></i> Back to Task List
                </button>
            </a>
            <h1 class="task_heading">Add Task Details</h1>

            <form id="task_form" action="add_task.php" method="post">
                <div class="show_task">
                    <label for="title">Title: </label>
                    <input type="text" id="title" name="title" placeholder="Enter Title" required>
                </div>
                <div class="show_task">
                    <label for="description">Description:</label>
                    <textarea name="description" id="description" placeholder="Enter Task Description" required></textarea>
                </div>
                    <input type="hidden" name="status" id="status" value="Pending">
                <div class="show_task">
                    <label for="category">Category:</label>
                    <select id="category" name="category">
                        <option value="" disabled selected>Select Category</option>
                        <option value="work">Work</option>
                        <option value="personal">Personal</option>
                    </select>
                </div>
                <div class="show_task">
                    <label for="deadline">Deadline:</label>
                    <input type="date" id="deadline" name="deadline" value="<?php echo date('Y-m-d', strtotime($tasks['deadline'])); ?>">
                </div>
                <div>
                    <input type="submit" name="add" value="Add Task" class="task_btn" style="margin: 1rem 0 1rem 1rem">
                </div>
            </form>
        </div>
        <div id="toastbox"></div>
    </div>
    <script src="js/notifications.js"></script>
    <script src="https://kit.fontawesome.com/7d550adf0b.js" crossorigin="anonymous"></script>
</body>

</html>