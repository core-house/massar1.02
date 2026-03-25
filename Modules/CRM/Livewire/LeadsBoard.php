<?php

declare(strict_types=1);

namespace Modules\CRM\Livewire;

use App\Models\Client;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Modules\CRM\Models\ChanceSource;
use Modules\CRM\Models\Lead;
use Modules\CRM\Models\LeadStatus;

class LeadsBoard extends Component
{
    public $statuses;

    public $leads;

    public $showAddModal = false;

    public $showEditModal = false;

    public $showViewModal = false;

    public $showReportModal = false;

    public $selectedStatus = null;

    public $selectedStatusForReport = null;

    public $selectedLead = null;

    public $viewingLead = null;

    public $sources;

    public $reportData = [];

    // Filters
    public $search = '';

    public $filterStatus = 'all';

    public $filterSource = 'all';

    public $filterAssignedTo = 'all';

    public $filterClient = 'all';

    public $filterDateFrom = '';

    public $filterDateTo = '';

    // إضافة متغيرات جديدة لإنشاء العميل
    public $showCreateClient = false;

    public $newClientName = '';

    public $clientSearch = '';

    public $filteredClients = [];

    public $showClientDropdown = false;

    public $selectedClientText = '';

    // بيانات الفرصة الجديدة
    public $newLead = [
        'title' => '',
        'client_id' => '',
        'amount' => '',
        'source' => '',
        'assigned_to' => '',
        'description' => '',
    ];

    // بيانات الفرصة للتعديل
    public $editingLead = [
        'id' => '',
        'title' => '',
        'client_id' => '',
        'amount' => '',
        'source' => '',
        'assigned_to' => '',
        'description' => '',
    ];

    public $clients;

    public $users;

    protected $rules = [
        'newLead.title' => 'required|string|max:255',
        'newLead.client_id' => 'required|exists:clients,id',
        'newLead.amount' => 'nullable|numeric|min:0',
        'newLead.source' => 'nullable|exists:chance_sources,id',
        'newLead.assigned_to' => 'nullable|exists:users,id',
        'newLead.description' => 'nullable|string',
        'newClientName' => 'required_if:showCreateClient,true|string|max:255|unique:clients,cname',

    ];

    protected $messages = [
        'newLead.title.required' => 'عنوان الفرصة مطلوب',
        'newLead.title.max' => 'عنوان الفرصة يجب أن يكون أقل من 255 حرف',
        'newLead.client_id.required' => 'يجب اختيار العميل',
        'newLead.client_id.exists' => 'العميل المحدد غير موجود',
        'newLead.amount.numeric' => 'القيمة يجب أن تكون رقماً',
        'newLead.amount.min' => 'القيمة يجب أن تكون أكبر من أو تساوي صفر',
        'newLead.assigned_to.exists' => 'المستخدم المسؤول غير موجود',
        'newLead.source.exists' => 'المصدر المحدد غير موجود',
        'newClientName.required_if' => 'اسم العميل مطلوب',
        'newClientName.max' => 'اسم العميل يجب أن يكون أقل من 255 حرف',
        'newClientName.unique' => 'اسم العميل موجود بالفعل',
    ];

    public function mount(): void
    {
        $statusCount = LeadStatus::count();
        if ($statusCount === 0) {
            $this->createDefaultStatuses();
        }

        $this->loadData();
        $this->clients = Client::select('id', 'cname')->get()->toArray();
        $this->users = User::all();
        $this->sources = ChanceSource::all();
    }

    private function createDefaultStatuses(): void
    {
        $defaultStatuses = [
            ['name' => 'جديد', 'color' => '#007bff', 'order_column' => 1],
            ['name' => 'قيد المتابعة', 'color' => '#ffc107', 'order_column' => 2],
            ['name' => 'مؤهل', 'color' => '#28a745', 'order_column' => 3],
            ['name' => 'مُتم', 'color' => '#6f42c1', 'order_column' => 4],
            ['name' => 'مُلغى', 'color' => '#dc3545', 'order_column' => 5],
        ];

        foreach ($defaultStatuses as $status) {
            LeadStatus::create($status);
        }
    }

