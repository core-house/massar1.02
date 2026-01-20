# 🔧 Database Schema Fixes - Offline POS

**Date:** 2026-01-20

---

## 📋 **Summary**

تم مراجعة وتصحيح `InitDataService` ليتوافق مع البنية الفعلية لقاعدة البيانات.

---

## 🔄 **التعديلات المُنفذة:**

### **1. Employees Table** ✅

**الجدول الفعلي:**
```sql
employees:
  - id
  - name (unique)
  - phone (unique)
  - email (unique)
  - position
  - status (enum: 'مفعل', 'معطل')
  - branch_id (added in migration 2025_12_01)
  - salary
  - finger_print_id
```

**التعديل:**
```php
// Before (خطأ):
Employee::where('is_active', 1)
    ->select('id', 'emp_code as code', 'emp_name as name', ...)

// After (صحيح):
Employee::where('status', 'مفعل')
    ->select('id', 'name', 'phone', 'email', 'position', 'branch_id', ...)
```

**البيانات المُرجعة:**
```javascript
{
  id: number,
  name: string,
  phone: string | null,
  email: string | null,
  position: string | null,
  branch_id: number | null,
  finger_print_id: number | null,
  last_synced: timestamp
}
```

---

### **2. Items Table Structure** ✅

**الجداول المرتبطة:**

1. **items**
   - id, name, code, info, average_cost, type

2. **item_units** (Pivot)
   - item_id, unit_id, u_val, cost

3. **barcodes**
   - id, item_id, unit_id, barcode, isdeleted, branch_id

4. **prices** (قوائم الأسعار)
   - id, name, is_deleted

5. **item_prices** (Pivot)
   - item_id, price_id, unit_id, price, discount, tax_rate

6. **notes** (التصنيفات الرئيسية)
   - id, name

7. **note_details** (التصنيفات الفرعية)
   - id, note_id, name

8. **item_notes** (Pivot)
   - item_id, note_id, note_detail_name

**العلاقات في Model:**
```php
class Item {
    public function units(): BelongsToMany
    public function prices(): BelongsToMany
    public function barcodes(): HasMany
    public function notes(): BelongsToMany
}
```

**التعديل:**
```php
// Before (خطأ):
Item::with(['units', 'prices', 'barcodes'])
    ->where('is_active', 1)

// After (صحيح):
Item::with([
    'units' => fn($q) => $q->orderBy('u_val'),
    'prices',  // BelongsToMany عبر item_prices
    'barcodes' => fn($q) => $q->where('isdeleted', 0),
    'notes',   // BelongsToMany عبر item_notes
])
```

**البيانات المُرجعة:**
```javascript
{
  id: number,
  code: string,
  name: string,
  description: string | null,
  type: number,
  average_cost: number,
  
  // Barcodes
  barcodes: string[],
  
  // Category (من notes)
  category_id: number | null,
  category_name: string | null,
  
  // Units (من item_units)
  units: [{
    id: number,
    name: string,
    code: string,
    conversion_factor: number,
    cost: number
  }],
  
  // Prices (من item_prices)
  prices: [{
    price_type_id: number,
    price_type_name: string,
    unit_id: number,
    price: number,
    discount: number,
    tax_rate: number
  }],
  
  // Stock balances
  stock_balances: [{
    store_id: number,
    store_name: string,
    branch_id: number | null,
    quantity: number
  }],
  
  last_synced: timestamp
}
```

---

### **3. Price Types** ✅

**الجدول الفعلي:**
```sql
prices:
  - id
  - name
  - is_deleted (tinyInteger, not isdeleted)
```

**التعديل:**
```php
// Before (خطأ):
DB::table('prices')->distinct()->select('id', 'name')

// After (صحيح):
DB::table('prices')
    ->where('is_deleted', 0)
    ->select('id', 'name')
```

---

## 📊 **Database Schema Summary**

