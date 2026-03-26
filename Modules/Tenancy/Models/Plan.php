<?php

declare(strict_types=1);

namespace Modules\Tenancy\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Plan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'amount',
        'duration_days',
        'max_users',
        'max_branches',
        'status',
        'features',
        'created_by',
    ];

    protected $casts = [
        'features' => 'array',
        'status' => 'boolean',
    ];
}
