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

$raw_id = $_POST['order_id'] ?? '';
$id = intval($raw_id);
if ($id <= 0) {
    $id = intval(preg_replace('/[^0-9]/', '', $raw_id));
}

if ($id <= 0) {
    echo json_encode(['success' => false, 'message' => 'رقم الطلبية غير صالح.']);
    exit;
}

try {
    $stmt = $pdo->prepare("DELETE FROM `orders` WHERE `id` = ?");
    $stmt->execute([$id]);

    echo json_encode(['success' => true, 'message' => 'تم مسح الطلبية بنجاح.']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'خطأ أثناء المسح: ' . $e->getMessage()]);
}
?>
