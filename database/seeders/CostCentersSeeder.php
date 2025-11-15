<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CostCentersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('cost_centers')->insert([
            'cname' => 'الإدارة العامة',
            'parent_id' => null,
            'info' => 'المركز الرئيسي',
         
        ]);
    }
}
