<?php

namespace Modules\Progress\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Progress\Models\ProjectItem;
use Modules\Progress\Models\DailyProgress;
use Modules\Progress\Models\ProjectProgress;

class DailyProgressController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware('can:dailyprogress-list')->only('index');
    //     // $this->middleware('can:dailyprogress-create')->only(['create', 'store']);
    //     $this->middleware('can:dailyprogress-edit')->only(['edit', 'update']);
    //     $this->middleware('can:dailyprogress-delete')->only('destroy');
    //     // $this->middleware('can:employees-permissions')->only('assignPermissions');
    // }
    /**
     * عرض قائمة التقدم اليومي مع الفلترة
     */
    /**
     * عرض قائمة التقدم اليومي مع الفلترة
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = DailyProgress::with(['project', 'projectItem.workItem', 'employee', 'user']);

        // لو الموظف مش أدمن -> يقيد العرض على سجلاته هو فقط (user_id OR employee_id)
        if (!$user->hasRole('admin') && !$user->hasRole('super-admin')) {
             $query->where(function($q) use ($user) {
                 $q->where('user_id', $user->id);
                 if ($user->employee) {
                     $q->orWhere('employee_id', $user->employee->id);
                 }
             });
        }
        
        // Projects List for Filter
        if ($user->employee) {
            $projects = $user->employee->projects()->select('projects.id', 'projects.name')->get();
        } else {
             // If no employee, show all projects (or restrict logic if needed later)
             $projects = ProjectProgress::select('id', 'name')->get();
        }

        // فلترة التاريخ
        if ($request->filled('progress_date')) {
            $query->whereDate('progress_date', $request->progress_date);
        } elseif (!$request->boolean('view_all')) {
            $query->whereDate('progress_date', today());
        }

        // فلترة المشروع
        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        // جلب البيانات بدون Pagination
        $dailyProgress = $query->orderBy('progress_date', 'desc')->get();

        // التجميع (Grouping)
        // المستوى الأول: المشروع
        $groupedProgress = $dailyProgress->groupBy('project_id')->map(function ($projectGroup) {
            // المستوى الثاني: Subproject
            return $projectGroup->groupBy(function ($item) {
                return $item->projectItem->subproject_name ?? 'عام';
            });
        });

        return view('progress::daily-progress.index', compact('groupedProgress', 'projects'));
    }



    /**
     * فورم إنشاء تقرير يومي جديد
     */
    public function create()
    {
        $user = Auth::user();
        
        // Load projects: If employee linked, load assigned. Else load all (or handled by permission scope)
        if ($user && $user->employee) {
            $projects = $user->employee->projects()->select('projects.id', 'projects.name')->get();
        } else {
            $projects = ProjectProgress::select('id', 'name')->get();
        }

        return view('progress::daily-progress.create', compact('projects'));
    }

    /**
     * حفظ تقرير يومي جديد
     */
    public function store(Request $request)
    {
        // 1. Filter out empty quantities
        $quantities = array_filter($request->input('quantities', []), function($value) {
            return !is_null($value) && $value !== '';
        });

        // 2. Merge back for validation
        $request->merge(['quantities' => $quantities]);

        // 3. Manual check for empty array to give clear error
        if (empty($quantities)) {
            return back()->withErrors(['quantities' => __('general.error_no_quantities_entered')])->withInput();
        }

        $validated = $request->validate([
            'project_id'   => 'required|exists:projects,id',
            'progress_date' => 'required|date',
            'quantities'   => 'required|array',
            'quantities.*' => 'numeric|min:0',
            'notes'        => 'nullable|string'
        ]);

        $employee = Employee::where('user_id', Auth::id())->first();
        // REMOVED STRICT CHECK: if (!$employee) return error...

        foreach ($validated['quantities'] as $itemId => $qty) {
            $item = ProjectItem::find($itemId);
            if (!$item) continue;

            $newCompleted = $item->completed_quantity + $qty;
            $completionPercentage = $item->total_quantity > 0
                ? min(100, round(($newCompleted / $item->total_quantity) * 100, 2))
                : 0;

            DailyProgress::create([
                'project_id'            => $validated['project_id'],
                'project_item_id'       => $itemId,
                'progress_date'         => $validated['progress_date'],
                'quantity'              => $qty,
                'notes'                 => $validated['notes'] ?? null,
                'employee_id'           => $employee ? $employee->id : null,
                'user_id'               => Auth::id(),
                'branch_id'             => Auth::user()->branch_id ?? 1, // Default to 1 if no branch
                'completion_percentage' => $completionPercentage
            ]);

            $item->update([
                'completed_quantity'    => $newCompleted,
                'completion_percentage' => $completionPercentage
            ]);
        }

        return redirect()->route('daily_progress.index', ['progress_date' => $validated['progress_date']])
            ->with('success', 'تم تسجيل التقدم اليومي بنجاح');
    }

    /**
     * فورم تعديل تقرير
     */
    public function edit(DailyProgress $dailyProgress)
    {
        // كل المشاريع
        $projects = ProjectProgress::select('id', 'name')->get();

        // كل الموظفين
        $employees = Employee::select('id', 'name')->get();

        // كل البنود (مربوطة بالمشاريع)
        $projectItems = $dailyProgress->project
            ? $dailyProgress->project->items()->with('workItem')->get()
            : collect();

        return view('progress::daily-progress.edit', compact('dailyProgress', 'projects', 'employees', 'projectItems'));
    }

    /**
     * تحديث تقرير يومي
     */
    public function update(Request $request, DailyProgress $dailyProgress)
    {
        $validated = $request->validate([
            'quantity'      => 'required|numeric|min:0',
            'progress_date' => 'required|date',
            'notes'         => 'nullable|string'
        ]);

        $item = $dailyProgress->projectItem;
        $diff = $request->quantity - $dailyProgress->quantity;

        $newCompleted = $item->completed_quantity + $diff;
        $completionPercentage = $item->total_quantity > 0
            ? min(100, round(($newCompleted / $item->total_quantity) * 100, 2))
            : 0;

        $dailyProgress->update(array_merge($validated, [
            'completion_percentage' => $completionPercentage
        ]));

        $item->update([
            'completed_quantity'    => $newCompleted,
            'completion_percentage' => $completionPercentage
        ]);

        return redirect()->route('daily_progress.index')
            ->with('success', 'تم تحديث التقدم اليومي بنجاح');
    }

    /**
     * حذف تقرير يومي
     */
    public function destroy(DailyProgress $dailyProgress)
    {
        $item = $dailyProgress->projectItem;

        $item->decrement('completed_quantity', $dailyProgress->quantity);

        $completionPercentage = $item->total_quantity > 0
            ? round(($item->completed_quantity / $item->total_quantity) * 100, 2)
            : 0;

        $item->update(['completion_percentage' => $completionPercentage]);

        $dailyProgress->delete();

        return redirect()->route('daily_progress.index')
            ->with('success', 'تم حذف التسجيل اليومي بنجاح');
    }
}
