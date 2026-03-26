<?php

declare(strict_types=1);

namespace Modules\Settings\Http\Controllers;

use Illuminate\Routing\Controller;
use Modules\Settings\Models\Currency;
use Illuminate\Support\Facades\{Auth, DB};
use Modules\Accounts\Models\AccHead;
use Modules\Settings\Http\Requests\CurrencyExchangeRequest;
use App\Models\{CostCenter, JournalDetail, JournalHead, OperHead};

class CurrencyExchangeController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view Currency Exchange')->only('index');
        $this->middleware('permission:create Currency Exchange')->only(['create', 'store']);
        $this->middleware('permission:edit Currency Exchange')->only(['edit', 'update']);
        $this->middleware('permission:delete Currency Exchange')->only('destroy');
    }

    public function index()
    {
        $exchanges = OperHead::whereIn('pro_type', [80, 81])
            ->where('isdeleted', 0)
            ->with(['acc1Head', 'acc2Head', 'currency', 'user'])
            ->orderByDesc('pro_date')
            ->get();

        return view('settings::currency-exchange.index', compact('exchanges'));
    }

    public function create()
    {
        // Get all cash accounts (code starts with 1101)
        $cashAccounts = AccHead::where('isdeleted', 0)
            ->where('is_basic', 0)
            ->where('code', 'like', '1101%')
            ->with('currency:id,name,symbol')
            ->select('id', 'aname', 'balance', 'currency_id', 'code')
            ->orderBy('code')
            ->get();

        // Get all active currencies
        $currencies = Currency::active()
            ->with('latestRate')
            ->get();

        // Get branches
        $branches = userBranches();

        // Get cost centers
        $costCenters = CostCenter::where('deleted', 0)->get();

        // Determine next pro_id for buy operation (we'll use 80 as default)
        $lastProIdBuy = OperHead::where('pro_type', 80)->max('pro_id') ?? 0;
        $newProIdBuy = $lastProIdBuy + 1;

        $lastProIdSell = OperHead::where('pro_type', 81)->max('pro_id') ?? 0;
        $newProIdSell = $lastProIdSell + 1;

        return view('settings::currency-exchange.create', compact(
            'cashAccounts',
            'currencies',
            'branches',
            'costCenters',
            'newProIdBuy',
            'newProIdSell'
        ));
    }

    /**
     * Store a newly created currency exchange in storage.
     */
    public function store(CurrencyExchangeRequest $request)
    {
        // نحصل على البيانات التي تم التحقق منها فقط
        $validated = $request->validated();

        // Check if multi-currency is enabled
        if (!isMultiCurrencyEnabled()) {
            return back()->withErrors(['error' => __('Multi-currency system is not enabled.')])->withInput();
        }

        // Check if accounts are valid (Extra safety check, though Validation handles exists)
        $acc1 = AccHead::find($validated['acc1']);

        // Validation: The selected currency must match the currency of the receiving account (acc1)
        if ($acc1->currency_id != $validated['currency_id']) {
            return back()->withErrors([
                'currency_mismatch' => __('The selected currency must match the currency of the receiving account (To Fund).')
            ])->withInput();
        }

        // Calculate base value (after multiplying by exchange rate)
        $baseValue = (float) $validated['pro_value'] * (float) $validated['currency_rate'];

        try {
            DB::beginTransaction();

            // Determine new operation ID based on pro_type
            $pro_type = (int) $validated['operation_type'];
            $lastProId = OperHead::where('pro_type', $pro_type)->max('pro_id') ?? 0;
            $newProId = $lastProId + 1;

            // Create new record in operhead
            $oper = OperHead::create([
                'pro_id' => $newProId,
                'pro_date' => $validated['pro_date'],
                'pro_type' => $pro_type,
                'acc1' => $validated['acc1'],
                'acc2' => $validated['acc2'],
                'pro_value' => $baseValue,
                'currency_id' => $validated['currency_id'],
                'currency_rate' => $validated['currency_rate'],
                'details' => $validated['details'] ?? null,
                'pro_num' => $validated['pro_num'] ?? null,
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
                'cost_center' => $validated['cost_center'] ?? null,
                'user' => Auth::id(),
                'branch_id' => $validated['branch_id'],
            ]);

            // Create Journal Header (JournalHead)
            $lastJournalId = JournalHead::max('journal_id') ?? 0;
            $newJournalId = $lastJournalId + 1;
            $journalHead = JournalHead::create([
                'journal_id' => $newJournalId,
                'total' => $baseValue,
                'date' => $validated['pro_date'],
                'op_id' => $oper->id,
                'pro_type' => $pro_type,
                'details' => $validated['details'] ?? null,
                'user' => Auth::id(),
                'branch_id' => $validated['branch_id'],
            ]);

            // Create Journal Details (Debit)
            JournalDetail::create([
                'journal_id' => $journalHead->journal_id,
                'account_id' => $validated['acc1'],
                'debit' => $baseValue,
                'credit' => 0,
                'type' => 0,
                'info' => $validated['details'] ?? null,
                'op_id' => $oper->id,
                'isdeleted' => 0,
                'branch_id' => $validated['branch_id'],
            ]);

            // Create Journal Details (Credit)
            JournalDetail::create([
                'journal_id' => $journalHead->journal_id,
                'account_id' => $validated['acc2'],
                'debit' => 0,
                'credit' => $baseValue,
                'type' => 1,
                'info' => $validated['details'] ?? null,
                'op_id' => $oper->id,
                'isdeleted' => 0,
                'branch_id' => $validated['branch_id'],
            ]);

            DB::commit();

            return redirect()->route('settings.currency-exchange.index')
                ->with('success', __('Currency exchange operation saved successfully.'));
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->withErrors(['error' => __('An error occurred while saving: ')])
                ->withInput();
        }
    }

    /**
     * Show the form for editing the specified currency exchange.
     */
    public function edit($id)
    {
        $exchange = OperHead::whereIn('pro_type', [80, 81])
            ->findOrFail($id);

        // Get all cash accounts (code starts with 1101)
        $cashAccounts = AccHead::where('isdeleted', 0)
            ->where('is_basic', 0)
            ->where('code', 'like', '1101%')
            ->with('currency:id,name,symbol')
            ->select('id', 'aname', 'balance', 'currency_id', 'code')
            ->orderBy('code')
            ->get();

        // Get all active currencies
        $currencies = Currency::active()
            ->with('latestRate')
            ->get();

        // Get branches
        $branches = userBranches();

        // Get cost centers
        $costCenters = CostCenter::where('deleted', 0)->get();

        return view('settings::currency-exchange.edit', compact(
            'exchange',
            'cashAccounts',
            'currencies',
            'branches',
            'costCenters'
        ));
    }

    /**
     * Update the specified currency exchange in storage.
     */
    public function update(CurrencyExchangeRequest $request, $id)
    {
        $exchange = OperHead::whereIn('pro_type', [80, 81])->findOrFail($id);

        $validated = $request->validated();

        // Check if multi-currency is enabled
        if (!isMultiCurrencyEnabled()) {
            return back()->withErrors(['error' => __('Multi-currency system is not enabled.')])->withInput();
        }

        // Check if currency matches the receiving account
        $acc1 = AccHead::find($validated['acc1']);

        if ($acc1->currency_id != $validated['currency_id']) {
            return back()->withErrors([
                'currency_mismatch' => __('The selected currency must match the currency of the receiving account (To Fund).')
            ])->withInput();
        }

        // Calculate base value
        $baseValue = (float) $validated['pro_value'] * (float) $validated['currency_rate'];

        try {
            DB::beginTransaction();

            // Update operhead
            $exchange->update([
                'pro_date' => $validated['pro_date'],
                'pro_type' => (int) $validated['operation_type'],
                'pro_num' => $validated['pro_num'] ?? null,
                'acc1' => $validated['acc1'],
                'acc2' => $validated['acc2'],
                'pro_value' => $baseValue,
                'currency_id' => $validated['currency_id'],
                'currency_rate' => $validated['currency_rate'],
                'details' => $validated['details'] ?? null,
                'cost_center' => $validated['cost_center'] ?? null,
                'user' => Auth::id(),
                'branch_id' => $validated['branch_id'],
            ]);

            // Update journal_head
            $journalHead = JournalHead::where('op_id', $exchange->id)->first();
            if ($journalHead) {
                $journalHead->update([
                    'total' => $baseValue,
                    'date' => $validated['pro_date'],
                    'pro_type' => (int) $validated['operation_type'],
                    'details' => $validated['details'] ?? null,
                    'user' => Auth::id(),
                    'branch_id' => $validated['branch_id'],
                ]);

                $journalId = $journalHead->journal_id;

                // Delete old details
                JournalDetail::where('journal_id', $journalId)->delete();

                // Create new details (Debit)
                JournalDetail::create([
                    'journal_id' => $journalId,
                    'account_id' => $validated['acc1'],
                    'debit' => $baseValue,
                    'credit' => 0,
                    'type' => 0,
                    'info' => $validated['details'] ?? null,
                    'op_id' => $exchange->id,
                    'isdeleted' => 0,
                    'branch_id' => $validated['branch_id'],
                ]);

                // Create new details (Credit)
                JournalDetail::create([
                    'journal_id' => $journalId,
                    'account_id' => $validated['acc2'],
                    'debit' => 0,
                    'credit' => $baseValue,
                    'type' => 1,
                    'info' => $validated['details'] ?? null,
                    'op_id' => $exchange->id,
                    'isdeleted' => 0,
                    'branch_id' => $validated['branch_id'],
                ]);
            }

            DB::commit();

            return redirect()->route('settings.currency-exchange.index')
                ->with('success', __('Currency exchange operation updated successfully.'));
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->withErrors(['error' => __('An error occurred: ')])
                ->withInput();
        }
    }

    /**
     * Remove the specified currency exchange from storage.
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $exchange = OperHead::whereIn('pro_type', [80, 81])->findOrFail($id);

            // Delete associated journal_head
            $journalHead = JournalHead::where('op_id', $exchange->id)->first();

            if ($journalHead) {
                // Delete journal details
                JournalDetail::where('journal_id', $journalHead->journal_id)->delete();

                // Delete journal head
                $journalHead->delete();
            }

            // Delete the operation
            $exchange->delete();

            DB::commit();

            return redirect()->route('settings.currency-exchange.index')
                ->with('success', __('Currency exchange operation deleted successfully.'));
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->withErrors(['error' => __('An error occurred while deleting: ')]);
        }
    }
}
