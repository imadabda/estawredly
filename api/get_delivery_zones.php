<?php
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-cache, no-store, must-revalidate');

$file = __DIR__ . '/data/delivery_zones.json';

if (file_exists($file)) {
    echo file_get_contents($file);
} else {
    echo json_encode([
        'enabled' => true,
        'zones' => [
            ['id' => 1, 'name' => 'الضفة', 'price' => 20],
            ['id' => 2, 'name' => 'القدس', 'price' => 30],
            ['id' => 3, 'name' => 'الداخل', 'price' => 70]
        ]
    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
}
