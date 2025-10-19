<?php

namespace Modules\Manufacturing\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Modules\Branches\Models\Branch;

class ManufacturingOrder extends Model
{
    protected $fillable = [
        'order_number',
        'template_name',
        'branch_id',
        'status',
        'description',
        'item_id',
        'total_cost',
        'estimated_duration',
        'is_template',
    ];

    protected $casts = [
        'is_template' => 'boolean',
        'total_cost' => 'decimal:2',
        'estimated_duration' => 'decimal:2',
    ];

    /**
     * العلاقة مع الفرع
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * العلاقة مع المراحل
     */
    public function stages(): BelongsToMany
    {
        return $this->belongsToMany(
            ManufacturingStage::class,
            'manufacturing_order_stage', // اسم الجدول الوسيط
            'manufacturing_order_id',
            'manufacturing_stage_id'
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
            ->withTimestamps()
            ->orderBy('manufacturing_order_stage.order', 'asc'); // ⬅️ مهم جداً: اسم الجدول الصحيح
    }

    /**
     * حساب الإجماليات
     */
    public function calculateTotals(): void
    {
        $this->total_cost = $this->stages()->sum('manufacturing_order_stage.cost');
        $this->estimated_duration = $this->stages()->sum('manufacturing_order_stage.estimated_duration');
        $this->save();
    }

    /**
     * توليد رقم أمر تلقائي
     */
    public static function generateOrderNumber(): string
    {
        $year = date('Y');
        $lastOrder = self::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();

        $nextNumber = $lastOrder ? (int) substr($lastOrder->order_number, -4) + 1 : 1;

        return 'MO-' . $year . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Scope للقوالب فقط
     */
    public function scopeTemplates($query)
    {
        return $query->where('is_template', true);
    }

    /**
     * Scope للأوامر فقط (مش قوالب)
     */
    public function scopeOrders($query)
    {
        return $query->where('is_template', false);
    }
}
