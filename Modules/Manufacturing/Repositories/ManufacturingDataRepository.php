<?php

declare(strict_types=1);

namespace Modules\Manufacturing\Repositories;

use App\Models\Item;
use Modules\Accounts\Models\AccHead;
use Illuminate\Support\Collection;

class ManufacturingDataRepository
{
    /**
     * Get all items in lite format (for client-side search)
     */
    public function getAllItemsLite(?int $branchId = null): array
    {
        $query = \DB::table('items')
            ->select([
                'items.id',
                'items.name',
                'items.code',
                'items.type',
                'items.average_cost',
            ])
            ->where('items.isdeleted', 0)
            ->where('items.is_active', 1);

        // Add branch filter if provided
        if ($branchId) {
            $query->where(function ($q) use ($branchId) {
                $q->where('items.branch_id', $branchId)
                    ->orWhereNull('items.branch_id');
            });
        }

        $items = $query->limit(8000)->get()->toArray();

        // Get all item IDs for bulk queries
        $itemIds = array_map(fn ($item) => $item->id, $items);

        // Get units for each item
        $unitsByItem = [];
        if (!empty($itemIds)) {
            $unitsQuery = \DB::table('item_units')
                ->join('units', 'units.id', '=', 'item_units.unit_id')
                ->whereIn('item_units.item_id', $itemIds)
                ->select([
                    'item_units.item_id',
                    'units.id',
                    'units.name',
                    'item_units.u_val',
                    'item_units.cost',
                ])
                ->get();

            foreach ($unitsQuery as $unit) {
                if (!isset($unitsByItem[$unit->item_id])) {
                    $unitsByItem[$unit->item_id] = [];
                }
                $unitsByItem[$unit->item_id][] = [
                    'id' => $unit->id,
                    'name' => $unit->name,
                    'u_val' => (float) ($unit->u_val ?? 1),
                    'cost' => (float) ($unit->cost ?? 0),
                ];
            }
        }

        // Get barcodes for each item
        $barcodesByItem = [];
        if (!empty($itemIds)) {
            $barcodesQuery = \DB::table('barcodes')
                ->whereIn('item_id', $itemIds)
                ->select('item_id', 'barcode')
                ->get();

            foreach ($barcodesQuery as $barcode) {
                if (!isset($barcodesByItem[$barcode->item_id])) {
                    $barcodesByItem[$barcode->item_id] = [];
                }
                $barcodesByItem[$barcode->item_id][] = $barcode->barcode;
            }
        }

        // Get last purchase prices for all items
        $lastPurchasePrices = [];
        if (!empty($itemIds)) {
            $purchasePricesQuery = \DB::table('operation_items as oi')
                ->join('operhead as oh', 'oi.pro_id', '=', 'oh.id')
                ->whereIn('oi.item_id', $itemIds)
                ->whereIn('oh.pro_type', [11, 13, 15, 17, 20, 24, 25]) // Purchase types
                ->where('oh.isdeleted', 0)
                ->select('oi.item_id', 'oi.item_price', 'oh.pro_date', 'oh.id as operation_id')
                ->orderBy('oh.pro_date', 'desc')
                ->orderBy('oh.id', 'desc')
                ->get();

            // Group by item_id and get first (latest) price for each item
            foreach ($purchasePricesQuery as $row) {
                if (!isset($lastPurchasePrices[$row->item_id])) {
                    $lastPurchasePrices[$row->item_id] = (float) $row->item_price;
                }
            }
        }

        // Get total stock quantity for each item
        $totalStockByItem = [];
        if (!empty($itemIds)) {
            $totalStocksQuery = \DB::table('operation_items as oi')
                ->join('operhead as oh', 'oi.pro_id', '=', 'oh.id')
                ->whereIn('oi.item_id', $itemIds)
                ->where('oh.isdeleted', 0)
                ->when($branchId, fn ($q) => $q->where('oh.branch_id', $branchId))
                ->groupBy('oi.item_id')
                ->selectRaw('oi.item_id, SUM(oi.qty_in - oi.qty_out) as total_stock')
                ->get();

            foreach ($totalStocksQuery as $stock) {
                $totalStockByItem[$stock->item_id] = (float) ($stock->total_stock ?? 0);
            }
        }

        // Get warehouse stocks for each item
        $warehouseStocksByItem = [];
        if (!empty($itemIds)) {
            $stocksQuery = \DB::table('operation_items as oi')
                ->join('operhead as oh', 'oi.pro_id', '=', 'oh.id')
                ->whereIn('oi.item_id', $itemIds)
                ->where('oh.isdeleted', 0)
                ->when($branchId, fn ($q) => $q->where('oh.branch_id', $branchId))
                ->whereNotNull('oh.acc2')
                ->groupBy('oi.item_id', 'oh.acc2')
                ->selectRaw('oi.item_id, oh.acc2 as warehouse_id, SUM(oi.qty_in - oi.qty_out) as stock')
                ->get();

            foreach ($stocksQuery as $stock) {
                if (!isset($warehouseStocksByItem[$stock->item_id])) {
                    $warehouseStocksByItem[$stock->item_id] = [];
                }
                $warehouseStocksByItem[$stock->item_id][$stock->warehouse_id] = (float) ($stock->stock ?? 0);
            }
        }

        // Build result
        $result = [];
        foreach ($items as $item) {
            $item = (array) $item;

            $result[] = [
                'id' => $item['id'],
                'name' => $item['name'],
                'code' => $item['code'] ?? '',
                'type' => $item['type'] ?? 1,
                'barcode' => $barcodesByItem[$item['id']] ?? [],
                'average_cost' => (float) ($item['average_cost'] ?? 0),
                'last_purchase_price' => $lastPurchasePrices[$item['id']] ?? 0,
                'units' => $unitsByItem[$item['id']] ?? [],
                'warehouse_stocks' => $warehouseStocksByItem[$item['id']] ?? [],
                'stock_quantity' => $totalStockByItem[$item['id']] ?? 0,
            ];
        }

        return $result;
    }

