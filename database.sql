-- ══════════════════════════════════════════════════
-- هيكل قاعدة بيانات متجر استوردلي - (Hostinger MySQL)
-- ══════════════════════════════════════════════════

CREATE TABLE IF NOT EXISTS `users` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) NOT NULL,
  `password` VARCHAR(255) DEFAULT NULL, -- يمكن أن تكون فارغة في حال التسجيل بحساب جوجل
  `phone` VARCHAR(20) DEFAULT NULL,
  `role` ENUM('customer', 'admin') DEFAULT 'customer',
  `status` ENUM('pending', 'active') DEFAULT 'pending',
  `google_id` VARCHAR(100) DEFAULT NULL,
  `reset_token` VARCHAR(100) DEFAULT NULL,
  `reset_expires` DATETIME DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `google_id` (`google_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- إدراج حساب مدير افتراضي (كلمة المرور: admin123)
-- ملاحظة: كلمة المرور مشفرة باستخدام bcrypt
INSERT IGNORE INTO `users` (`name`, `email`, `password`, `role`, `status`) VALUES
('المدير العام', 'admin@estawredly.com', '$2y$10$wN1I4O09L2M.3K4e3s0sOuWc3O1I1b1x2M5a/5g3t9o1Y8f4V9kG2', 'admin', 'active');
