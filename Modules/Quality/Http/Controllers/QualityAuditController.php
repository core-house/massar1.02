<?php

namespace Modules\Quality\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Exceptions\HttpResponseException;
use Modules\Quality\Models\QualityAudit;
use Modules\Quality\Http\Requests\AuditRequest;
use App\Models\User;

class QualityAuditController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:view audits')->only(['index', 'show']);
        $this->middleware('can:create audits')->only(['create', 'store']);
        $this->middleware('can:edit audits')->only(['edit', 'update']);
        $this->middleware('can:delete audits')->only(['destroy']);
    }

    public function index()
    {
        $audits = QualityAudit::with(['leadAuditor'])
            ->orderBy('planned_date', 'desc')
            ->paginate(20);

        $stats = [
            'total'       => QualityAudit::count(),
            'planned'     => QualityAudit::where('status', 'planned')->count(),
            'in_progress' => QualityAudit::where('status', 'in_progress')->count(),
            'completed'   => QualityAudit::where('status', 'completed')->count(),
        ];

        return view('quality::audits.index', compact('audits', 'stats'));
    }

    public function create()
    {
        $users = User::all();
        return view('quality::audits.create', compact('users'));
    }

    public function store(AuditRequest $request)
    {
        try {
            $validated = $request->validated();
            $validated['branch_id']  = auth()->user()->branches()->where('is_active', 1)->first()->id ?? 1;
            $validated['status']     = 'planned';
            $validated['created_by'] = auth()->id();

            $audit = QualityAudit::create($validated);

            return redirect()->route('quality.audits.show', $audit)
                ->with('success', __('quality::quality.audit') . ' ' . __('quality::quality.created'));
        } catch (\Exception $e) {
            return redirect()->back()->withInput()
                ->with('error', __('quality::quality.error') . ': ' . $e->getMessage());
        }
    }

    public function show(QualityAudit $audit)
    {
        $audit->load(['leadAuditor', 'approvedBy']);
        return view('quality::audits.show', compact('audit'));
    }

    public function edit(QualityAudit $audit)
    {
        $users = User::all();
        return view('quality::audits.edit', compact('audit', 'users'));
    }

    public function update(AuditRequest $request, QualityAudit $audit)
    {
        try {
            $validated = $request->validated();
            $validated['updated_by'] = auth()->id();
            $audit->update($validated);

            return redirect()->route('quality.audits.show', $audit)
                ->with('success', __('quality::quality.audit') . ' ' . __('quality::quality.save changes'));
        } catch (\Exception $e) {
            return redirect()->back()->withInput()
                ->with('error', __('quality::quality.error') . ': ' . $e->getMessage());
        }
    }

    public function destroy(QualityAudit $audit)
    {
        try {
            $audit->delete();

            return redirect()->route('quality.audits.index')
                ->with('success', __('quality::quality.delete audit') . ' ' . __('quality::quality.success'));
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', __('quality::quality.error') . ': ' . $e->getMessage());
        }
    }
}
