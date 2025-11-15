<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\City;
class TownSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $city = City::find(1);
        DB::table('towns')->insert([
            'title' => 'Riyadh',
            'city_id' => $city->id,
        ]);
    }
}