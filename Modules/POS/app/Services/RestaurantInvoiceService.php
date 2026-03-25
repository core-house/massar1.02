<?php

declare(strict_types=1);

namespace Modules\POS\app\Services;

use App\Models\Item;
use App\Models\JournalDetail;
use App\Models\JournalHead;
use App\Models\OperationItems;
use App\Models\OperHead;
use App\Models\Setting;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Manufacturing\Models\ManufacturingOrder;
use Modules\POS\app\Models\CashierTransaction;

class RestaurantInvoiceService
{
    private ?object $settings = null;

    public function __construct(
        private readonly FreeManufacturingInvoiceService $manufacturingService = new FreeManufacturingInvoiceService
    ) {}

    /**
     * الحساب الرئيسي للعملية.
     * يُنشئ فواتير التصنيع الحر (إن وُجدت) ثم OperHead + OperationItems + القيود المحاسبية.
     * كل العمليات داخل transaction واحدة لضمان الذرية.
     *
     * @param  array<string, mixed>  $data  البيانات المُتحقق منها من الـ Request
     * @return array{operhead_id: int, invoice_number: int, cashier_transaction_id: int, manufacturing_invoice_ids: int[]}
     *
     * @throws \Exception
     */
    public function save(array $data): array
    {
        return DB::transaction(function () use ($data): array {
            return $this->doSave($data);
        });
    }

