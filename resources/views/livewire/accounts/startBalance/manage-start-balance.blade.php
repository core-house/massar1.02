<?php

use Livewire\Volt\Component;
use App\Models\AccHead;
use Illuminate\Support\Facades\DB;
use App\Models\OperHead;
use App\Models\JournalHead;
use App\Models\JournalDetail;
use Modules\Settings\Models\PublicSetting;

new class extends Component {
    public $accounts;
    public $accountsTypes = [
        'client' => '122%',
        'supplier' => '211%',
        'fund' => '121%',
        'bank' => '124%',
        'expense' => '44%',
        'revenue' => '32%',
        'creditor' => '212%',
        'debtor' => '125%',
        'partner' => '221%',
        'asset' => '11%',
        'employee' => '213%',
        'rentable' => '112%',
        'store' => '123%',
    ];
    public $capitalAccountsTypes = [
        'client' => '122%',
        'supplier' => '211%',
        'fund' => '121%',
        'bank' => '124%',
        // 'expense' => '44%',
        // 'revenue' => '32%',
        'creditor' => '212%',
        'debtor' => '125%',
        // 'partner' => '221%',
        'asset' => '11%',
        'employee' => '213%',
        'rentable' => '112%',
        'store' => '123%',
    ];

    public $current_accounts_opening_balance = [];
    public $new_accounts_opening_balance = [];
    // public $adjustment = [];

    public function mount()
    {
        $this->loadData();
    }

    public function loadData()
    {
        $this->accounts = collect();
        foreach ($this->accountsTypes as $typePattern) {
            $this->accounts = $this->accounts->merge(AccHead::where('code', 'like', $typePattern)->where('is_basic', 0)->get());
        }

        foreach ($this->accounts as $account) {
            $this->current_accounts_opening_balance[$account->id] = (float) $account->start_balance;
            if (!Str::startsWith($account->code, '221') && !Str::startsWith($account->code, '123')) {
                $this->new_accounts_opening_balance[$account->id] = null;
            }
            // $this->adjustment[$account->id] = (float) 0.0;
        }
    }

    public function updateStartBalance()
    {
        if (
            empty(
                array_filter($this->new_accounts_opening_balance, function ($value) {
                    return !is_null($value);
                })
            )
        ) {
            session()->flash('error', 'لم يتم إدخال بيانات للتحديث');
            return;
        }
        try {
            DB::beginTransaction();
            foreach ($this->new_accounts_opening_balance as $accountId => $newBalance) {
                if ($newBalance != null) {
                    $account = AccHead::find($accountId);
                    $accountOldStartBalance = $account->start_balance;
                    $account->start_balance = $newBalance;
                    $account->save();
                    if ($account->parent_id != null || $account->parent_id != 0) {
                        $this->updateParentBalance($account, $accountOldStartBalance, $newBalance);
                    }
                }
            }
            $this->updateCapitalBalance();
            $this->createOperationAndJournal();
            $this->loadData();
            session()->flash('success', 'تم تحديث الرصيد بنجاح');
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'حدث خطأ ما أثناء تحديث الرصيد');
        }
    }

    public function updateParentBalance($account, $accountOldStartBalance, $newBalance)
    {
        $parent = AccHead::findorFail($account->parent_id);
        $parent->start_balance = $parent->start_balance - $accountOldStartBalance + $newBalance;
        $parent->save();
        if ($parent->parent_id != null || $parent->parent_id != 0) {
            $this->updateParentBalance($parent, $accountOldStartBalance, $newBalance);
        }

    }

    public function updateCapitalBalance()
    {
        $accounts = collect();
        foreach ($this->capitalAccountsTypes as $capitalAccountType) {
            $accounts = $accounts->merge(AccHead::where('code', 'like', $capitalAccountType)->where('is_basic', 0)->get());
        }
        $balance = $accounts->sum('start_balance');
        $BasicCapitalAccount = AccHead::where('code', '=', '2211')->first();
        $BasicCapitalAccountOldStartBalance = $BasicCapitalAccount->start_balance;
        $BasicCapitalAccount->update(['start_balance' => -$balance]);
        $BasicCapitalAccount->save();
        $this->updateParentBalance($BasicCapitalAccount, $BasicCapitalAccountOldStartBalance, -$balance);

    }

    public function createOperationAndJournal()
    {
        //create operation
        $oper = OperHead::updateOrCreate(
            ['pro_type' => 61],
            [
            'is_journal' => 1,
            'journal_type' => 1,
            'info' => 'تسجيل الارصده الافتتاحيه للحسابات',
            'pro_date' => PublicSetting::where('key', 'start_date')->first()->value,
            'user' => Auth::id(),
            'pro_type' => 61,
        ]);
        //create journal
        $existingJournal = JournalHead::where('pro_type', 61)->where('op_id', $oper->id)->first();

        if ($existingJournal) {
            $journalId = $existingJournal->journal_id;
        } else {
            $journalId = JournalHead::max('journal_id') + 1 ?? 1;
        }
        JournalHead::updateOrCreate(
            ['journal_id' => $journalId, 'pro_type' => 61],
            [
                'journal_id' => $journalId,
                'total' => array_sum($this->new_accounts_opening_balance),
                'date' => PublicSetting::where('key', 'start_date')->first()->value,
                'op_id' => $oper->id,
                'pro_type' => 61,
                'op2' => $oper->id,
                'user' => Auth::id(),
            ],
        );
        //create journal details
        foreach ($this->new_accounts_opening_balance as $accountId => $newBalance) {
            if ($newBalance != null) {
                if ($newBalance > 0) {
                    JournalDetail::updateOrCreate(
                        ['journal_id' => $journalId, 'credit' => 0, 'account_id' => $accountId],
                        [
                            'journal_id' => $journalId,
                            'account_id' => $accountId,
                            'debit' => $newBalance,
                            'credit' => 0,
                            'type' => 1,
                            'op_id' => $oper->id,
                        ],
                    );
                } elseif ($newBalance < 0) {
                    JournalDetail::updateOrCreate(
                        ['journal_id' => $journalId, 'debit' => 0, 'account_id' => $accountId],
                        [
                            'journal_id' => $journalId,
                            'account_id' => $accountId,
                            'debit' => 0,
                            'credit' => -$newBalance,
                            'type' => 1,
                            'op_id' => $oper->id,
                        ],
                    );
                }
            }
        }
        JournalDetail::updateOrCreate(
            ['journal_id' => $journalId, 'debit' => 0, 'account_id' => 2211],
            [
                'journal_id' => $journalId,
                'account_id' => 2211,
                'debit' => 0,
                'credit' => array_sum($this->new_accounts_opening_balance),
                'type' => 1,
                'op_id' => $oper->id,
            ],
        );
    }
}; ?>

