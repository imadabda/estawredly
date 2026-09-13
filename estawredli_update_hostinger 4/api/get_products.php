<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

$dataFile = __DIR__ . '/../products_data.json';

if (!file_exists($dataFile)) {
    echo json_encode(['success' => true, 'products' => []]);
    exit;
}

$fp = fopen($dataFile, 'r');
if ($fp) {
    flock($fp, LOCK_SH);
    $content = stream_get_contents($fp);
    flock($fp, LOCK_UN);
    fclose($fp);
    
    $products = json_decode($content, true);
    if (!is_array($products)) {
        $products = [];
    }
    echo json_encode(['success' => true, 'products' => $products]);
} else {
    $content = file_get_contents($dataFile);
    $products = json_decode($content, true) ?: [];
    echo json_encode(['success' => true, 'products' => $products]);
}
?>
