<?php
header('Content-Type: application/json; charset=utf-8');
header("Cache-Control: no-cache, must-revalidate");

$file = __DIR__ . '/data/banners.json';

if (file_exists($file)) {
    echo file_get_contents($file);
} else {
    echo json_encode([]);
}
