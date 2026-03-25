<?php

namespace Modules\Quality\Http\Controllers;

use Illuminate\Routing\Controller;
use Modules\Quality\Models\QualityStandard;
use Modules\Quality\Http\Requests\StandardRequest;
use App\Models\Item;

class QualityStandardController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:view standards')->only(['index', 'show']);
        $this->middleware('can:create standards')->only(['create', 'store']);
        $this->middleware('can:edit standards')->only(['edit', 'update']);
        $this->middleware('can:delete standards')->only(['destroy']);
    }

    public function index()
    {
        $standards = QualityStandard::with(['item', 'branch'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $stats = [
            'total'    => QualityStandard::count(),
            'active'   => QualityStandard::where('is_active', true)->count(),
            'inactive' => QualityStandard::where('is_active', false)->count(),
        ];

        return view('quality::standards.index', compact('standards', 'stats'));
    }

    public function create()
    {
        $items = Item::where('isdeleted', 0)->get();
        return view('quality::standards.create', compact('items'));
    }

    public function store(StandardRequest $request)
    {
        try {
            $validated = $request->validated();
            $validated['branch_id']  = auth()->user()->branches()->where('is_active', 1)->first()->id ?? 1;
            $validated['created_by'] = auth()->id();

            $standard = QualityStandard::create($validated);

            return redirect()->route('quality.standards.show', $standard)
                ->with('success', __('quality::quality.quality standard') . ' ' . __('quality::quality.created'));
        } catch (\Exception $e) {
            return redirect()->back()->withInput()
                ->with('error', __('quality::quality.error') . ': ' . $e->getMessage());
        }
    }

    public function show(QualityStandard $standard)
    {
        $standard->load(['item', 'inspections']);
        return view('quality::standards.show', compact('standard'));
    }

    public function edit(QualityStandard $standard)
    {
        $items = Item::where('isdeleted', 0)->get();
        return view('quality::standards.edit', compact('standard', 'items'));
    }

    public function update(StandardRequest $request, QualityStandard $standard)
    {
        try {
            $validated = $request->validated();
            $validated['updated_by'] = auth()->id();
            $standard->update($validated);

            return redirect()->route('quality.standards.show', $standard)
                ->with('success', __('quality::quality.quality standard') . ' ' . __('quality::quality.save changes'));
        } catch (\Exception $e) {
            return redirect()->back()->withInput()
                ->with('error', __('quality::quality.error') . ': ' . $e->getMessage());
        }
    }

    public function destroy(QualityStandard $standard)
    {
        try {
            $standard->delete();

            return redirect()->route('quality.standards.index')
                ->with('success', __('quality::quality.delete') . ' ' . __('quality::quality.success'));
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', __('quality::quality.error') . ': ' . $e->getMessage());
        }
    }
}