    public function hideClientDropdown(): void
    {
        $this->showClientDropdown = false;
        $this->filteredClients = [];
    }

    public function updatedClientSearch(string $value): void
    {
        if (empty($value)) {
            $this->hideClientDropdown();

            return;
        }
        $this->filteredClients = collect($this->clients)
            ->filter(function ($client) use ($value) {
                return str_contains(strtolower($client['cname']), strtolower($value));
            })
            ->take(5)
            ->values()
            ->toArray();

        $this->showClientDropdown = true;
        if ($this->newLead['client_id'] && $this->selectedClientText !== $value) {
            $this->clearClientSearch(false);
        }
    }

    public function selectClient(int $clientId, string $clientName): void
    {
        $this->newLead['client_id'] = $clientId;
        $this->clientSearch = $clientName;
        $this->selectedClientText = $clientName;
        $this->hideClientDropdown();
    }

    public function createClientFromSearch(): void
    {
        $this->validate([
            'clientSearch' => 'required|string|max:255|unique:clients,cname',
        ], [
            'clientSearch.unique' => 'يوجد عميل بهذا الاسم بالفعل.',
            'clientSearch.required' => 'اسم العميل مطلوب.',
        ]);

        try {
            $newClient = Client::create([
                'cname' => $this->clientSearch,
                'created_by' => Auth::id(),
            ]);
            $this->clients = Client::select('id', 'cname')->get()->toArray();
            $this->selectClient($newClient->id, $newClient->cname);

            session()->flash('message', 'تم إنشاء العميل "'.$newClient->cname.'" واختياره.');
        } catch (\Exception $e) {
            Log::error('LeadsBoard createClientFromSearch error: '.$e->getMessage());
            session()->flash('error', __('crm::crm.error_creating_client'));
        }
    }

    public function clearClientSearch(bool $hideDropdown = true): void
    {
        $this->clientSearch = '';
        $this->newLead['client_id'] = null;
        $this->selectedClientText = '';
        if ($hideDropdown) {
            $this->hideClientDropdown();
        }
    }

    public function loadData(): void
    {
        try {
            $this->statuses = LeadStatus::orderBy('order_column')->get();

            $query = Lead::with(['client', 'status', 'assignedTo', 'source']);

            if (! empty($this->search)) {
                $query->where(function ($q) {
                    $q->where('title', 'like', "%{$this->search}%")
                        ->orWhere('description', 'like', "%{$this->search}%")
                        ->orWhereHas('client', function ($q) {
                            $q->where('cname', 'like', "%{$this->search}%");
                        });
                });
            }

            if ($this->filterStatus !== 'all') {
                $query->where('status_id', $this->filterStatus);
            }

            if ($this->filterSource !== 'all') {
                $query->where('source_id', $this->filterSource);
            }

            if ($this->filterAssignedTo !== 'all') {
                $query->where('assigned_to', $this->filterAssignedTo);
            }

            if ($this->filterClient !== 'all') {
                $query->where('client_id', $this->filterClient);
            }

            if (! empty($this->filterDateFrom)) {
                $query->whereDate('created_at', '>=', $this->filterDateFrom);
            }

            if (! empty($this->filterDateTo)) {
                $query->whereDate('created_at', '<=', $this->filterDateTo);
            }

            $grouped = $query->get()
                ->groupBy('status_id')
                ->map(function ($leads) {
                    return $leads->map(function ($lead) {
                        return [
                            'id' => $lead->id,
                            'title' => $lead->title,
                            'client' => $lead->client ? $lead->client->only('cname') : null,
                            'amount' => $lead->amount,
                            'source' => $lead->source,
                            'assigned_to' => $lead->assignedTo ? $lead->assignedTo->only('name') : null,
                            'description' => $lead->description,
                        ];
                    })->values();
                });

            $this->leads = $grouped->toArray();
        } catch (\Exception $e) {
            $this->statuses = collect([]);
            $this->leads = [];
            Log::error('LeadsBoard loadData error: '.$e->getMessage());
            session()->flash('error', __('crm::crm.error_loading_data'));
        }
    }

    public function showCreateClientForm(): void
    {
        $this->showCreateClient = true;
        $this->newClientName = '';
    }

