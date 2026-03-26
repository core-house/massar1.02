<?php

namespace Modules\Tenancy\Providers;

use Illuminate\Database\Migrations\Migrator;
use Illuminate\Support\ServiceProvider;

class TenantMigrationServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->app->resolving(Migrator::class, function ($migrator) {
            foreach ($this->getTenantMigrationPaths() as $path) {
                $migrator->path($path);
            }
        });
    }

    protected function getTenantMigrationPaths()
    {
        $paths = [];

        // database/migrations/tenant
        if (is_dir(database_path('migrations/tenant'))) {
            $paths[] = database_path('migrations/tenant');
        }

        // Modules/*/database/tenant & Modules/*/Database/tenant
        $modules = glob(base_path('Modules/*'));
        foreach ($modules as $module) {
            $tenantPaths = [
                $module.'/database/tenant',
                $module.'/Database/tenant',
            ];

            foreach ($tenantPaths as $path) {
                if (is_dir($path)) {
                    $paths[] = $path;
                }
            }
        }

        return $paths;
    }
}
