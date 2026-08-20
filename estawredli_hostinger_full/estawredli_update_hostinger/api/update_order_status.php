<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
require_once 'db_connect.php';

// التحقق من أن المستخدم مدير
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'غير مصرح لك.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'طريقة الطلب غير صحيحة.']);
    exit;
}

$id = intval($_POST['order_id'] ?? 0);
$status = trim($_POST['status'] ?? '');

$validStatuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];

if ($id <= 0 || !in_array($status, $validStatuses)) {
    echo json_encode(['success' => false, 'message' => 'بيانات غير صالحة.']);
    exit;
}

try {
    $stmt = $pdo->prepare("UPDATE `orders` SET `status` = ? WHERE `id` = ?");
    $stmt->execute([$status, $id]);

    echo json_encode(['success' => true, 'message' => 'تم تحديث حالة الطلب.']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'خطأ في التحديث: ' . $e->getMessage()]);
}
?>
