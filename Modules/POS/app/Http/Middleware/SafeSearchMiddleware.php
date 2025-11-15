<?php

namespace Modules\POS\app\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SafeSearchMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // تنظيف معاملات البحث من الأحرف الخطيرة
        if ($request->has('searchTerm')) {
            $searchTerm = $request->input('searchTerm');
            $cleanSearchTerm = preg_replace('/[^\p{L}\p{N}\s]/u', '', $searchTerm);
            $request->merge(['searchTerm' => $cleanSearchTerm]);
        }

        if ($request->has('barcodeTerm')) {
            $barcodeTerm = $request->input('barcodeTerm');
            $cleanBarcodeTerm = preg_replace('/[^\p{L}\p{N}\s]/u', '', $barcodeTerm);
            $request->merge(['barcodeTerm' => $cleanBarcodeTerm]);
        }

        return $next($request);
    }
}
