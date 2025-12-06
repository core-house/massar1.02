<?php

declare(strict_types=1);

namespace Modules\Recruitment\Models;

use Modules\Branches\Models\Branch;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Scopes\BranchScope;
use App\Models\User;
use App\Models\Employee;
use App\Models\EmployeesJob;
use App\Models\ContractType;

class Contract extends Model
{
    protected $guarded = ['id'];

    protected static function booted(): void
    {
        static::addGlobalScope(new BranchScope);
    }

    public function created_by(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function contract_type(): BelongsTo
    {
        return $this->belongsTo(ContractType::class, 'contract_type_id');
    }

    public function job(): BelongsTo
    {
        return $this->belongsTo(EmployeesJob::class, 'job_id');
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function contract_points(): HasMany
    {
        return $this->hasMany(ContractPoint::class);
    }

    public function salary_points(): HasMany
    {
        return $this->hasMany(SalaryPoint::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function cv(): BelongsTo
    {
        return $this->belongsTo(Cv::class);
    }

    public function interview(): BelongsTo
    {
        return $this->belongsTo(Interview::class, 'interview_id');
    }

    public function termination(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Termination::class);
    }
}

