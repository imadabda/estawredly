<?php
session_start();
if (!isset($_SESSION["user_role"]) || $_SESSION["user_role"] !== "admin") {
    die("غير مصرح لك بالوصول");
}

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="قالب_استيراد_المنتجات_استوردلي.csv"');

$output = fopen('php://output', 'w');
// UTF-8 BOM for Microsoft Excel Arabic display
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

// Headers
fputcsv($output, [
    'اسم المنتج (إجباري)',
    'السعر الحالي (إجباري)',
    'السعر القديم (اختياري)',
    'سعر التكلفة (اختياري)',
    'التصنيف (إجباري)',
    'الماركة (اختياري)',
    'عدد القطع في الكرتونة (إجباري)',
    'كود المنتج SKU (اختياري)',
    'كود المصنع (اختياري)',
    'ملاحظات الرقم المرجعي (اختياري)',
    'رابط الصورة (اختياري)',
    'وصف المنتج (اختياري)',
    'الكمية بالمخزون (اختياري)',
    'الشارة (sale/new/hot/best)'
]);

// Sample Rows
fputcsv($output, [
    'ممسحة مايكروفايبر مع مقبض تلسكوبي',
    '28.50',
    '38.00',
    '18.00',
    'مماسح مايكروفايبر',
    'KLEANER',
    '24',
    'KL-MOP-101',
    'FC-7721',
    'الرف B-04 / كرتونة قوية',
    'https://images.unsplash.com/photo-1583947215259-38e31be8751f?w=500&q=80',
    'ممسحة احترافية سريعة الامتصاص لجميع أنواع الأرضيات والأسطح',
    '120',
    'hot'
]);

fputcsv($output, [
    'اسفنجة جلي سوبر 5 قطع',
    '6.00',
    '8.50',
    '3.50',
    'اسفنجات جلي',
    '',
    '48',
    'SP-GL-202',
    'FC-3390',
    'الرف C-11',
    'https://images.unsplash.com/photo-1585670149967-b4f4da88cc9f?w=500&q=80',
    'اسفنجة تنظيف وجلي شديدة التحمل للأواني والقدور',
    '300',
    'sale'
]);

fputcsv($output, [
    'جاروف ومكنسة يد صغيرة متعددة الاستخدام',
    '14.00',
    '18.00',
    '8.00',
    'جاروف مع مكنسه',
    'KLEANER',
    '36',
    'KL-DUST-303',
    'FC-5512',
    'الرف A-02',
    'https://images.unsplash.com/photo-1581578731548-c64695cc6952?w=500&q=80',
    'طقم جاروف ومكنسة خفيف وعملي لتنظيف الزوايا والأماكن الضيقة',
    '80',
    'new'
]);

fclose($output);
exit;
