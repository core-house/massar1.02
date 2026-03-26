<?php

namespace Modules\HR\Models;

use Illuminate\Database\Eloquent\Model;

class SalaryPoint extends Model
{
    protected $table = 'salary_points';
    protected $guarded = ['id'];

    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }
}
