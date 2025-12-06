<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Country;
use App\Models\State;
use Illuminate\Database\Seeder;

class StateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $country = Country::firstOrCreate(['title' => 'Saudi Arabia']);

        State::firstOrCreate(
            [
                'title' => 'Riyadh',
                'country_id' => $country->id,
            ]
        );
    }
}
