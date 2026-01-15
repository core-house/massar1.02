<?php

declare(strict_types=1);

use Modules\Recruitment\Models\Contract;
use Modules\Recruitment\Models\ContractPoint;
use Modules\HR\Models\ContractType;
use Modules\HR\Models\Employee;
use Modules\HR\Models\EmployeesJob;
use Modules\Recruitment\Models\SalaryPoint;
use Livewire\Volt\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Computed;
use Illuminate\Support\Facades\Auth;

new class extends Component {
    use WithPagination;

    public string $search = '';

    public bool $showModal = false;
    public bool $isEdit = false;
    public ?int $contractId = null;

    public string $name = '';
    public ?int $contract_type_id = null;
    public ?string $contract_start_date = null;
    public ?string $contract_end_date = null;
    public ?float $fixed_work_hours = null;
    public ?float $additional_work_hours = null;
    public ?float $monthly_holidays = null;
    public ?float $monthly_sick_days = null;
    public ?string $information = null;
    public ?int $job_id = null;
    public ?string $job_description = null;
    public ?int $employee_id = null;

    public array $contractPoints = [];
    public array $salaryPoints = [];

    public bool $showViewModal = false;
    public ?Contract $viewContract = null;

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'contract_type_id' => 'required|exists:contract_types,id',
            'employee_id' => 'required|exists:employees,id',
            'job_id' => 'required|exists:employees_jobs,id',
            'contract_start_date' => 'required|date',
            'contract_end_date' => 'required|date|after_or_equal:contract_start_date',
            'fixed_work_hours' => 'nullable|numeric|min:0',
            'additional_work_hours' => 'nullable|numeric|min:0',
            'monthly_holidays' => 'nullable|numeric|min:0',
            'monthly_sick_days' => 'nullable|numeric|min:0',
            'information' => 'nullable|string',
            'job_description' => 'nullable|string',
            'contractPoints.*.name' => 'required|string|max:255',
            'contractPoints.*.information' => 'nullable|string',
            'salaryPoints.*.name' => 'required|string|max:255',
            'salaryPoints.*.information' => 'nullable|string',
        ];
    }

    public function addContractPointInput()
    {
        $this->contractPoints[] = ['name' => '', 'information' => '', 'sequence' => count($this->contractPoints) + 1];
    }

    public function removeContractPointInput($index)
    {
        unset($this->contractPoints[$index]);
        $this->contractPoints = array_values($this->contractPoints);
    }

    public function addSalaryPointInput()
    {
        $this->salaryPoints[] = ['name' => '', 'information' => '', 'sequence' => count($this->salaryPoints) + 1];
    }

    public function removeSalaryPointInput($index)
    {
        unset($this->salaryPoints[$index]);
        $this->salaryPoints = array_values($this->salaryPoints);
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    #[Computed]
    public function contractTypes()
    {
        return ContractType::orderBy('name')->get();
    }

    #[Computed]
    public function jobs()
    {
        return EmployeesJob::orderBy('title')->get();
    }

    #[Computed]
    public function employees()
    {
        return Employee::orderBy('name')->get();
    }

    public function with(): array
    {
        $contracts = Contract::with(['employee', 'contract_type', 'contract_points', 'salary_points'])
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                      ->orWhereHas('employee', function ($q) {
                          $q->where('name', 'like', '%' . $this->search . '%');
                      });
            })
            ->orderByDesc('id')
            ->paginate(15);

        return [
            'contracts' => $contracts,
        ];
    }

    public function create(): void
    {
        $this->resetValidation();
        $this->resetExcept(['search']);
        $this->isEdit = false;
        $this->contractPoints = [];
        $this->salaryPoints = [];
        $this->addContractPointInput();
        $this->addSalaryPointInput();
        $this->showModal = true;
        $this->dispatch('showModal');
    }

    public function edit(int $id): void
    {
        $this->resetValidation();
        $contract = Contract::with('contract_points', 'salary_points')->findOrFail($id);
        $this->contractId = $id;
        $this->isEdit = true;
        $this->name = $contract->name;
        $this->contract_type_id = $contract->contract_type_id;
        $this->employee_id = $contract->employee_id;
        $this->job_id = $contract->job_id;
        $this->contract_start_date = $contract->contract_start_date;
        $this->contract_end_date = $contract->contract_end_date;
        $this->fixed_work_hours = $contract->fixed_work_hours !== null ? (float) $contract->fixed_work_hours : null;
        $this->additional_work_hours = $contract->additional_work_hours !== null ? (float) $contract->additional_work_hours : null;
        $this->monthly_holidays = $contract->monthly_holidays !== null ? (float) $contract->monthly_holidays : null;
        $this->monthly_sick_days = $contract->monthly_sick_days !== null ? (float) $contract->monthly_sick_days : null;
        $this->information = $contract->information ?? '';
        $this->job_description = $contract->job_description ?? '';

        $this->contractPoints = $contract->contract_points->map(fn($p) => ['name' => $p->name, 'information' => $p->information ?? '', 'sequence' => $p->sequence])->toArray();
        $this->salaryPoints = $contract->salary_points->map(fn($p) => ['name' => $p->name, 'information' => $p->information ?? '', 'sequence' => $p->sequence])->toArray();

        if (empty($this->contractPoints)) {
            $this->addContractPointInput();
        }
        if (empty($this->salaryPoints)) {
            $this->addSalaryPointInput();
        }
        $this->showModal = true;
        $this->dispatch('showModal');
    }

    public function save(): void
    {
        $validatedData = $this->validate();

        $contractData = collect($validatedData)
            ->except(['contractPoints', 'salaryPoints'])
            ->toArray();

        if ($this->isEdit) {
            $contract = Contract::findOrFail($this->contractId);
            $contract->update($contractData);
            session()->flash('success', __('recruitment.contract_updated_successfully'));
        } else {
            $contractData['created_by'] = auth()->id();
            
            // تعيين branch_id تلقائياً إذا لم يكن محدداً
            if (empty($contractData['branch_id'])) {
                $contractData['branch_id'] = optional(Auth::user())
                    ->branches()
                    ->where('branches.is_active', 1)
                    ->value('branches.id');
            }
            
            $contract = Contract::create($contractData);
            session()->flash('success', __('recruitment.contract_created_successfully'));
        }

        $contract->contract_points()->delete();
        foreach ($this->contractPoints as $index => $point) {
            if (!empty($point['name'])) {
                $contract->contract_points()->create([
                    'name' => $point['name'],
                    'information' => $point['information'] ?? '',
                    'sequence' => $point['sequence'] ?? ($index + 1),
                ]);
            }
        }

        $contract->salary_points()->delete();
        foreach ($this->salaryPoints as $index => $point) {
            if (!empty($point['name'])) {
                $contract->salary_points()->create([
                    'name' => $point['name'],
                    'information' => $point['information'] ?? '',
                    'sequence' => $point['sequence'] ?? ($index + 1),
                ]);
            }
        }

        $this->showModal = false;
        $this->reset(['name', 'contract_type_id', 'contract_start_date', 'contract_end_date', 'fixed_work_hours', 'additional_work_hours', 'monthly_holidays', 'monthly_sick_days', 'information', 'job_id', 'job_description', 'employee_id', 'contractPoints', 'salaryPoints', 'contractId', 'isEdit']);
        $this->dispatch('closeModal');
    }

    public function delete(int $id): void
    {
        $contract = Contract::findOrFail($id);
        $contract->contract_points()->delete();
        $contract->salary_points()->delete();
        $contract->delete();

        session()->flash('success', __('recruitment.contract_deleted_successfully'));
    }

    public function view(int $id): void
    {
        $this->viewContract = Contract::with(['contract_points', 'salary_points', 'employee', 'contract_type', 'job'])->findOrFail($id);
        $this->showViewModal = true;
        $this->dispatch('showViewModal');
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->reset(['name', 'contract_type_id', 'contract_start_date', 'contract_end_date', 'fixed_work_hours', 'additional_work_hours', 'monthly_holidays', 'monthly_sick_days', 'information', 'job_id', 'job_description', 'employee_id', 'contractPoints', 'salaryPoints', 'contractId', 'isEdit']);
        $this->resetValidation();
        $this->dispatch('closeModal');
    }

    public function closeViewModal(): void
    {
        $this->showViewModal = false;
        $this->viewContract = null;
        $this->dispatch('closeViewModal');
    }
}; ?>

