<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ItemSeeder extends Seeder
{
    public function run(): void
    {
        $unit = DB::table('units')->first();
        $price = DB::table('prices')->first();

        // نضيف وحدة كرتونة لو مش موجودة
        $cartonUnit = DB::table('units')->where('name', 'كرتونة')->first();
        if (!$cartonUnit) {
            $cartonUnitId = DB::table('units')->insertGetId([
                'name' => 'كرتونة',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $cartonUnit = DB::table('units')->find($cartonUnitId);
        }

        if (!$unit || !$price || !$cartonUnit) {
            $this->command->error("Please seed units and prices first.");
            return;
        }

        $items = [
            [
                'name' => 'Test Item 1',
                'code' => 1001,
                'info' => 'First test item',
                'cost' => 50.00,
                'carton_cost' => 600.00,
                'price_piece' => 100.00,
                'price_carton' => 1100.00,
            ],
            [
                'name' => 'Test Item 2',
                'code' => 1002,
                'info' => 'Second test item',
                'cost' => 80.00,
                'carton_cost' => 960.00,
                'price_piece' => 150.00,
                'price_carton' => 1600.00,
            ]
        ];

        foreach ($items as $item) {
            $itemId = DB::table('items')->insertGetId([
                'name' => $item['name'],
                'code' => $item['code'],
                'info' => $item['info'],
                'tenant' => 1,
                'branch' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // item_units
            DB::table('item_units')->insert([
                [
                    'item_id' => $itemId,
                    'unit_id' => $unit->id,
                    'u_val' => 1,
                    'cost' => $item['cost'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'item_id' => $itemId,
                    'unit_id' => $cartonUnit->id,
                    'u_val' => 12,
                    'cost' => $item['carton_cost'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            ]);

            // item_prices
            DB::table('item_prices')->insert([
                [
                    'item_id' => $itemId,
                    'price_id' => $price->id,
                    'unit_id' => $unit->id,
                    'price' => $item['price_piece'],
                    'discount' => 10.00,
                    'tax_rate' => 5.00,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'item_id' => $itemId,
                    'price_id' => $price->id,
                    'unit_id' => $cartonUnit->id,
                    'price' => $item['price_carton'],
                    'discount' => 100.00,
                    'tax_rate' => 5.00,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            ]);
        }
    }
}
