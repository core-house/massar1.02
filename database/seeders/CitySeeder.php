<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\State;

class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */ 
    public function run()
    {
        $state = State::find(1);
        DB::table('cities')->insert([
            'title' => 'Riyadh',
            'state_id' => $state->id,
        ]);
    }
}