<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JournalHead;
use App\Models\JournalDetail;
use Illuminate\Routing\Controller;


class JournalSummeryController extends Controller

{
   public function __construct()
{
    $this->middleware('can:عرض قيود يوميه حسابات')->only(['index']);
}

    public function index()
    {
        $journalHeads = JournalHead::with('dets')->get();
        return view('journals.journal-summery', compact('journalHeads'));
    }
}
