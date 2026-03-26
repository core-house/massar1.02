<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Logout;
use App\Models\LoginSession;

class LogUserLogout
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(Logout $event): void
    {
        LoginSession::where('session_id', session()->getId())
            ->update([
                'logout_at' => now()
            ]);
    }
}
