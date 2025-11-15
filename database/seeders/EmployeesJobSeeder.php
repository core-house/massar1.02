<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EmployeesJobSeeder extends Seeder
{
    public function run()
    {
        DB::table('employees_jobs')->insert([
            [
                'title' => 'Accountant',
                'description' => 'Accountant',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'title' => 'HR Manager',
                'description' => 'HR Manager',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'title' => 'IT Manager',
                'description' => 'IT Manager',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'title' => 'Marketing Manager',
                'description' => 'Marketing Manager',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'title' => 'Sales Manager',
                'description' => 'Sales Manager',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'title' => 'Customer Service Manager',
                'description' => 'Customer Service Manager',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'title' => 'Project Manager',
                'description' => 'Project Manager',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'title' => 'Software Engineer',
                'description' => 'Software Engineer',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'title' => 'Network Engineer',
                'description' => 'Network Engineer',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'title' => 'Database Administrator',
                'description' => 'Database Administrator',
                'created_at' => now(),
                'updated_at' => now()
            ],
        ]);
    }
}
