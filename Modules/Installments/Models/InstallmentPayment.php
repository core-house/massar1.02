<?php

namespace Modules\Installments\Models;

use Illuminate\Database\Eloquent\Model;

class InstallmentPayment extends Model
{
    protected $fillable = [
        'installment_plan_id',
        'installment_number',
        'amount_due',
        'amount_paid',
        'due_date',
        'payment_date',
        'status',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'due_date' => 'datetime',
        'payment_date' => 'datetime',
    ];

    public function plan()
    {
        return $this->belongsTo(InstallmentPlan::class, 'installment_plan_id');
    }
}
