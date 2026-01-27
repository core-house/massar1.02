<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class TenantMigrationServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->mergeTenantMigrationsPaths();
    }

    /**
     * Merge module tenant migration paths into the tenancy configuration.
     */
    protected function mergeTenantMigrationsPaths(): void
    {
        // Get the glob patterns from config (or default to known patterns)
        $globPatterns = config('tenancy.tenant_migrations_path', [
            'database/migrations/tenant',
            'Modules/*/Database/Migrations/tenant', // Standard Nwidart structure
            'Modules/*/database/migrations/tenant', // Lowercase variation
        ]);

        $resolvedPaths = [];

        foreach ($globPatterns as $pattern) {
            // Expand glob pattern to absolute paths
            $paths = glob(base_path($pattern));

            if ($paths === false) {
                continue;
            }

            foreach ($paths as $path) {
                if (is_dir($path)) {
                    $resolvedPaths[] = $path;
                }
            }
        }

        // Get existing tenancy migration parameters
        $migrationParams = config('tenancy.migration_parameters', []);

        // Ensure --path is initialized
        $existingPaths = $migrationParams['--path'] ?? [];
        if (! is_array($existingPaths)) {
            $existingPaths = [$existingPaths];
        }

        // Merge and unique
        $allPaths = array_unique(array_merge($existingPaths, $resolvedPaths));

        // Update the configuration
        $migrationParams['--path'] = $allPaths;

        // Save back to config
        config(['tenancy.migration_parameters' => $migrationParams]);
    }
}
