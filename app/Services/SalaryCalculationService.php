<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\AttendanceProcessing;
use App\Models\Employee;
use App\Models\LeaveRequest;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SalaryCalculationService
{
    /**
     * Calculate salary for an employee for a specific period
     */
    public function calculateSalary(Employee $employee, Carbon $startDate, Carbon $endDate): array
    {
        return DB::transaction(function () use ($employee, $startDate, $endDate) {
            // Check if employee is active
            if ($employee->isInactive()) {
                // Return empty data for inactive employees
                return [
                    'summary' => [
                        'total_days' => 0,
                        'working_days' => 0,
                        'overtime_days' => 0,
                        'present_days' => 0,
                        'absent_days' => 0,
                        'paid_leave_days' => 0,
                        'unpaid_leave_days' => 0,
                        'total_hours' => 0,
                        'actual_hours' => 0,
                        'overtime_hours' => 0,
                        'late_hours' => 0,
                        'holiday_days' => 0,
                    ],
                    'salary_data' => [
                        'basic_salary' => 0,
                        'overtime_salary' => 0,
                        'total_salary' => 0,
                        'daily_rate' => 0,
                        'hourly_rate' => 0,
                    ],
                    'details' => [],
                ];
            }

            // Get attendance records for the period
            $attendances = $this->getAttendanceRecords($employee, $startDate, $endDate);
            // dd($attendances);
            // Process attendance and calculate hours
            $processedData = $this->processAttendanceData($employee, $attendances, $startDate, $endDate);
            // dd($processedData);
            // Calculate salary based on type
            $salaryData = $this->calculateSalaryByType($employee, $processedData);

            return [
                'summary' => $processedData['summary'],
                'salary_data' => $salaryData,
                'details' => $processedData['daily_details'],
            ];
        });
    }

    /**
     * Get attendance records for employee in date range
     */
    private function getAttendanceRecords(Employee $employee, Carbon $startDate, Carbon $endDate): Collection
    {
        // Get the actual records for the date range
        $records = Attendance::where('employee_id', $employee->id)
            ->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->orderBy('date')
            ->orderBy('time')
            ->get();

        return $records;
    }

    /**
     * Process attendance data and calculate working hours
     */
    private function processAttendanceData(Employee $employee, Collection $attendances, Carbon $startDate, Carbon $endDate): array
    {
        $dailyDetails = [];
        $summary = [
            'total_days' => 0,
            'working_days' => 0,
            'overtime_days' => 0,
            'present_days' => 0,
            'absent_days' => 0,
            'paid_leave_days' => 0,
            'unpaid_leave_days' => 0,
            'total_hours' => 0,
            'actual_hours' => 0,
            'overtime_hours' => 0,
            'late_hours' => 0,
            'holiday_days' => 0,
        ];

        // Group attendances by date (extract date part only)
        $attendanceByDate = $attendances->groupBy(function ($attendance) {
            return Carbon::parse($attendance->date)->format('Y-m-d');
        });
        // dd($attendanceByDate);
        // Process each day in the period
        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            $dateStr = $date->format('Y-m-d');
            $dayOfWeek = $date->dayOfWeek; // 0=Sunday, 1=Monday, etc.

            $summary['total_days']++;

            // Check if it's a working day or overtime day or holiday
            $dayType = $this->dayType($employee, $dayOfWeek, $attendanceByDate, $dateStr);
            if ($dayType == 'holiday') {
                $summary['holiday_days']++;
            }
            if ($dayType == 'working_day') {
                $summary['working_days']++;
            }
            if ($dayType == 'overtime_day') {
                $summary['overtime_days']++;
            }

            // Get attendance for this day
            $dayAttendances = $attendanceByDate->get($dateStr, collect());
            // dd($dayAttendances);
            // Process day attendance
            $dayData = $this->processDayAttendance($employee, $dayAttendances, $date, $dayType, $startDate, $endDate);

            $dailyDetails[$dateStr] = $dayData;

            // Update summary
            if ($dayData['status'] === 'present') {
                $summary['present_days']++;
                $summary['actual_hours'] += $dayData['actual_hours'];
                $summary['overtime_hours'] += $dayData['overtime_hours'];
                $summary['late_hours'] += $dayData['late_hours'];
            } elseif ($dayData['status'] === 'absent') {
                $summary['absent_days']++;
            } elseif ($dayData['status'] === 'paid_leave') {
                $summary['paid_leave_days']++;
                $summary['actual_hours'] += $dayData['actual_hours']; // Count hours for paid leave
                $summary['present_days']++; // Count as present day for salary calculation
            } elseif ($dayData['status'] === 'unpaid_leave') {
                $summary['unpaid_leave_days']++;
                // Unpaid leave: no hours, no salary, count as absent for salary calculation
                // Don't add to absent_days to differentiate from regular absence
            }
        }

        $summary['total_hours'] = $summary['working_days'] * $this->getExpectedHours($employee);

        return [
            'summary' => $summary,
            'daily_details' => $dailyDetails,
        ];
    }

    /**
     * Check if a day is a working day for the employee or not and we make it as working day or over time day or holiday
     */
    private function dayType(Employee $employee, int $dayOfWeek, Collection $attendanceByDate, string $dateStr): string
    {
        if (! $employee->shift) {
            return 'no_shift';
        }

        $workingDays = json_decode($employee->shift->days, true) ?? [];

        // Convert day of week to match the stored format
        $dayMap = [
            0 => 'sunday',
            1 => 'monday',
            2 => 'tuesday',
            3 => 'wednesday',
            4 => 'thursday',
            5 => 'friday',
            6 => 'saturday',
        ];

        $currentDay = $dayMap[$dayOfWeek] ?? '';
        $attendanceCount = $attendanceByDate->get($dateStr, collect())->count();

        if (in_array($currentDay, $workingDays)) {
            return 'working_day';
        } elseif (! in_array($currentDay, $workingDays) && $attendanceCount == 0) {
            return 'holiday';
        } elseif (! in_array($currentDay, $workingDays) && $attendanceCount > 0) {
            return 'overtime_day';
        }

        return 'no_shift';
    }

    /**
     * Check if employee has an approved paid leave for a specific date
     */
    private function hasApprovedPaidLeave(Employee $employee, Carbon $date): ?LeaveRequest
    {
        $dateStr = $date->format('Y-m-d');

        $leaveRequest = LeaveRequest::where('employee_id', $employee->id)
            ->where('status', 'approved')
            ->whereDate('start_date', '<=', $dateStr)
            ->whereDate('end_date', '>=', $dateStr)
            ->with('leaveType')
            ->first();

        // Check if the leave type is paid
        if ($leaveRequest && $leaveRequest->leaveType && $leaveRequest->leaveType->is_paid) {
            return $leaveRequest;
        }

        return null;
    }

    /**
     * Check if employee has an approved unpaid leave for a specific date
     */
    private function hasApprovedUnpaidLeave(Employee $employee, Carbon $date): ?LeaveRequest
    {
        $dateStr = $date->format('Y-m-d');

        $leaveRequest = LeaveRequest::where('employee_id', $employee->id)
            ->where('status', 'approved')
            ->whereDate('start_date', '<=', $dateStr)
            ->whereDate('end_date', '>=', $dateStr)
            ->with('leaveType')
            ->first();

        // Check if the leave type is unpaid
        if ($leaveRequest && $leaveRequest->leaveType && ! $leaveRequest->leaveType->is_paid) {
            return $leaveRequest;
        }

        return null;
    }

    /**
     * Process attendance for a single day
     */
    private function processDayAttendance(Employee $employee, Collection $attendances, Carbon $date, string $dayType, Carbon $startDate, Carbon $endDate): array
    {
        $shift = $employee->shift;

        // Check if employee has approved paid or unpaid leave for this day
        $paidLeave = $this->hasApprovedPaidLeave($employee, $date);
        $unpaidLeave = $this->hasApprovedUnpaidLeave($employee, $date);

        if (! $shift) {
            // If employee has paid leave, count as leave (not absent)
            if ($paidLeave) {
                return [
                    'date' => $date->format('Y-m-d'),
                    'status' => 'paid_leave',
                    'day_type' => $dayType,
                    'expected_hours' => 0,
                    'actual_hours' => 0,
                    'overtime_hours' => 0,
                    'late_hours' => 0,
                    'check_in_time' => null,
                    'check_out_time' => null,
                    'notes' => 'إجازة مدفوعة الأجر: '.($paidLeave->leaveType->name ?? ''),
                ];
            }

            // If employee has unpaid leave, count as unpaid leave (not absent)
            if ($unpaidLeave) {
                return [
                    'date' => $date->format('Y-m-d'),
                    'status' => 'unpaid_leave',
                    'day_type' => $dayType,
                    'expected_hours' => 0,
                    'actual_hours' => 0,
                    'overtime_hours' => 0,
                    'late_hours' => 0,
                    'check_in_time' => null,
                    'check_out_time' => null,
                    'notes' => 'إجازة غير مدفوعة الأجر: '.($unpaidLeave->leaveType->name ?? ''),
                ];
            }

            return [
                'date' => $date->format('Y-m-d'),
                'status' => 'no_shift',
                'day_type' => $dayType,
                'expected_hours' => 0,
                'actual_hours' => 0,
                'overtime_hours' => 0,
                'late_hours' => 0,
                'check_in_time' => null,
                'check_out_time' => null,
                'notes' => null,
            ];
        }

        // Separate check-ins and check-outs
        $checkIns = $attendances->whereIn('type', ['check_in'])->sortBy('time');
        $checkOuts = $attendances->whereIn('type', ['check_out'])->sortByDesc('time');

        // Get valid check-in (within range) - pass the date for proper comparison
        $validCheckIn = $this->getFirstValidCheckIn($shift, $checkIns, $date);

        // Get salary type to determine processing logic
        $salaryType = $employee->salary_type;
        $expectedHours = $this->getExpectedHours($employee);
        $actualHours = 0;
        $overtimeHours = 0;
        $lateHours = 0;
        $result = [];
        $notes = [];

        // Handle paid leave first (applies to all salary types)
        if ($paidLeave && ! $validCheckIn && $checkOuts->isEmpty()) {
            return [
                'date' => $date->format('Y-m-d'),
                'status' => 'paid_leave',
                'day_type' => $dayType,
                'expected_hours' => $expectedHours,
                'actual_hours' => $expectedHours,
                'overtime_hours' => 0,
                'late_hours' => 0,
                'check_in_time' => null,
                'check_out_time' => null,
                'notes' => 'إجازة مدفوعة الأجر: '.($paidLeave->leaveType->name ?? ''),
            ];
        }

        // Handle unpaid leave (applies to all salary types)
        if ($unpaidLeave && ! $validCheckIn && $checkOuts->isEmpty()) {
            return [
                'date' => $date->format('Y-m-d'),
                'status' => 'unpaid_leave',
                'day_type' => $dayType,
                'expected_hours' => $expectedHours,
                'actual_hours' => 0, // No hours for unpaid leave
                'overtime_hours' => 0,
                'late_hours' => 0,
                'check_in_time' => null,
                'check_out_time' => null,
                'notes' => 'إجازة غير مدفوعة الأجر: '.($unpaidLeave->leaveType->name ?? ''),
            ];
        }

        // Handle holiday
        if ($dayType == 'holiday' && ! $validCheckIn && $checkOuts->isEmpty()) {
            return [
                'date' => $date->format('Y-m-d'),
                'status' => 'holiday',
                'day_type' => $dayType,
                'expected_hours' => 0,
                'actual_hours' => 0,
                'overtime_hours' => 0,
                'late_hours' => 0,
                'check_in_time' => null,
                'check_out_time' => null,
                'notes' => null,
            ];
        }

        // Handle absence (no check-in and no check-out, and no leave)
        // Only count as absent if there's no paid or unpaid leave
        if (! $validCheckIn && $checkOuts->isEmpty() && $dayType == 'working_day' && ! $paidLeave && ! $unpaidLeave) {
            return [
                'date' => $date->format('Y-m-d'),
                'status' => 'absent',
                'day_type' => $dayType,
                'expected_hours' => $expectedHours,
                'actual_hours' => 0,
                'overtime_hours' => 0,
                'late_hours' => 0,
                'check_in_time' => null,
                'check_out_time' => null,
                'notes' => null,
            ];
        }

        // Process based on salary type
        switch ($salaryType) {
            case 'ساعات عمل فقط':
                // 1. بصمة دخول في النطاق + أي بصمة خروج → يحسب اليوم كاملاً بدون أي إضافي
                // التأخير لا يُحسب لهذا النوع
                if ($validCheckIn && $checkOuts->isNotEmpty()) {
                    $anyCheckOut = $this->getAnyCheckOut($checkOuts);
                    $checkInTime = Carbon::parse($validCheckIn->time);
                    $checkOutTime = Carbon::parse($anyCheckOut->time);

                    return [
                        'date' => $date->format('Y-m-d'),
                        'status' => 'present',
                        'day_type' => $dayType,
                        'expected_hours' => $expectedHours,
                        'actual_hours' => $expectedHours, // يوم كامل فقط بدون إضافي
                        'overtime_hours' => 0, // لا يوجد إضافي
                        'late_hours' => 0, // لا يُحسب التأخير لهذا النوع
                        'check_in_time' => $validCheckIn->time,
                        'check_out_time' => $anyCheckOut->time,
                        'notes' => null,
                    ];
                }
                break;

            case 'ساعات عمل و إضافي يومى':
                // 2. يحسب اليوم (8 ساعات) + الإضافي إذا كانت بصمة الخروج بعد ending_check_out
                // الراتب الإضافي للساعات بعد end_time (ولكن فقط إذا كانت بصمة الخروج بعد ending_check_out)
                if ($validCheckIn && $checkOuts->isNotEmpty()) {
                    $anyCheckOut = $this->getAnyCheckOut($checkOuts);
                    // Use the date from $date parameter to create proper Carbon objects
                    $checkInTime = Carbon::parse($date->format('Y-m-d').' '.$validCheckIn->time);
                    $checkOutTime = Carbon::parse($date->format('Y-m-d').' '.$anyCheckOut->time);

                    // Basic hours = expected hours
                    $basicHours = $expectedHours;

                    // Overtime = hours after end_time, but only if check-out is after ending_check_out
                    $isAfterEndingCheckOut = $this->isCheckOutAfterEndingCheckOut($shift, $checkOutTime);
                    $overtimeHours = 0;

                    if ($isAfterEndingCheckOut) {
                        // Calculate overtime after end_time (official shift end time)
                        $overtimeHours = $this->getOvertimeHoursAfterEndTime($shift, $checkOutTime);
                    }

                    $lateHours = $this->calculateLateHours($shift, $checkInTime);

                    return [
                        'date' => $date->format('Y-m-d'),
                        'status' => 'present',
                        'day_type' => $dayType,
                        'expected_hours' => $expectedHours,
                        'actual_hours' => $basicHours,
                        'overtime_hours' => $overtimeHours > 0 ? $overtimeHours : 0,
                        'late_hours' => $lateHours > 0 ? $lateHours : 0,
                        'check_in_time' => $validCheckIn->time,
                        'check_out_time' => $anyCheckOut->time,
                        'notes' => null,
                    ];
                }
                break;

            case 'ساعات عمل و إضافي للمده':
                // 3. نفس "إضافي يومى" - يحسب اليوم (8 ساعات) + الإضافي إذا كانت بصمة الخروج بعد ending_check_out
                // الراتب الإضافي للساعات بعد end_time (ولكن فقط إذا كانت بصمة الخروج بعد ending_check_out)
                if ($validCheckIn && $checkOuts->isNotEmpty()) {
                    $anyCheckOut = $this->getAnyCheckOut($checkOuts);
                    // Use the date from $date parameter to create proper Carbon objects
                    $checkInTime = Carbon::parse($date->format('Y-m-d').' '.$validCheckIn->time);
                    $checkOutTime = Carbon::parse($date->format('Y-m-d').' '.$anyCheckOut->time);

                    // Basic hours = expected hours
                    $basicHours = $expectedHours;

                    // Overtime = hours after end_time, but only if check-out is after ending_check_out
                    $isAfterEndingCheckOut = $this->isCheckOutAfterEndingCheckOut($shift, $checkOutTime);
                    $overtimeHours = 0;

                    if ($isAfterEndingCheckOut) {
                        // Calculate overtime after end_time (official shift end time)
                        $overtimeHours = $this->getOvertimeHoursAfterEndTime($shift, $checkOutTime);
                    }

                    $lateHours = $this->calculateLateHours($shift, $checkInTime);

                    return [
                        'date' => $date->format('Y-m-d'),
                        'status' => 'present',
                        'day_type' => $dayType,
                        'expected_hours' => $expectedHours,
                        'actual_hours' => $basicHours,
                        'overtime_hours' => $overtimeHours > 0 ? $overtimeHours : 0,
                        'late_hours' => $lateHours > 0 ? $lateHours : 0,
                        'check_in_time' => $validCheckIn->time,
                        'check_out_time' => $anyCheckOut->time,
                        'notes' => null,
                    ];
                }
                break;

            case 'حضور فقط':
                // 4. في حالة وجود بصمة دخول داخل نطاق الدخول يحسب يوم كامل حتى لو ما فيش بصمة خروج
                // التأخير لا يُحسب لهذا النوع
                if ($validCheckIn) {
                    $checkInTime = Carbon::parse($validCheckIn->time);

                    // Get check-out if exists (for display purposes)
                    $anyCheckOut = $checkOuts->isNotEmpty() ? $this->getAnyCheckOut($checkOuts) : null;

                    return [
                        'date' => $date->format('Y-m-d'),
                        'status' => 'present',
                        'day_type' => $dayType,
                        'expected_hours' => $expectedHours,
                        'actual_hours' => $expectedHours, // Full day
                        'overtime_hours' => 0,
                        'late_hours' => 0, // لا يُحسب التأخير لهذا النوع
                        'check_in_time' => $validCheckIn->time,
                        'check_out_time' => $anyCheckOut ? $anyCheckOut->time : null,
                        'notes' => $anyCheckOut ? null : 'يوم كامل بناءً على بصمة الدخول فقط',
                    ];
                }
                break;

            case 'إنتاج فقط':
                // 5. البصمة ملهاش لازمة في الراتب لأن الراتب بناء على الإنتاج وليس ساعات الحضور
                // Always count as full day if there's any check-in
                if ($checkIns->isNotEmpty()) {
                    $firstCheckIn = $checkIns->first();
                    $anyCheckOut = $checkOuts->isNotEmpty() ? $this->getAnyCheckOut($checkOuts) : null;

                    return [
                        'date' => $date->format('Y-m-d'),
                        'status' => 'present',
                        'day_type' => $dayType,
                        'expected_hours' => $expectedHours,
                        'actual_hours' => $expectedHours, // Full day for display
                        'overtime_hours' => 0,
                        'late_hours' => 0,
                        'check_in_time' => $firstCheckIn->time,
                        'check_out_time' => $anyCheckOut ? $anyCheckOut->time : null,
                        'notes' => 'الراتب بناءً على الإنتاج وليس ساعات الحضور',
                    ];
                }
                break;

            default:
                // Default behavior (same as 'ساعات عمل فقط')
                // التأخير لا يُحسب لهذا النوع
                if ($validCheckIn && $checkOuts->isNotEmpty()) {
                    $anyCheckOut = $this->getAnyCheckOut($checkOuts);
                    $checkInTime = Carbon::parse($validCheckIn->time);
                    $checkOutTime = Carbon::parse($anyCheckOut->time);

                    $actualHours = $checkInTime->diffInHours($checkOutTime, false);

                    if ($actualHours > $expectedHours) {
                        $overtimeHours = $actualHours - $expectedHours;
                    }

                    return [
                        'date' => $date->format('Y-m-d'),
                        'status' => 'present',
                        'day_type' => $dayType,
                        'expected_hours' => $expectedHours,
                        'actual_hours' => $actualHours - $overtimeHours,
                        'overtime_hours' => $overtimeHours > 0 ? $overtimeHours : 0,
                        'late_hours' => 0, // لا يُحسب التأخير لهذا النوع
                        'check_in_time' => $validCheckIn->time,
                        'check_out_time' => $anyCheckOut->time,
                        'notes' => null,
                    ];
                }
                break;
        }

        // Default case: no valid check-in or no attendance
        return [
            'date' => $date->format('Y-m-d'),
            'status' => 'absent',
            'day_type' => $dayType,
            'expected_hours' => $expectedHours,
            'actual_hours' => 0,
            'overtime_hours' => 0,
            'late_hours' => 0,
            'check_in_time' => null,
            'check_out_time' => null,
            'notes' => null,
        ];
    }

    /**
     * Get expected working hours for employee
     */
    private function getExpectedHours(Employee $employee): float
    {
        if (! $employee->shift) {
            return 8.0; // Default 8 hours
        }

        $startTime = Carbon::parse($employee->shift->start_time);
        $endTime = Carbon::parse($employee->shift->end_time);

        return $startTime->diffInHours($endTime, false);
    }

    /**
     * Get first valid check-in within the allowed range
     */
    private function getFirstValidCheckIn($shift, Collection $checkIns, Carbon $date)
    {
        if (! $shift->beginning_check_in || ! $shift->ending_check_in) {
            // If no range is defined, return first check-in
            return $checkIns->first();
        }

        // Use the date from parameter for proper comparison
        $dateStr = $date->format('Y-m-d');
        $beginningCheckIn = Carbon::parse($dateStr.' '.$shift->beginning_check_in);
        $endingCheckIn = Carbon::parse($dateStr.' '.$shift->ending_check_in);

        foreach ($checkIns as $checkIn) {
            $checkInTime = Carbon::parse($dateStr.' '.$checkIn->time);

            // Check if check-in is within the allowed range
            if ($checkInTime->between($beginningCheckIn, $endingCheckIn)) {
                return $checkIn;
            }
        }

        return null;
    }

    /**
     * Get last valid check-out within the allowed range
     * If no check-out is within the range but there's a check-out after ending_check_out,
     * accept it as valid (likely overtime)
     */
    private function getLastValidCheckOut($shift, Collection $checkOuts, Carbon $date)
    {
        if (! $shift->beginning_check_out || ! $shift->ending_check_out) {
            // If no range is defined, return last check-out (first in sorted descending collection)
            return $checkOuts->first();
        }

        // Use the date from parameter for proper comparison
        $dateStr = $date->format('Y-m-d');
        $beginningCheckOut = Carbon::parse($dateStr.' '.$shift->beginning_check_out);
        $endingCheckOut = Carbon::parse($dateStr.' '.$shift->ending_check_out);

        // First, try to find a check-out within the allowed range
        foreach ($checkOuts as $checkOut) {
            $checkOutTime = Carbon::parse($dateStr.' '.$checkOut->time);

            // Check if check-out is within the allowed range
            if ($checkOutTime->between($beginningCheckOut, $endingCheckOut)) {
                return $checkOut;
            }
        }

        // If no check-out within range, check for check-outs after ending_check_out
        // These are likely overtime check-outs and should be accepted
        foreach ($checkOuts as $checkOut) {
            $checkOutTime = Carbon::parse($dateStr.' '.$checkOut->time);

            // Accept check-out if it's after ending_check_out (overtime)
            // But reject if it's before beginning_check_out (too early, likely wrong)
            if ($checkOutTime->gt($endingCheckOut)) {
                return $checkOut;
            }
        }

        // If no valid check-out found (including overtime), return null
        return null;
    }

    /**
     * Get any check-out (regardless of range) - used for certain salary types
     */
    private function getAnyCheckOut(Collection $checkOuts)
    {
        return $checkOuts->first(); // Last check-out (sorted descending, so first is last)
    }

    /**
     * Get overtime hours after ending_check_out
     */
    private function getOvertimeHoursAfterEndingCheckOut($shift, Carbon $checkOutTime): float
    {
        if (! $shift->ending_check_out) {
            return 0;
        }

        $endingCheckOut = Carbon::parse($shift->ending_check_out);

        if ($checkOutTime->gt($endingCheckOut)) {
            return $checkOutTime->diffInHours($endingCheckOut, false);
        }

        return 0;
    }

    /**
     * Get overtime hours after end_time (official shift end time)
     * This is used for calculating overtime salary
     */
    private function getOvertimeHoursAfterEndTime($shift, Carbon $checkOutTime): float
    {
        if (! $shift->end_time) {
            return 0;
        }

        // Use the same date as checkOutTime for proper comparison
        $endTime = Carbon::parse($checkOutTime->format('Y-m-d').' '.$shift->end_time);

        if ($checkOutTime->gt($endTime)) {
            // Calculate difference: from endTime to checkOutTime (positive value)
            $overtimeHours = $endTime->diffInHours($checkOutTime, false);

            return $overtimeHours;
        }

        return 0;
    }

    /**
     * Check if check-out is after ending_check_out
     */
    private function isCheckOutAfterEndingCheckOut($shift, Carbon $checkOutTime): bool
    {
        if (! $shift->ending_check_out) {
            return false;
        }

        // Use the same date as checkOutTime for proper comparison
        $endingCheckOut = Carbon::parse($checkOutTime->format('Y-m-d').' '.$shift->ending_check_out);

        return $checkOutTime->gt($endingCheckOut);
    }

    /**
     * Check if period is full month (30 days)
     */
    private function isFullMonthPeriod(Carbon $startDate, Carbon $endDate): bool
    {
        $daysDiff = $startDate->diffInDays($endDate) + 1;

        return $daysDiff >= 30;
    }

    /**
     * Calculate late hours based on actual start time
     * Only calculates late hours if:
     * 1. Check-in is within the check-in range (beginning_check_in to ending_check_in)
     * 2. Check-in is after the actual start time (start_time)
     * Note: Late hours are calculated from start_time, not from allowed time (start_time + allowed_late_minutes)
     */
    private function calculateLateHours($shift, Carbon $checkInTime): float
    {
        // Use the same date as checkInTime for proper comparison
        $dateStr = $checkInTime->format('Y-m-d');

        // Get the actual shift start time
        $shiftStart = Carbon::parse($dateStr.' '.$shift->start_time);
        $allowedLateMinutes = $shift->allowed_late_minutes ?? 0;
        $allowedCheckInTime = $shiftStart->copy()->addMinutes($allowedLateMinutes);

        // If no beginning_check_in is defined, check if check-in is after start_time
        if (! $shift->beginning_check_in) {
            // If check-in is after the start time, calculate late hours from start_time
            if ($checkInTime->gt($shiftStart)) {
                // Calculate late hours from actual start time
                return abs($checkInTime->diffInHours($shiftStart));
            }

            return 0;
        }

        // The valid range is from beginning_check_in to ending_check_in (inclusive)
        $beginningCheckIn = Carbon::parse($dateStr.' '.$shift->beginning_check_in);
        $endingCheckIn = Carbon::parse($dateStr.' '.$shift->ending_check_in);

        // Check if check-in is within the valid range (beginning_check_in to ending_check_in inclusive)
        if (! $checkInTime->between($beginningCheckIn, $endingCheckIn)) {
            // Check-in is outside the valid range, no late hours calculation
            return 0;
        }

        // Check-in is within the valid range, now check if it's after the start time
        // Only calculate late hours if check-in is after the allowed time (start_time + allowed_late_minutes)
        // This ensures we only count lateness when it exceeds the grace period
        if ($checkInTime->gt($allowedCheckInTime)) {
            // Calculate late hours from actual start time (not from allowed time)
            // This gives the total late hours from the official start time
            return abs($checkInTime->diffInHours($shiftStart));
        }

        // Check-in is within the range and before or equal to allowed time, no late hours
        return 0;
    }

    /**
     * Calculate salary based on employee's salary type
     */
    private function calculateSalaryByType(Employee $employee, array $processedData): array
    {
        $summary = $processedData['summary'];
        $salaryType = $employee->salary_type;

        switch ($salaryType) {
            case 'ساعات عمل فقط':
                return $this->calculateHoursOnlySalary($employee, $summary);

            case 'ساعات عمل و إضافي يومى':
                return $this->calculateHoursWithDailyOvertimeSalary($employee, $summary);

            case 'ساعات عمل و إضافي للمده':
                return $this->calculateHoursWithPeriodOvertimeSalary($employee, $summary);

            case 'حضور فقط':
                return $this->calculateAttendanceOnlySalary($employee, $summary);

            case 'إنتاج فقط':
                return $this->calculateProductionOnlySalary($employee, $summary);

            default:
                return $this->calculateHoursOnlySalary($employee, $summary);
        }
    }

    /**
     * Calculate salary for hours-only type
     */
    private function calculateHoursOnlySalary(Employee $employee, array $summary): array
    {
        $currentMonthDays = now()->daysInMonth;
        $shiftHours = $this->getExpectedHours($employee);
        $hourlyRate = $employee->salary / $currentMonthDays / $shiftHours;
        $dailyRate = $employee->salary / $currentMonthDays;
        $basicSalary = $summary['actual_hours'] * $hourlyRate;
        $overtimeSalary = $summary['overtime_hours'] * $hourlyRate * ($employee->additional_hour_calculation ?? 1.5);
        
        // Calculate deductions
        // Unpaid leave: full day deduction for each unpaid leave day
        $unpaidLeaveDeduction = ($summary['unpaid_leave_days'] ?? 0) * $dailyRate;
        
        // Absent days: deduction based on late_day_calculation (e.g., if late_day_calculation = 2, 1 absent day = 2 days deduction)
        $lateDayCalculation = $employee->late_day_calculation ?? 1.0;
        $absentDaysDeduction = ($summary['absent_days'] ?? 0) * $lateDayCalculation * $dailyRate;
        
        $totalDeductions = $unpaidLeaveDeduction + $absentDaysDeduction;

        return [
            'basic_salary' => round($basicSalary, 2),
            'overtime_salary' => round($overtimeSalary, 2),
            'unpaid_leave_deduction' => round($unpaidLeaveDeduction, 2),
            'absent_days_deduction' => round($absentDaysDeduction, 2),
            'total_salary' => round($basicSalary + $overtimeSalary - $totalDeductions, 2),
            'calculation_type' => 'hours_only',
            'hourly_rate' => $hourlyRate,
            'daily_rate' => $dailyRate,
        ];
    }

    /**
     * Calculate salary for hours with daily overtime
     */
    private function calculateHoursWithDailyOvertimeSalary(Employee $employee, array $summary): array
    {
        $currentMonthDays = now()->daysInMonth;
        $shiftHours = $this->getExpectedHours($employee);
        $hourlyRate = $employee->salary / $currentMonthDays / $shiftHours;
        $dailyRate = $employee->salary / $currentMonthDays;
        $basicSalary = $summary['actual_hours'] * $hourlyRate;
        $overtimeSalary = $summary['overtime_hours'] * $hourlyRate * ($employee->additional_hour_calculation ?? 1.5);
        
        // Calculate late hours deduction
        // late_hour_calculation: how many hours each late hour is counted as (e.g., 0.5 means 1 late hour = 0.5 hours deduction)
        $lateHourCalculation = $employee->late_hour_calculation ?? 1.0;
        $lateHoursDeduction = $summary['late_hours'] * $lateHourCalculation;
        $lateDeductionAmount = $lateHoursDeduction * $hourlyRate;
        
        // Calculate deductions for unpaid leave and absent days
        // Unpaid leave: full day deduction for each unpaid leave day
        $unpaidLeaveDeduction = ($summary['unpaid_leave_days'] ?? 0) * $dailyRate;
        
        // Absent days: deduction based on late_day_calculation (e.g., if late_day_calculation = 2, 1 absent day = 2 days deduction)
        $lateDayCalculation = $employee->late_day_calculation ?? 1.0;
        $absentDaysDeduction = ($summary['absent_days'] ?? 0) * $lateDayCalculation * $dailyRate;
        
        $totalDeductions = $lateDeductionAmount + $unpaidLeaveDeduction + $absentDaysDeduction;

        return [
            'basic_salary' => round($basicSalary, 2),
            'overtime_salary' => round($overtimeSalary, 2),
            'late_hours_deduction' => round($lateDeductionAmount, 2),
            'unpaid_leave_deduction' => round($unpaidLeaveDeduction, 2),
            'absent_days_deduction' => round($absentDaysDeduction, 2),
            'total_salary' => round($basicSalary + $overtimeSalary - $totalDeductions, 2),
            'calculation_type' => 'hours_with_daily_overtime',
            'hourly_rate' => $hourlyRate,
            'daily_rate' => $dailyRate,
        ];
    }

    /**
     * Calculate salary for hours with period overtime
     */
    private function calculateHoursWithPeriodOvertimeSalary(Employee $employee, array $summary): array
    {

        $currentMonthDays = now()->daysInMonth;
        $shiftHours = $this->getExpectedHours($employee);
        $hourlyRate = $employee->salary / $currentMonthDays / $shiftHours;
        $dailyRate = $employee->salary / $currentMonthDays;
        $basicSalary = $summary['actual_hours'] * $hourlyRate;

        // Calculate period overtime (total overtime for the period)
        $periodOvertimeRate = $employee->additional_day_calculation ?? 1.0;
        $overtimeSalary = $summary['overtime_hours'] * $hourlyRate * $periodOvertimeRate;
        
        // Calculate late hours deduction
        // late_hour_calculation: how many hours each late hour is counted as (e.g., 0.5 means 1 late hour = 0.5 hours deduction)
        $lateHourCalculation = $employee->late_hour_calculation ?? 1.0;
        $lateHoursDeduction = $summary['late_hours'] * $lateHourCalculation;
        $lateDeductionAmount = $lateHoursDeduction * $hourlyRate;
        
        // Calculate deductions for unpaid leave and absent days
        // Unpaid leave: full day deduction for each unpaid leave day
        $unpaidLeaveDeduction = ($summary['unpaid_leave_days'] ?? 0) * $dailyRate;
        
        // Absent days: deduction based on late_day_calculation (e.g., if late_day_calculation = 2, 1 absent day = 2 days deduction)
        $lateDayCalculation = $employee->late_day_calculation ?? 1.0;
        $absentDaysDeduction = ($summary['absent_days'] ?? 0) * $lateDayCalculation * $dailyRate;
        
        $totalDeductions = $lateDeductionAmount + $unpaidLeaveDeduction + $absentDaysDeduction;

        return [
            'basic_salary' => round($basicSalary, 2),
            'overtime_salary' => round($overtimeSalary, 2),
            'late_hours_deduction' => round($lateDeductionAmount, 2),
            'unpaid_leave_deduction' => round($unpaidLeaveDeduction, 2),
            'absent_days_deduction' => round($absentDaysDeduction, 2),
            'total_salary' => round($basicSalary + $overtimeSalary - $totalDeductions, 2),
            'calculation_type' => 'hours_with_period_overtime',
            'hourly_rate' => $hourlyRate,
            'daily_rate' => $dailyRate,
        ];
    }

    /**
     * Calculate salary for attendance-only type
     */
    private function calculateAttendanceOnlySalary(Employee $employee, array $summary): array
    {
        $currentMonthDays = now()->daysInMonth;
        $dailyRate = $employee->salary / $currentMonthDays;
        $attendanceSalary = $summary['present_days'] * $dailyRate;
        
        // Calculate deductions for unpaid leave and absent days
        // Unpaid leave: full day deduction for each unpaid leave day
        $unpaidLeaveDeduction = ($summary['unpaid_leave_days'] ?? 0) * $dailyRate;
        
        // Absent days: deduction based on late_day_calculation (e.g., if late_day_calculation = 2, 1 absent day = 2 days deduction)
        $lateDayCalculation = $employee->late_day_calculation ?? 1.0;
        $absentDaysDeduction = ($summary['absent_days'] ?? 0) * $lateDayCalculation * $dailyRate;
        
        $totalDeductions = $unpaidLeaveDeduction + $absentDaysDeduction;

        return [
            'basic_salary' => round($attendanceSalary, 2),
            'overtime_salary' => 0,
            'unpaid_leave_deduction' => round($unpaidLeaveDeduction, 2),
            'absent_days_deduction' => round($absentDaysDeduction, 2),
            'total_salary' => round($attendanceSalary - $totalDeductions, 2),
            'calculation_type' => 'attendance_only',
            'daily_rate' => $dailyRate,
        ];
    }

    /**
     * Calculate salary for production-only type
     */
    private function calculateProductionOnlySalary(Employee $employee, array $summary): array
    {
        // الراتب لا يُحسب بناءً على الساعات، بل بناءً على الإنتاج
        // يتم حساب الراتب لاحقاً بناءً على معادلة خاصة بالإنتاج
        // هنا نعيد 0 لكل القيم المتعلقة بالراتب

        return [
            'basic_salary' => 0,
            'overtime_salary' => 0,
            'total_salary' => 0,
            'calculation_type' => 'production_only',
            'hourly_rate' => 0,
            'daily_rate' => 0,
        ];
    }

    /**
     * Get salary calculation history for employee
     */
    public function getSalaryHistory(Employee $employee, int $limit = 10): Collection
    {
        return AttendanceProcessing::where('employee_id', $employee->id)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }
}

