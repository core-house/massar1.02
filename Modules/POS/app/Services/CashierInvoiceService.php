<?php

declare(strict_types=1);

namespace Modules\POS\app\Services;

use App\Models\OperHead;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Invoices\Services\Invoice\DetailValueCalculator;
use Modules\Invoices\Services\Invoice\DetailValueValidator;
use Modules\Invoices\Services\SaveInvoiceService;
use Modules\POS\app\Models\CashierTransaction;

/**
 * خدمة إنشاء فاتورة الكاشير الكاملة.
 *
 * تُفوّض إنشاء OperHead والقيود المحاسبية وسند القبض بالكامل
 * إلى SaveInvoiceService (type=10) لضمان توحيد المنطق المحاسبي.
 *
 * تُستخدم من:
 *  - POSController::store()            (المسار الأونلاين)
 *  - POSController::syncTransactions() (مسار المزامنة الأوفلاين)
 */
class CashierInvoiceService
{
    public function __construct(
        private readonly SaveInvoiceService $saveInvoiceService = new SaveInvoiceService(
            new DetailValueCalculator(),
            new DetailValueValidator()
        )
    ) {}

    /**
     * إنشاء فاتورة كاشير كاملة.
     *
     * يجب استدعاء هذه الدالة من داخل DB::transaction() في الـ caller.
     *
     * @param  array<string, mixed>  $data      بيانات الفاتورة المُتحقق منها
     * @param  int                   $userId    معرّف المستخدم الحالي
     * @param  int                   $branchId  معرّف الفرع
     * @return array{operhead_id: int, cashier_transaction_id: int, invoice_number: int}
     *
     * @throws \Exception
     */
    public function createFullInvoice(array $data, int $userId, int $branchId): array
    {
        // ── idempotency: لو الفاتورة اتحفظت قبل كده، ارجع بياناتها مباشرة ──
        $localId = $data['local_id'] ?? null;
        if ($localId) {
            $existing = CashierTransaction::where('local_id', $localId)
                ->where('sync_status', 'synced')
                ->first();
            if ($existing) {
                return [
                    'operhead_id'            => $existing->server_id,
                    'cashier_transaction_id' => $existing->id,
                    'invoice_number'         => $existing->pro_id,
                ];
            }
        }

        // ── تحويل بيانات POS إلى شكل SaveInvoiceService ──────────────────
        $mappedData = $this->buildSaveInvoiceData($data, $userId, $branchId);

        // ── تفويض الإنشاء الكامل لـ SaveInvoiceService ───────────────────
        $operheadId = $this->saveInvoiceService->saveInvoice($mappedData);

        if ($operheadId === false || !$operheadId) {
            throw new \Exception('فشل حفظ فاتورة POS عبر SaveInvoiceService');
        }

        $operHead = OperHead::find($operheadId);

        // ── CashierTransaction: ربط POS بالعملية ─────────────────────────
        $cashierTx = CashierTransaction::create([
            'local_id'              => $localId,
            'server_id'             => $operheadId,
            'pro_type_id'           => (int) ($data['invoice_type'] ?? 102),
            'pro_id'                => $operHead?->pro_id,
            'pro_date'              => $data['pro_date'] ?? now()->format('Y-m-d'),
            'accural_date'          => $data['pro_date'] ?? now()->format('Y-m-d'),
            'customer_id'           => isset($data['customer_id']) && $data['customer_id'] !== '' ? (int) $data['customer_id'] : null,
            'store_id'              => isset($data['store_id']) && $data['store_id'] !== '' ? (int) $data['store_id'] : null,
            'cash_account_id'       => isset($data['cash_account_id']) && $data['cash_account_id'] !== '' ? (int) $data['cash_account_id'] : null,
            'employee_id'           => isset($data['employee_id']) && $data['employee_id'] !== '' ? (int) $data['employee_id'] : null,
            'subtotal'              => $mappedData->subtotal,
            'discount'              => $mappedData->discount_value,
            'discount_percentage'   => 0,
            'additional'            => $mappedData->additional_value,
            'additional_percentage' => 0,
            'total'                 => $mappedData->total_after_additional,
            'payment_method'        => $data['payment_method'] ?? 'cash',
            'cash_amount'           => (float) ($data['cash_amount'] ?? 0),
            'card_amount'           => (float) ($data['card_amount'] ?? 0),
            'paid_amount'           => $mappedData->received_from_client,
            'notes'                 => $data['notes'] ?? null,
            'table_id'              => $data['table_id'] ?? null,
            'items'                 => $data['items'],
            'sync_status'           => 'synced',
            'synced_at'             => now(),
            'user_id'               => $userId,
            'branch_id'             => $branchId,
        ]);

        Log::info('POS: تم إنشاء فاتورة كاشير كاملة', [
            'operhead_id'            => $operheadId,
            'invoice_number'         => $operHead?->pro_id,
            'cashier_transaction_id' => $cashierTx->id,
            'local_id'               => $localId,
        ]);

        return [
            'operhead_id'            => $operheadId,
            'cashier_transaction_id' => $cashierTx->id,
            'invoice_number'         => $operHead?->pro_id ?? 0,
        ];
    }

