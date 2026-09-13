<?php
header('Content-Type: application/json; charset=utf-8');
$file = 'data/policies.json';
if(file_exists($file)){
    echo file_get_contents($file);
} else {
    echo json_encode([
        'shipping_policy' => 'نوفر شحناً سريعاً لجميع محافظات فلسطين خلال 2–5 أيام عمل. الشحن مجاني للطلبات التي تتجاوز ₪200، وبتكلفة ₪15 للطلبات الأقل.',
        'return_policy' => 'يمكنك إرجاع أي منتج خلال 30 يوماً من تاريخ الاستلام، شريطة أن يكون في حالته الأصلية مع تغليفه. سيتم استرداد المبلغ كاملاً خلال 5–7 أيام عمل.'
    ], JSON_UNESCAPED_UNICODE);
}
?>
