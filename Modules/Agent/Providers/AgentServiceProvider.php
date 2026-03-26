<?php

namespace Modules\Agent\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Modules\Agent\Models\AgentQuestion;
use Modules\Agent\Policies\AgentQuestionPolicy;
use Nwidart\Modules\Traits\PathNamespace;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class AgentServiceProvider extends ServiceProvider
{
    use PathNamespace;

    protected string $name = 'Agent';

    protected string $nameLower = 'agent';

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
        $this->registerPolicies();
        $this->registerGates();
    }

    /**
     * Register the service provider.
     */
    public function register(): void
    {
        $this->app->register(EventServiceProvider::class);
        $this->app->register(RouteServiceProvider::class);

        $this->registerServices();
    }

    /**
     * Register module services.
     */
    protected function registerServices(): void
    {
        // Register Core Services
        $this->app->singleton(\Modules\Agent\Services\IntentClassifier::class);
        $this->app->singleton(\Modules\Agent\Services\DomainConfigRegistry::class);
        $this->app->singleton(\Modules\Agent\Services\QueryPlanValidator::class);
        $this->app->singleton(\Modules\Agent\Services\PermissionScoper::class);
        $this->app->singleton(\Modules\Agent\Services\QueryExecutor::class);
        $this->app->singleton(\Modules\Agent\Services\QueryLogger::class);
        $this->app->singleton(\Modules\Agent\Services\ResponseFormatter::class);

        // Register Domain Services
        $this->app->singleton(\Modules\Agent\Services\Domains\HRQueryService::class);
        $this->app->singleton(\Modules\Agent\Services\Domains\InvoiceQueryService::class);
        $this->app->singleton(\Modules\Agent\Services\Domains\InventoryQueryService::class);
        $this->app->singleton(\Modules\Agent\Services\Domains\CRMQueryService::class);
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
        $publishedPath = resource_path('lang/modules/'.$this->nameLower);
        $moduleLangPath = module_path($this->name, 'lang');
        $moduleResourcesLangPath = module_path($this->name, 'Resources/lang');

        if (is_dir($publishedPath)) {
            $this->loadTranslationsFrom($publishedPath, $this->nameLower);
            $this->loadJsonTranslationsFrom($publishedPath);
        } elseif (is_dir($moduleLangPath)) {
            $this->loadTranslationsFrom($moduleLangPath, $this->nameLower);
            $this->loadJsonTranslationsFrom($moduleLangPath);
        } elseif (is_dir($moduleResourcesLangPath)) {
            $this->loadTranslationsFrom($moduleResourcesLangPath, $this->nameLower);
            $this->loadJsonTranslationsFrom($moduleResourcesLangPath);
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

        Blade::componentNamespace(config('modules.namespace').'\\'.$this->name.'\\View\\Components', $this->nameLower);
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
            // Register Volt components resolver for agent:: namespace
            $livewirePath = module_path($this->name, 'resources/views/livewire');

            if (is_dir($livewirePath) && class_exists(\Livewire\Volt\Volt::class)) {
                \Livewire\Livewire::resolveMissingComponent(function (string $name) use ($livewirePath) {
                    // Only handle agent:: namespaced components
                    if (! str_starts_with($name, 'agent::')) {
                        return null;
                    }

                    // Remove the agent:: prefix and convert dots to directory separators
                    $componentPath = str_replace('.', DIRECTORY_SEPARATOR, substr($name, 7));
                    $fullPath = $livewirePath.DIRECTORY_SEPARATOR.$componentPath.'.blade.php';

                    if (file_exists($fullPath)) {
                        // Use Volt's ComponentFactory to compile and return the component class
                        $factory = app(\Livewire\Volt\ComponentFactory::class);

                        return $factory->make($name, $fullPath);
                    }

                    return null;
                });
            }

            // Register class-based Livewire components (will be added later)
            // \Livewire\Livewire::component('agent::ask-question', \Modules\Agent\Livewire\AskQuestion::class);
            // \Livewire\Livewire::component('agent::question-history', \Modules\Agent\Livewire\QuestionHistory::class);
        }
    }

    /**
     * Register policies for the module.
     */
    protected function registerPolicies(): void
    {
        Gate::policy(AgentQuestion::class, AgentQuestionPolicy::class);
    }

    /**
     * Register gates for the module.
     */
    protected function registerGates(): void
    {
        // Gate for accessing the Agent module
        Gate::define('access-agent', function ($user) {
            // Check if user has the agent.access permission
            // This assumes you're using spatie/laravel-permission or similar
            if (method_exists($user, 'hasPermissionTo')) {
                return $user->hasPermissionTo('agent.access');
            }

            // Fallback: allow all authenticated users
            // You can customize this logic based on your authorization system
            return true;
        });

        // Gate for asking questions
        Gate::define('agent.ask', function ($user) {
            if (method_exists($user, 'hasPermissionTo')) {
                return $user->hasPermissionTo('agent.ask');
            }

            // Fallback: allow all authenticated users
            return true;
        });
    }
}
