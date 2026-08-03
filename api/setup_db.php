<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
require_once 'db_connect.php';

try {
    // Check if the 'status' column exists in 'users' table
    $result = $pdo->query("SHOW COLUMNS FROM `users` LIKE 'status'");
    
    if ($result->rowCount() == 0) {
        // Column does not exist, so we add it
        $sql = "ALTER TABLE `users` ADD `status` ENUM('pending', 'active') DEFAULT 'pending' AFTER `role`";
        $pdo->exec($sql);
        
        // Make existing admins active automatically
        $pdo->exec("UPDATE `users` SET `status` = 'active' WHERE `role` = 'admin'");
        
        // Also make existing customers active so we don't break old accounts, 
        // new registrations will be pending.
        $pdo->exec("UPDATE `users` SET `status` = 'active' WHERE `role` = 'customer'");

        echo json_encode(['success' => true, 'message' => 'تم إضافة نظام طلبات العضوية بنجاح إلى قاعدة البيانات! يمكنك الآن حذف هذا الملف.']);
    } else {
        echo json_encode(['success' => true, 'message' => 'قاعدة البيانات محدثة بالفعل ولا تحتاج لأي تعديلات.']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'حدث خطأ: ' . $e->getMessage()]);
}
?>
