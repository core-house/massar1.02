<?php

namespace Modules\OfflinePOS\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware للتأكد من وجود سياق الفرع (Branch Context)
 * يعمل مع stancl/tenancy للـ multi-tenancy
 */
class EnsureBranchContext
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // التحقق من أن الـ tenant معرف (من stancl/tenancy) - only if tenancy is installed
        if (function_exists('tenant') && !tenant()) {
            return response()->json([
                'success' => false,
                'message' => 'Tenant not identified. Please access via proper subdomain.',
            ], 403);
        }

        // جلب branch_id من session أو header
        $branchId = $request->header('X-Branch-ID') 
                    ?? $request->input('branch_id')
                    ?? session('current_branch_id')
                    ?? Auth::user()?->branch_id;

        // إذا لم يوجد branch_id، استخدم الفرع الافتراضي
        if (!$branchId) {
            // يمكن جلب الفرع الافتراضي من إعدادات الـ tenant
            $branchId = $this->getDefaultBranch();
        }

        // حفظ branch_id في request للاستخدام لاحقاً
        $request->merge(['current_branch_id' => $branchId]);
        
        // حفظ في session للطلبات القادمة
        if ($branchId) {
            session(['current_branch_id' => $branchId]);
        }

        return $next($request);
    }

    /**
     * جلب الفرع الافتراضي للـ tenant
     */
    protected function getDefaultBranch()
    {
        // يمكن تخصيص هذا حسب بنية قاعدة البيانات
        // مثلاً: جلب أول فرع أو الفرع الرئيسي
        
        // مثال بسيط:
        // return \App\Models\Branch::where('is_main', true)->first()?->id;
        
        return null; // أو قيمة افتراضية
    }
}
