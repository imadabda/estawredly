<?php
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-cache, no-store, must-revalidate');

$filePath = __DIR__ . '/data/import_sections.json';

if (!file_exists($filePath)) {
    echo json_encode([]);
    exit;
}

echo file_get_contents($filePath);
