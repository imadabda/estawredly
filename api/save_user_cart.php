<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");

$raw = file_get_contents('php://input');
$input = json_decode($raw, true);

$userId = intval($input['user_id'] ?? ($_SESSION['user_id'] ?? 0));
$cart = $input['cart'] ?? [];

if (!$userId) {
    echo json_encode(['success' => false, 'message' => 'غير مسجل']);
    exit;
}

$dataDir = __DIR__ . '/data';
if (!is_dir($dataDir)) {
    @mkdir($dataDir, 0775, true);
}

$cartFile = $dataDir . '/user_cart_' . $userId . '.json';

$payload = [
    'user_id' => $userId,
    'cart' => is_array($cart) ? array_values($cart) : [],
    'count' => is_array($cart) ? count($cart) : 0,
    'updated_at' => time()
];

$encoded = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
if (file_put_contents($cartFile, $encoded, LOCK_EX)) {
    @chmod($cartFile, 0664);
    echo json_encode([
        'success' => true,
        'count' => $payload['count'],
        'updated_at' => $payload['updated_at']
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'فشل حفظ السلة']);
}
?>
