<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Item;
use App\Models\OperHead;
use App\Models\OperationItems;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * نسخة محسّنة من AverageCostRecalculationService للأداء العالي
 * تستخدم SQL aggregations و batch updates بدلاً من loops
 */
class AverageCostRecalculationServiceOptimized
{
    /**
     * إعادة حساب متوسط التكلفة لصنف معين من تاريخ محدد
     * يستخدم SQL aggregation بدلاً من PHP loops
     * 
     * ملاحظة: عند الحذف، يجب حساب من جميع الفواتير غير المحذوفة (لا من fromDate فقط)
     * لأن الحذف يؤثر على كل الفواتير التالية
     */
    public function recalculateAverageCostForItem(int $itemId, ?string $fromDate = null, bool $isDelete = false): void
    {
        $item = Item::find($itemId);
        if (!$item) {
            Log::warning("Item not found for average cost recalculation: {$itemId}");
            return;
        }

        // عند الحذف، نحسب من جميع الفواتير غير المحذوفة (لا من fromDate فقط)
        // لأن الحذف يؤثر على كل الفواتير التالية
        if ($isDelete) {
            $fromDate = null;
        }

        // استخدام raw SQL للحصول على النتيجة النهائية مباشرة
        $sql = "
            SELECT 
                SUM(oi.qty_in - oi.qty_out) as total_qty,
                SUM(oi.detail_value) as total_value
            FROM operation_items oi
            INNER JOIN operhead oh ON oi.pro_id = oh.id
            WHERE oi.item_id = ?
                AND oi.is_stock = 1
                AND oi.pro_tybe IN (11, 12, 20, 59)
                AND oh.isdeleted = 0
        ";

        $params = [$itemId];

        // عند التعديل فقط، نحسب من fromDate
        // عند الحذف، نحسب من جميع الفواتير (fromDate = null)
        if ($fromDate && !$isDelete) {
            $sql .= " AND oh.pro_date >= ?";
            $params[] = $fromDate;
        }

        $result = DB::selectOne($sql, $params);

        $totalQty = (float) ($result->total_qty ?? 0);
        $totalValue = (float) ($result->total_value ?? 0);

        // تحديث واحد فقط للصنف
        $newAverage = $totalQty > 0 ? ($totalValue / $totalQty) : 0;
        
        DB::table('items')
            ->where('id', $itemId)
            ->update(['average_cost' => $newAverage]);

        Log::info("Recalculated average cost for item {$itemId} - Qty: {$totalQty}, Value: {$totalValue}, Avg: {$newAverage}, IsDelete: " . ($isDelete ? 'true' : 'false'));
    }

    /**
     * إعادة حساب متوسط التكلفة لعدة أصناف دفعة واحدة
     * يستخدم batch processing و single query لكل صنف
     */
    public function recalculateAverageCostForItems(array $itemIds, ?string $fromDate = null, bool $isDelete = false): void
    {
        if (empty($itemIds)) {
            return;
        }

        // عند الحذف، نحسب من جميع الفواتير غير المحذوفة
        if ($isDelete) {
            $fromDate = null;
        }

        // معالجة الأصناف في batches لتقليل الضغط على قاعدة البيانات
        $chunks = array_chunk($itemIds, 100); // معالجة 100 صنف في كل مرة

        foreach ($chunks as $chunk) {
            $this->recalculateBatch($chunk, $fromDate, $isDelete);
        }
    }

