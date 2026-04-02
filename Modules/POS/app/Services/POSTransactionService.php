<?php

declare(strict_types=1);

namespace Modules\POS\app\Services;

use App\Models\Item;
use App\Models\JournalDetail;
use App\Models\JournalHead;
use App\Models\OperHead;
use App\Models\Setting;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Modules\POS\app\Models\CashierTransaction;

class POSTransactionService
{
    /**
     * تعليق الفاتورة
     */
    public function holdOrder(array $data, int $userId, int $branchId): CashierTransaction
    {
        $subtotal = 0;
        foreach ($data['items'] as $item) {
            $subtotal += $item['quantity'] * $item['price'];
        }
        $discount = 0;
        $additional = 0;
        $total = $subtotal - $discount + $additional;
        $paidAmount = ($data['cash_amount'] ?? 0) + ($data['card_amount'] ?? 0);

        return CashierTransaction::create([
            'local_id' => $data['local_id'] ?? Str::uuid()->toString(),
            'pro_type_id' => 102,
            'pro_id' => null,
            'pro_date' => now()->format('Y-m-d'),
            'accural_date' => now()->format('Y-m-d'),
            'customer_id' => $data['customer_id'] ?? null,
            'store_id' => $data['store_id'] ?? null,
            'cash_account_id' => $data['cash_account_id'] ?? null,
            'employee_id' => $data['employee_id'] ?? null,
            'subtotal' => $subtotal,
            'discount' => $discount,
            'total' => $total,
            'payment_method' => $data['payment_method'] ?? null,
            'cash_amount' => $data['cash_amount'] ?? 0,
            'card_amount' => $data['card_amount'] ?? 0,
            'paid_amount' => $paidAmount,
            'notes' => $data['notes'] ?? 'فاتورة معلقة',
            'table_id' => $data['table_id'] ?? null,
            'items' => $data['items'],
            'status' => 'held',
            'held_at' => now(),
            'sync_status' => 'pending',
            'user_id' => $userId,
            'branch_id' => $branchId,
        ]);
    }

