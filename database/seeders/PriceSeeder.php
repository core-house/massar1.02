<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Price;
use Illuminate\Database\Seeder;

class PriceSeeder extends Seeder
{
    public function run(): void
    {
        $prices = [
            'قطاعى',
            'جملة',
            'السوق',
        ];

        foreach ($prices as $priceName) {
            Price::firstOrCreate(
                ['name' => $priceName]
            );
        }
    }
}
