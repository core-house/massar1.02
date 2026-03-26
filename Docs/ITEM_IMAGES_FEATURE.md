# ميزة إضافة صور للأصناف

## نظرة عامة
تم إضافة ميزة رفع وإدارة الصور للأصناف في نظام Massar ERP باستخدام مكتبة Spatie Media Library.

## المميزات

### 1. رفع الصور
- **صورة رئيسية (Thumbnail)**: صورة واحدة تمثل الصنف وتظهر في القوائم والتقارير
- **صور إضافية**: عدة صور من زوايا مختلفة للصنف

### 2. معالجة الصور التلقائية
يتم إنشاء 3 نسخ من كل صورة تلقائياً:
- **thumb**: 150x150 بكسل (للعرض في القوائم)
- **preview**: 400x400 بكسل (للمعاينة السريعة)
- **large**: 800x800 بكسل (للعرض الكامل)

### 3. القيود والمواصفات
- **الصيغ المدعومة**: JPG, PNG, GIF, WEBP
- **الحد الأقصى للحجم**: 2MB لكل صورة
- **عدد الصور**: صورة رئيسية واحدة + عدد غير محدود من الصور الإضافية

## الاستخدام

### إضافة صنف جديد مع صور
1. انتقل إلى **الأصناف > إضافة صنف جديد**
2. املأ البيانات الأساسية للصنف
3. في قسم **صور الصنف**:
   - اختر الصورة الرئيسية من جهازك
   - اختر صور إضافية (يمكنك اختيار عدة صور في نفس الوقت)
4. معاينة الصور قبل الحفظ
5. احفظ الصنف

### تعديل صور صنف موجود
1. انتقل إلى **الأصناف > قائمة الأصناف**
2. اضغط على زر **تعديل** للصنف المطلوب
3. في قسم **صور الصنف**:
   - عرض الصور الحالية
   - حذف صور غير مرغوبة بالضغط على أيقونة الحذف
   - إضافة صور جديدة
4. احفظ التعديلات

### عرض الصور في القائمة
- تظهر الصورة المصغرة (thumbnail) في عمود الصورة
- اضغط على الصورة لعرضها بحجم أكبر في نافذة جديدة
- إذا لم يكن للصنف صورة، تظهر أيقونة افتراضية

## البنية التقنية

### الموديل (Model)
```php
// app/Models/Item.php
class Item extends Model implements HasMedia
{
    use InteractsWithMedia;

    public function registerMediaCollections(): void
    {
        // الصورة الرئيسية (صورة واحدة فقط)
        $this->addMediaCollection('item-thumbnail')
            ->singleFile();

        // الصور الإضافية (متعددة)
        $this->addMediaCollection('item-images');
    }

    public function registerMediaConversions(Media $media = null): void
    {
        // إنشاء نسخ مصغرة
        $this->addMediaConversion('thumb')->width(150)->height(150);
        $this->addMediaConversion('preview')->width(400)->height(400);
        $this->addMediaConversion('large')->width(800)->height(800);
    }
}
```

### الـ Component
```php
// في create-item.blade.php و edit-item.blade.php
use Livewire\WithFileUploads;

public $itemThumbnail = null;
public $itemImages = [];
```

### قاعدة البيانات
- جدول `media`: يحتوي على معلومات جميع الصور
- العلاقة: `morphs('model')` - علاقة polymorphic مع Item

## الاختبارات

تم إنشاء اختبارات شاملة في `tests/Feature/ItemImageUploadTest.php`:
- رفع صورة رئيسية
- رفع صور متعددة
- التأكد من أن الصورة الرئيسية واحدة فقط
- إنشاء النسخ المصغرة
- حذف الصور عند حذف الصنف
- عرض صورة افتراضية عند عدم وجود صورة

### تشغيل الاختبارات
```bash
php artisan test --filter ItemImageUploadTest
```

## نصائح للاستخدام الأمثل

### للمستخدمين
1. **استخدم صوراً واضحة وعالية الجودة**
2. **الصورة الرئيسية يجب أن تكون معبرة عن الصنف**
3. **أضف صوراً من زوايا مختلفة في الصور الإضافية**
4. **تأكد من حجم الصورة أقل من 2MB**

### للمطورين
1. **استخدم `getFirstMedia('item-thumbnail')` للحصول على الصورة الرئيسية**
2. **استخدم `getMedia('item-images')` للحصول على جميع الصور الإضافية**
3. **استخدم `getUrl('thumb')` للحصول على رابط النسخة المصغرة**
4. **استخدم `getFirstMediaUrl('item-thumbnail')` للحصول على رابط مع fallback تلقائي**

## الملفات المعدلة

### Backend
- `app/Models/Item.php` - إضافة دعم Media Library
- `composer.json` - إضافة حزمة spatie/laravel-medialibrary

### Frontend
- `resources/views/livewire/item-management/items/create-item.blade.php`
- `resources/views/livewire/item-management/items/edit-item.blade.php`
- `resources/views/livewire/item-management/items/index.blade.php`
- `resources/views/livewire/item-management/items/partials/image-upload.blade.php` (جديد)

### اللغات
- `resources/lang/ar/items.php` - إضافة ترجمات عربية
- `resources/lang/en/items.php` - إضافة ترجمات إنجليزية

### قاعدة البيانات
- `database/migrations/xxxx_create_media_table.php` - جدول الصور

### الاختبارات
- `tests/Feature/ItemImageUploadTest.php` - اختبارات الميزة

## استكشاف الأخطاء

### الصورة لا تظهر
1. تأكد من تشغيل `php artisan storage:link`
2. تحقق من صلاحيات مجلد `storage/app/public`
3. تأكد من أن `FILESYSTEM_DISK=public` في ملف `.env`

### خطأ في رفع الصورة
1. تحقق من حجم الصورة (يجب أن يكون أقل من 2MB)
2. تأكد من صيغة الصورة (JPG, PNG, GIF, WEBP فقط)
3. تحقق من إعدادات PHP: `upload_max_filesize` و `post_max_size`

### النسخ المصغرة لا تُنشأ
1. تأكد من تثبيت GD أو Imagick في PHP
2. تحقق من صلاحيات الكتابة في مجلد storage

## الإصدار
- **التاريخ**: ديسمبر 2025
- **الإصدار**: 1.0
- **المطور**: Massar ERP Team

