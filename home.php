<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/notifications.css">
    <link rel="stylesheet" href="css/home.css">
    <link rel="icon" href="images/logo.png" type="image/x-icon">
    <title>TaskMaster</title>
</head>

<body>
    <nav class="navbar">
        <div class="logo">
            <a href="home.php"><img src="images/logo.png" alt=""></a>
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

        // Pagination logic
        $limit = 12; // Number of tasks per page
        $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
        $offset = ($page - 1) * $limit;

        // Fetch tasks with the closest deadline first with pagination
        $tasks_query = "SELECT * FROM tasks WHERE user_id = '$user_id' ORDER BY deadline ASC LIMIT $limit OFFSET $offset";
        $tasks_result = mysqli_query($conn, $tasks_query);

        // Get total number of tasks
        $total_tasks_query = "SELECT COUNT(*) as total FROM tasks WHERE user_id = '$user_id'";
        $total_tasks_result = mysqli_query($conn, $total_tasks_query);
        $total_tasks = mysqli_fetch_assoc($total_tasks_result)['total'];
        $total_pages = ceil($total_tasks / $limit);

        // Calculate the range of pages to display
        $max_pages_to_show = 5;
        $half_range = floor($max_pages_to_show / 2);
        $start_page = max(1, $page - $half_range);
        $end_page = min($total_pages, $page + $half_range);

        if ($end_page - $start_page < $max_pages_to_show - 1) {
            if ($start_page == 1) {
                $end_page = min($total_pages, $start_page + $max_pages_to_show - 1);
            } else {
                $start_page = max(1, $end_page - $max_pages_to_show + 1);
            }
        }

        // Organize tasks by deadline dates
        $tasks_by_date = [];
        if ($tasks_result && mysqli_num_rows($tasks_result) > 0) {
            while ($task = mysqli_fetch_assoc($tasks_result)) {
                $deadline_date = date("jS F, Y", strtotime($task['deadline']));
                $tasks_by_date[$deadline_date][] = $task;
            }
        }
        ?>
        <div class="filter_btn">
            <div class="filterBTN">
                <label for="statusFilter">Status</label>
                <select name="statusFilter" id="statusFilter">
                    <option value="all">All</option>
                    <option value="pending">Pending</option>
                    <option value="completed">Completed</option>
                </select>
            </div>
            <div class="filterBTN">
                <label for="categoryFilter">Category</label>
                <select name="categoryFilter" id="categoryFilter">
                    <option value="all">All</option>
                    <option value="personal">Personal</option>
                    <option value="work">Work</option>
                </select>
            </div>
            <button class="task_btn" id="add_task_btn" onclick="location.href='add_task.php'">Add Task</button>
        </div>

        <div class="all_task_buttons">
            <?php
            if (!empty($tasks_by_date)) {
                foreach ($tasks_by_date as $date => $tasks) {
                    foreach ($tasks as $task) {
                        $taskID = number_format($task['id']);
                        $title = $task['title'];
                        $description = $task['description'];
                        $deadline = date("jS F, Y", strtotime($task['deadline']));
                        $status = $task['status'];
                        $category = $task['category'];

                        echo "<div class='task_button' 
                        data-taskID='$taskID'
                        data-title='$title' 
                        data-description='$description' 
                        data-deadline='$deadline'
                        data-status='$status'
                        data-category='$category'>
                        <h2 class='TaskTitle'>$title</h2>
                        <p>Deadline: <span class='TaskDeadline'>$deadline</span></p>
                        <p>Status: <span class='TaskStatus'>$status</span></p>
                        <button class='go_to_task' onclick='submitTaskID($taskID)'>Task Details</button>
                      </div>";
                    }
                }
            } else {
                echo "<p>No tasks found</p>";
            }
            ?>
        </div>

        <!-- Pagination controls -->
        <div class="pagination">
            <?php
            if ($total_pages > 1) {
                if ($page > 1) {
                    echo "<a href='home.php?page=" . ($page - 1) . "' class='page_link'>Previous</a>";
                }
                for ($i = $start_page; $i <= $end_page; $i++) {
                    if ($i == $page) {
                        echo "<span class='current_page'>$i</span>";
                    } else {
                        echo "<a href='home.php?page=$i' class='page_link'>$i</a>";
                    }
                }
                if ($page < $total_pages) {
                    echo "<a href='home.php?page=" . ($page + 1) . "' class='page_link'>Next</a>";
                }
            }
            ?>
        </div>
    </div>

    <div id="toastbox"></div>

    <script src="js/notifications.js"></script>
    <script src="https://kit.fontawesome.com/7d550adf0b.js" crossorigin="anonymous"></script>
    <script>
        function submitTaskID(taskID) {
            // Create a form element
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = 'task_details.php'; // Point to the task details page

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

        function filterTasks() {
            const statusFilter = document.getElementById('statusFilter').value.toLowerCase();
            const categoryFilter = document.getElementById('categoryFilter').value.toLowerCase();

            const taskButtons = document.querySelectorAll('.task_button');

            taskButtons.forEach(button => {
                const status = button.getAttribute('data-status').toLowerCase();
                const category = button.getAttribute('data-category').toLowerCase();

                const statusMatch = (statusFilter === 'all' || status === statusFilter);
                const categoryMatch = (categoryFilter === 'all' || category === categoryFilter);

                if (statusMatch && categoryMatch) {
                    button.style.display = 'block';
                } else {
                    button.style.display = 'none';
                }
            });
        }

        document.getElementById('statusFilter').addEventListener('change', filterTasks);
        document.getElementById('categoryFilter').addEventListener('change', filterTasks);

        document.addEventListener('DOMContentLoaded', function() {
            const taskStatusElements = document.querySelectorAll('.TaskStatus');

            taskStatusElements.forEach(statusElement => {
                if (statusElement.textContent.toLowerCase() === 'completed') {
                    statusElement.style.color = 'green';
                } else if (statusElement.textContent.toLowerCase() === 'pending') {
                    statusElement.style.color = '#e20404';
                }
            });
        });
    </script>
</body>

</html>