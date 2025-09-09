<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoginSession extends Model
{
    protected $fillable = [
        'user_id',
        'ip_address',
        'user_agent',
        'device',
        'login_at',
        'logout_at',
        'session_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
