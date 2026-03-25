<?php

declare(strict_types=1);

namespace Modules\Decumintations\database\seeders;

use Illuminate\Database\Seeder;
use Modules\Authorization\Models\Permission;

class DecumintationsPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $groupedPermissions = [
            'Decumintations' => [
                'Documents',
                'Document Categories',
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
