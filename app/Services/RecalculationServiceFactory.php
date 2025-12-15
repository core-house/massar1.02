<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Queue;
use App\Models\OperationItems;
use App\Jobs\RecalculateAverageCostJob;

/**
 * Factory لاختيار نوع الخدمة المناسبة حسب حجم البيانات
 * Hybrid Approach: Stored Procedures للبيانات الكبيرة، PHP للبيانات المتوسطة/الصغيرة
 */
class RecalculationServiceFactory
{
    /**
     * تحديد ما إذا كان يجب استخدام Stored Procedures
     */
    private static function shouldUseStoredProcedures(array $itemIds, ?string $fromDate = null): bool
    {
        // التحقق من الإعداد
        $enabled = Config::get('app.use_stored_procedures_for_recalculation', false);
        if (!$enabled) {
            return false;
        }

        // إذا كان عدد الأصناف كبير جداً
        if (count($itemIds) > 1000) {
            return true;
        }

        // إذا كان عدد الفواتير المتأثرة كبير
        $operationCount = \App\Models\OperationItems::whereIn('operation_items.item_id', $itemIds)
            ->where('operation_items.is_stock', 1)
            ->join('operhead', 'operation_items.pro_id', '=', 'operhead.id')
            ->where('operhead.isdeleted', 0)
            ->when($fromDate, function ($query) use ($fromDate) {
                return $query->where('operhead.pro_date', '>=', $fromDate);
            })
            ->count();

        // إذا كان هناك أكثر من 100,000 عملية متأثرة، استخدم Stored Procedures
        return $operationCount > 100000;
    }

    /**
     * تحديد ما إذا كان يجب استخدام Queue Jobs
     */
    private static function shouldUseQueue(array $itemIds, ?string $fromDate = null): bool
    {
        $queueEnabled = Config::get('queue.default') !== 'sync';
        if (!$queueEnabled) {
            return false;
        }

        // إذا كان عدد الأصناف كبير جداً (> 5000) أو عدد العمليات > 500,000
        if (count($itemIds) > 5000) {
            return true;
        }

        $operationCount = \App\Models\OperationItems::whereIn('item_id', $itemIds)
            ->where('is_stock', 1)
            ->join('operhead', 'operation_items.pro_id', '=', 'operhead.id')
            ->where('operhead.isdeleted', 0)
            ->when($fromDate, function ($query) use ($fromDate) {
                return $query->where('operhead.pro_date', '>=', $fromDate);
            })
            ->count();

        return $operationCount > 500000;
    }

    /**
     * إعادة حساب average_cost مع اختيار الطريقة المناسبة (Queue/Stored Procedure/PHP)
     */
    public static function recalculateAverageCost(array $itemIds, ?string $fromDate = null, bool $useQueue = false): void
    {
        if (empty($itemIds)) {
            return;
        }

        // استخدام Queue للبيانات الكبيرة جداً
        if ($useQueue || self::shouldUseQueue($itemIds, $fromDate)) {
            RecalculateAverageCostJob::dispatch($itemIds, $fromDate, false);
            return;
        }

        // استخدام Stored Procedures أو PHP Services
        $service = self::createAverageCostService($itemIds, $fromDate);
        $service->recalculateFromOperationWithItems($itemIds, $fromDate ?? date('Y-m-d'));
    }

    /**
     * إنشاء خدمة إعادة حساب average_cost
     */
    public static function createAverageCostService(array $itemIds, ?string $fromDate = null): AverageCostRecalculationServiceOptimized|AverageCostRecalculationServiceStoredProcedure
    {
        if (self::shouldUseStoredProcedures($itemIds, $fromDate)) {
            return new AverageCostRecalculationServiceStoredProcedure();
        }

        return new AverageCostRecalculationServiceOptimized();
    }

    /**
     * إنشاء خدمة إعادة حساب الأرباح والقيود
     */
    public static function createProfitService(array $itemIds, ?string $fromDate = null): ProfitAndJournalRecalculationServiceOptimized|ProfitAndJournalRecalculationServiceStoredProcedure
    {
        if (self::shouldUseStoredProcedures($itemIds, $fromDate)) {
            return new ProfitAndJournalRecalculationServiceStoredProcedure();
        }

        return new ProfitAndJournalRecalculationServiceOptimized();
    }
}

