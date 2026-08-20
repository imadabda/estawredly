<?php
header('Content-Type: application/json; charset=utf-8');
header("Cache-Control: no-cache, must-revalidate");

$file = __DIR__ . '/data/hero_banner.json';

if (file_exists($file)) {
    echo file_get_contents($file);
} else {
    echo json_encode([
        "desktop_image" => "assets/hero_banner_import.png",
        "mobile_image" => "assets/hero_banner_import_mobile.png",
        "link" => "contact.html",
        "alt_text" => "إستوردلي - نستورد لك ما تحتاجه من المصدر"
    ]);
}
