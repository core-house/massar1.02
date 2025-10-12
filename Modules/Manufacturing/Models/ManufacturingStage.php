<?php

namespace Modules\Manufacturing\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Modules\Branches\Models\Branch;

class ManufacturingStage extends Model
{
    protected $fillable = [
        'name',
        'description',
        'branch_id',
        'is_active',
        'order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * العلاقة مع الفرع
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * العلاقة مع أوامر التصنيع
     */
    public function orders(): BelongsToMany
    {
        return $this->belongsToMany(
            ManufacturingOrder::class,
            'manufacturing_order_stage',
            'manufacturing_stage_id',
            'manufacturing_order_id'
        )
            ->withPivot([
                'order',
                'cost',
                'estimated_duration',
                'actual_duration',
                'status',
                'notes',
                'is_active',
                'started_at',
                'completed_at',
                'assigned_to'
            ])
            ->withTimestamps();
    }

    /**
     * Scope للمراحل النشطة
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope للترتيب
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order', 'asc');
    }
}
