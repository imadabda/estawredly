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
('المدير العام', 'admin@estawredly.com', '$2y$12$Wo0G9za0lax853eW09bCpucvrGLNaqqL4.90.UePQCaT/LJu86t4m', 'admin', 'active');
