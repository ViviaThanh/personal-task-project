<?php
session_start();
if (!isset($_SESSION['user']) || !isset($_SESSION['user']['id'])) {
    header("Location: Login.php"); 
    exit;
}
$user_id_can_lay = $_SESSION['user']['id'];
$username_hien_thi = $_SESSION['user']['username'];
$db_host = 'localhost';
$db_name = 'taskproject';
$db_user = 'root';
$db_pass = '';

$projects = [];
$tasks = [];

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8mb4", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


    $stmt_projects = $pdo->prepare("SELECT id, name, description FROM projects WHERE user_id = ?");
    $stmt_projects->execute([$user_id_can_lay]);
    $projects = $stmt_projects->fetchAll(PDO::FETCH_ASSOC);


    $sort_order = isset($_GET['sort']) && $_GET['sort'] == 'desc' ? 'DESC' : 'ASC';
    $status_filter = isset($_GET['status']) ? $_GET['status'] : '';

    $sql_tasks = "SELECT id, project_id, title, description, due_date, status FROM tasks WHERE user_id = ?";
    $params = [$user_id_can_lay];

if ($status_filter && ($status_filter == 'pending' || $status_filter == 'completed' || $status_filter == 'in_progress')) {
        $sql_tasks .= " AND status = ?";
        $params[] = $status_filter;
    }

    $sql_tasks .= " ORDER BY due_date $sort_order";
    $stmt_tasks = $pdo->prepare($sql_tasks);
    $stmt_tasks->execute($params);
    $all_tasks = $stmt_tasks->fetchAll(PDO::FETCH_ASSOC);

    $tasks_by_project = [];
    foreach ($all_tasks as $task) {
        $tasks_by_project[$task['project_id']][] = $task;
    }

} catch (PDOException $e) {
    die("Lỗi CSDL: " . $e->getMessage());
}