<div style="font-family: 'Cairo', sans-serif; direction: rtl;">
    <div class="row">
        @if (session()->has('success'))
            <div class="alert alert-success" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)">
                {{ session('success') }}
            </div>
        @elseif (session()->has('error'))
            <div class="alert alert-danger" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)">
                {{ session('error') }}
            </div>
        @endif
    </div>

    <div class="row mt-4">
        <div class="col-12">
            <form wire:submit="updateStartBalance" wire:target="updateStartBalance" wire:loading.attr="disabled">
                @csrf
                <table class="table table-bordered table-sm table-striped table-hover">
                    <thead class="table-light">
                        <tr class="text-center">
                            <th style="width: 10%" class="font-family-cairo fw-bold font-14">الكود</th>
                            <th style="width: 20%" class="font-family-cairo fw-bold font-14">الاسم</th>
                            <th style="width: 15%" class="font-family-cairo fw-bold font-14">رصيد اول المده الحالي</th>
                            <th style="width: 15%" class="font-family-cairo fw-bold font-14">رصيد اول المده الجديد</th>
                            {{-- <th style="width: 15%" class="font-family-cairo fw-bold font-14">كميه التسويه</th> --}}
                        </tr>
                    </thead>
                    <tbody id="items_table_body">
                        @foreach ($accounts as $account)
                            <tr data-item-id="{{ $account->id }}">
                                <td>
                                    <p class="font-family-cairo fw-bold font-16 text-center">{{ $account->code }}</p>
                                </td>
                                <td>
                                    <p class="font-family-cairo fw-bold font-16 text-center">{{ $account->aname }}</p>
                                </td>
                                <td>
                                    <p
                                        class="font-family-cairo fw-bold font-16 text-center @if ($current_accounts_opening_balance[$account->id] < 0) text-danger @endif">
                                        {{ number_format($current_accounts_opening_balance[$account->id] ?? 0, 2) }}</p>
                                </td>
                                <td>
                                    @if (!Str::startsWith($account->code, '221') && !Str::startsWith($account->code, '123'))
                                        <input type="number" step="0.01"
                                            wire:model.blur="new_accounts_opening_balance.{{ $account->id }}"
                                            class="form-control form-control-sm new-balance-input font-family-cairo fw-bold font-16
                                        @if ($new_accounts_opening_balance[$account->id] < 0) text-danger @endif"
                                            placeholder="رصيد اول المده الجديد" style="padding:2px;height:30px;"
                                            @click="this.focus()" data-item-id="{{ $account->id }}">
                                        {{-- @else
                                        <p class="font-family-cairo fw-bold font-16 text-center">
                                            {{ $account->start_balance ?? 0 }}
                                        </p> --}}
                                    @endif
                                </td>
                                {{-- <td>
                                    <p class="font-family-cairo fw-bold font-16 text-center @if (($new_accounts_opening_balance[$account->id] ?? 0) - ($current_accounts_opening_balance[$account->id] ?? 0) < 0) text-danger @endif">
                                        {{ number_format(($new_accounts_opening_balance[$account->id] ?? 0) - ($current_accounts_opening_balance[$account->id] ?? 0), 2) }}
                                    </p>
                                </td> --}}
                            </tr>
                        @endforeach

                    </tbody>
                </table>
                <button type="submit" class="btn btn-primary m-3" wire:click="$refresh"
                    wire:target="updateStartBalance" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="updateStartBalance">
                        تحديث
                    </span>
                    <span wire:loading wire:target="updateStartBalance">
                        جاري التحديث...
                    </span>
                </button>
            </form>

        </div>
    </div>
