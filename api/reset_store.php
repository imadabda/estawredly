<?php
session_start();
header('Content-Type: application/json');

// 1. Verify admin session
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'غير مصرح. يرجى تسجيل الدخول كمدير أولاً.']);
    exit;
}

require_once __DIR__ . '/db_connect.php';

if (!isset($pdo)) {
    echo json_encode(['success' => false, 'message' => 'فشل الاتصال بقاعدة البيانات']);
    exit;
}

try {
    $driver = $pdo->getAttribute(PDO::ATTR_DRIVER_NAME);

    // 2. Clear all orders
    $pdo->exec("DELETE FROM orders");
    
    // Reset auto-increment based on database engine
    if ($driver === 'sqlite') {
        try {
            $pdo->exec("DELETE FROM sqlite_sequence WHERE name='orders'");
        } catch (PDOException $e) {}
    } else {
        try {
            $pdo->exec("ALTER TABLE orders AUTO_INCREMENT = 1");
        } catch (PDOException $e) {}
    }

    // 3. Clear all users except the admin user(s)
    $pdo->exec("DELETE FROM users WHERE role != 'admin'");
    
    if ($driver === 'sqlite') {
        try {
            $pdo->exec("DELETE FROM sqlite_sequence WHERE name='users'");
        } catch (PDOException $e) {}
    } else {
        try {
            $pdo->exec("ALTER TABLE users AUTO_INCREMENT = 1");
        } catch (PDOException $e) {}
    }
    
    echo json_encode([
        'success' => true, 
        'message' => 'تم تصفير قاعدة البيانات بنجاح (حذف الطلبيات والمستخدمين مع الإبقاء على حساب المدير والمنتجات)'
    ]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'حدث خطأ أثناء تصفير البيانات: ' . $e->getMessage()]);
}
