# 📖 Quick Reference - Offline POS

## 🎯 **للمطورين - مرجع سريع**

---

## 📊 **Database Tables**

### **Employees** (from HR module)
```php
// Model: Modules\HR\Models\Employee

// الحقول:
id, name, phone, email, position, status, branch_id, 
salary, finger_print_id, department_id, etc.

// Filtering:
Employee::where('status', 'مفعل')->get()

// Branch isolation:
Employee::where('branch_id', $branchId)->get()
```

### **Items** (core)
```php
// Model: App\Models\Item

// الحقول الأساسية:
id, code, name, info, type, average_cost

// العلاقات:
$item->units          // BelongsToMany via item_units
$item->prices         // BelongsToMany via item_prices
$item->barcodes       // HasMany
$item->notes          // BelongsToMany via item_notes

// Pivot Data:
$item->units->first()->pivot->u_val      // conversion factor
$item->units->first()->pivot->cost       // cost per unit
$item->prices->first()->pivot->price     // السعر
$item->prices->first()->pivot->discount  // الخصم
$item->prices->first()->pivot->tax_rate  // الضريبة
```

### **Barcodes**
```sql
barcodes:
  - id
  - item_id
  - unit_id
  - barcode (unique)
  - isdeleted
  - branch_id
```

### **Price Lists**
```php
// قوائم الأسعار
prices:
  - id
  - name (مثل: سعر الجملة، سعر القطاعي)
  - is_deleted

// الأسعار الفعلية للأصناف
item_prices:
  - item_id
  - price_id (أي قائمة سعرية)
  - unit_id (السعر لوحدة معينة)
  - price
  - discount
  - tax_rate
```

### **Categories (التصنيفات)**
```php
// التصنيف الرئيسي
notes:
  - id
  - name (مثل: "التصنيفات")

// التصنيف الفرعي
note_details:
  - id
  - note_id
  - name (مثل: "إلكترونيات")

// ربط الصنف بالتصنيف
item_notes:
  - item_id
  - note_id
  - note_detail_name
```

---

## 🔄 **API Endpoints**

### **InitData API**
```javascript
// Get all data
GET /api/offline-pos/init-data

// Check updates
GET /api/offline-pos/init-data/check-updates?last_sync=2026-01-20T12:00:00Z

// Get specific section
GET /api/offline-pos/init-data/section/items
GET /api/offline-pos/init-data/section/customers
GET /api/offline-pos/init-data/section/employees
```

### **Sync API**
```javascript
// Sync single transaction
POST /api/offline-pos/sync-transaction

// Batch sync
POST /api/offline-pos/batch-sync

// Check status
GET /api/offline-pos/sync-status/{localId}

// Retry failed
POST /api/offline-pos/retry-sync/{localId}

// Get pending
GET /api/offline-pos/pending-transactions
```

### **Reports API**
```javascript
GET /api/offline-pos/reports/best-sellers?from_date=...&to_date=...&limit=10
GET /api/offline-pos/reports/top-customers?from_date=...&to_date=...&limit=10
GET /api/offline-pos/reports/daily-sales?date=2026-01-20
GET /api/offline-pos/reports/sales-summary?from_date=...&to_date=...
```

### **Return Invoice API**
```javascript
POST /api/offline-pos/return-invoice
```

---

## 🔐 **Authentication**

### **Required Headers**
```http
Authorization: Bearer {sanctum_token}
X-Branch-ID: {branch_id}
Content-Type: application/json
Accept: application/json
```

### **Permissions**
```php
// View
auth()->user()->can('view offline pos system')
auth()->user()->can('view offline pos transactions')
auth()->user()->can('view offline pos reports')

// Create
auth()->user()->can('create offline pos transaction')
auth()->user()->can('create offline pos return invoice')

// Sync
auth()->user()->can('sync offline pos transactions')
auth()->user()->can('download offline pos data')

// Print
auth()->user()->can('print offline pos invoice')
auth()->user()->can('print offline pos thermal')
```

---

## 💾 **IndexedDB Schema (Frontend)**

### **Database Name**
```javascript
const dbName = `OfflinePOS_${tenantId}_${branchId}`;
// Example: OfflinePOS_tenant1_branch1
```

### **Tables (Object Stores)**
```javascript
1. items           // الأصناف
2. customers       // العملاء
3. employees       // الموظفين
4. stores          // المخازن
5. cash_boxes      // الصناديق
6. transactions    // المعاملات المحلية
7. sync_queue      // قائمة انتظار المزامنة
8. settings        // الإعدادات
9. user            // المستخدم الحالي
```

