<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\JournalDetail;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Modules\Accounts\Models\AccHead;

class ClientPortalController extends Controller
{
    public function login(): View
    {
        return view('portal.login');
    }

    public function authenticate(Request $request): RedirectResponse
    {
        $request->validate([
            'phone'    => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        // العملاء في acc_head تحت كود 1103، لديهم phone + portal_password
        $account = AccHead::withoutGlobalScopes()
            ->where('phone', $request->phone)
            ->whereNotNull('portal_password')
            ->where(function ($q) {
                $q->where('code', 'like', '1103%');
            })
            ->first();

        if (! $account || $account->portal_password !== $request->password) {
            return back()->withErrors(['phone' => __('portal.invalid_credentials')])->withInput();
        }

        session(['portal_account_id' => $account->id]);

        return redirect()->route('portal.statement');
    }

    public function statement(Request $request): View|RedirectResponse
    {
        if (! session('portal_account_id')) {
            return redirect()->route('portal.login');
        }

        $account = AccHead::withoutGlobalScopes()->findOrFail(session('portal_account_id'));

        $fromDate = $request->input('from_date', now()->startOfMonth()->toDateString());
        $toDate   = $request->input('to_date', now()->endOfMonth()->toDateString());

        $balanceBefore = JournalDetail::where('account_id', $account->id)
            ->whereDate('crtime', '<', $fromDate)
            ->sum('debit')
            - JournalDetail::where('account_id', $account->id)
            ->whereDate('crtime', '<', $fromDate)
            ->sum('credit');

        $movements = JournalDetail::where('account_id', $account->id)
            ->whereDate('crtime', '>=', $fromDate)
            ->whereDate('crtime', '<=', $toDate)
            ->orderBy('crtime')
            ->paginate(50);

        return view('portal.statement', compact('account', 'movements', 'balanceBefore', 'fromDate', 'toDate'));
    }

    public function logout(): RedirectResponse
    {
        session()->forget('portal_account_id');

        return redirect()->route('portal.login');
    }
}
