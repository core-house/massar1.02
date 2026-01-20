# PWA Icons

هذا المجلد يحتوي على أيقونات التطبيق للـ PWA.

## الأيقونات المطلوبة:

يجب إنشاء الأيقونات التالية بنفس التصميم ولكن بأحجام مختلفة:

- `icon-72x72.png` - 72x72 بكسل
- `icon-96x96.png` - 96x96 بكسل
- `icon-128x128.png` - 128x128 بكسل
- `icon-144x144.png` - 144x144 بكسل
- `icon-152x152.png` - 152x152 بكسل
- `icon-192x192.png` - 192x192 بكسل
- `icon-384x384.png` - 384x384 بكسل
- `icon-512x512.png` - 512x512 بكسل

## كيفية إنشاء الأيقونات:

### الطريقة 1: استخدام أداة أونلاين

1. اذهب إلى: https://realfavicongenerator.net/ أو https://www.pwabuilder.com/
2. ارفع صورة (512x512 على الأقل)
3. قم بتنزيل جميع الأحجام

### الطريقة 2: باستخدام ImageMagick

```bash
convert logo.png -resize 72x72 icon-72x72.png
convert logo.png -resize 96x96 icon-96x96.png
convert logo.png -resize 128x128 icon-128x128.png
convert logo.png -resize 144x144 icon-144x144.png
convert logo.png -resize 152x152 icon-152x152.png
convert logo.png -resize 192x192 icon-192x192.png
convert logo.png -resize 384x384 icon-384x384.png
convert logo.png -resize 512x512 icon-512x512.png
```

### الطريقة 3: باستخدام Photoshop/GIMP

قم بإنشاء الصورة الأساسية (512x512) ثم احفظها بأحجام مختلفة.

## مواصفات التصميم:

- **الخلفية**: شفافة أو لون موحد
- **الأيقونة**: بسيطة وواضحة
- **الألوان**: تتماشى مع theme_color (#4F46E5)
- **الشكل**: مربع بزوايا دائرية (اختياري)

## ملاحظة مهمة:

في حالة عدم توفر الأيقونات، يمكن استخدام أيقونة افتراضية مؤقتة.
يمكنك إنشاء أيقونة بسيطة بنص "POS" فقط كبداية.
