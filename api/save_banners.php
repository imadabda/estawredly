<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

// Check admin auth
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!is_array($data)) {
    echo json_encode(['success' => false, 'message' => 'Invalid data format']);
    exit;
}

$file = __DIR__ . '/data/banners.json';
if (file_put_contents($file, json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT))) {
    echo json_encode(['success' => true, 'message' => 'تم حفظ البنرات بنجاح']);
} else {
    echo json_encode(['success' => false, 'message' => 'فشل في حفظ البنرات']);
}
