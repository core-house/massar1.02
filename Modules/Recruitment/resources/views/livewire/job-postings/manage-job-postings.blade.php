<?php

declare(strict_types=1);

use Livewire\Volt\Component;
use Modules\Recruitment\Models\JobPosting;
use Modules\HR\Models\EmployeesJob;
use Livewire\WithPagination;
use Livewire\Attributes\Rule;
use Livewire\Attributes\Computed;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

new class extends Component {
    use WithPagination;
    protected string $paginationTheme = 'bootstrap';

    public string $search = '';
    public string $filter_status = '';
    public bool $showModal = false;
    public bool $isEdit = false;
    public ?int $jobPostingId = null;

    #[Rule('required|string|max:255')]
    public string $title = '';

    #[Rule('nullable|string')]
    public ?string $description = null;

    #[Rule('nullable|exists:employees_jobs,id')]
    public ?int $job_id = null;

    #[Rule('required|in:active,closed,expired')]
    public string $status = 'active';

    #[Rule('required|date')]
    public ?string $start_date = null;

    #[Rule('nullable|date|after:start_date')]
    public ?string $end_date = null;

    #[Rule('nullable|string')]
    public ?string $requirements = null;

    #[Rule('nullable|string')]
    public ?string $benefits = null;

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
            'title', 'description', 'job_id', 'status', 'start_date', 
            'end_date', 'requirements', 'benefits', 'jobPostingId', 'isEdit'
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
        $jobPosting = JobPosting::findOrFail($id);
        $this->jobPostingId = $jobPosting->id;
        $this->title = $jobPosting->title;
        $this->description = $jobPosting->description;
        $this->job_id = $jobPosting->job_id;
        $this->status = $jobPosting->status;
        $this->start_date = $jobPosting->start_date?->format('Y-m-d');
        $this->end_date = $jobPosting->end_date?->format('Y-m-d');
        $this->requirements = $jobPosting->requirements;
        $this->benefits = $jobPosting->benefits;
        $this->isEdit = true;
        $this->showModal = true;
        $this->dispatch('showModal');
    }

    public function save(): void
    {
        $validated = $this->validate();

        if ($this->isEdit) {
            $jobPosting = JobPosting::findOrFail($this->jobPostingId);
            $jobPosting->update($validated);
            session()->flash('message', __('recruitment.job_posting_updated_successfully'));
        } else {
            $validated['created_by'] = auth()->id();
            
            // تعيين branch_id تلقائياً إذا لم يكن محدداً
            if (empty($validated['branch_id'])) {
                $validated['branch_id'] = optional(Auth::user())
                    ->branches()
                    ->where('branches.is_active', 1)
                    ->value('branches.id');
            }
            
            JobPosting::create($validated);
            session()->flash('message', __('recruitment.job_posting_created_successfully'));
        }

        $this->resetForm();
        $this->showModal = false;
        $this->dispatch('closeModal');
    }

    public function delete(int $id): void
    {
        $jobPosting = JobPosting::findOrFail($id);
        $jobPosting->delete();
        session()->flash('message', __('recruitment.job_posting_deleted_successfully'));
    }

    public function closeModal(): void
    {
        $this->resetForm();
        $this->showModal = false;
        $this->dispatch('closeModal');
    }

    #[Computed]
    public function jobPostings(): LengthAwarePaginator
    {
        return JobPosting::with(['job', 'createdBy', 'cvs', 'interviews'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('title', 'like', '%' . $this->search . '%')
                      ->orWhere('description', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->filter_status, function ($query) {
                $query->where('status', $this->filter_status);
            })
            ->latest()
            ->paginate(10);
    }

    #[Computed]
    public function jobs(): \Illuminate\Database\Eloquent\Collection
    {
        return EmployeesJob::orderBy('title')->get();
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
                    <i class="mdi mdi-briefcase text-primary" style="font-size: 2.5rem;"></i>
                </div>
                <div>
                    <h4 class="mb-1">{{ __('recruitment.job_postings') }}</h4>
                    <p class="text-muted mb-0">{{ __('recruitment.manage_job_postings') }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-6 text-end">
            @can('create Job Postings')
                <button wire:click="create" 
                        class="btn btn-main btn-lg shadow-sm"
                        wire:loading.attr="disabled"
                        wire:target="create">
                    <span wire:loading.remove wire:target="create">
                        <i class="mdi mdi-plus me-2"></i> {{ __('recruitment.create_job_posting') }}
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
                        <input wire:model.live.debounce.300ms="search" type="text" class="form-control ps-5" placeholder="{{ __('recruitment.search_job_postings') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <select wire:model.live="filter_status" class="form-select">
                        <option value="">{{ __('recruitment.all_status') }}</option>
                        <option value="active">{{ __('recruitment.active') }}</option>
                        <option value="closed">{{ __('recruitment.closed') }}</option>
                        <option value="expired">{{ __('recruitment.expired') }}</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Job Postings Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-0 py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h6 class="mb-0">
                    <i class="mdi mdi-format-list-bulleted me-2"></i>{{ __('recruitment.job_postings_list') }}
                    <span class="badge bg-primary ms-2">{{ $this->jobPostings->total() }}</span>
                </h6>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="border-0 fw-semibold">{{ __('recruitment.job_title') }}</th>
                            <th class="border-0 fw-semibold">{{ __('recruitment.job') }}</th>
                            <th class="border-0 fw-semibold">{{ __('recruitment.status') }}</th>
                            <th class="border-0 fw-semibold">{{ __('recruitment.start_date') }}</th>
                            <th class="border-0 fw-semibold">{{ __('recruitment.end_date') }}</th>
                            <th class="border-0 fw-semibold">{{ __('recruitment.applicants') }}</th>
                            @canany(['edit Job Postings', 'delete Job Postings'])
                                <th class="border-0 fw-semibold text-center">{{ __('hr.actions') }}</th>
                            @endcanany
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($this->jobPostings as $jobPosting)
                            <tr>
                                <td>
                                    <h6 class="mb-0">{{ $jobPosting->title }}</h6>
                                    @if($jobPosting->description)
                                        <small class="text-muted">{{ Str::limit($jobPosting->description, 50) }}</small>
                                    @endif
                                </td>
                                <td>{{ $jobPosting->job?->title ?? __('recruitment.no_job_assigned') }}</td>
                                <td>
                                    <span class="badge bg-{{ $jobPosting->status === 'active' ? 'success' : ($jobPosting->status === 'closed' ? 'secondary' : 'danger') }}">
                                        {{ __('recruitment.' . $jobPosting->status) }}
                                    </span>
                                </td>
                                <td>{{ $jobPosting->start_date?->format('Y-m-d') }}</td>
                                <td>{{ $jobPosting->end_date?->format('Y-m-d') ?? __('recruitment.no_deadline') }}</td>
                                <td>
                                    <span class="badge bg-info">{{ $jobPosting->cvs_count ?? 0 }}</span>
                                </td>
                                @canany(['edit Job Postings', 'delete Job Postings'])
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-1">
                                            @can('edit Job Postings')
                                                <button wire:click="edit({{ $jobPosting->id }})" 
                                                        class="btn btn-sm btn-outline-warning" 
                                                        title="{{ __('hr.edit') }}"
                                                        wire:loading.attr="disabled"
                                                        wire:target="edit({{ $jobPosting->id }})">
                                                    <span wire:loading.remove wire:target="edit({{ $jobPosting->id }})">
                                                        <i class="mdi mdi-pencil"></i>
                                                    </span>
                                                    <span wire:loading wire:target="edit({{ $jobPosting->id }})">
                                                        <i class="mdi mdi-loading mdi-spin"></i>
                                                    </span>
                                                </button>
                                            @endcan
                                            @can('delete Job Postings')
                                                <button wire:click="delete({{ $jobPosting->id }})" 
                                                        wire:confirm="{{ __('recruitment.confirm_delete_job_posting') }}"
                                                        class="btn btn-sm btn-outline-danger" 
                                                        title="{{ __('hr.delete') }}"
                                                        wire:loading.attr="disabled"
                                                        wire:target="delete({{ $jobPosting->id }})">
                                                    <span wire:loading.remove wire:target="delete({{ $jobPosting->id }})">
                                                        <i class="mdi mdi-delete"></i>
                                                    </span>
                                                    <span wire:loading wire:target="delete({{ $jobPosting->id }})">
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
                                <td colspan="{{ auth()->user()->canany(['edit Job Postings', 'delete Job Postings']) ? 7 : 6 }}" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="mdi mdi-briefcase-outline text-muted" style="font-size: 4rem;"></i>
                                        <h5 class="mt-3 mb-2">{{ __('recruitment.no_job_postings_found') }}</h5>
                                        <p class="mb-3">{{ __('recruitment.start_by_adding_first_job_posting') }}</p>
                                        @can('create Job Postings')
                                            <button wire:click="create" 
                                                    class="btn btn-main"
                                                    wire:loading.attr="disabled"
                                                    wire:target="create">
                                                <span wire:loading.remove wire:target="create">
                                                    <i class="mdi mdi-plus me-2"></i> {{ __('recruitment.add_first_job_posting') }}
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
        @if($this->jobPostings->hasPages())
            <div class="card-footer bg-white border-0">
                {{ $this->jobPostings->links() }}
            </div>
        @endif
    </div>

    <!-- Modal -->
    <div wire:ignore.self class="modal fade" id="jobPostingModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-fullscreen" style="width: 100vw; max-width: 100vw; height: 100vh; margin: 0;">
            <form wire:submit.prevent="save">
                <div class="modal-content" style="height: 100vh; border-radius: 0; border: none;">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ $isEdit ? __('recruitment.edit_job_posting') : __('recruitment.create_job_posting') }}</h5>
                        <button type="button" class="btn-close" wire:click="closeModal"></button>
                    </div>
                    <div class="modal-body" style="overflow-y: auto; max-height: calc(100vh - 150px);">
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label">{{ __('recruitment.job_title') }} <span class="text-danger">*</span></label>
                                <input wire:model.defer="title" type="text" class="form-control" required>
                                @error('title') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{ __('recruitment.job') }}</label>
                                <select wire:model.defer="job_id" class="form-select">
                                    <option value="">{{ __('recruitment.select_job') }}</option>
                                    @foreach($this->jobs as $job)
                                        <option value="{{ $job->id }}">{{ $job->title }}</option>
                                    @endforeach
                                </select>
                                @error('job_id') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{ __('recruitment.status') }} <span class="text-danger">*</span></label>
                                <select wire:model.defer="status" class="form-select" required>
                                    <option value="active">{{ __('recruitment.active') }}</option>
                                    <option value="closed">{{ __('recruitment.closed') }}</option>
                                    <option value="expired">{{ __('recruitment.expired') }}</option>
                                </select>
                                @error('status') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{ __('recruitment.start_date') }} <span class="text-danger">*</span></label>
                                <input wire:model.defer="start_date" type="date" class="form-control" required>
                                @error('start_date') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{ __('recruitment.end_date') }}</label>
                                <input wire:model.defer="end_date" type="date" class="form-control">
                                @error('end_date') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">{{ __('recruitment.job_description') }}</label>
                                <textarea wire:model.defer="description" class="form-control" rows="3"></textarea>
                                @error('description') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">{{ __('recruitment.requirements') }}</label>
                                <textarea wire:model.defer="requirements" class="form-control" rows="3"></textarea>
                                @error('requirements') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">{{ __('recruitment.benefits') }}</label>
                                <textarea wire:model.defer="benefits" class="form-control" rows="3"></textarea>
                                @error('benefits') <span class="text-danger">{{ $message }}</span> @enderror
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

@push('styles')
<style>
    /* Fullscreen Modal Fix for Job Postings */
    #jobPostingModal.modal.show {
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

    #jobPostingModal .modal-fullscreen {
        width: 100vw !important;
        max-width: 100vw !important;
        height: 100vh !important;
        margin: 0 !important;
        padding: 0 !important;
    }

    #jobPostingModal .modal-fullscreen .modal-content {
        width: 100vw !important;
        max-width: 100vw !important;
        height: 100vh !important;
        border-radius: 0 !important;
        border: none !important;
        margin: 0 !important;
    }

    #jobPostingModal .modal-fullscreen .modal-body {
        overflow-y: auto;
        max-height: calc(100vh - 150px);
    }
</style>
@endpush

@script
<script>
    document.addEventListener('livewire:initialized', () => {
        let modalInstance = null;
        const modalElement = document.getElementById('jobPostingModal');

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
