<?php

declare(strict_types=1);

use Livewire\Volt\Component;
use Modules\Recruitment\Models\Interview;
use Modules\Recruitment\Models\Cv;
use Modules\Recruitment\Models\JobPosting;
use App\Models\User;
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
    public string $filter_type = '';
    public string $filter_result = '';
    public bool $showModal = false;
    public bool $isEdit = false;
    public ?int $interviewId = null;
    public bool $showViewModal = false;
    public ?Interview $viewInterview = null;

    #[Rule('required|exists:cvs,id')]
    public ?int $cv_id = null;

    #[Rule('nullable|exists:job_postings,id')]
    public ?int $job_posting_id = null;

    #[Rule('required|in:phone,video,in_person,panel')]
    public string $interview_type = 'in_person';

    #[Rule('required|in:scheduled,completed,cancelled,rescheduled')]
    public string $status = 'scheduled';

    #[Rule('required|date')]
    public ?string $scheduled_at = null;

    #[Rule('nullable|integer|min:15|max:480')]
    public ?int $duration = 60;

    #[Rule('nullable|string|max:255')]
    public ?string $location = null;

    #[Rule('nullable|string')]
    public ?string $notes = null;

    #[Rule('nullable|exists:users,id')]
    public ?int $interviewer_id = null;

    #[Rule('nullable|in:pending,accepted,rejected,on_hold')]
    public ?string $result = null;

    #[Rule('nullable|string')]
    public ?string $feedback = null;

    protected array $queryString = ['search', 'filter_status', 'filter_type', 'filter_result'];

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

    public function updatingFilterType(): void
    {
        $this->resetPage();
    }

    public function updatingFilterResult(): void
    {
        $this->resetPage();
    }

    public function resetForm(): void
    {
        $this->reset([
            'cv_id', 'job_posting_id', 'interview_type', 'status', 'scheduled_at',
            'duration', 'location', 'notes', 'interviewer_id', 'result', 'feedback',
            'interviewId', 'isEdit', 'filter_result'
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
        $interview = Interview::findOrFail($id);
        $this->interviewId = $interview->id;
        $this->cv_id = $interview->cv_id;
        $this->job_posting_id = $interview->job_posting_id;
        $this->interview_type = $interview->interview_type;
        $this->status = $interview->status;
        $this->scheduled_at = $interview->scheduled_at?->format('Y-m-d\TH:i');
        $this->duration = $interview->duration;
        $this->location = $interview->location;
        $this->notes = $interview->notes;
        $this->interviewer_id = $interview->interviewer_id;
        $this->result = $interview->result;
        $this->feedback = $interview->feedback;
        $this->isEdit = true;
        $this->showModal = true;
        $this->dispatch('showModal');
    }

    public function save(): void
    {
        $validated = $this->validate();

        if ($this->isEdit) {
            $interview = Interview::findOrFail($this->interviewId);
            $interview->update($validated);
            session()->flash('message', __('recruitment.interview_updated_successfully'));
        } else {
            $validated['created_by'] = auth()->id();
            
            // تعيين branch_id تلقائياً إذا لم يكن محدداً
            if (empty($validated['branch_id'])) {
                $validated['branch_id'] = optional(Auth::user())
                    ->branches()
                    ->where('branches.is_active', 1)
                    ->value('branches.id');
            }
            
            Interview::create($validated);
            session()->flash('message', __('recruitment.interview_created_successfully'));
        }

        $this->resetForm();
        $this->showModal = false;
        $this->dispatch('closeModal', 'interviewModal');
    }

    public function delete(int $id): void
    {
        $interview = Interview::findOrFail($id);
        $interview->delete();
        session()->flash('message', __('recruitment.interview_deleted_successfully'));
    }

    public function closeModal(): void
    {
        $this->resetForm();
        $this->showModal = false;
        $this->dispatch('closeModal');
    }

    public function view(int $id): void
    {
        $this->viewInterview = Interview::with(['cv', 'jobPosting', 'interviewer', 'createdBy'])->findOrFail($id);
        $this->showViewModal = true;
        $this->dispatch('showViewModal');
    }

    public function closeViewModal(): void
    {
        $this->showViewModal = false;
        $this->viewInterview = null;
        $this->dispatch('closeViewModal');
    }

    #[Computed]
    public function interviews(): LengthAwarePaginator
    {
        return Interview::with(['cv', 'jobPosting', 'interviewer', 'createdBy'])
            ->when($this->search, function ($query) {
                $query->whereHas('cv', function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->filter_status, function ($query) {
                $query->where('status', $this->filter_status);
            })
            ->when($this->filter_type, function ($query) {
                $query->where('interview_type', $this->filter_type);
            })
            ->when($this->filter_result, function ($query) {
                $query->where('result', $this->filter_result);
            })
            ->latest('scheduled_at')
            ->paginate(10);
    }

    #[Computed]
    public function cvs(): \Illuminate\Database\Eloquent\Collection
    {
        return Cv::orderBy('name')->get();
    }

    #[Computed]
    public function jobPostings(): \Illuminate\Database\Eloquent\Collection
    {
        return JobPosting::where('status', 'active')->orderBy('title')->get();
    }

    #[Computed]
    public function interviewers(): \Illuminate\Database\Eloquent\Collection
    {
        return User::orderBy('name')->get();
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
                    <i class="mdi mdi-account-group text-primary" style="font-size: 2.5rem;"></i>
                </div>
                <div>
                    <h4 class="mb-1">{{ __('recruitment.interviews') }}</h4>
                    <p class="text-muted mb-0">{{ __('recruitment.manage_interviews') }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-6 text-end">
            @can('create Interviews')
                <button wire:click="create" 
                        class="btn btn-main btn-lg shadow-sm"
                        wire:loading.attr="disabled"
                        wire:target="create">
                    <span wire:loading.remove wire:target="create">
                        <i class="mdi mdi-plus me-2"></i> {{ __('recruitment.create_interview') }}
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
                <div class="col-md-3">
                    <div class="position-relative">
                        <i class="mdi mdi-magnify position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                        <input wire:model.live.debounce.300ms="search" type="text" class="form-control ps-5" placeholder="{{ __('recruitment.search_interviews') }}">
                    </div>
                </div>
                <div class="col-md-2">
                    <select wire:model.live="filter_status" class="form-select">
                        <option value="">{{ __('recruitment.all_status') }}</option>
                        <option value="scheduled">{{ __('recruitment.scheduled') }}</option>
                        <option value="completed">{{ __('recruitment.completed') }}</option>
                        <option value="cancelled">{{ __('recruitment.cancelled') }}</option>
                        <option value="rescheduled">{{ __('recruitment.rescheduled') }}</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select wire:model.live="filter_type" class="form-select">
                        <option value="">{{ __('recruitment.all_types') }}</option>
                        <option value="phone">{{ __('recruitment.phone') }}</option>
                        <option value="video">{{ __('recruitment.video') }}</option>
                        <option value="in_person">{{ __('recruitment.in_person') }}</option>
                        <option value="panel">{{ __('recruitment.panel') }}</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select wire:model.live="filter_result" class="form-select">
                        <option value="">{{ __('recruitment.all_results') }}</option>
                        <option value="pending">{{ __('recruitment.pending') }}</option>
                        <option value="accepted">{{ __('recruitment.accepted') }}</option>
                        <option value="rejected">{{ __('recruitment.rejected') }}</option>
                        <option value="on_hold">{{ __('recruitment.on_hold') }}</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Interviews Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-0 py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h6 class="mb-0">
                    <i class="mdi mdi-format-list-bulleted me-2"></i>{{ __('recruitment.interviews_list') }}
                    <span class="badge bg-primary ms-2">{{ $this->interviews->total() }}</span>
                </h6>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="border-0 fw-semibold">{{ __('recruitment.candidate') }}</th>
                            <th class="border-0 fw-semibold">{{ __('recruitment.job_posting') }}</th>
                            <th class="border-0 fw-semibold">{{ __('recruitment.interview_type') }}</th>
                            <th class="border-0 fw-semibold">{{ __('recruitment.scheduled_at') }}</th>
                            <th class="border-0 fw-semibold">{{ __('recruitment.status') }}</th>
                            <th class="border-0 fw-semibold">{{ __('recruitment.result') }}</th>
                            <th class="border-0 fw-semibold">{{ __('recruitment.interviewer') }}</th>
                            @canany(['view Interviews', 'edit Interviews', 'delete Interviews'])
                                <th class="border-0 fw-semibold text-center">{{ __('hr.actions') }}</th>
                            @endcanany
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($this->interviews as $interview)
                            <tr>
                                <td>{{ $interview->cv?->name ?? __('recruitment.no_candidate') }}</td>
                                <td>{{ $interview->jobPosting?->title ?? __('recruitment.no_job_posting') }}</td>
                                <td>
                                    <span class="badge bg-info">{{ __('recruitment.' . $interview->interview_type) }}</span>
                                </td>
                                <td>{{ $interview->scheduled_at?->format('Y-m-d H:i') ?? __('recruitment.not_scheduled') }}</td>
                                <td>
                                    <span class="badge bg-{{ $interview->status === 'completed' ? 'success' : ($interview->status === 'cancelled' ? 'danger' : 'warning') }}">
                                        {{ __('recruitment.' . $interview->status) }}
                                    </span>
                                </td>
                                <td>
                                    @if($interview->result)
                                        <span class="badge bg-{{ $interview->result === 'accepted' ? 'success' : ($interview->result === 'rejected' ? 'danger' : ($interview->result === 'on_hold' ? 'warning' : 'secondary')) }}">
                                            {{ __('recruitment.' . $interview->result) }}
                                        </span>
                                    @else
                                        <span class="text-muted">{{ __('recruitment.pending') }}</span>
                                    @endif
                                </td>
                                <td>{{ $interview->interviewer?->name ?? __('recruitment.no_interviewer') }}</td>
                                @canany(['view Interviews', 'edit Interviews', 'delete Interviews'])
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-1">
                                            @can('view Interviews')
                                                <button wire:click="view({{ $interview->id }})" 
                                                        class="btn btn-sm btn-outline-info" 
                                                        title="{{ __('hr.view') }}"
                                                        wire:loading.attr="disabled"
                                                        wire:target="view({{ $interview->id }})">
                                                    <span wire:loading.remove wire:target="view({{ $interview->id }})">
                                                        <i class="mdi mdi-eye"></i>
                                                    </span>
                                                    <span wire:loading wire:target="view({{ $interview->id }})">
                                                        <i class="mdi mdi-loading mdi-spin"></i>
                                                    </span>
                                                </button>
                                            @endcan
                                            @can('edit Interviews')
                                                <button wire:click="edit({{ $interview->id }})" 
                                                        class="btn btn-sm btn-outline-warning" 
                                                        title="{{ __('hr.edit') }}"
                                                        wire:loading.attr="disabled"
                                                        wire:target="edit({{ $interview->id }})">
                                                    <span wire:loading.remove wire:target="edit({{ $interview->id }})">
                                                        <i class="mdi mdi-pencil"></i>
                                                    </span>
                                                    <span wire:loading wire:target="edit({{ $interview->id }})">
                                                        <i class="mdi mdi-loading mdi-spin"></i>
                                                    </span>
                                                </button>
                                            @endcan
                                            @can('delete Interviews')
                                                <button wire:click="delete({{ $interview->id }})" 
                                                        wire:confirm="{{ __('recruitment.confirm_delete_interview') }}"
                                                        class="btn btn-sm btn-outline-danger" 
                                                        title="{{ __('hr.delete') }}"
                                                        wire:loading.attr="disabled"
                                                        wire:target="delete({{ $interview->id }})">
                                                    <span wire:loading.remove wire:target="delete({{ $interview->id }})">
                                                        <i class="mdi mdi-delete"></i>
                                                    </span>
                                                    <span wire:loading wire:target="delete({{ $interview->id }})">
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
                                <td colspan="{{ auth()->user()->canany(['view Interviews', 'edit Interviews', 'delete Interviews']) ? 8 : 7 }}" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="mdi mdi-account-group-outline text-muted" style="font-size: 4rem;"></i>
                                        <h5 class="mt-3 mb-2">{{ __('recruitment.no_interviews_found') }}</h5>
                                        <p class="mb-3">{{ __('recruitment.start_by_adding_first_interview') }}</p>
                                        @can('create Interviews')
                                            <button wire:click="create" 
                                                    class="btn btn-main"
                                                    wire:loading.attr="disabled"
                                                    wire:target="create">
                                                <span wire:loading.remove wire:target="create">
                                                    <i class="mdi mdi-plus me-2"></i> {{ __('recruitment.add_first_interview') }}
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
        @if($this->interviews->hasPages())
            <div class="card-footer bg-white border-0">
                {{ $this->interviews->links() }}
            </div>
        @endif
    </div>

    <!-- Modal -->
    <div wire:ignore.self class="modal fade" id="interviewModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-fullscreen" style="width: 100vw; max-width: 100vw; height: 100vh; margin: 0;">
            <form wire:submit.prevent="save">
                <div class="modal-content" style="height: 100vh; border-radius: 0; border: none;">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ $isEdit ? __('recruitment.edit_interview') : __('recruitment.create_interview') }}</h5>
                        <button type="button" class="btn-close" wire:click="closeModal"></button>
                    </div>
                    <div class="modal-body" style="overflow-y: auto; max-height: calc(100vh - 150px);">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">{{ __('recruitment.candidate') }} <span class="text-danger">*</span></label>
                                <select wire:model.blur="cv_id" class="form-select" required>
                                    <option value="">{{ __('recruitment.select_candidate') }}</option>
                                    @foreach($this->cvs as $cv)
                                        <option value="{{ $cv->id }}">{{ $cv->name }}</option>
                                    @endforeach
                                </select>
                                @error('cv_id') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{ __('recruitment.job_posting') }}</label>
                                <select wire:model.blur="job_posting_id" class="form-select">
                                    <option value="">{{ __('recruitment.select_job_posting') }}</option>
                                    @foreach($this->jobPostings as $jobPosting)
                                        <option value="{{ $jobPosting->id }}">{{ $jobPosting->title }}</option>
                                    @endforeach
                                </select>
                                @error('job_posting_id') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{ __('recruitment.interview_type') }} <span class="text-danger">*</span></label>
                                <select wire:model.blur="interview_type" class="form-select" required>
                                    <option value="phone">{{ __('recruitment.phone') }}</option>
                                    <option value="video">{{ __('recruitment.video') }}</option>
                                    <option value="in_person">{{ __('recruitment.in_person') }}</option>
                                    <option value="panel">{{ __('recruitment.panel') }}</option>
                                </select>
                                @error('interview_type') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{ __('recruitment.status') }} <span class="text-danger">*</span></label>
                                <select wire:model.blur="status" class="form-select" required>
                                    <option value="scheduled">{{ __('recruitment.scheduled') }}</option>
                                    <option value="completed">{{ __('recruitment.completed') }}</option>
                                    <option value="cancelled">{{ __('recruitment.cancelled') }}</option>
                                    <option value="rescheduled">{{ __('recruitment.rescheduled') }}</option>
                                </select>
                                @error('status') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{ __('recruitment.scheduled_at') }} <span class="text-danger">*</span></label>
                                <input wire:model.blur="scheduled_at" type="datetime-local" class="form-control" required>
                                @error('scheduled_at') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{ __('recruitment.duration') }} ({{ __('recruitment.minutes') }})</label>
                                <input wire:model.blur="duration" type="number" class="form-control" min="15" max="480" value="60">
                                @error('duration') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{ __('recruitment.location') }}</label>
                                <input wire:model.blur="location" type="text" class="form-control">
                                @error('location') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{ __('recruitment.interviewer') }}</label>
                                <select wire:model.blur="interviewer_id" class="form-select">
                                    <option value="">{{ __('recruitment.select_interviewer') }}</option>
                                    @foreach($this->interviewers as $interviewer)
                                        <option value="{{ $interviewer->id }}">{{ $interviewer->name }}</option>
                                    @endforeach
                                </select>
                                @error('interviewer_id') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{ __('recruitment.result') }}</label>
                                <select wire:model.blur="result" class="form-select">
                                    <option value="">{{ __('recruitment.select_result') }}</option>
                                    <option value="pending">{{ __('recruitment.pending') }}</option>
                                    <option value="accepted">{{ __('recruitment.accepted') }}</option>
                                    <option value="rejected">{{ __('recruitment.rejected') }}</option>
                                    <option value="on_hold">{{ __('recruitment.on_hold') }}</option>
                                </select>
                                @error('result') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">{{ __('recruitment.notes') }}</label>
                                <textarea wire:model.blur="notes" class="form-control" rows="2"></textarea>
                                @error('notes') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">{{ __('recruitment.feedback') }}</label>
                                <textarea wire:model.blur="feedback" class="form-control" rows="3"></textarea>
                                @error('feedback') <span class="text-danger">{{ $message }}</span> @enderror
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
    <div wire:ignore.self class="modal fade" id="viewInterviewModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-fullscreen" style="width: 100vw; max-width: 100vw; height: 100vh; margin: 0;">
            <div class="modal-content" style="height: 100vh; border-radius: 0; border: none;">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('recruitment.interview_details') }}</h5>
                    <button type="button" class="btn-close" wire:click="closeViewModal"></button>
                </div>
                <div class="modal-body" style="overflow-y: auto; max-height: calc(100vh - 150px);">
                    @if($viewInterview)
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">{{ __('recruitment.candidate') }}:</label>
                            <p class="mb-0">{{ $viewInterview->cv?->name ?? __('recruitment.no_candidate') }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">{{ __('recruitment.job_posting') }}:</label>
                            <p class="mb-0">{{ $viewInterview->jobPosting?->title ?? __('recruitment.no_job_posting') }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">{{ __('recruitment.interview_type') }}:</label>
                            <p class="mb-0">
                                <span class="badge bg-info">{{ __('recruitment.' . $viewInterview->interview_type) }}</span>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">{{ __('recruitment.status') }}:</label>
                            <p class="mb-0">
                                <span class="badge bg-{{ $viewInterview->status === 'completed' ? 'success' : ($viewInterview->status === 'cancelled' ? 'danger' : 'warning') }}">
                                    {{ __('recruitment.' . $viewInterview->status) }}
                                </span>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">{{ __('recruitment.scheduled_at') }}:</label>
                            <p class="mb-0">{{ $viewInterview->scheduled_at?->format('Y-m-d H:i') ?? __('recruitment.not_scheduled') }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">{{ __('recruitment.duration') }}:</label>
                            <p class="mb-0">{{ $viewInterview->duration ? $viewInterview->duration . ' ' . __('recruitment.minutes') : '-' }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">{{ __('recruitment.location') }}:</label>
                            <p class="mb-0">{{ $viewInterview->location ?? '-' }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">{{ __('recruitment.interviewer') }}:</label>
                            <p class="mb-0">{{ $viewInterview->interviewer?->name ?? __('recruitment.no_interviewer') }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">{{ __('recruitment.result') }}:</label>
                            <p class="mb-0">
                                @if($viewInterview->result)
                                    <span class="badge bg-{{ $viewInterview->result === 'accepted' ? 'success' : ($viewInterview->result === 'rejected' ? 'danger' : ($viewInterview->result === 'on_hold' ? 'warning' : 'secondary')) }}">
                                        {{ __('recruitment.' . $viewInterview->result) }}
                                    </span>
                                @else
                                    <span class="text-muted">{{ __('recruitment.pending') }}</span>
                                @endif
                            </p>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-bold">{{ __('recruitment.notes') }}:</label>
                            <p class="mb-0">{{ $viewInterview->notes ?? '-' }}</p>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-bold">{{ __('recruitment.feedback') }}:</label>
                            <p class="mb-0">{{ $viewInterview->feedback ?? '-' }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">{{ __('recruitment.created_by') }}:</label>
                            <p class="mb-0">{{ $viewInterview->createdBy?->name ?? '-' }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">{{ __('recruitment.created_at') }}:</label>
                            <p class="mb-0">{{ $viewInterview->created_at?->format('Y-m-d H:i') ?? '-' }}</p>
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
    /* Fullscreen Modal Fix for Interviews */
    #interviewModal.modal.show,
    #viewInterviewModal.modal.show {
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

    #interviewModal .modal-fullscreen,
    #viewInterviewModal .modal-fullscreen {
        width: 100vw !important;
        max-width: 100vw !important;
        height: 100vh !important;
        margin: 0 !important;
        padding: 0 !important;
    }

    #interviewModal .modal-fullscreen .modal-content,
    #viewInterviewModal .modal-fullscreen .modal-content {
        width: 100vw !important;
        max-width: 100vw !important;
        height: 100vh !important;
        border-radius: 0 !important;
        border: none !important;
        margin: 0 !important;
    }

    #interviewModal .modal-fullscreen .modal-body,
    #viewInterviewModal .modal-fullscreen .modal-body {
        overflow-y: auto;
        max-height: calc(100vh - 150px);
    }
</style>
@endpush

@script
<script>
    document.addEventListener('livewire:initialized', () => {
        let modalInstance = null;
        const modalElement = document.getElementById('interviewModal');

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

        // View Modal
        let viewModalInstance = null;
        const viewModalElement = document.getElementById('viewInterviewModal');
        
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
