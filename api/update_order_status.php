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

$rawInput = json_decode(file_get_contents('php://input'), true);

$id = intval($_POST['order_id'] ?? $rawInput['order_id'] ?? 0);
$status = trim($_POST['status'] ?? $rawInput['status'] ?? '');

$validStatuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled', 'completed'];

if ($id <= 0 || !in_array($status, $validStatuses)) {
    echo json_encode(['success' => false, 'message' => 'بيانات غير صالحة. رقم الطلبية: ' . $id . ' والحالة: ' . $status]);
    exit;
}

try {
    // التأكد من تعديل نوع العمود إن كان MySQL مقيداً بـ ENUM
    try {
        $pdo->exec("ALTER TABLE `orders` MODIFY COLUMN `status` VARCHAR(50) DEFAULT 'pending'");
    } catch (Exception $altE) {}

    $stmt = $pdo->prepare("UPDATE `orders` SET `status` = ? WHERE `id` = ?");
    $stmt->execute([$status, $id]);

    echo json_encode(['success' => true, 'message' => 'تم تحديث حالة الطلب إلى: ' . $status]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'خطأ في التحديث: ' . $e->getMessage()]);
}
?>
