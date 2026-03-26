<?php

declare(strict_types=1);

namespace Modules\Recruitment\Models;

use Modules\Branches\Models\Branch;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Scopes\BranchScope;
use App\Models\User;

class Interview extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'scheduled_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope(new BranchScope);
    }

    public function cv(): BelongsTo
    {
        return $this->belongsTo(Cv::class);
    }

    public function jobPosting(): BelongsTo
    {
        return $this->belongsTo(JobPosting::class);
    }

    public function interviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'interviewer_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(InterviewSchedule::class);
    }

    public function contract(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Contract::class);
    }

    public function isScheduled(): bool
    {
        return $this->status === 'scheduled';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }
}

