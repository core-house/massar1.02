<?php

namespace Modules\HR\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model 
{

    protected $table = 'countries';
    protected $guarded = ['id'];

    public function states()
    {
        return $this->hasMany(State::class);
    }

}