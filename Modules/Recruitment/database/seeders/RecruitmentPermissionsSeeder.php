<?php

declare(strict_types=1);

namespace Modules\Recruitment\database\seeders;

use Illuminate\Database\Seeder;
use Modules\Authorization\Models\Permission;

class RecruitmentPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // مصفوفة المجموعات مع العناصر داخل كل مجموعة
        $groupedPermissions = [
            'Recruitment' => [
                'Recruitment Dashboard',
                'Job Postings',
                'CVs',
                'Interviews',
                'Interview Schedule',
                'Contracts',
                'Onboardings',
                'Terminations',
                'Recruitment Statistics',
            ],
        ];

        // الأفعال القياسية
        $actions = ['view', 'create', 'edit', 'delete', 'print'];

        $totalCreated = 0;
        $totalSkipped = 0;

        // إنشاء الصلاحيات إن لم تكن موجودة
        foreach ($groupedPermissions as $category => $items) {
            foreach ($items as $base) {
                foreach ($actions as $action) {
                    $fullName = "$action $base";
                    $permission = Permission::firstOrCreate(
                        ['name' => $fullName, 'guard_name' => 'web'],
                        ['category' => $category]
                    );

                    if ($permission->wasRecentlyCreated) {
                        $totalCreated++;
                    } else {
                        $totalSkipped++;
                        // تحديث category إذا كانت مختلفة
                        if ($permission->category !== $category) {
                            $permission->update(['category' => $category]);
                        }
                    }
                }
            }
        }

        $this->command->info('✅ تم إنشاء/تحديث صلاحيات وحدة التوظيف بنجاح!');
        $this->command->info("📊 الصلاحيات الجديدة: {$totalCreated}");
        $this->command->info("🔄 الصلاحيات الموجودة (تم تحديثها): {$totalSkipped}");
        $this->command->info('📝 إجمالي الصلاحيات: ' . ($totalCreated + $totalSkipped));

        // Note: Permissions are assigned directly to users via model_has_permissions table
        // Roles are not used for permission assignment
    }
}

