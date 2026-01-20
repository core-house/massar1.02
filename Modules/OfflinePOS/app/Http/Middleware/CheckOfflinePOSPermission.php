<?php

namespace Modules\OfflinePOS\app\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware للتحقق من صلاحيات Offline POS
 * يعمل مع نظام الصلاحيات per-tenant
 */
class CheckOfflinePOSPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $permission = null): Response
    {
        // التحقق من تسجيل الدخول
        if (!auth()->check()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated.',
                ], 401);
            }
            
            return redirect()->route('login');
        }

        // إذا تم تحديد صلاحية معينة، التحقق منها
        if ($permission && !auth()->user()->can($permission)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You do not have permission to perform this action.',
                    'required_permission' => $permission,
                ], 403);
            }
            
            abort(403, 'You do not have permission to access Offline POS.');
        }

        // التحقق من الصلاحية الأساسية لاستخدام النظام
        if (!$permission && !auth()->user()->can('view offline pos system')) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You do not have access to Offline POS system.',
                ], 403);
            }
            
            abort(403, 'You do not have access to Offline POS system.');
        }

        return $next($request);
    }
}
