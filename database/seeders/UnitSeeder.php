<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UnitSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('units')->insert([
            [
                'name' => 'قطعه',
                'code' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'كرتونة',
                'code' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
