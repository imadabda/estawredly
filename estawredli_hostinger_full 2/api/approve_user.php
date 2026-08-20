<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
require_once 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'طريقة طلب غير صالحة']);
    exit;
}

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'غير مصرح لك بالوصول']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$userId = $data['user_id'] ?? 0;
$status = $data['status'] ?? 'active';

if (!$userId) {
    echo json_encode(['success' => false, 'message' => 'معرف المستخدم غير صحيح']);
    exit;
}

try {
    $stmt = $pdo->prepare("UPDATE users SET status = ? WHERE id = ?");
    if ($stmt->execute([$status, $userId])) {
        echo json_encode(['success' => true, 'message' => 'تم تحديث حالة الحساب بنجاح']);
    } else {
        echo json_encode(['success' => false, 'message' => 'فشل في تحديث حالة الحساب']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'خطأ في قاعدة البيانات']);
}
?>
