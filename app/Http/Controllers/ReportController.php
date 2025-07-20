<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AccHead;
use App\Models\User;
use App\Models\OperHead;

class ReportController extends Controller
{
    public function index()
    {
        return view('reports.index');
    }
    public function overall()
    {
        $opers = \App\Models\OperHead::with('user')
            ->orderBy('created_at', 'desc')
            ->take(100) // عرض آخر 100 عملية
            ->get();

        return view('reports.overall', compact('opers'));
    }

    // accounts tree
    public function accountsTree()
    {

        $accounts = AccHead::where('parent_id', 0)->get();
        return view('reports.accounts-tree', compact('accounts'));
    }

    // accounts balance
    public function accountsBalance()
    {
        $accounts = AccHead::where('parent_id', 0)->get();
        return view('reports.accounts-balance', compact('accounts'));
    }
}
