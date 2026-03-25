<?php

declare(strict_types=1);

namespace Modules\Invoices\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Invoices\Services\InvoiceDataPreparationService;

/**
 * API Controller for invoice data operations
 */
class InvoiceDataApiController extends Controller
{
    public function __construct(
        private readonly InvoiceDataPreparationService $dataPreparationService
    ) {}

    /**
     * Get initial data for invoice form
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getInitialData(Request $request): JsonResponse
    {
        $type = (int) $request->query('type');
        $branchId = $request->query('branch_id') ? (int) $request->query('branch_id') : null;

        $result = $this->dataPreparationService->prepareInitialData($type, $branchId);

        return response()->json($result);
    }

    /**
     * Get invoice data for editing
     *
     * @param int $invoiceId
     * @return JsonResponse
     */
    public function getInvoiceForEdit(int $invoiceId): JsonResponse
    {
        $result = $this->dataPreparationService->prepareInvoiceForEdit($invoiceId);

        if (!$result['success']) {
            return response()->json($result, 404);
        }

        return response()->json($result);
    }

    /**
     * Get invoice data by pro_id (for workflow)
     *
     * @param int $proId
     * @return JsonResponse
     */
    public function getInvoiceByProId(int $proId): JsonResponse
    {
        // Find invoice by pro_id
        $invoice = \App\Models\OperHead::where('pro_id', $proId)
            ->with(['operationItems.item.units', 'operationItems.unit'])
            ->first();

        if (!$invoice) {
            return response()->json(['success' => false, 'message' => 'Invoice not found'], 404);
        }

        // Prepare items data
        $items = $invoice->operationItems->map(function ($item) {
            // ✅ Use fat_quantity (display quantity) instead of base quantity
            // fat_quantity is already in the selected unit
            $quantity = (float) ($item->fat_quantity ?? 0);
            
            // Get unit factor
            $unitFactor = 1;
            if ($item->unit_id && $item->item && $item->item->units) {
                $unit = $item->item->units->firstWhere('id', $item->unit_id);
                if ($unit && $unit->pivot) {
                    $unitFactor = (float) $unit->pivot->u_val;
                }
            }
            
            // If fat_quantity is 0, fallback to calculating from base quantity
            if ($quantity == 0) {
                $baseQuantity = $item->qty_in > 0 ? $item->qty_in : $item->qty_out;
                $quantity = $baseQuantity / $unitFactor;
            }
            
            // ✅ Use fat_price (display price) instead of calculating from base price
            // fat_price is already in the selected unit
            $unitPrice = (float) ($item->fat_price ?? $item->item_price * $unitFactor);
            
            // ✅ Calculate sub_value WITHOUT invoice-level discount/additional
            // sub_value should be: (price × quantity) - item_discount + item_additional
            $itemSubValue = ($unitPrice * abs($quantity)) - (float) $item->item_discount + (float) ($item->additional ?? 0);
            
            return [
                'item_id' => $item->item_id,
                'item_name' => $item->item->name ?? '',
                'item_code' => $item->item->code ?? '',
                'unit_id' => $item->unit_id,
                'unit_name' => $item->unit->name ?? '',
                'quantity' => abs($quantity), // ✅ Use fat_quantity (display quantity)
                'price' => $unitPrice, // ✅ Use fat_price (display price)
                'discount_percentage' => (float) $item->item_discount_pre,
                'discount_value' => (float) $item->item_discount,
                'sub_value' => (float) $itemSubValue, // ✅ Use calculated sub_value without invoice-level adjustments
                'batch_number' => $item->batch_number ?? '',
                'expiry_date' => $item->expiry_date ?? '',
                'notes' => $item->notes ?? '',
                'units' => $item->item->units ?? [],
                'available_stock' => $item->item->available_stock ?? 0,
            ];
        });

        return response()->json([
            'success' => true,
            'items' => $items,
            'invoice' => [
                'id' => $invoice->id,
                'pro_id' => $invoice->pro_id,
                'pro_serial' => $invoice->pro_serial,
                'pro_type' => $invoice->pro_type,
                'pro_date' => $invoice->pro_date,
                'accural_date' => $invoice->accural_date,
                'acc1' => $invoice->acc1,
                'acc2' => $invoice->acc2,
                'emp_id' => $invoice->emp_id,
                'emp2_id' => $invoice->emp2_id,
                'template_id' => $invoice->template_id,
                'discount_percentage' => (float) ($invoice->fat_disc_per ?? 0),
                'discount_value' => (float) ($invoice->fat_disc ?? 0),
                'additional_percentage' => (float) ($invoice->fat_plus_per ?? 0),
                'additional_value' => (float) ($invoice->fat_plus ?? 0),
                'received_from_client' => (float) ($invoice->paid_from_client ?? 0),
                'details' => $invoice->details ?? '',
                'payment_notes' => $invoice->info2 ?? '',
            ]
        ]);
    }
}
