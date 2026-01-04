<?php

namespace Modules\HR\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeProduction extends Model
{
    protected $guarded = ['id'];
    protected $table = 'employee_productions';
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
    public function attendanceProcessing()
    {
        return $this->belongsTo(AttendanceProcessing::class);
    }
}
