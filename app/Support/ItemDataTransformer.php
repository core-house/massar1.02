<?php

namespace App\Support;

use App\Models\Item;
use Illuminate\Support\Facades\DB;

class ItemDataTransformer
{
    /**
     * Transform item data for display
     * Lightweight alternative to ItemViewModel
     */
    public static function transform(Item $item, ?int $unitId, ?int $warehouseId = null, ?float $precomputedBaseQty = null): array
    {
        return [
            'id' => $item->id,
            'code' => $item->code,
            'name' => $item->name,
            'unitOptions' => self::getUnitOptions($item),
            'formattedQuantity' => self::getFormattedQuantity($item, $unitId, $warehouseId, $precomputedBaseQty),
            'unitCostPrice' => self::getUnitCostPrice($item, $unitId),
            'quantityCost' => self::getQuantityCost($item, $unitId, $warehouseId, $precomputedBaseQty),
            'unitAverageCost' => self::getUnitAverageCost($item, $unitId),
            'quantityAverageCost' => self::getQuantityAverageCost($item, $unitId, $warehouseId, $precomputedBaseQty),
            'unitSalePrices' => self::getUnitSalePrices($item, $unitId),
            'unitBarcodes' => self::getUnitBarcodes($item, $unitId),
            'itemNotes' => self::getItemNotes($item),
        ];
    }

    /**
     * Get unit options for dropdown
     */
    private static function getUnitOptions(Item $item): array
    {
        return $item->units->map(fn ($unit) => [
            'value' => $unit->id,
            'label' => $unit->name.' ['.number_format($unit->pivot->u_val ?? 1).']',
        ])->toArray();
    }

    /**
     * Get total base quantity (u_val = 1) from operation_items
     * Note: qty_in and qty_out are already stored as base quantities
     */
    private static function getTotalBaseQuantity(Item $item, ?int $warehouseId, ?float $precomputedBaseQty = null): float
    {
        // Use precomputed value if available
        if ($precomputedBaseQty !== null) {
            return $precomputedBaseQty;
        }

        $query = DB::table('operation_items')
            ->where('item_id', $item->id)
            ->where('isdeleted', 0);

        if ($warehouseId) {
            $query->where('detail_store', $warehouseId);
        }

        return (float) $query->sum(DB::raw('qty_in - qty_out'));
    }

    /**
     * Get formatted quantity (integer + remainder)
     */
    private static function getFormattedQuantity(Item $item, ?int $unitId, ?int $warehouseId, ?float $precomputedBaseQty = null): array
    {
        $selectedUnit = $item->units->firstWhere('id', $unitId);
        $selectedUVal = $selectedUnit && isset($selectedUnit->pivot) ? $selectedUnit->pivot->u_val : 1;
        $unitName = $selectedUnit->name ?? '';
        $smallerUnit = $item->units->sortBy('pivot.u_val')->first();
        $smallerUnitName = $smallerUnit->name ?? '';

        $totalBaseQty = self::getTotalBaseQuantity($item, $warehouseId, $precomputedBaseQty);

        $integer = $selectedUVal > 0 ? floor($totalBaseQty / $selectedUVal) : 0;
        $remainder = $selectedUVal > 0 ? $totalBaseQty % $selectedUVal : 0;

        return [
            'quantity' => [
                'integer' => $integer,
                'remainder' => $remainder,
            ],
            'unitName' => $unitName,
            'smallerUnitName' => $smallerUnitName,
        ];
    }

    /**
     * Get current unit quantity as float
     */
    private static function getCurrentUnitQuantity(Item $item, ?int $unitId, ?int $warehouseId, ?float $precomputedBaseQty = null): float
    {
        $selectedUnit = $item->units->firstWhere('id', $unitId);
        $selectedUVal = $selectedUnit && isset($selectedUnit->pivot) ? $selectedUnit->pivot->u_val : 1;
        $totalBaseQty = self::getTotalBaseQuantity($item, $warehouseId, $precomputedBaseQty);

        return $selectedUVal > 0 ? $totalBaseQty / $selectedUVal : 0;
    }

    /**
     * Get unit cost price
     */
    private static function getUnitCostPrice(Item $item, ?int $unitId): float
    {
        return $item->units->firstWhere('id', $unitId)?->pivot->cost ?? 0;
    }

    /**
     * Get quantity cost
     */
    private static function getQuantityCost(Item $item, ?int $unitId, ?int $warehouseId, ?float $precomputedBaseQty = null): float
    {
        $quantity = self::getCurrentUnitQuantity($item, $unitId, $warehouseId, $precomputedBaseQty);
        $cost = self::getUnitCostPrice($item, $unitId);

        return $quantity * $cost;
    }

    /**
     * Get unit average cost
     */
    private static function getUnitAverageCost(Item $item, ?int $unitId): float
    {
        $uVal = $item->units->firstWhere('id', $unitId)?->pivot->u_val ?? 0;

        return $item->average_cost * $uVal;
    }

    /**
     * Get quantity average cost
     */
    private static function getQuantityAverageCost(Item $item, ?int $unitId, ?int $warehouseId, ?float $precomputedBaseQty = null): float
    {
        $quantity = self::getCurrentUnitQuantity($item, $unitId, $warehouseId, $precomputedBaseQty);
        $avgCost = self::getUnitAverageCost($item, $unitId);

        return $quantity * $avgCost;
    }

