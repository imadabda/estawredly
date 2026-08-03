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

try {
    $stmt = $pdo->query("SELECT * FROM `orders` ORDER BY `created_at` DESC");
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'orders' => $orders
    ]);
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'خطأ في جلب الطلبات: ' . $e->getMessage()
    ]);
}
?>