</div>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('form').forEach(function(form) {
                form.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter' && e.target.tagName.toLowerCase() !== 'textarea') {
                        e.preventDefault();
                        const inputs = Array.from(form.querySelectorAll(
                            'input:not([type=hidden]):not([readonly]), select, textarea'
                        ));
                        const idx = inputs.indexOf(e.target);
                        if (idx > -1 && idx < inputs.length - 1) {
                            // انتقل للحقل التالي
                            inputs[idx + 1].focus();
                        } else if (idx === inputs.length - 1) {
                            // إذا كان في آخر حقل، انتقل إلى زر التحديث أو الحفظ
                            const submitBtn = form.querySelector(
                                'button[type="submit"], input[type="submit"]');
                            if (submitBtn) {
                                submitBtn.focus();
                                // عند الضغط على Enter مرة أخرى على الزر، قم بالتحديث أو الحفظ
                                submitBtn.addEventListener('keydown', function handler(ev) {
                                    if (ev.key === 'Enter' || ev.keyCode === 13) {
                                        ev.preventDefault();
                                        submitBtn.click();
                                    }
                                    // إزالة الحدث بعد أول استخدام لمنع التكرار
                                    submitBtn.removeEventListener('keydown', handler);
                                });
                            }
                        }
                    }
                });
            });
        });
    </script>
@endpush
