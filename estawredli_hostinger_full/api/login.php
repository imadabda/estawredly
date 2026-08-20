<?php
session_start();
require_once 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'طريقة طلب غير صالحة']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

$email = trim($data['email'] ?? '');
$password = $data['password'] ?? '';

if (empty($email) || empty($password)) {
    echo json_encode(['success' => false, 'message' => 'يرجى إدخال البريد الإلكتروني وكلمة المرور']);
    exit;
}

try {
    // جلب المستخدم بناءً على البريد الإلكتروني (بما في ذلك الحالة)
    $stmt = $pdo->prepare("SELECT id, name, email, password, role, status FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user) {
        // إذا كان المستخدم قد سجل عبر جوجل (لا يوجد له باسوورد)
        if (empty($user['password'])) {
            echo json_encode(['success' => false, 'message' => 'تم إنشاء هذا الحساب مسبقاً باستخدام جوجل. يرجى تسجيل الدخول عبر زر جوجل.']);
            exit;
        }

        // التحقق من صحة كلمة المرور المشفرة
        if (password_verify($password, $user['password'])) {
            
            // التحقق من حالة الحساب (للزوار العاديين فقط)
            if ($user['role'] !== 'admin' && isset($user['status']) && $user['status'] === 'pending') {
                echo json_encode(['success' => false, 'message' => 'حسابك قيد المراجعة. سوف يتم التواصل معك قريباً لتفعيل الحساب.']);
                exit;
            }

            // بيانات صحيحة -> حفظ الجلسة
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role'] = $user['role'];
            $_SESSION['user_status'] = $user['status'] ?? 'active';

            echo json_encode([
                'success' => true,
                'message' => 'تم تسجيل الدخول بنجاح',
                'user' => [
                    'name' => $user['name'],
                    'email' => $user['email'],
                    'role' => $user['role'],
                    'status' => $user['status'] ?? 'active'
                ]
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'كلمة المرور غير صحيحة']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'البريد الإلكتروني غير مسجل لدينا']);
    }

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'خطأ في قاعدة البيانات']);
}
?>