---

## 🔄 **Sync Workflow**

```
1. المستخدم يحفظ معاملة
   ↓
2. حفظ في IndexedDB (local)
   status: 'pending'
   ↓
3. إضافة في sync_queue
   ↓
4. Service Worker يراقب الاتصال
   ↓
5. عند توفر الإنترنت
   ↓
6. POST /api/offline-pos/sync-transaction
   ↓
7. السيرفر يعالج المعاملة:
   - إنشاء OperHead
   - إنشاء OperationItems
   - إنشاء JournalEntries
   - إنشاء سند قبض (إن وجد)
   ↓
8. تحديث IndexedDB:
   status: 'synced'
   server_id: 1234
   ↓
9. إزالة من sync_queue
```

---

## 🧪 **Testing Examples**

### **Test InitData API**
```javascript
const response = await fetch('/api/offline-pos/init-data', {
  headers: {
    'Authorization': 'Bearer ' + token,
    'X-Branch-ID': '1'
  }
});

const { data, metadata } = await response.json();

console.log('Items:', data.items.length);
console.log('Customers:', data.customers.length);
console.log('Execution time:', metadata.execution_time_ms + 'ms');
```

### **Test Sync**
```javascript
const transaction = {
  transaction_type: 'sale',
  date: '2026-01-20 14:30:00',
  customer_id: 61,
  store_id: 62,
  items: [{
    item_id: 100,
    unit_id: 1,
    quantity: 2,
    price: 50,
    discount: 0
  }],
  total: 100
};

const response = await fetch('/api/offline-pos/sync-transaction', {
  method: 'POST',
  headers: {
    'Authorization': 'Bearer ' + token,
    'X-Branch-ID': '1',
    'Content-Type': 'application/json'
  },
  body: JSON.stringify({
    local_id: 'uuid-xxxx-xxxx',
    transaction: transaction
  })
});

const result = await response.json();
console.log('Server ID:', result.data.server_transaction_id);
```

---

## 📝 **Common Queries**

### **Get Item with Full Details**
```php
$item = Item::with([
    'units',
    'prices',
    'barcodes' => fn($q) => $q->where('isdeleted', 0),
    'notes'
])->find($itemId);

// Access data:
$item->units->first()->pivot->u_val;     // conversion factor
$item->prices->first()->pivot->price;    // السعر
$item->barcodes->pluck('barcode');       // array of barcodes
$item->notes->first()->pivot->note_detail_name; // التصنيف
```

### **Get Stock Balance**
```php
$balance = DB::table('operation_items')
    ->where('item_id', $itemId)
    ->where('detail_store', $storeId)
    ->selectRaw('SUM(qty_in - qty_out) as quantity')
    ->value('quantity') ?? 0;
```

### **Get Customer Balance**
```php
$balance = DB::table('journal_details')
    ->where('account_id', $customerId)
    ->where('isdeleted', 0)
    ->selectRaw('SUM(debit) - SUM(credit) as balance')
    ->value('balance') ?? 0;
```

---

## 🚀 **Performance Tips**

1. **Use Caching:**
   ```php
   Cache::remember("key", now()->addMinutes(30), fn() => /* query */);
   ```

2. **Eager Loading:**
   ```php
   Item::with(['units', 'prices', 'barcodes'])->get();
   ```

3. **Batch Processing:**
   - Sync up to 50 transactions at once
   - Use batch-sync endpoint

4. **Index Usage:**
   - Items: indexed on `name`
   - Employees: indexed on `branch_id + name`, `status`
   - Barcodes: indexed on `barcode` (unique)

---

## 📦 **Dependencies**

```json
{
  "stancl/tenancy": "^3.x",
  "spatie/laravel-permission": "^5.x",
  "laravel/sanctum": "^3.x"
}
```

---

## 🔗 **Related Documentation**

- `MULTI_TENANCY.md` - دليل Multi-tenancy
- `API_DOCUMENTATION.md` - دليل API كامل
- `DATABASE_SCHEMA_FIXES.md` - تصحيحات البنية
- `TENANCY_INTEGRATION_CHECKLIST.md` - قائمة التحقق
- `CHANGELOG.md` - سجل التغييرات

---

**Last Updated:** 2026-01-20  
**Version:** 1.0.0
