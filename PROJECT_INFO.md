# 🌐 معلومات مشروع استوردلي | ESTAWREDLI Project Documentation

---

## 📌 1. نظرة عامة على المشروع (Project Overview)
- **اسم المنصة**: استوردلي (ESTAWREDLI)
- **الرابط الرسمي**: [https://estawredli.com/](https://estawredli.com/)
- **الشعار الرسمي المعتمد**:
  > **استوردلي | منصتك للوصول إلى آلاف المنتجات من المصانع والموردين في الصين**  
  > *نقرّب لك المصانع، نسهّل عليك الطلب، ونساعدك على بناء تجارتك من الصين إلى أسواقك. استورد بسهولة… وتوسّع بثقة مع استوردلي.*
- **التقنيات المستخدمة**:
  - **الواجهة الأمامية (Frontend)**: HTML5, CSS3 (Vanilla CSS, Modern UI, Responsive Design), Vanilla JavaScript.
  - **الواجهة الخلفية (Backend & APIs)**: PHP 8.3.
  - **قاعدة البيانات (Database)**: MariaDB 10.11 / MySQL + JSON Data Storage.
  - **الخادم وحماية الاتصال (Web Server & Security)**: Apache 2.4 مع تفعيل mod_rewrite و SSL Let's Encrypt (HTTPS).

---

## 🖥️ 2. بيانات الدخول إلى السيرفر (Server Access & Credentials)

| البيان | القيمة / التفاصيل |
| :--- | :--- |
| **نوع الخادم** | خادم سحابي VPS (DigitalOcean Droplet - Ubuntu 24.04 LTS) |
| **عنوان الآي بي (Server IP)** | `188.166.79.227` |
| **منفذ الاتصال (SSH Port)** | `22` |
| **اسم المستخدم (Username)** | `root` |
| **كلمة مرور السيرفر (Password)** | `AdminUser99s` |
| **أمر الاتصال عبر الطرفية (SSH)** | `ssh root@188.166.79.227` |
| **مسار ملفات الموقع (Web Root)** | `/var/www/estawredli/` |
| **ملف إعدادات أباتشي (Apache Conf)** | `/etc/apache2/sites-available/estawredli-le-ssl.conf` |
| **سجلات الأخطاء (Error Logs)** | `/var/log/apache2/estawredli_error.log` |

---

## 🗄️ 3. بيانات قاعدة البيانات (Database Credentials)

| البيان | القيمة / التفاصيل |
| :--- | :--- |
| **محرك قاعدة البيانات** | MariaDB / MySQL |
| **المضيف (Host)** | `localhost` (أو `127.0.0.1`) |
| **اسم قاعدة البيانات (DB Name)** | `u515868829_store` |
| **اسم مستخدم القاعدة (DB User)** | `u515868829_estawredly_usr` |
| **كلمة مرور القاعدة (DB Password)** | `Estawredly@2026#DB` |
| **ملف الاتصال بالبرمجة** | `api/db_connect.php` |
| **أمر الدخول المباشر لقاعدة البيانات** | `mysql -u u515868829_estawredly_usr -p'Estawredly@2026#DB' u515868829_store` |

---

## 🔐 4. بيانات لوحة الإدارة والتحكم (Admin Dashboard)

| البيان | القيمة / التفاصيل |
| :--- | :--- |
| **رابط تسجيل دخول الإدارة** | [https://estawredli.com/admin-login.php](https://estawredli.com/admin-login.php) |
| **رابط اللوحة المباشر** | [https://estawredli.com/admin.php](https://estawredli.com/admin.php) |
| **البريد الإلكتروني الافتراضي (Email)** | `admin@estawredly.com` |
| **كلمة المرور الافتراضية (Password)** | `admin123` |
| **صلاحيات الحساب** | `admin` (المدير العام) |

---

## 🐙 5. مستودعات المشروع على GitHub (Git Repositories)

- **المستودع الأساسي (Primary Remote - Origin)**:
  - الرابط: [https://github.com/imadabda/estawredly.git](https://github.com/imadabda/estawredly.git)
  - الفرع الرئيسي: `main`
- **المستودع الاحتياطي (Backup Remote - Origin 2)**:
  - الرابط: [https://github.com/imadabda/estawredly-store.git](https://github.com/imadabda/estawredly-store.git)
  - الفرع الرئيسي: `main`
- **أوامر الرفع والتحديث**:
  ```bash
  git add -A
  git commit -m "Update project"
  git push origin main
  git push origin2 main
  ```

---

## 🛠️ 6. أوامر الصيانة والإدارة الشائعة على السيرفر (Server Maintenance Commands)

### 1. إعادة تشغيل الخدمات:
```bash
# إعادة تشغيل خادم الويب Apache
systemctl restart apache2

# إعادة تشغيل خادم قواعد البيانات MariaDB
systemctl restart mariadb
```

### 2. ضبط الصلاحيات والأذونات (مهم بعد رفع ملفات جديدة):
```bash
chown -R www-data:www-data /var/www/estawredli
chmod -R 755 /var/www/estawredli
chmod -R 775 /var/www/estawredli/api/data
chmod -R 775 /var/www/estawredli/product_images
```

### 3. مراقبة سجلات السيرفر والأخطاء لحظياً:
```bash
tail -f /var/log/apache2/estawredli_error.log
```

### 4. أخذ نسخة احتياطية سريعة لقاعدة البيانات:
```bash
mysqldump -u u515868829_estawredly_usr -p'Estawredly@2026#DB' u515868829_store > backup_$(date +%Y%m%d).sql
```

---

## 📂 7. هيكلية الملفات والمجلدات الرئيسية (File Structure)

- `index.html`: الصفحة الرئيسية للمتجر (سلايدر، عروض اليوم، التصنيفات، وصل حديثاً، الأكثر مبيعاً).
- `shop.html`: صفحة المتجر الشاملة مع الفلترة الذكية بالتصنيفات، الماركات، الأسعار، وحالة المخزون.
- `product.html`: صفحة تفاصيل المنتج ومعرض الصور، والمنتجات ذات الصلة والطلب المباشر.
- `admin.php`: لوحة التحكم الإدارية الشاملة لإدارة المنتجات، الطلبات، العملاء، والماركات.
- `admin-login.php`: بوابة تسجيل الدخول الآمنة للوحة التحكم.
- `store.js`: المحرك المركزي لبيانات المتجر والأسعار وإخفاء الماركات المعطلة وتحويل العملات.
- `main.js`: تفاعلات الواجهة الأمامية، عربة التسوق، المفضلة، والأحداث التفاعلية.
- `products_data.json` & `products_db.js`: قاعدة بيانات المنتجات السريعة للواجهة.
- `api/`: المجلد البرمجي للواجهات الخلفية وربط قاعدة البيانات:
  - `db_connect.php`: كود الربط بقاعدة البيانات MySQL / MariaDB.
  - `save_products.php`: حفظ وتحديث وحذف المنتجات بالدفعة والفردي.
  - `get_brands.php` & `save_brands.php`: إدارة الماركات وحالاتها.
  - `data/`: تخزين ملفات إعدادات الموقع (البانرات، التصنيفات، سياسات الاستخدام، الماركات).
- `product_images/`: المجلد المخصص لصور المنتجات والمعارض.
- `sitemap.xml` & `robots.txt`: ملفات الفهرسة والأرشفة لمحركات البحث مثل Google.

---

*تم التوثيق والاعتماد — منصة استوردلي © 2026*
