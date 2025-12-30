<?php

declare(strict_types=1);

namespace Modules\Reports\database\seeders;

use Illuminate\Database\Seeder;
use Modules\Authorization\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class ReportPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        // مصفوفة المجموعات مع العناصر داخل كل مجموعة
        $groupedPermissions = [
            'Reports' => [
                'General Basic Reports',
                'General Financial Reports',

                'Accounts Basic Reports',
                'Accounts Financial Reports',
                'Accounts Management Reports',

                'Inventory Basic Reports',
                'Inventory Financial Reports',
                'Inventory Management Reports',

                'Sales Basic Reports',
                'Sales Financial Reports',
                'Sales Management Reports',

                'Purchases Basic Reports',
                'Purchases Financial Reports',
                'Purchases Management Reports',

                'Expenses Basic Reports',
                'Expenses Financial Reports',
                'Expenses Management Reports',

                'Cash Financial Reports',

                'Manufacturing Financial Reports',

                'Quality Basic Reports',
                'Quality Management Reports',
            ],
        ];

        // الأفعال (عرض فقط للتقارير)
        $actions = ['view'];

        // إنشاء أو تحديث الصلاحيات
        foreach ($groupedPermissions as $category => $items) {
            foreach ($items as $base) {
                foreach ($actions as $action) {
                    $fullName = "$action $base";
                    $permission = Permission::firstOrCreate(
                        ['name' => $fullName, 'guard_name' => 'web'],
                        ['category' => $category, 'option_type' => '1', 'description' => "Reports Access for $base"]
                    );

                    // تحديث الفئة والنوع إذا كانت موجودة بالفعل
                    if ($permission->category !== $category || $permission->option_type !== '1') {
                        $permission->update([
                            'category' => $category,
                            'option_type' => '1'
                        ]);
                    }
                }
            }
        }
    }
}
