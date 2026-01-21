<?php

// namespace App\Helpers;

use Illuminate\Support\Facades\Auth;
use App\Models\User;


if (! function_exists('userBranches')) {
    function userBranches()
    {
        if (! app()->bound(\Stancl\Tenancy\Tenancy::class) || ! tenancy()->initialized) {
            return collect();
        }

        if (Auth::check()) {
            /** @var User $user */
            $user = Auth::user();

            if ($user->email === 'admin@admin.com' || ! \Illuminate\Support\Facades\Schema::hasTable('branches')) {
                return collect();
            }

            return $user->branches()
                ->where('branches.is_active', 1)
                ->select('branches.id', 'branches.name')
                ->get();
        }
        return collect();
    }
}
