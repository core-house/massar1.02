<?php

declare(strict_types=1);

namespace Modules\HR\Services\SalaryCalculation;

use Modules\HR\Models\Employee;
use Modules\HR\Services\SalaryCalculation\Contracts\SalaryCalculationStrategy;
use Modules\HR\Services\SalaryCalculation\Strategies\AttendanceOnlyStrategy;
use Modules\HR\Services\SalaryCalculation\Strategies\FixedWithFlexibleHoursStrategy;
use Modules\HR\Services\SalaryCalculation\Strategies\HoursOnlyStrategy;
use Modules\HR\Services\SalaryCalculation\Strategies\HoursWithDailyOvertimeStrategy;
use Modules\HR\Services\SalaryCalculation\Strategies\HoursWithPeriodOvertimeStrategy;
use Modules\HR\Services\SalaryCalculation\Strategies\ProductionOnlyStrategy;

class SalaryStrategyFactory
{
    /**
     * Create appropriate strategy based on employee's salary type
     */
    public static function create(Employee $employee): SalaryCalculationStrategy
    {
        return match ($employee->salary_type) {
            'ساعات عمل فقط' => new HoursOnlyStrategy,
            'ساعات عمل و إضافي يومى' => new HoursWithDailyOvertimeStrategy,
            'ساعات عمل و إضافي للمده' => new HoursWithPeriodOvertimeStrategy,
            'حضور فقط' => new AttendanceOnlyStrategy,
            'إنتاج فقط' => new ProductionOnlyStrategy,
            'ثابت + ساعات عمل مرن' => new FixedWithFlexibleHoursStrategy,
            default => new HoursOnlyStrategy, // Default fallback
        };
    }
}
