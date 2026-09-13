<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");

$userId = intval($_GET['user_id'] ?? ($_SESSION['user_id'] ?? 0));

if (!$userId) {
    echo json_encode(['success' => false, 'exists' => false, 'cart' => []]);
    exit;
}

$cartFile = __DIR__ . '/data/user_cart_' . $userId . '.json';
if (file_exists($cartFile)) {
    $raw = file_get_contents($cartFile);
    $data = json_decode($raw, true);
    
    $cart = [];
    $updatedAt = filemtime($cartFile);
    if (is_array($data)) {
        if (isset($data['cart']) && is_array($data['cart'])) {
            $cart = array_values($data['cart']);
            $updatedAt = $data['updated_at'] ?? $updatedAt;
        } else {
            $cart = array_values($data);
        }
    }
    
    echo json_encode([
        'success' => true,
        'exists' => true,
        'cart' => $cart,
        'updated_at' => $updatedAt
    ]);
} else {
    echo json_encode([
        'success' => true,
        'exists' => false,
        'cart' => []
    ]);
}
?>
