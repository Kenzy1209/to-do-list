<?php
$host = 'localhost'; // Database host
$dbname = 'todo_list'; // Database name
$username = 'root'; // Database username
$password = ''; // Database password

// Create PDO instance
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Handle CRUD operations
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add'])) {
        // Add Task
        $title = $_POST['title'];
        $content = $_POST['content'];
        $due_date = $_POST['due_date'];
        $stmt = $pdo->prepare("INSERT INTO tasks (title, content, due_date) VALUES (?, ?, ?)");
        $stmt->execute([$title, $content, $due_date]);
    } elseif (isset($_POST['edit'])) {
        // Edit Task
        $id = $_POST['id'];
        $title = $_POST['title'];
        $content = $_POST['content'];
        $due_date = $_POST['due_date'];
        $stmt = $pdo->prepare("UPDATE tasks SET title = ?, content = ?, due_date = ? WHERE id = ?");
        $stmt->execute([$title, $content, $due_date, $id]);
    } elseif (isset($_POST['delete'])) {
        // Delete Task
        $id = $_POST['id'];
        $stmt = $pdo->prepare("DELETE FROM tasks WHERE id = ?");
        $stmt->execute([$id]);
    }
}

// Search functionality
$searchQuery = '';
if (isset($_GET['search'])) {
    $searchQuery = $_GET['search'];
    $stmt = $pdo->prepare("SELECT * FROM tasks WHERE title LIKE ? OR content LIKE ?");
    $stmt->execute(["%$searchQuery%", "%$searchQuery%"]);
} else {
    $stmt = $pdo->query("SELECT * FROM tasks");
}

$tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP CRUD with Search</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <h1 class="text-center mb-4">To-Do List</h1>

        <!-- Search Form -->
        <form method="get" class="mb-4">
            <input type="text" name="search" class="form-control" placeholder="Search tasks" value="<?= htmlspecialchars($searchQuery) ?>">
        </form>

        <!-- Task Form -->
        <form method="post" class="mb-4">
            <input type="hidden" name="id" id="taskId">
            <input type="text" name="title" id="taskTitle" class="form-control mb-2" placeholder="Task Title" required>
            <textarea name="content" id="taskContent" class="form-control mb-2" placeholder="Task Content" required></textarea>
            <input type="date" name="due_date" id="taskDate" class="form-control mb-2" required>
            <button type="submit" name="add" class="btn btn-primary">Add Task</button>
        </form>

        <!-- Task List -->
        <h2 class="text-center mb-3">Tasks</h2>
        <ul class="list-group">
            <?php foreach ($tasks as $task): ?>
                <li class="list-group-item d-flex justify-content-between align-items-start">
                    <div>
                        <h5><?= htmlspecialchars($task['title']) ?></h5>
                        <p><?= htmlspecialchars($task['content']) ?></p>
                        <p class="text-muted">Due: <?= $task['due_date'] ?></p>
                    </div>
                    <div>
                        <!-- Edit Button -->
                        <button class="btn btn-warning btn-sm" onclick="editTask(<?= $task['id'] ?>, '<?= addslashes($task['title']) ?>', '<?= addslashes($task['content']) ?>', '<?= $task['due_date'] ?>')">Edit</button>

                        <!-- Delete Button -->
                        <form method="post" style="display: inline;">
                            <input type="hidden" name="id" value="<?= $task['id'] ?>">
                            <button type="submit" name="delete" class="btn btn-danger btn-sm">Delete</button>
                        </form>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>

    <script>
        // Edit Task Function
        function editTask(id, title, content, due_date) {
            document.getElementById('taskId').value = id;
            document.getElementById('taskTitle').value = title;
            document.getElementById('taskContent').value = content;
            document.getElementById('taskDate').value = due_date;
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
