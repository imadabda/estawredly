<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$data = file_get_contents('php://input');
$json = json_decode($data);

if ($json === null) {
    echo json_encode(['success' => false, 'message' => 'Invalid JSON']);
    exit;
}

$file_path = __DIR__ . '/data/nav.json';
if (file_put_contents($file_path, json_encode($json, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT))) {
    echo json_encode(['success' => true, 'message' => 'Navigation saved successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to save file']);
}
?>