    public function hideCreateClientForm(): void
    {
        $this->showCreateClient = false;
        $this->newClientName = '';
        $this->resetErrorBag('newClientName');
    }

    public function createQuickClient(): void
    {
        $this->validate([
            'newClientName' => 'required|string|max:255|unique:clients,cname',
        ]);

        try {
            $newClient = Client::create([
                'cname' => $this->newClientName,
                'phone' => null,
                'email' => null,
                'address' => null,
                'created_by' => Auth::id(),
            ]);
            $this->clients = Client::select('id', 'cname')->get()->toArray();
            $this->newLead['client_id'] = $newClient->id;
            $this->hideCreateClientForm();
            session()->flash('message', __('crm::crm.client_created_successfully'));
        } catch (\Exception $e) {
            Log::error('LeadsBoard createQuickClient error: '.$e->getMessage());
            session()->flash('error', __('crm::crm.error_creating_client'));
        }
    }

    public function updateLeadStatus(int $leadId, int $newStatusId): void
    {
        try {
            $lead = Lead::find($leadId);
            if ($lead) {
                $lead->changeStatus($newStatusId);
                $newStatus = LeadStatus::find($newStatusId);

                $users = \App\Models\User::all();
                \Illuminate\Support\Facades\Notification::send($users, new \Modules\Notifications\Notifications\GeneralNotification(
                    title: __('Lead Status Updated'),
                    message: __('Lead ":title" status changed to ":status".', [
                        'title' => $lead->title,
                        'status' => $newStatus->name ?? 'Unknown',
                    ]),
                    url: route('leads.board'),
                    type: 'info',
                    icon: 'las la-exchange-alt'
                ));

                $this->loadData();

                $this->dispatch('lead-moved', [
                    'leadId' => $leadId,
                    'newStatus' => $newStatus->name,
                ]);

                session()->flash('message', __('crm::crm.lead_status_updated'));
            }
        } catch (\Exception $e) {
            Log::error('LeadsBoard updateLeadStatus error: '.$e->getMessage());
            session()->flash('error', __('crm::crm.error_updating_status'));
        }
    }

    #[On('open-add-modal')]
    public function openAddModal(?int $statusId = null): void
    {
        $this->selectedStatus = $statusId;
        $this->showAddModal = true;
        $this->resetNewLead();
    }

    public function showLead(int $leadId): void
    {
        try {
            $lead = Lead::with(['client', 'assignedTo', 'source', 'status'])->findOrFail($leadId);

            $this->viewingLead = [
                'id' => $lead->id,
                'title' => $lead->title,
                'client' => $lead->client ? ['cname' => $lead->client->cname] : null,
                'amount' => $lead->amount,
                'status' => $lead->status ? ['name' => $lead->status->name, 'color' => $lead->status->color] : null,
                'source_title' => $lead->source ? $lead->source->title : null,
                'assigned_to' => $lead->assignedTo ? ['name' => $lead->assignedTo->name] : null,
                'created_by' => null,
                'description' => $lead->description,
                'created_at' => $lead->created_at,
                'updated_at' => $lead->updated_at,
            ];

            $this->showViewModal = true;
        } catch (\Exception $e) {
            Log::error('LeadsBoard showLead error: '.$e->getMessage());
            session()->flash('error', __('crm::crm.error_loading_lead'));
        }
    }

    public function editLead(int $leadId): void
    {
        try {
            $lead = Lead::with(['client', 'assignedTo', 'source'])->findOrFail($leadId);

            $this->selectedLead = $lead;
            $this->editingLead = [
                'id' => $lead->id,
                'title' => $lead->title,
                'client_id' => $lead->client_id,
                'amount' => $lead->amount,
                'source' => $lead->source_id,
                'assigned_to' => $lead->assigned_to,
                'description' => $lead->description,
            ];

            $this->showEditModal = true;
        } catch (\Exception $e) {
            Log::error('LeadsBoard editLead error: '.$e->getMessage());
            session()->flash('error', __('crm::crm.error_loading_lead'));
        }
    }

