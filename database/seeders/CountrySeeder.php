<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */     
    public function run()
    {
        DB::table('countries')->insert([
            'title' => 'Saudi Arabia',
        ]);
    }
}