<?php

namespace Modules\Inquiries\Models;

use Illuminate\Database\Eloquent\Model;

class QuotationUnit extends Model
{
    protected $fillable = ['name', 'quotation_type_id'];

    public function type()
    {
        return $this->belongsTo(QuotationType::class, 'quotation_type_id');
    }
}
