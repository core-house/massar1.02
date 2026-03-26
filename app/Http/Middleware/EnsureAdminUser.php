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
     */
    private function isAdminUser($user): bool
    {
        // Admin user هو المستخدم الذي email = admin@admin.com
        return $user && $user->email === 'admin@admin.com';
    }
}
