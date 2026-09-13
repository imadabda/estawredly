<?php
session_start();
header('Content-Type: text/html; charset=utf-8');
require_once 'db_connect.php';

echo "<div style='font-family:sans-serif; direction:rtl; max-width:600px; margin:40px auto; padding:20px; border:1px solid #e2e8f0; border-radius:12px; box-shadow:0 4px 12px rgba(0,0,0,0.05);'>";
echo "<h2 style='color:#2563eb;'>🛠️ فحص وتحديث قاعدة بيانات هوستنجر</h2>";

try {
    // 1. جدول المستخدمين
    $pdo->exec("CREATE TABLE IF NOT EXISTS `users` (
        `id` INT(11) NOT NULL AUTO_INCREMENT,
        `name` VARCHAR(100) NOT NULL,
        `email` VARCHAR(100) NOT NULL,
        `password` VARCHAR(255) DEFAULT NULL,
        `phone` VARCHAR(20) DEFAULT NULL,
        `role` VARCHAR(20) DEFAULT 'customer',
        `status` VARCHAR(20) DEFAULT 'pending',
        `google_id` VARCHAR(100) DEFAULT NULL,
        `reset_token` VARCHAR(100) DEFAULT NULL,
        `reset_expires` DATETIME DEFAULT NULL,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        UNIQUE KEY `email` (`email`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");
    echo "<p style='color:#16a34a;'>✅ جدول المستخدمين (users) جاهز ومحدث.</p>";

    // 2. جدول الطلبيات
    $pdo->exec("CREATE TABLE IF NOT EXISTS `orders` (
        `id` INT(11) NOT NULL AUTO_INCREMENT,
        `customer_name` VARCHAR(100) NOT NULL,
        `customer_phone` VARCHAR(20) NOT NULL,
        `customer_address` TEXT NOT NULL,
        `shipping_zone` VARCHAR(50) NOT NULL,
        `items_json` JSON NOT NULL,
        `subtotal` DECIMAL(10,2) NOT NULL,
        `shipping_cost` DECIMAL(10,2) NOT NULL,
        `total_price` DECIMAL(10,2) NOT NULL,
        `status` VARCHAR(50) DEFAULT 'pending',
        `notes` TEXT DEFAULT NULL,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

    // 3. تحويل نوع حقل status في جدول الطلبيات إلى VARCHAR لمنع رفض حالات الشحن
    $pdo->exec("ALTER TABLE `orders` MODIFY COLUMN `status` VARCHAR(50) DEFAULT 'pending'");
    echo "<p style='color:#16a34a;'>✅ جدول الطلبيات (orders) جاهز وتم توسيع حالات الطلب (status) لتقبل: قيد الانتظار، تم الشحن، تم التسليم، ملغي، جاري المعالجة.</p>";

    echo "<hr style='margin:20px 0; border:0; border-top:1px solid #e2e8f0;'>";
    echo "<p style='font-weight:bold; color:#0f172a;'>🎉 تم تحديث قاعدة البيانات بنجاح 100%! يمكنك العودة للوحة الإدارة وتغيير الحالات كما تشاء.</p>";
    echo "<a href='../admin.php' style='display:inline-block; background:#2563eb; color:#fff; text-decoration:none; padding:10px 20px; border-radius:8px; font-weight:bold;'>العودة للوحة الإدارة ⬅️</a>";
} catch (PDOException $e) {
    echo "<p style='color:#dc2626;'>❌ حدث خطأ: " . $e->getMessage() . "</p>";
}
echo "</div>";
?>
