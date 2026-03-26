<?php

namespace App\Http\Middleware;

use Closure;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;

class CustomInitializeTenancyByDomain
{
    /**
     * Handle an incoming request.
     */
    public function handle($request, Closure $next)
    {
        if ($this->isCentralDomain($request)) {
            // في حالة الدومين المركزي، لا تفعل شيئاً واترك الـ Default (Central DB) يعمل
            return $next($request);
        }

        // في حالة الـ Tenant، قم ببدء الـ Tenancy
        return app(InitializeTenancyByDomain::class)->handle($request, $next);
    }

    protected function isCentralDomain($request): bool
    {
        $host = $request->getHost();
        $centralDomains = config('tenancy.central_domains', []);
        $hostWithoutPort = explode(':', $host)[0];

        return in_array($host, $centralDomains)
            || in_array($hostWithoutPort, $centralDomains);
    }
}
