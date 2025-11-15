<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ContractTypeSeeder extends Seeder
{
    public function run()
    {
        DB::table('contract_types')->insert([
            [
                'name' => 'عقد عمل',
                'description' => 'عقد عمل',
            ],
            [
                'name' => 'عقد تدريب',
                'description' => 'عقد تدريب',
            ],
        ]);
    }
}