<?php

declare(strict_types=1);

namespace Modules\POS\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Modules\POS\Jobs\PrintKitchenOrderJob;
use Modules\POS\Models\KitchenPrinterStation;
use Modules\POS\Models\PrintJob;
use Modules\POS\Services\PrintJobIdempotencyService;
use Modules\POS\Services\PrintJobMonitoringService;
use RealRashid\SweetAlert\Facades\Alert;

class PrintJobController extends Controller
{
    public function __construct(
        private PrintJobIdempotencyService $idempotencyService,
        private PrintJobMonitoringService $monitoringService
    ) {
        $this->middleware('permission:view Print Jobs')->only(['index', 'show', 'monitoring']);
        $this->middleware('permission:retry Print Jobs')->only(['retry', 'batchRetry']);
    }

    public function index(Request $request)
    {
        $query = PrintJob::with(['printerStation', 'transaction', 'printedBy', 'retriedBy'])
            ->orderBy('created_at', 'desc');

        // تصفية حسب التاريخ
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // تصفية حسب المحطة
        if ($request->filled('printer_station_id')) {
            $query->where('printer_station_id', $request->printer_station_id);
        }

        // تصفية حسب الحالة
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // تصفية حسب نوع الخطأ
        if ($request->filled('error_type')) {
            $query->where('error_type', $request->error_type);
        }

        // تصفية: فقط المهام القابلة لإعادة المحاولة
        if ($request->filled('retryable') && $request->retryable === '1') {
            $query->where('status', 'failed')
                ->where('can_auto_retry', false);
        }

        $printJobs = $query->paginate(50);
        $printerStations = KitchenPrinterStation::orderBy('name')->get();

        // Get quick stats
        $stats = [
            'total' => PrintJob::recent(24)->count(),
            'printed' => PrintJob::recent(24)->byStatus('printed')->count(),
            'failed' => PrintJob::recent(24)->byStatus('failed')->count(),
            'queued' => PrintJob::recent(24)->whereIn('status', ['queued', 'sending'])->count(),
        ];

        return view('pos::print-jobs.index', compact('printJobs', 'printerStations', 'stats'));
    }

    public function show(PrintJob $printJob)
    {
        $printJob->load(['printerStation', 'transaction', 'printedBy', 'retriedBy']);

        // Get related jobs (same transaction and station)
        $relatedJobs = PrintJob::where('transaction_id', $printJob->transaction_id)
            ->where('printer_station_id', $printJob->printer_station_id)
            ->where('id', '!=', $printJob->id)
            ->orderBy('sequence')
            ->get();

        return view('pos::print-jobs.show', compact('printJob', 'relatedJobs'));
    }

    public function retry(PrintJob $printJob)
    {
        try {
            if (! $printJob->transaction) {
                Alert::error('المعاملة غير موجودة');

                return back();
            }

            // Create new print job for manual retry with audit logging
            $retryJob = $this->idempotencyService->createManualRetryJob(
                $printJob,
                auth()->id()
            );

            // Dispatch job
            PrintKitchenOrderJob::dispatch($retryJob);

            // Log manual retry
            Log::info('Manual print job retry initiated', [
                'original_job_id' => $printJob->id,
                'retry_job_id' => $retryJob->id,
                'user_id' => auth()->id(),
                'user_name' => auth()->user()->name,
                'transaction_id' => $printJob->transaction_id,
                'printer_station_id' => $printJob->printer_station_id,
            ]);

            Alert::success('تم إضافة المهمة لقائمة الانتظار لإعادة المحاولة');
        } catch (\Exception $e) {
            Log::error('Manual print job retry failed', [
                'print_job_id' => $printJob->id,
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
            ]);

            Alert::error('فشلت إعادة المحاولة: '.$e->getMessage());
        }

        return back();
    }

    public function batchRetry(Request $request)
    {
        $request->validate([
            'job_ids' => 'required|array',
            'job_ids.*' => 'exists:print_jobs,id',
        ]);

        $successCount = 0;
        $failCount = 0;

        foreach ($request->job_ids as $jobId) {
            try {
                $printJob = PrintJob::findOrFail($jobId);

                if (! $printJob->transaction) {
                    $failCount++;

                    continue;
                }

                // Create new print job for manual retry
                $retryJob = $this->idempotencyService->createManualRetryJob(
                    $printJob,
                    auth()->id()
                );

                // Dispatch job
                PrintKitchenOrderJob::dispatch($retryJob);

                $successCount++;
            } catch (\Exception $e) {
                $failCount++;
                Log::error('Batch retry failed for job', [
                    'job_id' => $jobId,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        // Log batch retry
        Log::info('Batch print job retry completed', [
            'user_id' => auth()->id(),
            'user_name' => auth()->user()->name,
            'success_count' => $successCount,
            'fail_count' => $failCount,
            'total' => count($request->job_ids),
        ]);

        if ($successCount > 0) {
            Alert::success("تم إضافة {$successCount} مهمة لإعادة المحاولة");
        }

        if ($failCount > 0) {
            Alert::warning("فشلت {$failCount} مهمة");
        }

        return back();
    }

    public function monitoring()
    {
        // Get KPIs for different time periods
        $kpis = [
            'last_hour' => $this->monitoringService->getKPIs(1),
            'last_24_hours' => $this->monitoringService->getKPIs(24),
            'last_week' => $this->monitoringService->getKPIs(168),
        ];

        // Get alerts
        $alerts = $this->monitoringService->getAlerts();

        // Get agent health
        $agentHealth = $this->monitoringService->checkAgentHealth();

        // Get mean time to detect agent down
        $mttd = $this->monitoringService->getMeanTimeToDetectAgentDown();

        return view('pos::print-jobs.monitoring', compact('kpis', 'alerts', 'agentHealth', 'mttd'));
    }
}
