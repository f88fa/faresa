<?php
require_once 'includes/config.php';

try {
    // إنشاء مستخدم جديد
    $sql = "INSERT INTO users (username, password, full_name, email, role) VALUES 
            (:username, :password, :full_name, :email, 'admin')";
    
    $stmt = $pdo->prepare($sql);
    
    // كلمة المرور: admin123
    $hashed_password = password_hash('admin123', PASSWORD_DEFAULT);
    
    $stmt->execute([
        ':username' => 'admin',
        ':password' => $hashed_password,
        ':full_name' => 'مدير النظام',
        ':email' => 'admin@example.com'
    ]);
    
    echo "تم إنشاء المستخدم بنجاح!";
    echo "<br>اسم المستخدم: admin";
    echo "<br>كلمة المرور: admin123";
    
} catch(PDOException $e) {
    echo "خطأ: " . $e->getMessage();
}
?>
