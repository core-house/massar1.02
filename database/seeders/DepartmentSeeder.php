<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Department;
use Illuminate\Support\Facades\DB;

class DepartmentSeeder extends Seeder
{
    public function run()
    {
        DB::table('departments')->insert([
            [
                'title' => 'IT',
                'description' => 'Information Technology Department',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'HR',
                'description' => 'Human Resources Department',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Finance',
                'description' => 'Finance Department',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Marketing',
                'description' => 'Marketing Department',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Sales',
                'description' => 'Sales Department',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}