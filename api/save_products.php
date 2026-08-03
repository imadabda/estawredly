<?php
session_start();
header('Content-Type: application/json');

// التأكد من تسجيل دخول الأدمن
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'غير مصرح']);
    exit;
}

// قراءة البيانات المرسلة
$data = json_decode(file_get_contents('php://input'), true);

if (!is_array($data)) {
    echo json_encode(['success' => false, 'message' => 'بيانات غير صالحة']);
    exit;
}

$dataFile = '../products_data.json';
$jsFile = '../products_db.js';

// حفظ البيانات في ملف JSON
if (file_put_contents($dataFile, json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT))) {
    
    // إنشاء ملف JS ليكون سريع التحميل للواجهة الأمامية
    $jsContent = "const PRODUCTS_DB = " . json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . ";\n";
    file_put_contents($jsFile, $jsContent);
    
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'فشل في حفظ البيانات']);
}
?>
