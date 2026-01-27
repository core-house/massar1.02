<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckModuleAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $module
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next, string $module): Response
    {
        // Skip for central domain
        if (!tenant()) {
            return $next($request);
        }

        if (!tenant()->hasModule($module)) {
            abort(403, __('This module is not enabled for your account.'));
        }

        return $next($request);
    }
}
