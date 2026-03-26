<?php

namespace Modules\CRM\Http\Controllers;

use App\Models\Client;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\CRM\Models\Ticket;
use Modules\CRM\Models\TicketComment;

class TicketController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:view Tickets')->only(['index', 'show']);
        $this->middleware('can:create Tickets')->only(['create', 'store']);
        $this->middleware('can:edit Tickets')->only(['edit', 'update', 'updateStatus', 'addComment']);
        $this->middleware('can:delete Tickets')->only(['destroy']);
    }

    public function index(Request $request)
    {
        $query = Ticket::with(['client', 'assignedTo', 'createdBy']);

        // Filter by search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('subject', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('ticket_reference', 'like', "%{$search}%")
                    ->orWhereHas('client', function ($q) use ($search) {
                        $q->where('cname', 'like', "%{$search}%");
                    });
            });
        }

        // Filter by status
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Filter by priority
        if ($request->filled('priority') && $request->priority !== 'all') {
            $query->where('priority', $request->priority);
        }

        // Filter by ticket type
        if ($request->filled('ticket_type') && $request->ticket_type !== 'all') {
            $query->where('ticket_type', $request->ticket_type);
        }

        // Filter by client
        if ($request->filled('client_id')) {
            $query->where('client_id', $request->client_id);
        }

        // Filter by assigned user
        if ($request->filled('assigned_to')) {
            $query->where('assigned_to', $request->assigned_to);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $tickets = $query->orderBy('created_at', 'desc')
            ->get()
            ->groupBy('status');

        $clients = Client::all();
        $users = User::all();

        // Get unique ticket types for filter
        $ticketTypes = Ticket::select('ticket_type')
            ->distinct()
            ->whereNotNull('ticket_type')
            ->pluck('ticket_type');

        return view('crm::tickets.index', compact('tickets', 'clients', 'users', 'ticketTypes'));
    }

    public function create()
    {
        $branches = userBranches();
        $clients = Client::all();
        $users = User::all();

        return view('crm::tickets.create', compact('clients', 'users', 'branches'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'subject' => 'required|string|max:255',
            'branch_id' => 'required|exists:branches,id',
            'description' => 'required|string',
            'priority' => 'required|in:low,medium,high',
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        $validated['created_by'] = Auth::id();
        $validated['status'] = 'open';

        Ticket::create($validated);

        return redirect()->route('tickets.index')->with('message', __('crm::crm.ticket_created_successfully'));
    }

    public function show(Ticket $ticket)
    {
        $ticket->load(['client', 'assignedTo', 'createdBy', 'comments.user']);
        $users = User::all();

        return view('crm::tickets.show', compact('ticket', 'users'));
    }

    public function edit(Ticket $ticket)
    {
        $branches = userBranches();
        $clients = Client::all();
        $users = User::all();

        return view('crm::tickets.edit', compact('ticket', 'clients', 'users', 'branches'));
    }

    public function update(Request $request, Ticket $ticket)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'subject' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'required|in:low,medium,high',
            'assigned_to' => 'nullable|exists:users,id',
            'status' => 'required|in:open,in_progress,resolved,closed',
        ]);

        $ticket->update($validated);

        return redirect()->route('tickets.show', $ticket)->with('message', __('crm::crm.ticket_updated_successfully'));
    }

    public function updateStatus(Request $request)
    {
        $request->validate([
            'ticket_id' => 'required|exists:crm_tickets,id',
            'new_status' => 'required|in:open,in_progress,resolved,closed',
        ]);

        $ticket = Ticket::find($request->ticket_id);
        $ticket->updateStatus($request->new_status);

        $ticket->updateStatus($request->new_status);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => __('crm::crm.ticket_status_updated'),
            ]);
        }

        return redirect()->back()->with('message', __('crm::crm.ticket_status_updated'));
    }

    public function addComment(Request $request, Ticket $ticket)
    {
        $validated = $request->validate([
            'comment' => 'required|string',
        ]);

        TicketComment::create([
            'ticket_id' => $ticket->id,
            'user_id' => Auth::id(),
            'comment' => $validated['comment'],
        ]);

        return redirect()->route('tickets.show', $ticket)->with('message', __('crm::crm.comment_added_successfully'));
    }

    public function destroy(Ticket $ticket)
    {
        $ticket->delete();

        return redirect()->route('tickets.index')->with('message', __('crm::crm.ticket_deleted_successfully'));
    }
}