    /**
     * Get unit sale prices
     */
    private static function getUnitSalePrices(Item $item, ?int $unitId): array
    {
        if (! $unitId) {
            return [];
        }

        return $item->prices
            ->where('pivot.unit_id', $unitId)
            ->mapWithKeys(fn ($priceTypeModel) => [
                $priceTypeModel->id => [
                    'name' => $priceTypeModel->name,
                    'price' => $priceTypeModel->pivot->price,
                ],
            ])->toArray();
    }

    /**
     * Get unit barcodes
     */
    private static function getUnitBarcodes(Item $item, ?int $unitId): array
    {
        return $item->barcodes
            ->where('unit_id', $unitId)
            ->map(fn ($barcode) => [
                'id' => $barcode->id,
                'barcode' => $barcode->barcode,
            ])->toArray();
    }

    /**
     * Get item notes
     */
    private static function getItemNotes(Item $item): array
    {
        return $item->notes
            ->mapWithKeys(function ($note) {
                // Ensure we always return a string, not an array or object
                $noteDetailName = $note->pivot->note_detail_name ?? '';
                // If it's an array or object, convert to string
                if (is_array($noteDetailName) || is_object($noteDetailName)) {
                    $noteDetailName = json_encode($noteDetailName);
                }

                return [$note->id => (string) $noteDetailName];
            })
            ->all();
    }

    /**
     * Get item data for Alpine.js (all units, prices, barcodes)
     */
    public static function getItemDataForAlpine(Item $item, ?int $warehouseId = null, ?float $precomputedBaseQty = null, ?float $precomputedLastPurchasePrice = null): array
    {
        $baseQty = $precomputedBaseQty ?? self::getTotalBaseQuantity($item, $warehouseId);

        // Prepare all units data
        $unitsData = [];
        foreach ($item->units as $unit) {
            $unitsData[$unit->id] = [
                'id' => $unit->id,
                'name' => $unit->name,
                'u_val' => $unit->pivot->u_val ?? 1,
                'cost' => $unit->pivot->cost ?? 0,
            ];
        }

        // Prepare all prices data (grouped by unit_id)
        $pricesData = [];
        foreach ($item->prices as $price) {
            $unitId = $price->pivot->unit_id;
            if (! isset($pricesData[$unitId])) {
                $pricesData[$unitId] = [];
            }
            $pricesData[$unitId][$price->id] = [
                'id' => $price->id,
                'name' => $price->name,
                'price' => $price->pivot->price ?? 0,
                'discount' => $price->pivot->discount ?? 0,
                'tax_rate' => $price->pivot->tax_rate ?? 0,
            ];
        }

        // Prepare all barcodes data (grouped by unit_id)
        $barcodesData = [];
        foreach ($item->barcodes as $barcode) {
            $unitId = $barcode->unit_id;
            if (! isset($barcodesData[$unitId])) {
                $barcodesData[$unitId] = [];
            }
            $barcodesData[$unitId][] = [
                'id' => $barcode->id,
                'barcode' => $barcode->barcode,
            ];
        }

        // Get last purchase price (use precomputed if available)
        $lastPurchasePrice = $precomputedLastPurchasePrice ?? self::getLastPurchasePrice($item->id);

        return [
            'id' => $item->id,
            'code' => $item->code,
            'name' => $item->name,
            'average_cost' => $item->average_cost,
            'last_purchase_price' => $lastPurchasePrice,
            'base_quantity' => $baseQty,
            'units' => $unitsData,
            'prices' => $pricesData,
            'barcodes' => $barcodesData,
            'notes' => self::getItemNotes($item),
            'has_images' => $item->hasMedia('item-thumbnail') || $item->hasMedia('item-images'),
        ];
    }

    /**
     * Get last purchase prices for multiple items (batch loading)
     */
    public static function getLastPurchasePricesForItems(array $itemIds): array
    {
        if (empty($itemIds)) {
            return [];
        }

        $prices = DB::table('operation_items as oi')
            ->join('operhead as oh', 'oi.pro_id', '=', 'oh.id')
            ->whereIn('oi.item_id', $itemIds)
            ->whereIn('oh.pro_type', [11, 13, 15, 17, 20, 24, 25]) // Purchase types
            ->where('oh.isdeleted', 0)
            ->select('oi.item_id', 'oi.item_price', 'oh.pro_date', 'oh.id')
            ->orderBy('oh.pro_date', 'desc')
            ->orderBy('oh.id', 'desc')
            ->get();

        $result = [];
        foreach ($prices as $price) {
            if (! isset($result[$price->item_id])) {
                $result[$price->item_id] = (float) $price->item_price;
            }
        }

        return $result;
    }

    /**
     * Get last purchase price for item
     */
    private static function getLastPurchasePrice(int $itemId): float
    {
        $lastPrice = DB::table('operation_items as oi')
            ->join('operhead as oh', 'oi.pro_id', '=', 'oh.id')
            ->where('oi.item_id', $itemId)
            ->whereIn('oh.pro_type', [11, 13, 15, 17, 20, 24, 25]) // Purchase types
            ->where('oh.isdeleted', 0)
            ->orderBy('oh.pro_date', 'desc')
            ->orderBy('oh.id', 'desc')
            ->value('oi.item_price');

        return (float) ($lastPrice ?? 0);
    }
}
