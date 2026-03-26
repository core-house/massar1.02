<?php

namespace Modules\SOPs\Database\Seeders;

use Illuminate\Database\Seeder;

class SOPsDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            SOPPermissionsSeeder::class,
        ]);
    }
}