    /**
     * إكمال فاتورة معلقة
     */
    public function completeHeldOrder(int $heldOrderId, int $userId, int $branchId): array
    {
        return DB::transaction(function () use ($heldOrderId, $userId, $branchId) {
            $heldOrder = CashierTransaction::held()->where('branch_id', $branchId)->findOrFail($heldOrderId);
            $nextProId = OperHead::max('pro_id') + 1 ?? 1;

            $operHead = OperHead::create([
                'pro_id' => $nextProId,
                'pro_date' => now()->format('Y-m-d'),
                'accural_date' => now()->format('Y-m-d'),
                'pro_type' => 102,
                'acc1' => $heldOrder->customer_id,
                'acc2' => $heldOrder->cash_account_id ?? $heldOrder->store_id,
                'store_id' => $heldOrder->store_id,
                'emp_id' => $heldOrder->employee_id,
                'fat_total' => $heldOrder->subtotal,
                'fat_disc' => $heldOrder->discount,
                'fat_net' => $heldOrder->total,
                'pro_value' => $heldOrder->total,
                'paid_from_client' => $heldOrder->paid_amount,
                'info' => $heldOrder->notes ?? 'فاتورة كاشير (من فاتورة معلقة)',
                'details' => $heldOrder->notes ?? 'فاتورة كاشير (من فاتورة معلقة)',
                'isdeleted' => 0,
                'is_stock' => 1,
                'is_finance' => 1,
                'is_journal' => 1,
                'journal_type' => 2,
                'user' => $userId,
                'branch_id' => $branchId,
            ]);

            foreach ($heldOrder->items as $item) {
                $itemModel = Item::find($item['id']);
                $unitId = $item['unit_id'] ?? $itemModel->units()->first()?->id ?? null;
                
                DB::table('operation_items')->insert([
                    'pro_id' => $operHead->id,
                    'item_id' => $item['id'],
                    'unit_id' => $unitId,
                    'qty_in' => 0,
                    'qty_out' => $item['quantity'],
                    'item_price' => $item['price'],
                    'cost_price' => 0,
                    'detail_value' => $item['quantity'] * $item['price'],
                    'is_stock' => 1,
                    'isdeleted' => 0,
                    'branch_id' => $branchId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // القيود المحاسبية (تبسيط للـ service)
            $this->createOrderJournal($operHead, $heldOrder, $nextProId, $userId, $branchId);

            $heldOrder->update([
                'server_id' => $operHead->id,
                'pro_id' => $nextProId,
                'status' => 'completed',
                'completed_at' => now(),
                'sync_status' => 'synced',
                'synced_at' => now(),
            ]);

            return [
                'transaction_id' => $operHead->id,
                'invoice_number' => $nextProId,
            ];
        });
    }

    private function createOrderJournal($operHead, $heldOrder, $nextProId, $userId, $branchId)
    {
        $lastJournalId = JournalHead::max('journal_id') ?? 0;
        $journalId = $lastJournalId + 1;
        
        JournalHead::create([
            'journal_id' => $journalId,
            'total' => $heldOrder->total,
            'op_id' => $operHead->id,
            'pro_type' => 102,
            'date' => now()->format('Y-m-d'),
            'details' => 'قيد فاتورة كاشير رقم '.$nextProId.' (من فاتورة معلقة)',
            'user' => $userId,
            'branch_id' => $branchId,
        ]);

        if ($heldOrder->customer_id) {
            JournalDetail::create([
                'journal_id' => $journalId, 'account_id' => $heldOrder->customer_id,
                'debit' => $heldOrder->total, 'credit' => 0, 'type' => 0,
                'info' => 'مدين - عميل', 'op_id' => $operHead->id, 'isdeleted' => 0, 'branch' => $branchId,
            ]);
        }

        $creditAccount = $heldOrder->cash_account_id ?? $heldOrder->store_id;
        if ($creditAccount) {
            JournalDetail::create([
                'journal_id' => $journalId, 'account_id' => $creditAccount,
                'debit' => 0, 'credit' => $heldOrder->total, 'type' => 1,
                'info' => 'دائن - مخزن/صندوق', 'op_id' => $operHead->id, 'isdeleted' => 0, 'branch' => $branchId,
            ]);
        }
    }

    /**
     * تسجيل مصروف نثري
     */
    public function recordPettyCash(array $data, int $userId, int $branchId): array
    {
        return DB::transaction(function () use ($data, $userId, $branchId) {
            $nextProId = OperHead::where('pro_type', 2)->max('pro_id') + 1 ?? 1;

            $operHead = OperHead::create([
                'pro_id' => $nextProId,
                'pro_date' => now()->format('Y-m-d'),
                'accural_date' => now()->format('Y-m-d'),
                'pro_type' => 2,
                'acc1' => $data['expense_account_id'],
                'acc2' => $data['cash_account_id'],
                'pro_value' => $data['amount'],
                'fat_net' => $data['amount'],
                'info' => $data['description'],
                'details' => ($data['notes'] ?? '') ? $data['description'].' - '.$data['notes'] : $data['description'],
                'isdeleted' => 0, 'is_finance' => 1, 'is_journal' => 1, 'journal_type' => 2,
                'user' => $userId, 'branch_id' => $branchId,
            ]);

            $lastJournalId = JournalHead::max('journal_id') ?? 0;
            $journalId = $lastJournalId + 1;
            JournalHead::create([
                'journal_id' => $journalId, 'total' => $data['amount'], 'op_id' => $operHead->id,
                'pro_type' => 2, 'date' => now()->format('Y-m-d'), 'details' => 'قيد مصروف نثري - '.$data['description'],
                'user' => $userId, 'branch_id' => $branchId,
            ]);

            JournalDetail::create([
                'journal_id' => $journalId, 'account_id' => $data['expense_account_id'],
                'debit' => $data['amount'], 'credit' => 0, 'type' => 0,
                'info' => 'مدين - مصروف نثري: '.$data['description'], 'op_id' => $operHead->id, 'isdeleted' => 0, 'branch' => $branchId,
            ]);

            JournalDetail::create([
                'journal_id' => $journalId, 'account_id' => $data['cash_account_id'],
                'debit' => 0, 'credit' => $data['amount'], 'type' => 1,
                'info' => 'دائن - صندوق (مصروف نثري)', 'op_id' => $operHead->id, 'isdeleted' => 0, 'branch' => $branchId,
            ]);

            return [
                'voucher_id' => $operHead->id,
                'voucher_number' => $nextProId,
            ];
        });
    }

    /**
     * إرجاع فاتورة كاشير
     */
    public function returnInvoice(int $originalInvoiceId, array $data, int $userId, int $branchId): array
    {
        return DB::transaction(function () use ($originalInvoiceId, $data, $userId, $branchId) {
            $originalInvoice = OperHead::with('operationItems')->where('pro_type', 102)->findOrFail($originalInvoiceId);
            $nextProId = OperHead::where('pro_type', 112)->max('pro_id') + 1 ?? 1;

            $cashierTx = CashierTransaction::where('server_id', $originalInvoice->id)->first();
            $returnCashAmount = $data['cash_amount'] ?? ($cashierTx->cash_amount ?? 0);
            $returnCardAmount = $data['card_amount'] ?? ($cashierTx->card_amount ?? 0);
            $totalReturnAmount = $returnCashAmount + $returnCardAmount;

            $returnInvoice = OperHead::create([
                'pro_id' => $nextProId,
                'pro_date' => now()->format('Y-m-d'),
                'accural_date' => now()->format('Y-m-d'),
                'pro_type' => 112,
                'acc1' => $originalInvoice->acc1,
                'acc2' => $originalInvoice->acc2,
                'store_id' => $originalInvoice->store_id,
                'emp_id' => $originalInvoice->emp_id,
                'fat_total' => $originalInvoice->fat_total,
                'fat_net' => $originalInvoice->fat_net,
                'pro_value' => $originalInvoice->pro_value,
                'paid_from_client' => $totalReturnAmount,
                'info' => 'إرجاع فاتورة كاشير رقم '.$originalInvoice->pro_id,
                'details' => 'إرجاع فاتورة كاشير رقم '.$originalInvoice->pro_id,
                'isdeleted' => 0, 'is_stock' => 1, 'is_finance' => 1, 'is_journal' => 1, 'journal_type' => 2,
                'op2' => $originalInvoice->id, 'user' => $userId, 'branch_id' => $branchId,
            ]);

            foreach ($originalInvoice->operationItems as $item) {
                DB::table('operation_items')->insert([
                    'pro_id' => $returnInvoice->id, 'item_id' => $item->item_id, 'unit_id' => $item->unit_id,
                    'qty_in' => $item->qty_out, 'qty_out' => 0, 'item_price' => $item->item_price,
                    'cost_price' => $item->cost_price ?? 0, 'detail_value' => $item->detail_value,
                    'notes' => 'إرجاع من فاتورة كاشير '.$originalInvoice->pro_id,
                    'is_stock' => 1, 'isdeleted' => 0, 'branch_id' => $branchId,
                    'created_at' => now(), 'updated_at' => now(),
                ]);
            }

            // القيود المحاسبية للمرتجع (تبسيط)
            $this->createReturnJournal($returnInvoice, $originalInvoice, $nextProId, $userId, $branchId);

            return [
                'return_invoice_id' => $returnInvoice->id,
                'return_invoice_number' => $nextProId,
            ];
        });
    }

    /**
     * تحديث معاملة POS
     */
    public function updateTransaction(int $id, array $data, int $userId, int $branchId): void
    {
        DB::transaction(function () use ($id, $data, $branchId) {
            $transaction = OperHead::where('pro_type', 102)->where('isdeleted', 0)->findOrFail($id);
            
            $subtotal = collect($data['items'])->sum(fn($item) => $item['quantity'] * $item['price']);
            $total = $subtotal; // يمكن إضافة خصم/إضافات إذا لزم الأمر
            $paidAmount = ($data['cash_amount'] ?? 0) + ($data['card_amount'] ?? 0);

            $transaction->update([
                'acc1' => $data['customer_id'] ?? $transaction->acc1,
                'acc2' => $data['cash_account_id'] ?? $data['store_id'] ?? $transaction->acc2,
                'store_id' => $data['store_id'] ?? $transaction->store_id,
                'emp_id' => $data['employee_id'] ?? $transaction->emp_id,
                'fat_total' => $subtotal,
                'fat_net' => $total,
                'pro_value' => $total,
                'paid_from_client' => $paidAmount,
                'info' => $data['notes'] ?? $transaction->info,
                'details' => $data['notes'] ?? $transaction->details,
            ]);

            $transaction->operationItems()->delete();

            foreach ($data['items'] as $item) {
                $itemModel = Item::find($item['id']);
                $unitId = $item['unit_id'] ?? $itemModel->units()->first()?->id ?? null;
                
                DB::table('operation_items')->insert([
                    'pro_id' => $transaction->id, 'item_id' => $item['id'], 'unit_id' => $unitId,
                    'qty_in' => 0, 'qty_out' => $item['quantity'], 'item_price' => $item['price'],
                    'cost_price' => 0, 'detail_value' => $item['quantity'] * $item['price'],
                    'is_stock' => 1, 'isdeleted' => 0, 'branch_id' => $branchId,
                    'created_at' => now(), 'updated_at' => now(),
                ]);
            }

            $this->updateTransactionJournal($transaction, $total, $data['customer_id'] ?? $transaction->acc1, $branchId);
        });
    }

    private function updateTransactionJournal($transaction, $total, $customerId, $branchId)
    {
        $journalHead = JournalHead::where('op_id', $transaction->id)->first();
        if ($journalHead) {
            $journalHead->update(['total' => $total]);
            JournalDetail::where('journal_id', $journalHead->journal_id)->delete();

            if ($customerId) {
                JournalDetail::create([
                    'journal_id' => $journalHead->journal_id, 'account_id' => $customerId,
                    'debit' => $total, 'credit' => 0, 'type' => 0,
                    'info' => 'مدين - عميل', 'op_id' => $transaction->id, 'isdeleted' => 0, 'branch' => $branchId,
                ]);
            }

            if ($transaction->acc2) {
                JournalDetail::create([
                    'journal_id' => $journalHead->journal_id, 'account_id' => $transaction->acc2,
                    'debit' => 0, 'credit' => $total, 'type' => 1,
                    'info' => 'دائن - مخزن/صندوق', 'op_id' => $transaction->id, 'isdeleted' => 0, 'branch' => $branchId,
                ]);
            }
        }
    }

    /**
     * تحديث إعدادات الكاشير
     */
    public function updateSettings(array $data): void
    {
        $settings = Setting::first() ?? new Setting;
        
        $settings->def_pos_client = $data['def_pos_client'] ?? $settings->def_pos_client;
        $settings->def_pos_store = $data['def_pos_store'] ?? $settings->def_pos_store;
        $settings->def_pos_employee = $data['def_pos_employee'] ?? $settings->def_pos_employee;
        $settings->def_pos_fund = $data['def_pos_fund'] ?? $settings->def_pos_fund;
        $settings->def_pos_bank = $data['def_pos_bank'] ?? $settings->def_pos_bank;
        $settings->def_pos_price_group = $data['def_pos_price_group'] ?? $settings->def_pos_price_group;
        $settings->enable_scale_items = (bool)($data['enable_scale_items'] ?? $settings->enable_scale_items);
        $settings->scale_code_prefix = $data['scale_code_prefix'] ?? $settings->scale_code_prefix;
        $settings->scale_code_digits = $data['scale_code_digits'] ?? $settings->scale_code_digits;
        $settings->scale_quantity_digits = $data['scale_quantity_digits'] ?? $settings->scale_quantity_digits;
        $settings->scale_quantity_divisor = $data['scale_quantity_divisor'] ?? $settings->scale_quantity_divisor;
        $settings->restaurant_kitchen_store = $data['restaurant_kitchen_store'] ?? $settings->restaurant_kitchen_store;
        $settings->restaurant_operating_account = $data['restaurant_operating_account'] ?? $settings->restaurant_operating_account;
        $settings->restaurant_sales_account = $data['restaurant_sales_account'] ?? $settings->restaurant_sales_account;
        $settings->restaurant_cogs_account = $data['restaurant_cogs_account'] ?? $settings->restaurant_cogs_account;
        $settings->restaurant_inventory_account = $data['restaurant_inventory_account'] ?? $settings->restaurant_inventory_account;

        $settings->save();
    }

    /**
     * حفظ عميل توصيل جديد
     */
    public function saveDeliveryCustomer(array $data, int $branchId): array
    {
        $client = \App\Models\Client::create([
            'cname' => $data['name'],
            'phone' => $data['phone'],
            'address' => $data['address'] ?? null,
            'isdeleted' => 0, 'is_active' => 1, 'branch_id' => $branchId,
        ]);

        return ['id' => $client->id, 'name' => $client->cname, 'phone' => $client->phone];
    }

    /**
     * تحديث عنوان عميل التوصيل
     */
    public function updateDeliveryCustomerAddress(int $customerId, string $address, string $field): void
    {
        \App\Models\Client::where('id', $customerId)->update([$field => $address]);
    }

    /**
     * حذف معامله POS
     */
    public function deleteTransaction(int $id): void
    {
        DB::transaction(function () use ($id) {
            $operation = OperHead::findOrFail($id);
            if ($operation->pro_type !== 102 && $operation->pro_type !== 10) {
                 throw new \Exception('المعاملة المطلوبة غير موجودة.');
            }

            $operation->operationItems()->delete();
            JournalDetail::where('op_id', $operation->id)->delete();
            JournalHead::where('op_id', $operation->id)->orWhere('op2', $operation->id)->delete();

            $autoVoucher = OperHead::where('op2', $operation->id)->where('is_journal', 1)->where('is_stock', 0)->first();
            if ($autoVoucher) {
                JournalDetail::where('op_id', $autoVoucher->id)->delete();
                JournalHead::where('op_id', $autoVoucher->id)->orWhere('op2', $autoVoucher->id)->delete();
                $autoVoucher->delete();
            }

            $operation->delete();
        });
    }

    private function createReturnJournal($returnInvoice, $originalInvoice, $nextProId, $userId, $branchId)
    {
        $lastJournalId = JournalHead::max('journal_id') ?? 0;
        $jId = $lastJournalId + 1;
        JournalHead::create([
            'journal_id' => $jId, 'total' => $returnInvoice->fat_net, 'op_id' => $returnInvoice->id,
            'pro_type' => 112, 'date' => now()->format('Y-m-d'), 'details' => 'قيد مردود مبيعات - مرتجع كاشير رقم '.$nextProId,
            'user' => $userId, 'branch_id' => $branchId,
        ]);

        if ($originalInvoice->acc1) {
            JournalDetail::create([
                'journal_id' => $jId, 'account_id' => $originalInvoice->acc1, 'debit' => 0, 'credit' => $returnInvoice->fat_net,
                'type' => 1, 'info' => 'دائن - عميل (مردود مبيعات)', 'op_id' => $returnInvoice->id, 'isdeleted' => 0, 'branch' => $branchId,
            ]);
        }
        JournalDetail::create([
            'journal_id' => $jId, 'account_id' => 48, 'debit' => $returnInvoice->fat_net, 'credit' => 0,
            'type' => 0, 'info' => 'مدين - مردود مبيعات', 'op_id' => $returnInvoice->id, 'isdeleted' => 0, 'branch' => $branchId,
        ]);
    }
}
