<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CostCenter extends Model
{
    protected $guarded = [];
    public function operHead()
    {
        return $this->belongsTo(OperHead::class, 'cost_center');
    }
}
