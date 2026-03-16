<?php

namespace App\Providers;

use App\Models\Item;
use App\Models\JournalDetail;
use App\Models\NoteDetails;
use App\Observers\ItemObserver;
use App\Observers\JournalDetailObserver;
use App\Observers\NoteDetailsObserver;
use Illuminate\Database\Eloquent\Model;
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
        $this->app->useLangPath(base_path('lang'));

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