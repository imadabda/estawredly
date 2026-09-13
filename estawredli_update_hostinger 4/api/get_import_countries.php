<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

$file = __DIR__ . '/data/import_countries.json';

if (file_exists($file)) {
    echo file_get_contents($file);
} else {
    $default = [
        "section_title" => "نستورد من الأفضل 🌏",
        "section_subtitle" => "شراكات مع كبرى المصانع والموردين حول العالم",
        "countries" => [
            ["id" => "china", "flag" => "🇨🇳", "name" => "الصين", "categories" => "عروض خاصة • مماسح • مماسح دوارة", "badge" => "الأكثر استيراداً", "active" => true],
            ["id" => "turkey", "flag" => "🇹🇷", "name" => "تركيا", "categories" => "مماسح • جملة • مماسح بخاخ", "badge" => "جودة عالية", "active" => true],
            ["id" => "india", "flag" => "🇮🇳", "name" => "الهند", "categories" => "مجوهرات • توابل • جملة", "badge" => "حرف يدوية", "active" => true],
            ["id" => "germany", "flag" => "🇩🇪", "name" => "ألمانيا", "categories" => "معدات • سيارات • تقنية", "badge" => "دقة ألمانية", "active" => true],
            ["id" => "korea", "flag" => "🇰🇷", "name" => "كوريا", "categories" => "تجميل • عروض خاصة • غذاء", "badge" => "K-Trend", "active" => true],
            ["id" => "italy", "flag" => "🇮🇹", "name" => "إيطاليا", "categories" => "موضة • جلود • طعام", "badge" => "فخامة أوروبية", "active" => true]
        ]
    ];
    echo json_encode($default, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
}
?>
