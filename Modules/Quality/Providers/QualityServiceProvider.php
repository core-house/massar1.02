<?php

namespace Modules\Quality\Providers;

use Illuminate\Support\ServiceProvider;

class QualityServiceProvider extends ServiceProvider
{
    protected string $moduleName = 'Quality';

    public function boot(): void
    {
        $this->registerTranslations();
        $this->registerViews();
        $this->loadMigrationsFrom(module_path($this->moduleName, 'database/migrations'));
    }

    public function register(): void
    {
        $this->app->register(RouteServiceProvider::class);
    }

    protected function registerViews(): void
    {
        $viewPath = resource_path('views/modules/' . strtolower($this->moduleName));
        $sourcePath = module_path($this->moduleName, 'resources/views');

        $this->publishes([
            $sourcePath => $viewPath
        ], ['views', $this->moduleName . '-module-views']);

        $this->loadViewsFrom(array_merge($this->getPublishableViewPaths(), [$sourcePath]), strtolower($this->moduleName));
    }

    protected function registerTranslations(): void
    {
        $publishedPath = resource_path('lang/modules/' . strtolower($this->moduleName));
        $moduleLangPath = module_path($this->moduleName, 'lang');
        $moduleResourcesLangPath = module_path($this->moduleName, 'resources/lang');

        if (is_dir($publishedPath)) {
            $this->loadTranslationsFrom($publishedPath, strtolower($this->moduleName));
        } elseif (is_dir($moduleResourcesLangPath)) {
            $this->loadTranslationsFrom($moduleResourcesLangPath, strtolower($this->moduleName));
        } elseif (is_dir($moduleLangPath)) {
            $this->loadTranslationsFrom($moduleLangPath, strtolower($this->moduleName));
        }
    }

    private function getPublishableViewPaths(): array
    {
        $paths = [];
        foreach ($this->app['config']->get('view.paths') as $path) {
            if (is_dir($path . '/modules/' . strtolower($this->moduleName))) {
                $paths[] = $path . '/modules/' . strtolower($this->moduleName);
            }
        }

        return $paths;
    }
}

