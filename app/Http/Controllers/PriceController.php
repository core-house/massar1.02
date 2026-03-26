<?php

namespace App\Http\Controllers;

use App\Models\Price;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class PriceController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:view prices')->only(['index']);
        $this->middleware('can:create prices')->only(['create', 'store']);
        $this->middleware('can:edit prices')->only(['edit', 'update']);
        $this->middleware('can:delete prices')->only(['destroy']);
    }


    // عرض جميع قوائم الأسعار
    public function index()
    {
        return view('item-management.prices.manage-prices');
    }

    // عرض نموذج إضافة قائمة أسعار جديدة
    public function create()
    {
        return view('prices.create');
    }

    // حفظ قائمة أسعار جديدة
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:prices,name',
        ]);

        Price::create($validated);

        return redirect()->route('prices.index')->with('success', 'تم إضافة قائمة الأسعار بنجاح');
    }

    // عرض نموذج تعديل قائمة أسعار موجودة
    public function edit($id)
    {
        $price = Price::findOrFail($id);
        return view('prices.edit', compact('price'));
    }

    // تحديث قائمة أسعار
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:prices,name,' . $id,
        ]);

        $price = Price::findOrFail($id);
        $price->update($validated);

        return redirect()->route('prices.index')->with('success', 'تم تحديث قائمة الأسعار بنجاح');
    }

    // حذف قائمة أسعار
    public function destroy($id)
    {
        $price = Price::findOrFail($id);
        $price->delete();

        return redirect()->route('prices.index')->with('success', 'تم حذف قائمة الأسعار بنجاح');
    }
}
