<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
require_once 'db_connect.php';

// التحقق من أن المستخدم مدير
$isAdmin = (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin') ||
           (isset($_SESSION['role']) && $_SESSION['role'] === 'admin');

if (!$isAdmin) {
    echo json_encode(['success' => false, 'message' => 'غير مصرح لك.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'طريقة الطلب غير صحيحة.']);
    exit;
}

$rawInput = json_decode(file_get_contents('php://input'), true);

$id = intval($_POST['order_id'] ?? $rawInput['order_id'] ?? 0);
$container = trim($_POST['container_number'] ?? $rawInput['container_number'] ?? '');

if ($id <= 0) {
    echo json_encode(['success' => false, 'message' => 'رقم الطلبية غير صالح.']);
    exit;
}

$saved = false;

if ($pdo) {
    try {
        try {
            $colCheck = $pdo->query("SHOW COLUMNS FROM `orders` LIKE 'container_number'");
            if ($colCheck && $colCheck->rowCount() == 0) {
                $pdo->exec("ALTER TABLE `orders` ADD `container_number` VARCHAR(100) DEFAULT ''");
            }
        } catch (Exception $e) {}

        $stmt = $pdo->prepare("UPDATE `orders` SET `container_number` = ? WHERE `id` = ?");
        $stmt->execute([$container, $id]);
        $saved = true;
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'خطأ في التحديث: ' . $e->getMessage()]);
        exit;
    }
}

// Also update orders.json if exists
$jsonFile = __DIR__ . '/../orders.json';
if (file_exists($jsonFile)) {
    $orders = json_decode(file_get_contents($jsonFile), true);
    if (is_array($orders)) {
        foreach ($orders as &$o) {
            if (isset($o['id']) && strval($o['id']) === strval($id)) {
                $o['container_number'] = $container;
                $saved = true;
                break;
            }
        }
        file_put_contents($jsonFile, json_encode($orders, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }
}

if ($saved) {
    echo json_encode([
        'success' => true,
        'message' => 'تم حفظ رقم الكونتينر بنجاح.',
        'order_id' => $id,
        'container_number' => $container
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'تعذر تحديث رقم الكونتينر في قاعدة البيانات.'
    ]);
}
?>
