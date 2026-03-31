<?php

declare(strict_types=1);

namespace Modules\Installments\Livewire;

use App\Models\{JournalDetail, JournalHead, OperHead};
use Carbon\Carbon;
use Illuminate\Support\Facades\{Auth, DB};
use Livewire\Component;
use Modules\Accounts\Models\AccHead;
use Modules\Installments\Models\{InstallmentPayment, InstallmentPlan};

class ShowInstallmentPlan extends Component
{
    public InstallmentPlan $plan;

    public $selectedPaymentId;
    public $paymentAmount;
    public $paymentDate;
    public $notes;

    public function mount(InstallmentPlan $plan)
    {
        $this->plan = $plan->load('payments', 'account');
    }

    public function openPaymentModal($paymentId)
    {
        $payment = InstallmentPayment::findOrFail($paymentId);
        $this->selectedPaymentId = $payment->id;
        $this->paymentAmount = $payment->amount_due - $payment->amount_paid; // القيمة المتبقية كقيمة افتراضية
        $this->paymentDate = Carbon::now()->format('Y-m-d\TH:i');
        $this->notes = '';

        // إرسال حدث للمتصفح لفتح النافذة المنبثقة
        $this->dispatch('open-modal', 'paymentModal');
    }

    public function recordPayment()
    {
        $payment = InstallmentPayment::findOrFail($this->selectedPaymentId);

        $validated = $this->validate([
            'paymentAmount' => 'required|numeric|min:0.01|max:' . ($payment->amount_due - $payment->amount_paid),
            'paymentDate' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();
            // Update payment record
            $payment->update([
                'amount_paid' => $payment->amount_paid + $validated['paymentAmount'],
                'payment_date' => $validated['paymentDate'],
                'notes' => $validated['notes'],
            ]);

            // Update payment status
            if ($payment->amount_paid >= $payment->amount_due) {
                $payment->status = 'paid';
            }
            $payment->save();

            // Create journal entry for the payment
            $this->createJournalEntry($payment, $validated['paymentAmount'], $validated['paymentDate'], $validated['notes']);

            DB::commit();

            // Refresh plan
            $this->plan->refresh();

            // Close modal and show success message
            $this->dispatch('close-modal', 'paymentModal');
            $this->dispatch('payment-success', [
                'title' => __('installments::installments.recorded_successfully'),
                'text' => __('installments::installments.payment_recorded_and_journal_created'),
            ]);
        } catch (\Exception) {
            DB::rollBack();

            $this->dispatch('payment-error', [
                'title' => __('installments::installments.error'),
                'text' => __('installments::installments.error_recording_payment'),
            ]);
        }
    }

    /**
     * Create receipt voucher and journal entry for installment payment
     */
    private function createJournalEntry(InstallmentPayment $payment, float $amount, string $date, ?string $notes)
    {
        try {
            DB::beginTransaction();
            $plan = $payment->plan;

            // Get the cash/bank account
            $cashAccount = AccHead::where('code', 'like', '1101%')
                ->where('is_basic', 0)
                ->where('isdeleted', 0)
                ->first();

            if (! $cashAccount) {
                throw new \Exception(__('installments::installments.cash_account_not_found'));
            }

            // Get next pro_id for receipt vouchers (pro_type = 1)
            $lastProId = OperHead::where('pro_type', 1)->max('pro_id') ?? 0;
            $newProId = $lastProId + 1;

            // Create OperHead record as Receipt Voucher (pro_type = 1)
            $operHead = OperHead::create([
                'pro_id' => $newProId,
                'pro_date' => $date,
                'pro_type' => 1, // Receipt Voucher type
                'acc1' => $cashAccount->id,
                'acc2' => $plan->acc_head_id,
                'pro_value' => $amount,
                'total' => $amount,
                'details' => $notes ?? __('installments::installments.receipt_voucher') . ' - ' . __('installments::installments.installment_number') . " {$payment->installment_number} - " . __('installments::installments.plan_number') . " {$plan->id}",
                'user' => Auth::id(),
                'branch_id' => Auth::user()->branch_id ?? 1,
                'isdeleted' => 0,
                'tenant' => 0,
                'branch' => 1,
                'is_finance' => 1,
                'is_journal' => 1,
                'journal_type' => 2,
                'acc1_before' => 0,
                'acc1_after' => 0,
                'acc2_before' => 0,
                'acc2_after' => 0,
            ]);

            // Get next journal_id
            $lastJournal = JournalHead::orderBy('journal_id', 'desc')->first();
            $journalId = $lastJournal ? $lastJournal->journal_id + 1 : 1;

            JournalHead::create([
                'journal_id' => $journalId,
                'op_id' => $operHead->id,
                'total' => $amount,
                'date' => $date,
                'pro_type' => 32,
                'details' => __('installments::installments.receipt_voucher') . ' - ' . __('installments::installments.installment_number') . " {$payment->installment_number} - " . __('installments::installments.plan_number') . " {$plan->id}",
                'branch_id' => $operHead->branch_id,
                'user' => Auth::id(),
            ]);

            JournalDetail::create([
                'journal_id' => $journalId,
                'op_id' => $operHead->id,
                'account_id' => $cashAccount->id,
                'debit' => $amount,
                'credit' => 0,
                'type' => 0,
                'info' => __('installments::installments.record_payment') . ' ' . __('installments::installments.installment_number') . " {$payment->installment_number}",
                'branch_id' => $operHead->branch_id,
                'isdeleted' => 0,
            ]);

            JournalDetail::create([
                'journal_id' => $journalId,
                'op_id' => $operHead->id,
                'account_id' => $plan->acc_head_id,
                'debit' => 0,
                'credit' => $amount,
                'type' => 32,
                'info' => __('installments::installments.amount_paid') . ' ' . __('installments::installments.installment_number') . " {$payment->installment_number}",
                'branch_id' => $operHead->branch_id,
                'isdeleted' => 0,
            ]);
            DB::commit();
        } catch (\Exception) {
            DB::rollBack();
        }
    }

    /**
     * Delete an unpaid payment
     */
    public function deletePayment($paymentId)
    {
        try {
            $payment = InstallmentPayment::findOrFail($paymentId);

            if ($payment->status === 'paid') {
                $this->dispatch('payment-error', [
                    'title' => __('installments::installments.error'),
                    'text' => __('installments::installments.cannot_delete_paid_installment'),
                ]);

                return;
            }

            $payment->delete();
            $this->plan->refresh();

            $this->dispatch('payment-success', [
                'title' => __('installments::installments.success'),
                'text' => __('installments::installments.installment_deleted_successfully'),
            ]);
        } catch (\Exception) {
            $this->dispatch('payment-error', [
                'title' => __('installments::installments.error'),
                'text' => __('installments::installments.error_deleting_installment'),
            ]);
        }
    }

    /**
     * Cancel a paid payment and delete its journal entry
     */
    public function cancelPayment($paymentId)
    {
        try {
            DB::beginTransaction();

            $payment = InstallmentPayment::findOrFail($paymentId);

            if ($payment->status !== 'paid') {
                $this->dispatch('payment-error', [
                    'title' => __('installments::installments.error'),
                    'text' => __('installments::installments.installment_not_paid'),
                ]);

                return;
            }

            // Find and delete the journal entry
            $this->deleteJournalEntry($payment);

            // Reset payment
            $payment->update([
                'amount_paid' => 0,
                'payment_date' => null,
                'status' => 'pending',
                'notes' => ($payment->notes ?? '') . ' ' . __('installments::installments.cancelled_label'),
            ]);

            DB::commit();

            $this->plan->refresh();

            $this->dispatch('payment-success', [
                'title' => __('installments::installments.success'),
                'text' => __('installments::installments.payment_cancelled_and_journal_deleted'),
            ]);
        } catch (\Exception) {
            DB::rollBack();

            $this->dispatch('payment-error', [
                'title' => __('installments::installments.error'),
                'text' => __('installments::installments.error_cancelling_payment'),
            ]);
        }
    }

    /**
     * Delete receipt voucher and journal entry for a payment
     */
    private function deleteJournalEntry(InstallmentPayment $payment)
    {
        $plan = $payment->plan;

        // Find the OperHead (Receipt Voucher) for this payment
        $operHead = OperHead::where('pro_type', 32) // Receipt voucher type
            ->where('acc2', $plan->acc_head_id)
            ->where('details', 'like', '%' . __('installments::installments.installment_number') . " {$payment->installment_number}%")
            ->where('details', 'like', '%' . __('installments::installments.plan_number') . " {$plan->id}%")
            ->first();

        if ($operHead) {
            // Delete JournalDetails
            JournalDetail::where('op_id', $operHead->id)->delete();

            // Delete JournalHead
            JournalHead::where('op_id', $operHead->id)->delete();

            // Delete OperHead (Receipt Voucher)
            $operHead->delete();
        }
    }

    /**
     * Delete entire installment plan
     */
    public function deletePlan()
    {
        try {
            DB::beginTransaction();

            // Cancel all paid payments (delete their journal entries)
            $paidPayments = $this->plan->payments()->where('status', 'paid')->get();
            foreach ($paidPayments as $payment) {
                $this->deleteJournalEntry($payment);
            }

            // Delete all payments
            $this->plan->payments()->delete();

            // Delete the plan
            $planId = $this->plan->id;
            $this->plan->delete();

            DB::commit();

            // Redirect to plans index with success message
            session()->flash('success', __('installments::installments.plan_and_all_deleted_successfully'));

            return redirect()->route('installments.plans.index');
        } catch (\Exception) {
            DB::rollBack();

            $this->dispatch('payment-error', [
                'title' => __('installments::installments.error'),
                'text' => __('installments::installments.error_deleting_plan'),
            ]);
        }
    }

    public function render()
    {
        return view('installments::livewire.show-installment-plan');
    }
}
