<?php

declare(strict_types=1);

namespace Modules\Gamification\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserGamification extends Model
{
    protected $table = 'user_gamification';

    protected $fillable = [
        'user_id',
        'points',
        'level',
        'streak',
        'last_activity_at',
    ];

    protected $casts = [
        'last_activity_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
