<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnomalyEvent extends Model
{
    protected $table = 'anomaly_events';

    protected $guarded = ['id'];

    protected $casts = [
        'meta' => 'array',
        'detected_at' => 'datetime',
        'resolved_at' => 'datetime',
    ];

    public function subject()
    {
        return $this->morphTo();
    }
}

