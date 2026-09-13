<?php
require_once 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'طريقة طلب غير صالحة']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$email = trim($data['email'] ?? '');

if (empty($email)) {
    echo json_encode(['success' => false, 'message' => 'يرجى إدخال البريد الإلكتروني']);
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->rowCount() > 0) {
        
        // إنشاء توكن آمن للاسترجاع
        $token = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));

        $update_stmt = $pdo->prepare("UPDATE users SET reset_token = ?, reset_expires = ? WHERE email = ?");
        $update_stmt->execute([$token, $expires, $email]);

        // إرسال الإيميل
        // في Hostinger، يجب تغيير 'From' لتكون من نفس دومين الموقع
        $reset_link = "https://" . $_SERVER['HTTP_HOST'] . "/reset_password.php?token=" . $token;
        $subject = "استرجاع كلمة المرور - متجر استوردلي";
        $message = "مرحباً،\nلقد طلبت استرجاع كلمة المرور الخاصة بك.\nيرجى النقر على الرابط التالي لتعيين كلمة مرور جديدة:\n" . $reset_link . "\n\nملاحظة: هذا الرابط صالح لمدة ساعة واحدة فقط.";
        $headers = "From: noreply@" . $_SERVER['HTTP_HOST'];

        if (mail($email, $subject, $message, $headers)) {
            echo json_encode(['success' => true, 'message' => 'تم إرسال رابط استرجاع كلمة المرور إلى بريدك الإلكتروني']);
        } else {
            echo json_encode(['success' => false, 'message' => 'حدث خطأ في سيرفر البريد، يرجى المحاولة لاحقاً']);
        }

    } else {
        // لا نظهر أن البريد غير موجود لأسباب أمنية
        echo json_encode(['success' => true, 'message' => 'إذا كان البريد مسجلاً، فستصلك رسالة لاسترجاع كلمة المرور']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'خطأ في قاعدة البيانات']);
}
?>
