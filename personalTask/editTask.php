<?php
session_start();

// 1. NẠP FILE
// Hãy chắc chắn đường dẫn 'db.php' là đúng (có thể là 'Config/db.php'?)
require_once 'db.php'; 
require_once 'Model/taskModel.php'; 
    
// 2. KIỂM TRA ĐĂNG NHẬP
if (!isset($_SESSION['user']) || !isset($_SESSION['user']['id'])) {
    header("Location: Login.php");
    exit;
}

$user_id = $_SESSION['user']['id'];

// 3. LẤY TASK ID TỪ URL (PHẢI LÀM TRƯỚC)
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Task không hợp lệ.");
}
$task_id = intval($_GET['id']);

// 4. KHỞI TẠO MODEL (ĐÂY LÀ DÒNG BẠN BỊ THIẾU)
$taskModel = new TaskModel(); 

// 5. LẤY DỮ LIỆU TASK TỪ CSDL
$task = $taskModel->getById($task_id);

// 6. KIỂM TRA TASK (Chỉ cần 1 lần kiểm tra)
if (empty($task) || $task->user_id != $user_id){
    die("Bạn không có quyền chỉnh sửa task này hoặc task không tồn tại.");
}


$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $due_date = trim($_POST['due_date']);
    $status = $_POST['status'];

    if ($title === '') {
        $errors[] = "Tiêu đề task không được để trống.";
    }

    $allowed_status = ['pending', 'in_progress', 'completed'];
    if (!in_array($status, $allowed_status)) {
        $errors[] = "Trạng thái không hợp lệ.";
    }

    if (empty($errors)) {
        // Biến $taskModel đã được tạo ở bước 4, nên ở đây dùng được
        $taskModel->update($task_id, $title, $description, $due_date ?: null, $status);
        header("Location: index.php"); 
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sửa Task</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    
    <style>
        body {
            background-color: #f4f7f6; /* Thêm màu nền giống trang chủ */
        }
        .edit-form-container {
            max-width: 600px;
            margin: 40px auto; /* Căn giữa và tạo khoảng cách */
            padding: 30px;
            background-color: #ffffff;
            border-radius: 16px; /* Bo góc mượt */
            box-shadow: 0 10px 25px rgba(0,0,0,0.1); /* Thêm bóng đổ */
        }
        .edit-form-container h2 {
            text-align: center;
            margin-bottom: 25px;
            color: #2c3e50;
        }
        .btn-primary {
            width: 100%; /* Kéo dài nút */
            padding: 10px;
            font-weight: bold;
        }
        .btn-secondary {
            width: 100%; /* Kéo dài nút */
            padding: 10px;
            margin-top: 10px; /* Tạo khoảng cách 2 nút */
        }
    </style>
    </head>
<body>

<div class="container">
    <div class="edit-form-container">
        <h2>Chỉnh sửa Task</h2>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <ul>
                    <?php foreach ($errors as $err): ?>
                        <li><?php echo htmlspecialchars($err); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label for="title" class="form-label">Tiêu đề</label>
                <input type="text" class="form-control" id="title" name="title" 
                       value="<?php echo htmlspecialchars($task->title); ?>" required>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Mô tả</label>
               <textarea class="form-control" id="description" name="description" rows="3"><?php echo htmlspecialchars($task->description); ?></textarea>
            </div>

            <div class="mb-3">
                <label for="due_date" class="form-label">Hạn hoàn thành</label>
                <input type="date" class="form-control" id="due_date" name="due_date" 
                       value="<?php echo htmlspecialchars($task->due_date); ?>" required>
            </div>

            <div class="mb-3">
                <label for="status" class="form-label">Trạng thái</label>
                <select name="status" id="status" class="form-control">
                       <option value="pending" <?php echo $task->status=='pending' ? 'selected' : ''; ?>>Đang chờ</option>
                    <option value="in_progress" <?php echo $task->status=='in_progress' ? 'selected' : ''; ?>>Đang tiến hành</option>
                    <option value="completed" <?php echo $task->status=='completed' ? 'selected' : ''; ?>>Hoàn thành</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Cập nhật Task</button>
            <a href="index.php" class="btn btn-secondary">Hủy</a>
        </form>
    </div>
</div>
</body>
</html>