<?php

namespace Modules\Checks\Livewire;

use Livewire\Component;
use Modules\Checks\Models\Check;
use Illuminate\Support\Facades\DB;

class ChecksDashboard extends Component
{
    public $dateFilter = 'month'; // month, week, year
    public $selectedStatus = 'all';

    public function render()
    {
        $dateRange = $this->getDateRange();
        
        // Statistics
        $totalChecks = Check::whereBetween('created_at', $dateRange)->count();
        $pendingChecks = Check::whereBetween('created_at', $dateRange)
            ->where('status', Check::STATUS_PENDING)->count();
        $clearedChecks = Check::whereBetween('created_at', $dateRange)
            ->where('status', Check::STATUS_CLEARED)->count();
        $bouncedChecks = Check::whereBetween('created_at', $dateRange)
            ->where('status', Check::STATUS_BOUNCED)->count();

        // Amounts
        $totalAmount = Check::whereBetween('created_at', $dateRange)
            ->sum('amount');
        $pendingAmount = Check::whereBetween('created_at', $dateRange)
            ->where('status', Check::STATUS_PENDING)->sum('amount');
        $clearedAmount = Check::whereBetween('created_at', $dateRange)
            ->where('status', Check::STATUS_CLEARED)->sum('amount');

        // Overdue checks
        $overdueChecks = Check::where('status', Check::STATUS_PENDING)
            ->where('due_date', '<', now())
            ->orderBy('due_date', 'asc')
            ->limit(10)
            ->get();

        // Recent checks
        $recentChecks = Check::with(['creator'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Checks by bank
        $checksByBank = Check::whereBetween('created_at', $dateRange)
            ->select('bank_name', DB::raw('count(*) as count'), DB::raw('sum(amount) as total_amount'))
            ->groupBy('bank_name')
            ->orderBy('count', 'desc')
            ->limit(5)
            ->get();

        // Monthly trend (for charts)
        $monthlyTrend = Check::select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('YEAR(created_at) as year'),
                DB::raw('count(*) as count'),
                DB::raw('sum(amount) as total_amount')
            )
            ->where('created_at', '>=', now()->subYear())
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();

        return view('checks::livewire.checks-dashboard', [
            'stats' => [
                'total' => $totalChecks,
                'pending' => $pendingChecks,
                'cleared' => $clearedChecks,
                'bounced' => $bouncedChecks,
                'totalAmount' => $totalAmount,
                'pendingAmount' => $pendingAmount,
                'clearedAmount' => $clearedAmount,
            ],
            'overdueChecks' => $overdueChecks,
            'recentChecks' => $recentChecks,
            'checksByBank' => $checksByBank,
            'monthlyTrend' => $monthlyTrend,
        ]);
    }

    private function getDateRange()
    {
        return match($this->dateFilter) {
            'week' => [now()->startOfWeek(), now()->endOfWeek()],
            'month' => [now()->startOfMonth(), now()->endOfMonth()],
            'year' => [now()->startOfYear(), now()->endOfYear()],
            default => [now()->startOfMonth(), now()->endOfMonth()],
        };
    }
}