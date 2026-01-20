# دليل تثبيت Multi-Tenancy (Stancl/Tenancy)

## 📦 الخطوة 1: تثبيت Package

```bash
composer require stancl/tenancy
```

## ⚙️ الخطوة 2: نشر الملفات

```bash
php artisan tenancy:install
php artisan migrate
```

## 🔧 الخطوة 3: تفعيل Middleware عالمياً في `bootstrap/app.php`

```php
<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Middleware;
use Stancl\Tenancy\Middleware\InitializeTenancyBySubdomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // ✅ تطبيق Tenancy على جميع Web Routes
        $middleware->web(append: [
            InitializeTenancyBySubdomain::class,
            PreventAccessFromCentralDomains::class,
            \App\Http\Middleware\SetLocale::class,
            \App\Http\Middleware\PersistSidebarSelection::class,
        ]);

        // ✅ تطبيق Tenancy على جميع API Routes
        $middleware->api(append: [
            InitializeTenancyBySubdomain::class,
            PreventAccessFromCentralDomains::class,
        ]);

        $middleware->alias([
            'employee.auth' => \Modules\HR\Http\Middleware\EmployeeAuth::class,
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
        ]);
    })
    ->withExceptions(function ($exceptions) {
        //
    })
    ->create();
```

## 🗄️ الخطوة 4: تكوين Database في `config/tenancy.php`

```php
'central_domains' => [
    'localhost',
    '127.0.0.1',
],

'database' => [
    'based_on' => 'subdomain', // or 'path'
    'prefix' => 'tenant',
],
```

## 🧪 الخطوة 5: اختبار

```bash
# إنشاء tenant جديد
php artisan tenants:create company1

# التحقق
php artisan tenants:list
```

## 📝 ملاحظات مهمة:

1. ✅ **كل الـ modules ستستخدم tenancy تلقائياً**
2. ✅ **لا حاجة لتكرار middleware في كل route file**
3. ✅ **الـ central domain (localhost) للـ admin**
4. ✅ **الـ tenant domains (company1.localhost) للشركات**

---

## 🎯 استثناء Routes معينة من Tenancy

إذا كنت تريد routes معينة **بدون tenancy** (مثل admin panel):

```php
// في routes/web.php
Route::middleware(['web'])->group(function () {
    // هذه routes بدون tenancy
    Route::get('/admin', [AdminController::class, 'index']);
});
```
