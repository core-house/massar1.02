<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Price extends Model
{
    use HasFactory;

    protected $table = 'prices';

    protected $guarded = ['id'];
    
    public function items(): BelongsToMany
    {
        return $this->belongsToMany(Item::class, 'item_prices', 'price_id', 'item_id')
            ->withPivot('unit_id','price','discount','tax_rate')
            ->withTimestamps();
    }

    public function units(): BelongsToMany
    {
        return $this->belongsToMany(Unit::class, 'item_prices', 'price_id', 'unit_id')
            ->withPivot('price','discount','tax_rate')
            ->withTimestamps();
    }

}
