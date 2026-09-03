<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// التأكد من تسجيل دخول الأدمن
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'غير مصرح لك بالوصول']);
    exit;
}

// قراءة البيانات المرسلة
$rawInput = file_get_contents('php://input');
$payload = json_decode($rawInput, true);

if ($payload === null) {
    echo json_encode(['success' => false, 'message' => 'بيانات JSON غير صالحة']);
    exit;
}

$dataFile = __DIR__ . '/../products_data.json';
$jsFile = __DIR__ . '/../products_db.js';

// استخدام قفل ملف حصري لمنع تضارب الكتابة عند العمل من أكثر من جهاز في نفس اللحظة
$lockFile = __DIR__ . '/products.lock';
$lockFp = fopen($lockFile, 'c+');

if (!$lockFp || !flock($lockFp, LOCK_EX)) {
    echo json_encode(['success' => false, 'message' => 'السيرفر مشغول حالياً بمعالجة عملية حفظ أخرى، يرجى المحاولة بعد ثوانٍ']);
    if ($lockFp) fclose($lockFp);
    exit;
}

try {
    // قراءة أحدث نسخة من المنتجات على السيرفر
    $currentProducts = [];
    if (file_exists($dataFile)) {
        $existingJson = file_get_contents($dataFile);
        $currentProducts = json_decode($existingJson, true);
        if (!is_array($currentProducts)) {
            $currentProducts = [];
        }
    }

    $action = is_array($payload) && isset($payload['action']) ? $payload['action'] : 'batch';

    if ($action === 'upsert' && isset($payload['product']) && is_array($payload['product'])) {
        $p = $payload['product'];
        $targetId = strval($p['id'] ?? '');

        if (empty($targetId)) {
            $p['id'] = time() . rand(100, 999);
            $targetId = strval($p['id']);
        }

        $foundIndex = -1;
        foreach ($currentProducts as $idx => $item) {
            if (strval($item['id'] ?? '') === $targetId) {
                $foundIndex = $idx;
                break;
            }
        }

        if ($foundIndex !== -1) {
            // تحديث منتج موجود
            $currentProducts[$foundIndex] = $p;
        } else {
            // إضافة منتج جديد في البداية
            array_unshift($currentProducts, $p);
        }

    } elseif ($action === 'delete' && isset($payload['id'])) {
        $delId = strval($payload['id']);
        $currentProducts = array_values(array_filter($currentProducts, function($item) use ($delId) {
            return strval($item['id'] ?? '') !== $delId;
        }));

    } elseif ($action === 'toggle' && isset($payload['id'])) {
        $togId = strval($payload['id']);
        $activeVal = !empty($payload['active']);
        foreach ($currentProducts as &$item) {
            if (strval($item['id'] ?? '') === $togId) {
                $item['active'] = $activeVal;
                break;
            }
        }
        unset($item);

    } elseif (is_array($payload) && !isset($payload['action'])) {
        // مصفوفة كاملة مباشرة
        $currentProducts = $payload;
    } elseif ($action === 'batch' && isset($payload['products']) && is_array($payload['products'])) {
        $currentProducts = $payload['products'];
    }

    // حفظ ملف JSON مع التأكد من سلامة البيانات
    $jsonFormatted = json_encode($currentProducts, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    if ($jsonFormatted === false) {
        throw new Exception('فشل في تشفير بيانات المنتجات إلى JSON');
    }

    // كتابة مؤقتة واستبدال ذري لمنع تلف الملف
    $tempDataFile = $dataFile . '.tmp.' . uniqid();
    file_put_contents($tempDataFile, $jsonFormatted);
    rename($tempDataFile, $dataFile);

    // كتابة ملف products_db.js للواجهة الأمامية
    $jsContent = "const PRODUCTS_DB = " . $jsonFormatted . ";\n";
    $tempJsFile = $jsFile . '.tmp.' . uniqid();
    file_put_contents($tempJsFile, $jsContent);
    rename($tempJsFile, $jsFile);

    // فك القفل
    flock($lockFp, LOCK_UN);
    fclose($lockFp);

    echo json_encode([
        'success' => true,
        'message' => 'تم الحفظ والمزامنة بنجاح',
        'count' => count($currentProducts),
        'products' => $currentProducts
    ]);

} catch (Exception $e) {
    if ($lockFp) {
        flock($lockFp, LOCK_UN);
        fclose($lockFp);
    }
    echo json_encode([
        'success' => false,
        'message' => 'حدث خطأ أثناء الحفظ: ' . $e->getMessage()
    ]);
}
?>
