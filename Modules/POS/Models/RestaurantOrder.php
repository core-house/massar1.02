<?php

namespace Modules\POS\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\POS\Database\Factories\RestaurantOrderFactory;

class RestaurantOrder extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];

    // protected static function newFactory(): RestaurantOrderFactory
    // {
    //     // return RestaurantOrderFactory::new();
    // }
}
