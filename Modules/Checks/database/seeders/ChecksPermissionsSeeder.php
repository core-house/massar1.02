<?php

namespace Modules\Checks\Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class ChecksPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create permissions for checks management
        $permissions = [
            'عرض الشيكات',
            'إضافة الشيكات',
            'تعديل الشيكات',
            'حذف الشيكات',
            'تصفية الشيكات',
            'تمييز الشيكات كمرتدة',
            'إلغاء الشيكات',
            'اعتماد الشيكات',
            'تصدير الشيكات',
            // حافظات الأوراق المالية
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

        // Assign all permissions to admin role
        $adminRole = Role::where('name', 'admin')->first();
        if ($adminRole) {
            $adminRole->givePermissionTo($permissions);
        }

        $this->command->info('Checks permissions created successfully!');
    }
}
