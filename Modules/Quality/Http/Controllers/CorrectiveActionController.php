<?php

namespace Modules\Quality\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Http\Exceptions\HttpResponseException;
use Modules\Quality\Models\CorrectiveAction;
use Modules\Quality\Models\NonConformanceReport;
use Modules\Quality\Http\Requests\CapaRequest;
use App\Models\User;

class CorrectiveActionController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:view capa')->only(['index', 'show']);
        $this->middleware('can:create capa')->only(['create', 'store']);
        $this->middleware('can:edit capa')->only(['edit', 'update']);
        $this->middleware('can:delete capa')->only(['destroy']);
    }

    public function index()
    {
        $capas = CorrectiveAction::with(['nonConformanceReport.item', 'responsiblePerson'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $stats = [
            'total'       => CorrectiveAction::count(),
            'in_progress' => CorrectiveAction::where('status', 'in_progress')->count(),
            'completed'   => CorrectiveAction::where('status', 'completed')->count(),
            'overdue'     => CorrectiveAction::overdue()->count(),
        ];

        return view('quality::capa.index', compact('capas', 'stats'));
    }

    public function create()
    {
        $ncrs  = NonConformanceReport::where('status', '!=', 'closed')->with('item')->get();
        $users = User::all();
        return view('quality::capa.create', compact('ncrs', 'users'));
    }

    public function store(CapaRequest $request)
    {
        try {
            $validated = $request->validated();
            $validated['branch_id']  = auth()->user()->branches()->where('is_active', 1)->first()->id ?? 1;
            $validated['created_by'] = auth()->id();
            $validated['status']     = 'planned';

            if ($request->hasFile('attachments')) {
                $paths = [];
                foreach ($request->file('attachments') as $file) {
                    $paths[] = [
                        'path'          => $file->store('capa-attachments', 'public'),
                        'original_name' => $file->getClientOriginalName(),
                    ];
                }
                $validated['attachments'] = $paths;
            }

            $capa = CorrectiveAction::create($validated);

            return redirect()->route('quality.capa.show', $capa)
                ->with('success', __('quality::quality.capa') . ' ' . __('quality::quality.created'));
        } catch (\Exception $e) {
            return redirect()->back()->withInput()
                ->with('error', __('quality::quality.error') . ': ' . $e->getMessage());
        }
    }

    public function show(CorrectiveAction $capa)
    {
        $capa->load(['nonConformanceReport', 'responsiblePerson', 'verifiedBy']);
        return view('quality::capa.show', compact('capa'));
    }

    public function edit(CorrectiveAction $capa)
    {
        $ncrs  = NonConformanceReport::with('item')->get();
        $users = User::all();
        return view('quality::capa.edit', compact('capa', 'ncrs', 'users'));
    }

    public function update(CapaRequest $request, CorrectiveAction $capa)
    {
        try {
            $validated = $request->validated();
            $validated['updated_by'] = auth()->id();

            if ($request->hasFile('attachments')) {
                $existing = $capa->attachments ?? [];
                foreach ($request->file('attachments') as $file) {
                    $existing[] = [
                        'path'          => $file->store('capa-attachments', 'public'),
                        'original_name' => $file->getClientOriginalName(),
                    ];
                }
                $validated['attachments'] = $existing;
            }

            $capa->update($validated);

            return redirect()->route('quality.capa.show', $capa)
                ->with('success', __('quality::quality.capa') . ' ' . __('quality::quality.save changes'));
        } catch (\Exception $e) {
            return redirect()->back()->withInput()
                ->with('error', __('quality::quality.error') . ': ' . $e->getMessage());
        }
    }

    public function verify(Request $request, CorrectiveAction $capa)
    {
        $request->validate([
            'verification_result' => 'required|string',
            'is_effective'        => 'required|boolean',
        ]);

        $capa->update([
            'verified_by'         => auth()->id(),
            'verification_date'   => now(),
            'verification_result' => $request->verification_result,
            'is_effective'        => $request->is_effective,
            'status'              => 'verified',
        ]);

        return redirect()->route('quality.capa.show', $capa)
            ->with('success', __('quality::quality.verified'));
    }

    public function destroy(CorrectiveAction $capa)
    {
        try {
            $capa->delete();

            return redirect()->route('quality.capa.index')
                ->with('success', __('quality::quality.delete') . ' ' . __('quality::quality.success'));
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', __('quality::quality.error') . ': ' . $e->getMessage());
        }
    }
}
