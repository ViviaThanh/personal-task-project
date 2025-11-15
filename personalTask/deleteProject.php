<?php
session_start();

if (!isset($_SESSION['user']) || !isset($_SESSION['user']['id'])) {
    header("Location: Login.php"); 
    exit;
}


if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("ID Project không hợp lệ.");
}

$user_id = $_SESSION['user']['id'];
$project_id = (int)$_GET['id'];


$db_host = 'localhost';
$db_name = 'taskproject';
$db_user = 'root';
$db_pass = '';

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8mb4", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $pdo->beginTransaction();

    $stmt_tasks = $pdo->prepare("DELETE FROM tasks WHERE project_id = ? AND user_id = ?");
    $stmt_tasks->execute([$project_id, $user_id]);


    $stmt_project = $pdo->prepare("DELETE FROM projects WHERE id = ? AND user_id = ?");
    $stmt_project->execute([$project_id, $user_id]);

    $pdo->commit();

    header("Location: index.php"); 
    exit;

} catch (PDOException $e) {

    $pdo->rollBack(); 
    die("Lỗi CSDL khi xóa: " . $e->getMessage());
}
?>