    // =========================================================================
    // Data Mapper: تحويل بيانات POS → شكل SaveInvoiceService
    // =========================================================================

    /**
     * يُحوّل بيانات POS إلى الكائن الذي يتوقعه SaveInvoiceService.
     *
     * @param  array<string, mixed>  $data
     * @param  int                   $userId
     * @param  int                   $branchId
     * @return object
     */
    private function buildSaveInvoiceData(array $data, int $userId, int $branchId): object
    {
        $payMethod  = $data['payment_method'] ?? 'cash';
        $cashAccId  = isset($data['cash_account_id']) && $data['cash_account_id'] !== '' ? (int) $data['cash_account_id'] : null;
        $bankAccId  = isset($data['bank_account_id']) && $data['bank_account_id'] !== '' ? (int) $data['bank_account_id'] : null;

        // تحديد cash_box_id بحسب طريقة الدفع
        $cashBoxId = match ($payMethod) {
            'card'  => $bankAccId,
            default => $cashAccId, // cash أو mixed → الصندوق النقدي
        };

        // حساب المبالغ
        $subtotal   = 0.0;
        foreach ($data['items'] as $item) {
            $subtotal += (float) $item['quantity'] * (float) $item['price'];
        }
        $discount   = (float) ($data['discount'] ?? 0);
        $additional = (float) ($data['additional'] ?? 0);
        $total      = round($subtotal - $discount + $additional, 2);
        $paidAmt    = (float) ($data['cash_amount'] ?? 0) + (float) ($data['card_amount'] ?? 0);

        // رقم الفاتورة التالي (مع lock لتجنب race condition)
        $nextProId = (int) (OperHead::lockForUpdate()->max('pro_id') ?? 0) + 1;

        $proDate = $data['pro_date'] ?? now()->format('Y-m-d');

        // تحويل الأصناف
        $invoiceItems = array_map(fn ($item) => [
            'item_id'   => (int) $item['id'],
            'quantity'  => (float) $item['quantity'],
            'price'     => (float) $item['price'],
            'unit_id'   => isset($item['unit_id']) && $item['unit_id'] !== '' ? (int) $item['unit_id'] : null,
            'discount'  => 0,
            'sub_value' => round((float) $item['quantity'] * (float) $item['price'], 2),
            // حقول مطلوبة لـ DetailValueCalculator
            'item_price'                       => (float) $item['price'],
            'item_discount'                    => 0,
            'additional'                       => 0,
            'item_vat_percentage'              => 0,
            'item_vat_value'                   => 0,
            'item_withholding_tax_percentage'  => 0,
            'item_withholding_tax_value'       => 0,
        ], $data['items']);

        return (object) [
            // نوع الفاتورة: مبيعات
            'type'                       => 10,

            // الحسابات
            'acc1_id'                    => isset($data['customer_id']) && $data['customer_id'] !== '' ? (int) $data['customer_id'] : null,
            'acc2_id'                    => isset($data['store_id']) && $data['store_id'] !== '' ? (int) $data['store_id'] : null,
            'cash_box_id'                => $cashBoxId,
            'emp_id'                     => isset($data['employee_id']) && $data['employee_id'] !== '' ? (int) $data['employee_id'] : null,
            'delivery_id'                => null,

            // التواريخ
            'pro_date'                   => $proDate,
            'accural_date'               => $proDate,

            // رقم الفاتورة
            'pro_id'                     => $nextProId,
            'serial_number'              => null,

            // الأصناف
            'invoiceItems'               => $invoiceItems,

            // المبالغ
            'subtotal'                   => $subtotal,
            'discount_value'             => $discount,
            'discount_percentage'        => 0,
            'additional_value'           => $additional,
            'additional_percentage'      => 0,
            'total_after_additional'     => $total,
            'received_from_client'       => $paidAmt,

            // الضرائب (لا ضرائب في POS الأساسي)
            'vat_percentage'             => 0,
            'vat_value'                  => 0,
            'withholding_tax_percentage' => 0,
            'withholding_tax_value'      => 0,

            // العملة
            'currency_id'                => null,
            'currency_rate'              => 1,

            // بيانات إضافية
            'notes'                      => $data['notes'] ?? null,
            'payment_notes'              => null,
            'branch_id'                  => $branchId,
            'currentBalance'             => 0,
            'balanceAfterInvoice'        => 0,
            'selectedPriceType'          => null,
            'template_id'                => null,
            'op2'                        => null,
            'status'                     => 0,
        ];
    }
}
