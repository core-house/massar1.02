# 🏢 Multi-tenancy Architecture - Offline POS

## نظرة عامة

موديول Offline POS مصمم للعمل مع `stancl/tenancy` لدعم:
- ✅ **Multi-database:** قاعدة بيانات منفصلة لكل tenant
- ✅ **Subdomain routing:** tenant1.domain.com, tenant2.domain.com
- ✅ **Branch isolation:** عزل البيانات حسب الفرع (Branch)
- ✅ **Per-tenant permissions:** الصلاحيات منفصلة لكل tenant

---

## البنية المعمارية

```
Central Database
    └── tenants table (domain, database name, etc.)

Tenant 1 Database (tenant1.domain.com)
    ├── users
    ├── permissions (per-tenant)
    ├── branches
    │   ├── Branch 1 (ID: 1)
    │   ├── Branch 2 (ID: 2)
    │   └── Branch 3 (ID: 3)
    ├── offline_sync_logs (with branch_id)
    └── offline_transactions_temp (with branch_id)

Tenant 2 Database (tenant2.domain.com)
    ├── users
    ├── permissions (per-tenant)
    ├── branches
    │   ├── Branch 1 (ID: 1)
    │   └── Branch 2 (ID: 2)
    ├── offline_sync_logs (with branch_id)
    └── offline_transactions_temp (with branch_id)
```

---

## كيفية العمل

### 1. التعرف على Tenant (Domain Detection)

```php
// يتم تلقائياً عبر Middleware من stancl/tenancy
InitializeTenancyByDomain::class
```

عند زيارة `tenant1.domain.com/offline-pos`:
1. ✅ Middleware يتعرف على tenant من subdomain
2. ✅ يتم التبديل إلى database الخاصة بالـ tenant
3. ✅ جميع الـ queries تذهب للـ database الصحيحة

### 2. عزل البيانات حسب الفرع (Branch Isolation)

```php
// في EnsureBranchContext Middleware
$branchId = $request->header('X-Branch-ID') 
            ?? session('current_branch_id')
            ?? auth()->user()->branch_id;

$request->merge(['current_branch_id' => $branchId]);
```

**مصادر branch_id:**
1. Header: `X-Branch-ID` (من frontend)
2. Session: `current_branch_id`
3. User: `auth()->user()->branch_id`
4. Default: الفرع الافتراضي للـ tenant

### 3. الصلاحيات Per-Tenant

```php
// الصلاحيات مخزنة في database كل tenant
auth()->user()->can('view offline pos system');
```

- ✅ كل tenant له صلاحيات منفصلة
- ✅ User في tenant1 لا يرى صلاحيات tenant2
- ✅ يتم seed الصلاحيات تلقائياً عند إنشاء tenant

---

## Offline Data Isolation

### IndexedDB Schema (Frontend)

```javascript
// في المتصفح، كل tenant + branch له بيانات منفصلة
const dbName = `OfflinePOS_${tenantId}_${branchId}`;

// مثال:
// OfflinePOS_tenant1_branch1
// OfflinePOS_tenant1_branch2
// OfflinePOS_tenant2_branch1
```

### Transaction Data

```javascript
{
  local_id: "uuid-xxx",
  branch_id: 1,              // ✅ معزول حسب الفرع
  customer_id: 61,
  items: [...],
  sync_status: 'pending'
}
```

### Sync Process

```javascript
// عند المزامنة
POST /api/offline-pos/sync-transaction
Headers: {
  'X-Branch-ID': 1,
  'Authorization': 'Bearer token'
}

// السيرفر:
// 1. يتعرف على tenant من domain
// 2. يتعرف على branch من header
// 3. يحفظ في database الصحيحة مع branch_id
```

---

## Routes Structure

### Web Routes
```php
// tenant1.domain.com/offline-pos
Route::middleware([
    InitializeTenancyByDomain::class,
    PreventAccessFromCentralDomains::class,
    'auth',
    EnsureBranchContext::class,
])->prefix('offline-pos')->group(...)
```

### API Routes
```php
// tenant1.domain.com/api/offline-pos
Route::middleware([
    InitializeTenancyByDomain::class,
    'auth:sanctum',
    EnsureBranchContext::class,
])->prefix('offline-pos')->group(...)
```

