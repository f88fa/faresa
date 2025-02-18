<?php
// معلومات الاتصال بقاعدة البيانات
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'faresa_db');

// محاولة الاتصال بقاعدة البيانات
try {
    $pdo = new PDO("mysql:host=" . DB_SERVER . ";dbname=" . DB_NAME, DB_USERNAME, DB_PASSWORD);
    // ضبط وضع الأخطاء PDO إلى الاستثناء
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // ضبط الترميز إلى UTF-8
    $pdo->exec("set names utf8mb4");
} catch(PDOException $e) {
    die("خطأ: فشل الاتصال بقاعدة البيانات. " . $e->getMessage());
}

// دالة للتحقق من تسجيل الدخول
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// دالة للتحقق من صلاحيات المستخدم
function hasPermission($permission) {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === $permission;
}

// دالة لتنظيف المدخلات
function cleanInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}
?>
