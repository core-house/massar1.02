<?php

namespace Modules\OfflinePOS\Services;

use App\Models\OperHead;
use App\Models\OperationItems;
use App\Models\JournalHead;
use App\Models\JournalDetail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

/**
 * Service لمعالجة المعاملات
 * يحول المعاملة من offline format إلى server format ويحفظها
 */
class TransactionProcessorService
{
    /**
     * معالجة معاملة offline وإنشائها في السيرفر
     * 
     * @param array $transactionData
     * @param int|null $branchId
     * @return array
     */
    public function processTransaction(array $transactionData, ?int $branchId): array
    {
        try {
            // التحقق من البيانات المطلوبة
            $this->validateTransactionData($transactionData);

            // تحديد نوع المعاملة
            $proType = $this->getProType($transactionData['transaction_type']);

            // إنشاء رأس المعاملة (OperHead)
            $operHead = $this->createOperHead($transactionData, $proType, $branchId);

            // إنشاء تفاصيل الأصناف (OperationItems)
            $this->createOperationItems($operHead, $transactionData['items']);

            // إنشاء القيود المحاسبية (JournalHead + JournalDetail)
            $this->createJournalEntries($operHead, $transactionData);

            // إنشاء سند القبض إذا كان هناك مدفوع
            if (isset($transactionData['paid_amount']) && $transactionData['paid_amount'] > 0) {
                $this->createReceiptVoucher($operHead, $transactionData);
            }

            return [
                'success' => true,
                'transaction_id' => $operHead->id,
                'invoice_number' => $operHead->pro_id,
                'created_at' => $operHead->created_at->toISOString(),
            ];

        } catch (\Exception $e) {
            Log::error('TransactionProcessor Error: ' . $e->getMessage(), [
                'transaction' => $transactionData,
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * التحقق من البيانات المطلوبة
     * 
     * @param array $data
     * @throws \Exception
     */
    protected function validateTransactionData(array $data): void
    {
        $required = ['transaction_type', 'customer_id', 'store_id', 'items', 'total'];

        foreach ($required as $field) {
            if (!isset($data[$field])) {
                throw new \Exception("Missing required field: {$field}");
            }
        }

        if (empty($data['items'])) {
            throw new \Exception("Transaction must have at least one item");
        }
    }

    /**
     * تحديد نوع المعاملة (pro_type)
     * 
     * @param string $type
     * @return int
     */
    protected function getProType(string $type): int
    {
        return match($type) {
            'sale' => 10,      // فاتورة مبيعات
            'return' => 12,    // فاتورة مرتجع مبيعات
            default => 10,
        };
    }

    /**
     * إنشاء رأس المعاملة
     * 
     * @param array $data
     * @param int $proType
     * @param int|null $branchId
     * @return OperHead
     */
    protected function createOperHead(array $data, int $proType, ?int $branchId): OperHead
    {
        // جلب رقم فاتورة تلقائي
        $proId = OperHead::max('pro_id') + 1;

        // تحضير البيانات
        $operData = [
            'pro_id' => $proId,
            'pro_type' => $proType,
            'pro_date' => isset($data['date']) ? Carbon::parse($data['date'])->format('Y-m-d') : now()->format('Y-m-d'),
            'accural_date' => isset($data['date']) ? Carbon::parse($data['date'])->format('Y-m-d') : now()->format('Y-m-d'),
            'pro_serial' => $this->generateSerialNumber($proType, $proId),
            
            // الحسابات
            'acc1' => $data['customer_id'],
            'acc2' => $data['store_id'],
            'emp_id' => $data['employee_id'] ?? null,
            
            // القيم المالية
            'fat_total' => $data['subtotal'] ?? $data['total'],
            'fat_disc_per' => $data['discount_percentage'] ?? 0,
            'fat_disc' => $data['discount_value'] ?? 0,
            'fat_plus_per' => $data['additional_percentage'] ?? 0,
            'fat_plus' => $data['additional_value'] ?? 0,
            'fat_net' => $data['total'],
            
            // الدفع
            'paid_from_client' => $data['paid_amount'] ?? 0,
            
            // معلومات إضافية
            'info' => $data['notes'] ?? 'Synced from Offline POS',
            'user' => Auth::id(),
            
            // الحالة
            'is_journal' => 1,
            'is_stock' => 1,
            'isdeleted' => 0,
        ];

        return OperHead::create($operData);
    }

    /**
     * توليد رقم تسلسلي
     * 
     * @param int $proType
     * @param int $proId
     * @return string
     */
    protected function generateSerialNumber(int $proType, int $proId): string
    {
        $prefix = match($proType) {
            10 => 'SALE',
            12 => 'RET',
            default => 'INV',
        };

        return $prefix . '-' . now()->format('Y') . '-' . str_pad($proId, 6, '0', STR_PAD_LEFT);
    }

    /**
     * إنشاء تفاصيل الأصناف
     * 
     * @param OperHead $operHead
     * @param array $items
     */
    protected function createOperationItems(OperHead $operHead, array $items): void
    {
        foreach ($items as $item) {
            // حساب القيم
            $quantity = $item['quantity'];
            $price = $item['price'];
            $discount = $item['discount'] ?? 0;
            $subTotal = ($quantity * $price) - $discount;

            $itemData = [
                'op_id' => $operHead->id,
                'pro_type' => $operHead->pro_type,
                'pro_tybe' => $operHead->pro_type,
                'item_id' => $item['item_id'],
                'unit_id' => $item['unit_id'],
                'detail_store' => $operHead->acc2,
                
                // الكمية (حسب نوع المعاملة)
                'qty_in' => $operHead->pro_type == 12 ? $quantity : 0,  // مرتجع
                'qty_out' => $operHead->pro_type == 10 ? $quantity : 0, // مبيعات
                
                // الأسعار
                'item_price' => $price,
                'item_discount' => $discount,
                'detail_value' => $subTotal,
                
                // حالة
                'is_stock' => 1,
                'isdeleted' => 0,
            ];

            OperationItems::create($itemData);
        }
    }

    /**
     * إنشاء القيود المحاسبية
     * 
     * @param OperHead $operHead
     * @param array $data
     */
    protected function createJournalEntries(OperHead $operHead, array $data): void
    {
        // إنشاء JournalHead
        $journalHead = JournalHead::create([
            'op_id' => $operHead->id,
            'op2' => null,
            'journaldate' => $operHead->pro_date,
            'serial_no' => $operHead->pro_serial,
            'info' => $operHead->info,
            'isdeleted' => 0,
        ]);

        // القيد الأول: المدين (العميل)
        JournalDetail::create([
            'journal_id' => $journalHead->id,
            'op_id' => $operHead->id,
            'account_id' => $operHead->acc1,
            'debit' => $operHead->fat_net,
            'credit' => 0,
            'info' => 'مبيعات - ' . $operHead->pro_serial,
            'isdeleted' => 0,
        ]);

        // القيد الثاني: الدائن (المبيعات/المخزن)
        JournalDetail::create([
            'journal_id' => $journalHead->id,
            'op_id' => $operHead->id,
            'account_id' => $operHead->acc2,
            'debit' => 0,
            'credit' => $operHead->fat_net,
            'info' => 'مبيعات - ' . $operHead->pro_serial,
            'isdeleted' => 0,
        ]);
    }

    /**
     * إنشاء سند قبض
     * 
     * @param OperHead $operHead
     * @param array $data
     */
    protected function createReceiptVoucher(OperHead $operHead, array $data): void
    {
        $paidAmount = $data['paid_amount'];
        $cashBoxId = $data['cash_box_id'] ?? null;

        if (!$cashBoxId) {
            return; // لا يوجد صندوق محدد
        }

        // إنشاء سند قبض تلقائي
        $receiptProId = OperHead::max('pro_id') + 1;

        $receiptOper = OperHead::create([
            'pro_id' => $receiptProId,
            'pro_type' => 1, // سند قبض
            'pro_date' => $operHead->pro_date,
            'accural_date' => $operHead->accural_date,
            'pro_serial' => 'REC-' . now()->format('Y') . '-' . str_pad($receiptProId, 6, '0', STR_PAD_LEFT),
            'acc1' => $cashBoxId,      // الصندوق (مدين)
            'acc2' => $operHead->acc1, // العميل (دائن)
            'fat_net' => $paidAmount,
            'info' => 'سند قبض - فاتورة ' . $operHead->pro_serial,
            'op2' => $operHead->id,
            'user' => Auth::id(),
            'is_journal' => 1,
            'is_stock' => 0,
            'isdeleted' => 0,
        ]);

        // القيود المحاسبية للسند
        $receiptJournalHead = JournalHead::create([
            'op_id' => $receiptOper->id,
            'op2' => $operHead->id,
            'journaldate' => $receiptOper->pro_date,
            'serial_no' => $receiptOper->pro_serial,
            'info' => $receiptOper->info,
            'isdeleted' => 0,
        ]);

        // مدين: الصندوق
        JournalDetail::create([
            'journal_id' => $receiptJournalHead->id,
            'op_id' => $receiptOper->id,
            'account_id' => $cashBoxId,
            'debit' => $paidAmount,
            'credit' => 0,
            'info' => 'سند قبض - ' . $receiptOper->pro_serial,
            'isdeleted' => 0,
        ]);

        // دائن: العميل
        JournalDetail::create([
            'journal_id' => $receiptJournalHead->id,
            'op_id' => $receiptOper->id,
            'account_id' => $operHead->acc1,
            'debit' => 0,
            'credit' => $paidAmount,
            'info' => 'سند قبض - ' . $receiptOper->pro_serial,
            'isdeleted' => 0,
        ]);
    }
}
