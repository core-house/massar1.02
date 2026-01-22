<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        channels: __DIR__ . '/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {

        // 1. إجبار لارافيل على تنفيذ الـ Middleware بالترتيب الصحيح
        // هذا يمنع ضياع الـ Session عند التنقل بين الـ Subdomains
        $middleware->priority([
            \Illuminate\Cookie\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \App\Http\Middleware\CustomInitializeTenancyByDomain::class, // التينانت الأول
            \App\Http\Middleware\CheckTenantStatus::class,              // تحقق من حالة التينانت
            \Illuminate\Session\Middleware\StartSession::class,         // بعدين السيشن
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ]);

        // 2. إعدادات مجموعة الـ Web (التي يتم استدعاؤها في routes/web.php)
        $middleware->web(append: [
            \App\Http\Middleware\CustomInitializeTenancyByDomain::class,
            \App\Http\Middleware\CheckTenantStatus::class,
            \App\Http\Middleware\SetLocale::class,
            \App\Http\Middleware\PersistSidebarSelection::class,
        ]);

        // 3. تعريف الـ Aliases المستخدمة في الـ Controllers أو الـ Routes
        $middleware->alias([
            'employee.auth' => \Modules\HR\Http\Middleware\EmployeeAuth::class,
            'engineer.access' => \Modules\Inquiries\Middleware\EngineerAccessMiddleware::class,
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
            'admin' => \App\Http\Middleware\EnsureAdminUser::class,
        ]);

        $middleware->group('employee', [
            \Modules\HR\Http\Middleware\EmployeeAuth::class,
        ]);
    })
    ->withCommands([
        \Modules\Inquiries\Console\TestGoogleMapsCommand::class,
    ])
    ->withExceptions(function (Exceptions $exceptions) {
        // معالجة أخطاء مكتبة البحث (كما في كودك الأصلي)
        $exceptions->render(function (Throwable $e, $request) {
            if (
                str_contains($e->getMessage(), 'FailedToInitializePatternSearchException') ||
                str_contains($e->getMessage(), 'PatternSearcher') ||
                str_contains($e->getMessage(), 'syntax-highlight')
            ) {
                \Illuminate\Support\Facades\Log::error('Phiki Pattern Search Error: ' . $e->getMessage());

                return response()->view('errors.simple', [
                    'message' => 'حدث خطأ في البحث. يرجى المحاولة مرة أخرى.',
                ], 500);
            }

            return null;
        });
    })->create();
