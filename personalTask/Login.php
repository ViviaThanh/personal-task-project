<?php
require_once 'db.php';  
$has_error = false;
session_start();
$message = '';

try {
    $db = new DbHelper();
} catch (Exception $e) {
    die("Lỗi không thể kết nối CSDL: " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    $username_form = trim($_POST['username'] ?? '');
    $password_form = $_POST['password'] ?? '';

    if ($username_form === '' || $password_form === '') {
        $message = "Vui lòng nhập đầy đủ tên đăng nhập và mật khẩu!";
        $has_error = true;
    } else {

        $sql = "SELECT * FROM users WHERE username = ?";
        $params = [$username_form];
        $user = $db->select($sql, $params, false);

        if ($user && password_verify($password_form, $user->password)) {

            $_SESSION['user'] = [
                'id' => $user->id,
                'username' => $user->username,
                'email' => $user->email,
                'avatar' => $user->avatar
            ];

            header("Location:index.php"); 
            exit;
        } else {

            $message = "Sai tên đăng nhập hoặc mật khẩu!";
            $has_error = true;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng nhập - Personal Task</title>
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


        .outer-container {
            display: flex; 
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 25px rgba(0, 0, 0, 0.2);
            width: 800px; 
            max-width: 90%; 
            overflow: hidden; 
        }

        .image-section {
            flex: 1; 
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #f0f8ff;
            padding: 20px;
        }

        .image-section img {
            max-width: 100%;
            height: auto;
            border-radius: 8px; 
        }

        .form-section {
            flex: 1; 
            padding: 40px 30px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        #splash-screen {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background-color: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            z-index: 9999;
            opacity: 1;
            transition: opacity 2s ease-out;
        }

        #splash-screen h1 {
            font-size: 2.5rem;
            color: #0984e3;
            font-weight: 700;
        }

        h3 {
            color: #0984e3;
            font-weight: 600;
        }

        .form-label {
            font-weight: 500;
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

        p a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <div id="splash-screen">
        <h1>Personal Task</h1>
    </div>

    <div id="login-main-container" style="display: none;"> <div class="outer-container">
            <div class="image-section">
                <img src="managementTask.png" alt="Personal Task Illustration">
            </div>
            <div class="form-section">
                <h3 class="mb-4 text-center">Welcome Back!</h3>

                <?php if (isset($message) && $message): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($message) ?></div>
                <?php endif; ?>

                <form method="POST" action="login.php">
                    <div class="mb-3">
                        <label for="username" class="form-label"> Username</label>
                        <input type="text" class="form-control" id="username" name="username" required>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">Confirm</button>

                    <p class="text-center mt-3">
                        Don't have account? <a href="register.php">Register</a>
                    </p>
                </form>
            </div>
        </div>
    </div>
    <script src="js/bootstrap.min.js"></script>

    <script>
        window.addEventListener('load', function() {
            const splashScreen = document.getElementById('splash-screen');
            const loginMainContainer = document.getElementById('login-main-container'); // Lấy container mới

            <?php if ($has_error): ?>
                splashScreen.style.display = 'none';
                loginMainContainer.style.display = 'flex'; 
            <?php else: ?>
                setTimeout(function() {
                    splashScreen.style.opacity = '0';
                    setTimeout(function() {
                        splashScreen.style.display = 'none';
                        loginMainContainer.style.display = 'flex'; // Hiển thị container mới
                    }, 2000);
                }, 500);
            <?php endif; ?>
        });
    </script>
</body>
</html>

