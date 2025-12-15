# 🚀 دليل إعداد Production - نظام إعادة الحساب

## ✅ الإعدادات المكتملة

تم إعداد النظام ليعمل تلقائياً مع اختيار ذكي للطريقة المناسبة:

### 1. الإعدادات في `.env`

```env
# تفعيل Queue
QUEUE_CONNECTION=database

# تفعيل Stored Procedures للبيانات الكبيرة
USE_STORED_PROCEDURES_FOR_RECALCULATION=true
```

### 2. النظام يختار تلقائياً:

```
البيانات الصغيرة (< 5,000 صنف)
  ↓
PHP Services (مباشر)

البيانات المتوسطة (5,000 - 100,000 عملية)
  ↓
Stored Procedures (مباشر)

البيانات الكبيرة جداً (> 500,000 عملية)
  ↓
Queue Jobs (في الخلفية)
```

---

## 📋 خطوات الإعداد على السيرفر (Production)

### 1. تشغيل Migrations

```bash
php artisan migrate
```

سيتم إنشاء:
- ✅ جدول `jobs` (للـ Queue)
- ✅ Stored Procedures
- ✅ Database Indexes

### 2. إنشاء جدول Jobs (إذا لم يكن موجوداً)

```bash
php artisan queue:table
php artisan migrate
```

### 3. إعداد Supervisor (مطلوب للـ Queue Workers)

Supervisor يضمن أن Queue Workers تعمل دائماً وتعيد التشغيل تلقائياً عند الفشل.

#### أ. تثبيت Supervisor

```bash
# Ubuntu/Debian
sudo apt-get install supervisor

# CentOS/RHEL
sudo yum install supervisor
```

#### ب. إنشاء ملف إعداد Supervisor

أنشئ ملف: `/etc/supervisor/conf.d/laravel-worker.conf`

```ini
[program:laravel-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /path/to/your/project/artisan queue:work database --sleep=3 --tries=3 --max-time=3600 --queue=recalculation,recalculation-large,default
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/path/to/your/project/storage/logs/worker.log
stopwaitsecs=3600
```

**ملاحظات مهمة:**
- استبدل `/path/to/your/project` بمسار المشروع الفعلي
- `numprocs=2` يعني 2 workers (يمكن زيادتها حسب الحاجة)
- `--queue=recalculation,recalculation-large,default` يعني معالجة هذه الـ queues بالترتيب

#### ج. تفعيل Supervisor

```bash
# إعادة تحميل Supervisor
sudo supervisorctl reread
sudo supervisorctl update

# بدء Workers
sudo supervisorctl start laravel-worker:*

# التحقق من الحالة
sudo supervisorctl status
```

#### د. إدارة Workers

```bash
# إعادة تشغيل Workers (بعد تحديث الكود)
sudo supervisorctl restart laravel-worker:*

# إيقاف Workers
sudo supervisorctl stop laravel-worker:*

# عرض Logs
tail -f /path/to/your/project/storage/logs/worker.log
```

### 4. إعداد Cron Job (للمهام المجدولة - اختياري)

إذا كنت تستخدم Laravel Scheduler:

```bash
# فتح crontab
crontab -e

# إضافة السطر التالي
* * * * * cd /path/to/your/project && php artisan schedule:run >> /dev/null 2>&1
```

---

## 🔍 التحقق من أن كل شيء يعمل

### 1. التحقق من Queue

```bash
# عرض Jobs المعلقة
php artisan queue:work --once

# عرض عدد Jobs في Queue
php artisan queue:monitor
```

### 2. التحقق من Stored Procedures

```sql
-- عرض Stored Procedures
SHOW PROCEDURE STATUS WHERE Db = 'your_database_name';

-- يجب أن ترى:
-- sp_recalculate_average_cost
-- sp_recalculate_average_cost_batch
-- sp_recalculate_profit
-- sp_recalculate_profits_batch
-- sp_recalculate_all_after_operation
```

### 3. التحقق من Database Indexes

```sql
-- عرض Indexes على operation_items
SHOW INDEXES FROM operation_items;

-- يجب أن ترى:
-- idx_operation_items_cost_calc
-- idx_operation_items_pro_id
```

### 4. اختبار النظام

```bash
# اختبار إعادة الحساب يدوياً
php artisan tinker

# في Tinker:
use App\Services\RecalculationServiceHelper;
RecalculationServiceHelper::recalculateAverageCost([1, 2, 3], '2024-01-01');
```

---

## 📊 مراقبة الأداء

### 1. مراقبة Queue

```bash
# عرض Jobs المعلقة
php artisan queue:monitor

# عرض Jobs الفاشلة
php artisan queue:failed

# إعادة محاولة Jobs الفاشلة
php artisan queue:retry all
```

### 2. مراقبة Logs

```bash
# Logs التطبيق
tail -f storage/logs/laravel.log

# Logs Workers
tail -f storage/logs/worker.log
```

### 3. مراقبة قاعدة البيانات

```sql
-- عدد Jobs المعلقة
SELECT COUNT(*) FROM jobs;

-- Jobs القديمة (أكثر من ساعة)
SELECT * FROM jobs WHERE created_at < NOW() - INTERVAL 1 HOUR;

-- Jobs الفاشلة
SELECT * FROM failed_jobs;
```

