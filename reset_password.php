<?php
require_once 'api/db_connect.php';

$token = $_GET['token'] ?? '';
$valid = false;
$msg = '';

if (!empty($token)) {
    $stmt = $pdo->prepare("SELECT id, email FROM users WHERE reset_token = ? AND reset_expires > NOW()");
    $stmt->execute([$token]);
    $user = $stmt->fetch();
    
    if ($user) {
        $valid = true;
    } else {
        $msg = "رابط استرجاع كلمة المرور غير صالح أو منتهي الصلاحية.";
    }
} else {
    $msg = "لم يتم توفير رابط صحيح.";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $valid) {
    $new_pass = $_POST['password'] ?? '';
    
    if (strlen($new_pass) < 8) {
        $msg = "يجب أن تتكون كلمة المرور من 8 أحرف على الأقل.";
    } else {
        $hashed = password_hash($new_pass, PASSWORD_DEFAULT);
        
        $update = $pdo->prepare("UPDATE users SET password = ?, reset_token = NULL, reset_expires = NULL WHERE id = ?");
        if ($update->execute([$hashed, $user['id']])) {
            $msg = "تم إعادة تعيين كلمة المرور بنجاح. يمكنك الآن تسجيل الدخول.";
            $valid = false; // لإخفاء الفورم
        } else {
            $msg = "حدث خطأ أثناء حفظ كلمة المرور.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>استعادة كلمة المرور | استوردلي</title>
  <link rel="stylesheet" href="style.css">
  <style>
    body { background: #f9fafb; display: flex; justify-content: center; align-items: center; min-height: 100vh; font-family: 'Tajawal', sans-serif; }
    .reset-card { background: #fff; padding: 30px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); width: 100%; max-width: 400px; text-align: center; }
    .reset-card h2 { margin-bottom: 20px; color: #1f2937; }
    .msg { margin-bottom: 20px; padding: 10px; border-radius: 8px; background: #eff6ff; color: #1d4ed8; font-weight: 600; font-size: 14px; }
    .msg.error { background: #fef2f2; color: #dc2626; }
    .msg.success { background: #ecfdf5; color: #059669; }
    .form-group { margin-bottom: 15px; text-align: right; }
    .form-group label { display: block; margin-bottom: 5px; font-size: 14px; color: #4b5563; font-weight: 600; }
    .form-group input { width: 100%; padding: 10px 14px; border: 1.5px solid #d1d5db; border-radius: 8px; font-size: 14px; }
    .btn { background: #2563eb; color: #fff; width: 100%; padding: 12px; border: none; border-radius: 8px; font-size: 16px; font-weight: bold; cursor: pointer; }
    .btn:hover { background: #1d4ed8; }
    .back-link { display: inline-block; margin-top: 15px; color: #2563eb; text-decoration: none; font-size: 14px; }
  </style>
</head>
<body>
  <div class="reset-card">
    <h2>استعادة كلمة المرور</h2>
    
    <?php if ($msg): ?>
      <div class="msg <?php echo strpos($msg, 'بنجاح') !== false ? 'success' : 'error'; ?>">
        <?php echo $msg; ?>
      </div>
    <?php endif; ?>

    <?php if ($valid): ?>
      <form method="POST">
        <div class="form-group">
          <label>كلمة المرور الجديدة</label>
          <input type="password" name="password" required minlength="8" placeholder="أدخل كلمة المرور الجديدة">
        </div>
        <button type="submit" class="btn">حفظ كلمة المرور</button>
      </form>
    <?php endif; ?>

    <a href="index.html" class="back-link">العودة للصفحة الرئيسية</a>
  </div>
</body>
</html>
