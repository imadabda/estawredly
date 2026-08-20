<?php
// ملف الاتصال بقاعدة بيانات Hostinger

// ضع بيانات قاعدة البيانات الخاصة بك هنا عندما ترفع الملفات إلى Hostinger
$db_host = 'localhost'; 
$db_user = 'u515868829_estawredly_usr'; // استبدله باسم مستخدم قاعدة بيانات Hostinger
$db_pass = 'Estawredly@2026#DB'; // استبدله بكلمة مرور قاعدة بيانات Hostinger
$db_name = 'u515868829_store'; // استبدله باسم قاعدة بيانات Hostinger

// إعداد الترويسات (Headers)
// تمت إزالة هيدر JSON من هنا لكي تعمل صفحات HTML بشكل صحيح

try {
    // حاول الاتصال بخادم MySQL (بيئة الإنتاج / Hostinger أو MySQL محلي)
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8mb4", $db_user, $db_pass, [
        PDO::ATTR_TIMEOUT => 2,
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
    // عند عدم توفر خادم MySQL، التبديل تلقائياً لقاعدة بيانات SQLite محلية للتطوير
    try {
        $sqlite_file = __DIR__ . '/local_database.sqlite';
        $pdo = new PDO("sqlite:" . $sqlite_file);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

        // إنشاء جدول المستخدمين إن لم يكن موجوداً
        $pdo->exec("CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            email TEXT NOT NULL UNIQUE,
            password TEXT DEFAULT NULL,
            phone TEXT DEFAULT NULL,
            role TEXT DEFAULT 'customer',
            status TEXT DEFAULT 'pending',
            google_id TEXT UNIQUE,
            reset_token TEXT,
            reset_expires DATETIME,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");

        // إنشاء جدول الطلبات إن لم يكن موجوداً
        $pdo->exec("CREATE TABLE IF NOT EXISTS orders (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            customer_name TEXT NOT NULL,
            customer_phone TEXT NOT NULL,
            customer_address TEXT NOT NULL,
            shipping_zone TEXT NOT NULL,
            items_json TEXT NOT NULL,
            subtotal REAL NOT NULL,
            shipping_cost REAL NOT NULL,
            total_price REAL NOT NULL,
            status TEXT DEFAULT 'pending',
            notes TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");

        // إدراج المدير الافتراضي إن لم يكن موجوداً (password: admin123)
        $admin_hash = password_hash('admin123', PASSWORD_DEFAULT);
        $checkAdmin = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $checkAdmin->execute(['admin@estawredly.com']);
        if (!$checkAdmin->fetch()) {
            $insertAdmin = $pdo->prepare("INSERT INTO users (name, email, password, role, status) VALUES (?, ?, ?, 'admin', 'active')");
            $insertAdmin->execute(['المدير العام', 'admin@estawredly.com', $admin_hash]);
        }
    } catch (PDOException $sqlite_err) {
        $pdo = null;
        $db_error_message = $sqlite_err->getMessage();
        
        if (strpos($_SERVER['REQUEST_URI'] ?? '', '/api/') !== false) {
            echo json_encode([
                'success' => false,
                'message' => 'فشل الاتصال بقاعدة البيانات: ' . $db_error_message
            ]);
            exit;
        }
    }
}
?>
