<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Symfony\Component\HttpFoundation\Response;

class CustomInitializeTenancyByDomain
{
    /**
     * Handle an incoming request.
     */
    public function handle($request, Closure $next)
    {
        if ($this->isCentralDomain($request)) {
            tenancy()->end(); // 🔥 مهم جدًا
            return $next($request);
        }

        return app(InitializeTenancyByDomain::class)
            ->handle($request, $next);
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
