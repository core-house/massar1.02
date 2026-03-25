<?php

declare(strict_types=1);

namespace Modules\POS\app\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Modules\POS\Models\PrintJob;

/**
 * Service for monitoring print job KPIs and health metrics.
 */
class PrintJobMonitoringService
{
    /**
     * Get comprehensive KPIs for print jobs.
     */
    public function getKPIs(int $hours = 24): array
    {
        $startTime = now()->subHours($hours);

        return [
            'success_rate' => $this->getSuccessRate($startTime),
            'failure_rate' => $this->getFailureRate($startTime),
            'average_dispatch_latency' => $this->getAverageDispatchLatency($startTime),
            'queue_backlog_length' => $this->getQueueBacklogLength(),
            'per_station_failures' => $this->getPerStationFailures($startTime),
            'duplicate_prints' => $this->getDuplicatePrints($startTime),
            'error_type_distribution' => $this->getErrorTypeDistribution($startTime),
            'total_jobs' => $this->getTotalJobs($startTime),
            'period_hours' => $hours,
            'generated_at' => now()->toIso8601String(),
        ];
    }

    /**
     * Get success rate (target: >= 99.5%).
     */
    public function getSuccessRate(\DateTimeInterface $startTime): float
    {
        $total = PrintJob::where('created_at', '>=', $startTime)->count();

        if ($total === 0) {
            return 100.0;
        }

        $successful = PrintJob::where('created_at', '>=', $startTime)
            ->where('status', 'printed')
            ->count();

        return round(($successful / $total) * 100, 2);
    }

    /**
     * Get failure rate.
     */
    public function getFailureRate(\DateTimeInterface $startTime): float
    {
        $total = PrintJob::where('created_at', '>=', $startTime)->count();

        if ($total === 0) {
            return 0.0;
        }

        $failed = PrintJob::where('created_at', '>=', $startTime)
            ->where('status', 'failed')
            ->count();

        return round(($failed / $total) * 100, 2);
    }

    /**
     * Get average dispatch latency (created_at -> sent_at) in seconds.
     * Target: < 1s under normal load.
     */
    public function getAverageDispatchLatency(\DateTimeInterface $startTime): float
    {
        $avgLatency = PrintJob::where('created_at', '>=', $startTime)
            ->whereNotNull('sent_at')
            ->selectRaw('AVG(TIMESTAMPDIFF(SECOND, created_at, sent_at)) as avg_latency')
            ->value('avg_latency');

        return round($avgLatency ?? 0, 2);
    }

    /**
     * Get queue backlog length (jobs in queued or sending status).
     */
    public function getQueueBacklogLength(): int
    {
        return PrintJob::whereIn('status', ['queued', 'sending'])->count();
    }

    /**
     * Get per-station failure counts.
     */
    public function getPerStationFailures(\DateTimeInterface $startTime): array
    {
        return PrintJob::where('created_at', '>=', $startTime)
            ->where('status', 'failed')
            ->select('printer_station_id', DB::raw('COUNT(*) as failure_count'))
            ->with('printerStation:id,name')
            ->groupBy('printer_station_id')
            ->get()
            ->map(fn ($item) => [
                'station_id' => $item->printer_station_id,
                'station_name' => $item->printerStation->name ?? 'Unknown',
                'failure_count' => $item->failure_count,
            ])
            ->toArray();
    }

    /**
     * Get duplicate prints count (should be 0 with idempotency).
     */
    public function getDuplicatePrints(\DateTimeInterface $startTime): int
    {
        // Count jobs with same transaction_id, station_id, and payload_hash
        // but different idempotency_key (indicating duplicates)
        $duplicates = PrintJob::where('created_at', '>=', $startTime)
            ->select('transaction_id', 'printer_station_id', 'payload_hash', DB::raw('COUNT(*) as count'))
            ->groupBy('transaction_id', 'printer_station_id', 'payload_hash')
            ->having('count', '>', 1)
            ->get();

        return $duplicates->sum('count') - $duplicates->count();
    }

    /**
     * Get error type distribution.
     */
    public function getErrorTypeDistribution(\DateTimeInterface $startTime): array
    {
        return PrintJob::where('created_at', '>=', $startTime)
            ->where('status', 'failed')
            ->select('error_type', DB::raw('COUNT(*) as count'))
            ->groupBy('error_type')
            ->get()
            ->pluck('count', 'error_type')
            ->toArray();
    }

