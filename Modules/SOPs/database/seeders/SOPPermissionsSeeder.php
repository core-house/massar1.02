<?php

namespace Modules\SOPs\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Authorization\Models\Permission;

class SOPPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $category = 'Quality';
        $permissions = ['sops', 'sop-categories'];
        $actions = ['view', 'create', 'edit', 'delete'];

        foreach ($permissions as $permission) {
            foreach ($actions as $action) {
                $name = "$action $permission";
                Permission::firstOrCreate(
                    ['name' => $name, 'guard_name' => 'web'],
                    ['category' => $category]
                );
            }
        }
    }
}
