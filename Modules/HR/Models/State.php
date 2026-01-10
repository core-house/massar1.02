<?php

namespace Modules\HR\Models;

use Illuminate\Database\Eloquent\Model;

class State extends Model 
{

    protected $table = 'states';
    protected $guarded = ['id'];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function cities()
    {
        return $this->hasMany(City::class);
    }

}