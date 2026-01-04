<?php

namespace Modules\HR\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Nwidart\Modules\Traits\PathNamespace;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class HRServiceProvider extends ServiceProvider
{
    use PathNamespace;

    protected string $name = 'HR';

    protected string $nameLower = 'hr';

    /**
     * Boot the application events.
     */
    public function boot(): void
    {
        $this->registerCommands();
        $this->registerCommandSchedules();
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
        $this->loadMigrationsFrom(module_path($this->name, 'database/migrations'));
        $this->registerLivewireComponents();
    }

    /**
     * Register the service provider.
     */
    public function register(): void
    {
        $this->app->register(EventServiceProvider::class);
        $this->app->register(RouteServiceProvider::class);
    }

    /**
     * Register commands in the format of Command::class
     */
    protected function registerCommands(): void
    {
        // $this->commands([]);
    }

    /**
     * Register command Schedules.
     */
    protected function registerCommandSchedules(): void
    {
        // $this->app->booted(function () {
        //     $schedule = $this->app->make(Schedule::class);
        //     $schedule->command('inspire')->hourly();
        // });
    }

    /**
     * Register translations.
     */
    public function registerTranslations(): void
    {
        $langPath = resource_path('lang/modules/'.$this->nameLower);

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, $this->nameLower);
            $this->loadJsonTranslationsFrom($langPath);
        } else {
            $this->loadTranslationsFrom(module_path($this->name, 'lang'), $this->nameLower);
            $this->loadJsonTranslationsFrom(module_path($this->name, 'lang'));
        }
    }

    /**
     * Register config.
     */
    protected function registerConfig(): void
    {
        $configPath = module_path($this->name, config('modules.paths.generator.config.path'));

        if (is_dir($configPath)) {
            $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($configPath));

            foreach ($iterator as $file) {
                if ($file->isFile() && $file->getExtension() === 'php') {
                    $config = str_replace($configPath.DIRECTORY_SEPARATOR, '', $file->getPathname());
                    $config_key = str_replace([DIRECTORY_SEPARATOR, '.php'], ['.', ''], $config);
                    $segments = explode('.', $this->nameLower.'.'.$config_key);

                    // Remove duplicated adjacent segments
                    $normalized = [];
                    foreach ($segments as $segment) {
                        if (end($normalized) !== $segment) {
                            $normalized[] = $segment;
                        }
                    }

                    $key = ($config === 'config.php') ? $this->nameLower : implode('.', $normalized);

                    $this->publishes([$file->getPathname() => config_path($config)], 'config');
                    $this->merge_config_from($file->getPathname(), $key);
                }
            }
        }
    }

    /**
     * Merge config from the given path recursively.
     */
    protected function merge_config_from(string $path, string $key): void
    {
        $existing = config($key, []);
        $module_config = require $path;

        config([$key => array_replace_recursive($existing, $module_config)]);
    }

    /**
     * Register views.
     */
    public function registerViews(): void
    {
        $viewPath = resource_path('views/modules/'.$this->nameLower);
        $sourcePath = module_path($this->name, 'resources/views');

        $this->publishes([$sourcePath => $viewPath], ['views', $this->nameLower.'-module-views']);

        $this->loadViewsFrom(array_merge($this->getPublishableViewPaths(), [$sourcePath]), $this->nameLower);

        Blade::componentNamespace(config('modules.namespace').'\\' . $this->name . '\\View\\Components', $this->nameLower);
    }

    /**
     * Get the services provided by the provider.
     */
    public function provides(): array
    {
        return [];
    }

    private function getPublishableViewPaths(): array
    {
        $paths = [];
        foreach (config('view.paths') as $path) {
            if (is_dir($path.'/modules/'.$this->nameLower)) {
                $paths[] = $path.'/modules/'.$this->nameLower;
            }
        }

        return $paths;
    }

    /**
     * Register Livewire components.
     */
    protected function registerLivewireComponents(): void
    {
        if (class_exists(\Livewire\Livewire::class)) {
            // Register a resolver for hr:: namespaced Volt components
            // This allows using <livewire:hr::hr-management.* /> syntax
            $livewirePath = module_path($this->name, 'resources/views/livewire');
            
            if (is_dir($livewirePath) && class_exists(\Livewire\Volt\Volt::class)) {
                \Livewire\Livewire::resolveMissingComponent(function (string $name) use ($livewirePath) {
                    // Only handle hr:: namespaced components
                    if (!str_starts_with($name, 'hr::')) {
                        return null;
                    }
                    
                    // Remove the hr:: prefix and convert dots to directory separators
                    $componentPath = str_replace('.', DIRECTORY_SEPARATOR, substr($name, 4));
                    $fullPath = $livewirePath . DIRECTORY_SEPARATOR . $componentPath . '.blade.php';
                    
                    if (file_exists($fullPath)) {
                        // Use Volt's ComponentFactory to compile and return the component class
                        $factory = app(\Livewire\Volt\ComponentFactory::class);
                        return $factory->make($name, $fullPath);
                    }
                    
                    return null;
                });
            }

            // Register class-based Livewire components
            \Livewire\Livewire::component(
                'hr::flexible-salary-processor',
                \Modules\HR\Livewire\FlexibleSalaryProcessor::class
            );
            \Livewire\Livewire::component(
                'hr::flexible-salary-processing.index',
                \Modules\HR\Livewire\FlexibleSalaryProcessing\Index::class
            );
            \Livewire\Livewire::component(
                'hr::flexible-salary-processing.create',
                \Modules\HR\Livewire\FlexibleSalaryProcessing\Create::class
            );
            \Livewire\Livewire::component(
                'hr::flexible-salary-processing.edit',
                \Modules\HR\Livewire\FlexibleSalaryProcessing\Edit::class
            );
            \Livewire\Livewire::component(
                'hr::employee-advances.manage',
                \Modules\HR\Livewire\EmployeeAdvances\ManageEmployeeAdvances::class
            );
            \Livewire\Livewire::component(
                'hr::employee-deductions-rewards.manage',
                \Modules\HR\Livewire\EmployeeDeductionsRewards\ManageEmployeeDeductionsRewards::class
            );
            \Livewire\Livewire::component(
                'hr::attendance-processing-manager',
                \Modules\HR\Livewire\AttendanceProcessingManager::class
            );
            \Livewire\Livewire::component(
                'hr::leaves.leave-requests.index',
                \Modules\HR\Livewire\Leaves\LeaveRequests\Index::class
            );
            \Livewire\Livewire::component(
                'hr::leaves.leave-requests.create',
                \Modules\HR\Livewire\Leaves\LeaveRequests\Create::class
            );
            \Livewire\Livewire::component(
                'hr::leaves.leave-requests.edit',
                \Modules\HR\Livewire\Leaves\LeaveRequests\Edit::class
            );
            \Livewire\Livewire::component(
                'hr::leaves.leave-requests.show',
                \Modules\HR\Livewire\Leaves\LeaveRequests\Show::class
            );
            \Livewire\Livewire::component(
                'hr::leaves.leave-balances.index',
                \Modules\HR\Livewire\Leaves\LeaveBalances\Index::class
            );
            \Livewire\Livewire::component(
                'hr::leaves.leave-balances.create-edit',
                \Modules\HR\Livewire\Leaves\LeaveBalances\CreateEdit::class
            );
            \Livewire\Livewire::component(
                'hr::leaves.leave-types.manage-leave-types',
                \Modules\HR\Livewire\Leaves\LeaveTypes\ManageLeaveTypes::class
            );
            \Livewire\Livewire::component(
                'hr::hr-settings.index',
                \Modules\HR\Livewire\HrManagement\HrSettings\Index::class
            );
            \Livewire\Livewire::component(
                'hr::hr-settings.create-edit',
                \Modules\HR\Livewire\HrManagement\HrSettings\CreateEdit::class
            );
        }
        // Volt components are auto-discovered from resources/views/livewire
    }
}
