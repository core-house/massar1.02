<?php

declare(strict_types=1);

namespace Modules\Settings\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Authorization\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class SettingsPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        // مصفوفة المجموعات مع العناصر داخل كل مجموعة
        $groupedPermissions = [
            'Settings' => [
                'General Settings',
                'Currencies',
                'Barcode Settings',
                'Export Data',
                'Settings Control',
                // 'Barcode Print Settings',
                'System Settings',
                // 'Invoice Options',
                // 'Invoice Templates',
            ],
        ];

        // الأفعال القياسية
        $standardActions = ['view', 'edit'];

        // أفعال العملات
        $currenciesActions = ['view', 'create', 'edit', 'delete'];

        // أفعال أسعار الصرف
        $exchangeRatesActions = ['view', 'edit', 'update'];

        // إنشاء الصلاحيات إن لم تكن موجودة
        foreach ($groupedPermissions as $category => $items) {
            foreach ($items as $base) {
                // تحديد الأفعال حسب العنصر
                $currentActions = match ($base) {
                    'Currencies' => $currenciesActions,
                    'Exchange Rates' => $exchangeRatesActions,
                    default => $standardActions
                };

                foreach ($currentActions as $action) {
                    $fullName = "$action $base";
                    Permission::firstOrCreate(
                        ['name' => $fullName, 'guard_name' => 'web'],
                        ['category' => $category]
                    );
                }
            }
        }

        // Additional specific permissions
        // $specificPermissions = [
        //     'export Data',
        //     'export SQL',
        //     'view Export Stats',
        // ];

        // foreach ($specificPermissions as $permission) {
        //     Permission::firstOrCreate(
        //         ['name' => $permission, 'guard_name' => 'web'],
        //         ['category' => 'Settings']
        //     );
        // }
    }
}
