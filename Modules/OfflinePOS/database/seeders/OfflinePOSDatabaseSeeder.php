<?php

namespace Modules\OfflinePOS\Database\Seeders;

use Illuminate\Database\Seeder;

class OfflinePOSDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            OfflinePOSPermissionsSeeder::class,
        ]);
    }
}
