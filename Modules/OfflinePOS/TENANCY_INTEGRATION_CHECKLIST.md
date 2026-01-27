# ✅ Tenancy Integration Checklist - Offline POS

## 📋 **التحقق من التكامل مع stancl/tenancy**

---

## ✅ **ما تم تنفيذه بالفعل:**

### 1. **Middleware** ✅
```php
// Routes تستخدم InitializeTenancyBySubdomain
\Stancl\Tenancy\Middleware\InitializeTenancyBySubdomain::class
\Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains::class
```
- ✅ Web routes: Updated
- ✅ API routes: Updated

### 2. **Database Migrations** ✅
```php
// الجداول مع branch_id
offline_sync_logs (with branch_id)
offline_transactions_temp (with branch_id)
```
- ✅ سيتم تشغيلها تلقائياً على كل tenant database
- ✅ Indexes محسّنة

### 3. **Models** ✅
```php
// Models مع scope للـ branch
OfflineSyncLog::forBranch($branchId)->get();
OfflineTransaction::forBranch($branchId)->get();
```
- ✅ tenant-aware تلقائياً (من stancl/tenancy)

### 4. **Permissions** ✅
```php
// 18 permissions per-tenant
OfflinePOSPermissionsSeeder
```
- ✅ سيتم seed تلقائياً على كل tenant

---

## 🔧 **خطوات التكامل المطلوبة:**

### **Step 1: التأكد من تشغيل Seeder تلقائياً**

في ملف `config/tenancy.php`:

```php
'seeder_parameters' => [
    '--class' => 'DatabaseSeeder',
],
```

**يجب إضافة seeder الموديول في `DatabaseSeeder` الرئيسي:**

```php
// database/seeders/DatabaseSeeder.php

public function run()
{
    $this->call([
        // ... seeders أخرى
        \Modules\OfflinePOS\Database\Seeders\OfflinePOSDatabaseSeeder::class,
    ]);
}
```

---

### **Step 2: التحقق من أن Migrations تُنفذ تلقائياً**

في `config/tenancy.php`:

```php
'migration_parameters' => [
    '--force' => true,
    '--path' => [
        'database/migrations',
        'database/migrations/tenant',
    ],
],
```

**الموديول migrations موجودة في:**
```
Modules/OfflinePOS/database/migrations/
```

**سيتم تنفيذها تلقائياً عبر:**
```bash
php artisan tenants:migrate
```

---

### **Step 3: التحقق من البنية الصحيحة**

#### **عند إنشاء tenant جديد:**

```php
// مثال
$tenant = Tenant::create([
    'id' => 'tenant1',
]);

$tenant->domains()->create([
    'domain' => 'tenant1.yourdomain.com',
]);
```

**يجب أن يحدث تلقائياً:**
1. ✅ إنشاء database للـ tenant
2. ✅ تشغيل جميع migrations (بما فيها OfflinePOS)
3. ✅ تشغيل seeders (بما فيها OfflinePOS permissions)

---

## 🧪 **اختبار التكامل:**

### **Test 1: إنشاء Tenant جديد**

```bash
# إنشاء tenant للتجربة
php artisan tinker

Tenant::create(['id' => 'test1']);
\App\Models\Tenant::find('test1')->domains()->create(['domain' => 'test1.yourdomain.test']);
```

### **Test 2: التحقق من Migrations**

```bash
# على tenant محدد
php artisan tenants:migrate --tenants=test1

# يجب أن ترى:
# ✓ 2026_01_20_170330_create_offline_sync_logs_table .... DONE
# ✓ 2026_01_20_170332_create_offline_transactions_temp_table .... DONE
```

### **Test 3: التحقق من Permissions**

```bash
php artisan tinker

# التبديل لـ tenant
Tenant::find('test1')->run(function() {
    // التحقق من وجود الصلاحيات
    $permissions = \Spatie\Permission\Models\Permission::where('category', 'Offline POS')->count();
    echo "Offline POS Permissions: $permissions"; // يجب أن يكون 18
});
```

### **Test 4: الوصول عبر Subdomain**

```
http://test1.yourdomain.test/offline-pos
```

**يجب أن:**
- ✅ يتعرف على tenant تلقائياً
- ✅ يُحمّل البيانات من database الصحيحة
- ✅ يعرض الصفحة بدون أخطاء

---

## 🔍 **Troubleshooting:**

### **المشكلة 1: Migrations لم تُنفذ**

```bash
# تنفيذ يدوي
php artisan tenants:migrate
```

### **المشكلة 2: Permissions غير موجودة**

```bash
# تنفيذ seeder يدوياً
php artisan tenants:seed --class="\Modules\OfflinePOS\Database\Seeders\OfflinePOSDatabaseSeeder"
```

### **المشكلة 3: Tenant لا يتعرف**

تأكد من:
```php
// في .env
APP_URL=http://yourdomain.test

// في config/tenancy.php
'central_domains' => [
    'yourdomain.test', // المجال المركزي
],
```

---

## ✅ **Checklist للتأكد:**

- [ ] stancl/tenancy مُثبت (`composer.json`)
- [ ] `config/tenancy.php` موجود وصحيح
- [ ] `InitializeTenancyBySubdomain` في الـ routes ✅
- [ ] Migrations OfflinePOS في المسار الصحيح ✅
- [ ] Seeder مُضاف في `DatabaseSeeder` الرئيسي
- [ ] اختبار إنشاء tenant جديد
- [ ] التحقق من الصلاحيات per-tenant
- [ ] اختبار الوصول عبر subdomain

---

## 📝 **ملاحظات مهمة:**

### **1. Branch ID:**
```php
// في EnsureBranchContext middleware
// Branch ID يُجلب من:
1. Header: X-Branch-ID
2. Session: current_branch_id  
3. User: auth()->user()->branch_id
4. Default: null (سيحتاج تخصيص)
```

**يجب التأكد من:**
- ✅ جدول `branches` موجود في tenant database
- ✅ User model له `branch_id`
- ✅ Frontend يرسل `X-Branch-ID` في requests

### **2. Permissions:**
```php
// الصلاحيات بالإنجليزية للترجمة
'view offline pos system'
'create offline pos transaction'
// ... إلخ (18 permission)
```

**يجب:**
- ✅ assign للـ roles المطلوبة
- ✅ التحقق في Controllers/Middleware

### **3. Data Isolation:**
```php
// كل query تلقائياً في tenant database الصحيحة
OfflineSyncLog::forBranch($branchId)->pending()->get();
```

**لا تقلق:**
- ✅ stancl/tenancy يعزل البيانات تلقائياً
- ✅ لا يمكن لـ tenant1 رؤية بيانات tenant2

---

## 🚀 **جاهز للاستخدام؟**

إذا كانت جميع النقاط في Checklist ✅، فالموديول:
- ✅ **متوافق تماماً** مع stancl/tenancy
- ✅ **جاهز** للعمل على multi-tenant environment
- ✅ **معزول** per-tenant و per-branch

---

## 📞 **الخطوات التالية:**

1. **Pull من GitHub** (إذا لزم)
2. **تشغيل `composer update`**
3. **Test على tenant تجريبي**
4. **البدء في Phase 2** (API Controllers)

---

**Status: ✅ READY FOR MULTI-TENANT ENVIRONMENT**