---

## Database Queries Examples

### جلب معاملات فرع معين

```php
// تلقائياً في database الصحيحة (من tenancy)
OfflineSyncLog::forBranch($branchId)
    ->pending()
    ->get();
```

### إنشاء معاملة جديدة

```php
OfflineSyncLog::create([
    'local_transaction_id' => 'uuid-xxx',
    'branch_id' => $request->current_branch_id, // من middleware
    'user_id' => auth()->id(),
    'transaction_data' => $data,
    'status' => 'pending',
]);
```

---

## Frontend Integration

### تحديد Branch عند تحميل البيانات

```javascript
// عند تنزيل البيانات للعمل offline
fetch('/api/offline-pos/init-data', {
    headers: {
        'X-Branch-ID': currentBranchId,
        'Authorization': 'Bearer ' + token
    }
})
```

### تحديد Branch عند المزامنة

```javascript
// عند المزامنة
const syncTransaction = async (transaction) => {
    await fetch('/api/offline-pos/sync-transaction', {
        method: 'POST',
        headers: {
            'X-Branch-ID': transaction.branch_id,
            'Content-Type': 'application/json',
            'Authorization': 'Bearer ' + token
        },
        body: JSON.stringify({
            local_id: transaction.local_id,
            transaction: transaction
        })
    });
};
```

---

## Migrations على كل Tenant

### تلقائياً

```bash
# عند إنشاء tenant جديد، يتم تشغيل migrations تلقائياً
# من إعدادات stancl/tenancy
```

### يدوياً (إذا احتجت)

```bash
# على tenant محدد
php artisan tenants:migrate --tenants=1

# على جميع tenants
php artisan tenants:migrate
```

---

## Testing Multi-tenancy

### 1. إنشاء Tenant للتجربة

```bash
php artisan tenants:create tenant1.domain.test
```

### 2. الوصول عبر Subdomain

```
http://tenant1.domain.test/offline-pos
```

### 3. اختبار Branch Isolation

```php
// في Tinker
Tenant::find('tenant1')->run(function () {
    // جميع queries هنا في database tenant1
    OfflineSyncLog::forBranch(1)->count();
    OfflineSyncLog::forBranch(2)->count();
});
```

---

## Security Considerations

### 1. Tenant Isolation ✅
- ✅ لا يمكن لـ tenant1 الوصول لبيانات tenant2
- ✅ يتم تلقائياً عبر stancl/tenancy

### 2. Branch Isolation ✅
- ✅ لا يمكن لـ branch1 رؤية معاملات branch2
- ✅ يتم عبر scope `forBranch()`

### 3. Permission Isolation ✅
- ✅ الصلاحيات per-tenant
- ✅ لا مشاركة للصلاحيات بين tenants

### 4. API Security ✅
- ✅ Sanctum authentication
- ✅ Branch ID validation
- ✅ Permission checks

---

## Troubleshooting

### مشكلة: بيانات tenant خاطئة

```php
// التحقق من tenant الحالي
dd(tenant());
```

### مشكلة: branch_id خاطئ

```php
// في Controller
dd($request->current_branch_id);
```

### مشكلة: المزامنة تذهب لـ tenant خاطئ

```javascript
// تأكد من وجود X-Branch-ID في headers
console.log(request.headers['X-Branch-ID']);
```

---

## Best Practices

1. ✅ دائماً استخدم `forBranch()` scope عند query
2. ✅ أرسل `X-Branch-ID` في كل API request
3. ✅ احفظ `branch_id` في session للاستمرارية
4. ✅ اختبر على tenants مختلفة قبل Production
5. ✅ استخدم IndexedDB منفصلة per tenant/branch

---

## الملخص

```
✅ Multi-database per tenant (stancl/tenancy)
✅ Subdomain routing (tenant1.domain.com)
✅ Branch isolation (branch_id في كل جدول)
✅ Per-tenant permissions
✅ Offline data isolated per branch
✅ Automatic tenant detection
✅ Secure API with Sanctum
```

**النظام جاهز للعمل مع Multi-tenancy! 🚀**
