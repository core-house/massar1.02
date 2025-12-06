<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Varibal;
use App\Models\VaribalValue;
use Illuminate\Database\Seeder;

class VaribalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create or get Size varibal
        $sizeVaribal = Varibal::firstOrCreate(
            ['name' => 'المقاس'],
            ['description' => 'مقاسات المنتج المختلفة']
        );

        // Add size values
        $sizes = ['S', 'M', 'L', 'XL', 'XXL'];

        foreach ($sizes as $size) {
            VaribalValue::firstOrCreate(
                [
                    'varibal_id' => $sizeVaribal->id,
                    'value' => $size,
                ]
            );
        }

        // Create or get Color varibal
        $colorVaribal = Varibal::firstOrCreate(
            ['name' => 'اللون'],
            ['description' => 'ألوان المنتج المختلفة']
        );

        // Add color values
        $colors = ['أبيض', 'أسود', 'أحمر', 'أزرق', 'أخضر'];

        foreach ($colors as $color) {
            VaribalValue::firstOrCreate(
                [
                    'varibal_id' => $colorVaribal->id,
                    'value' => $color,
                ]
            );
        }
    }
}
