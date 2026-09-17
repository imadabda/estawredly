<?php
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-cache, no-store, must-revalidate');

$filePath = __DIR__ . '/data/trust_bar.json';

if (!file_exists($filePath)) {
    echo json_encode([
        'enabled' => true,
        'items' => [
            ['id' => 1, 'icon' => '🚚', 'title' => 'توصيل مجاني', 'desc' => 'للطلبات فوق $50', 'enabled' => true],
            ['id' => 2, 'icon' => '↩️', 'title' => 'إرجاع سهل', 'desc' => 'خلال 30 يوم', 'enabled' => true],
            ['id' => 3, 'icon' => '🔒', 'title' => 'دفع آمن', 'desc' => 'حماية 100%', 'enabled' => true],
            ['id' => 4, 'icon' => '🎧', 'title' => 'دعم 24/7', 'desc' => 'نحن دائمًا هنا', 'enabled' => true],
            ['id' => 5, 'icon' => '🏆', 'title' => 'ضمان الجودة', 'desc' => 'منتجات أصلية 100%', 'enabled' => true]
        ]
    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit;
}

echo file_get_contents($filePath);
