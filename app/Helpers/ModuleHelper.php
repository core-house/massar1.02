<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Route;

class ModuleHelper
{
    /**
     * Module routes mapping to their sidebar components
     */
    private static $moduleRoutes = [
        'invoices' => [
            'prefix' => 'invoices',
            'routes' => ['index', 'create', 'edit', 'show'],
            'sidebar' => 'invoices'
        ],
        'sales-invoices' => [
            'prefix' => 'invoices',
            'routes' => ['index', 'create', 'edit', 'show'],
            'sidebar' => 'invoices',
            'params' => ['type' => 10]
        ],
        'purchase-invoices' => [
            'prefix' => 'invoices',
            'routes' => ['index', 'create', 'edit', 'show'],
            'sidebar' => 'invoices',
            'params' => ['type' => 11]
        ],
        'inventory' => [
            'prefix' => 'invoices',
            'routes' => ['index', 'create', 'edit', 'show'],
            'sidebar' => 'invoices',
            'params' => ['type' => 18]
        ],
        'crm' => [
            'prefix' => 'crm',
            'routes' => ['clients', 'leads', 'opportunities'],
            'sidebar' => 'crm'
        ],
        'accounts' => [
            'prefix' => 'accounts',
            'routes' => ['clients', 'suppliers', 'employees'],
            'sidebar' => 'accounts'
        ],
        'services' => [
            'prefix' => 'services',
            'routes' => ['clients', 'requests', 'tickets'],
            'sidebar' => 'service'
        ],
        'pos' => [
            'prefix' => 'pos',
            'routes' => ['sales', 'products', 'inventory'],
            'sidebar' => 'POS'
        ],
        'hr' => [
            'prefix' => 'hr',
            'routes' => ['employees', 'attendance', 'payroll'],
            'sidebar' => 'hr'
        ],
        'projects' => [
            'prefix' => 'projects',
            'routes' => ['tasks', 'milestones', 'teams'],
            'sidebar' => 'projects'
        ],
        'settings' => [
            'prefix' => 'settings',
            'routes' => ['general', 'users', 'roles', 'permissions'],
            'sidebar' => 'settings'
        ],
        'rentals' => [
            'prefix' => 'rentals',
            'routes' => ['contracts', 'assets', 'payments'],
            'sidebar' => 'rentals'
        ],
        'shipping' => [
            'prefix' => 'shipping',
            'routes' => ['shipments', 'drivers', 'vehicles'],
            'sidebar' => 'shipping'
        ],
        'inquiries' => [
            'prefix' => 'inquiries',
            'routes' => ['tickets', 'feedback', 'support'],
            'sidebar' => 'inquiries'
        ],
        'daily_progress' => [
            'prefix' => 'daily_progress',
            'routes' => ['tasks', 'reports', 'stats'],
            'sidebar' => 'daily_progress'
        ],
        'manufacturing' => [
            'prefix' => 'manufacturing',
            'routes' => ['production', 'materials', 'quality'],
            'sidebar' => 'manufacturing'
        ],
        'departments' => [
            'prefix' => 'departments',
            'routes' => ['list', 'employees', 'budgets'],
            'sidebar' => 'departments'
        ],
        'vouchers' => [
            'prefix' => 'vouchers',
            'routes' => ['payment', 'receipt', 'journal'],
            'sidebar' => 'vouchers'
        ],
        'transfers' => [
            'prefix' => 'transfers',
            'routes' => ['internal', 'external', 'history'],
            'sidebar' => 'transfers'
        ],
        'discounts' => [
            'prefix' => 'discounts',
            'routes' => ['promotions', 'offers', 'coupons'],
            'sidebar' => 'discounts'
        ]
    ];

    /**
     * Get the current module name from the URL and route information
     */
    public static function getCurrentModule(): ?string
    {
        $currentPath = request()->path();
        $currentRoute = Route::current();

        if (!$currentRoute) {
            return null;
        }

        // Get the controller class if available
        $controller = $currentRoute->getController();
        if ($controller) {
            $controllerClass = get_class($controller);
            // If controller is in a module namespace, use that
            if (str_contains($controllerClass, 'Modules\\')) {
                $parts = explode('\\', $controllerClass);
                if (isset($parts[1])) {
                    $moduleName = strtolower($parts[1]);
                    return self::getSidebarComponent($moduleName);
                }
            }
        }

        // Fallback to checking URL segments
        $segments = explode('/', $currentPath);

        // Check each module's configuration
        foreach (self::$moduleRoutes as $module => $config) {
            // Check if the URL starts with the module prefix
            if (in_array($config['prefix'], $segments)) {
                return $config['sidebar'];
            }

            // If no prefix match but a route matches and we're sure about the module
            if (!empty($config['routes'])) {
                foreach ($segments as $segment) {
                    if (in_array($segment, $config['routes'])) {
                        // Only return if we can determine the module from the controller
                        // or if this route is unique to this module
                        if ($controller || self::isRouteUniqueToModule($segment)) {
                            return $config['sidebar'];
                        }
                    }
                }
            }
        }

        return null;
    }

    /**
     * Check if a route name is unique to a specific module
     */
    private static function isRouteUniqueToModule(string $route): bool
    {
        $moduleCount = 0;
        foreach (self::$moduleRoutes as $config) {
            if (in_array($route, $config['routes'] ?? [])) {
                $moduleCount++;
            }
            if ($moduleCount > 1) {
                return false;
            }
        }
        return $moduleCount === 1;
    }

    /**
     * Get the sidebar component name for a module
     */
    private static function getSidebarComponent(string $moduleName): string
    {
        // Map module names to their sidebar components
        $sidebarMap = [
            'crm' => 'crm',
            'accounts' => 'accounts',
            'services' => 'service',
            'pos' => 'POS',
            // Add other mappings as needed...
        ];

        return $sidebarMap[strtolower($moduleName)] ?? strtolower($moduleName);
    }
}
