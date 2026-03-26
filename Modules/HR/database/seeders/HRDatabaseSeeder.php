<?php

namespace Modules\HR\database\seeders;

use Illuminate\Database\Seeder;

class HRDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            HRPermissionsSeeder::class,
            // Location Data (Dependencies for Employees)
            CountrySeeder::class,
            StateSeeder::class,
            CitySeeder::class,
            TownSeeder::class,
            // Organizational Data
            DepartmentSeeder::class,
            EmployeesJobSeeder::class,
            ShiftSeeder::class,
            ContractTypeSeeder::class,
            LeaveTypeSeeder::class,
            // Employee Data
            EmployeeSeeder::class,
            // Data dependent on Employees
            AttendanceProcessingTestSeeder::class,
            AttendanceSeeder::class,
            KpiSeeder::class,
            CvSeeder::class,
            // CreateTestAttendanceData::class,
            // AttendanceTestDataSeeder::class,
        ]);
    }
}
