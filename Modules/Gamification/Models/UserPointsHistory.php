<?php

declare(strict_types=1);

namespace Modules\Gamification\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class UserPointsHistory extends Model
{
    protected $table = 'user_points_history';

    protected $fillable = [
        'user_id',
        'points',
        'event_type',
        'description',
        'source_id',
        'source_type',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function source(): MorphTo
    {
        return $this->morphTo();
    }
}
