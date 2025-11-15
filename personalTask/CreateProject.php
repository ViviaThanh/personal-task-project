<?php
session_start();
if (!isset($_SESSION['user']) || !isset($_SESSION['user']['id'])) {
    header("Location: Login.php");
    exit;
}

$user_id = $_SESSION['user']['id'];
$db_host = 'localhost';
$db_name = 'taskproject';
$db_user = 'root';
$db_pass = '';

$message = '';
$error = '';

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8mb4", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');

        if ($name == '') {
            $error = "Tên Project không được để trống!";
        } else {
            $stmt = $pdo->prepare("INSERT INTO projects (name, description, user_id) VALUES (?, ?, ?)");
            $stmt->execute([$name, $description, $user_id]);

            header("Location: index.php?message=project_created");
            exit;
        }
    }

} catch (PDOException $e) {
    die("Database Error: " . $e->getMessage());
}

?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Tạo Project Mới</title>

<style>
    body { font-family: Arial, sans-serif; background: #f4f7f6; display:flex; justify-content:center; align-items:center; height:100vh; }
    .container { background:#fff; padding:30px; border-radius:10px; width:450px; box-shadow:0 5px 10px rgba(0,0,0,0.1); }
    h2 { text-align:center; margin-bottom:20px; color:#2c3e50; }
    .form-group { margin-bottom:15px; }
    label { font-weight:bold; }
    input, textarea { width:100%; padding:10px; border:1px solid #ccc; border-radius:5px; margin-top:5px; }
    button { width:100%; padding:12px; background:#8e44ad; color:#fff; border:none; border-radius:5px; font-size:16px; cursor:pointer; }
    button:hover { background:#732d91; }
    .back-btn { text-decoration:none; display:block; text-align:center; margin-top:15px; color:#34495e; font-weight:bold; }
    .error { color:#c0392b; text-align:center; margin-bottom:10px; }
</style>
</head>

<body>
<div class="container">
    <h2>Tạo Project Mới</h2>

    <?php if ($error): ?>
        <p class="error"><?php echo $error; ?></p>
    <?php endif; ?>

    <form method="POST">
        <div class="form-group">
            <label>Tên Project:</label>
            <input type="text" name="name" placeholder="Nhập tên project" required>
        </div>

        <div class="form-group">
            <label>Mô tả:</label>
            <textarea name="description" rows="4" placeholder="Mô tả project (không bắt buộc)"></textarea>
        </div>

        <button type="submit">Tạo Project</button>
        <a href="index.php" class="back-btn">← Quay lại Dashboard</a>
    </form>
</div>
</body>
</html>
