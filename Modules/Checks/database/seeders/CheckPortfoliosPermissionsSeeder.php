<?php

namespace Modules\Checks\Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class CheckPortfoliosPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'عرض حافظات أوراق القبض',
            'إضافة حافظات أوراق القبض',
            'تعديل حافظات أوراق القبض',
            'حذف حافظات أوراق القبض',
            'عرض حافظات أوراق الدفع',
            'إضافة حافظات أوراق الدفع',
            'تعديل حافظات أوراق الدفع',
            'حذف حافظات أوراق الدفع',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Assign to admin role
        $adminRole = Role::where('name', 'admin')->first();
        if ($adminRole) {
            $adminRole->givePermissionTo($permissions);
        }

        $this->command->info('Check portfolios permissions created successfully!');
    }
}
