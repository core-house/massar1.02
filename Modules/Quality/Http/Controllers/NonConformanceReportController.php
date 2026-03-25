<?php

namespace Modules\Quality\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Http\Exceptions\HttpResponseException;
use Modules\Quality\Models\NonConformanceReport;
use Modules\Quality\Models\QualityInspection;
use Modules\Quality\Http\Requests\NcrRequest;
use App\Models\Item;
use App\Models\User;

class NonConformanceReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:view ncr')->only(['index', 'show']);
        $this->middleware('can:create ncr')->only(['create', 'store']);
        $this->middleware('can:edit ncr')->only(['edit', 'update']);
        $this->middleware('can:delete ncr')->only(['destroy']);
    }

    public function index(Request $request)
    {
        $query = NonConformanceReport::with(['item', 'detectedBy', 'assignedTo'])
            ->orderBy('detected_date', 'desc');

        if ($request->filled('status'))   $query->where('status', $request->status);
        if ($request->filled('severity'))  $query->where('severity', $request->severity);
        if ($request->filled('source'))    $query->where('source', $request->source);
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('ncr_number', 'like', "%{$search}%")
                  ->orWhere('problem_description', 'like', "%{$search}%")
                  ->orWhere('batch_number', 'like', "%{$search}%");
            });
        }

        $ncrs  = $query->paginate(20);
        $stats = [
            'total'    => NonConformanceReport::count(),
            'open'     => NonConformanceReport::where('status', 'open')->count(),
            'critical' => NonConformanceReport::where('severity', 'critical')->count(),
            'overdue'  => NonConformanceReport::overdue()->count(),
        ];

        return view('quality::ncr.index', compact('ncrs', 'stats'));
    }

    public function create()
    {
        $items       = Item::where('isdeleted', 0)->get();
        $inspections = QualityInspection::where('result', 'fail')
            ->whereDoesntHave('nonConformanceReport')
            ->with('item')->get();
        $users = User::all();

        return view('quality::ncr.create', compact('items', 'inspections', 'users'));
    }

    public function store(NcrRequest $request)
    {
        try {
            $validated = $request->validated();
            $branch = auth()->user()->branches()->where('is_active', 1)->first();
            $validated['branch_id']   = $branch ? $branch->id : 1;
            $validated['detected_by'] = auth()->id();
            $validated['created_by']  = auth()->id();
            $validated['status']      = 'open';

            if ($request->hasFile('attachments')) {
                $attachments = [];
                foreach ($request->file('attachments') as $file) {
                    $attachments[] = [
                        'path'          => $file->store('ncr-attachments', 'public'),
                        'original_name' => $file->getClientOriginalName(),
                        'size'          => $file->getSize(),
                    ];
                }
                $validated['attachments'] = $attachments;
            }

            $ncr = NonConformanceReport::create($validated);

            return redirect()->route('quality.ncr.show', $ncr)
                ->with('success', __('quality::quality.ncr') . ' ' . __('quality::quality.created'));
        } catch (HttpResponseException $e) {
            throw $e;
        } catch (\Exception $e) {
            return redirect()->back()->withInput()
                ->with('error', __('quality::quality.error') . ': ' . $e->getMessage());
        }
    }

    public function show(NonConformanceReport $ncr)
    {
        $ncr->load(['item', 'inspection', 'detectedBy', 'assignedTo', 'closedBy', 'correctiveActions']);
        return view('quality::ncr.show', compact('ncr'));
    }

    public function edit(NonConformanceReport $ncr)
    {
        $items = Item::where('isdeleted', 0)->get();
        $users = User::all();
        return view('quality::ncr.edit', compact('ncr', 'items', 'users'));
    }

    public function update(NcrRequest $request, NonConformanceReport $ncr)
    {
        try {
            $validated = $request->validated();
            $validated['updated_by'] = auth()->id();

            if ($request->hasFile('attachments')) {
                $existing = $ncr->attachments ?? [];
                foreach ($request->file('attachments') as $file) {
                    $existing[] = [
                        'path'          => $file->store('ncr-attachments', 'public'),
                        'original_name' => $file->getClientOriginalName(),
                        'size'          => $file->getSize(),
                    ];
                }
                $validated['attachments'] = $existing;
            }

            $ncr->update($validated);

            return redirect()->route('quality.ncr.show', $ncr)
                ->with('success', __('quality::quality.ncr') . ' ' . __('quality::quality.save changes'));
        } catch (HttpResponseException $e) {
            throw $e;
        } catch (\Exception $e) {
            return redirect()->back()->withInput()
                ->with('error', __('quality::quality.error') . ': ' . $e->getMessage());
        }
    }

    public function close(Request $request, NonConformanceReport $ncr)
    {
        $request->validate([
            'closure_notes'       => 'required|string',
            'actual_closure_date' => 'required|date',
        ]);

        $ncr->update([
            'status'              => 'closed',
            'closed_by'           => auth()->id(),
            'closure_notes'       => $request->closure_notes,
            'actual_closure_date' => $request->actual_closure_date,
        ]);

        return redirect()->route('quality.ncr.show', $ncr)
            ->with('success', __('quality::quality.closed'));
    }

    public function destroy(NonConformanceReport $ncr)
    {
        try {
            $ncr->delete();

            return redirect()->route('quality.ncr.index')
                ->with('success', __('quality::quality.delete') . ' ' . __('quality::quality.success'));
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', __('quality::quality.error') . ': ' . $e->getMessage());
        }
    }
}
