<?php

declare(strict_types=1);

namespace Modules\Installments\Livewire;

use Carbon\Carbon;
use Illuminate\Support\Facades\{Auth, DB};
use Livewire\Component;
use App\Models\{JournalDetail, JournalHead, OperHead};
use Modules\Accounts\Models\AccHead;
use Modules\Installments\Models\InstallmentPlan;

class CreateInstallmentFromInvoice extends Component
{
    public $invoiceId;
    public $invoiceTotal;
    public $clientAccountId;
    public $accHeadId;
    public $accountBalance = 0;
    public $totalInstallmentPlans = 0;

    public $availableBalance = 0;
    public $existingPlans = [];
    public $showBalanceWarning = false;
    public $totalAmount;
    public $downPayment = 0;
    public $interestValue = 0;
    public $interestPercentage = 0;
    public $interestType = 'fixed';
    public $amountToBeInstalled;
    public $numberOfInstallments = 1;
    public $installmentAmount;
    public $startDate;
    public $intervalType = 'monthly';

    protected $listeners = ['update-installment-from-button' => 'handleUpdateFromButton'];

    public function handleUpdateFromButton($invoiceTotal, $clientAccountId)
    {
        $this->updateFromInvoice($invoiceTotal, $clientAccountId);
    }

    // عند تغيير قيمة الفائدة، احسب النسبة المئوية
    public function updatedInterestValue($value)
    {
        $baseAmount = $this->totalAmount - $this->downPayment;
        if ($baseAmount > 0 && $value > 0) {
            $this->interestPercentage = round(($value / $baseAmount) * 100, 2);
        } elseif ($value == 0) {
            $this->interestPercentage = 0;
        }
        $this->calculateInstallments();
    }

    // عند تغيير النسبة المئوية، احسب قيمة الفائدة
    public function updatedInterestPercentage($value)
    {
        $baseAmount = $this->totalAmount - $this->downPayment;
        if ($baseAmount > 0 && $value > 0) {
            $this->interestValue = round(($baseAmount * $value) / 100, 2);
        } elseif ($value == 0) {
            $this->interestValue = 0;
        }
        $this->calculateInstallments();
    }

    // عند تغيير المبلغ الإجمالي، أعد حساب قيمة الفائدة من النسبة
    public function updatedTotalAmount($value)
    {
        if ($this->interestPercentage > 0) {
            $baseAmount = $value - $this->downPayment;
            if ($baseAmount > 0) {
                $this->interestValue = round(($baseAmount * $this->interestPercentage) / 100, 2);
            }
        }
        $this->calculateInstallments();
    }

    // عند تغيير الدفعة الأولى، أعد حساب قيمة الفائدة من النسبة
    public function updatedDownPayment($value)
    {
        if ($this->interestPercentage > 0) {
            $baseAmount = $this->totalAmount - $value;
            if ($baseAmount > 0) {
                $this->interestValue = round(($baseAmount * $this->interestPercentage) / 100, 2);
            }
        }
        $this->calculateInstallments();
    }

    public function mount($invoiceTotal = 0, $clientAccountId = null, $paidAmount = 0)
    {
        $this->invoiceTotal = $invoiceTotal;
        $paidAmount = floatval($paidAmount);
        $this->clientAccountId = $clientAccountId;
        $this->accHeadId = $clientAccountId;
        $this->startDate = Carbon::now()->format('Y-m-d\TH:i');

        // Calculate remaining amount from invoice
        $remainingFromInvoice = $this->invoiceTotal - $paidAmount;
        $this->totalAmount = $remainingFromInvoice > 0 ? $remainingFromInvoice : $invoiceTotal;

        if ($this->accHeadId) {
            $this->updateAccountBalance();
        }

        $this->calculateInstallments();
    }

