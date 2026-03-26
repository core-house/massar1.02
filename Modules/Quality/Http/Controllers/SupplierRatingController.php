<?php

namespace Modules\Quality\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Exceptions\HttpResponseException;
use Modules\Quality\Models\SupplierRating;
use Modules\Quality\Models\QualityInspection;
use Modules\Quality\Models\NonConformanceReport;
use Modules\Quality\Http\Requests\SupplierRatingRequest;
use Modules\Accounts\Models\AccHead;
use Carbon\Carbon;

class SupplierRatingController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:view rateSuppliers')->only(['index', 'show']);
        $this->middleware('can:create rateSuppliers')->only(['create', 'store']);
        $this->middleware('can:edit rateSuppliers')->only(['edit', 'update']);
        $this->middleware('can:delete rateSuppliers')->only(['destroy']);
    }

    public function index()
    {
        $ratings = SupplierRating::with(['supplier', 'ratedBy'])
            ->orderBy('rating_date', 'desc')
            ->paginate(20);

        $stats = [
            'total_suppliers' => AccHead::where('code', 'like', '2101%')->count(),
            'excellent'       => SupplierRating::where('rating', 'excellent')->count(),
            'good'            => SupplierRating::where('rating', 'good')->count(),
            'poor'            => SupplierRating::whereIn('rating', ['poor', 'unacceptable'])->count(),
        ];

        return view('quality::suppliers.index', compact('ratings', 'stats'));
    }

    public function create()
    {
        $suppliers = AccHead::where('code', 'like', '2101%')->where('isdeleted', 0)->get();
        return view('quality::suppliers.create', compact('suppliers'));
    }

    public function store(SupplierRatingRequest $request)
    {
        try {
            $validated = $request->validated();
            $validated['branch_id']   = auth()->user()->branches()->where('is_active', 1)->first()->id ?? 1;
            $validated['rating_date'] = now();
            $validated['rated_by']    = auth()->id();

            $supplierId  = $validated['supplier_id'];
            $periodStart = Carbon::parse($validated['period_start']);
            $periodEnd   = Carbon::parse($validated['period_end']);

            $inspections = QualityInspection::where('supplier_id', $supplierId)
                ->whereBetween('inspection_date', [$periodStart, $periodEnd])->get();

            $validated['total_inspections']  = $inspections->count();
            $validated['passed_inspections'] = $inspections->where('result', 'pass')->count();
            $validated['failed_inspections'] = $inspections->where('result', 'fail')->count();

            $ncrs = NonConformanceReport::whereHas('inspection', function ($q) use ($supplierId) {
                $q->where('supplier_id', $supplierId);
            })->whereBetween('detected_date', [$periodStart, $periodEnd])->get();

            $validated['ncrs_raised']    = $ncrs->count();
            $validated['critical_ncrs']  = $ncrs->where('severity', 'critical')->count();
            $validated['major_ncrs']     = $ncrs->where('severity', 'major')->count();
            $validated['minor_ncrs']     = $ncrs->where('severity', 'minor')->count();
            $validated['total_deliveries']       = 0;
            $validated['on_time_deliveries']     = 0;
            $validated['certificates_required']  = 0;
            $validated['certificates_received']  = 0;
            $validated['supplier_status']        = 'approved';

            $rating = SupplierRating::create($validated);

            return redirect()->route('quality.suppliers.show', $rating)
                ->with('success', __('quality::quality.supplier evaluation') . ' ' . __('quality::quality.created'));
        } catch (\Exception $e) {
            return redirect()->back()->withInput()
                ->with('error', __('quality::quality.error') . ': ' . $e->getMessage());
        }
    }

    public function show(SupplierRating $supplier)
    {
        $supplier->load(['supplier', 'ratedBy', 'approvedBy']);
        return view('quality::suppliers.show', ['rating' => $supplier]);
    }

    public function edit(SupplierRating $supplier)
    {
        return view('quality::suppliers.edit', ['rating' => $supplier]);
    }

    public function update(SupplierRatingRequest $request, SupplierRating $supplier)
    {
        try {
            $supplier->update($request->validated());

            return redirect()->route('quality.suppliers.show', $supplier)
                ->with('success', __('quality::quality.supplier evaluation') . ' ' . __('quality::quality.save changes'));
        } catch (\Exception $e) {
            return redirect()->back()->withInput()
                ->with('error', __('quality::quality.error') . ': ' . $e->getMessage());
        }
    }

    public function destroy(SupplierRating $supplier)
    {
        try {
            $supplier->delete();

            return redirect()->route('quality.suppliers.index')
                ->with('success', __('quality::quality.delete') . ' ' . __('quality::quality.success'));
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', __('quality::quality.error') . ': ' . $e->getMessage());
        }
    }
}
