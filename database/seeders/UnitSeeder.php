<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Unit;
use Illuminate\Database\Seeder;

class UnitSeeder extends Seeder
{
    public function run(): void
    {
        $units = [
            [
                'name' => 'قطعه',
                'code' => 1,
            ],
            [
                'name' => 'كرتونة',
                'code' => 2,
            ],
        ];

        foreach ($units as $unit) {
            Unit::firstOrCreate(
                ['code' => $unit['code']],
                $unit
            );
        }
    }
}