    public function updateLead(): void
    {
        abort_unless(auth()->user()->can('edit Leads'), 403);
        $this->validate([
            'editingLead.title' => 'required|string|max:255',
            'editingLead.client_id' => 'required|exists:clients,id',
            'editingLead.amount' => 'nullable|numeric|min:0',
            'editingLead.source' => 'nullable|exists:chance_sources,id',
            'editingLead.assigned_to' => 'nullable|exists:users,id',
            'editingLead.description' => 'nullable|string',
        ], [
            'editingLead.title.required' => 'عنوان الفرصة مطلوب',
            'editingLead.title.max' => 'عنوان الفرصة يجب أن يكون أقل من 255 حرف',
            'editingLead.client_id.required' => 'يجب اختيار العميل',
            'editingLead.client_id.exists' => 'العميل المحدد غير موجود',
            'editingLead.amount.numeric' => 'القيمة يجب أن تكون رقماً',
            'editingLead.amount.min' => 'القيمة يجب أن تكون أكبر من أو تساوي صفر',
            'editingLead.assigned_to.exists' => 'المستخدم المسؤول غير موجود',
            'editingLead.source.exists' => 'المصدر المحدد غير موجود',
        ]);

        try {
            $lead = Lead::findOrFail($this->editingLead['id']);
            $lead->update([
                'title' => $this->editingLead['title'],
                'client_id' => $this->editingLead['client_id'],
                'amount' => $this->editingLead['amount'],
                'source_id' => $this->editingLead['source'],
                'assigned_to' => $this->editingLead['assigned_to'],
                'description' => $this->editingLead['description'],
            ]);

            $users = \App\Models\User::all();
            \Illuminate\Support\Facades\Notification::send($users, new \Modules\Notifications\Notifications\GeneralNotification(
                title: __('Lead Updated'),
                message: __('Lead ":title" has been updated.', ['title' => $lead->title]),
                url: route('leads.board'),
                type: 'info',
                icon: 'las la-edit'
            ));

            $this->closeModal();
            $this->loadData();
            session()->flash('message', __('crm::crm.lead_updated_successfully'));
        } catch (\Exception $e) {
            Log::error('LeadsBoard updateLead error: '.$e->getMessage());
            session()->flash('error', __('crm::crm.error_updating_lead'));
        }
    }

    public function openStatusReport(int $statusId): void
    {
        try {
            $this->selectedStatusForReport = LeadStatus::findOrFail($statusId);

            $leads = Lead::with(['client', 'assignedTo', 'source'])
                ->where('status_id', $statusId)
                ->get();

            $this->reportData = [
                'total_leads' => $leads->count(),
                'total_amount' => $leads->sum('amount'),
                'avg_amount' => $leads->count() > 0 ? $leads->avg('amount') : 0,
                'leads_by_source' => $leads->groupBy(fn ($l) => optional($l->source)->title)->map->count(),
                'leads_by_user' => $leads->groupBy(fn ($l) => optional($l->assignedTo)->name)->map->count(),
                'leads_details' => $leads->map(function ($lead) {
                    return [
                        'title' => $lead->title,
                        'client_name' => optional($lead->client)->cname ?? __('crm::crm.undefined'),
                        'amount' => $lead->amount,
                        'source' => optional($lead->source)->title ?? __('crm::crm.undefined'),
                        'assigned_to' => optional($lead->assignedTo)->name ?? __('crm::crm.unassigned'),
                        'created_at' => $lead->created_at->format('Y-m-d'),
                    ];
                }),
            ];
            $this->showReportModal = true;
        } catch (\Exception $e) {
            Log::error('LeadsBoard openStatusReport error: '.$e->getMessage());
            session()->flash('error', __('crm::crm.error_loading_report'));
        }
    }

    public function closeModal(): void
    {
        $this->showAddModal = false;
        $this->showEditModal = false;
        $this->showViewModal = false;
        $this->showReportModal = false;
        $this->selectedStatus = null;
        $this->selectedLead = null;
        $this->viewingLead = null;
        $this->selectedStatusForReport = null;
        $this->resetNewLead();
        $this->resetEditingLead();
        $this->resetErrorBag();
    }

