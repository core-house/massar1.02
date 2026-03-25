<?php

declare(strict_types=1);

namespace Modules\UniversalSearch\Console;

use Illuminate\Console\Command;
use Illuminate\Routing\Route as IlluminateRoute;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Str;

class ExportModuleRoutesJsonCommand extends Command
{
    protected $signature = 'universalsearch:export-module-routes-json
                            {--path=Docs/module-routes.json : Output path relative to base_path()}';

    protected $description = 'Export module routes grouped by view/add/edit to a JSON file with translated names.';

    public function handle(): int
    {
        $outputPath = (string) $this->option('path');
        $absoluteOutputPath = base_path($outputPath);

        $this->info('Building route label map from sidebar blades...');
        $labelMap = $this->buildRouteToTranslationKeyMap();

        $this->info('Scanning routes...');
        $groups = [
            'view' => [],
            'add' => [],
            'edit' => [],
        ];

        /** @var array<int, IlluminateRoute> $routes */
        $routes = app('router')->getRoutes()->getRoutes();

        foreach ($routes as $route) {
            if (!$route instanceof IlluminateRoute) {
                continue;
            }

            $name = (string) ($route->getName() ?? '');
            if ($name === '') {
                continue;
            }

            if (!$this->isModuleRoute($route)) {
                continue;
            }

            if (!$this->isGetLike($route)) {
                continue;
            }

            if ($this->isApiRoute($route, $name)) {
                continue;
            }

            $bucket = $this->classifyRoute($route, $name);

            $translationKey = $labelMap[$name] ?? null;
            $baseTranslated = $this->translateLabel($translationKey, $name);
            $actionKey = 'common.' . $bucket;
            $actionTranslated = $this->translateLabel($actionKey, $bucket);
            $translated = [
                'ar' => trim($actionTranslated['ar'] . ' - ' . $baseTranslated['ar']),
                'en' => trim($actionTranslated['en'] . ' - ' . $baseTranslated['en']),
            ];

            $groups[$bucket][] = [
                'route' => $name,
                'uri' => $route->uri(),
                'module' => $this->inferModuleName($route),
                'name_key' => $translationKey,
                'action_key' => $actionKey,
                'name' => $translated,
                'base_name' => $baseTranslated,
            ];
        }

        // Sort for stability
        foreach ($groups as $k => $items) {
            usort($items, fn ($a, $b) => strcmp($a['route'], $b['route']));
            $groups[$k] = $items;
        }

        $payload = [
            'generated_at' => now()->toIso8601String(),
            'counts' => [
                'view' => count($groups['view']),
                'add' => count($groups['add']),
                'edit' => count($groups['edit']),
            ],
            'groups' => $groups,
        ];

        File::ensureDirectoryExists(dirname($absoluteOutputPath));
        File::put($absoluteOutputPath, json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        $this->info('Exported: ' . $absoluteOutputPath);

        return self::SUCCESS;
    }

    private function isGetLike(IlluminateRoute $route): bool
    {
        $methods = $route->methods();
        return in_array('GET', $methods, true) || in_array('HEAD', $methods, true);
    }

    private function isModuleRoute(IlluminateRoute $route): bool
    {
        $action = $route->getAction();
        $uses = $action['uses'] ?? null;

        if (is_string($uses) && Str::contains($uses, 'Modules\\')) {
            return true;
        }

        $controller = $route->getControllerClass();
        if (is_string($controller) && Str::startsWith($controller, 'Modules\\')) {
            return true;
        }

        // Some routes are closures/Livewire Volt, still module-scoped by name/uri; keep simple & strict.
        return false;
    }

    private function isApiRoute(IlluminateRoute $route, string $name): bool
    {
        if (Str::startsWith($name, 'api.')) {
            return true;
        }

        $uri = (string) $route->uri();
        if (Str::startsWith($uri, 'api/') || Str::startsWith($uri, 'api\\')) {
            return true;
        }

        $mw = $route->gatherMiddleware();
        return in_array('api', $mw, true);
    }

    private function inferModuleName(IlluminateRoute $route): ?string
    {
        $controller = $route->getControllerClass();
        if (!is_string($controller) || !Str::startsWith($controller, 'Modules\\')) {
            return null;
        }

        // Modules\CRM\Http\Controllers\...
        $parts = explode('\\', $controller);
        return $parts[1] ?? null;
    }

    private function classifyRoute(IlluminateRoute $route, string $name): string
    {
        // Middleware-based classification first (more accurate)
        $mw = $route->gatherMiddleware();
        foreach ($mw as $m) {
            if (!is_string($m)) {
                continue;
            }

            if (Str::startsWith($m, 'can:create') || Str::startsWith($m, 'permission:create')) {
                return 'add';
            }
            if (
                Str::startsWith($m, 'can:edit') || Str::startsWith($m, 'permission:edit') ||
                Str::startsWith($m, 'can:update') || Str::startsWith($m, 'permission:update') ||
                Str::startsWith($m, 'can:delete') || Str::startsWith($m, 'permission:delete') ||
                Str::startsWith($m, 'can:toggle') || Str::startsWith($m, 'permission:toggle') ||
                Str::startsWith($m, 'can:restore') || Str::startsWith($m, 'permission:restore')
            ) {
                return 'edit';
            }
        }

        // Fallback: route-name conventions
        if (Str::endsWith($name, ['.create', '.store', '.new', '.add'])) {
            return 'add';
        }
        if (
            Str::endsWith($name, ['.edit', '.update', '.destroy', '.delete', '.toggle-status', '.toggle', '.restore', '.forceDelete', '.force-delete']) ||
            Str::contains($name, ['.edit.', '.update.', '.destroy.', '.delete.', '.toggle.', '.restore.'])
        ) {
            return 'edit';
        }

        return 'view';
    }

    /**
     * @return array<string, string> routeName => translationKey
     */
    private function buildRouteToTranslationKeyMap(): array
    {
        $map = [];
        $files = File::glob(resource_path('views/components/sidebar/*.blade.php')) ?: [];

        foreach ($files as $file) {
            $content = File::get($file);
            if ($content === '') {
                continue;
            }

            // Find anchors that contain route('x') and later __('y') within the next ~400 chars
            $pattern = '/route\\(\\s*[\\\"\\\']([^\\\"\\\']+)[\\\"\\\']\\s*\\)(?:(?!route\\().){0,400}?__\\(\\s*[\\\"\\\']([^\\\"\\\']+)[\\\"\\\']/s';
            if (!preg_match_all($pattern, $content, $matches, PREG_SET_ORDER)) {
                continue;
            }

            foreach ($matches as $m) {
                $routeName = trim((string) ($m[1] ?? ''));
                $key = trim((string) ($m[2] ?? ''));
                if ($routeName === '' || $key === '') {
                    continue;
                }
                if (!isset($map[$routeName])) {
                    $map[$routeName] = $key;
                }
            }
        }

        return $map;
    }

    /**
     * @return array{ar:string, en:string}
     */
    private function translateLabel(?string $key, string $fallback): array
    {
        $result = ['ar' => $fallback, 'en' => $fallback];

        if (!$key) {
            return $result;
        }

        foreach (['ar', 'en'] as $locale) {
            if (Lang::has($key, $locale)) {
                $result[$locale] = trans($key, [], $locale);
            }
        }

        return $result;
    }
}