    public function updateFromInvoice($invoiceTotal, $clientAccountId, $paidAmount = 0)
    {
        $this->invoiceTotal = floatval($invoiceTotal);
        $paidAmount = floatval($paidAmount);
        $this->clientAccountId = $clientAccountId;
        $this->accHeadId = $clientAccountId;

        // Calculate remaining amount from invoice (invoice total - paid amount)
        $remainingFromInvoice = $this->invoiceTotal - $paidAmount;
        $this->totalAmount = $remainingFromInvoice > 0 ? $remainingFromInvoice : 0;

        // Reset down payment when updating from invoice
        $this->downPayment = 0;

        if ($this->accHeadId) {
            $this->updateAccountBalance();
        }

        $this->calculateInstallments();
    }

    public function updated($propertyName)
    {
        if ($propertyName === 'accHeadId') {
            $this->updateAccountBalance();
        }

        if ($propertyName === 'totalAmount' && $this->accHeadId) {
            $this->updateAccountBalance();

            // Check if amount exceeds available balance
            if ($this->totalAmount > $this->availableBalance) {
                $this->dispatch('amount-exceeds-balance', [
                    'amount' => $this->totalAmount,
                    'balance' => $this->availableBalance,
                    'accountBalance' => $this->accountBalance,
                    'existingPlansTotal' => $this->totalInstallmentPlans,
                    'existingPlansCount' => count($this->existingPlans),
                ]);
            }
        }

        $this->calculateInstallments();
    }

    public function updateAccountBalance()
    {
        if ($this->accHeadId) {
            $account = AccHead::find($this->accHeadId);
            $this->accountBalance = $account ? $account->calculateCurrentBalance() : 0;

            // Get existing installment plans for this account
            $this->existingPlans = InstallmentPlan::where('acc_head_id', $this->accHeadId)
                ->where('status', '!=', 'cancelled')
                ->select('id', 'total_amount', 'status', 'created_at')
                ->get()
                ->toArray();

            // Calculate total amount in existing plans
            $this->totalInstallmentPlans = InstallmentPlan::where('acc_head_id', $this->accHeadId)
                ->where('status', '!=', 'cancelled')
                ->sum('total_amount');

            // Calculate available balance
            $this->availableBalance = $this->accountBalance - $this->totalInstallmentPlans;
            $this->showBalanceWarning = $this->availableBalance <= 0;
        } else {
            $this->accountBalance = 0;
            $this->totalInstallmentPlans = 0;
            $this->availableBalance = 0;
            $this->existingPlans = [];
            $this->showBalanceWarning = false;
        }
    }

    public function calculateInstallments()
    {
        $this->totalAmount = floatval($this->totalAmount) > 0 ? floatval($this->totalAmount) : 0;
        $this->downPayment = floatval($this->downPayment) > 0 ? floatval($this->downPayment) : 0;
        $this->interestValue = floatval($this->interestValue) > 0 ? floatval($this->interestValue) : 0;
        $this->numberOfInstallments = intval($this->numberOfInstallments) > 0 ? intval($this->numberOfInstallments) : 1;

        // استخدم قيمة الفائدة مباشرة (تم حسابها من النسبة أو القيمة)
        $interestAmount = $this->interestValue;

        $this->amountToBeInstalled = $this->totalAmount - $this->downPayment + $interestAmount;

        if ($this->amountToBeInstalled > 0 && $this->numberOfInstallments > 0) {
            $this->installmentAmount = round($this->amountToBeInstalled / $this->numberOfInstallments, 2);
        } else {
            $this->installmentAmount = 0;
        }
    }

