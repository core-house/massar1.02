<?php

namespace Modules\Tenancy\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdminUser
{
    public function handle(Request $request, Closure $next): Response
    {
        // تأكد إن المستخدم مسجل دخول
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // تأكد إن المستخدم admin
        if (!$user->isAdmin()) {
            abort(403, 'Unauthorized - Admin access only');
        }

        // تأكد إننا على central domain
        $host = $request->getHost();
        $centralDomains = config('tenancy.central_domains', []);
        $hostWithoutPort = explode(':', $host)[0];

        if (!in_array($host, $centralDomains) && !in_array($hostWithoutPort, $centralDomains)) {
            abort(403, 'Admin panel accessible only from central domain');
        }

        // تأكد من استخدام central database
        config(['database.default' => 'central']);

        return $next($request);
    }
}
