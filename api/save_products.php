<?php
session_start();
header("Content-Type: application/json; charset=utf-8");
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// التأكد من تسجيل دخول الأدمن
if (!isset($_SESSION["user_role"]) || $_SESSION["user_role"] !== "admin") {
    echo json_encode(["success" => false, "message" => "غير مصرح لك بالوصول"]);
    exit;
}

// قراءة البيانات المرسلة
$rawInput = file_get_contents("php://input");
$payload = json_decode($rawInput, true);

if ($payload === null) {
    echo json_encode(["success" => false, "message" => "بيانات JSON غير صالحة"]);
    exit;
}

$dataFile = __DIR__ . "/../products_data.json";
$jsFile = __DIR__ . "/../products_db.js";
$imgDir = __DIR__ . "/../product_images/";
if (!file_exists($imgDir)) {
    @mkdir($imgDir, 0755, true);
}

// دالة لمعالجة وحفظ صور Base64 كملفات فوراً
function saveBase64Image($b64Str, $imgDir, $prefix = "img") {
    if (empty($b64Str) || !is_string($b64Str) || strpos($b64Str, "data:image") !== 0) {
        return $b64Str;
    }
    $ext = "jpg";
    if (preg_match("/data:image\/([a-zA-Z0-9]+);base64,(.+)/s", $b64Str, $m)) {
        $ext = strtolower($m[1]) === "jpeg" ? "jpg" : strtolower($m[1]);
        $b64Data = $m[2];
    } else {
        $parts = explode(",", $b64Str, 2);
        $b64Data = isset($parts[1]) ? $parts[1] : $b64Str;
    }
    $filename = $prefix . "_" . mt_rand(1000, 9999) . "." . $ext;
    $decoded = base64_decode($b64Data);
    if ($decoded && file_put_contents($imgDir . $filename, $decoded)) {
        @chmod($imgDir . $filename, 0664);
        @chown($imgDir . $filename, "www-data");
        return "product_images/" . $filename;
    }
    return $b64Str;
}

function sanitizeProductImage(&$product, $imgDir) {
    if (!empty($product["img"])) {
        $product["img"] = saveBase64Image($product["img"], $imgDir, "img_" . ($product["id"] ?? time()));
    }
    if (!empty($product["images"]) && is_array($product["images"])) {
        foreach ($product["images"] as $idx => &$galleryImg) {
            $galleryImg = saveBase64Image($galleryImg, $imgDir, "img_" . ($product["id"] ?? time()) . "_g" . $idx);
        }
        unset($galleryImg);
    }
}

// دالة لتنظيف وتوحيد التصنيفات لمنع التكرار والمسافات الزائدة
function sanitizeProductCategory(&$product) {
    if (isset($product["cat"]) && is_string($product["cat"])) {
        $c = trim($product["cat"]);
        $c = preg_replace('/\s+/u', ' ', $c);
        $map = [
            'سفنجات جلي' => 'اسفنجات جلي',
            'إسفنجات جلي' => 'اسفنجات جلي',
            'اسفنجات جلي ' => 'اسفنجات جلي',
            'سفنجات جلي ' => 'اسفنجات جلي',
            'جاروف مع فرشاي' => 'مجرفة مع فرشاة',
            'جاروف وفرشاة' => 'مجرفة مع فرشاة',
            'جاروف مع فرشاية' => 'مجرفة مع فرشاة',
            'جاروف ومكنسة' => 'جاروف مع مكنسه',
            'جاروف مع مكنسه ' => 'جاروف مع مكنسه',
            'فراشي مرحاض' => 'فرشاة مرحاض',
            'مكنسة' => 'مكانس',
            'مكانس ' => 'مكانس',
            'مناديل ' => 'مناديل',
            'منظف نوافذ ' => 'منظف نوافذ',
            'مكبس مرحاض ' => 'مكبس مرحاض',
            'قفازات' => 'قفازات يد',
            'قفازات ' => 'قفازات يد',
            'ورق المنيوم' => 'ورق المنيوم (فويل)',
            'ورق ألمنيوم' => 'ورق المنيوم (فويل)',
            'خيط أسنان (مع علبة عرض)' => 'خيط أسنان',
            'شفاط مرحاض' => 'مكبس مرحاض'
        ];
        if (isset($map[$c])) {
            $c = $map[$c];
        }
        $product["cat"] = $c;
    }
    if (isset($product["brand"]) && is_string($product["brand"])) {
        $product["brand"] = preg_replace('/\s+/u', ' ', trim($product["brand"]));
    }
}

