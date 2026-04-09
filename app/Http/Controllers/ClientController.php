<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Client;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\CRM\Models\ClientType;
use App\Http\Requests\ClientRequest;
use Illuminate\Support\Facades\Auth;
use Modules\CRM\Models\ClientCategory;
use RealRashid\SweetAlert\Facades\Alert;


class ClientController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view CRM Clients')->only(['index', 'show']);
        $this->middleware('permission:create CRM Clients')->only(['create', 'store']);
        $this->middleware('permission:edit CRM Clients')->only(['edit', 'update']);
        $this->middleware('permission:delete CRM Clients')->only(['destroy']);
    }

    public function index(Request $request)
    {
        $query = Client::with(['assignedUser', 'clientType']);

        if ($request->filled('search'))
            $query->where(function($q) use ($request) {
                $q->where('cname', 'like', '%' . $request->search . '%')
                  ->orWhere('phone', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });

        if ($request->filled('client_type_id'))
            $query->where('client_type_id', $request->client_type_id);

        if ($request->filled('is_active'))
            $query->where('is_active', $request->is_active);

        if ($request->filled('assigned_user_id'))
            $query->where('assigned_user_id', $request->assigned_user_id);

        $clients     = $query->paginate(50)->withQueryString();
        $clientTypes = ClientType::all();

        return view('clients.index', compact('clients', 'clientTypes'));
    }

    public function create()
    {
        $branches = userBranches();
        $clientTypes = ClientType::all();
        $categories = ClientCategory::all();
        return view('clients.create', compact('branches', 'categories', 'clientTypes'));
    }

    public function store(ClientRequest $request)
    {
        DB::beginTransaction();
        try {
            $data = $request->all();
            $data['is_active'] = $request->has('is_active') ? 1 : 0;
            $data['created_by'] = Auth::id();

            $client = Client::create($data);

            DB::commit();

            // Handle AJAX requests
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'تم إنشاء العميل بنجاح',
                    'client' => $client
                ]);
            }

            Alert::toast('تم إنشاء العميل بنجاح', 'success');
            return redirect()->route('clients.index');
        } catch (Exception $e) {
            DB::rollBack();

            // Handle AJAX requests
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'حدث خطأ أثناء إنشاء العميل: ' . $e->getMessage()
                ], 422);
            }

            Alert::toast('حدث خطأ أثناء إنشاء العميل', 'error');
            return redirect()->back()->withInput();
        }
    }

    public function show($id)
    {
        $client = Client::with([
            'clientType',
            'category',
            'invoices' => function($q) { $q->latest('pro_date')->take(20); },
            'tasks' => function($q) { $q->latest()->take(20); },
            'activities' => function($q) { $q->latest('activity_date')->take(20); },
            'tickets' => function($q) { $q->latest()->take(20); },
            'leads' => function($q) { $q->latest()->take(20); },
            // 'projectsAsClient' removed because inquiries doesn't have client_id
        ])->findOrFail($id);

        // Prepare Timeline Data
        $timeline = collect();

        // One-to-Many Relationships
        foreach ($client->invoices as $invoice) {
            $timeline->push((object)[
                'type' => 'invoice',
                'date' => \Carbon\Carbon::parse($invoice->pro_date),
                'icon' => 'las la-file-invoice',
                'color' => 'primary',
                'title' => __('Invoice') . ' #' . $invoice->id,
                'description' => __('Value') . ': ' . number_format($invoice->pro_value, 2),
                'link' => route('invoices.show', $invoice->id)
            ]);
        }

        foreach ($client->tasks as $task) {
            $timeline->push((object)[
                'type' => 'task',
                'date' => $task->created_at,
                'icon' => 'las la-tasks',
                'color' => 'warning',
                'title' => __('Task') . ': ' . $task->title,
                'description' => $task->status,
                'link' => route('tasks.edit', $task->id)
            ]);
        }

        foreach ($client->activities as $activity) {
            $timeline->push((object)[
                'type' => 'activity',
                'date' => $activity->activity_date,
                'icon' => 'las la-calendar-check',
                'color' => 'success',
                'title' => $activity->title,
                'description' => $activity->description,
                'link' => route('activities.edit', $activity->id)
            ]);
        }

        foreach ($client->tickets as $ticket) {
            $timeline->push((object)[
                'type' => 'ticket',
                'date' => $ticket->created_at,
                'icon' => 'las la-ticket-alt',
                'color' => 'danger',
                'title' => __('Ticket') . ': ' . $ticket->subject,
                'description' => $ticket->status,
                'link' => route('tickets.edit', $ticket->id)
            ]);
        }

        foreach ($client->leads as $lead) {
            $timeline->push((object)[
                'type' => 'lead',
                'date' => $lead->created_at,
                'icon' => 'las la-funnel-dollar',
                'color' => 'info',
                'title' => __('Lead') . ': ' . $lead->title,
                'description' => __('Status') . ': ' . $lead->status_id,
                'link' => '#'
            ]);
        }

        // Fetch Inquiries (Projects) via Email matching if independent
        // Using full namespace for Inquiry to be safe, or import it.
        // Assuming Client email is unique identifier for Contact in Inquiry module.
        if ($client->email) {
            $inquiries = \Modules\Inquiries\Models\Inquiry::whereHas('contacts', function($q) use ($client) {
                $q->where('email', $client->email);
            })->latest()->take(20)->get();

            foreach ($inquiries as $project) {
                 $timeline->push((object)[
                    'type' => 'project',
                    'date' => $project->created_at,
                    'icon' => 'las la-project-diagram',
                    'color' => 'secondary',
                    'title' => __('Inquiry') . ': ' . ($project->project_name ?? $project->id),
                    'description' => $project->status?->label() ?? '',
                    'link' => route('inquiries.edit', $project->id) // Assuming edit route is main
                ]);
            }
        }

        $timeline = $timeline->sortByDesc('date');

        return view('clients.show', compact('client', 'timeline'));
    }

    public function edit($id)
    {
        $client = Client::findOrFail($id);
        $categories = ClientCategory::all();
        $clientTypes = ClientType::all();

        return view('clients.edit', compact('client', 'categories', 'clientTypes'));
    }


    public function update(ClientRequest $request, $id)
    {
        DB::beginTransaction();
        try {
            $client = Client::findOrFail($id);

            $data = $request->all();
            $data['is_active'] = $request->has('is_active') ? 1 : 0;

            $client->update($data);

            DB::commit();
            Alert::toast('تم تحديث بيانات العميل بنجاح', 'success');
            return redirect()->route('clients.index');
        } catch (Exception $e) {
            DB::rollBack();
            Alert::toast('حدث خطأ أثناء تحديث بيانات العميل', 'error');
            return redirect()->back()->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            $client = Client::findOrFail($id);
            $client->delete();
            Alert::toast('تم حذف العنصر بنجاح', 'success');

            return redirect()->route('clients.index');
        } catch (Exception) {
            Alert::toast('حدث خطأ أثناء حذف العميل', 'error');
            return redirect()->route('clients.index');
        }
    }

    public function toggleActive($id)
    {
        $client = Client::findOrFail($id);
        $client->is_active = !$client->is_active;
        $client->save();

        return response()->json([
            'success' => true,
            'status'  => $client->is_active ? 'نشط' : 'غير نشط',
        ]);
    }
}
