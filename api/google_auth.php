<?php
session_start();
require_once 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'طريقة طلب غير صالحة']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$id_token = $data['id_token'] ?? '';

if (empty($id_token)) {
    echo json_encode(['success' => false, 'message' => 'بيانات مفقودة']);
    exit;
}

// التحقق من التوكن باستخدام خدمة جوجل
$verify_url = "https://oauth2.googleapis.com/tokeninfo?id_token=" . $id_token;
$response = file_get_contents($verify_url);

if ($response === false) {
    echo json_encode(['success' => false, 'message' => 'فشل التحقق من حساب جوجل']);
    exit;
}

$google_data = json_decode($response, true);

// التأكد من أن التوكن صالح وأن الإيميل موجود
if (isset($google_data['error']) || !isset($google_data['email'])) {
    echo json_encode(['success' => false, 'message' => 'حساب جوجل غير صالح']);
    exit;
}

$google_id = $google_data['sub'];
$email = $google_data['email'];
$name = $google_data['name'];

try {
    // البحث عن المستخدم باستخدام الإيميل أو الجوجل آي دي
    $stmt = $pdo->prepare("SELECT id, name, email, role, google_id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user) {
        // إذا كان حسابه مسجلاً مسبقاً بالإيميل لكن بدون google_id، نحدثه
        if (empty($user['google_id'])) {
            $update_stmt = $pdo->prepare("UPDATE users SET google_id = ? WHERE id = ?");
            $update_stmt->execute([$google_id, $user['id']]);
        }
        
        $user_id = $user['id'];
        $user_role = $user['role'];
    } else {
        // إنشاء حساب جديد عبر جوجل بوضع النشط تلقائياً
        $insert_stmt = $pdo->prepare("INSERT INTO users (name, email, google_id, role, status) VALUES (?, ?, ?, 'customer', 'active')");
        $insert_stmt->execute([$name, $email, $google_id]);
        $user_id = $pdo->lastInsertId();
        $user_role = 'customer';
    }

    // تسجيل الدخول بنجاح
    $_SESSION['user_id'] = $user_id;
    $_SESSION['user_name'] = $name;
    $_SESSION['user_email'] = $email;
    $_SESSION['user_role'] = $user_role;
    $_SESSION['user_status'] = 'active';

    echo json_encode([
        'success' => true,
        'message' => 'تم تسجيل الدخول بنجاح عبر جوجل',
        'user' => [
            'name' => $name,
            'email' => $email,
            'role' => $user_role,
            'status' => 'active'
        ]
    ]);

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'خطأ في قاعدة البيانات']);
}
?>
