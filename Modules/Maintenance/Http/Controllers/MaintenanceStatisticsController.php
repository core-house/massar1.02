<?php

namespace Modules\Maintenance\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Maintenance\Models\{Maintenance, ServiceType};

class MaintenanceStatisticsController extends Controller
{
    private function toIntStatus($status): int
    {
        if (is_object($status) && property_exists($status, 'value')) {
            return (int) $status->value;
        }
        if (is_object($status) && method_exists($status, 'value')) {
            return (int) $status->value();
        }
        if (is_string($status) && is_numeric($status)) {
            return (int) $status;
        }
        return (int) ($status ?? 0);
    }

    public function index()
    {
        $stats = $this->getDashboardStatistics();

        return view('maintenance::dashboard.index', compact('stats'));
    }

    /**
     * جلب جميع الإحصائيات بشكل محسّن
     */
    private function getDashboardStatistics()
    {
        return [
            'overview' => $this->getOverviewStats(),
            'status_breakdown' => $this->getStatusBreakdown(),
            'service_types' => $this->getServiceTypesStats(),
            'monthly_trend' => $this->getMonthlyTrend(),
            'recent_maintenances' => $this->getRecentMaintenances(),
            'performance' => $this->getPerformanceMetrics(),
        ];
    }

    /**
     * إحصائيات عامة سريعة
     */
    private function getOverviewStats()
    {
        $maintenanceQuery = Maintenance::query();
        $serviceTypeQuery = ServiceType::query();

        $total = $maintenanceQuery->count();

        return [
            'total_maintenances' => $total,
            'pending' => (clone $maintenanceQuery)->where('status', 0)->count(),
            'in_progress' => (clone $maintenanceQuery)->where('status', 1)->count(),
            'completed' => (clone $maintenanceQuery)->where('status', 2)->count(),
            'cancelled' => (clone $maintenanceQuery)->where('status', 3)->count(),
            'total_service_types' => $serviceTypeQuery->count(),
            'this_month' => (clone $maintenanceQuery)
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
            'this_week' => (clone $maintenanceQuery)->whereBetween('created_at', [
                now()->startOfWeek(),
                now()->endOfWeek()
            ])->count(),
        ];
    }

    /**
     * تفصيل الحالات مع النسب المئوية
     */
    private function getStatusBreakdown()
    {
        $statusCounts = Maintenance::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get()
            ->mapWithKeys(function ($item) {
                $status = $this->toIntStatus($item->status);
                return [$status => (int)$item->count];
            })
            ->toArray();

        $total = array_sum($statusCounts);

        $statuses = [
            0 => ['label' => 'قيد الانتظار', 'color' => 'warning', 'icon' => 'clock'],
            1 => ['label' => 'قيد التنفيذ', 'color' => 'info', 'icon' => 'tools'],
            2 => ['label' => 'مكتملة', 'color' => 'success', 'icon' => 'check-circle'],
            3 => ['label' => 'ملغاة', 'color' => 'danger', 'icon' => 'times-circle'],
        ];

        $breakdown = [];
        foreach ($statuses as $status => $info) {
            $count = $statusCounts[$status] ?? 0;
            $breakdown[$status] = [
                'label' => $info['label'],
                'color' => $info['color'],
                'icon' => $info['icon'],
                'count' => $count,
                'percentage' => $total > 0 ? round(($count / $total) * 100, 2) : 0,
            ];
        }

        return $breakdown;
    }

    /**
     * إحصائiات أنواع الصيانة
     */
    private function getServiceTypesStats()
    {
        $stats = ServiceType::select(
            'service_types.id',
            'service_types.name',
            'service_types.description',
            DB::raw('COALESCE(COUNT(maintenances.id), 0) as total_maintenances'),
            DB::raw('COALESCE(SUM(CASE WHEN maintenances.status = 0 THEN 1 ELSE 0 END), 0) as pending'),
            DB::raw('COALESCE(SUM(CASE WHEN maintenances.status = 1 THEN 1 ELSE 0 END), 0) as in_progress'),
            DB::raw('COALESCE(SUM(CASE WHEN maintenances.status = 2 THEN 1 ELSE 0 END), 0) as completed'),
            DB::raw('COALESCE(SUM(CASE WHEN maintenances.status = 3 THEN 1 ELSE 0 END), 0) as cancelled')
        )
            ->leftJoin('maintenances', 'service_types.id', '=', 'maintenances.service_type_id')
            ->groupBy('service_types.id', 'service_types.name', 'service_types.description')
            ->orderByDesc('total_maintenances')
            ->get();

        return $stats->map(function ($type) {
            $type->total_maintenances = (int)$type->total_maintenances;
            $type->pending = (int)$type->pending;
            $type->in_progress = (int)$type->in_progress;
            $type->completed = (int)$type->completed;
            $type->cancelled = (int)$type->cancelled;
            $type->completion_rate = $type->total_maintenances > 0
                ? round(($type->completed / $type->total_maintenances) * 100, 2)
                : 0;
            return $type;
        });
    }