    public function save()
    {
        $this->validate([
            'accHeadId' => 'required|exists:acc_head,id',
            'totalAmount' => 'required|numeric|min:0',
            'downPayment' => 'required|numeric|min:0|lte:totalAmount',
            'numberOfInstallments' => 'required|integer|min:1',
            'startDate' => 'required|date',
            'intervalType' => 'required|in:monthly,daily',
        ]);

        $this->calculateInstallments();

        try {
            DB::beginTransaction();

            $plan = InstallmentPlan::create([
                'acc_head_id' => $this->accHeadId,
                'invoice_id' => $this->invoiceId,
                'total_amount' => $this->totalAmount,
                'down_payment' => $this->downPayment,
                'amount_to_be_installed' => $this->amountToBeInstalled,
                'number_of_installments' => $this->numberOfInstallments,
                'start_date' => $this->startDate,
                'interval_type' => $this->intervalType,
            ]);

            $dueDate = Carbon::parse($this->startDate);
            $remainingAmount = $this->amountToBeInstalled;

            for ($i = 1; $i <= $this->numberOfInstallments; $i++) {
                // توزيع المبلغ المتبقي على آخر قسط لضمان عدم وجود فرق كسور
                $currentInstallmentAmount = ($i === $this->numberOfInstallments) ? $remainingAmount : $this->installmentAmount;

                $plan->payments()->create([
                    'installment_number' => $i,
                    'amount_due' => $currentInstallmentAmount,
                    'due_date' => $dueDate->format('Y-m-d H:i:s'),
                    'status' => 'pending',
                ]);

                $remainingAmount -= $currentInstallmentAmount;

                if ($this->intervalType == 'monthly') {
                    $dueDate->addMonth();
                } else {
                    $dueDate->addDay();
                }
            }

            // Create journal entry for down payment if exists
            if ($this->downPayment > 0) {
                $this->createDownPaymentJournalEntry($plan, $this->downPayment, $this->startDate);
            }

            DB::commit();

            $this->dispatch('installment-created', [
                'title' => __('installments::installments.saved_successfully'),
                'text' => __('installments::installments.installment_plan_created_successfully'),
                'planId' => $plan->id,
            ]);

            $this->dispatch('close-installment-modal');
        } catch (\Exception) {
            DB::rollBack();

            $this->dispatch('validation-error', [
                'title' => __('installments::installments.error'),
                'text' => __('installments::installments.error_creating_plan'),
            ]);
        }
    }

    public function render()
    {
        $clients = $this->getClientAccounts();

        return view('installments::livewire.create-installment-from-invoice', [
            'clients' => $clients,
            'existingPlans' => $this->existingPlans,
        ]);
    }

    /**
     * Get client accounts from acc_head tree (code 1103)
     */
    private function getClientAccounts()
    {
        return AccHead::where('isdeleted', 0)
            ->where('is_basic', 0)
            ->where('code', 'like', '1103%')
            ->select('id', 'aname', 'code')
            ->get();
    }

    /**
     * Create journal entry for down payment
     */
    private function createDownPaymentJournalEntry(InstallmentPlan $plan, float $amount, string $date)
    {
        try {
            // Get the cash/bank account
            $cashAccount = AccHead::where('code', 'like', '1101%')
                ->where('is_basic', 0)
                ->where('isdeleted', 0)
                ->first();

            if (!$cashAccount) {
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
                'details' => __('installments::installments.down_payment') . ' - ' . __('installments::installments.plan_number') . " {$plan->id}",
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
                'pro_type' => 1,
                'details' => __('installments::installments.down_payment') . ' - ' . __('installments::installments.plan_number') . " {$plan->id}",
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
                'info' => __('installments::installments.down_payment') . ' - ' . __('installments::installments.plan_number') . " {$plan->id}",
                'branch_id' => $operHead->branch_id,
                'isdeleted' => 0,
            ]);

            JournalDetail::create([
                'journal_id' => $journalId,
                'op_id' => $operHead->id,
                'account_id' => $plan->acc_head_id,
                'debit' => 0,
                'credit' => $amount,
                'type' => 1,
                'info' => __('installments::installments.down_payment') . ' - ' . __('installments::installments.plan_number') . " {$plan->id}",
                'branch_id' => $operHead->branch_id,
                'isdeleted' => 0,
            ]);
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
