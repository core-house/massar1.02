<?php

declare(strict_types=1);

namespace Modules\CRM\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Branches\Models\Branch;

class LeadStatus extends Model
{
    protected $table = 'lead_statuses';

    protected $fillable = [
        'name',
        'color',
        'order_column',
        'branch_id',
    ];

    protected static function booted()
    {
        static::addGlobalScope(new \App\Models\Scopes\BranchScope);
    }

    public static function ordered(): \Illuminate\Database\Eloquent\Collection
    {
        return self::orderBy('order_column')->get();
    }

    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class, 'status_id');
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }
}
