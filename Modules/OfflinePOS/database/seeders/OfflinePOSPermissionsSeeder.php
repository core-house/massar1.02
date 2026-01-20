<?php

namespace Modules\OfflinePOS\database\seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class OfflinePOSPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define permissions structure for Offline POS module
        // الصلاحيات بالإنجليزية للدعم متعدد اللغات
        $permissions = [
            // View permissions
            'view offline pos system',              // عرض نظام نقاط البيع الأوفلاين
            'view offline pos transactions',        // عرض معاملات نقاط البيع الأوفلاين
            'view offline pos reports',             // عرض تقارير نقاط البيع الأوفلاين
            'view offline pos sync status',         // عرض حالة المزامنة
            
            // Create permissions
            'create offline pos transaction',       // إنشاء معاملة نقاط بيع أوفلاين
            'create offline pos return invoice',    // إنشاء فاتورة مرتجعة أوفلاين
            
            // Edit permissions
            'edit offline pos transaction',         // تعديل معاملة نقاط بيع أوفلاين
            'edit offline pos settings',            // تعديل إعدادات نقاط البيع الأوفلاين
            
            // Delete permissions
            'delete offline pos transaction',       // حذف معاملة نقاط بيع أوفلاين
            
            // Print permissions
            'print offline pos invoice',            // طباعة فاتورة نقاط بيع أوفلاين
            'print offline pos thermal',            // الطباعة الحرارية لنقاط البيع
            
            // Sync permissions
            'sync offline pos transactions',        // مزامنة معاملات نقاط البيع
            'force sync offline pos',               // فرض المزامنة الفورية
            
            // Data management
            'download offline pos data',            // تنزيل بيانات نقاط البيع المحلية
            'clear offline pos local data',         // مسح البيانات المحلية
            
            // Advanced permissions
            'manage offline pos settings',          // إدارة إعدادات نقاط البيع الأوفلاين
            'access offline pos reports advanced',  // الوصول للتقارير المتقدمة
            'export offline pos reports',           // تصدير تقارير نقاط البيع
        ];

        // Create permissions
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                [
                    'name' => $permission,
                    'guard_name' => 'web',
                ],
                [
                    'category' => 'Offline POS',
                ]
            );
        }

        // Assign all permissions to 'default user' role (ID: 2)
        $defaultUserRole = Role::find(2);
        if ($defaultUserRole) {
            $createdPermissions = Permission::whereIn('name', $permissions)->pluck('id');
            $defaultUserRole->givePermissionTo($createdPermissions);
            
            $this->command->info('✅ Permissions assigned to "default user" role');
        }

        // Optional: Assign to super admin if exists
        $superAdminRole = Role::where('name', 'super-admin')
            ->orWhere('name', 'admin')
            ->orWhere('id', 1)
            ->first();
            
        if ($superAdminRole) {
            $createdPermissions = Permission::whereIn('name', $permissions)->pluck('id');
            $superAdminRole->givePermissionTo($createdPermissions);
            
            $this->command->info('✅ Permissions assigned to "super admin" role');
        }

        $this->command->info('✅ Offline POS permissions created and assigned successfully!');
        $this->command->info('📊 Total permissions created: ' . count($permissions));
    }
}
