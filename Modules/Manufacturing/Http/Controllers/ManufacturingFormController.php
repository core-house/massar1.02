<?php

declare(strict_types=1);

namespace Modules\Manufacturing\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Modules\Manufacturing\Services\ManufacturingDataPreparationService;
use Modules\Manufacturing\Repositories\ManufacturingDataRepository;

class ManufacturingFormController extends Controller
{
    public function __construct(
        private ManufacturingDataPreparationService $dataService,
        private ManufacturingDataRepository $dataRepository
    ) {
        // Removed middleware - handled in routes
    }

    /**
     * Show create form
     */
    public function create(Request $request)
    {
        $data = $this->dataService->prepareCreateFormData(
            orderId: $request->query('order_id'),
            stageId: $request->query('stage_id')
        );
        
        return view('manufacturing::create', $data);
    }

    /**
     * Show edit form
     */
    public function edit(int $id)
    {
        $data = $this->dataService->prepareEditFormData($id);
        
        return view('manufacturing::edit', $data);
    }

    /**
     * Get initial data for create form (AJAX)
     */
    public function getInitialData(Request $request): JsonResponse
    {
        $data = $this->dataService->prepareCreateFormData(
            orderId: $request->query('order_id'),
            stageId: $request->query('stage_id')
        );
        
        return response()->json($data);
    }

    /**
     * Get edit data (AJAX)
     */
    public function getEditData(int $id): JsonResponse
    {
        $data = $this->dataService->prepareEditFormData($id);
        
        return response()->json($data);
    }

    /**
     * Get all items for client-side search (AJAX)
     */
    public function getAllItems(Request $request): JsonResponse
    {
        try {
            $branchId = $request->input('branch_id');
            
            $items = $this->dataRepository->getAllItemsLite($branchId ? (int) $branchId : null);
            
            return response()->json([
                'success' => true,
                'items' => $items,
                'count' => count($items)
            ]);
        } catch (\Exception $e) {
            \Log::error('Get all items error', [
                'message' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'error' => 'Failed to load items',
                'message' => $e->getMessage(),
                'breadcrumb_items' => []
            ], 500);
        }
    }

