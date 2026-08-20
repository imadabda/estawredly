<?php
// إعداد الترويسات للتعامل مع طلبات JSON
header('Content-Type: application/json; charset=utf-8');
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// استدعاء ملف الاتصال بقاعدة البيانات
// (ملاحظة: لقد قمنا سابقاً بإزالة الهيدر من db_connect.php، لذا نضعه هنا)
require_once 'db_connect.php';

// التأكد من أن الطلب POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'طريقة الطلب غير صحيحة.']);
    exit;
}

// قراءة البيانات المرسلة كـ JSON
$inputJSON = file_get_contents('php://input');
$input = json_decode($inputJSON, true);

if (!$input) {
    echo json_encode(['success' => false, 'message' => 'بيانات غير صالحة.']);
    exit;
}

// استخراج البيانات والتحقق من الحقول الأساسية
$name = trim($input['name'] ?? '');
$phone = trim($input['phone'] ?? '');
$address = trim($input['address'] ?? '');
$zone = trim($input['zone'] ?? '');
$notes = trim($input['notes'] ?? '');
$items = $input['items'] ?? [];
$subtotal = floatval($input['subtotal'] ?? 0);
$shipping_cost = floatval($input['shipping_cost'] ?? 0);
$total = floatval($input['total'] ?? 0);

// التحقق من أن رقم الهاتف والاسم والتوصيل موجودين
if (empty($name) || empty($phone) || empty($zone) || empty($items)) {
    echo json_encode(['success' => false, 'message' => 'الاسم، رقم الهاتف، ومنطقة التوصيل حقول إجبارية.']);
    exit;
}

try {
    // تحويل المنتجات إلى JSON نصي
    $items_json = json_encode($items, JSON_UNESCAPED_UNICODE);

    // إدخال الطلب في قاعدة البيانات
    $stmt = $pdo->prepare("INSERT INTO `orders` 
        (`customer_name`, `customer_phone`, `customer_address`, `shipping_zone`, `items_json`, `subtotal`, `shipping_cost`, `total_price`) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    
    $stmt->execute([
        $name,
        $phone,
        $address,
        $zone,
        $items_json,
        $subtotal,
        $shipping_cost,
        $total
    ]);

    $orderId = $pdo->lastInsertId();

    // -- تحديث المخزون --
    $dataFile = '../products_data.json';
    $jsFile = '../products_db.js';
    $stock_debug = [];
    if (file_exists($dataFile)) {
        $productsData = json_decode(file_get_contents($dataFile), true);
        if (is_array($productsData)) {
            $stockChanged = false;
            foreach ($items as $item) {
                if (isset($item['id']) && isset($item['qty'])) {
                    foreach ($productsData as &$p) {
                        if ((string)$p['id'] === (string)$item['id']) {
                            if (isset($p['stock']) && $p['stock'] !== null && $p['stock'] !== '') {
                                $oldStock = intval($p['stock']);
                                $newStock = max(0, $oldStock - intval($item['qty']));
                                $p['stock'] = $newStock;
                                $stockChanged = true;
                                $stock_debug[] = "Item {$item['id']} stock changed from $oldStock to $newStock";
                            }
                            break;
                        }
                    }
                }
            }
            if ($stockChanged) {
                $w1 = file_put_contents($dataFile, json_encode($productsData, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
                $jsContent = "const PRODUCTS_DB = " . json_encode($productsData, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . ";\n";
                $w2 = file_put_contents($jsFile, $jsContent);
                if ($w1 === false || $w2 === false) {
                    $stock_debug[] = "Failed to write files! Permissions issue.";
                }
            }
        }
    }

    echo json_encode([
        'success' => true,
        'message' => 'تم استلام طلبك بنجاح!',
        'order_id' => $orderId,
        'stock_debug' => $stock_debug
    ]);

} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'حدث خطأ أثناء حفظ الطلب: ' . $e->getMessage()
    ]);
}
?>
