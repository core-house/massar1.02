<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use App\Jobs\RecalculateAverageCostJob;

/**
 * Helper class لإعادة الحساب مع اختيار تلقائي للطريقة المناسبة
 * يختار تلقائياً بين: Queue Jobs / Stored Procedures / PHP Services
 */
class RecalculationServiceHelper
{
    /**
     * إعادة حساب average_cost مع اختيار تلقائي للطريقة المناسبة
     */
    public static function recalculateAverageCost(array $itemIds, ?string $fromDate = null, bool $forceQueue = false, bool $isDelete = false): void
    {
        if (empty($itemIds)) {
            return;
        }

        $queueEnabled = Config::get('queue.default') !== 'sync';
        $storedProceduresEnabled = Config::get('app.use_stored_procedures_for_recalculation', false);

        // 1. استخدام Queue للبيانات الكبيرة جداً
        if ($forceQueue || self::shouldUseQueue($itemIds, $fromDate)) {
            Log::info('Using Queue Job for average cost recalculation', [
                'item_count' => count($itemIds),
                'from_date' => $fromDate,
                'is_delete' => $isDelete,
            ]);
            RecalculateAverageCostJob::dispatch($itemIds, $fromDate, false);
            return;
        }

        // 2. استخدام Factory لاختيار Stored Procedures أو PHP Services
        $service = RecalculationServiceFactory::createAverageCostService($itemIds, $fromDate);
        $service->recalculateFromOperationWithItems($itemIds, $fromDate ?? date('Y-m-d'), $isDelete);
    }

    /**
     * إعادة حساب الأرباح والقيود مع اختيار تلقائي للطريقة المناسبة
     * 
     * @param array $itemIds الأصناف المتأثرة
     * @param string|null $fromDate تاريخ الفاتورة المضافة/المعدلة/المحذوفة
     * @param int|null $currentInvoiceId ID الفاتورة الحالية (للتأكد من عدم إعادة حسابها)
     * @param string|null $currentInvoiceCreatedAt وقت إنشاء الفاتورة الحالية (لمقارنة الفواتير في نفس اليوم)
     */
    public static function recalculateProfitsAndJournals(array $itemIds, ?string $fromDate = null, ?int $currentInvoiceId = null, ?string $currentInvoiceCreatedAt = null): void
    {
        if (empty($itemIds)) {
            return;
        }

        // استخدام Factory لاختيار Stored Procedures أو PHP Services
        $service = RecalculationServiceFactory::createProfitService($itemIds, $fromDate);
        $service->recalculateAllAffectedOperations($itemIds, $fromDate ?? date('Y-m-d'), $currentInvoiceId, $currentInvoiceCreatedAt);
    }

    /**
     * إعادة حساب كل شيء (average_cost + profits + journals)
     */
    public static function recalculateAll(array $itemIds, ?string $fromDate = null, bool $forceQueue = false, ?int $currentInvoiceId = null, ?string $currentInvoiceCreatedAt = null): void
    {
        if (empty($itemIds)) {
            return;
        }

        // إعادة حساب average_cost
        self::recalculateAverageCost($itemIds, $fromDate, $forceQueue);

        // إعادة حساب الأرباح والقيود (دائماً مباشر، لا queue)
        self::recalculateProfitsAndJournals($itemIds, $fromDate, $currentInvoiceId, $currentInvoiceCreatedAt);
    }

    /**
     * تحديد ما إذا كان يجب استخدام Queue
     */
    private static function shouldUseQueue(array $itemIds, ?string $fromDate = null): bool
    {
        $queueEnabled = Config::get('queue.default') !== 'sync';
        if (!$queueEnabled) {
            return false;
        }

        // إذا كان عدد الأصناف كبير جداً (> 5000)
        if (count($itemIds) > 5000) {
            return true;
        }

        // إذا كان عدد العمليات المتأثرة كبير جداً (> 500,000)
        // استخدام DB facade لتجنب مشاكل ambiguous columns
        $query = \Illuminate\Support\Facades\DB::table('operation_items')
            ->whereIn('operation_items.item_id', $itemIds)
            ->where('operation_items.is_stock', 1)
            ->join('operhead', 'operation_items.pro_id', '=', 'operhead.id')
            ->where('operhead.isdeleted', 0);
            
        if ($fromDate) {
            $query->where('operhead.pro_date', '>=', $fromDate);
        }
        
        $operationCount = $query->count();

        return $operationCount > 500000;
    }
}

