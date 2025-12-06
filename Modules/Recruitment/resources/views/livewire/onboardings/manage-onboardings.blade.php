<?php

declare(strict_types=1);

use Livewire\Volt\Component;
use Modules\Recruitment\Models\Onboarding;
use Modules\Recruitment\Models\Contract;
use Modules\Recruitment\Models\Interview;
use Modules\Recruitment\Models\Cv;
use App\Models\Employee;
use Modules\Accounts\Models\AccHead;
use Modules\Accounts\Services\AccountService;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Livewire\Attributes\Rule;
use Livewire\Attributes\Computed;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

new class extends Component {
    use WithPagination, WithFileUploads;
    protected string $paginationTheme = 'bootstrap';

    public string $search = '';
    public string $filter_status = '';
    public bool $showModal = false;
    public bool $isEdit = false;
    public ?int $onboardingId = null;
    public bool $showViewModal = false;
    public ?Onboarding $viewOnboarding = null;

    #[Rule('nullable|exists:contracts,id')]
    public ?int $contract_id = null;

    #[Rule('nullable|exists:interviews,id')]
    public ?int $interview_id = null;

    #[Rule('required|exists:cvs,id')]
    public ?int $cv_id = null;

    #[Rule('required|in:pending,in_progress,completed,cancelled')]
    public string $status = 'pending';

    #[Rule('nullable|date')]
    public ?string $start_date = null;

    #[Rule('nullable|date|after_or_equal:start_date')]
    public ?string $completion_date = null;

    #[Rule('nullable|string')]
    public ?string $notes = null;

    public array $checklist = [];

    #[Rule('nullable|array')]
    public array $documents = [];

    protected array $queryString = ['search', 'filter_status'];

    public function mount(): void
    {
        $this->resetForm();
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFilterStatus(): void
    {
        $this->resetPage();
    }

    public function resetForm(): void
    {
        $this->reset([
            'contract_id', 'interview_id', 'cv_id', 'status',
            'start_date', 'completion_date', 'notes', 'checklist', 'documents',
            'onboardingId', 'isEdit'
        ]);
        $this->resetValidation();
    }

    public function create(): void
    {
        $this->resetForm();
        $this->checklist = [['item' => '', 'completed' => false]];
        $this->documents = [];
        $this->showModal = true;
        $this->dispatch('showModal');
    }

    public function edit(int $id): void
    {
        $onboarding = Onboarding::findOrFail($id);
        $this->onboardingId = $onboarding->id;
        $this->contract_id = $onboarding->contract_id;
        $this->interview_id = $onboarding->interview_id;
        $this->cv_id = $onboarding->cv_id;
        $this->status = $onboarding->status;
        $this->start_date = $onboarding->start_date?->format('Y-m-d');
        $this->completion_date = $onboarding->completion_date?->format('Y-m-d');
        $this->notes = $onboarding->notes;
        $this->checklist = $onboarding->checklist ?? [['item' => '', 'completed' => false]];
        $this->documents = [];
        $this->isEdit = true;
        $this->showModal = true;
        $this->dispatch('showModal');
    }

    public function save(): void
    {
        $validated = $this->validate();

        // تنظيف checklist من العناصر الفارغة
        $validated['checklist'] = array_filter($this->checklist, function($item) {
            return !empty($item['item']);
        });

        DB::transaction(function () use ($validated) {
            if ($this->isEdit) {
                $onboarding = Onboarding::findOrFail($this->onboardingId);
                $oldStatus = $onboarding->status;
                $onboarding->update($validated);
                
                // إذا تم تغيير الحالة إلى completed، تحقق من إنشاء الموظف
                if ($validated['status'] === 'completed' && $oldStatus !== 'completed') {
                    $this->createEmployeeIfNeeded($onboarding);
                }
                
                session()->flash('message', __('recruitment.onboarding_updated_successfully'));
            } else {
                $validated['created_by'] = auth()->id();
                
                // تعيين branch_id تلقائياً إذا لم يكن محدداً
                if (empty($validated['branch_id'])) {
                    $validated['branch_id'] = optional(Auth::user())
                        ->branches()
                        ->where('branches.is_active', 1)
                        ->value('branches.id');
                }
                
                $onboarding = Onboarding::create($validated);
                
                // إذا كانت الحالة completed من البداية، تحقق من إنشاء الموظف
                if ($validated['status'] === 'completed') {
                    $this->createEmployeeIfNeeded($onboarding);
                }
                
                session()->flash('message', __('recruitment.onboarding_created_successfully'));
            }

            // Handle file uploads - نفس الطريقة المستخدمة في Covenant
            if (!empty($this->documents) && is_array($this->documents)) {
                foreach ($this->documents as $file) {
                    if ($file && method_exists($file, 'isValid') && $file->isValid()) {
                        try {
                            // استخدام addMediaFromStream كما في Covenant
                            $onboarding->addMediaFromStream($file->readStream())
                                ->usingName($file->getClientOriginalName())
                                ->usingFileName($file->getClientOriginalName())
                                ->toMediaCollection('onboarding_documents');
                        } catch (\Exception $e) {
                            session()->flash('error', __('recruitment.document_upload_failed') . ': ' . $e->getMessage());
                        }
                    }
                }
            }
        });

        $this->resetForm();
        $this->showModal = false;
        $this->dispatch('closeModal');
    }

    /**
     * إنشاء موظف تلقائياً عند إكمال إجراءات التوظيف
     */
    private function createEmployeeIfNeeded(Onboarding $onboarding): void
    {
        // التحقق من أن الموظف لم يتم إنشاؤه من قبل
        if ($onboarding->employee_id) {
            return;
        }

        // التحقق من أن جميع المهام في checklist مكتملة
        if (!empty($onboarding->checklist)) {
            $allCompleted = true;
            foreach ($onboarding->checklist as $item) {
                if (empty($item['completed']) || !$item['completed']) {
                    $allCompleted = false;
                    break;
                }
            }
            
            if (!$allCompleted) {
                session()->flash('warning', __('recruitment.cannot_create_employee_checklist_incomplete'));
                return;
            }
        }

        // الحصول على بيانات CV
        $cv = $onboarding->cv;
        if (!$cv) {
            session()->flash('error', __('recruitment.cv_required_to_create_employee'));
            return;
        }

        // الحصول على salary_basic_account_id (الحساب الأساسي للرواتب) - نفس الطريقة في HandlesEmployeeForm
        $salaryBasicAccount = AccHead::where([
            'acc_type' => 5,
            'is_basic' => 1,
        ])->first();

        if (!$salaryBasicAccount) {
            session()->flash('error', __('recruitment.salary_basic_account_not_found'));
            return;
        }

        // تحويل marital_status من English إلى Arabic
        $maritalStatusMap = [
            'single' => 'غير متزوج',
            'married' => 'متزوج',
            'divorced' => 'مطلق',
            'widowed' => 'أرمل',
        ];
        $maritalStatus = $cv->marital_status;
        if (isset($maritalStatusMap[$maritalStatus])) {
            $maritalStatus = $maritalStatusMap[$maritalStatus];
        }

        // إنشاء الموظف بالحد الأدنى من الحقول المطلوبة
        try {
            $employee = Employee::create([
                'name' => $cv->name,
                'email' => $cv->email ?: (strtolower(str_replace(' ', '.', $cv->name)) . '@company.com'),
                'phone' => $cv->phone ?? 123456789,
                'password' => Hash::make('123456'), // كلمة مرور افتراضية
                'status' => 'مفعل',
                'gender' => $cv->gender,
                'date_of_birth' => $cv->birth_date ? date('Y-m-d', strtotime($cv->birth_date)) : null,
                'marital_status' => $maritalStatus,
                'date_of_hire' => $onboarding->completion_date ?? now(),
                'branch_id' => $onboarding->branch_id,
            ]);

            // ربط Onboarding بالموظف
            $onboarding->update(['employee_id' => $employee->id]);

            // إنشاء حساب المحاسبة للموظف - نفس الطريقة في HandlesEmployeeForm
            $this->syncEmployeeAccount($employee, $salaryBasicAccount->id, 0); // opening_balance = 0

            session()->flash('message', __('recruitment.employee_created_successfully', ['name' => $employee->name]));
        } catch (\Exception $e) {
            session()->flash('error', __('recruitment.employee_creation_failed') . ': ' . $e->getMessage());
        }
    }

    /**
     * إنشاء حساب محاسبة للموظف - نفس الطريقة في HandlesEmployeeForm
     */
    private function syncEmployeeAccount(Employee $employee, int $parentAccountId, float $openingBalance = 0): void
    {
        $employee->load('account');

        // Get the parent account and its children once
        $parentAccount = AccHead::where('id', $parentAccountId)->first();

        if (!$parentAccount) {
            throw new \Exception('Parent account not found');
        }

        // Get all direct children and find the maximum code value
        $children = $parentAccount->haveChildrens()->get();
        
        $maxChildCode = null;
        foreach ($children as $child) {
            if (is_numeric($child->code)) {
                $childCodeValue = (int) $child->code;
                if ($maxChildCode === null || $childCodeValue > $maxChildCode) {
                    $maxChildCode = $childCodeValue;
                }
            }
        }
        
        // Generate new code: max child code + 1, or find next available if no children
        if ($maxChildCode !== null) {
            $newCode = (string) ($maxChildCode + 1);
        } else {
            // No children exist - find next available code starting from parent + 1
            $parentCodeValue = (int) $parentAccount->code;
            $newCodeValue = $parentCodeValue + 1;
            
            // Ensure the code doesn't already exist as a non-child account
            while (AccHead::where('code', (string) $newCodeValue)->exists()) {
                $newCodeValue++;
            }
            
            $newCode = (string) $newCodeValue;
        }

        $accountData = [
            'code' => $newCode,
            'aname' => $employee->name,
            'parent_id' => $parentAccountId,
            'acc_type' => 5,
            'accountable_type' => Employee::class,
            'accountable_id' => $employee->id,
        ];

        if (!$employee->account) {
            $employee->account()->create($accountData);
            $employee->load('account');
            app(AccountService::class)->setStartBalances([$employee->account->id => $openingBalance]);
            app(AccountService::class)->recalculateOpeningCapitalAndSyncJournal();
        } else {
            unset($accountData['accountable_type'], $accountData['accountable_id']);
            $employee->account->update($accountData);
            app(AccountService::class)->setStartBalances([$employee->account->id => $openingBalance]);
            app(AccountService::class)->recalculateOpeningCapitalAndSyncJournal();
        }

        // Create sub-accounts (advance, deductions, rewards)
        $this->createEmployeeSubAccounts($employee);
    }

    /**
     * Create sub-accounts for employee (advance, deductions, rewards) - نفس الطريقة في HandlesEmployeeForm
     */
    private function createEmployeeSubAccounts(Employee $employee): void
    {
        // 1. حساب السلف (تحت 110601)
        $this->createOrUpdateSubAccount(
            $employee,
            '110601',
            'سلف '.$employee->name,
            'advance'
        );

        // 2. حساب الجزاءات والخصومات (تحت 210402 - جزاءات وخصومات الموظفين)
        $this->createOrUpdateSubAccount(
            $employee,
            '210402',
            'جزاءات وخصومات '.$employee->name,
            'deductions'
        );

        // 3. حساب المكافآت والحوافز (تحت 5303 - المكافآت والحوافز)
        $this->createOrUpdateSubAccount(
            $employee,
            '5303',
            'مكافآت وحوافز '.$employee->name,
            'rewards'
        );
    }

    /**
     * Create or update a sub-account for employee - نفس الطريقة في HandlesEmployeeForm
     */
    private function createOrUpdateSubAccount(Employee $employee, string $parentCode, string $accountName, string $accountType): void
    {
        $parentAccount = AccHead::where('code', $parentCode)->first();

        if (!$parentAccount) {
            Log::warning("Parent account with code {$parentCode} not found for employee {$employee->id}");
            return;
        }

        // Check if account already exists for this employee
        $existingAccount = AccHead::where('accountable_type', Employee::class)
            ->where('accountable_id', $employee->id)
            ->where('aname', $accountName)
            ->where('parent_id', $parentAccount->id)
            ->first();

        if ($existingAccount) {
            // Update existing account
            $existingAccount->update([
                'aname' => $accountName,
                'mdtime' => now(),
            ]);
            return;
        }

        // Generate code for new account
        // Get all direct children and find the maximum code value
        $children = $parentAccount->haveChildrens()->get();
        
        $maxChildCode = null;
        foreach ($children as $child) {
            if (is_numeric($child->code)) {
                $childCodeValue = (int) $child->code;
                if ($maxChildCode === null || $childCodeValue > $maxChildCode) {
                    $maxChildCode = $childCodeValue;
                }
            }
        }
        
        // Generate new code: max child code + 1, or find next available if no children
        if ($maxChildCode !== null) {
            $newCode = (string) ($maxChildCode + 1);
        } else {
            // No children exist - find next available code starting from parent + 1
            $parentCodeValue = (int) $parentAccount->code;
            $newCodeValue = $parentCodeValue + 1;
            
            // Ensure the code doesn't already exist as a non-child account
            // or find the next available sequential code
            while (AccHead::where('code', (string) $newCodeValue)->exists()) {
                $newCodeValue++;
            }
            
            $newCode = (string) $newCodeValue;
        }

        // Create new account
        AccHead::create([
            'code' => (string) $newCode,
            'aname' => $accountName,
            'parent_id' => $parentAccount->id,
            'acc_type' => 5,
            'accountable_type' => Employee::class,
            'accountable_id' => $employee->id,
            'is_basic' => 0,
            'deletable' => 0,
            'editable' => 1,
            'is_stock' => 0,
            'is_fund' => 0,
            'start_balance' => 0,
            'credit' => 0,
            'debit' => 0,
            'balance' => 0,
            'crtime' => now(),
            'mdtime' => now(),
        ]);
    }

    public function delete(int $id): void
    {
        $onboarding = Onboarding::findOrFail($id);
        $onboarding->clearMediaCollection('onboarding_documents');
        $onboarding->delete();
        session()->flash('message', __('recruitment.onboarding_deleted_successfully'));
    }

    public function closeModal(): void
    {
        $this->resetForm();
        $this->showModal = false;
        $this->dispatch('closeModal');
    }

    public function addChecklistItem(): void
    {
        $this->checklist[] = ['item' => '', 'completed' => false];
    }

    public function removeChecklistItem(int $index): void
    {
        unset($this->checklist[$index]);
        $this->checklist = array_values($this->checklist);
    }

    public function view(int $id): void
    {
        $this->viewOnboarding = Onboarding::with(['employee', 'contract', 'interview', 'cv', 'createdBy'])->findOrFail($id);
        $this->showViewModal = true;
        $this->dispatch('showViewModal');
    }

    public function closeViewModal(): void
    {
        $this->showViewModal = false;
        $this->viewOnboarding = null;
        $this->dispatch('closeViewModal');
    }

    public function downloadDocument(int $onboardingId, int $mediaId)
    {
        $onboarding = Onboarding::findOrFail($onboardingId);
        $media = $onboarding->getMedia('onboarding_documents')->where('id', $mediaId)->first();
        
        if ($media) {
            try {
                return response()->download($media->getPath(), $media->file_name);
            } catch (\Exception $e) {
                session()->flash('error', __('recruitment.document_download_failed') . ': ' . $e->getMessage());
                return null;
            }
        }
        
        session()->flash('error', __('recruitment.document_not_found'));
        return null;
    }

    public function deleteDocument(int $onboardingId, int $mediaId): void
    {
        $onboarding = Onboarding::findOrFail($onboardingId);
        $media = $onboarding->getMedia('onboarding_documents')->where('id', $mediaId)->first();
        
        if ($media) {
            $media->delete();
            session()->flash('message', __('recruitment.document_deleted_successfully'));
        }
    }

    #[Computed]
    public function onboardings(): LengthAwarePaginator
    {
        return Onboarding::with(['employee', 'contract', 'interview', 'cv', 'createdBy'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->whereHas('cv', function ($cv) {
                        $cv->where('name', 'like', '%' . $this->search . '%');
                    })
                    ->orWhere('notes', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->filter_status, function ($query) {
                $query->where('status', $this->filter_status);
            })
            ->latest('start_date')
            ->paginate(10);
    }


    #[Computed]
    public function contracts(): \Illuminate\Database\Eloquent\Collection
    {
        return Contract::orderBy('created_at', 'desc')->get();
    }

    #[Computed]
    public function interviews(): \Illuminate\Database\Eloquent\Collection
    {
        return Interview::where('result', 'accepted')->orderBy('scheduled_at', 'desc')->get();
    }

    #[Computed]
    public function cvs(): \Illuminate\Database\Eloquent\Collection
    {
        return Cv::orderBy('name')->get();
    }
}; ?>

<div>
    @if (session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Header -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="d-flex align-items-center">
                <div class="me-3">
                    <i class="mdi mdi-account-plus text-primary" style="font-size: 2.5rem;"></i>
                </div>
                <div>
                    <h4 class="mb-1">{{ __('recruitment.onboardings') }}</h4>
                    <p class="text-muted mb-0">{{ __('recruitment.manage_onboardings') }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-6 text-end">
            @can('create Onboardings')
                <button wire:click="create" 
                        class="btn btn-main btn-lg shadow-sm"
                        wire:loading.attr="disabled"
                        wire:target="create">
                    <span wire:loading.remove wire:target="create">
                        <i class="mdi mdi-plus me-2"></i> {{ __('recruitment.create_onboarding') }}
                    </span>
                    <span wire:loading wire:target="create">
                        <i class="mdi mdi-loading mdi-spin me-2"></i> {{ __('hr.opening') }}...
                    </span>
                </button>
            @endcan
        </div>
    </div>

    <!-- Filters -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <div class="position-relative">
                        <i class="mdi mdi-magnify position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                        <input wire:model.live.debounce.300ms="search" type="text" class="form-control ps-5" placeholder="{{ __('recruitment.search_onboardings') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <select wire:model.live="filter_status" class="form-select">
                        <option value="">{{ __('recruitment.all_status') }}</option>
                        <option value="pending">{{ __('recruitment.pending') }}</option>
                        <option value="in_progress">{{ __('recruitment.in_progress') }}</option>
                        <option value="completed">{{ __('recruitment.completed') }}</option>
                        <option value="cancelled">{{ __('recruitment.cancelled') }}</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Onboardings Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-0 py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h6 class="mb-0">
                    <i class="mdi mdi-format-list-bulleted me-2"></i>{{ __('recruitment.onboardings_list') }}
                    <span class="badge bg-primary ms-2">{{ $this->onboardings->total() }}</span>
                </h6>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="border-0 fw-semibold">{{ __('recruitment.candidate') }}</th>
                            <th class="border-0 fw-semibold">{{ __('recruitment.contract') }}</th>
                            <th class="border-0 fw-semibold">{{ __('recruitment.status') }}</th>
                            <th class="border-0 fw-semibold">{{ __('recruitment.start_date') }}</th>
                            <th class="border-0 fw-semibold">{{ __('recruitment.completion_date') }}</th>
                            <th class="border-0 fw-semibold">{{ __('recruitment.documents') }}</th>
                            @canany(['view Onboardings', 'edit Onboardings', 'delete Onboardings'])
                                <th class="border-0 fw-semibold text-center">{{ __('hr.actions') }}</th>
                            @endcanany
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($this->onboardings as $onboarding)
                            <tr>
                                <td>
                                    @if($onboarding->employee)
                                        <span class="badge bg-success me-1">{{ __('recruitment.employee_created') }}</span>
                                    @endif
                                    {{ $onboarding->cv?->name ?? __('recruitment.no_candidate') }}
                                </td>
                                <td>{{ $onboarding->contract?->name ?? __('recruitment.no_contract') }}</td>
                                <td>
                                    <span class="badge bg-{{ $onboarding->status === 'completed' ? 'success' : ($onboarding->status === 'cancelled' ? 'danger' : ($onboarding->status === 'in_progress' ? 'warning' : 'secondary')) }}">
                                        {{ __('recruitment.' . $onboarding->status) }}
                                    </span>
                                </td>
                                <td>{{ $onboarding->start_date?->format('Y-m-d') ?? '-' }}</td>
                                <td>{{ $onboarding->completion_date?->format('Y-m-d') ?? '-' }}</td>
                                <td>
                                    @if($onboarding->getMedia('onboarding_documents')->count() > 0)
                                        <span class="badge bg-info" title="{{ __('recruitment.documents') }}">
                                            <i class="mdi mdi-file-document me-1"></i>
                                            {{ $onboarding->getMedia('onboarding_documents')->count() }}
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                @canany(['view Onboardings', 'edit Onboardings', 'delete Onboardings'])
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-1">
                                            @can('view Onboardings')
                                                <button wire:click="view({{ $onboarding->id }})" 
                                                        class="btn btn-sm btn-outline-info" 
                                                        title="{{ __('hr.view') }}"
                                                        wire:loading.attr="disabled"
                                                        wire:target="view({{ $onboarding->id }})">
                                                    <span wire:loading.remove wire:target="view({{ $onboarding->id }})">
                                                        <i class="mdi mdi-eye"></i>
                                                    </span>
                                                    <span wire:loading wire:target="view({{ $onboarding->id }})">
                                                        <i class="mdi mdi-loading mdi-spin"></i>
                                                    </span>
                                                </button>
                                            @endcan
                                            @can('edit Onboardings')
                                                <button wire:click="edit({{ $onboarding->id }})" 
                                                        class="btn btn-sm btn-outline-warning" 
                                                        title="{{ __('hr.edit') }}"
                                                        wire:loading.attr="disabled"
                                                        wire:target="edit({{ $onboarding->id }})">
                                                    <span wire:loading.remove wire:target="edit({{ $onboarding->id }})">
                                                        <i class="mdi mdi-pencil"></i>
                                                    </span>
                                                    <span wire:loading wire:target="edit({{ $onboarding->id }})">
                                                        <i class="mdi mdi-loading mdi-spin"></i>
                                                    </span>
                                                </button>
                                            @endcan
                                            @can('delete Onboardings')
                                                <button wire:click="delete({{ $onboarding->id }})" 
                                                        wire:confirm="{{ __('recruitment.confirm_delete_onboarding') }}"
                                                        class="btn btn-sm btn-outline-danger" 
                                                        title="{{ __('hr.delete') }}"
                                                        wire:loading.attr="disabled"
                                                        wire:target="delete({{ $onboarding->id }})">
                                                    <span wire:loading.remove wire:target="delete({{ $onboarding->id }})">
                                                        <i class="mdi mdi-delete"></i>
                                                    </span>
                                                    <span wire:loading wire:target="delete({{ $onboarding->id }})">
                                                        <i class="mdi mdi-loading mdi-spin"></i>
                                                    </span>
                                                </button>
                                            @endcan
                                        </div>
                                    </td>
                                @endcanany
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ auth()->user()->canany(['view Onboardings', 'edit Onboardings', 'delete Onboardings']) ? 7 : 6 }}" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="mdi mdi-account-plus-outline text-muted" style="font-size: 4rem;"></i>
                                        <h5 class="mt-3 mb-2">{{ __('recruitment.no_onboardings_found') }}</h5>
                                        <p class="mb-3">{{ __('recruitment.start_by_adding_first_onboarding') }}</p>
                                        @can('create Onboardings')
                                            <button wire:click="create" 
                                                    class="btn btn-main"
                                                    wire:loading.attr="disabled"
                                                    wire:target="create">
                                                <span wire:loading.remove wire:target="create">
                                                    <i class="mdi mdi-plus me-2"></i> {{ __('recruitment.add_first_onboarding') }}
                                                </span>
                                                <span wire:loading wire:target="create">
                                                    <i class="mdi mdi-loading mdi-spin me-2"></i> {{ __('hr.opening') }}...
                                                </span>
                                            </button>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($this->onboardings->hasPages())
            <div class="card-footer bg-white border-0">
                {{ $this->onboardings->links() }}
            </div>
        @endif
    </div>

    <!-- Modal -->
    <div wire:ignore.self class="modal fade" id="onboardingModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-fullscreen" style="width: 100vw; max-width: 100vw; height: 100vh; margin: 0;">
            <form wire:submit.prevent="save">
                <div class="modal-content" style="height: 100vh; border-radius: 0; border: none;">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ $isEdit ? __('recruitment.edit_onboarding') : __('recruitment.create_onboarding') }}</h5>
                        <button type="button" class="btn-close" wire:click="closeModal"></button>
                    </div>
                    <div class="modal-body" style="overflow-y: auto; max-height: calc(100vh - 150px);">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">{{ __('recruitment.contract') }}</label>
                                <select wire:model.blur="contract_id" class="form-select">
                                    <option value="">{{ __('recruitment.select_contract') }}</option>
                                    @foreach($this->contracts as $contract)
                                        <option value="{{ $contract->id }}">{{ $contract->name ?? __('recruitment.contract') . ' #' . $contract->id }}</option>
                                    @endforeach
                                </select>
                                @error('contract_id') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{ __('recruitment.candidate') }} <span class="text-danger">*</span></label>
                                <select wire:model.blur="cv_id" class="form-select" required>
                                    <option value="">{{ __('recruitment.select_cv') }}</option>
                                    @foreach($this->cvs as $cv)
                                        <option value="{{ $cv->id }}">{{ $cv->name }}</option>
                                    @endforeach
                                </select>
                                @error('cv_id') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{ __('recruitment.interview') }}</label>
                                <select wire:model.blur="interview_id" class="form-select">
                                    <option value="">{{ __('recruitment.select_interview') }}</option>
                                    @foreach($this->interviews as $interview)
                                        <option value="{{ $interview->id }}">{{ $interview->cv?->name ?? 'Interview #' . $interview->id }} - {{ $interview->scheduled_at?->format('Y-m-d') }}</option>
                                    @endforeach
                                </select>
                                @error('interview_id') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{ __('recruitment.status') }} <span class="text-danger">*</span></label>
                                <select wire:model.blur="status" class="form-select" required>
                                    <option value="pending">{{ __('recruitment.pending') }}</option>
                                    <option value="in_progress">{{ __('recruitment.in_progress') }}</option>
                                    <option value="completed">{{ __('recruitment.completed') }}</option>
                                    <option value="cancelled">{{ __('recruitment.cancelled') }}</option>
                                </select>
                                @error('status') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{ __('recruitment.start_date') }}</label>
                                <input wire:model.blur="start_date" type="date" class="form-control">
                                @error('start_date') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{ __('recruitment.completion_date') }}</label>
                                <input wire:model.blur="completion_date" type="date" class="form-control">
                                @error('completion_date') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">{{ __('recruitment.notes') }}</label>
                                <textarea wire:model.blur="notes" class="form-control" rows="3"></textarea>
                                @error('notes') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            
                            <!-- Checklist -->
                            <div class="col-md-12">
                                <label class="form-label">{{ __('recruitment.checklist') }}</label>
                                @foreach($checklist as $index => $item)
                                    <div class="d-flex gap-2 mb-2">
                                        <input wire:model="checklist.{{ $index }}.item" type="text" class="form-control" placeholder="{{ __('recruitment.checklist_item') }}">
                                        <div class="form-check d-flex align-items-center">
                                            <input wire:model="checklist.{{ $index }}.completed" type="checkbox" class="form-check-input" id="checklist_{{ $index }}">
                                            <label class="form-check-label ms-2" for="checklist_{{ $index }}">{{ __('recruitment.completed') }}</label>
                                        </div>
                                        @if(count($checklist) > 1)
                                            <button type="button" wire:click="removeChecklistItem({{ $index }})" class="btn btn-danger btn-sm">
                                                <i class="mdi mdi-delete"></i>
                                            </button>
                                        @endif
                                    </div>
                                @endforeach
                                <button type="button" wire:click="addChecklistItem" class="btn btn-sm btn-outline-primary">
                                    <i class="mdi mdi-plus"></i> {{ __('recruitment.add_checklist_item') }}
                                </button>
                            </div>

                            <!-- File Upload -->
                            <div class="col-md-12">
                                <label class="form-label">{{ __('recruitment.documents') }}</label>
                                <input wire:model="documents" type="file" class="form-control" multiple accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                <small class="text-muted">{{ __('recruitment.upload_document') }}</small>
                                @error('documents') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeModal">{{ __('hr.cancel') }}</button>
                        <button type="submit" class="btn btn-main">
                            {{ $isEdit ? __('hr.update') : __('hr.save') }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- View Modal -->
    <div wire:ignore.self class="modal fade" id="viewOnboardingModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-fullscreen" style="width: 100vw; max-width: 100vw; height: 100vh; margin: 0;">
            <div class="modal-content" style="height: 100vh; border-radius: 0; border: none;">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('recruitment.onboardings') }}</h5>
                    <button type="button" class="btn-close" wire:click="closeViewModal"></button>
                </div>
                <div class="modal-body" style="overflow-y: auto; max-height: calc(100vh - 150px);">
                    @if($viewOnboarding)
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">{{ __('recruitment.employee') }}:</label>
                            <p class="mb-0">{{ $viewOnboarding->employee?->name ?? ($viewOnboarding->cv?->name ?? __('recruitment.no_employee')) }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">{{ __('recruitment.contract') }}:</label>
                            <p class="mb-0">{{ $viewOnboarding->contract?->name ?? __('recruitment.no_contract') }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">{{ __('recruitment.candidate') }}:</label>
                            <p class="mb-0">{{ $viewOnboarding->cv?->name ?? __('recruitment.no_candidate') }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">{{ __('recruitment.status') }}:</label>
                            <p class="mb-0">
                                <span class="badge bg-{{ $viewOnboarding->status === 'completed' ? 'success' : ($viewOnboarding->status === 'cancelled' ? 'danger' : ($viewOnboarding->status === 'in_progress' ? 'warning' : 'secondary')) }}">
                                    {{ __('recruitment.' . $viewOnboarding->status) }}
                                </span>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">{{ __('recruitment.start_date') }}:</label>
                            <p class="mb-0">{{ $viewOnboarding->start_date?->format('Y-m-d') ?? '-' }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">{{ __('recruitment.completion_date') }}:</label>
                            <p class="mb-0">{{ $viewOnboarding->completion_date?->format('Y-m-d') ?? '-' }}</p>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-bold">{{ __('recruitment.notes') }}:</label>
                            <p class="mb-0">{{ $viewOnboarding->notes ?? '-' }}</p>
                        </div>
                        @if($viewOnboarding->checklist && count($viewOnboarding->checklist) > 0)
                            <div class="col-md-12">
                                <label class="form-label fw-bold">{{ __('recruitment.checklist') }}:</label>
                                <ul class="list-group">
                                    @foreach($viewOnboarding->checklist as $item)
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            {{ $item['item'] ?? '' }}
                                            @if(isset($item['completed']) && $item['completed'])
                                                <span class="badge bg-success">{{ __('recruitment.completed') }}</span>
                                            @else
                                                <span class="badge bg-secondary">{{ __('recruitment.pending') }}</span>
                                            @endif
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <div class="col-md-12">
                            <label class="form-label fw-bold">{{ __('recruitment.documents') }}:</label>
                            @if($viewOnboarding->getMedia('onboarding_documents')->count() > 0)
                                <div class="list-group">
                                    @foreach($viewOnboarding->getMedia('onboarding_documents') as $media)
                                        <div class="list-group-item d-flex justify-content-between align-items-center">
                                            <div class="d-flex align-items-center">
                                                <i class="mdi mdi-file-document me-2 text-primary"></i>
                                                <div>
                                                    <div class="fw-semibold">{{ $media->name ?? $media->file_name }}</div>
                                                    <small class="text-muted">
                                                        {{ $media->file_name }} 
                                                        ({{ number_format($media->size / 1024, 2) }} KB)
                                                    </small>
                                                </div>
                                            </div>
                                            <div class="d-flex gap-2">
                                                <a href="{{ asset('storage/'.$media->id.'/'.$media->file_name) }}" 
                                                   target="_blank"
                                                   class="btn btn-sm btn-outline-info"
                                                   title="{{ __('recruitment.view_document') }}">
                                                    <i class="mdi mdi-eye"></i>
                                                </a>
                                                <button wire:click="downloadDocument({{ $viewOnboarding->id }}, {{ $media->id }})" 
                                                        class="btn btn-sm btn-outline-primary"
                                                        title="{{ __('recruitment.download_document') }}">
                                                    <i class="mdi mdi-download"></i>
                                                </button>
                                                @can('delete Onboardings')
                                                    <button wire:click="deleteDocument({{ $viewOnboarding->id }}, {{ $media->id }})" 
                                                            wire:confirm="{{ __('recruitment.confirm_delete_document') }}"
                                                            class="btn btn-sm btn-outline-danger"
                                                            title="{{ __('recruitment.delete_document') }}">
                                                        <i class="mdi mdi-delete"></i>
                                                    </button>
                                                @endcan
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-muted">{{ __('recruitment.no_documents') }}</p>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">{{ __('recruitment.created_by') }}:</label>
                            <p class="mb-0">{{ $viewOnboarding->createdBy?->name ?? '-' }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">{{ __('recruitment.created_at') }}:</label>
                            <p class="mb-0">{{ $viewOnboarding->created_at?->format('Y-m-d H:i') ?? '-' }}</p>
                        </div>
                    </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="closeViewModal">{{ __('hr.close') }}</button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    /* Fullscreen Modal Fix for Onboardings */
    #onboardingModal.modal.show,
    #viewOnboardingModal.modal.show {
        display: block !important;
        position: fixed !important;
        top: 0 !important;
        left: 0 !important;
        width: 100vw !important;
        height: 100vh !important;
        z-index: 1055 !important;
        margin: 0 !important;
        padding: 0 !important;
    }

    #onboardingModal .modal-fullscreen,
    #viewOnboardingModal .modal-fullscreen {
        width: 100vw !important;
        max-width: 100vw !important;
        height: 100vh !important;
        margin: 0 !important;
        padding: 0 !important;
    }

    #onboardingModal .modal-fullscreen .modal-content,
    #viewOnboardingModal .modal-fullscreen .modal-content {
        width: 100vw !important;
        max-width: 100vw !important;
        height: 100vh !important;
        border-radius: 0 !important;
        border: none !important;
        margin: 0 !important;
    }

    #onboardingModal .modal-fullscreen .modal-body,
    #viewOnboardingModal .modal-fullscreen .modal-body {
        overflow-y: auto;
        max-height: calc(100vh - 150px);
    }
</style>
@endpush

@script
<script>
    document.addEventListener('livewire:initialized', () => {
        let modalInstance = null;
        const modalElement = document.getElementById('onboardingModal');

        if (!modalElement) return;

        if (!modalInstance) {
            modalInstance = new bootstrap.Modal(modalElement);
        }

        Livewire.on('showModal', () => {
            if (modalInstance && modalElement) {
                modalInstance.show();
            }
        });

        Livewire.on('closeModal', () => {
            if (modalInstance) {
                modalInstance.hide();
            }
        });

        modalElement.addEventListener('hidden.bs.modal', function() {
            // لا نحذف الـ instance
        });

        // View Modal
        let viewModalInstance = null;
        const viewModalElement = document.getElementById('viewOnboardingModal');
        
        if (viewModalElement) {
            if (!viewModalInstance) {
                viewModalInstance = new bootstrap.Modal(viewModalElement);
            }

            Livewire.on('showViewModal', () => {
                if (viewModalInstance && viewModalElement) {
                    viewModalInstance.show();
                }
            });

            Livewire.on('closeViewModal', () => {
                if (viewModalInstance) {
                    viewModalInstance.hide();
                }
            });

            viewModalElement.addEventListener('hidden.bs.modal', function() {
                // لا نحذف الـ instance
            });
        }
    });
</script>
@endscript

