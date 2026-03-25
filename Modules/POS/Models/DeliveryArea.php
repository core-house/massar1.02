<?php

namespace Modules\POS\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\POS\Database\Factories\DeliveryAreaFactory;

class DeliveryArea extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $guarded = ['id'];

    // protected static function newFactory(): DeliveryAreaFactory
    // {
    //     // return DeliveryAreaFactory::new();
    // }
}
