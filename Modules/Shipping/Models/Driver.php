<?php

declare(strict_types=1);

namespace Modules\Shipping\Models;

use Modules\Branches\Models\Branch;
use Illuminate\Database\Eloquent\Model;

class Driver extends Model
{
    protected $fillable = [
        'name',
        'phone',
        'vehicle_type',
        'license_number',
        'vehicle_plate',
        'is_available',
        'rating',
        'total_ratings',
        'completed_deliveries',
        'failed_deliveries',
        'notes',
        'branch_id',
    ];

    protected $casts = [
        'is_available' => 'boolean',
        'rating' => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope(new \App\Models\Scopes\BranchScope);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function ratings()
    {
        return $this->hasMany(DriverRating::class);
    }

    public function updateRating(): void
    {
        $avgRating = $this->ratings()->avg('rating');
        $totalRatings = $this->ratings()->count();

        $this->rating = $avgRating ? round((float)$avgRating, 2) : 0;
        $this->total_ratings = $totalRatings;
        $this->save();
    }

    public function getSuccessRateAttribute(): float
    {
        $total = $this->completed_deliveries + $this->failed_deliveries;
        if ($total === 0) return 0.0;
        return round(($this->completed_deliveries / $total) * 100, 2);
    }
}
