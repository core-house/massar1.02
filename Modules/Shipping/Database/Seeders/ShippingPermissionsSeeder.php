<?php

namespace Modules\Shipping\Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class ShippingPermissionsSeeder extends Seeder
{
    public function run()
    {
        $groupedPermissions = [
            'Shipping' => [
                'Shipping Companies',
                'Shipments',
                'Drivers',
                'Orders',
                'Shipping Statistics',
            ],
        ];

        $actions = ['view', 'create', 'edit', 'delete', 'print'];

        foreach ($groupedPermissions as $category => $permissions) {
            foreach ($permissions as $basePermission) {
                foreach ($actions as $action) {
                    $fullName = "$action $basePermission";

                    Permission::firstOrCreate(
                        ['name' => $fullName, 'guard_name' => 'web'],
                        ['category' => $category]
                    );
                }
            }
        }

        // إضافة الصلاحيات للرولات
        $adminRole = Role::firstOrCreate(['name' => 'admin'], ['guard_name' => 'web']);
        $userRole = Role::firstOrCreate(['name' => 'user'], ['guard_name' => 'web']);

        // إعطاء جميع صلاحيات الشحن للأدمن
        $shippingPermissions = Permission::where('category', 'Shipping')->get();
        $adminRole->givePermissionTo($shippingPermissions);

        // إعطاء صلاحيات العرض فقط لليوزر
        $userViewPermissions = Permission::where('category', 'Shipping')
            ->where('name', 'like', 'view%')
            ->get();
        $userRole->givePermissionTo($userViewPermissions);
    }
}