    /**
     * الاتجاه الشهري لطلبات الصيانة
     */
    private function getMonthlyTrend()
    {
        $months = Maintenance::select(
            DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
            DB::raw('DATE_FORMAT(created_at, "%M %Y") as month_name'),
            DB::raw('COUNT(*) as total'),
            DB::raw('COALESCE(SUM(CASE WHEN status = 0 THEN 1 ELSE 0 END), 0) as pending'),
            DB::raw('COALESCE(SUM(CASE WHEN status = 1 THEN 1 ELSE 0 END), 0) as in_progress'),
            DB::raw('COALESCE(SUM(CASE WHEN status = 2 THEN 1 ELSE 0 END), 0) as completed'),
            DB::raw('COALESCE(SUM(CASE WHEN status = 3 THEN 1 ELSE 0 END), 0) as cancelled')
        )
            ->where('created_at', '>=', now()->subMonths(12))
            ->groupBy('month', 'month_name')
            ->orderBy('month', 'asc')
            ->get();

        return [
            'labels' => $months->pluck('month_name')->toArray(),
            'data' => [
                'total' => $months->pluck('total')->map(fn($v) => (int)$v)->toArray(),
                'pending' => $months->pluck('pending')->map(fn($v) => (int)$v)->toArray(),
                'in_progress' => $months->pluck('in_progress')->map(fn($v) => (int)$v)->toArray(),
                'completed' => $months->pluck('completed')->map(fn($v) => (int)$v)->toArray(),
                'cancelled' => $months->pluck('cancelled')->map(fn($v) => (int)$v)->toArray(),
            ],
        ];
    }

    /**
     * أحدث طلبات الصيانة
     */
    private function getRecentMaintenances()
    {
        return Maintenance::with('type')
            ->select('id', 'client_name', 'item_name', 'item_number', 'service_type_id', 'status', 'date', 'created_at')
            ->latest()
            ->limit(10)
            ->get()
            ->map(function ($maintenance) {
                $status = $this->toIntStatus($maintenance->status);
                return [
                    'id' => $maintenance->id,
                    'client_name' => $maintenance->client_name ?? 'غير محدد',
                    'item_name' => $maintenance->item_name ?? 'غير محدد',
                    'item_number' => $maintenance->item_number ?? 'غير محدد',
                    'service_type' => $maintenance->type->name ?? 'غير محدد',
                    'status' => $status,
                    'status_label' => $this->getStatusLabel($status),
                    'status_color' => $this->getStatusColor($status),
                    'date' => $maintenance->date ? $maintenance->date->format('Y-m-d') : null,
                    'created_at' => $maintenance->created_at->format('Y-m-d H:i'),
                ];
            });
    }

    /**
     * مقاييس الأداء
     */
    private function getPerformanceMetrics()
    {
        $today = now();
        $lastMonth = now()->subMonth();

        // عدد الطلبات الشهر الحالي
        $currentMonthCount = Maintenance::whereMonth('created_at', $today->month)
            ->whereYear('created_at', $today->year)
            ->count();

        // عدد الطلبات الشهر الماضي
        $lastMonthCount = Maintenance::whereMonth('created_at', $lastMonth->month)
            ->whereYear('created_at', $lastMonth->year)
            ->count();

        // نسبة التغيير
        $changePercentage = $lastMonthCount > 0
            ? round((($currentMonthCount - $lastMonthCount) / $lastMonthCount) * 100, 2)
            : ($currentMonthCount > 0 ? 100 : 0);

        // معدل الإنجاز
        $totalCompleted = Maintenance::where('status', 2)->count();
        $totalMaintenances = Maintenance::count();
        $completionRate = $totalMaintenances > 0
            ? round(($totalCompleted / $totalMaintenances) * 100, 2)
            : 0;

        // متوسط وقت الإنجاز (بالأيام)
        $avgCompletionTime = Maintenance::where('status', 2)
            ->whereNotNull('date')
            ->whereNotNull('accural_date')
            ->selectRaw('AVG(DATEDIFF(date, accural_date)) as avg_days')
            ->value('avg_days');

        // الطلبات العاجلة
        $pendingUrgent = Maintenance::where('status', 0)
            ->whereNotNull('date')
            ->where('date', '<', now()->addDays(3))
            ->count();

        return [
            'current_month_count' => $currentMonthCount,
            'last_month_count' => $lastMonthCount,
            'change_percentage' => $changePercentage,
            'is_increase' => $changePercentage >= 0,
            'completion_rate' => $completionRate,
            'avg_completion_days' => $avgCompletionTime ? round($avgCompletionTime, 1) : 0,
            'pending_urgent' => $pendingUrgent,
        ];
    }

    /**
     * الحصول على label الحالة
     */
    private function getStatusLabel($status)
    {
        $labels = [
            0 => 'قيد الانتظار',
            1 => 'قيد التنفيذ',
            2 => 'مكتملة',
            3 => 'ملغاة',
        ];
        $status = $this->toIntStatus($status);
        return $labels[$status] ?? 'غير محدد';
    }

    /**
     * الحصول على لون الحالة
     */
    private function getStatusColor($status)
    {
        $colors = [
            0 => 'warning',
            1 => 'info',
            2 => 'success',
            3 => 'danger',
        ];
        $status = $this->toIntStatus($status);
        return $colors[$status] ?? 'secondary';
    }
}
