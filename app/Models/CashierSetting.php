<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Branches\Models\Branch;

class CashierSetting extends Model
{
    protected $table = 'cashier_settings';

    protected $fillable = [
        'def_pos_client',
        'def_pos_store',
        'def_pos_employee',
        'def_pos_fund',
        'enable_scale_items',
        'scale_code_prefix',
        'scale_code_digits',
        'scale_quantity_digits',
        'scale_quantity_divisor',
        'branch_id',
    ];

    protected $casts = [
        'enable_scale_items' => 'boolean',
        'scale_code_digits' => 'integer',
        'scale_quantity_digits' => 'integer',
        'scale_quantity_divisor' => 'integer',
        'def_pos_client' => 'integer',
        'def_pos_store' => 'integer',
        'def_pos_employee' => 'integer',
        'def_pos_fund' => 'integer',
        'branch_id' => 'integer',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