// استخدام قفل ملف حصري لمنع تضارب الكتابة عند العمل من أكثر من جهاز في نفس اللحظة
$lockFile = __DIR__ . "/products.lock";
$lockFp = fopen($lockFile, "c+");

if (!$lockFp || !flock($lockFp, LOCK_EX)) {
    echo json_encode(["success" => false, "message" => "السيرفر مشغول حالياً بمعالجة عملية حفظ أخرى، يرجى المحاولة بعد ثوانٍ"]);
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

    $action = is_array($payload) && isset($payload["action"]) ? $payload["action"] : "batch";

    if ($action === "upsert" && isset($payload["product"]) && is_array($payload["product"])) {
        $p = $payload["product"];
        sanitizeProductImage($p, $imgDir);
        sanitizeProductCategory($p);
        $targetId = strval($p["id"] ?? "");

        if (empty($targetId)) {
            $p["id"] = time() . rand(100, 999);
            $targetId = strval($p["id"]);
        }

        $foundIndex = -1;
        foreach ($currentProducts as $idx => $item) {
            if (strval($item["id"] ?? "") === $targetId) {
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

    } elseif ($action === "delete" && isset($payload["id"])) {
        $delId = strval($payload["id"]);
        $currentProducts = array_values(array_filter($currentProducts, function($item) use ($delId) {
            return strval($item["id"] ?? "") !== $delId;
        }));

    } elseif ($action === "toggle" && isset($payload["id"])) {
        $togId = strval($payload["id"]);
        $activeVal = !empty($payload["active"]);
        foreach ($currentProducts as &$item) {
            if (strval($item["id"] ?? "") === $togId) {
                $item["active"] = $activeVal;
                break;
            }
        }
        unset($item);

    } elseif (($action === "batch" || $action === "save_all") && isset($payload["products"]) && is_array($payload["products"])) {
        // حفظ دفعة كاملة من لوحة الإدارة
        foreach ($payload["products"] as &$item) {
            sanitizeProductImage($item, $imgDir);
            sanitizeProductCategory($item);
        }
        unset($item);
        $currentProducts = $payload["products"];

    } elseif (is_array($payload) && !isset($payload["action"])) {
        // مصفوفة كاملة مباشرة
        foreach ($payload as &$item) {
            sanitizeProductImage($item, $imgDir);
            sanitizeProductCategory($item);
        }
        unset($item);
        $currentProducts = $payload;
    } elseif ($action === "import" && isset($payload["products"]) && is_array($payload["products"])) {
        $importList = $payload["products"];
        $mode = $payload["mode"] ?? "append";
        $addedCount = 0;
        $updatedCount = 0;

        if ($mode === "replace") {
            $newProducts = [];
            foreach ($importList as $p) {
                sanitizeProductImage($p, $imgDir);
                sanitizeProductCategory($p);
                if (empty($p["id"])) {
                    $p["id"] = time() . rand(100, 999);
                }
                $newProducts[] = $p;
                $addedCount++;
            }
            $currentProducts = $newProducts;
        } else {
            foreach ($importList as $p) {
                sanitizeProductImage($p, $imgDir);
                sanitizeProductCategory($p);

                $targetId = !empty($p["id"]) ? strval($p["id"]) : "";
                $targetSku = !empty($p["product_code"]) ? trim(strval($p["product_code"])) : "";

                $foundIdx = -1;
                foreach ($currentProducts as $idx => $existing) {
                    if (!empty($targetId) && strval($existing["id"] ?? "") === $targetId) {
                        $foundIdx = $idx;
                        break;
                    }
                    if (!empty($targetSku) && !empty($existing["product_code"]) && trim(strval($existing["product_code"])) === $targetSku) {
                        $foundIdx = $idx;
                        break;
                    }
                }

                if ($foundIdx !== -1) {
                    $currentProducts[$foundIdx] = array_merge($currentProducts[$foundIdx], $p);
                    $updatedCount++;
                } else {
                    if (empty($p["id"])) {
                        $p["id"] = time() . rand(100, 999);
                    }
                    array_unshift($currentProducts, $p);
                    $addedCount++;
                }
            }
        }
    }

    // حفظ ملف JSON مع التأكد من سلامة البيانات
    $jsonFormatted = json_encode($currentProducts, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    if ($jsonFormatted === false) {
        throw new Exception("فشل في تشفير بيانات المنتجات إلى JSON");
    }

    // كتابة مؤقتة واستبدال ذري لمنع تلف الملف
    $tempDataFile = $dataFile . ".tmp." . uniqid();
    file_put_contents($tempDataFile, $jsonFormatted);
    rename($tempDataFile, $dataFile);

    // كتابة ملف products_db.js للواجهة الأمامية
    $jsContent = "const PRODUCTS_DB = " . $jsonFormatted . ";\n";
    $tempJsFile = $jsFile . ".tmp." . uniqid();
    file_put_contents($tempJsFile, $jsContent);
    rename($tempJsFile, $jsFile);

    // مزامنة أي أكواد مصانع جديدة في المنتجات مع ملف data/factory_codes.json
    $fcFile = __DIR__ . '/data/factory_codes.json';
    if (file_exists($fcFile)) {
        $fcRaw = @file_get_contents($fcFile);
        $fcList = @json_decode($fcRaw, true);
        if (is_array($fcList)) {
            $knownCodes = [];
            foreach ($fcList as $c) {
                $codeStr = is_string($c) ? $c : ($c['code'] ?? '');
                if (!empty($codeStr)) $knownCodes[strtolower(trim($codeStr))] = true;
            }
            $fcChanged = false;
            foreach ($currentProducts as $p) {
                $fc = trim(strval($p['factory_code'] ?? ''));
                if (!empty($fc) && !isset($knownCodes[strtolower($fc)])) {
                    $knownCodes[strtolower($fc)] = true;
                    $fcList[] = [
                        'code' => $fc,
                        'name' => '',
                        'active' => true
                    ];
                    $fcChanged = true;
                }
            }
            if ($fcChanged) {
                @file_put_contents($fcFile, json_encode($fcList, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            }
        }
    }

    // ضبط الصلاحيات
    @chown($dataFile, "www-data");
    @chown($jsFile, "www-data");

    // فك القفل
    flock($lockFp, LOCK_UN);
    fclose($lockFp);

    $msg = "تم الحفظ والمزامنة بنجاح";
    if ($action === "import") {
        $msg = "تم استيراد المنتجات بنجاح (تمت إضافة {$addedCount} منتج جديد وتحديث {$updatedCount} منتج)";
    }

    echo json_encode([
        "success" => true,
        "message" => $msg,
        "added" => $addedCount ?? 0,
        "updated" => $updatedCount ?? 0,
        "count" => count($currentProducts),
        "products" => $currentProducts
    ]);

} catch (Exception $e) {
    if ($lockFp) {
        flock($lockFp, LOCK_UN);
        fclose($lockFp);
    }
    echo json_encode([
        "success" => false,
        "message" => "حدث خطأ أثناء الحفظ: " . $e->getMessage()
    ]);
}
?>
