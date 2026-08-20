<?php
header('Content-Type: application/json; charset=utf-8');
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

$file_path = __DIR__ . '/data/nav.json';

if (file_exists($file_path)) {
    echo file_get_contents($file_path);
} else {
    echo json_encode([]);
}
?>