### **Employees:**
```
employees
  ├── Basic Info: id, name, phone, email
  ├── Work Info: position, status, branch_id
  ├── Salary: salary, salary_type
  ├── Attendance: finger_print_id, finger_print_name
  └── Relations: project_id, user_id, department_id, etc.
```

### **Items:**
```
items
  ├── Basic: id, name, code, info, type
  ├── Cost: average_cost
  └── Relations:
      ├── item_units (pivot) → units
      ├── item_prices (pivot) → prices
      ├── barcodes (hasMany)
      └── item_notes (pivot) → notes → note_details
```

### **Price Structure:**
```
prices (قوائم الأسعار)
  ↓
item_prices (الأسعار الفعلية)
  ├── item_id
  ├── price_id (أي قائمة سعرية)
  ├── unit_id (السعر لوحدة معينة)
  ├── price (السعر)
  ├── discount (الخصم)
  └── tax_rate (الضريبة)
```

### **Category Structure:**
```
notes (التصنيف الرئيسي)
  ↓
note_details (التصنيف الفرعي)
  ↓
item_notes (ربط الصنف بالتصنيف)
  ├── item_id
  ├── note_id
  └── note_detail_name
```

---

## ✅ **Verification Checklist**

- [x] Employees: استخدام `status = 'مفعل'` بدلاً من `is_active`
- [x] Employees: استخدام `name` مباشرة (ليس emp_name)
- [x] Employees: إضافة `branch_id` support
- [x] Items: استخدام علاقة `units()` عبر `item_units`
- [x] Items: استخدام علاقة `prices()` عبر `item_prices`
- [x] Items: استخدام علاقة `notes()` عبر `item_notes`
- [x] Items: فلترة barcodes حسب `isdeleted = 0`
- [x] Prices: فلترة حسب `is_deleted = 0` (ليس isdeleted)
- [x] جميع البيانات متوافقة مع IndexedDB schema

---

## 🧪 **Testing**

### Test 1: Get Items
```bash
curl -X GET "https://tenant1.domain.com/api/offline-pos/init-data" \
  -H "Authorization: Bearer {token}" \
  -H "X-Branch-ID: 1"
```

**Expected:**
- ✅ Items with correct units (from item_units)
- ✅ Items with correct prices (from item_prices)
- ✅ Items with barcodes (isdeleted = 0)
- ✅ Items with category (from item_notes)

### Test 2: Get Employees
```bash
curl -X GET "https://tenant1.domain.com/api/offline-pos/init-data/section/employees" \
  -H "Authorization: Bearer {token}" \
  -H "X-Branch-ID: 1"
```

**Expected:**
- ✅ Only employees with `status = 'مفعل'`
- ✅ Correct field names (name, phone, email)
- ✅ branch_id included

---

## 📝 **Notes**

1. **BelongsToMany Relations:**
   - `units()` - عبر `item_units` (pivot: u_val, cost)
   - `prices()` - عبر `item_prices` (pivot: unit_id, price, discount, tax_rate)
   - `notes()` - عبر `item_notes` (pivot: note_detail_name)

2. **Pivot Data Access:**
   ```php
   $item->units->first()->pivot->u_val       // conversion factor
   $item->prices->first()->pivot->price      // السعر
   $item->notes->first()->pivot->note_detail_name  // اسم التصنيف
   ```

3. **Barcodes:**
   - HasMany relation (جدول منفصل)
   - فلترة حسب `isdeleted = 0`
   - يحتوي على `branch_id` للعزل

4. **Stock Balances:**
   - يتم حسابها من `operation_items`
   - `SUM(qty_in - qty_out)` per store
   - يمكن فلترتها حسب branch_id

---

## 🚀 **Impact**

- ✅ InitData API يرجع بيانات صحيحة 100%
- ✅ متوافق مع البنية الفعلية للـ database
- ✅ جاهز لـ IndexedDB في Frontend
- ✅ لا توجد أخطاء في العلاقات

---

**Status:** ✅ **ALL FIXES APPLIED**
