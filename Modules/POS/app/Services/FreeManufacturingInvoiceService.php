<?php

declare(strict_types=1);

namespace Modules\POS\app\Services;

use App\Models\Item;
use App\Models\OperationItems;
use App\Models\OperHead;
use Modules\Manufacturing\Models\ManufacturingOrder;

class FreeManufacturingInvoiceService
{
    /**
     * ينشئ فاتورة تصنيع حر (pro_type=59) لصنف واحد بناءً على نموذج التصنيع.
     *
     * المنطق: الصنف المباع في فاتورة المطعم هو المنتج التام.
     * نُنشئ فاتورة تصنيع حر تُضيف هذا الصنف للمخزن (qty_in = quantity).
     *
     * @param  ManufacturingOrder  $template  نموذج التصنيع (is_template=1)
     * @param  float  $quantity  الكمية المطلوبة من فاتورة المطعم
     * @param  int  $storeId  معرف المخزن
     * @param  int  $branchId  معرف الفرع
     * @param  int  $userId  معرف المستخدم
     * @return OperHead فاتورة التصنيع الحر المنشأة
     *
     * @throws \Exception
     */
    public function create(
        ManufacturingOrder $template,
        float $quantity,
        ?int $storeId,
        int $branchId,
        int $userId
    ): OperHead {
        $nextProId = (int) (OperHead::max('pro_id') ?? 0) + 1;

        $item = Item::find($template->item_id);
        $itemName = $item?->name ?? 'صنف غير معروف';
        $averageCost = (float) ($item?->average_cost ?? 0);

        $totalCost = $averageCost * $quantity;

        $operHead = OperHead::create([
            'pro_id' => $nextProId,
            'pro_type' => 59,
            'pro_date' => now()->format('Y-m-d'),
            'accural_date' => now()->format('Y-m-d'),
            'store_id' => $storeId,
            'acc2' => $storeId,
            'manufacturing_order_id' => $template->id,
            'is_stock' => 1,
            'is_finance' => 0,
            'is_journal' => 0,
            'is_manager' => 0,
            'is_template' => 0,
            'isdeleted' => 0,
            'pro_value' => $totalCost,
            'fat_net' => $totalCost,
            'info' => "تصنيع حر - {$itemName}",
            'user' => $userId,
            'branch_id' => $branchId,
        ]);

        // إنشاء سطر OperationItems للمنتج التام (qty_in = الكمية المصنعة)
        $unitId = $item?->units()->first()?->id;

        OperationItems::create([
            'pro_tybe' => 59,
            'detail_store' => $storeId,
            'pro_id' => $operHead->id,
            'item_id' => $template->item_id,
            'unit_id' => $unitId,
            'qty_in' => $quantity,
            'qty_out' => 0,
            'item_price' => $averageCost,
            'cost_price' => $averageCost,
            'detail_value' => $totalCost,
            'is_stock' => 1,
            'isdeleted' => 0,
            'branch_id' => $branchId,
        ]);

        return $operHead;
    }
}
