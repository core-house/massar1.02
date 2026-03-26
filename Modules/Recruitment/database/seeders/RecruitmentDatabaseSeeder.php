<?php

declare(strict_types=1);

namespace Modules\Recruitment\database\seeders;

use Illuminate\Database\Seeder;

class RecruitmentDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            RecruitmentPermissionsSeeder::class,
        ]);
    }
}
