<?php

namespace Modules\Installments\Livewire;

use Carbon\Carbon;
use App\Models\Client;
use Illuminate\Support\Facades\{Auth, DB};
use Livewire\Component;
use App\Models\{JournalDetail, JournalHead, OperHead};
use Modules\Accounts\Models\AccHead;
use Modules\Installments\Models\InstallmentPlan;

class CreateInstallmentPlan extends Component
{
    public $clientId;
    public $accHeadId;
    public $accountBalance = 0;
    public $totalInstallmentPlans = 0;
    public $availableBalance = 0;
    public $existingPlans = [];
    public $showBalanceWarning = false;
    public $totalAmount = 10000;
    public $downPayment = 0;
    public $interestValue = 0;
    public $interestPercentage = 0;
    public $interestType = 'fixed'; // 'fixed' or 'percentage'
    public $amountToBeInstalled = 10000;
    public $numberOfInstallments = 1;
    public $installmentAmount = 10000;
    public $startDate;
    public $intervalType = 'monthly';

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

    public function mount()
    {
        $this->startDate = Carbon::now()->format('Y-m-d\TH:i');
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

        // Amount to be installed = Total - Down Payment + Interest
        $this->amountToBeInstalled = $this->totalAmount - $this->downPayment + $interestAmount;

        if ($this->amountToBeInstalled > 0 && $this->numberOfInstallments > 0) {
            $this->installmentAmount = round($this->amountToBeInstalled / $this->numberOfInstallments, 2);
        } else {
            $this->installmentAmount = 0;
        }
    }

    public function save()
    {
        // Get account balance for validation
        $account = AccHead::find($this->accHeadId);
        $accountBalance = $account ? $account->calculateCurrentBalance() : 0;

        // Calculate existing plans total
        $existingPlansTotal = InstallmentPlan::where('acc_head_id', $this->accHeadId)
            ->where('status', '!=', 'cancelled')
            ->sum('total_amount');

        $availableBalance = $accountBalance - $existingPlansTotal;

        // Get existing plans for error message
        $existingPlans = InstallmentPlan::where('acc_head_id', $this->accHeadId)
            ->where('status', '!=', 'cancelled')
            ->get();

        // Check if amount exceeds available balance
        if ($this->totalAmount > $availableBalance) {
            $currency = __('installments::installments.sar');
            $errorMessage = __('installments::installments.requested_amount_greater_than_balance', [
                'amount' => number_format($this->totalAmount, 2) . ' ' . $currency,
                'balance' => number_format($availableBalance, 2) . ' ' . $currency
            ]);

            if ($existingPlans->count() > 0) {
                $errorMessage .= "\n\n" . __('installments::installments.account_details') . ':';
                $errorMessage .= "\n" . '• ' . __('installments::installments.total_balance') . ': ' . number_format($accountBalance, 2) . ' ' . $currency;
                $errorMessage .= "\n" . '• ' . __('installments::installments.existing_plans_count') . ': ' . $existingPlans->count();
                $errorMessage .= "\n" . '• ' . __('installments::installments.existing_plans_total') . ': ' . number_format($existingPlansTotal, 2) . ' ' . $currency;
                $errorMessage .= "\n" . '• ' . __('installments::installments.available_for_installments') . ': ' . number_format($availableBalance, 2) . ' ' . $currency;
            }

            $this->dispatch('validation-error', [
                'title' => __('installments::installments.installment_amount_error'),
                'text' => $errorMessage,
                'html' => true,
            ]);
            return;
        }

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

            session()->flash('message', __('installments::installments.installment_plan_created_successfully'));

            $this->dispatch('save-success', [
                'title' => __('installments::installments.saved_successfully'),
                'text' => __('installments::installments.installment_plan_created_successfully'),
                'planId' => $plan->id,
            ]);
        } catch (\Exception $e) {
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
        return view('installments::livewire.create-installment-plan', [
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
