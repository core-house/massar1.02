<?php

namespace Modules\HR\Models;

use Illuminate\Database\Eloquent\Model;

class Town extends Model
{
    protected $guarded = ['id'];

    public function city()
    {
        return $this->belongsTo(City::class);
    }
}