    /**
     * Search products (AJAX)
     */
    public function searchProducts(Request $request): JsonResponse
    {
        try {
            $searchTerm = $request->input('search', '');
            $limit = $request->input('limit', 50);
            
            $products = $this->dataRepository->searchProducts($searchTerm, (int) $limit);
            
            return response()->json([
                'success' => true,
                'items' => $products,
                'count' => count($products)
            ]);
        } catch (\Exception $e) {
            \Log::error('Search products error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'error' => 'Search failed',
                'message' => $e->getMessage(),
                'breadcrumb_items' => []
            ], 500);
        }
    }

    /**
     * Search raw materials (AJAX)
     */
    public function searchRawMaterials(Request $request): JsonResponse
    {
        try {
            $searchTerm = $request->input('search', '');
            $storeId = $request->input('store_id');
            $limit = $request->input('limit', 50);
            
            $materials = $this->dataRepository->searchRawMaterials($searchTerm, $storeId ? (int) $storeId : null, (int) $limit);
            
            return response()->json([
                'success' => true,
                'items' => $materials,
                'count' => count($materials)
            ]);
        } catch (\Exception $e) {
            \Log::error('Search raw materials error', [
                'message' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'error' => 'Search failed',
                'message' => $e->getMessage(),
                'breadcrumb_items' => []
            ], 500);
        }
    }

    /**
     * Get item with units (AJAX)
     */
    public function getItemWithUnits(int $id): JsonResponse
    {
        $item = $this->dataRepository->getItemWithUnits($id);
        
        if (!$item) {
            return response()->json(['error' => 'Item not found'], 404);
        }
        
        return response()->json($item);
    }

    /**
     * Get available stock for item (AJAX)
     */
    public function getAvailableStock(Request $request): JsonResponse
    {
        $itemId = $request->input('item_id');
        $storeId = $request->input('store_id');
        $unitId = $request->input('unit_id');
        
        $stock = $this->dataRepository->getAvailableStock($itemId, $storeId, $unitId);
        
        return response()->json(['available_stock' => $stock]);
    }

    /**
     * Check duplicate invoice number (AJAX)
     */
    public function checkDuplicateInvoice(Request $request): JsonResponse
    {
        $proId = $request->input('pro_id');
        $branchId = $request->input('branch_id');

        if (!$proId || !$branchId) {
            return response()->json(['exists' => false]);
        }

        $exists = \App\Models\OperHead::where('pro_type', 59)
            ->where('pro_id', $proId)
            ->where('branch_id', $branchId)
            ->exists();

        return response()->json(['exists' => $exists]);
    }

    /**
     * Check MO quantity (AJAX)
     */
    public function checkMOQuantity(Request $request): JsonResponse
    {
        $orderId = $request->input('order_id');
        $stageId = $request->input('stage_id');

        if (!$orderId) {
            return response()->json([
                'success' => false,
                'message' => 'Order ID is required'
            ]);
        }

        try {
            $order = \Modules\Manufacturing\Models\ManufacturingOrder::find($orderId);
            
            if (!$order) {
                return response()->json([
                    'success' => false,
                    'message' => 'Manufacturing order not found'
                ]);
            }

            // Get total produced quantity from existing invoices
            $producedQuantity = \App\Models\OperHead::where('pro_type', 59)
                ->where('order_id', $orderId)
                ->join('operation_items', 'operhead.id', '=', 'operation_items.pro_id')
                ->where('operation_items.item_id', $order->item_id)
                ->sum('operation_items.qty_in');

            $remainingQuantity = $order->quantity - $producedQuantity;

            return response()->json([
                'success' => true,
                'target_item_id' => $order->item_id,
                'target_quantity' => $order->quantity,
                'produced_quantity' => $producedQuantity,
                'remaining_quantity' => max(0, $remainingQuantity)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Check BOM exists and is active (AJAX)
     */
    public function checkBOM(Request $request): JsonResponse
    {
        $itemId = $request->input('item_id');

        if (!$itemId) {
            return response()->json([
                'has_bom' => false,
                'is_active' => false
            ]);
        }

        // Check if BOM exists (manufacturing invoice with this item as product)
        $hasBOM = \App\Models\OperHead::where('pro_type', 59)
            ->where('is_template', 1) // Templates are BOMs
            ->whereHas('operationItems', function($q) use ($itemId) {
                $q->where('item_id', $itemId)
                  ->where('qty_in', '>', 0); // Product (output)
            })
            ->exists();

        // For now, assume all BOMs are active
        // You can add an 'is_active' field to templates if needed
        $isActive = $hasBOM;

        return response()->json([
            'has_bom' => $hasBOM,
            'is_active' => $isActive
        ]);
    }

    /**
     * Validate accounts (AJAX)
     */
    public function validateAccounts(Request $request): JsonResponse
    {
        $acc1 = $request->input('acc1');
        $acc2 = $request->input('acc2');
        $operating = $request->input('operating');

        try {
            $acc1Account = \Modules\Accounts\Models\AccHead::find($acc1);
            $acc2Account = \Modules\Accounts\Models\AccHead::find($acc2);
            $operatingAccount = $operating ? \Modules\Accounts\Models\AccHead::find($operating) : null;

            if (!$acc1Account || !$acc2Account) {
                return response()->json([
                    'valid' => false,
                    'message' => 'One or more accounts not found'
                ]);
            }

            // Check if accounts are inventory accounts (code starts with 1104)
            $acc1IsInventory = str_starts_with($acc1Account->code, '1104');
            $acc2IsInventory = str_starts_with($acc2Account->code, '1104');

            return response()->json([
                'valid' => true,
                'acc1_is_inventory' => $acc1IsInventory,
                'acc2_is_inventory' => $acc2IsInventory,
                'operating_exists' => $operatingAccount !== null
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'valid' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Get BOM for item (AJAX)
     */
    public function getBOM(Request $request): JsonResponse
    {
        $itemId = $request->input('item_id');

        if (!$itemId) {
            return response()->json([
                'has_bom' => false,
                'bom_items' => []
            ]);
        }

        try {
            // Get BOM from template (manufacturing invoice marked as template)
            $bomInvoice = \App\Models\OperHead::where('pro_type', 59)
                ->where('is_template', 1)
                ->whereHas('operationItems', function($q) use ($itemId) {
                    $q->where('item_id', $itemId)
                      ->where('qty_in', '>', 0); // Product (output)
                })
                ->with(['operationItems.item'])
                ->first();

            if (!$bomInvoice) {
                return response()->json([
                    'has_bom' => false,
                    'bom_items' => []
                ]);
            }

            // Get raw materials (inputs) from BOM
            $bomItems = $bomInvoice->operationItems()
                ->where('qty_out', '>', 0) // Raw materials (inputs)
                ->with('item')
                ->get()
                ->map(function($item) {
                    return [
                        'item_id' => $item->item_id,
                        'item_name' => $item->item->name ?? '',
                        'quantity' => $item->qty_out, // Base unit quantity
                        'unit_id' => $item->unit_id,
                    ];
                });

            return response()->json([
                'has_bom' => true,
                'bom_items' => $bomItems
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'has_bom' => false,
                'bom_items' => [],
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Check accounting period (AJAX)
     */
    public function checkAccountingPeriod(Request $request): JsonResponse
    {
        $date = $request->input('date');

        if (!$date) {
            return response()->json(['is_open' => true]);
        }

        try {
            // Check if there's a closed period that includes this date
            // This is a simplified check - you may need to adjust based on your accounting period structure
            
            // For now, we'll check if the date is not in the past beyond a certain threshold
            // You can implement proper accounting period checks based on your system
            
            $invoiceDate = \Carbon\Carbon::parse($date);
            $currentDate = \Carbon\Carbon::now();
            
            // Example: Don't allow invoices older than 3 months
            $thresholdDate = $currentDate->copy()->subMonths(3);
            
            if ($invoiceDate->lt($thresholdDate)) {
                return response()->json([
                    'is_open' => false,
                    'message' => 'Cannot create invoices older than 3 months'
                ]);
            }

            // Check if date is in the future (more than 1 day)
            if ($invoiceDate->gt($currentDate->copy()->addDay())) {
                return response()->json([
                    'is_open' => false,
                    'message' => 'Cannot create invoices with future dates'
                ]);
            }

            return response()->json(['is_open' => true]);
        } catch (\Exception $e) {
            return response()->json([
                'is_open' => true, // Allow if check fails
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Get tolerance setting (AJAX)
     */
    public function getToleranceSetting(Request $request): JsonResponse
    {
        try {
            // Get tolerance percentage from settings
            // Default to 10% if not set
            $tolerance = setting('manufacturing_consumption_tolerance', 10);
            
            return response()->json([
                'tolerance_percentage' => floatval($tolerance)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'tolerance_percentage' => 10 // Default
            ]);
        }
    }
}
