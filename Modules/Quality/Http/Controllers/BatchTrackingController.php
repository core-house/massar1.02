<?php

namespace Modules\Quality\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Exceptions\HttpResponseException;
use Modules\Quality\Models\BatchTracking;
use Modules\Quality\Http\Requests\BatchRequest;
use App\Models\Item;
use Modules\Accounts\Models\AccHead;

class BatchTrackingController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:view batches')->only(['index', 'show']);
        $this->middleware('can:create batches')->only(['create', 'store']);
        $this->middleware('can:edit batches')->only(['edit', 'update']);
        $this->middleware('can:delete batches')->only(['destroy']);
    }

    public function index()
    {
        $batches = BatchTracking::with(['item', 'supplier', 'warehouse'])
            ->orderBy('production_date', 'desc')
            ->paginate(20);

        $stats = [
            'total'         => BatchTracking::count(),
            'active'        => BatchTracking::where('status', 'active')->count(),
            'expiring_soon' => BatchTracking::expiringSoon(30)->count(),
            'expired'       => BatchTracking::expired()->count(),
        ];

        return view('quality::batches.index', compact('batches', 'stats'));
    }

    public function create()
    {
        $items      = Item::where('isdeleted', 0)->get();
        $suppliers  = AccHead::where('code', 'like', '2101%')->where('isdeleted', 0)->get();
        $warehouses = AccHead::where('code', 'like', '13%')->where('isdeleted', 0)->get();

        return view('quality::batches.create', compact('items', 'suppliers', 'warehouses'));
    }

    public function store(BatchRequest $request)
    {
        try {
            $validated = $request->validated();
            $validated['branch_id']          = auth()->user()->branches()->where('is_active', 1)->first()->id ?? 1;
            $validated['remaining_quantity']  = $validated['quantity'];
            $validated['status']             = 'active';
            $validated['created_by']         = auth()->id();

            $batch = BatchTracking::create($validated);

            return redirect()->route('quality.batches.show', $batch)
                ->with('success', __('quality::quality.batch details') . ' ' . __('quality::quality.created'));
        } catch (\Exception $e) {
            return redirect()->back()->withInput()
                ->with('error', __('quality::quality.error') . ': ' . $e->getMessage());
        }
    }

    public function show(BatchTracking $batch)
    {
        $batch->load(['item', 'supplier', 'warehouse', 'inspection']);
        return view('quality::batches.show', compact('batch'));
    }

    public function edit(BatchTracking $batch)
    {
        $items      = Item::where('isdeleted', 0)->get();
        $suppliers  = AccHead::where('code', 'like', '2101%')->where('isdeleted', 0)->get();
        $warehouses = AccHead::where('code', 'like', '13%')->where('isdeleted', 0)->get();

        return view('quality::batches.edit', compact('batch', 'items', 'suppliers', 'warehouses'));
    }

    public function update(BatchRequest $request, BatchTracking $batch)
    {
        try {
            $validated = $request->validated();
            $validated['updated_by'] = auth()->id();
            $batch->update($validated);

            return redirect()->route('quality.batches.show', $batch)
                ->with('success', __('quality::quality.batch details') . ' ' . __('quality::quality.save changes'));
        } catch (\Exception $e) {
            return redirect()->back()->withInput()
                ->with('error', __('quality::quality.error') . ': ' . $e->getMessage());
        }
    }

    public function destroy(BatchTracking $batch)
    {
        try {
            $batch->delete();

            return redirect()->route('quality.batches.index')
                ->with('success', __('quality::quality.delete') . ' ' . __('quality::quality.success'));
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', __('quality::quality.error') . ': ' . $e->getMessage());
        }
    }
}
