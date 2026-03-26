<?php

namespace Modules\CRM\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\CRM\Models\Task;
use Modules\CRM\Enums\TaskStatusEnum;
use Modules\CRM\Enums\TaskPriorityEnum;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class TaskStatisticsController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view Tasks');
    }

    public function index(Request $request)
    {
        $dateFilter = $request->get('date_filter', 'month');
        
        // تحديد نطاق التاريخ
        $startDate = match($dateFilter) {
            'week' => Carbon::now()->startOfWeek(),
            'month' => Carbon::now()->startOfMonth(),
            'year' => Carbon::now()->startOfYear(),
            default => Carbon::now()->startOfMonth(),
        };
        
        $endDate = Carbon::now()->endOfDay();

        // إحصائيات عامة
        $stats = [
            'total' => Task::count(),
            'pending' => Task::where('status', TaskStatusEnum::PENDING)->count(),
            'in_progress' => Task::where('status', TaskStatusEnum::IN_PROGRESS)->count(),
            'completed' => Task::where('status', TaskStatusEnum::COMPLETED)->count(),
            'cancelled' => Task::where('status', TaskStatusEnum::CANCELLED)->count(),
            'overdue' => Task::where('status', '!=', TaskStatusEnum::COMPLETED)
                ->where('due_date', '<', Carbon::now())
                ->count(),
        ];

        // إحصائيات حسب الأولوية
        $priorityStats = [
            'high' => Task::where('priority', TaskPriorityEnum::HIGH)->count(),
            'medium' => Task::where('priority', TaskPriorityEnum::MEDIUM)->count(),
            'low' => Task::where('priority', TaskPriorityEnum::LOW)->count(),
        ];

        // المهام المتأخرة
        $overdueTasks = Task::with(['client', 'targetUser', 'taskType'])
            ->where('status', '!=', TaskStatusEnum::COMPLETED)
            ->where('due_date', '<', Carbon::now())
            ->orderBy('due_date', 'asc')
            ->limit(10)
            ->get();

        // المهام حسب النوع
        $tasksByType = Task::select('task_type_id', DB::raw('count(*) as count'))
            ->with('taskType')
            ->groupBy('task_type_id')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get();

        // المهام حسب المستخدم المسؤول
        $tasksByUser = Task::select('target_user_id', DB::raw('count(*) as count'))
            ->with('targetUser')
            ->whereNotNull('target_user_id')
            ->groupBy('target_user_id')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get();

        // الاتجاه الشهري (آخر 6 أشهر)
        $monthlyTrend = Task::select(
                DB::raw('YEAR(created_at) as year'),
                DB::raw('MONTH(created_at) as month'),
                DB::raw('count(*) as count')
            )
            ->where('created_at', '>=', Carbon::now()->subMonths(6))
            ->groupBy('year', 'month')
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->get();

        // المهام الأخيرة
        $recentTasks = Task::with(['client', 'targetUser', 'taskType', 'user'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // معدل الإنجاز
        $completionRate = $stats['total'] > 0 
            ? round(($stats['completed'] / $stats['total']) * 100, 2) 
            : 0;

        return view('crm::tasks.statistics', compact(
            'stats',
            'priorityStats',
            'overdueTasks',
            'tasksByType',
            'tasksByUser',
            'monthlyTrend',
            'recentTasks',
            'dateFilter',
            'completionRate'
        ));
    }
}
