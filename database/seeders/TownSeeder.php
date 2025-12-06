<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\City;
use App\Models\Country;
use App\Models\State;
use App\Models\Town;
use Illuminate\Database\Seeder;

class TownSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $country = Country::firstOrCreate(['title' => 'Saudi Arabia']);
        $state = State::firstOrCreate(
            [
                'title' => 'Riyadh',
                'country_id' => $country->id,
            ]
        );
        $city = City::firstOrCreate(
            [
                'title' => 'Riyadh',
                'state_id' => $state->id,
            ]
        );

        Town::firstOrCreate(
            [
                'title' => 'Riyadh',
                'city_id' => $city->id,
            ]
        );
    }
}