---

## ⚠️ استكشاف الأخطاء

### المشكلة: Queue Workers لا تعمل

**الحل:**
1. تحقق من Supervisor:
   ```bash
   sudo supervisorctl status
   ```
2. تحقق من Logs:
   ```bash
   tail -f storage/logs/worker.log
   ```
3. أعد تشغيل Workers:
   ```bash
   sudo supervisorctl restart laravel-worker:*
   ```

### المشكلة: Stored Procedures لا تعمل

**الحل:**
1. تحقق من وجود Procedures:
   ```sql
   SHOW PROCEDURE STATUS WHERE Db = 'your_database_name';
   ```
2. إذا لم تكن موجودة، شغّل migrations:
   ```bash
   php artisan migrate
   ```
3. تحقق من الصلاحيات:
   ```sql
   GRANT EXECUTE ON PROCEDURE sp_recalculate_average_cost TO 'your_user'@'localhost';
   ```

### المشكلة: النظام بطيء

**الحل:**
1. تحقق من Database Indexes:
   ```sql
   SHOW INDEXES FROM operation_items;
   ```
2. زد عدد Queue Workers:
   ```ini
   numprocs=4  # في supervisor config
   ```
3. استخدم Redis للـ Queue (أسرع من Database):
   ```env
   QUEUE_CONNECTION=redis
   ```

---

## 🔧 الإعدادات المتقدمة

### استخدام Redis للـ Queue (أسرع)

```env
QUEUE_CONNECTION=redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

### زيادة عدد Workers

في `/etc/supervisor/conf.d/laravel-worker.conf`:

```ini
numprocs=4  # 4 workers بدلاً من 2
```

### إعدادات Timeout

في `config/queue.php`:

```php
'retry_after' => 300,  // 5 دقائق
```

---

## ✅ Checklist قبل الانتقال إلى Production

- [ ] تم تشغيل `php artisan migrate`
- [ ] تم إنشاء جدول `jobs`
- [ ] تم إنشاء Stored Procedures
- [ ] تم إنشاء Database Indexes
- [ ] تم تثبيت Supervisor
- [ ] تم إعداد Supervisor config
- [ ] تم تفعيل Supervisor workers
- [ ] تم اختبار النظام
- [ ] تم مراقبة Logs
- [ ] تم إعداد Cron (إذا لزم الأمر)

---

## 📝 ملخص

### النظام الآن:

1. ✅ **يختار تلقائياً** الطريقة المناسبة (Queue/Stored Procedures/PHP)
2. ✅ **يعمل في الخلفية** للبيانات الكبيرة (Queue Jobs)
3. ✅ **أسرع** للبيانات الكبيرة (Stored Procedures)
4. ✅ **مرن** للبيانات المتوسطة/الصغيرة (PHP Services)
5. ✅ **موثوق** (Supervisor يعيد التشغيل تلقائياً)

### لا تحتاج لأي كود إضافي:

- ✅ النظام يعمل تلقائياً عند إضافة/تعديل/حذف الفواتير
- ✅ يدعم الفواتير بتواريخ قديمة - يعيد حساب جميع الفواتير المتأثرة
- ✅ يحدّث القيود الموجودة بدلاً من حذفها وإنشاء قيود جديدة
- ✅ يختار الطريقة المناسبة تلقائياً
- ✅ يعمل في الخلفية للبيانات الكبيرة
- ✅ يسجل الأخطاء في Logs

---

## 📌 المميزات الجديدة (v2.2.0)

### 1. دعم الفواتير بتواريخ قديمة

عند إضافة فاتورة مشتريات بتاريخ قديم (مثلاً 1-12) وفاتورة مبيعات بتاريخ لاحق (مثلاً 13-12):
- ✅ يتم إعادة حساب `average_cost` تلقائياً
- ✅ يتم إعادة حساب قيد COGS في فاتورة المبيعات بالقيمة الجديدة
- ✅ يتم إعادة حساب **فقط** الفواتير التي بعد تاريخ الفاتورة المضافة (مع مراعاة الوقت في نفس اليوم)
- ✅ الفواتير التي قبل تاريخ الفاتورة المضافة لا تتأثر

### 2. تحديث القيود بدلاً من حذفها

- ✅ النظام يحدّث القيود الموجودة بدلاً من حذفها وإنشاء قيود جديدة
- ✅ يحافظ على `journal_id` الأصلي
- ✅ يضمن التكامل المحاسبي

### 3. منع تكرار قيود COGS

- ✅ عند إعادة الحساب، يتم البحث عن قيد COGS الموجود (باستخدام `op_id` أو `op2`)
- ✅ يتم تحديث قيد COGS الموجود بدلاً من إنشاء قيد جديد
- ✅ إذا كان هناك قيود COGS مكررة، يتم حذفها تلقائياً

### 4. دعم فواتير التصنيع

- ✅ فواتير التصنيع تحتوي على خامات ومنتجات
- ✅ عند إضافة/تعديل/حذف فاتورة تصنيع، يتم إعادة حساب `average_cost` للمنتجات والأرباح والقيود

---

**آخر تحديث**: ديسمبر 2024  
**الإصدار**: 2.2.0

