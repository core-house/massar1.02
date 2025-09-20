<?php

namespace Modules\Branches\database\seeders;

use Illuminate\Database\Seeder;
use Modules\Branches\Models\Branch;

class BranchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Branch::firstOrCreate(
            ['code' => 'MAIN'],
            [
                'name' => 'الفرع الرئيسي',
                'address' => 'المقر الرئيسي',
                'is_active' => true,
            ]
        );
        Branch::firstOrCreate(
            ['code' => 'BRANCH2'],
            [
                'name' => 'فرع 2',
                'address' => 'المقر الثانى',
                'is_active' => true,
            ]
        );
    }
}
