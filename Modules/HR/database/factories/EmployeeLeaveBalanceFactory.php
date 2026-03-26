<?php

namespace Database\Factories;

use Modules\HR\Models\Employee;
use Modules\HR\Models\EmployeeLeaveBalance;
use Modules\HR\Models\LeaveType;
use Illuminate\Database\Eloquent\Factories\Factory;

class EmployeeLeaveBalanceFactory extends Factory
{
    protected $model = EmployeeLeaveBalance::class;

    public function definition(): array
    {
        return [
            'employee_id' => Employee::factory(),
            'leave_type_id' => LeaveType::factory(),
            'year' => $this->faker->numberBetween(2023, 2025),
            'opening_balance_days' => $this->faker->randomFloat(2, 0, 30),
            'used_days' => $this->faker->randomFloat(2, 0, 15),
            'pending_days' => $this->faker->randomFloat(2, 0, 5),
            'notes' => $this->faker->optional()->sentence(),
        ];
    }
}
