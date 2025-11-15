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

$errors = [];
$success_message = "";

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8mb4", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 2. XỬ LÝ KHI USER SUBMIT FORM (POST)
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        
        $username = trim($_POST['username']);
        $email = trim($_POST['email']);
        $current_avatar = $_POST['current_avatar']; // Lấy avatar hiện tại từ trường ẩn
        $new_avatar_path = $current_avatar; // Mặc định là avatar cũ
        
        // --- A. XỬ LÝ UPLOAD ẢNH (Nếu có) ---
        if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] == 0) {
            
            $upload_dir = 'uploads/avatars/';
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
            $max_size = 5 * 1024 * 1024; // 5MB

            $file_type = $_FILES['avatar']['type'];
            $file_size = $_FILES['avatar']['size'];
            
            if (!in_array($file_type, $allowed_types)) {
                $errors[] = "Chỉ cho phép file JPG, PNG, GIF.";
            } elseif ($file_size > $max_size) {
                $errors[] = "File quá lớn. Tối đa 5MB.";
            } else {
                // Tạo tên file duy nhất (để tránh trùng lặp)
                $file_extension = pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION);
                $new_filename = 'user_' . $user_id . '_' . uniqid() . '.' . $file_extension;
                $target_file = $upload_dir . $new_filename;

                if (move_uploaded_file($_FILES['avatar']['tmp_name'], $target_file)) {
                    $new_avatar_path = $target_file; 

                    if ($current_avatar && $current_avatar != 'path/to/default/avatar.png') {
                         @unlink($current_avatar); 
                    }
                } else {
                    $errors[] = "Lỗi khi upload file.";
                }
            }
        }
        

        if (empty($errors)) {
            $sql = "UPDATE users SET username = ?, email = ?, avatar = ? WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$username, $email, $new_avatar_path, $user_id]);
            
            $_SESSION['user']['username'] = $username;
            $_SESSION['user']['avatar'] = $new_avatar_path;
            
            $success_message = "Cập nhật thông tin thành công!";
        }
    }

    // 3. LẤY THÔNG TIN USER HIỆN TẠI (GET)
    $stmt = $pdo->prepare("SELECT username, email, avatar FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Nếu trong session chưa có avatar thì lấy từ DB
    if (!isset($_SESSION['user']['avatar'])) {
        $_SESSION['user']['avatar'] = $user['avatar'];
    }

} catch (PDOException $e) {
    $errors[] = "Lỗi CSDL: " . $e->getMessage();
}
$pdo = null;
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cài đặt tài khoản</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    
    <style>
        body {
            background-color: #f4f7f6;
        }
        .settings-container {
            max-width: 600px;
            margin: 40px auto;
            padding: 30px;
            background-color: #ffffff;
            border-radius: 16px; 
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        .settings-container h2 {
            text-align: center;
            margin-bottom: 25px;
            color: #2c3e50;
        }
        .avatar-preview {
            width: 120px;
            height: 120px;
            border-radius: 50%; 
            object-fit: cover; 
            border: 4px solid #eee;
            margin-bottom: 15px;
            display: block;
            margin-left: auto;
            margin-right: auto;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="settings-container">
        <h2>Cài đặt tài khoản</h2>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <?php foreach ($errors as $err): ?>
                    <p><?php echo htmlspecialchars($err); ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
        <?php if ($success_message): ?>
            <div class="alert alert-success">
                <?php echo htmlspecialchars($success_message); ?>
            </div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">

            <div class="mb-3 text-center">
                <label for="avatar" class="form-label">Avatar</label>
                <div>
                    <img src="<?php echo htmlspecialchars($user['avatar'] ?? 'path/to/default/avatar.png'); ?>" 
                         alt="Avatar" class="avatar-preview" id="avatarPreview">
                </div>
                <input type="file" class="form-control" id="avatar" name="avatar" onchange="previewImage(event)">
                <input type="hidden" name="current_avatar" value="<?php echo htmlspecialchars($user['avatar'] ?? ''); ?>">
            </div>

            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" id="username" name="username" 
                       value="<?php echo htmlspecialchars($user['username'] ?? ''); ?>" required>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" 
                       value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" required>
            </div>

            <button type="submit" class="btn btn-primary w-100">Save</button>
            <a href="index.php" class="btn btn-secondary w-100 mt-2">Back</a>
        </form>
    </div>
</div>

<script>
    function previewImage(event) {
        var reader = new FileReader();
        reader.onload = function(){
            var output = document.getElementById('avatarPreview');
            output.src = reader.result;
        };
        reader.readAsDataURL(event.target.files[0]);
    }
</script>

</body>
</html>