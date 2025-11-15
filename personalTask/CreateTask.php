<?php
session_start();
require_once 'Model/taskModel.php';
require_once 'Model/projectModel.php';

if (!isset($_SESSION['user'])) {
    header("Location: Login.php");
    exit;
}


$user_id = $_SESSION['user']['id'];
$username = $_SESSION['user']['username'];

$taskModel = new TaskModel();
$projectModel = new ProjectModel();
$message = "";
$error = "";


$project_id = $_GET['project_id'] ?? 0;
if ($project_id == 0) {
    die("Thiếu project_id hoặc project không tồn tại.");
}

$project = $projectModel->getById($project_id);

if (!$project) {
    die("Project không tồn tại!");
}

$project_name = $project->name;





if ($_SERVER['REQUEST_METHOD'] === "POST") {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $due_date = trim($_POST['due_date'] ?? '');

    if ($title === '') {
        $error = "Vui lòng nhập tiêu đề task!";
    } else {
        $newTaskId = $taskModel->insert($user_id, $project_id, $title, $description, $due_date);

        if ($newTaskId) {
            header("Location: index.php?message=task_created");
            exit;
        } else {
            $error = "Lỗi khi thêm task, vui lòng thử lại.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tạo Task Mới</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f6f7; padding: 30px; }
        .container { max-width: 600px; margin: auto; background: white; padding: 25px; border-radius: 8px;
                     box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
        h2 { margin-top: 0; color: #2c3e50; }
        label { display: block; margin-top: 15px; font-weight: bold; }
        input, textarea { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px; margin-top: 5px; }
        button { margin-top: 20px; padding: 12px; width: 100%; background-color: #27ae60; color: white; border: none; border-radius: 5px; font-size: 16px; cursor: pointer; }
        button:hover { background-color: #1e8449; }
        .back-link { display: block; margin-top: 15px; text-align: center; }
        .error { color: red; margin-top: 10px; }
    </style>
</head>
<body>
<div class="container">
    <h2>Thêm Task mới cho: <span style="color:#8e44ad"><?php echo htmlspecialchars($project_name); ?></span></h2>

    <?php if ($error): ?>
        <p class="error"><?php echo $error; ?></p>
    <?php endif; ?>

    <form method="POST">
        <input type="hidden" name="project_id" value="<?php echo $project_id; ?>">

        <label>Tiêu đề Task:</label>
        <input type="text" name="title" required>

        <label>Mô tả:</label>
        <textarea name="description" rows="4"></textarea>

        <label>Ngày hết hạn:</label>
        <input type="date" name="due_date">

        <button type="submit">Tạo Task</button>
    </form>

    <a class="back-link" href="index.php">← Quay lại Dashboard</a>
</div>
</body>
</html>
