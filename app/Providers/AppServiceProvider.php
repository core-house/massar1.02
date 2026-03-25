<?php

namespace App\Providers;

use App\Models\Item;
use App\Models\NoteDetails;
use App\Observers\ItemObserver;
use App\Observers\NoteDetailsObserver;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Modules\Settings\Models\PublicSetting;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->useLangPath(resource_path('lang'));

        // Allow module PHP translations to be loaded without namespace
        // e.g. __('general.key') from Modules/*/Resources/lang/*/general.php
        // and __('dashboard.title') from Modules/*/lang/*/dashboard.php
        $this->app->afterResolving('translation.loader', function ($loader): void {
            if (! method_exists($loader, 'addPath')) {
                return;
            }

            foreach (glob(base_path('Modules/*/lang')) ?: [] as $path) {
                if (is_dir($path)) {
                    $loader->addPath($path);
                }
            }

            foreach (glob(base_path('Modules/*/Resources/lang')) ?: [] as $path) {
                if (is_dir($path)) {
                    $loader->addPath($path);
                }
            }
        });

        // حل مشكلة المسارات المفقودة على Hostinger (Shared Hosting)
        // هذا الجزء يوجه أي نداء قديم للمسار الجديد داخل الموديولات
        $aliases = [
            'App\Models\Employee'       => \Modules\HR\Models\Employee::class,
            'App\Models\WorkPermission' => \Modules\HR\Models\WorkPermission::class,
            // أضف أي موديلات أخرى قمت بنقلها هنا بنفس الطريقة
        ];

        foreach ($aliases as $oldPath => $newClass) {
            if (!class_exists($oldPath) && class_exists($newClass)) {
                class_alias($newClass, $oldPath);
            }
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        try {
            if (! Schema::hasTable('public_settings')) {
                return;
            }
            $settings = Cache::rememberForever('public_settings', function () {
                return PublicSetting::pluck('value', 'key')->toArray();
            });
            config(['public_settings' => $settings]);
        } catch (\Exception $e) {
            // Log if needed
        }

        // تحميل رمز العملة الافتراضية
        try {
            $currency = \Modules\Settings\Models\Currency::where('is_default', true)->first();
            config(['app.currency_symbol' => $currency?->symbol ?? 'ر.س']);
        } catch (\Exception $e) {
            config(['app.currency_symbol' => 'ر.س']);
        }

        Paginator::useBootstrapFive();
        Item::observe(ItemObserver::class);
        NoteDetails::observe(NoteDetailsObserver::class);
        // Project Observer moved to Projects Module
    }
}
