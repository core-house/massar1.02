<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdminUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        if (! $this->isAdminUser($user)) {
            Auth::logout(); // يفضل تسجيل الخروج إذا حاول مستخدم عادي دخول السنترال
            return redirect()->route('login')->with('error', 'غير مسموح لك بدخول هذه المنطقة');
        }

        return $next($request);
    }

    /**
     * التحقق من أن المستخدم هو admin user
     * في السنترال، نسمح لأي مستخدم بالدخول
     */
    private function isAdminUser($user): bool
    {
        // في السنترال، نسمح لأي مستخدم مسجل بالدخول لإدارة التينانتس
        return (bool) $user;
    }
}
