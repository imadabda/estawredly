<?php
session_start();
header('Content-Type: application/json');

// Verify admin login
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'غير مصرح']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
if (!is_array($data)) {
    echo json_encode(['success' => false, 'message' => 'بيانات غير صالحة']);
    exit;
}

$file = __DIR__ . '/data/currency.json';

// Load existing config to merge defaults
$config = [
    'enabled' => false,
    'base_rate' => 0.50,
    'current_rate' => 0.50,
    'auto_fetch' => true,
    'last_updated' => ''
];

if (file_exists($file)) {
    $existing = json_decode(file_get_contents($file), true);
    if (is_array($existing)) {
        $config = array_merge($config, $existing);
    }
}

// Update settings
$config['enabled'] = filter_var($data['enabled'] ?? $config['enabled'], FILTER_VALIDATE_BOOLEAN);
$config['auto_fetch'] = filter_var($data['auto_fetch'] ?? $config['auto_fetch'], FILTER_VALIDATE_BOOLEAN);
$config['base_rate'] = floatval($data['base_rate'] ?? $config['base_rate']);
$config['current_rate'] = floatval($data['current_rate'] ?? $config['current_rate']);

if (isset($data['force_fetch']) && $data['force_fetch']) {
    $apiUrl = 'https://open.er-api.com/v6/latest/CNY';
    $ctx = stream_context_create([
        'http' => [
            'timeout' => 5,
            'header' => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64)\r\n"
        ]
    ]);
    $response = @file_get_contents($apiUrl, false, $ctx);
    if ($response) {
        $apiData = json_decode($response, true);
        if (isset($apiData['rates']['ILS'])) {
            $config['current_rate'] = floatval($apiData['rates']['ILS']);
            $config['last_updated'] = date('Y-m-d H:i:s');
        } else {
            echo json_encode(['success' => false, 'message' => 'لم يتم العثور على سعر الصرف في الاستجابة']);
            exit;
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'فشل الاتصال بمزود خدمة سعر الصرف']);
        exit;
    }
}

file_put_contents($file, json_encode($config, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
echo json_encode(['success' => true, 'config' => $config]);