    /**
     * Get accounts by code pattern
     */
    public function getAccountsByCode(string $codePattern): array
    {
        return AccHead::where('code', 'like', $codePattern)
            ->pluck('aname', 'id')
            ->toArray();
    }

    /**
     * Search products (all inventory items)
     */
    public function searchProducts(string $searchTerm, int $limit = 50): array
    {
        if (strlen($searchTerm) < 2) {
            return [];
        }

        $query = Item::query()
            ->where('isdeleted', 0)
            ->where('is_active', 1)
            ->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                    ->orWhere('code', 'like', "%{$searchTerm}%")
                    ->orWhereHas('barcodes', function ($bq) use ($searchTerm) {
                        $bq->where('barcode', 'like', "%{$searchTerm}%");
                    });
            })
            ->where('type', 1) // Inventory items only
            ->select('id', 'name', 'code', 'average_cost')
            ->with(['units' => function($q) {
                $q->select('units.id', 'units.name')->withPivot('u_val');
            }])
            ->limit($limit);

        return $query->get()->map(function ($item) {
            return [
                'id' => $item->id,
                'name' => $item->name,
                'code' => $item->code ?? '',
            ];
        })->toArray();
    }

    /**
     * Search raw materials (all inventory items - same as products for now)
     */
    public function searchRawMaterials(string $searchTerm, ?int $storeId = null, int $limit = 50): array
    {
        if (strlen($searchTerm) < 2) {
            return [];
        }

        $query = Item::query()
            ->where('isdeleted', 0)
            ->where('is_active', 1)
            ->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                    ->orWhere('code', 'like', "%{$searchTerm}%")
                    ->orWhereHas('barcodes', function ($bq) use ($searchTerm) {
                        $bq->where('barcode', 'like', "%{$searchTerm}%");
                    });
            })
            ->where('type', 1) // Inventory items only
            ->select('id', 'name', 'code', 'average_cost')
            ->with(['units' => function($q) {
                $q->select('units.id', 'units.name')->withPivot('u_val');
            }])
            ->limit($limit);

        if ($storeId) {
            // Add store filter if needed
            // $query->whereHas('stock', fn($q) => $q->where('store_id', $storeId));
        }

        return $query->get()->map(function ($item) {
            return [
                'id' => $item->id,
                'name' => $item->name,
                'code' => $item->code ?? '',
            ];
        })->toArray();
    }

    /**
     * Get item with units
     */
    public function getItemWithUnits(int $itemId): ?array
    {
        $item = Item::with(['units' => function ($query) {
            $query->orderBy('pivot_u_val');
        }])->find($itemId);

        if (!$item) {
            return null;
        }

        $units = $item->units->map(function ($unit) {
            return [
                'id' => $unit->id,
                'name' => $unit->name,
                'u_val' => $unit->pivot->u_val ?? 1,
            ];
        });

        return [
            'id' => $item->id,
            'name' => $item->name,
            'item_type' => $item->item_type,
            'units' => $units,
            'average_cost' => $item->average_cost ?? 0,
        ];
    }

    /**
     * Get available stock for item
     */
    public function getAvailableStock(int $itemId, int $storeId, ?int $unitId = null): float
    {
        // This is a simplified version
        // You need to implement actual stock calculation based on your system
        
        $item = Item::find($itemId);
        if (!$item) {
            return 0;
        }

        // Get stock from your stock table
        // This is placeholder - adjust based on your actual stock system
        $stock = \DB::table('operation_items')
            ->where('item_id', $itemId)
            ->where('acc_id', $storeId)
            ->selectRaw('SUM(qty_in - qty_out) as available')
            ->value('available') ?? 0;

        return (float) $stock;
    }

    /**
     * Get item average cost
     */
    public function getItemAverageCost(int $itemId, int $storeId): float
    {
        $item = Item::find($itemId);
        return $item?->average_cost ?? 0;
    }
}
