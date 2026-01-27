<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckTenantStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Skip for central domain
        if ($this->isCentralDomain($request)) {
            return $next($request);
        }

        // Check if tenant is initialized
        if (!tenant()) {
            return $next($request);
        }

        // Skip for the inactive page itself and logout route to avoid infinite loops
        $allowedRoutes = ['tenant.inactive', 'logout'];
        if ($request->routeIs(...$allowedRoutes)) {
            return $next($request);
        }

        // Check tenant status (Real-time check)
        $tenant = tenant();
        if (!$tenant || !$tenant->isActive() || $tenant->status == false) {
            return redirect()->route('tenant.inactive');
        }

        return $next($request);
    }

    protected function isCentralDomain(Request $request): bool
    {
        $host = $request->getHost();
        $centralDomains = config('tenancy.central_domains', []);
        $hostWithoutPort = explode(':', $host)[0];

        return in_array($host, $centralDomains)
            || in_array($hostWithoutPort, $centralDomains);
    }
}