    /**
     * المنطق الداخلي للحفظ (يُستدعى داخل transaction).
     *
     * @param  array<string, mixed>  $data
     * @return array{operhead_id: int, invoice_number: int, cashier_transaction_id: int, manufacturing_invoice_ids: int[]}
     */
    private function doSave(array $data): array
    {
        // ── idempotency: لو الفاتورة اتحفظت قبل كده، ارجع بياناتها مباشرة ──
        $localId = $data['local_id'] ?? null;
        if ($localId) {
            $existing = CashierTransaction::where('local_id', $localId)->first();
            if ($existing) {
                return [
                    'operhead_id'               => $existing->server_id,
                    'invoice_number'            => $existing->pro_id,
                    'cashier_transaction_id'    => $existing->id,
                    'manufacturing_invoice_ids' => [],
                ];
            }
        }

        $this->settings = Setting::first();

        $branchId = Auth::user()->branch_id ?? 1;
        $userId = Auth::id();
        $customerId = $data['customer_id'] ?? null;
        $storeId = $data['store_id'] ?? 1;
        $cashAccId = isset($data['cash_account_id']) && $data['cash_account_id'] !== '' ? (int) $data['cash_account_id'] : null;
        $bankAccId = isset($data['bank_account_id']) && $data['bank_account_id'] !== '' ? (int) $data['bank_account_id'] : null;
        $employeeId = $data['employee_id'] ?? null;
        $notes = $data['notes'] ?? 'فاتورة مطعم';
        $payMethod = $data['payment_method'] ?? 'cash';
        $cashAmt = (float) ($data['cash_amount'] ?? 0);
        $cardAmt = (float) ($data['card_amount'] ?? 0);
        $paidAmt = $cashAmt + $cardAmt;

        // ── فواتير التصنيع الحر (قبل فاتورة المطعم) ─────────────────────
        $manufacturingInvoiceIds = $this->createManufacturingInvoicesForItems(
            $data['items'],
            isset($storeId) ? (int) $storeId : null,
            $branchId,
            $userId
        );

        // ── حساب الإجماليات ──────────────────────────────────────────────
        $subtotal = 0.0;
        foreach ($data['items'] as $item) {
            $subtotal += (float) $item['quantity'] * (float) $item['price'];
        }
        $total = $subtotal;

        // ── رقم الفاتورة التالي ──────────────────────────────────────────
        $nextProId = (int) (OperHead::max('pro_id') ?? 0) + 1;

        // ── إنشاء رأس العملية ────────────────────────────────────────────
        $operHead = OperHead::create([
            'pro_id' => $nextProId,
            'pro_date' => now()->format('Y-m-d'),
            'accural_date' => now()->format('Y-m-d'),
            'pro_type' => 103,
            'acc1' => $customerId,
            'acc2' => $storeId,
            'store_id' => $storeId,
            'emp_id' => $employeeId,
            'fat_total' => $subtotal,
            'fat_disc' => 0,
            'fat_disc_per' => 0,
            'fat_plus' => 0,
            'fat_plus_per' => 0,
            'fat_net' => $total,
            'pro_value' => $total,
            'paid_from_client' => $paidAmt,
            'info' => $notes,
            'details' => $notes,
            'isdeleted' => 0,
            'is_stock' => 1,
            'is_finance' => 1,
            'is_journal' => 1,
            'journal_type' => 2,
            'user' => Auth::id(),
            'branch_id' => $branchId,
            'order_type' => $data['order_type'] ?? null,
            'table_id' => $data['table_id'] ?? null,
            'driver_id' => $data['driver_id'] ?? null,
            'contact_id' => $data['contact_id'] ?? $customerId,
            'price_group_id' => $data['price_group_id'] ?? null,
            'delivery_fee' => (float) ($data['delivery_fee'] ?? 0),
        ]);

        // ── إنشاء تفاصيل الأصناف ─────────────────────────────────────────
        foreach ($data['items'] as $item) {
            $itemModel = Item::find($item['id']);
            $unitId = $item['unit_id'] ?? $itemModel?->units()->first()?->id;
            $qty = (float) $item['quantity'];
            $price = (float) $item['price'];

            OperationItems::create([
                'pro_tybe' => 103,
                'detail_store' => $storeId,
                'pro_id' => $operHead->id,
                'item_id' => $item['id'],
                'unit_id' => $unitId,
                'qty_in' => 0,
                'qty_out' => $qty,
                'item_price' => $price,
                'cost_price' => (float) ($itemModel?->average_cost ?? 0),
                'current_stock_value' => 0,
                'item_discount' => 0,
                'additional' => 0,
                'detail_value' => $qty * $price,
                'profit' => 0,
                'is_stock' => 1,
                'isdeleted' => 0,
                'branch_id' => $branchId,
            ]);
        }

        // ── القيود المحاسبية ──────────────────────────────────────────────
        $this->createJournals($data, $operHead, $branchId, $customerId, $cashAccId, $bankAccId, $total, $cashAmt, $cardAmt, $paidAmt, $payMethod, $nextProId);

        // ── CashierTransaction ────────────────────────────────────────────
        $cashierTx = CashierTransaction::create([
            'local_id' => $data['local_id'] ?? null,
            'server_id' => $operHead->id,
            'pro_type_id' => 103,
            'pro_id' => $nextProId,
            'pro_date' => now()->format('Y-m-d'),
            'accural_date' => now()->format('Y-m-d'),
            'customer_id' => $customerId,
            'store_id' => $storeId,
            'cash_account_id' => $cashAccId,
            'employee_id' => $employeeId,
            'subtotal' => $subtotal,
            'discount' => 0,
            'discount_percentage' => 0,
            'additional' => 0,
            'additional_percentage' => 0,
            'total' => $total,
            'payment_method' => $payMethod,
            'cash_amount' => $cashAmt,
            'card_amount' => $cardAmt,
            'paid_amount' => $paidAmt,
            'notes' => $notes,
            'table_id' => $data['table_id'] ?? null,
            'items' => $data['items'],
            'sync_status' => 'synced',
            'synced_at' => now(),
            'user_id' => Auth::id(),
            'branch_id' => $branchId,
        ]);

        return [
            'operhead_id' => $operHead->id,
            'invoice_number' => $nextProId,
            'cashier_transaction_id' => $cashierTx->id,
            'manufacturing_invoice_ids' => $manufacturingInvoiceIds,
        ];
    }

    // =========================================================================
    // فواتير التصنيع الحر
    // =========================================================================

    /**
     * ينشئ فواتير التصنيع الحر لكل صنف له نموذج تصنيع.
     *
     * @param  array<int, array<string, mixed>>  $items
     * @return int[] معرفات فواتير التصنيع المنشأة
     */
    private function createManufacturingInvoicesForItems(
        array $items,
        ?int $storeId,
        int $branchId,
        int $userId
    ): array {
        $ids = [];

        foreach ($items as $itemData) {
            $itemId = (int) $itemData['id'];
            $quantity = (float) $itemData['quantity'];

            $template = ManufacturingOrder::where('item_id', $itemId)
                ->where('is_template', 1)
                ->first();

            if (! $template) {
                continue;
            }

            $invoice = $this->manufacturingService->create(
                $template,
                $quantity,
                $storeId,
                $branchId,
                $userId
            );

            $ids[] = $invoice->id;

            Log::info('POS: تم إنشاء فاتورة تصنيع حر', [
                'manufacturing_invoice_id' => $invoice->id,
                'item_id' => $itemId,
                'quantity' => $quantity,
                'template_id' => $template->id,
            ]);
        }

        return $ids;
    }

