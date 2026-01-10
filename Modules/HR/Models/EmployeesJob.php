<?php

namespace Modules\HR\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeesJob extends Model
{
    protected $table = 'employees_jobs';
    protected $guarded = ['id'];

    public function employees()
    {
        return $this->hasMany(Employee::class);
    }
}
