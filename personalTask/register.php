<?php
require_once 'db.php'; 

session_start();
$message = '';
$message_type = 'danger';

try {
    $db = new DbHelper();
} catch (Exception $e) {
    die("Lỗi không thể kết nối CSDL: " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    $username_form = trim($_POST['username'] ?? '');
    $email_form = trim($_POST['email'] ?? '');
    $password_form = $_POST['password'] ?? '';
    $password_confirm_form = $_POST['password_confirm'] ?? '';

    if ($username_form === '' || $email_form === '' || $password_form === '' || $password_confirm_form === '') {
        $message = "Vui lòng nhập đầy đủ thông tin!";
    } 
    else if (!filter_var($email_form, FILTER_VALIDATE_EMAIL)) {
        $message = "Email không đúng định dạng!";
    }
    else if ($password_form !== $password_confirm_form) {
        $message = "Mật khẩu xác nhận không khớp!";
    } 
    else if (strlen($password_form) < 6) {
        $message = "Mật khẩu phải có ít nhất 6 ký tự.";
    }
    else {
        $sql_check = "SELECT id FROM users WHERE username = ? OR email = ?";
        $params_check = [$username_form, $email_form];
        $existing_user = $db->select($sql_check, $params_check, false);

        if ($existing_user) {
            $message = "Tên đăng nhập hoặc Email này đã tồn tại!";
        } else {
            $hashed_password = password_hash($password_form, PASSWORD_DEFAULT);

            $sql_insert = "INSERT INTO users (username, password, email) VALUES (?, ?, ?)";
            $params_insert = [$username_form, $hashed_password, $email_form];

            try {
                $new_user_id = $db->insert($sql_insert, $params_insert);
                if ($new_user_id > 0) {
                    
                    $_SESSION['success_message'] = 'Đăng ký tài khoản thành công! Vui lòng đăng nhập.';

                    header("Location: Login.php");
                    exit();
                }
                else {
                    $message = "Có lỗi xảy ra trong quá trình đăng ký.";
                }
            } catch (Exception $e) {
                $message = "Lỗi CSDL: " . $e->getMessage();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng ký - Personal Task</title>
    <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">

    <style>
        body {
            background: linear-gradient(to bottom, #4169E1, #8A2BE2);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: "Times New Roman", sans-serif;
        }

        #register-form-container {
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 25px rgba(0, 0, 0, 0.2);
            padding: 40px 30px;
            width: 420px;
        }

        h3 {
            color: #0984e3;
            font-weight: 600;
        }

        .btn-primary {
            background-color: #0984e3;
            border: none;
        }

        .btn-primary:hover {
            background-color: #74b9ff;
        }

        p a {
            color: #0984e3;
            text-decoration: none;
        }
    </style>
</head>

<body>

    <div id="register-form-container">
        <h3 class="mb-4 text-center">Create Account</h3>

        <?php if ($message): ?>
            <div class="alert alert-<?= htmlspecialchars($message_type) ?>">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="register.php">
            <div class="mb-3">
                <label for="username" class="form-label"> Username</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>

            <div class="mb-3">
                <label for="password_confirm" class="form-label"> Confirm Password</label>
                <input type="password" class="form-control" id="password_confirm" name="password_confirm" required>
            </div>

            <button type="submit" class="btn btn-primary w-100">Confirm</button>

            <p class="text-center mt-3">
                Already have an account? <a href="login.php">Login</a>
            </p>
        </form>
    </div>

</body>
</html>
