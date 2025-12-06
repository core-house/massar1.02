<?php

declare(strict_types=1);

use Livewire\Volt\Component;
use Modules\Recruitment\Models\Termination;
use Modules\Recruitment\Models\Contract;
use App\Models\Employee;
use Livewire\WithPagination;
use Livewire\Attributes\Rule;
use Livewire\Attributes\Computed;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

new class extends Component {
    use WithPagination;
    protected string $paginationTheme = 'bootstrap';

    public string $search = '';
    public string $filter_type = '';
    public bool $showModal = false;
    public bool $isEdit = false;
    public ?int $terminationId = null;

    #[Rule('required|exists:employees,id')]
    public ?int $employee_id = null;

    #[Rule('nullable|exists:contracts,id')]
    public ?int $contract_id = null;

    #[Rule('required|in:resignation,dismissal,death,retirement')]
    public string $termination_type = 'resignation';

    #[Rule('required|date')]
    public ?string $termination_date = null;

    #[Rule('nullable|string')]
    public ?string $reason = null;

    #[Rule('nullable|numeric|min:0')]
    public ?float $final_settlement = null;

    protected array $queryString = ['search', 'filter_type'];

    public function mount(): void
    {
        $this->resetForm();
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFilterType(): void
    {
        $this->resetPage();
    }

    public function resetForm(): void
    {
        $this->reset([
            'employee_id', 'contract_id', 'termination_type', 'termination_date',
            'reason', 'final_settlement', 'terminationId', 'isEdit'
        ]);
        $this->resetValidation();
    }

    public function create(): void
    {
        $this->resetForm();
        $this->showModal = true;
        $this->dispatch('showModal');
    }

    public function edit(int $id): void
    {
        $termination = Termination::findOrFail($id);
        $this->terminationId = $termination->id;
        $this->employee_id = $termination->employee_id;
        $this->contract_id = $termination->contract_id;
        $this->termination_type = $termination->termination_type;
        $this->termination_date = $termination->termination_date?->format('Y-m-d');
        $this->reason = $termination->reason;
        $this->final_settlement = $termination->final_settlement;
        $this->isEdit = true;
        $this->showModal = true;
        $this->dispatch('showModal');
    }

    public function save(): void
    {
        $validated = $this->validate();

        if ($this->isEdit) {
            $termination = Termination::findOrFail($this->terminationId);
            $termination->update($validated);
            session()->flash('message', __('recruitment.termination_updated_successfully'));
        } else {
            $validated['created_by'] = auth()->id();
            
            // تعيين branch_id تلقائياً إذا لم يكن محدداً
            if (empty($validated['branch_id'])) {
                $validated['branch_id'] = optional(Auth::user())
                    ->branches()
                    ->where('branches.is_active', 1)
                    ->value('branches.id');
            }
            
            Termination::create($validated);
            session()->flash('message', __('recruitment.termination_created_successfully'));
        }

        $this->resetForm();
        $this->showModal = false;
        $this->dispatch('closeModal');
    }

    public function delete(int $id): void
    {
        $termination = Termination::findOrFail($id);
        $termination->delete();
        session()->flash('message', __('recruitment.termination_deleted_successfully'));
    }

    public function closeModal(): void
    {
        $this->resetForm();
        $this->showModal = false;
        $this->dispatch('closeModal');
    }

    #[Computed]
    public function terminations(): LengthAwarePaginator
    {
        return Termination::with(['employee', 'contract', 'createdBy'])
            ->when($this->search, function ($query) {
                $query->whereHas('employee', function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->filter_type, function ($query) {
                $query->where('termination_type', $this->filter_type);
            })
            ->latest('termination_date')
            ->paginate(10);
    }

    #[Computed]
    public function employees(): \Illuminate\Database\Eloquent\Collection
    {
        return Employee::orderBy('name')->get();
    }

    #[Computed]
    public function contracts(): \Illuminate\Database\Eloquent\Collection
    {
        return Contract::orderBy('created_at', 'desc')->get();
    }
}; ?>

<div>
    @if (session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Header -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="d-flex align-items-center">
                <div class="me-3">
                    <i class="mdi mdi-account-remove text-primary" style="font-size: 2.5rem;"></i>
                </div>
                <div>
                    <h4 class="mb-1">{{ __('recruitment.terminations') }}</h4>
                    <p class="text-muted mb-0">{{ __('recruitment.manage_terminations') }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-6 text-end">
            @can('create Terminations')
                <button wire:click="create" 
                        class="btn btn-main btn-lg shadow-sm"
                        wire:loading.attr="disabled"
                        wire:target="create">
                    <span wire:loading.remove wire:target="create">
                        <i class="mdi mdi-plus me-2"></i> {{ __('recruitment.create_termination') }}
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
                        <input wire:model.live.debounce.300ms="search" type="text" class="form-control ps-5" placeholder="{{ __('recruitment.search_terminations') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <select wire:model.live="filter_type" class="form-select">
                        <option value="">{{ __('recruitment.all_types') }}</option>
                        <option value="resignation">{{ __('recruitment.resignation') }}</option>
                        <option value="dismissal">{{ __('recruitment.dismissal') }}</option>
                        <option value="death">{{ __('recruitment.death') }}</option>
                        <option value="retirement">{{ __('recruitment.retirement') }}</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Terminations Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-0 py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h6 class="mb-0">
                    <i class="mdi mdi-format-list-bulleted me-2"></i>{{ __('recruitment.terminations_list') }}
                    <span class="badge bg-primary ms-2">{{ $this->terminations->total() }}</span>
                </h6>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="border-0 fw-semibold">{{ __('recruitment.employee') }}</th>
                            <th class="border-0 fw-semibold">{{ __('recruitment.termination_type') }}</th>
                            <th class="border-0 fw-semibold">{{ __('recruitment.termination_date') }}</th>
                            <th class="border-0 fw-semibold">{{ __('recruitment.reason') }}</th>
                            <th class="border-0 fw-semibold">{{ __('recruitment.final_settlement') }}</th>
                            @canany(['edit Terminations', 'delete Terminations'])
                                <th class="border-0 fw-semibold text-center">{{ __('hr.actions') }}</th>
                            @endcanany
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($this->terminations as $termination)
                            <tr>
                                <td>{{ $termination->employee?->name ?? __('recruitment.no_employee') }}</td>
                                <td>
                                    <span class="badge bg-{{ $termination->termination_type === 'resignation' ? 'info' : ($termination->termination_type === 'dismissal' ? 'danger' : 'secondary') }}">
                                        {{ __('recruitment.' . $termination->termination_type) }}
                                    </span>
                                </td>
                                <td>{{ $termination->termination_date?->format('Y-m-d') }}</td>
                                <td>{{ Str::limit($termination->reason ?? __('recruitment.no_reason'), 50) }}</td>
                                <td>{{ $termination->final_settlement ? number_format($termination->final_settlement, 2) : __('recruitment.no_settlement') }}</td>
                                @canany(['edit Terminations', 'delete Terminations'])
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-1">
                                            @can('edit Terminations')
                                                <button wire:click="edit({{ $termination->id }})" 
                                                        class="btn btn-sm btn-outline-warning" 
                                                        title="{{ __('hr.edit') }}"
                                                        wire:loading.attr="disabled"
                                                        wire:target="edit({{ $termination->id }})">
                                                    <span wire:loading.remove wire:target="edit({{ $termination->id }})">
                                                        <i class="mdi mdi-pencil"></i>
                                                    </span>
                                                    <span wire:loading wire:target="edit({{ $termination->id }})">
                                                        <i class="mdi mdi-loading mdi-spin"></i>
                                                    </span>
                                                </button>
                                            @endcan
                                            @can('delete Terminations')
                                                <button wire:click="delete({{ $termination->id }})" 
                                                        wire:confirm="{{ __('recruitment.confirm_delete_termination') }}"
                                                        class="btn btn-sm btn-outline-danger" 
                                                        title="{{ __('hr.delete') }}"
                                                        wire:loading.attr="disabled"
                                                        wire:target="delete({{ $termination->id }})">
                                                    <span wire:loading.remove wire:target="delete({{ $termination->id }})">
                                                        <i class="mdi mdi-delete"></i>
                                                    </span>
                                                    <span wire:loading wire:target="delete({{ $termination->id }})">
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
                                <td colspan="{{ auth()->user()->canany(['edit Terminations', 'delete Terminations']) ? 6 : 5 }}" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="mdi mdi-account-remove-outline text-muted" style="font-size: 4rem;"></i>
                                        <h5 class="mt-3 mb-2">{{ __('recruitment.no_terminations_found') }}</h5>
                                        <p class="mb-3">{{ __('recruitment.start_by_adding_first_termination') }}</p>
                                        @can('create Terminations')
                                            <button wire:click="create" 
                                                    class="btn btn-main"
                                                    wire:loading.attr="disabled"
                                                    wire:target="create">
                                                <span wire:loading.remove wire:target="create">
                                                    <i class="mdi mdi-plus me-2"></i> {{ __('recruitment.add_first_termination') }}
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
        @if($this->terminations->hasPages())
            <div class="card-footer bg-white border-0">
                {{ $this->terminations->links() }}
            </div>
        @endif
    </div>

    <!-- Modal -->
    <div wire:ignore.self class="modal fade" id="terminationModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-lg">
            <form wire:submit.prevent="save">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ $isEdit ? __('recruitment.edit_termination') : __('recruitment.create_termination') }}</h5>
                        <button type="button" class="btn-close" wire:click="closeModal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">{{ __('recruitment.employee') }} <span class="text-danger">*</span></label>
                                <select wire:model.blur="employee_id" class="form-select" required>
                                    <option value="">{{ __('recruitment.select_employee') }}</option>
                                    @foreach($this->employees as $employee)
                                        <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                                    @endforeach
                                </select>
                                @error('employee_id') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
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
                                <label class="form-label">{{ __('recruitment.termination_type') }} <span class="text-danger">*</span></label>
                                <select wire:model.blur="termination_type" class="form-select" required>
                                    <option value="resignation">{{ __('recruitment.resignation') }}</option>
                                    <option value="dismissal">{{ __('recruitment.dismissal') }}</option>
                                    <option value="death">{{ __('recruitment.death') }}</option>
                                    <option value="retirement">{{ __('recruitment.retirement') }}</option>
                                </select>
                                @error('termination_type') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{ __('recruitment.termination_date') }} <span class="text-danger">*</span></label>
                                <input wire:model.blur="termination_date" type="date" class="form-control" required>
                                @error('termination_date') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">{{ __('recruitment.reason') }}</label>
                                <textarea wire:model.blur="reason" class="form-control" rows="3"></textarea>
                                @error('reason') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">{{ __('recruitment.final_settlement') }}</label>
                                <input wire:model.blur="final_settlement" type="number" step="0.01" class="form-control" min="0">
                                @error('final_settlement') <span class="text-danger">{{ $message }}</span> @enderror
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
</div>

@script
<script>
    document.addEventListener('livewire:initialized', () => {
        let modalInstance = null;
        const modalElement = document.getElementById('terminationModal');

        if (!modalElement) return;

        // إنشاء instance واحدة فقط
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
            // لا نحذف الـ instance، فقط نتركه للاستخدام مرة أخرى
        });
    });
</script>
@endscript