    /**
     * Get total jobs count.
     */
    public function getTotalJobs(\DateTimeInterface $startTime): int
    {
        return PrintJob::where('created_at', '>=', $startTime)->count();
    }

    /**
     * Check if print agent is healthy (via health endpoint).
     * Cache result for 1 minute to avoid excessive checks.
     */
    public function checkAgentHealth(): array
    {
        return Cache::remember('print_agent_health', 60, function () {
            try {
                $agentUrl = config('kitchen-printer.print_agent_url', 'http://localhost:5000/print');
                $healthUrl = str_replace('/print', '/health', $agentUrl);

                $response = \Illuminate\Support\Facades\Http::timeout(2)->get($healthUrl);

                if ($response->successful()) {
                    return [
                        'status' => 'healthy',
                        'data' => $response->json(),
                        'checked_at' => now()->toIso8601String(),
                    ];
                }

                return [
                    'status' => 'unhealthy',
                    'error' => "HTTP {$response->status()}",
                    'checked_at' => now()->toIso8601String(),
                ];
            } catch (\Exception $e) {
                return [
                    'status' => 'down',
                    'error' => $e->getMessage(),
                    'checked_at' => now()->toIso8601String(),
                ];
            }
        });
    }

    /**
     * Get mean time to detect agent down (in minutes).
     * Target: < 2 minutes.
     */
    public function getMeanTimeToDetectAgentDown(): float
    {
        // Get first AGENT_DOWN error after a period of successful prints
        $recentDowntimes = PrintJob::where('error_type', 'AGENT_DOWN')
            ->where('created_at', '>=', now()->subDays(7))
            ->orderBy('created_at')
            ->get();

        if ($recentDowntimes->isEmpty()) {
            return 0.0;
        }

        $detectionTimes = [];

        foreach ($recentDowntimes as $downtime) {
            // Find last successful print before this downtime
            $lastSuccess = PrintJob::where('status', 'printed')
                ->where('created_at', '<', $downtime->created_at)
                ->orderBy('created_at', 'desc')
                ->first();

            if ($lastSuccess) {
                $detectionTimes[] = $downtime->created_at->diffInMinutes($lastSuccess->created_at);
            }
        }

        if (empty($detectionTimes)) {
            return 0.0;
        }

        return round(array_sum($detectionTimes) / count($detectionTimes), 2);
    }

    /**
     * Get alert-worthy issues.
     */
    public function getAlerts(): array
    {
        $alerts = [];
        $kpis = $this->getKPIs(1); // Last hour

        // Alert if success rate < 99.5%
        if ($kpis['success_rate'] < 99.5) {
            $alerts[] = [
                'severity' => 'high',
                'type' => 'low_success_rate',
                'message' => "معدل النجاح منخفض: {$kpis['success_rate']}% (الهدف: >= 99.5%)",
                'value' => $kpis['success_rate'],
            ];
        }

        // Alert if dispatch latency > 1s
        if ($kpis['average_dispatch_latency'] > 1.0) {
            $alerts[] = [
                'severity' => 'medium',
                'type' => 'high_latency',
                'message' => "زمن الإرسال مرتفع: {$kpis['average_dispatch_latency']}s (الهدف: < 1s)",
                'value' => $kpis['average_dispatch_latency'],
            ];
        }

        // Alert if queue backlog > 10
        if ($kpis['queue_backlog_length'] > 10) {
            $alerts[] = [
                'severity' => 'medium',
                'type' => 'high_backlog',
                'message' => "تراكم في قائمة الانتظار: {$kpis['queue_backlog_length']} مهمة",
                'value' => $kpis['queue_backlog_length'],
            ];
        }

        // Alert if duplicate prints detected
        if ($kpis['duplicate_prints'] > 0) {
            $alerts[] = [
                'severity' => 'critical',
                'type' => 'duplicate_prints',
                'message' => "تم اكتشاف طباعة مكررة: {$kpis['duplicate_prints']} حالة",
                'value' => $kpis['duplicate_prints'],
            ];
        }

        // Alert if agent is down
        $agentHealth = $this->checkAgentHealth();
        if ($agentHealth['status'] !== 'healthy') {
            $alerts[] = [
                'severity' => 'critical',
                'type' => 'agent_down',
                'message' => "وكيل الطباعة غير متاح: {$agentHealth['status']}",
                'value' => $agentHealth,
            ];
        }

        return $alerts;
    }
}
