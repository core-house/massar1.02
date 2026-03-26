<?php

namespace Modules\Inquiries\Middleware;

use Closure;
use Illuminate\Http\Request;
use Modules\Inquiries\Models\Inquiry;

class EngineerAccessMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $user = \Illuminate\Support\Facades\Auth::user();

        // جيب الـ inquiry ID من الـ route
        $inquiryId = $request->route('inquiry') ?? $request->route('id');

        if ($inquiryId) {
            $inquiry = Inquiry::with(['assignedEngineers', 'creator'])->find($inquiryId);

            if ($inquiry) {
                // السماح لصاحب الاستفسار أو من لديه صلاحية إجبارية أو من هو مهندس معيَّن
                $isAssignedEngineer = $inquiry->assignedEngineers->contains('id', $user->id);
                $isCreator = $inquiry->creator && $inquiry->creator->id === $user->id;
                // استخدام Gate بدلاً من استدعاء can مباشرةً على الموديل لتفادي تحذيرات الإنترفينس
                $canForceEdit = \Illuminate\Support\Facades\Gate::forUser($user)->allows('force_edit_inquiries');

                if (!($isAssignedEngineer || $isCreator || $canForceEdit)) {
                    abort(403, 'ليس لديك صلاحية للوصول لهذا الاستفسار.');
                }
            }
        }

        return $next($request);
    }
}




// <?php

// namespace Modules\Inquiries\Middleware;

// use Closure;
// use Illuminate\Http\Request;
// use Modules\Inquiries\Models\Inquiry;

// class EngineerAccessMiddleware
// {
//     public function handle(Request $request, Closure $next)
//     {
//         $user = auth()->user();

//         // جيب الـ inquiry ID من الـ route
//         $inquiryId = $request->route('inquiry') ?? $request->route('id');

//         if ($inquiryId) {
//             $inquiry = Inquiry::with('assignedEngineers')->find($inquiryId);

//             if ($inquiry) {
//                 // تأكد إن اليوزر ده مكلف بالاستفسار
//                 // لو مش مكلف، ارفض الوصول
//                 $isAssigned = $inquiry->assignedEngineers->contains('id', $user->id);

//                 if (!$isAssigned) {
//                     abort(403, 'ليس لديك صلاحية للوصول لهذا الاستفسار. أنت غير مكلف به.');
//                 }
//             }
//         }

//         return $next($request);
//     }
// }
