<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Note;
use App\Models\Unit;
use App\Models\Varibal;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class ItemController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:view items')->only(['index', 'show']);
        $this->middleware('can:create items')->only(['create', 'store']);
        $this->middleware('can:edit items')->only(['edit', 'update', 'managePrices']);
        $this->middleware('can:delete items')->only(['destroy']);
        $this->middleware('can:print items')->only(['printItems', 'printItemMovement']);
        $this->middleware('can:view item-statistics')->only(['getStatistics', 'refresh']);

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
        $item = Item::with(['units', 'prices', 'notes', 'barcodes', 'branch'])->findOrFail($id);

        return view('item-management.items.show', compact('item'));
    }

    public function edit($id)
    {
        $itemModel = Item::findOrFail($id);

        return view('item-management.items.edit', compact('itemModel'));
    }

    public function managePrices()
    {
        return view('item-management.items.manage-prices');
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
        return view('reports::purchases.manage-item-purchase-report');

        // Get item as JSON for AJAX requests
    }

    public function getItemJson($id)
    {
        $item = Item::with(['units', 'prices', 'notes'])->findOrFail($id);
        
        // Add images to the response - try item-images first, then fallback to thumbnail
        $images = $item->getMedia('item-images');
        if ($images->isEmpty()) {
            $images = $item->getMedia('item-thumbnail');
        }
        
        $item->images = $images->map(function($media) {
            return [
                'url' => $media->getUrl(),
                'thumb' => $media->hasGeneratedConversion('thumb') ? $media->getUrl('thumb') : $media->getUrl(),
            ];
        });

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
            'operationType' => $request->get('operationType', 'all'),
            'fromDate' => $request->get('fromDate', now()->startOfMonth()->toDateString()),
            'toDate' => $request->get('toDate', now()->endOfMonth()->toDateString()),
        ]);
    }

    public function getStatistics()
    {

        // إجمالي الأصناف
        $totalItems = Item::count();

        // الأصناف حسب النوع
        $itemsByType = Item::select('type', DB::raw('count(*) as count'))
            ->groupBy('type')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->type->label() => $item->count];
            })
            ->toArray();

        // الأصناف ذات المتغيرات
        // $itemsWithVariations = Item::whereHas('varibals')->count();
        // $itemsWithoutVariations = $totalItems - $itemsWithVariations;

        // إجمالي الوحدات والباركود
        $totalUnits = Unit::count();
        $totalBarcodes = DB::table('barcodes')->count();

        // متوسط الوحدات والباركود لكل صنف
        $avgUnitsPerItem = $totalItems > 0
            ? round(DB::table('item_units')->count() / $totalItems, 2)
            : 0;
        $avgBarcodesPerItem = $totalItems > 0
            ? round($totalBarcodes / $totalItems, 2)
            : 0;

        // إجمالي الملاحظات والمتغيرات
        $totalNotes = Note::count();
        $totalVaribals = Varibal::count();

        // أكثر الوحدات استخداماً
        $mostUsedUnits = DB::table('item_units')
            ->join('units', 'item_units.unit_id', '=', 'units.id')
            ->select('units.name', DB::raw('count(*) as usage_count'))
            ->groupBy('units.id', 'units.name')
            ->orderByDesc('usage_count')
            ->limit(5)
            ->get();

        // الأصناف التي تحتوي على أكثر عدد من الوحدات
        $itemsWithMostUnits = Item::withCount('units')
            ->orderByDesc('units_count')
            ->limit(5)
            ->get()
            ->map(function ($item) {
                return [
                    'name' => $item->name,
                    'units_count' => $item->units_count,
                ];
            });

        // أحدث الأصناف المضافة
        $recentItems = Item::latest()
            ->limit(10)
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'name' => $item->name,
                    'code' => $item->code,
                    'type' => $item->type->label(),
                    'created_at' => $item->created_at->format('Y-m-d'),
                ];
            });

        // نطاقات الأسعار
        $priceRanges = DB::table('item_prices')
            ->select(
                DB::raw('CASE
                    WHEN price < 10 THEN "أقل من 10"
                    WHEN price >= 10 AND price < 50 THEN "10 - 50"
                    WHEN price >= 50 AND price < 100 THEN "50 - 100"
                    WHEN price >= 100 AND price < 500 THEN "100 - 500"
                    ELSE "أكثر من 500"
                END as price_range'),
                DB::raw('count(DISTINCT item_id) as count')
            )
            ->groupBy('price_range')
            ->get()
            ->pluck('count', 'price_range')
            ->toArray();

        // إحصائيات التكلفة
        $costStats = DB::table('item_units')
            ->selectRaw('
                MIN(cost) as min_cost,
                MAX(cost) as max_cost,
                AVG(cost) as avg_cost
            ')
            ->first();

        // إحصائيات الأسعار (سعر البيع مقابل سعر الشراء)
        $priceComparison = DB::table('item_units')
            ->join('items', 'item_units.item_id', '=', 'items.id')
            ->leftJoin('item_prices', function ($join) {
                $join->on('item_units.item_id', '=', 'item_prices.item_id')
                    ->on('item_units.unit_id', '=', 'item_prices.unit_id');
            })
            ->select(
                'items.name as item_name',
                'items.code as item_code',
                DB::raw('COALESCE(item_units.cost, 0) as purchase_price'),
                DB::raw('COALESCE(item_prices.price, 0) as sale_price')
            )
            ->orderBy('items.name')
            ->get()
            ->map(function ($item) {
                $purchasePrice = (float) $item->purchase_price;
                $salePrice = (float) $item->sale_price;

                return [
                    'item_name' => $item->item_name,
                    'item_code' => $item->item_code,
                    'purchase_price' => $purchasePrice,
                    'sale_price' => $salePrice,
                    'trend' => $salePrice > $purchasePrice ? 'up' : ($salePrice < $purchasePrice ? 'down' : 'equal'),
                ];
            });

        $statistics = compact(
            'totalItems',
            'itemsByType',
            // 'itemsWithVariations',
            // 'itemsWithoutVariations',
            'totalUnits',
            'totalBarcodes',
            'avgUnitsPerItem',
            'avgBarcodesPerItem',
            'totalNotes',
            'totalVaribals',
            'mostUsedUnits',
            'itemsWithMostUnits',
            'recentItems',
            'priceRanges',
            'costStats',
            'priceComparison'
        );

        return view('item-management.items.items-statistics', $statistics);
    }

    public function refresh()
    {
        return redirect()->route('items.statistics')->with('success', 'تم تحديث الإحصائيات بنجاح!');
    }
}
