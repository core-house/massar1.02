<?php

namespace Modules\Installments\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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

    public function plan()
    {
        return $this->belongsTo(InstallmentPlan::class, 'installment_plan_id');
    }
}
