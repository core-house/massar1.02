<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:عرض الأصناف')->only(['index', 'show']);
        $this->middleware('can:إضافة الأصناف')->only(['create', 'store']);
        $this->middleware('can:تعديل الأصناف')->only(['edit', 'update']);
        $this->middleware('can:حذف الأصناف')->only(['destroy']);
    }
    public function index()
    {
        return view('item-management.items.index');
    }

    public function create()
    {
        return view('item-management.items.create');
    }

    public function store(Request $request)
    {
        //
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        $itemModel = Item::findOrFail($id);
        return view('item-management.items.edit', compact('itemModel'));
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        //
    }

    // 📁 Item Movement
    public function itemMovementReport($itemId = null, $warehouseId = null)
    {
        return view('item-management.reports.item-movement', compact('itemId', 'warehouseId')); // itemId and warehouseId are optional
    }

    // 📁 Item Sales Report
    public function itemSalesReport()
    {
        return view('reports.sales.manage-item-sales');
    }

    // 📁 Item Purchase Report
    public function itemPurchaseReport()
    {
        return view('reports.purchase.manage-item-purchase-report');

        // Get item as JSON for AJAX requests
    }


    public function getItemJson($id)
    {
        $item = Item::with(['units', 'prices'])->findOrFail($id);
        return response()->json($item);
    }

    // 📁 Print Items Report
    public function printItems(Request $request)
    {
        return view('item-management.items.print', [
            'search' => $request->get('search', ''),
            'selectedWarehouse' => $request->get('warehouse', null),
            'selectedGroup' => $request->get('group', null),
            'selectedCategory' => $request->get('category', null),
            'selectedPriceType' => $request->get('priceType', ''),
        ]);
    }

    public function printItemMovement(Request $request)
    {
        return view('item-management.reports.item-movement-print', [
            'itemId' => $request->get('itemId', null),
            'warehouseId' => $request->get('warehouseId', 'all'),
            'fromDate' => $request->get('fromDate', now()->startOfMonth()->toDateString()),
            'toDate' => $request->get('toDate', now()->endOfMonth()->toDateString()),
        ]);
    }
}
