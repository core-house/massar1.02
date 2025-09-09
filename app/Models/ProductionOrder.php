<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Item;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductionOrder extends Model
{
    use HasFactory;
    
    protected $table = 'production_orders';
    protected $guarded = ['id'];
    
    protected $casts = [
        'order_date' => 'date',
        'total_amount' => 'decimal:2',
    ];

    public function items(): BelongsToMany
    {
        return $this->belongsToMany(Item::class, 'production_order_items', 'production_order_id', 'item_id')
        ->withPivot('quantity', 'note')->withTimestamps();
    }
    public function customer(): BelongsTo
    {
        return $this->belongsTo(AccHead::class, 'customer_id');
    }
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
    public function productionInvoice(): HasOne
    {
        return $this->hasOne(OperHead::class, 'production_order_id');
    }
}
