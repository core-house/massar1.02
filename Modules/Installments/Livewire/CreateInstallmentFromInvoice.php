<?php

declare(strict_types=1);

namespace Modules\Installments\Livewire;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
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

    public function mount($invoiceTotal = 0, $clientAccountId = null)
    {
        $this->invoiceTotal = $invoiceTotal;
        $this->totalAmount = $invoiceTotal;
        $this->clientAccountId = $clientAccountId;
        $this->accHeadId = $clientAccountId;
        $this->startDate = Carbon::now()->format('Y-m-d');

        if ($this->accHeadId) {
            $this->updateAccountBalance();
        }

        $this->calculateInstallments();
    }

    public function updateFromInvoice($invoiceTotal, $clientAccountId)
    {
        $this->invoiceTotal = floatval($invoiceTotal);
        $this->totalAmount = floatval($invoiceTotal);
        $this->clientAccountId = $clientAccountId;
        $this->accHeadId = $clientAccountId;

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
        $this->numberOfInstallments = intval($this->numberOfInstallments) > 0 ? intval($this->numberOfInstallments) : 1;

        $this->amountToBeInstalled = $this->totalAmount - $this->downPayment;

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
                    'due_date' => $dueDate->format('Y-m-d'),
                    'status' => 'pending',
                ]);

                $remainingAmount -= $currentInstallmentAmount;

                if ($this->intervalType == 'monthly') {
                    $dueDate->addMonth();
                } else {
                    $dueDate->addDay();
                }
            }

            DB::commit();

            $this->dispatch('installment-created', [
                'title' => __('Saved Successfully'),
                'text' => __('Installment plan created successfully'),
                'planId' => $plan->id,
            ]);

            $this->dispatch('close-installment-modal');
        } catch (\Exception ) {
            DB::rollBack();

            $this->dispatch('validation-error', [
                'title' => __('Error'),
                'text' => __('An error occurred while creating the installment plan: '),
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
}
