<?php

namespace Modules\POS\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\POS\Database\Factories\RestaurantTableFactory;

class RestaurantTable extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $guarded = ['id'];

    // protected static function newFactory(): RestaurantTableFactory
    // {
    //     // return RestaurantTableFactory::new();
    // }
}
