<?php
if(!isset($_SESSION)) {
    session_start();
}
?>
<div class="col-md-3 col-lg-2 d-md-block bg-dark sidebar collapse">
    <div class="position-sticky pt-3">
        <div class="text-center mb-4">
            <img src="../assets/img/user-avatar.png" alt="صورة المستخدم" class="rounded-circle" width="64" height="64">
            <h6 class="text-white mt-2"><?php echo htmlspecialchars($_SESSION['username']); ?></h6>
        </div>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link text-white" href="../index.php">
                    <i class="fas fa-home ms-2"></i>
                    الرئيسية
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white" href="../pages/tasks.php">
                    <i class="fas fa-tasks ms-2"></i>
                    المهام
                </a>
            </li>
            <?php if(isset($_SESSION['user_role']) && $_SESSION['user_role'] == 'admin'): ?>
            <li class="nav-item">
                <a class="nav-link text-white" href="../pages/users.php">
                    <i class="fas fa-users ms-2"></i>
                    المستخدمين
                </a>
            </li>
            <?php endif; ?>
            <li class="nav-item">
                <a class="nav-link text-white" href="../pages/files.php">
                    <i class="fas fa-folder ms-2"></i>
                    الملفات
                </a>
            </li>
            <li class="nav-item mt-3">
                <a class="nav-link text-white" href="../logout.php">
                    <i class="fas fa-sign-out-alt ms-2"></i>
                    تسجيل الخروج
                </a>
            </li>
        </ul>
    </div>
</div>
