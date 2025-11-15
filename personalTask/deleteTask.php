<?php
session_start();

if (!isset($_SESSION['user']['id'])) {
    header("Location: Login.php");
    exit;
}

if (!isset($_POST['id'])) {
    die("Task ID không hợp lệ");
}
$task_id = (int)$_POST['id'];

$user_id = $_SESSION['user']['id'];

$db_host = 'localhost';
$db_name = 'taskproject';
$db_user = 'root';
$db_pass = '';

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8mb4", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Chỉ xóa task của chính user
    $stmt = $pdo->prepare("DELETE FROM tasks WHERE id = ? AND user_id = ?");
    $stmt->execute([$task_id, $user_id]);

    // ▼▼▼ SỬA 2: Quay lại "index.php" (file bạn đang ở) thay vì "dashboard.php" ▼▼▼
    header("Location: index.php"); 
    exit;
} catch (PDOException $e) {
    die("Lỗi CSDL: " . $e->getMessage());
}
?>