<div style="font-family: 'Cairo', sans-serif; direction: rtl;">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-3 mt-3">
            @can('create Contracts')
                <button class="btn btn-main font-hold fw-bold font-14" wire:click="create">
                    <i class="las la-plus font-14 me-2"></i>{{ __('hr.add_contract') }}
                </button>
            @endcan
            <input type="text" 
                   class="form-control w-25 font-hold" 
                   placeholder="{{ __('hr.search_by_title') }}"
                   wire:model.live.debounce.300ms="search">
        </div>

        @if (session()->has('success'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
                class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"
                    x-on:click="show = false"></button>
            </div>
        @endif
        
        <div class="card">
            <div class="card-body">
                <div class="table-responsive" style="overflow-x: auto;">
                    <table id="contracts-table" class="table table-striped mb-0" style="min-width: 1200px;">
                        <thead class="table-light text-center align-middle">
                            <tr>
                                <th class="font-hold fw-bold font-14">#</th>
                                <th class="font-hold fw-bold font-14">{{ __('Contract Name') }}</th>
                                <th class="font-hold fw-bold font-14">{{ __('Employee') }}</th>
                                <th class="font-hold fw-bold font-14">{{ __('Contract Type') }}</th>
                                <th class="font-hold fw-bold font-14">{{ __('Start Date') }}</th>
                                <th class="font-hold fw-bold font-14">{{ __('End Date') }}</th>
                                @canany(['edit Contracts', 'delete Contracts', 'view Contracts'])
                                    <th class="font-hold fw-bold font-14">{{ __('hr.actions') }}</th>
                                @endcanany
                            </tr>
                        </thead>
                        <tbody class="table-light text-center align-middle">
                            @forelse ($contracts as $contract)
                                <tr>
                                    <td class="font-hold fw-bold font-14">{{ $loop->iteration }}</td>
                                    <td class="font-hold fw-bold font-14">{{ $contract->name }}</td>
                                    <td class="font-hold fw-bold font-14">{{ $contract->employee?->name }}</td>
                                    <td class="font-hold fw-bold font-14">{{ $contract->contract_type?->name }}</td>
                                    <td class="font-hold fw-bold font-14">{{ $contract->contract_start_date }}</td>
                                    <td class="font-hold fw-bold font-14">{{ $contract->contract_end_date }}</td>
                                    @canany(['edit Contracts', 'delete Contracts', 'view Contracts'])
                                        <td class="font-hold fw-bold font-14">
                                            <div class="btn-group" role="group">
                                                @can('view Contracts')
                                                    <button type="button" 
                                                            class="btn btn-success btn-icon-square-sm font-hold fw-bold"
                                                            wire:click="view({{ $contract->id }})"
                                                            title="{{ __('hr.view') }}">
                                                        <i class="las la-eye font-18"></i>
                                                    </button>
                                                @endcan
                                                @can('edit Contracts')
                                                    <button type="button" 
                                                            class="btn btn-success btn-icon-square-sm font-hold fw-bold"
                                                            wire:click="edit({{ $contract->id }})"
                                                            title="{{ __('hr.edit') }}">
                                                        <i class="las la-edit font-18"></i>
                                                    </button>
                                                @endcan
                                                @can('delete Contracts')
                                                    <button type="button" 
                                                            class="btn btn-danger btn-icon-square-sm font-hold fw-bold"
                                                            wire:click="delete({{ $contract->id }})"
                                                            wire:confirm="{{ __('hr.confirm_delete_contract') }}"
                                                            title="{{ __('hr.delete') }}">
                                                        <i class="las la-trash font-18"></i>
                                                    </button>
                                                @endcan
                                            </div>
                                        </td>
                                    @endcanany
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ auth()->user()->canany(['edit Contracts', 'delete Contracts', 'view Contracts']) ? '7' : '6' }}" 
                                        class="text-center font-hold fw-bold py-4">
                                        <div class="alert alert-info mb-0">
                                            <i class="las la-info-circle me-2"></i>
                                            {{ __('hr.no_contracts_found') }}
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="mt-4">
            {{ $contracts->links('pagination::bootstrap-5') }}
        </div>
    </div>

    <!-- Contract Modal -->
    <div wire:ignore.self class="modal fade" id="contractModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-fullscreen" style="width: 100vw; max-width: 100vw; height: 100vh; margin: 0;">
            <form wire:submit.prevent="save">
                <div class="modal-content" style="height: 100vh; border-radius: 0; border: none;">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ $isEdit ? __('hr.edit_contract') : __('hr.add_contract') }}</h5>
                        <button type="button" class="btn-close" wire:click="closeModal"></button>
                    </div>
                    <div class="modal-body" style="overflow-y: auto; max-height: calc(100vh - 150px);">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">{{ __('Contract Name') }} <span class="text-danger">*</span></label>
                                <input wire:model.blur="name" type="text" class="form-control" required>
                                @error('name') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{ __('Employee') }} <span class="text-danger">*</span></label>
                                <select wire:model.blur="employee_id" class="form-select" required>
                                    <option value="">{{ __('Select Employee') }}</option>
                                    @foreach($this->employees as $employee)
                                        <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                                    @endforeach
                                </select>
                                @error('employee_id') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{ __('Contract Type') }} <span class="text-danger">*</span></label>
                                <select wire:model.blur="contract_type_id" class="form-select" required>
                                    <option value="">{{ __('Select Contract Type') }}</option>
                                    @foreach($this->contractTypes as $type)
                                        <option value="{{ $type->id }}">{{ $type->name }}</option>
                                    @endforeach
                                </select>
                                @error('contract_type_id') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{ __('Job') }} <span class="text-danger">*</span></label>
                                <select wire:model.blur="job_id" class="form-select" required>
                                    <option value="">{{ __('Select Job') }}</option>
                                    @foreach($this->jobs as $job)
                                        <option value="{{ $job->id }}">{{ $job->title }}</option>
                                    @endforeach
                                </select>
                                @error('job_id') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{ __('Start Date') }} <span class="text-danger">*</span></label>
                                <input wire:model.blur="contract_start_date" type="date" class="form-control" required>
                                @error('contract_start_date') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{ __('End Date') }} <span class="text-danger">*</span></label>
                                <input wire:model.blur="contract_end_date" type="date" class="form-control" required>
                                @error('contract_end_date') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">{{ __('Fixed Work Hours') }}</label>
                                <input wire:model.blur="fixed_work_hours" type="number" step="0.01" class="form-control" min="0">
                                @error('fixed_work_hours') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">{{ __('Additional Work Hours') }}</label>
                                <input wire:model.blur="additional_work_hours" type="number" step="0.01" class="form-control" min="0">
                                @error('additional_work_hours') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">{{ __('Monthly Holidays') }}</label>
                                <input wire:model.blur="monthly_holidays" type="number" step="0.01" class="form-control" min="0">
                                @error('monthly_holidays') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">{{ __('Monthly Sick Days') }}</label>
                                <input wire:model.blur="monthly_sick_days" type="number" step="0.01" class="form-control" min="0">
                                @error('monthly_sick_days') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">{{ __('Job Description') }}</label>
                                <textarea wire:model.blur="job_description" class="form-control" rows="2"></textarea>
                                @error('job_description') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">{{ __('Information') }}</label>
                                <textarea wire:model.blur="information" class="form-control" rows="3"></textarea>
                                @error('information') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            
                            <!-- Contract Points -->
                            <div class="col-md-12">
                                <label class="form-label">{{ __('Contract Points') }}</label>
                                @foreach($contractPoints as $index => $point)
                                    <div class="d-flex gap-2 mb-2">
                                        <input wire:model="contractPoints.{{ $index }}.name" type="text" class="form-control" placeholder="{{ __('Point Name') }}">
                                        <input wire:model="contractPoints.{{ $index }}.information" type="text" class="form-control" placeholder="{{ __('Information') }}">
                                        <button type="button" wire:click="removeContractPointInput({{ $index }})" class="btn btn-danger btn-sm">
                                            <i class="las la-trash"></i>
                                        </button>
                                    </div>
                                @endforeach
                                <button type="button" wire:click="addContractPointInput" class="btn btn-sm btn-outline-primary">
                                    <i class="las la-plus"></i> {{ __('Add Point') }}
                                </button>
                            </div>

                            <!-- Salary Points -->
                            <div class="col-md-12">
                                <label class="form-label">{{ __('Salary Points') }}</label>
                                @foreach($salaryPoints as $index => $point)
                                    <div class="d-flex gap-2 mb-2">
                                        <input wire:model="salaryPoints.{{ $index }}.name" type="text" class="form-control" placeholder="{{ __('Point Name') }}">
                                        <input wire:model="salaryPoints.{{ $index }}.information" type="text" class="form-control" placeholder="{{ __('Information') }}">
                                        <button type="button" wire:click="removeSalaryPointInput({{ $index }})" class="btn btn-danger btn-sm">
                                            <i class="las la-trash"></i>
                                        </button>
                                    </div>
                                @endforeach
                                <button type="button" wire:click="addSalaryPointInput" class="btn btn-sm btn-outline-primary">
                                    <i class="las la-plus"></i> {{ __('Add Point') }}
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeModal">{{ __('hr.cancel') }}</button>
                        <button type="submit" class="btn btn-main">{{ $isEdit ? __('hr.update') : __('hr.save') }}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- View Modal -->
    <div wire:ignore.self class="modal fade" id="viewContractModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-fullscreen" style="width: 100vw; max-width: 100vw; height: 100vh; margin: 0;">
            <div class="modal-content" style="height: 100vh; border-radius: 0; border: none;">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('View Contract') }}</h5>
                    <button type="button" class="btn-close" wire:click="closeViewModal"></button>
                </div>
                <div class="modal-body" style="overflow-y: auto; max-height: calc(100vh - 150px);">
                    @if($viewContract)
                    <div class="row">
                        <div class="col-md-6"><strong>{{ __('Contract Name') }}:</strong> {{ $viewContract->name }}</div>
                        <div class="col-md-6"><strong>{{ __('Employee') }}:</strong> {{ $viewContract->employee?->name }}</div>
                        <div class="col-md-6"><strong>{{ __('Contract Type') }}:</strong> {{ $viewContract->contract_type?->name }}</div>
                        <div class="col-md-6"><strong>{{ __('Start Date') }}:</strong> {{ $viewContract->contract_start_date }}</div>
                        <div class="col-md-6"><strong>{{ __('End Date') }}:</strong> {{ $viewContract->contract_end_date }}</div>
                        @if($viewContract->contract_points->count() > 0)
                            <div class="col-md-12 mt-3">
                                <strong>{{ __('Contract Points') }}:</strong>
                                <ul>
                                    @foreach($viewContract->contract_points as $point)
                                        <li>{{ $point->name }}: {{ $point->information }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        @if($viewContract->salary_points->count() > 0)
                            <div class="col-md-12 mt-3">
                                <strong>{{ __('Salary Points') }}:</strong>
                                <ul>
                                    @foreach($viewContract->salary_points as $point)
                                        <li>{{ $point->name }}: {{ $point->information }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
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
    /* Fullscreen Modal Fix for Contracts */
    #contractModal.modal.show,
    #viewContractModal.modal.show {
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

    #contractModal .modal-fullscreen,
    #viewContractModal .modal-fullscreen {
        width: 100vw !important;
        max-width: 100vw !important;
        height: 100vh !important;
        margin: 0 !important;
        padding: 0 !important;
    }

    #contractModal .modal-fullscreen .modal-content,
    #viewContractModal .modal-fullscreen .modal-content {
        width: 100vw !important;
        max-width: 100vw !important;
        height: 100vh !important;
        border-radius: 0 !important;
        border: none !important;
        margin: 0 !important;
    }

    #contractModal .modal-fullscreen .modal-body,
    #viewContractModal .modal-fullscreen .modal-body {
        overflow-y: auto;
        max-height: calc(100vh - 150px);
    }
</style>
@endpush

@script
<script>
    document.addEventListener('livewire:initialized', () => {
        let modalInstance = null;
        const modalElement = document.getElementById('contractModal');
        
        if (modalElement) {
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
        }

        let viewModalInstance = null;
        const viewModalElement = document.getElementById('viewContractModal');
        
        if (viewModalElement) {
            // إنشاء instance واحدة فقط
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
                // لا نحذف الـ instance، فقط نتركه للاستخدام مرة أخرى
            });
        }
    });
</script>
@endscript

