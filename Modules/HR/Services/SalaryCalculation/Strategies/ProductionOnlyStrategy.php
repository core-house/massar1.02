<?php

declare(strict_types=1);

namespace Modules\HR\Services\SalaryCalculation\Strategies;

use Modules\HR\Models\Employee;
use Modules\HR\Services\SalaryCalculation\Contracts\SalaryCalculationStrategy;

class ProductionOnlyStrategy implements SalaryCalculationStrategy
{
    /**
     * Calculate salary for production-only type
     * Note: Salary is calculated later based on production formula, not attendance hours
     */
    public function calculate(Employee $employee, array $summary): array
    {
        return [
            'basic_salary' => 0,
            'overtime_salary' => 0,
            'total_salary' => 0,
            'calculation_type' => 'production_only',
            'hourly_rate' => 0,
            'daily_rate' => 0,
        ];
    }
}

