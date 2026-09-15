<?php
if (!ob_start("ob_gzhandler")) ob_start();
session_start();
header('Content-Type: application/json; charset=utf-8');
header("Cache-Control: no-cache, must-revalidate");

$dataFile = __DIR__ . '/../products_data.json';

if (!file_exists($dataFile)) {
    echo '{"success":true,"products":[]}';
    exit;
}

$content = @file_get_contents($dataFile);
if ($content === false || trim($content) === '') {
    echo '{"success":true,"products":[]}';
    exit;
}

// Direct stream to avoid heavy json_decode and re-encoding
echo '{"success":true,"products":' . $content . '}';
?>
