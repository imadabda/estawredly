<?php
session_start();
require_once 'api/db_connect.php';

// إذا كان مسجلاً دخول كمدير مسبقاً، تحويله فوراً
if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin') {
    header("Location: admin.php");
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!empty($email) && !empty($password)) {
        if ($pdo) {
            try {
                $stmt = $pdo->prepare("SELECT id, name, email, password, role FROM users WHERE email = ? AND role = 'admin'");
                $stmt->execute([$email]);
                $admin = $stmt->fetch();

                if ($admin && password_verify($password, $admin['password'])) {
                    $_SESSION['user_id'] = $admin['id'];
                    $_SESSION['user_name'] = $admin['name'];
                    $_SESSION['user_email'] = $admin['email'];
                    $_SESSION['user_role'] = $admin['role'];

                    header("Location: admin.php");
                    exit;
                } else {
                    $error = "بيانات الدخول غير صحيحة أو أنك لا تملك صلاحية الإدارة.";
                }
            } catch (PDOException $e) {
                $error = "خطأ في الاتصال بقاعدة البيانات.";
            }
        } else {
            // Local fallback when MySQL is not available (for local testing only)
            if ($email === 'admin@estawredly.com' && $password === 'admin123') {
                $_SESSION['user_id'] = 1;
                $_SESSION['user_name'] = 'المدير العام (محلي)';
                $_SESSION['user_email'] = 'admin@estawredly.com';
                $_SESSION['user_role'] = 'admin';

                header("Location: admin.php");
                exit;
            } else {
                $error = "قاعدة البيانات غير متصلة محلياً. استخدم الايميل admin@estawredly.com وكلمة المرور admin123 للدخول المحلي.";
            }
        }
    } else {
        $error = "يرجى إدخال البريد الإلكتروني وكلمة المرور.";
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل دخول الإدارة | استوردلي</title>
    <link rel="icon" type="image/png" href="favicon.png" />
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Tajawal', sans-serif; background: #0f172a; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .login-box { background: #1e293b; padding: 40px; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.5); width: 100%; max-width: 350px; text-align: center; color: #fff; }
        .login-icon { font-size: 40px; margin-bottom: 20px; color: #3b82f6; }
        h2 { margin-top: 0; margin-bottom: 30px; font-size: 24px; }
        input { width: 100%; padding: 12px; margin-bottom: 20px; border: 1px solid #334155; border-radius: 8px; box-sizing: border-box; background: #0f172a; color: #fff; outline: none; }
        input:focus { border-color: #3b82f6; }
        button { background: #3b82f6; color: #fff; border: none; padding: 12px; width: 100%; border-radius: 8px; font-size: 16px; font-weight: bold; cursor: pointer; transition: 0.3s; }
        button:hover { background: #2563eb; }
        .error { color: #ef4444; margin-bottom: 20px; font-size: 14px; }
    </style>
</head>
<body>
    <div class="login-box">
        <div class="login-icon">🔒</div>
        <h2>لوحة الإدارة الآمنة</h2>
        <?php if (!empty($error)): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <form method="POST">
            <input type="email" name="email" placeholder="البريد الإلكتروني" required>
            <input type="password" name="password" placeholder="كلمة المرور" required>
            <button type="submit">دخول</button>
        </form>
    </div>
</body>
</html>
