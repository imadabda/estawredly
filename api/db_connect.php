<?php
// ملف الاتصال بقاعدة بيانات Hostinger

// ضع بيانات قاعدة البيانات الخاصة بك هنا عندما ترفع الملفات إلى Hostinger
$db_host = 'localhost'; 
$db_user = 'u515868829_estawredly_usr'; // استبدله باسم مستخدم قاعدة بيانات Hostinger
$db_pass = 'Estawredly@2026#DB'; // استبدله بكلمة مرور قاعدة بيانات Hostinger
$db_name = 'u515868829_store'; // استبدله باسم قاعدة بيانات Hostinger

// إعداد الترويسات (Headers)
// تمت إزالة هيدر JSON من هنا لكي تعمل صفحات HTML بشكل صحيح

try {
    // إنشاء الاتصال باستخدام PDO لتوفير أقصى درجات الحماية من الاختراقات (SQL Injection)
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8mb4", $db_user, $db_pass);
    
    // إعداد PDO لإظهار الأخطاء بشكل واضح
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $pdo = null; // Set to null instead of exiting immediately
    $db_error_message = $e->getMessage();
    
    // Only output JSON if this is a direct API call (not included from HTML pages)
    if (strpos($_SERVER['REQUEST_URI'], '/api/') !== false) {
        echo json_encode([
            'success' => false,
            'message' => 'فشل الاتصال بقاعدة البيانات: ' . $db_error_message
        ]);
        exit;
    }
}
?>
