<?php

namespace Modules\Quality\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Exceptions\HttpResponseException;
use Modules\Quality\Models\QualityInspection;
use Modules\Quality\Http\Requests\InspectionRequest;
use App\Models\Item;
use Modules\Accounts\Models\AccHead;

class QualityInspectionController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:view inspections')->only(['index', 'show']);
        $this->middleware('can:create inspections')->only(['create', 'store']);
        $this->middleware('can:edit inspections')->only(['edit', 'update']);
        $this->middleware('can:delete inspections')->only(['destroy']);
    }

    public function index()
    {
        $inspections = QualityInspection::with(['item', 'inspector', 'supplier'])
            ->orderBy('inspection_date', 'desc')
            ->paginate(20);

        return view('quality::inspections.index', compact('inspections'));
    }

    public function create()
    {
        $items     = Item::where('isdeleted', 0)->get();
        $suppliers = AccHead::where('code', 'like', '2101%')->where('isdeleted', 0)->get();

        return view('quality::inspections.create', compact('items', 'suppliers'));
    }

    public function store(InspectionRequest $request)
    {
        try {
            $validated = $request->validated();
            $validated['inspector_id'] = auth()->id();
            $validated['branch_id']    = auth()->user()->branches()->where('is_active', 1)->first()->id ?? 1;
            $validated['created_by']   = auth()->id();
            $validated['status']       = 'completed';

            $date      = date('Ymd', strtotime($validated['inspection_date']));
            $timestamp = now()->format('His');
            $validated['inspection_number'] = 'INS-' . $date . '-' . $timestamp;

            if ($request->hasFile('attachments')) {
                $paths = [];
                foreach ($request->file('attachments') as $file) {
                    $paths[] = $file->store('quality/inspections', 'public');
                }
                $validated['attachments'] = $paths;
            }

            $inspection = QualityInspection::create($validated);

            return redirect()->route('quality.inspections.show', $inspection)
                ->with('success', __('quality::quality.inspection details') . ' ' . __('quality::quality.created'));
        } catch (\Exception $e) {
            return redirect()->back()->withInput()
                ->with('error', __('quality::quality.error') . ': ' . $e->getMessage());
        }
    }

    public function show(QualityInspection $inspection)
    {
        $inspection->load(['item', 'inspector', 'supplier', 'qualityStandard']);
        return view('quality::inspections.show', compact('inspection'));
    }

    public function edit(QualityInspection $inspection)
    {
        $items     = Item::where('isdeleted', 0)->get();
        $suppliers = AccHead::where('code', 'like', '2101%')->where('isdeleted', 0)->get();

        return view('quality::inspections.edit', compact('inspection', 'items', 'suppliers'));
    }

    public function update(InspectionRequest $request, QualityInspection $inspection)
    {
        try {
            $validated = $request->validated();
            $validated['updated_by'] = auth()->id();

            if ($request->hasFile('attachments')) {
                $paths = $inspection->attachments ?? [];
                foreach ($request->file('attachments') as $file) {
                    $paths[] = $file->store('quality/inspections', 'public');
                }
                $validated['attachments'] = $paths;
            }

            $inspection->update($validated);

            return redirect()->route('quality.inspections.show', $inspection)
                ->with('success', __('quality::quality.inspection details') . ' ' . __('quality::quality.save changes'));
        } catch (\Exception $e) {
            return redirect()->back()->withInput()
                ->with('error', __('quality::quality.error') . ': ' . $e->getMessage());
        }
    }

    public function destroy(QualityInspection $inspection)
    {
        try {
            $inspection->delete();

            return redirect()->route('quality.inspections.index')
                ->with('success', __('quality::quality.delete') . ' ' . __('quality::quality.success'));
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', __('quality::quality.error') . ': ' . $e->getMessage());
        }
    }
}
