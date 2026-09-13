<?php
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-cache, no-store, must-revalidate');

$filePath = __DIR__ . '/data/popup_banner.json';

if (!file_exists($filePath)) {
    echo json_encode([
        'enabled' => false,
        'tag' => '',
        'title' => '',
        'message' => '',
        'image' => '',
        'btn_text' => '',
        'btn_link' => '',
        'show_once' => false
    ]);
    exit;
}

echo file_get_contents($filePath);
