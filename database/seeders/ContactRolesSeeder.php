<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Inquiries\Models\ContactRole;

class ContactRolesSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            [
                'name' => 'Client',
                'slug' => 'client',
                'description' => 'العميل الذي طلب المشروع',
                'icon' => 'fas fa-user-tie',
                'color' => 'blue',
                'is_active' => true,
            ],
            [
                'name' => 'Main Contractor',
                'slug' => 'main_contractor',
                'description' => 'المقاول الرئيسي المسؤول عن التنفيذ',
                'icon' => 'fas fa-hard-hat',
                'color' => 'orange',
                'is_active' => true,
            ],
            [
                'name' => 'Consultant',
                'slug' => 'consultant',
                'description' => 'الاستشاري الهندسي',
                'icon' => 'fas fa-user-graduate',
                'color' => 'teal',
                'is_active' => true,
            ],
            [
                'name' => 'Owner',
                'slug' => 'owner',
                'description' => 'مالك المشروع',
                'icon' => 'fas fa-crown',
                'color' => 'green',
                'is_active' => true,
            ],
            [
                'name' => 'Engineer',
                'slug' => 'engineer',
                'description' => 'المهندس المعين على المشروع',
                'icon' => 'fas fa-helmet-safety',
                'color' => 'purple',
                'is_active' => true,
            ],
            [
                'name' => 'Supplier',
                'slug' => 'supplier',
                'description' => 'مورد المواد',
                'icon' => 'fas fa-truck',
                'color' => 'amber',
                'is_active' => true,
            ],
            [
                'name' => 'Subcontractor',
                'slug' => 'subcontractor',
                'description' => 'مقاول من الباطن',
                'icon' => 'fas fa-users-cog',
                'color' => 'indigo',
                'is_active' => true,
            ],
        ];

        foreach ($roles as $role) {
            ContactRole::updateOrCreate(
                ['slug' => $role['slug']],
                $role
            );
        }
    }
}
