<?php

namespace Modules\HR\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Employee_Evaluation extends Model
{
    protected $table = 'employee_evaluations';
    
    protected $fillable = [
        'employee_id',
        'evaluation_date',
        'direct_manager',
        'evaluation_period_from',
        'evaluation_period_to',
        'total_score',
        'final_rating',
    ];

    protected $casts = [
        'evaluation_date' => 'date',
        'evaluation_period_from' => 'date',
        'evaluation_period_to' => 'date',
        'total_score' => 'float',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function kpis(): BelongsToMany
    {
        return $this->belongsToMany(Kpi::class, 'employeeEvaluation_kpis', 'employee_evaluation_id', 'kpi_id')
            ->withPivot('score', 'notes')
            ->withTimestamps();
    }
}
