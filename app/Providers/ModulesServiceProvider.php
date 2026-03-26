<?php

declare(strict_types=1);

namespace App\Providers;

use Nwidart\Modules\LaravelModulesServiceProvider as BaseModulesServiceProvider;

class ModulesServiceProvider extends BaseModulesServiceProvider
{
    /**
     * Register the service provider.
     */
    public function register(): void
    {
        parent::register();
    }

    /**
     * Boot the service provider.
     */
    public function boot(): void
    {
        parent::boot();
    }
}
