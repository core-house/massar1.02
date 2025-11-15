<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Country;

class StateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $country = Country::find(1);
        DB::table('states')->insert([
            'title' => 'Riyadh',
            'country_id' => $country->id,
        ]);
    }
    }