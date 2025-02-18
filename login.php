<?php
session_start();
require_once 'includes/config.php';

// التحقق إذا كان المستخدم مسجل الدخول بالفعل
if(isset($_SESSION["user_id"])){
    header("location: index.php");
    exit;
}

// معالجة نموذج تسجيل الدخول
if($_SERVER["REQUEST_METHOD"] == "POST"){
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    
    // طباعة البيانات للتحقق
    echo "<pre>POST: "; print_r($_POST); echo "</pre>";
    $username = cleanInput($_POST["username"]);
    $password = $_POST["password"];
    
    $sql = "SELECT id, username, password, role FROM users WHERE username = :username";
    
    if($stmt = $pdo->prepare($sql)){
        $stmt->bindParam(":username", $username, PDO::PARAM_STR);
        
        if($stmt->execute()){
            if($stmt->rowCount() == 1){
                if($row = $stmt->fetch()){
                    $id = $row["id"];
                    $username = $row["username"];
                    $hashed_password = $row["password"];
                    $role = $row["role"];
                    if(password_verify($password, $hashed_password)){
                        // تخزين البيانات في الجلسة
                        $_SESSION["user_id"] = $id;
                        $_SESSION["username"] = $username;
                        $_SESSION["user_role"] = $role;
                        
                        header("location: index.php");
                    } else {
                        $login_err = "اسم المستخدم أو كلمة المرور غير صحيحة.";
                    }
                }
            } else {
                $login_err = "اسم المستخدم أو كلمة المرور غير صحيحة.";
            }
        } else {
            $login_err = "حدث خطأ ما. الرجاء المحاولة لاحقاً.";
        }
        unset($stmt);
    }
    unset($pdo);
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل الدخول - نظام إدارة المهام</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .login-container {
            max-width: 400px;
            margin: 100px auto;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container">
        <div class="login-container">
            <div class="card shadow-lg">
                <div class="card-body p-5">
                    <h2 class="text-center mb-4">تسجيل الدخول</h2>
                    
                    <?php 
                    if(!empty($login_err)){
                        echo '<div class="alert alert-danger">' . $login_err . '</div>';
                    }        
                    ?>

                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                        <div class="mb-3">
                            <label class="form-label">اسم المستخدم</label>
                            <input type="text" name="username" class="form-control" required>
                        </div>    
                        <div class="mb-3">
                            <label class="form-label">كلمة المرور</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">دخول</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
