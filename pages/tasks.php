<?php
session_start();
require_once '../includes/config.php';

// التحقق من تسجيل الدخول
if (!isLoggedIn()) {
    header("Location: ../login.php");
    exit;
}

// إضافة مهمة جديدة
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_task'])) {
    $title = cleanInput($_POST['title']);
    $description = cleanInput($_POST['description']);
    $priority = cleanInput($_POST['priority']);
    $assigned_to = cleanInput($_POST['assigned_to']);
    $due_date = cleanInput($_POST['due_date']);

    $sql = "INSERT INTO tasks (title, description, priority, assigned_to, created_by, due_date) 
            VALUES (:title, :description, :priority, :assigned_to, :created_by, :due_date)";
    
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':title' => $title,
            ':description' => $description,
            ':priority' => $priority,
            ':assigned_to' => $assigned_to,
            ':created_by' => $_SESSION['user_id'],
            ':due_date' => $due_date
        ]);
        $_SESSION['success'] = "تمت إضافة المهمة بنجاح";
    } catch(PDOException $e) {
        $_SESSION['error'] = "حدث خطأ أثناء إضافة المهمة";
    }
}

// جلب قائمة المستخدمين للقائمة المنسدلة
$users_sql = "SELECT id, full_name FROM users";
$users_stmt = $pdo->query($users_sql);
$users = $users_stmt->fetchAll(PDO::FETCH_ASSOC);

// جلب قائمة المهام
$tasks_sql = "SELECT t.*, u.full_name as assigned_name 
              FROM tasks t 
              LEFT JOIN users u ON t.assigned_to = u.id 
              ORDER BY t.created_at DESC";
$tasks_stmt = $pdo->query($tasks_sql);
$tasks = $tasks_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إدارة المهام</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <?php include '../includes/sidebar.php'; ?>

            <!-- Main content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">إدارة المهام</h1>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addTaskModal">
                        <i class="fas fa-plus"></i> إضافة مهمة جديدة
                    </button>
                </div>

                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?php 
                        echo $_SESSION['success'];
                        unset($_SESSION['success']);
                        ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?php 
                        echo $_SESSION['error'];
                        unset($_SESSION['error']);
                        ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <!-- جدول المهام -->
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>العنوان</th>
                                <th>الحالة</th>
                                <th>الأولوية</th>
                                <th>مسند إلى</th>
                                <th>تاريخ الاستحقاق</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($tasks as $task): ?>
                                <tr>
                                    <td><?php echo $task['id']; ?></td>
                                    <td><?php echo htmlspecialchars($task['title']); ?></td>
                                    <td>
                                        <span class="badge bg-<?php 
                                            echo $task['status'] == 'completed' ? 'success' : 
                                                ($task['status'] == 'in_progress' ? 'warning' : 'secondary');
                                        ?>">
                                            <?php 
                                            echo $task['status'] == 'completed' ? 'مكتمل' : 
                                                ($task['status'] == 'in_progress' ? 'قيد التنفيذ' : 'معلق');
                                            ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?php 
                                            echo $task['priority'] == 'high' ? 'danger' : 
                                                ($task['priority'] == 'medium' ? 'warning' : 'info');
                                        ?>">
                                            <?php 
                                            echo $task['priority'] == 'high' ? 'عالية' : 
                                                ($task['priority'] == 'medium' ? 'متوسطة' : 'منخفضة');
                                            ?>
                                        </span>
                                    </td>
                                    <td><?php echo htmlspecialchars($task['assigned_name']); ?></td>
                                    <td><?php echo $task['due_date']; ?></td>
                                    <td>
                                        <button class="btn btn-sm btn-info" title="عرض التفاصيل">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-warning" title="تعديل">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger" title="حذف">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </main>
        </div>
    </div>

    <!-- Modal إضافة مهمة جديدة -->
    <div class="modal fade" id="addTaskModal" tabindex="-1" aria-labelledby="addTaskModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addTaskModalLabel">إضافة مهمة جديدة</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="" method="POST">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="title" class="form-label">عنوان المهمة</label>
                            <input type="text" class="form-control" id="title" name="title" required>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">وصف المهمة</label>
                            <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="priority" class="form-label">الأولوية</label>
                            <select class="form-select" id="priority" name="priority" required>
                                <option value="low">منخفضة</option>
                                <option value="medium" selected>متوسطة</option>
                                <option value="high">عالية</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="assigned_to" class="form-label">تعيين إلى</label>
                            <select class="form-select" id="assigned_to" name="assigned_to" required>
                                <?php foreach ($users as $user): ?>
                                    <option value="<?php echo $user['id']; ?>">
                                        <?php echo htmlspecialchars($user['full_name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="due_date" class="form-label">تاريخ الاستحقاق</label>
                            <input type="date" class="form-control" id="due_date" name="due_date" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" name="add_task" class="btn btn-primary">إضافة المهمة</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/main.js"></script>
</body>
</html>
