# 📋 مرجع سريع: Database Fresh Safe Commands

## 🚀 الأمر الأساسي

```bash
php artisan db:fresh-safe
```
**يفعل:** Fresh + استعادة البيانات تلقائياً

---

## 📌 الخيارات السريعة

### 1. استثناء جداول
```bash
php artisan db:fresh-safe --tables=cache,sessions
```
**يفعل:** يحذف بيانات Cache و Sessions

---

### 2. مع Seeders
```bash
php artisan db:fresh-safe --seed
```
**يفعل:** Fresh + استعادة + Seeders

---

### 3. ضغط النسخة
```bash
php artisan db:fresh-safe --compress
```
**يفعل:** يضغط النسخة الاحتياطية (70-90% أصغر)

---

### 4. حجم الدفعة
```bash
php artisan db:fresh-safe --chunk-size=5000
```
**يفعل:** يعالج 5000 صف في كل مرة (للجداول الكبيرة)

---

### 5. تخطي الجداول الكبيرة
```bash
php artisan db:fresh-safe --skip-large
```
**يفعل:** يتخطى الجداول التي تحتوي على أكثر من 10,000 صف

---

## 🎯 أمثلة شائعة

### استخدام عادي
```bash
php artisan db:fresh-safe
```

### للبيانات الكبيرة
```bash
php artisan db:fresh-safe --compress --chunk-size=5000
```

### مع Seeders
```bash
php artisan db:fresh-safe --seed
```

### كل الخيارات
```bash
php artisan db:fresh-safe --tables=cache,sessions --seed --compress --chunk-size=3000
```

---

## ⚠️ تحذيرات

- ⚠️ **لا تستخدم على الإنتاج** بدون نسخة احتياطية
- ⚠️ **اختبر أولاً** على بيئة التطوير
- ✅ **النسخ الاحتياطية** تُحفظ في `storage/app/backups/`

---

## 📚 للتفاصيل الكاملة

راجع: `Docs/database-fresh-safe-commands.md`