    public function addLead(): void
    {
        abort_unless(auth()->user()->can('create Leads'), 403);
        $this->validate();
        try {
            $leadData = $this->newLead;
            if ($this->selectedStatus) {
                $leadData['status_id'] = $this->selectedStatus;
            } else {
                $firstStatus = LeadStatus::orderBy('order_column')->first();
                if ($firstStatus) {
                    $leadData['status_id'] = $firstStatus->id;
                } else {
                    session()->flash('error', __('crm::crm.create_statuses_first'));

                    return;
                }
            }

            if (! empty($leadData['source'])) {
                $leadData['source_id'] = $leadData['source'];
                unset($leadData['source']);
            }

            if (empty($leadData['branch_id'])) {
                $leadData['branch_id'] = optional(Auth::user())
                    ->branches()
                    ->where('branches.is_active', 1)
                    ->value('branches.id');
            }

            $lead = Lead::create($leadData);

            $users = \App\Models\User::all();
            \Illuminate\Support\Facades\Notification::send($users, new \Modules\Notifications\Notifications\GeneralNotification(
                title: __('New Lead Created'),
                message: __('A new lead named ":title" has been added.', ['title' => $lead->title]),
                url: route('leads.board'),
                type: 'success',
                icon: 'las la-bullseye'
            ));

            $this->closeModal();
            $this->loadData();
            $this->dispatch('lead-added');

            session()->flash('message', __('crm::crm.lead_added_successfully'));
        } catch (\Exception $e) {
            Log::error('LeadsBoard addLead error: '.$e->getMessage());
            session()->flash('error', __('crm::crm.error_adding_lead'));
        }
    }

    private function resetNewLead(): void
    {
        $this->newLead = [
            'title' => '',
            'client_id' => '',
            'amount' => '',
            'source' => '',
            'assigned_to' => '',
            'description' => '',
        ];
        $this->clientSearch = '';
        $this->selectedClientText = '';
        $this->filteredClients = [];
        $this->showClientDropdown = false;
    }

    private function resetEditingLead(): void
    {
        $this->editingLead = [
            'id' => '',
            'title' => '',
            'client_id' => '',
            'amount' => '',
            'source' => '',
            'assigned_to' => '',
            'description' => '',
        ];
    }

    public function deleteLead(int $leadId): void
    {
        abort_unless(auth()->user()->can('delete Leads'), 403);
        try {
            $lead = Lead::find($leadId);
            if ($lead) {
                $leadTitle = $lead->title;
                $lead->delete();

                $users = \App\Models\User::all();
                \Illuminate\Support\Facades\Notification::send($users, new \Modules\Notifications\Notifications\GeneralNotification(
                    title: __('Lead Deleted'),
                    message: __('Lead ":title" has been deleted.', ['title' => $leadTitle]),
                    url: route('leads.board'),
                    type: 'warning',
                    icon: 'las la-trash'
                ));

                $this->loadData();
                session()->flash('message', __('crm::crm.lead_deleted_successfully'));
            }
        } catch (\Exception $e) {
            Log::error('LeadsBoard deleteLead error: '.$e->getMessage());
            session()->flash('error', __('crm::crm.error_deleting_lead'));
        }
    }

    public function resetFilters(): void
    {
        $this->search = '';
        $this->filterStatus = 'all';
        $this->filterSource = 'all';
        $this->filterAssignedTo = 'all';
        $this->filterClient = 'all';
        $this->filterDateFrom = '';
        $this->filterDateTo = '';
        $this->loadData();
    }

    public function updatedSearch(): void
    {
        $this->loadData();
    }

    public function updatedFilterStatus(): void
    {
        $this->loadData();
    }

    public function updatedFilterSource(): void
    {
        $this->loadData();
    }

    public function updatedFilterAssignedTo(): void
    {
        $this->loadData();
    }

    public function updatedFilterClient(): void
    {
        $this->loadData();
    }

    public function updatedFilterDateFrom(): void
    {
        $this->loadData();
    }

    public function updatedFilterDateTo(): void
    {
        $this->loadData();
    }

    public function render(): \Illuminate\View\View
    {
        return view('crm::livewire.leads-board');
    }
}
