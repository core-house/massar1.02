<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Item;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * خدمة تستخدم Stored Procedures لإعادة حساب average_cost
 * أسرع بكثير للبيانات الكبيرة جداً (ملايين الصفوف)
 */
class AverageCostRecalculationServiceStoredProcedure
{
    /**
     * إعادة حساب متوسط التكلفة لصنف معين باستخدام Stored Procedure
     */
    public function recalculateAverageCostForItem(int $itemId, ?string $fromDate = null): void
    {
        $item = Item::find($itemId);
        if (!$item) {
            Log::warning("Item not found for average cost recalculation: {$itemId}");
            return;
        }

        try {
            DB::statement('CALL sp_recalculate_average_cost(?, ?)', [
                $itemId,
                $fromDate
            ]);

            Log::info("Recalculated average cost for item {$itemId} using stored procedure");
        } catch (\Exception $e) {
            Log::error("Error in stored procedure for item {$itemId}: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * إعادة حساب متوسط التكلفة لعدة أصناف باستخدام Stored Procedure
     */
    public function recalculateAverageCostForItems(array $itemIds, ?string $fromDate = null): void
    {
        if (empty($itemIds)) {
            return;
        }

        // تحويل array إلى comma-separated string
        $itemIdsString = implode(',', $itemIds);

        try {
            DB::statement('CALL sp_recalculate_average_cost_batch(?, ?)', [
                $itemIdsString,
                $fromDate
            ]);

            Log::info("Recalculated average cost for " . count($itemIds) . " items using stored procedure");
        } catch (\Exception $e) {
            Log::error("Error in batch stored procedure: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * إعادة حساب متوسط التكلفة بعد تعديل/حذف فاتورة
     */
    public function recalculateFromOperationWithItems(array $itemIds, string $fromDate, bool $isDelete = false): void
    {
        if (empty($itemIds)) {
            return;
        }

        // عند الحذف، نحسب من جميع الفواتير غير المحذوفة (نمرر null بدلاً من fromDate)
        if ($isDelete) {
            $fromDate = null;
        }

        $this->recalculateAverageCostForItems($itemIds, $fromDate);
    }

    /**
     * إعادة حساب متوسط التكلفة لجميع الأصناف (للاستخدام في الصيانة)
     * يستخدم pagination لتجنب memory issues
     */
    public function recalculateAllItems(?string $fromDate = null, int $chunkSize = 500): void
    {
        $totalItems = Item::count();
        $processed = 0;

        Log::info("Starting full average cost recalculation for {$totalItems} items using stored procedures");

        Item::chunk($chunkSize, function ($items) use ($fromDate, &$processed, $totalItems) {
            $itemIds = $items->pluck('id')->toArray();
            $this->recalculateAverageCostForItems($itemIds, $fromDate);
            $processed += count($itemIds);
            
            Log::info("Processed {$processed} / {$totalItems} items");
        });

        Log::info("Completed full average cost recalculation");
    }
}

