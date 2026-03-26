<?php

namespace Modules\Tenancy\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        parent::boot();
    }

    public function map(): void
    {
        $this->mapWebRoutes();
    }

    protected function mapWebRoutes(): void
    {
        // تأكد أن الروابط تُحمل بدون قيود الـ Tenant Middleware التلقائية
        Route::middleware('web')
            ->group(module_path('Tenancy', 'routes/web.php'));
    }

    protected function mapCentralRoutes(): void
    {
        $centralDomains = config('tenancy.central_domains', []);

        // تحقق من وجود ملف الـ routes قبل تحميله
        $routesPath = module_path('Tenancy', 'Routes/web.php');

        if (!file_exists($routesPath)) {
            return; // لو الملف مش موجود، متعملش حاجة
        }

        foreach ($centralDomains as $domain) {
            Route::middleware(['web'])
                ->domain($domain)
                ->group($routesPath);
        }
    }
}
