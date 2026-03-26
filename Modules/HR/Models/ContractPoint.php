<?php

namespace Modules\HR\Models;

use Illuminate\Database\Eloquent\Model;

class ContractPoint extends Model
{
    protected $guarded = ['id'];

    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }
}
