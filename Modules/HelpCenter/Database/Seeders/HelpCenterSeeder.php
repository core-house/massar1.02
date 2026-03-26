<?php

declare(strict_types=1);

namespace Modules\HelpCenter\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Modules\HelpCenter\Models\HelpCategory;

class HelpCenterSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'المحاسبة',       'name_en' => 'Accounting',       'icon' => 'fas fa-calculator',    'sort_order' => 1],
            ['name' => 'المخزون',         'name_en' => 'Inventory',        'icon' => 'fas fa-boxes',         'sort_order' => 2],
            ['name' => 'نقطة البيع',      'name_en' => 'POS',              'icon' => 'fas fa-cash-register', 'sort_order' => 3],
            ['name' => 'الموارد البشرية', 'name_en' => 'HR',               'icon' => 'fas fa-users',         'sort_order' => 4],
            ['name' => 'التقارير',        'name_en' => 'Reports',          'icon' => 'fas fa-chart-bar',     'sort_order' => 5],
            ['name' => 'الإعدادات',       'name_en' => 'Settings',         'icon' => 'fas fa-cog',           'sort_order' => 6],
            ['name' => 'التصنيع',         'name_en' => 'Manufacturing',    'icon' => 'fas fa-industry',      'sort_order' => 7],
            ['name' => 'المشاريع',        'name_en' => 'Projects',         'icon' => 'fas fa-project-diagram','sort_order' => 8],
        ];

        foreach ($categories as $cat) {
            HelpCategory::firstOrCreate(
                ['slug' => Str::slug($cat['name_en'])],
                array_merge($cat, ['is_active' => true])
            );
        }
    }
}
