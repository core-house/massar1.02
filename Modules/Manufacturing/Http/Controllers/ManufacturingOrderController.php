<?php

namespace Modules\Manufacturing\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class ManufacturingOrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view Manufacturing Orders')->only(['index', 'show']);
        $this->middleware('permission:create Manufacturing Orders')->only(['create', 'store']);
        $this->middleware('permission:edit Manufacturing Orders')->only(['edit', 'update']);
        $this->middleware('permission:delete Manufacturing Orders')->only('destroy');
    }

    public function index()
    {
        return view('manufacturing::manufacturing-order.index');
    }

    public function create()
    {
        return view('manufacturing::manufacturing-order.create');
    }

    public function store(Request $request)
    {
        // Handled by Livewire component
    }

    public function show($id)
    {
        return view('manufacturing::manufacturing-order.show', ['order_id' => $id]);
    }

    public function edit($id)
    {
        return view('manufacturing::manufacturing-order.edit', ['order_id' => $id]);
    }

    public function update(Request $request, $id)
    {
        // Handled by Livewire component
    }

    public function destroy($id)
    {
        // Handled by Livewire component
    }
}
