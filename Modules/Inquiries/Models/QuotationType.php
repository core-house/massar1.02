<?php

namespace Modules\Inquiries\Models;

use Illuminate\Database\Eloquent\Model;

class QuotationType extends Model
{
    protected $fillable = ['name'];

    public function units()
    {
        return $this->hasMany(QuotationUnit::class, 'quotation_type_id');
    }
}