    /**
     * إعادة حساب batch من الأصناف باستخدام single query
     */
    private function recalculateBatch(array $itemIds, ?string $fromDate = null, bool $isDelete = false): void
    {
        // عند الحذف، نحسب من جميع الفواتير غير المحذوفة
        if ($isDelete) {
            $fromDate = null;
        }

        $placeholders = implode(',', array_fill(0, count($itemIds), '?'));
        
        $sql = "
            SELECT 
                oi.item_id,
                SUM(oi.qty_in - oi.qty_out) as total_qty,
                SUM(oi.detail_value) as total_value
            FROM operation_items oi
            INNER JOIN operhead oh ON oi.pro_id = oh.id
            WHERE oi.item_id IN ({$placeholders})
                AND oi.is_stock = 1
                AND oi.pro_tybe IN (11, 12, 20, 59)
                AND oh.isdeleted = 0
        ";

        $params = $itemIds;

        // عند التعديل فقط، نحسب من fromDate
        if ($fromDate && !$isDelete) {
            $sql .= " AND oh.pro_date >= ?";
            $params[] = $fromDate;
        }

        $sql .= " GROUP BY oi.item_id";

        $results = DB::select($sql, $params);

        // إعداد بيانات للـ batch update
        $updates = [];
        foreach ($results as $result) {
            $itemId = (int) $result->item_id;
            $totalQty = (float) $result->total_qty;
            $totalValue = (float) $result->total_value;
            $newAverage = $totalQty > 0 ? ($totalValue / $totalQty) : 0;
            
            $updates[] = [
                'id' => $itemId,
                'average_cost' => $newAverage,
            ];
        }

        // معالجة الأصناف التي لم تظهر في النتائج (لا توجد فواتير لها)
        $processedIds = array_column($updates, 'id');
        $missingIds = array_diff($itemIds, $processedIds);
        
        foreach ($missingIds as $itemId) {
            $updates[] = [
                'id' => $itemId,
                'average_cost' => 0,
            ];
        }

        // Batch update باستخدام CASE statement (أسرع من multiple updates)
        if (!empty($updates)) {
            $this->batchUpdateAverageCost($updates);
        }
    }

    /**
     * Batch update للأصناف باستخدام CASE statement
     * أسرع بكثير من multiple UPDATE statements
     */
    private function batchUpdateAverageCost(array $updates): void
    {
        if (empty($updates)) {
            return;
        }

        // تقسيم إلى batches صغيرة لتجنب query كبيرة جداً
        $chunks = array_chunk($updates, 50);
        
        foreach ($chunks as $chunk) {
            $ids = [];
            $cases = [];
            $params = [];

            foreach ($chunk as $update) {
                $ids[] = $update['id'];
                $cases[] = "WHEN ? THEN ?";
                $params[] = $update['id'];
                $params[] = $update['average_cost'];
            }

            $idsPlaceholder = implode(',', array_fill(0, count($ids), '?'));
            $casesSql = implode(' ', $cases);

            $sql = "
                UPDATE items 
                SET average_cost = CASE id 
                    {$casesSql}
                END
                WHERE id IN ({$idsPlaceholder})
            ";

            $params = array_merge($params, $ids);
            
            DB::update($sql, $params);
        }
    }

    /**
     * إعادة حساب متوسط التكلفة بعد تعديل/حذف فاتورة
     * نسخة محسّنة تستخدم batch processing
     */
    public function recalculateFromOperationWithItems(array $itemIds, string $fromDate, bool $isDelete = false): void
    {
        if (empty($itemIds)) {
            return;
        }

        // استخدام batch processing
        $this->recalculateAverageCostForItems($itemIds, $fromDate, $isDelete);
        
        Log::info("Recalculated average cost for " . count($itemIds) . " items from date {$fromDate}, isDelete: " . ($isDelete ? 'true' : 'false'));
    }

    /**
     * إعادة حساب متوسط التكلفة لجميع الأصناف (للاستخدام في الصيانة)
     * يستخدم pagination لتجنب memory issues
     */
    public function recalculateAllItems(?string $fromDate = null, int $chunkSize = 500): void
    {
        $totalItems = Item::count();
        $processed = 0;

        Log::info("Starting full average cost recalculation for {$totalItems} items");

        Item::chunk($chunkSize, function ($items) use ($fromDate, &$processed, $totalItems) {
            $itemIds = $items->pluck('id')->toArray();
            $this->recalculateAverageCostForItems($itemIds, $fromDate);
            $processed += count($itemIds);
            
            Log::info("Processed {$processed} / {$totalItems} items");
        });

        Log::info("Completed full average cost recalculation");
    }
}
