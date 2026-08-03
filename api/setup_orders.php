<?php
require_once 'db_connect.php';

try {
    $sql = "CREATE TABLE IF NOT EXISTS `orders` (
      `id` INT(11) NOT NULL AUTO_INCREMENT,
      `customer_name` VARCHAR(100) NOT NULL,
      `customer_phone` VARCHAR(20) NOT NULL,
      `customer_address` TEXT NOT NULL,
      `shipping_zone` VARCHAR(50) NOT NULL,
      `items_json` JSON NOT NULL,
      `subtotal` DECIMAL(10,2) NOT NULL,
      `shipping_cost` DECIMAL(10,2) NOT NULL,
      `total_price` DECIMAL(10,2) NOT NULL,
      `status` ENUM('pending', 'processing', 'completed', 'cancelled') DEFAULT 'pending',
      `notes` TEXT DEFAULT NULL,
      `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

    $pdo->exec($sql);
    echo "<h1>تم إنشاء جدول الطلبات بنجاح! 🎉</h1>";
    echo "<p>يمكنك الآن إغلاق هذه الصفحة والعودة للوحة الإدارة.</p>";
} catch(PDOException $e) {
    echo "<h1>حدث خطأ:</h1>";
    echo "<p>" . $e->getMessage() . "</p>";
}
?>
