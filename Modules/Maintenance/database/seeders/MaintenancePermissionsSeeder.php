<?php

namespace Modules\Maintenance\database\seeders;

use Illuminate\Database\Seeder;
use Modules\Authorization\Models\Permission;

class MaintenancePermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $groupedPermissions = [
            'Maintenance' => [
                'Maintenance Statistics',
                'Service Types',
                'Maintenances',
                'Periodic Maintenance',
            ],
        ];

        $actions = ['view', 'create', 'edit', 'delete', 'print'];

        foreach ($groupedPermissions as $category => $items) {
            foreach ($items as $base) {
                foreach ($actions as $action) {
                    $fullName = "$action $base";
                    Permission::firstOrCreate(
                        ['name' => $fullName, 'guard_name' => 'web'],
                        ['category' => $category]
                    );
                }
            }
        }
    }
}