    // =========================================================================
    // القيود المحاسبية
    // =========================================================================

    /**
     * ينشئ جميع القيود المحاسبية للفاتورة.
     */
    private function createJournals(
        array $data,
        OperHead $operHead,
        int $branchId,
        ?int $customerId,
        ?int $cashAccId,
        ?int $bankAccId,
        float $total,
        float $cashAmt,
        float $cardAmt,
        float $paidAmt,
        string $payMethod,
        int $nextProId
    ): void {
        $date = now()->format('Y-m-d');
        $label = "فاتورة مطعم رقم {$nextProId}";
        $opId = $operHead->id;

        // حسابات المطعم من الإعدادات
        $kitchenStore = (int) ($this->settings?->restaurant_kitchen_store ?? 0);
        $operatingAccount = (int) ($this->settings?->restaurant_operating_account ?? 0);
        $salesAccount = (int) ($this->settings?->restaurant_sales_account ?? 47);
        $cogsAccount = (int) ($this->settings?->restaurant_cogs_account ?? 0);
        $inventoryAccount = (int) ($this->settings?->restaurant_inventory_account ?? 0);

        // ── قيود نموذج التصنيع (لكل صنف له template) ────────────────────
        if ($kitchenStore && $operatingAccount) {
            foreach ($data['items'] as $itemData) {
                $itemId = (int) $itemData['id'];
                $qty = (float) $itemData['quantity'];
                $price = (float) $itemData['price'];

                // هل للصنف نموذج تصنيع؟
                $template = ManufacturingOrder::where('item_id', $itemId)
                    ->where('is_template', 1)
                    ->first();

                if (! $template) {
                    continue;
                }

                // تكلفة الصنف (average_cost × الكمية)
                $itemModel = Item::find($itemId);
                $itemCost = (float) ($itemModel?->average_cost ?? 0) * $qty;
                $itemRevenue = $qty * $price;

                // قيد 1: تحويل من مركز التشغيل (وسيط) → مخزن المطبخ
                $jId = $this->nextJournalId();
                JournalHead::create([
                    'journal_id' => $jId,
                    'total' => $itemCost,
                    'op_id' => $opId,
                    'pro_type' => 103,
                    'date' => $date,
                    'details' => "تحويل خامات للمطبخ - {$label}",
                    'user' => Auth::id(),
                    'branch_id' => $branchId,
                ]);
                $this->debit($jId, $kitchenStore, $itemCost, 'مدين - مخزن المطبخ', $opId, $branchId);
                $this->credit($jId, $operatingAccount, $itemCost, 'دائن - مركز التشغيل', $opId, $branchId);

                // قيد 2: تحويل من مخزن المطبخ → مركز التشغيل (بعد التصنيع)
                $jId = $this->nextJournalId();
                JournalHead::create([
                    'journal_id' => $jId,
                    'total' => $itemCost,
                    'op_id' => $opId,
                    'pro_type' => 103,
                    'date' => $date,
                    'details' => "تحويل منتج تام من المطبخ - {$label}",
                    'user' => Auth::id(),
                    'branch_id' => $branchId,
                ]);
                $this->debit($jId, $operatingAccount, $itemCost, 'مدين - مركز التشغيل', $opId, $branchId);
                $this->credit($jId, $kitchenStore, $itemCost, 'دائن - مخزن المطبخ', $opId, $branchId);

                // قيد 3: تكلفة البضاعة المباعة
                if ($cogsAccount && $inventoryAccount && $itemCost > 0) {
                    $jId = $this->nextJournalId();
                    JournalHead::create([
                        'journal_id' => $jId,
                        'total' => $itemCost,
                        'op_id' => $opId,
                        'pro_type' => 103,
                        'date' => $date,
                        'details' => "تكلفة البضاعة المباعة - {$label}",
                        'user' => Auth::id(),
                        'branch_id' => $branchId,
                    ]);
                    $this->debit($jId, $cogsAccount, $itemCost, 'مدين - تكلفة البضاعة المباعة', $opId, $branchId);
                    $this->credit($jId, $inventoryAccount, $itemCost, 'دائن - المخزون', $opId, $branchId);
                }
            }
        }

        // ── قيد 4: المبيعات (العميل ← حساب المبيعات) ────────────────────
        if ($customerId && $salesAccount) {
            $jId = $this->nextJournalId();
            JournalHead::create([
                'journal_id' => $jId,
                'total' => $total,
                'op_id' => $opId,
                'pro_type' => 103,
                'date' => $date,
                'details' => "قيد مبيعات - {$label}",
                'user' => Auth::id(),
                'branch_id' => $branchId,
            ]);
            $this->debit($jId, $customerId, $total, 'مدين - عميل (مبيعات)', $opId, $branchId);
            $this->credit($jId, $salesAccount, $total, 'دائن - حساب المبيعات', $opId, $branchId);
        }

        // ── قيد 5 & 6: سندات الدفع ───────────────────────────────────────
        if ($paidAmt > 0 && $customerId) {
            if ($payMethod === 'mixed' && $cashAmt > 0 && $cardAmt > 0) {
                // دفع مختلط: قيدان منفصلان
                if ($cashAccId && $cashAmt > 0) {
                    $this->createPaymentJournal($cashAccId, $customerId, $cashAmt, "سند دفع نقدي - {$label}", $opId, $branchId, $date);
                }
                if ($bankAccId && $cardAmt > 0) {
                    $this->createPaymentJournal($bankAccId, $customerId, $cardAmt, "سند دفع بنك/فيزا - {$label}", $opId, $branchId, $date);
                }
            } elseif ($payMethod === 'card' && $bankAccId) {
                $this->createPaymentJournal($bankAccId, $customerId, $paidAmt, "سند دفع بنك/فيزا - {$label}", $opId, $branchId, $date);
            } else {
                // نقدي أو افتراضي
                $accId = $cashAccId ?? $bankAccId;
                if ($accId) {
                    $this->createPaymentJournal($accId, $customerId, $paidAmt, "سند دفع نقدي - {$label}", $opId, $branchId, $date);
                }
            }
        }
    }

