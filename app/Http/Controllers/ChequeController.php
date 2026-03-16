<?php

namespace App\Http\Controllers;

use App\Models\Cheque;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class ChequeController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:view Checks')->only(['index', 'show']);
        $this->middleware('can:create Checks')->only(['create', 'store']);
        $this->middleware('can:edit Checks')->only(['edit', 'update']);
        $this->middleware('can:delete Checks')->only(['destroy']);
    }

    public function index()
    {
        // $cheques = Cheque::all();
        return view('cheques.index');
    }

    public function create()
    {
        return view('cheques.create');
    }

    public function store(Request $request)
    {
        // تحقق من البيانات
        $cheque = Cheque::create($request->all());
        return redirect()->route('cheques.index')->with('success', 'Cheque created successfully');
    }

    public function show($id)
    {
        $cheque = Cheque::findOrFail($id);
        return view('cheques.show', compact('cheque'));
    }

    public function edit($id)
    {
        $cheque = Cheque::findOrFail($id);
        return view('cheques.edit', compact('cheque'));
    }

    public function update(Request $request, $id)
    {
        $cheque = Cheque::findOrFail($id);
        $cheque->update($request->all());
        return redirect()->route('cheques.index')->with('success', 'Cheque updated successfully');
    }

    public function destroy($id)
    {
        $cheque = Cheque::findOrFail($id);
        $cheque->delete();
        return redirect()->route('cheques.index')->with('success', 'Cheque deleted successfully');
    }
}
