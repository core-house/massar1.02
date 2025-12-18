<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Enums\ItemType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ItemSearchController extends Controller
{
    /**
     * ✅ تم استبدال هذا method بـ Livewire method (searchItems) في CreateInvoiceForm
     * Method محسّن يستخدم eager loading و caching
     * 
     * @deprecated استخدام CreateInvoiceForm::searchItems() بدلاً منه
     */
    // public function search(Request $request) { ... } // ✅ تم حذفه - استخدم Livewire method بدلاً منه

    public function getItemDetails($id)
    {
        $item = Item::with(['units', 'prices', 'barcodes'])
            ->where('id', $id)
            ->where('isdeleted', 0)
            ->first();

        if (!$item) {
            return response()->json(['error' => 'Item not found'], 404);
        }

        return response()->json([
            'id' => $item->id,
            'name' => $item->name,
            'code' => $item->code,
            'units' => $item->units,
            'prices' => $item->prices,
            'barcodes' => $item->barcodes
        ]);
    }
}
