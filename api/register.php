<?php
session_start();
require_once 'db_connect.php';

// التأكد من أن الطلب من نوع POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'طريقة طلب غير صالحة']);
    exit;
}

// قراءة البيانات القادمة من JSON (Fetch API)
$data = json_decode(file_get_contents('php://input'), true);

$name = trim($data['name'] ?? '');
$email = trim($data['email'] ?? '');
$password = $data['password'] ?? '';
$phone = trim($data['phone'] ?? '');

// التحقق من صحة المدخلات
if (empty($name) || empty($email) || empty($password) || empty($phone)) {
    echo json_encode(['success' => false, 'message' => 'يرجى تعبئة جميع الحقول المطلوبة (الاسم، البريد، كلمة المرور، رقم الجوال)']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'البريد الإلكتروني غير صالح']);
    exit;
}

if (strlen($password) < 8) {
    echo json_encode(['success' => false, 'message' => 'يجب أن تتكون كلمة المرور من 8 أحرف على الأقل']);
    exit;
}

try {
    // التأكد من أن البريد الإلكتروني غير مستخدم مسبقاً
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => false, 'message' => 'البريد الإلكتروني مسجل مسبقاً']);
        exit;
    }

    // تشفير كلمة المرور بقوة
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // إدخال المستخدم في قاعدة البيانات
    $insert_stmt = $pdo->prepare("INSERT INTO users (name, email, password, phone, role) VALUES (?, ?, ?, ?, 'customer')");
    
    if ($insert_stmt->execute([$name, $email, $hashed_password, $phone])) {
        
        // إرسال إيميل ترحيبي للمستخدم
        $user_subject = "مرحباً بك في متجر استوردلي - حسابك قيد المراجعة";
        $user_message = "مرحباً " . $name . "،

شكراً لتسجيلك في متجر استوردلي.
حسابك الآن قيد المراجعة. سوف يتم التواصل معك قريباً لتفعيل حسابك وإظهار الأسعار.

مع تحيات فريق استوردلي.";
        $headers = "From: noreply@" . $_SERVER['HTTP_HOST'];
        mail($email, $user_subject, $user_message, $headers);
        
        // إرسال إيميل للأدمن
        $admin_email = "admin@" . $_SERVER['HTTP_HOST']; // قم بتغيير هذا للإيميل الخاص بك
        $admin_subject = "تسجيل عضوية جديدة بانتظار التفعيل";
        $admin_message = "مرحباً،

هناك طلب عضوية جديد بانتظار تفعيلك:
الاسم: $name
الإيميل: $email
رقم الجوال: $phone

يرجى الدخول للوحة التحكم لتفعيل الحساب.";
        mail($admin_email, $admin_subject, $admin_message, $headers);

        echo json_encode([
            'success' => true, 
            'message' => 'تم إنشاء الحساب بنجاح! سوف يتم التواصل معك لتفعيل الحساب، أو يرجى التواصل معنا.'
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'حدث خطأ أثناء إنشاء الحساب، يرجى المحاولة لاحقاً']);
    }

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'خطأ في قاعدة البيانات']);
}
?>
