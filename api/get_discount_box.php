<?php
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-cache, no-store, must-revalidate');

$filePath = __DIR__ . '/data/discount_box.json';

if (!file_exists($filePath)) {
    echo json_encode([
        'enabled' => true,
        'tag' => 'عضوية مميزة',
        'title' => "احصل على خصم 15%\nعلى أول طلب!",
        'subtitle' => 'سجّل الآن واحصل على كود خصم خاص على أول عملية شراء',
        'btn_text' => 'احصل على الكود'
    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit;
}

echo file_get_contents($filePath);
