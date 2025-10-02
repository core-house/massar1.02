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
                'cost' => 00.00,
                'carton_cost' => 00.00,
                'price_piece' => 00.00,
                'price_carton' => 00.00,
            ],
            [
                'name' => 'Test Item 2',
                'code' => 1002,
                'info' => 'Second test item',
                'cost' => 00.00,
                'carton_cost' => 00.00,
                'price_piece' => 00.00,
                'price_carton' => 00.00,
            ],
            [
                'name' => 'منتج 1',
                'code' => 2001,
                'info' => 'هذا منتج 1',
                'cost' => 00.00,
                'carton_cost' => 00.00,
                'price_piece' => 00.00,
                'price_carton' => 00.00,
            ],
            [
                'name' => 'منتج 2',
                'code' => 2002,
                'info' => 'هذا منتج 2',
                'cost' => 00.00,    // 90   
                'carton_cost' => 00.00,
                'price_piece' => 00.00,
                'price_carton' => 00.00,
            ],
            [
                'name' => 'خام 1',
                'code' => 3001,
                'info' => 'هذا خام 1',
                'cost' => 00.00,
                'carton_cost' => 00.00,
                'price_piece' => 00.00,
                'price_carton' => 00.00,
            ],
            [
                'name' => 'خام 2',
                'code' => 3002,
                'info' => 'هذا خام 2',
                'cost' => 00.00,
                'carton_cost' => 00.00,
                'price_piece' => 00.00,
                'price_carton' => 00.00,
            ],
        ];

        foreach ($items as $item) {
            $itemId = DB::table('items')->insertGetId([
                'name' => $item['name'],
                'type' => 1, // Inventory
                'code' => $item['code'],
                'info' => $item['info'],
                'tenant' => 1,
                'branch_id' => null, // بدل الرقم الثابت
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
                    'discount' => 00.00,
                    'tax_rate' => 00.00,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'item_id' => $itemId,
                    'price_id' => $price->id,
                    'unit_id' => $cartonUnit->id,
                    'price' => $item['price_carton'],
                    'discount' => 00.00,
                    'tax_rate' => 00.00,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            ]);
        }
    }
}
