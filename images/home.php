<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/notifications.css">
    <link rel="stylesheet" href="css/home.css">
    <title>TaskMaster</title>
</head>

<body>
    <nav class="navbar">
        <div class="logo">
            <a href="home.php"><img src="images/logo_black.png" alt=""> </a>
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

        // Fetch tasks with the closest deadline first
        $tasks_query = "SELECT * FROM tasks WHERE user_id = '$user_id' ORDER BY deadline ASC";
        $tasks_result = mysqli_query($conn, $tasks_query);

        // Organize tasks by deadline dates
        $tasks_by_date = [];
        if ($tasks_result && mysqli_num_rows($tasks_result) > 0) {
            while ($task = mysqli_fetch_assoc($tasks_result)) {
                $deadline_date = date("jS F, Y", strtotime($task['deadline']));
                $tasks_by_date[$deadline_date][] = $task;
            }
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

        //complete
        if (isset($_POST['complete'])) {
            $task_id = $_POST['task_id'];

            $complete_query = "UPDATE tasks SET status = 'Completed' WHERE id = '$task_id' AND user_id = '$user_id'";
            $complete_result = mysqli_query($conn, $complete_query);
            // echo $complete_query;
            if ($complete_result) {
                echo "<script>window.onload = function() { showToast('<i class=\'fa-solid fa-circle-check\'></i>Task Completed Successfully'); };</script>";
            } else {
                echo "<script>window.onload = function() { showToast('<i class=\'fa-solid fa-circle-xmark\'></i>Error'); };</script>";
            }
        }
        ?>

        <div class="left_container">
            <button class="task_btn" id="add_task_btn" onclick="location.href='add_task.php'">Add Task</button>
            <div class="status_title">
                <h2 style="margin-bottom: 1rem;">Status</h2>
                <div class="status_buttons">
                    <button class="stat_button" id="all_stat">All</button>
                    <button class="stat_button" id="pending_stat">Pending</button>
                    <button class="stat_button" id="completed_stat">Completed</button>
                </div>
            </div>
            <div class="category_title">
                <h2 style="margin-bottom: 1rem;">Category</h2>
                <div class="category_buttons">
                    <button class="cat_button" id="all_cat">All</button>
                    <button class="cat_button" id="work_cat">Work</button>
                    <button class="cat_button" id="personal_cat">Personal</button>
                </div>
            </div>

            <?php
            if (!empty($tasks_by_date)) {
                foreach ($tasks_by_date as $date => $tasks) {
                    echo "<div class='task-date-container'>";
                    echo "<h3 class = 'task_date'>$date</h3>";
                    echo "<hr>";
                    foreach ($tasks as $task) {
                        $taskID = number_format($task['id']);
                        $title = $task['title'];
                        $description = $task['description'];
                        $deadline = date("jS F, Y", strtotime($task['deadline']));
                        $status = $task['status'];
                        $category = $task['category'];

                        echo "<div class='task-item' 
                                data-taskID = '$taskID'
                                data-title='$title' 
                                data-description='$description' 
                                data-deadline='$deadline'
                                data-status='$status'
                                data-category='$category'
                                style='display: flex; align-items: center; margin-bottom: 10px; cursor: pointer;'>
                                <i style='margin-right: 0.6rem;' class='fa-solid fa-angles-right'></i>$title
                              </div>";
                    }
                    echo "</div>";
                }
            } else {
                echo "<p>No tasks found</p>";
            }
            ?>
        </div>

        <div class="right_container" id="taskDetails">

        </div>

    </div>

    <div id="toastbox"></div>

    <script src="js/notifications.js"></script>
    <script src="https://kit.fontawesome.com/7d550adf0b.js" crossorigin="anonymous"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const taskItems = document.querySelectorAll('.task-item');
            const taskDateContainers = document.querySelectorAll('.task-date-container');
            const taskDetails = document.getElementById('taskDetails');
            let currentStatusFilter = 'all';
            let currentCategoryFilter = 'all';

            taskDetails.innerHTML = `
        <h1 class="task_ID" style='display:none;'></h1>
        <h1 class="task_title"></h1>
        <b class="task_deadline">Deadline: <span class="task_dead"></span></b><br>
        <b class="task_status">Status: <span class="task_stat" ></span></b><br>
        <b class="task_description">Description:</b>
        <p class="description_text"></p>
        <b class="task_category">Category: <span class="task_cat"></span></b>
        <form method="post" action="home.php" class="task_buttons" id="taskForm">
            <input type="hidden" name="task_id" class="task_id">
            <input class="task_btn" type="button" id="completeTaskBtn" value="Mark as Completed">
            <input class="task_btn" type="button" id="editTaskBtn" value="Edit Task">
            <input class="task_btn" type="submit" formaction="home.php" name="delete" id="delete" value="Delete Task">
        </form>
    `;

            function displayTaskDetails(taskElement) {
                const taskID = taskElement.getAttribute('data-taskID');
                const title = taskElement.getAttribute('data-title');
                const description = taskElement.getAttribute('data-description').replace(/\\r\\n|\\n|\\r/g, '<br>');
                const deadline = taskElement.getAttribute('data-deadline');
                const status = taskElement.getAttribute('data-status');
                const category = taskElement.getAttribute('data-category');

                document.querySelector('.task_ID').textContent = taskID;
                document.querySelector('.task_id').value = taskID;
                document.querySelector('.task_title').textContent = title;
                document.querySelector('.task_dead').textContent = deadline;
                document.querySelector('.task_stat').textContent = status;
                document.querySelector('.description_text').innerHTML = description;
                document.querySelector('.task_cat').textContent = category;

                const statusElement = document.querySelector('.task_stat');
                if (status === 'completed') {
                    statusElement.style.color = 'green';
                } else {
                    statusElement.style.color = 'red';
                }
            }

            function filterTasks() {
                taskDateContainers.forEach(dateContainer => {
                    const taskItems = dateContainer.querySelectorAll('.task-item');
                    let hasVisibleTasks = false;

                    taskItems.forEach(item => {
                        const taskStatus = item.getAttribute('data-status').toLowerCase();
                        const taskCategory = item.getAttribute('data-category').toLowerCase();

                        const statusMatch = (currentStatusFilter === 'all' || taskStatus === currentStatusFilter);
                        const categoryMatch = (currentCategoryFilter === 'all' || taskCategory === currentCategoryFilter);

                        if (statusMatch && categoryMatch) {
                            item.style.display = 'flex';
                            hasVisibleTasks = true;
                        } else {
                            item.style.display = 'none';
                        }
                    });

                    if (hasVisibleTasks) {
                        dateContainer.style.display = 'block';
                    } else {
                        dateContainer.style.display = 'none';
                    }
                });
            }

            taskItems.forEach(item => {
                item.addEventListener('click', function() {
                    displayTaskDetails(this);
                });
            });

            if (taskItems.length > 0) {
                displayTaskDetails(taskItems[0]);
            }

            document.getElementById('completeTaskBtn').addEventListener('click', function() {
                const taskID = document.querySelector('.task_id').value;

                const xhr = new XMLHttpRequest();
                xhr.open('POST', 'complete_task.php', true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.onload = function() {
                    if (xhr.status === 200) {
                        document.querySelector('.task_stat').textContent = 'completed';
                        document.querySelector('.task_stat').style.color = 'green';
                        showToast('<i class="fa-solid fa-circle-check"></i>Task Completed Successfully');
                    } else {
                        showToast('<i class="fa-solid fa-circle-xmark"></i>Error');
                    }
                };
                xhr.send('task_id=' + taskID);
            });

            document.querySelector('#editTaskBtn').addEventListener('click', () => {
                const taskID = document.querySelector('.task_id').value;
                window.location.href = `edit_task.php?task_id=${taskID}`;
            });

            document.getElementById('all_stat').addEventListener('click', () => {
                currentStatusFilter = 'all';
                document.getElementById('all_stat').style.background = 'black';
                document.getElementById('pending_stat').style.background = '#00796b';
                document.getElementById('completed_stat').style.background = '#00796b';
                filterTasks();
            });

            document.getElementById('pending_stat').addEventListener('click', () => {
                currentStatusFilter = 'pending';
                document.getElementById('all_stat').style.background = '#00796b';
                document.getElementById('pending_stat').style.background = 'black';
                document.getElementById('completed_stat').style.background = '#00796b';
                filterTasks();
            });

            document.getElementById('completed_stat').addEventListener('click', () => {
                currentStatusFilter = 'completed';
                document.getElementById('all_stat').style.background = '#00796b';
                document.getElementById('pending_stat').style.background = '#00796b';
                document.getElementById('completed_stat').style.background = 'black';
                filterTasks();
            });

            document.getElementById('all_cat').addEventListener('click', () => {
                currentCategoryFilter = 'all';
                document.getElementById('all_cat').style.background = 'black';
                document.getElementById('personal_cat').style.background = '#00796b';
                document.getElementById('work_cat').style.background = '#00796b';
                filterTasks();
            });

            document.getElementById('work_cat').addEventListener('click', () => {
                currentCategoryFilter = 'work';
                document.getElementById('all_cat').style.background = '#00796b';
                document.getElementById('personal_cat').style.background = '#00796b';
                document.getElementById('work_cat').style.background = 'black';
                filterTasks();
            });

            document.getElementById('personal_cat').addEventListener('click', () => {
                currentCategoryFilter = 'personal';
                document.getElementById('all_cat').style.background = '#00796b';
                document.getElementById('personal_cat').style.background = 'black';
                document.getElementById('work_cat').style.background = '#00796b';
                filterTasks();
            });
        });
    </script>


</body>

</html>