    // =========================================================================
    // Helpers
    // =========================================================================

    /**
     * يُنشئ قيد سند دفع (مدين: حساب الدفع، دائن: العميل).
     */
    private function createPaymentJournal(
        int $payAccId,
        int $customerId,
        float $amount,
        string $details,
        int $opId,
        int $branchId,
        string $date
    ): void {
        $jId = $this->nextJournalId();
        JournalHead::create([
            'journal_id' => $jId,
            'total' => $amount,
            'op_id' => $opId,
            'pro_type' => 103,
            'date' => $date,
            'details' => $details,
            'user' => Auth::id(),
            'branch_id' => $branchId,
        ]);
        $this->debit($jId, $payAccId, $amount, 'مدين - '.$details, $opId, $branchId);
        $this->credit($jId, $customerId, $amount, 'دائن - عميل', $opId, $branchId);
    }

    /**
     * يُنشئ سطر مدين في journal_details.
     */
    private function debit(int $journalId, int $accountId, float $amount, string $info, int $opId, int $branchId): void
    {
        JournalDetail::create([
            'journal_id' => $journalId,
            'account_id' => $accountId,
            'debit' => $amount,
            'credit' => 0,
            'type' => 1,
            'info' => $info,
            'op_id' => $opId,
            'isdeleted' => 0,
            'branch_id' => $branchId,
        ]);
    }

    /**
     * يُنشئ سطر دائن في journal_details.
     */
    private function credit(int $journalId, int $accountId, float $amount, string $info, int $opId, int $branchId): void
    {
        JournalDetail::create([
            'journal_id' => $journalId,
            'account_id' => $accountId,
            'debit' => 0,
            'credit' => $amount,
            'type' => 1,
            'info' => $info,
            'op_id' => $opId,
            'isdeleted' => 0,
            'branch_id' => $branchId,
        ]);
    }

    /**
     * يُعيد journal_id التالي بأمان (يتجنب race condition).
     */
    private function nextJournalId(): int
    {
        return DB::transaction(function (): int {
            $max = JournalHead::lockForUpdate()->max('journal_id') ?? 0;

            return (int) $max + 1;
        });
    }
}
