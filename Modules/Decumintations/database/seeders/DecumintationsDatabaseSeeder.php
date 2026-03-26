<?php

declare(strict_types=1);

namespace Modules\Decumintations\database\seeders;

use Illuminate\Database\Seeder;

class DecumintationsDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            DecumintationsPermissionsSeeder::class,
        ]);
    }
}
