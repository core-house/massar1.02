<?php

namespace Modules\HR\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ContractType extends Model
{
    protected $guarded = ['id'];

    public function contracts(): HasMany
    {
        return $this->hasMany(Contract::class);
    }
}