$pdo = null;
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Quản Lý Công Việc</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f7f6; color: #333; margin: 0; padding: 20px; }
        header { text-align: center; margin-bottom: 30px; }
        h1, h2, h3 { color: #2c3e50; }
        .project-container { display: grid; grid-template-columns: repeat(auto-fit, minmax(350px, 1fr)); gap: 20px; max-width: 1400px; margin: 0 auto; }
       .project-card {
        background: linear-gradient(135deg, #5A5CF0, #8A2BE2);
        border-radius: 18px;
        padding: 22px;
        color: white;
        border: 2px solid rgba(255,255,255,0.15);
        padding-bottom: 80px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.12);
        transition: all 0.35s ease;
        cursor: pointer;
        position: relative;
        overflow: hidden;
        }
        .project-card::before {
            content: "";
            position: absolute;
            top: -120%;
            left: -120%;
            width: 250%;
            height: 250%;
            background: radial-gradient(
                circle at center,
                rgba(255,255,255,0.25),
                transparent 70%
            );
            transform: rotate(25deg);
            transition: 0.6s;
            opacity: 0;
        }
        .user-menu-btn {
            background-color: #2254abff; 
            color: white !important;
            border: none;
            font-weight: bold;
            padding: 10px 15px;
            border-radius: 50px; 
            display: flex; 
            align-items: center;
            gap: 8px; 
        }

        .user-menu-btn:hover, .user-menu-btn:focus {
            background-color: #5412b0ff; 
            color: white !important;
        }


        .user-menu-btn.dropdown-toggle::after {
            margin-left: 0.5em;
        }

        .dropdown-menu .bi {
            margin-right: 10px;
            width: 20px; 
        }
        .project-card:hover::before {
            top: -40%;
            left: -40%;
            opacity: 1;
        }

                .project-card:hover {
                transform: translateY(-7px) scale(1.02);
                box-shadow: 0 18px 35px rgba(0,0,0,0.25);
                border-color: rgba(255,255,255,0.35);
                }

                .project-card h3 {
            margin: 0;
            font-size: 22px;
            font-weight: bold;
            color: #fff;
        }

        .project-card p {
            margin-top: 8px;
            font-size: 15px;
            color: #f0f0f0;
        }

        .card-footer { margin-top: 20px; border-top: 1px solid #eee; padding-top: 15px; text-align: right; }
        .add-task-btn {
            width: 40px;            
            height: 40px;           
            display: inline-flex;     
            align-items: center;     
            justify-content: center;   
            border-radius: 50%;        
            background-color: #27ae60; 
            color: white;
            text-decoration: none;
            font-size: 20px;
            font-weight: bold;
            transition: background-color 0.3s;
        }

        .add-task-btn:hover {
            background-color: #1e8449;
        }

        .task-list {
            list-style: none;
            padding: 0;
            margin-top: 20px;
        }
        .task-item {
            background-color: #f9f9f9;
            border: 1px solid #eaeaea;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 10px;
        }

        .task-item.status-completed {
            background-color: #e8f5e9;
            text-decoration: line-through; 
            opacity: 0.7;
        }
        .task-item strong {
            display: block;
            font-size: 1.1em;
            color: #333;
        }
        .task-item .task-meta {
            font-size: 0.9em;
            color: #777;
            margin: 5px 0;
        }
        .task-actions {
            margin-top: 10px;
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            align-items: center;
        }
        .task-actions a,
        .task-actions button { 
            text-decoration: none;
            padding: 5px 10px;
            border-radius: 6px; 
            font-size: 0.9em;
            color: #fff;
            border: none; 
        }
        .modal-overlay {
            display: none; 
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.6); 
            align-items: center;
            justify-content: center;
        }

        .modal-content {

            background-color: #fefefe;
            margin: auto;
            padding: 25px;
            border: 1px solid #888;
            border-radius: 8px;
            width: 80%;
            max-width: 700px; 
            position: relative;
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
            max-height: 85vh; 
            overflow-y: auto;
            }

        .modal-close {
            color: #aaa;
            position: absolute;
            top: 10px;
            right: 20px;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }
        .modal-close:hover,
        .modal-close:focus {
            color: black;
            text-decoration: none;
        }

        .action-edit { background-color: #3a2bc0ff; } 
        .action-delete { background-color: #3a2bc0ff; } 
        
        .page-controls {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1400px;
            margin: 0 auto 20px auto;
            flex-wrap: wrap;
            gap: 15px;
        }
        .add-task-btn {
        position: absolute; 
        bottom: 22px;       
        right: 22px;       
        z-index: 10;        
        cursor: pointer;
        background-color: #27ae60;
        font-size: 20px !important;
        font-weight: bold;
        padding: 8px 14px !important;
        }
        .add-task-btn:hover {
            background-color: #1e8449;
        }
        .filter-form { display: flex; gap: 10px; align-items: center; }
        .filter-form select, .filter-form button { padding: 8px; border-radius: 5px; border: 1px solid #ccc; }
        .filter-form button { background-color: #34495e; color: white; cursor: pointer;display: none; }
        .logout-btn { background-color: #e74c3c; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px; 
        }
        .add-project-btn {
        background-color: #8e44ad;
        color: white;
        padding: 10px 15px;
        text-decoration: none;
        font-weight: bold;
        border-radius: 50px;
        transition: 0.3s;
        }
        .add-project-btn:hover {
            background-color: #732d91;
        }
        .project-delete-btn {
            position: absolute;
            top: 15px;  
            right: 20px; 
            z-index: 20; 
            color: rgba(16, 224, 117, 0.7); 
            text-decoration: none;
            font-size: 1.2rem; 
            line-height: 1;

            transition: all 0.2s ease;
        }

        .project-delete-btn:hover {
            color: #fff; 
            transform: scale(1.1);
        }

        .project-progress {
            position: absolute;
            bottom: 22px; 
            left: 22px;   
            z-index: 10;
            display: flex; 
            align-items: center;
            gap: 10px; 
            cursor: default; 
        }

        /* 2. Vòng tròn tiến độ */
        .progress-circle {
            width: 50px; 
            height: 50px;
            border-radius: 50%;
            background-image: conic-gradient(
                rgba(255, 255, 255, 0.9) var(--progress-percent), 
                rgba(255, 255, 255, 0.2) var(--progress-percent) 100%
            );
            -webkit-mask-image: radial-gradient(transparent 65%, black 66%);
            mask-image: radial-gradient(transparent 65%, black 66%);
        }


        .progress-text {
            font-size: 13px;
            color: #f0f0f0; 
            font-weight: 500;
        }
        .progress-text strong {
            font-size: 15px;
            font-weight: bold;
            display: block; 
            color: #fff;
        }
        .project-card {

            position: relative;        
        }
        .action-delete-icon {
            background-color: transparent !important; 
            color: #e71134ff; 
            padding: 5px;
            font-size: 1.2rem; 
            margin-left: auto; 
            text-decoration: none;
            line-height: 1;
        }
        .action-delete-icon:hover {
            color: #e71134ff; 
        }
        

    </style>
</head>
<body>
    <header>
        <h1> TO DO MANAGEMENT </h1>
    </header>
    <main>
        <div class="page-controls">
            <form method="GET" class="filter-form">
                <label for="status">Lọc theo trạng thái:</label>

                <select name="status" id="status" onchange="this.form.submit()">
                    <option value="">Tất cả</option>
                    <option value="pending" <?php echo ($status_filter == 'pending' ? 'selected' : ''); ?>>Đang chờ</option>

                    <option value="in_progress" <?php echo ($status_filter == 'in_progress' ? 'selected' : ''); ?>>Đang tiến hành</option>

                    <option value="completed" <?php echo ($status_filter == 'completed' ? 'selected' : ''); ?>>Đã hoàn thành</option>
                </select>

                <label for="sort">Sắp xếp theo ngày:</label>

                <select name="sort" id="sort" onchange="this.form.submit()">
                    <option value="asc" <?php echo ($sort_order == 'ASC' ? 'selected' : ''); ?>>Tăng dần</option>
                    <option value="desc" <?php echo ($sort_order == 'DESC' ? 'selected' : ''); ?>>Giảm dần</option>
                </select>

                <button type="submit">Lọc/Sắp xếp</button> 

                <a href="CreateProject.php" class="add-project-btn">+ Add Project</a>
            </form>
            <div class="dropdown">
    <button class="btn user-menu-btn dropdown-toggle" type="button" id="userMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
        <?php 
            $avatar_path = $_SESSION['user']['avatar'] ?? 'path/to/default-avatar.png'; 
        ?>
        <img src="<?php echo htmlspecialchars($avatar_path); ?>" 
             alt="Avatar" 
             style="width: 24px; height: 24px; border-radius: 50%; object-fit: cover; margin-right: 5px;">
        <?php echo htmlspecialchars($_SESSION['user']['username'] ?? 'Tài khoản'); ?>
    </button>

    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userMenuButton">
        <li>
            <a class="dropdown-item" href="setting.php">
                <i class="bi bi-gear"></i>
                Setting 
            </a>
        </li>
        <li><hr class="dropdown-divider"></li>
        <li>
            <a class="dropdown-item" href="logout.php">
                <i class="bi bi-box-arrow-right"></i>
                Log Out 
            </a>
        </li>
    </ul>
</div>

        <div class="project-container">
    <?php if (empty($projects)): ?>
        <p>Bạn chưa có dự án nào. Hãy tạo một dự án mới!</p>
    <?php else: ?>
        <?php foreach ($projects as $project): ?>

            <div class="project-card" onclick="openProjectModal(this)">
                <a href="deleteProject.php?id=<?php echo $project['id']; ?>" 
               class="project-delete-btn" 
               onclick="event.stopPropagation(); return confirm('Bạn có chắc muốn XÓA TOÀN BỘ dự án này và tất cả task bên trong?');">
                <i class="bi bi-x-lg"></i> </a>
                <?php 
                $completed_count = 0;
                $total_count = 0; 

                if (!empty($tasks_by_project[$project['id']])) {

                $total_count = count($tasks_by_project[$project['id']]);


                foreach ($tasks_by_project[$project['id']] as $task) {
                if ($task['status'] == 'completed') $completed_count++;
                }
                }
                ?>

                    <?php 
                    $completed_count = 0;
                    $total_count = 0; 
                    $percentage = 0; 

                    if (!empty($tasks_by_project[$project['id']])) {
                    $total_count = count($tasks_by_project[$project['id']]);

                    foreach ($tasks_by_project[$project['id']] as $task) {
                    if ($task['status'] == 'completed') $completed_count++;
                    }

                    if ($total_count > 0) {
                    $percentage = ($completed_count / $total_count) * 100;
                    }
                    }
                    ?>
                    <div class="project-progress" onclick="event.stopPropagation();">
                    <div class="progress-circle" style="--progress-percent: <?php echo $percentage; ?>%;"></div>
                    <div class="progress-text">
                    <strong><?php echo $completed_count; ?>/<?php echo $total_count; ?></strong>
                    tasks hoàn thành
                    </div>
                    </div>               
                <h3><?php echo htmlspecialchars($project['name']); ?></h3>
                <p><?php echo htmlspecialchars($project['description']); ?></p>
                <ul class="task-list">
                <?php if (!empty($tasks_by_project[$project['id']])): ?>
                    <?php foreach ($tasks_by_project[$project['id']] as $task): ?>
                        <li class="task-item <?php echo ($task['status'] == 'completed' ? 'status-completed' : ''); ?>">
                            <strong><?php echo htmlspecialchars($task['title']); ?></strong>
                            <div class="task-meta">
                                Hạn: <?php echo htmlspecialchars($task['due_date']); ?> |
                                Trạng thái: <?php echo htmlspecialchars($task['status']); ?>
                            </div>

                            <div class="task-actions">
                                <a class="action-edit" href="editTask.php?id=<?php echo $task['id']; ?>">Update</a>
                                <form method="POST" action="deleteTask.php" style="margin-left: auto;" onsubmit="return confirm('Bạn chắc muốn xóa task này?');">
                                    <input type="hidden" name="id" value="<?php echo $task['id']; ?>">
                                    <button type="submit" class="action-delete">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                                
                            </div>
                        </li>
                    <?php endforeach; ?>
                <?php else: ?>
        <p>Chưa có task nào.</p>
    <?php endif; ?>
    </ul>
<div class="card-footer">
    <a href="CreateTask.php?project_id=<?php echo $project['id']; ?>" class="add-task-btn"> + </a>
</div>

            </div>

        <?php endforeach; ?>
    <?php endif; ?>
</div>

    </main>
    <div id="projectModal" class="modal-overlay">
        <div class="modal-content">
            <span class="modal-close" onclick="closeProjectModal()">&times;</span>
            
            <div id="modal-project-content">
                </div>
            
        </div>
    </div>

    <script>

        var modal = document.getElementById("projectModal");
        var modalContent = document.getElementById("modal-project-content");


        function openProjectModal(cardElement) {
            var projectName = cardElement.querySelector('h3').outerHTML;
            var projectDesc = cardElement.querySelector('p').outerHTML;
            var taskList = cardElement.querySelector('.task-list').outerHTML;
            var cardFooter = cardElement.querySelector('.card-footer').outerHTML;
            modalContent.innerHTML = projectName + projectDesc + taskList + cardFooter;
            modal.style.display = "flex";
        }
        function closeProjectModal() {
            modal.style.display = "none";
            modalContent.innerHTML = ""; 
        }
        window.onclick = function(event) {
            if (event.target == modal) {
                closeProjectModal();
            }
        }
    </script>
    </body>
</html>
