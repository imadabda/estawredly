<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

$file = __DIR__ . '/data/footer_settings.json';

if (file_exists($file)) {
    echo file_get_contents($file);
} else {
    $default = [
        "about_text" => "متجرك المفضل لكل شيء. نقدم آلاف المنتجات بأفضل الأسعار مع توصيل سريع وخدمة عملاء استثنائية.",
        "social" => [
            "facebook" => "https://facebook.com",
            "instagram" => "https://instagram.com",
            "tiktok" => "https://tiktok.com",
            "youtube" => "https://youtube.com"
        ],
        "apps" => [
            "app_store" => "https://apple.com/app-store",
            "google_play" => "https://play.google.com"
        ],
        "copyright" => "© جميع الحقوق محفوظة – إستوردلي 2024"
    ];
    echo json_encode($default, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
}
