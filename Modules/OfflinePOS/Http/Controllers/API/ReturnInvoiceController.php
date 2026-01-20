<?php

namespace Modules\OfflinePOS\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Modules\OfflinePOS\Services\TransactionProcessorService;

/**
 * API Controller لفواتير المرتجعات
 */
class ReturnInvoiceController extends Controller
{
    protected TransactionProcessorService $transactionProcessor;

    public function __construct(TransactionProcessorService $transactionProcessor)
    {
        $this->transactionProcessor = $transactionProcessor;
    }

    /**
     * إنشاء فاتورة مرتجعة
     */
    public function create(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'original_invoice_id' => 'required|integer|exists:oper_heads,id',
            'return_items' => 'required|array|min:1',
            'return_items.*.item_id' => 'required|integer',
            'return_items.*.quantity' => 'required|numeric|min:0',
            'return_items.*.price' => 'required|numeric|min:0',
            'return_items.*.reason' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            if (!Auth::user()->can('create offline pos return invoice')) {
                return response()->json([
                    'success' => false,
                    'message' => 'You do not have permission to create return invoices.',
                ], 403);
            }

            $branchId = $request->input('current_branch_id');

            // تحضير بيانات المرتجع
            $returnData = $this->prepareReturnData($request->all());

            // معالجة المرتجع
            $result = $this->transactionProcessor->processTransaction($returnData, $branchId);

            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'message' => 'Return invoice created successfully.',
                    'data' => [
                        'return_invoice_id' => $result['transaction_id'],
                        'invoice_number' => $result['invoice_number'],
                    ],
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => $result['error'] ?? 'Failed to create return invoice.',
                ], 400);
            }

        } catch (\Exception $e) {
            Log::error('ReturnInvoice API Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to create return invoice.',
                'error' => app()->environment('local') ? $e->getMessage() : 'Internal server error',
            ], 500);
        }
    }

    /**
     * تحضير بيانات المرتجع
     */
    protected function prepareReturnData(array $data): array
    {
        // جلب الفاتورة الأصلية
        $originalInvoice = \App\Models\OperHead::findOrFail($data['original_invoice_id']);

        // حساب الإجمالي
        $total = collect($data['return_items'])->sum(function ($item) {
            return $item['quantity'] * $item['price'];
        });

        return [
            'transaction_type' => 'return',
            'customer_id' => $originalInvoice->acc1,
            'store_id' => $originalInvoice->acc2,
            'employee_id' => $originalInvoice->emp_id,
            'items' => $data['return_items'],
            'subtotal' => $total,
            'total' => $total,
            'paid_amount' => 0,
            'notes' => 'Return for invoice #' . $originalInvoice->pro_id . ' - ' . ($data['notes'] ?? ''),
            'date' => now()->format('Y-m-d'),
        ];
    }
}
