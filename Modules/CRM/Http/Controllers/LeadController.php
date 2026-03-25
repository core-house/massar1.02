<?php

declare(strict_types=1);

namespace Modules\CRM\Http\Controllers;

use App\Models\Client;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\View\View;
use Modules\CRM\Models\Lead;
use Modules\CRM\Models\LeadStatus;

class LeadController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:view Leads')->only(['board']);
        $this->middleware('can:create Leads')->only(['store']);
        $this->middleware('can:edit Leads')->only(['updateStatus']);
        $this->middleware('can:delete Leads')->only(['destroy']);
    }

    public function board(): View
    {
        // LeadStatus and Lead models have BranchScope applied automatically
        $statuses = LeadStatus::orderBy('order_column')->get();
        $leads = Lead::with(['client', 'status', 'assignedTo'])
            ->get()
            ->groupBy('status_id');

        $clients = Client::select('id', 'cname')->get();
        $users = User::select('id', 'name')->get();

        return view('crm::leads.board', compact('statuses', 'leads', 'clients', 'users'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'client_id' => 'required|exists:clients,id',
            'status_id' => 'required|exists:lead_statuses,id',
            'amount' => 'nullable|numeric|min:0',
            'source_id' => 'nullable|exists:chance_sources,id',
            'assigned_to' => 'nullable|exists:users,id',
            'description' => 'nullable|string',
        ]);

        Lead::create($validated);

        return redirect()->route('leads.board')->with('message', __('crm::crm.lead_added_successfully'));
    }

    public function updateStatus(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'lead_id' => 'required|exists:leads,id',
            'new_status_id' => 'required|exists:lead_statuses,id',
        ]);

        $lead = Lead::findOrFail($request->lead_id);
        $lead->update(['status_id' => $request->new_status_id]);

        return response()->json([
            'success' => true,
            'message' => __('crm::crm.lead_status_updated'),
            'new_status' => $lead->status->name,
        ]);
    }

    public function destroy(Lead $lead): RedirectResponse
    {
        $lead->delete();

        return redirect()->route('leads.board')->with('message', __('crm::crm.lead_deleted_successfully'));
    }
}
