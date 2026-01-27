<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Livewire\Volt\Volt;

class VoltServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $paths = [
            config('livewire.view_path', resource_path('views/livewire')),
            resource_path('views/pages'),
        ];

        // Mount Livewire Volt paths from all enabled modules
        // Use app() instead of facade to avoid "Class modules does not exist" error
        try {
            if ($this->app->bound('modules')) {
                $moduleRepository = $this->app['modules'];
                $modules = $moduleRepository->allEnabled();

                foreach ($modules as $module) {
                    // Try both Resources (capital) and resources (lowercase) for compatibility
                    $livewirePath = module_path($module->getName(), 'Resources/views/livewire');
                    if (! is_dir($livewirePath)) {
                        $livewirePath = module_path($module->getName(), 'resources/views/livewire');
                    }
                    if (is_dir($livewirePath)) {
                        $paths[] = $livewirePath;
                    }
                }
            }
        } catch (\Exception $e) {
            // Silently fail if modules not available yet (during installation/setup)
        }

        Volt::mount($paths);
    }
}
