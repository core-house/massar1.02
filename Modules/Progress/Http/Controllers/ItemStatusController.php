<?php

namespace Modules\Progress\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Progress\Models\ItemStatus;
use RealRashid\SweetAlert\Facades\Alert;

class ItemStatusController extends Controller
{
    public function index()
    {
        $statuses = ItemStatus::orderBy('order')->orderBy('name')->get();
        return view('progress::item-statuses.index', compact('statuses'));
    }

    public function create()
    {
        return view('progress::item-statuses.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:item_statuses,name',
            'color' => 'nullable|string|max:50',
            'icon' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'order' => 'integer|min:0',
            'is_active' => 'boolean',
        ]);

        ItemStatus::create($request->all());

        Alert::toast(__('general.created_successfully'), 'success');
        return redirect()->route('item-statuses.index');
    }

    public function edit(ItemStatus $itemStatus)
    {
        return view('progress::item-statuses.edit', compact('itemStatus'));
    }

    public function update(Request $request, ItemStatus $itemStatus)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:item_statuses,name,' . $itemStatus->id,
            'color' => 'nullable|string|max:50',
            'icon' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'order' => 'integer|min:0',
            'is_active' => 'boolean',
        ]);

        $itemStatus->update($request->all());

        Alert::toast(__('general.updated_successfully'), 'success');
        return redirect()->route('item-statuses.index');
    }

    public function destroy(ItemStatus $itemStatus)
    {
        $itemStatus->delete();
        Alert::toast(__('general.deleted_successfully'), 'success');
        return redirect()->route('item-statuses.index');
    }
}
