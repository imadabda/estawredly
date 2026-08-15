<?php
header('Content-Type: application/json; charset=utf-8');
$file = 'data/ticker.json';
if(file_exists($file)){
    echo file_get_contents($file);
} else {
    echo json_encode([
        'enabled' => true,
        'text' => 'شحن مجاني لكافة المناطق للطلبات فوق 300 شيكل! | أسرع توصيل خلال 2-5 أيام عمل | منتجات أصلية 100%'
    ], JSON_UNESCAPED_UNICODE);
}
?>
