<?php

declare(strict_types=1);

namespace Modules\CRM\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Authorization\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class CRMSpecialPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        // صلاحيات إضافية خاصة بـ CRM
        $specialPermissions = [
            'view all Tasks' => 'CRM', // صلاحية مشاهدة كل المهمات
        ];

        foreach ($specialPermissions as $permissionName => $category) {
            Permission::firstOrCreate(
                ['name' => $permissionName, 'guard_name' => 'web'],
                ['category' => $category]
            );
        }
    }
}
