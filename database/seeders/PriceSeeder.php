<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PriceSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('prices')->insert([
            [
                'name' => 'قطاعى',
            ],
            [
                'name' => 'جملة',
            ],
            [
                'name' => 'السوق',
            ],
        ]);
    }
}
