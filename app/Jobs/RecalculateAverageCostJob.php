<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Services\AverageCostRecalculationServiceOptimized;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Job لإعادة حساب متوسط التكلفة في الخلفية
 * للاستخدام مع الفواتير الكبيرة أو عند إعادة حساب جميع الأصناف
 */
class RecalculateAverageCostJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 600; // 10 دقائق
    public int $tries = 3;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public array $itemIds,
        public ?string $fromDate = null,
        public bool $isFullRecalculation = false
    ) {
        // تحديد queue connection حسب الحجم
        if (count($itemIds) > 1000 || $isFullRecalculation) {
            $this->onQueue('recalculation-large');
        } else {
            $this->onQueue('recalculation');
        }
    }

    /**
     * Execute the job.
     */
    public function handle(AverageCostRecalculationServiceOptimized $service): void
    {
        try {
            Log::info("Starting average cost recalculation job", [
                'item_count' => count($this->itemIds),
                'from_date' => $this->fromDate,
                'is_full' => $this->isFullRecalculation,
            ]);

            if ($this->isFullRecalculation) {
                $service->recalculateAllItems($this->fromDate);
            } else {
                $service->recalculateAverageCostForItems($this->itemIds, $this->fromDate);
            }

            Log::info("Completed average cost recalculation job", [
                'item_count' => count($this->itemIds),
            ]);
        } catch (\Exception $e) {
            Log::error("Error in average cost recalculation job", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error("Average cost recalculation job failed", [
            'item_ids' => $this->itemIds,
            'error' => $exception->getMessage(),
        ]);
    }
}

