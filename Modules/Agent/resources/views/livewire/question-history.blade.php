<?php

declare(strict_types=1);

use Livewire\Volt\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Computed;
use Modules\Agent\Models\AgentQuestion;

new class extends Component {
    use WithPagination;

    public string $search = '';
    public ?int $selectedQuestionId = null;

    /**
     * Get paginated questions for current user
     */
    #[Computed]
    public function questions()
    {
        return AgentQuestion::query()
            ->forUser(auth()->id())
            ->answered()
            ->when($this->search, fn($q) => $q->where('question_text', 'like', "%{$this->search}%"))
            ->recent()
            ->paginate(15);
    }

    /**
     * Get selected question details
     */
    #[Computed]
    public function selectedQuestion()
    {
        if (!$this->selectedQuestionId) {
            return null;
        }

        return AgentQuestion::query()
            ->forUser(auth()->id())
            ->find($this->selectedQuestionId);
    }

    /**
     * Select a question to view details
     */
    public function selectQuestion(int $questionId): void
    {
        $this->selectedQuestionId = $questionId;
    }

    /**
     * Clear question selection
     */
    public function clearSelection(): void
    {
        $this->selectedQuestionId = null;
    }

    /**
     * Reset pagination when search changes
     */
    public function updatedSearch(): void
    {
        $this->resetPage();
    }
}; ?>

<div>
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="las la-history me-2"></i>
                            {{ __('agent.history') }}
                        </h4>
                        <a href="{{ route('agent.index') }}" class="btn btn-light btn-sm">
                            <i class="las la-arrow-left me-1"></i>
                            {{ __('agent.back_to_ask') }}
                        </a>
                    </div>
                    <div class="card-body">
                        <!-- Search Input -->
                        <div class="mb-4">
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="las la-search"></i>
                                </span>
                                <input 
                                    type="text" 
                                    wire:model.live.debounce.500ms="search"
                                    class="form-control"
                                    placeholder="{{ __('agent.search_placeholder') }}"
                                    aria-label="{{ __('agent.search') }}"
                                >
                                @if($search)
                                    <button 
                                        class="btn btn-outline-secondary" 
                                        type="button"
                                        wire:click="$set('search', '')"
                                    >
                                        <i class="las la-times"></i>
                                    </button>
                                @endif
                            </div>
                        </div>

                        <!-- Questions List -->
                        @if($this->questions->count() > 0)
                            <div class="list-group mb-3">
                                @foreach($this->questions as $question)
                                    <div 
                                        class="list-group-item list-group-item-action"
                                        role="button"
                                        wire:click="selectQuestion({{ $question->id }})"
                                        data-bs-toggle="modal"
                                        data-bs-target="#questionModal"
                                    >
                                        <div class="d-flex w-100 justify-content-between align-items-start">
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1">
                                                    <i class="las la-question-circle text-primary me-1"></i>
                                                    {{ Str::limit($question->question_text, 100) }}
                                                </h6>
                                                <p class="mb-1 text-muted small">
                                                    {{ Str::limit($question->answer_text, 150) }}
                                                </p>
                                            </div>
                                            <div class="text-end ms-3">
                                                <small class="text-muted d-block">
                                                    <i class="las la-clock me-1"></i>
                                                    {{ $question->created_at->diffForHumans() }}
                                                </small>
                                                @if($question->domain)
                                                    <span class="badge bg-info mt-1">
                                                        {{ __('agent.domains.' . $question->domain) }}
                                                    </span>
                                                @endif
                                                @if($question->result_count !== null)
                                                    <span class="badge bg-secondary mt-1">
                                                        {{ $question->result_count }} {{ __('agent.results') }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <!-- Pagination -->
                            <div class="d-flex justify-content-center">
                                {{ $this->questions->links() }}
                            </div>
                        @else
                            <div class="alert alert-info text-center" role="alert">
                                <i class="las la-info-circle me-2"></i>
                                @if($search)
                                    {{ __('agent.no_search_results') }}
                                @else
                                    {{ __('agent.no_history') }}
                                @endif
                            </div>
                        @endif

                        <!-- Loading Indicator -->
                        <div wire:loading class="text-center mt-3">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">{{ __('agent.loading') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Question Details Modal -->
    <div 
        class="modal fade" 
        id="questionModal" 
        tabindex="-1" 
        aria-labelledby="questionModalLabel" 
        aria-hidden="true"
        wire:ignore.self
    >
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                @if($this->selectedQuestion)
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="questionModalLabel">
                            <i class="las la-file-alt me-2"></i>
                            {{ __('agent.question_details') }}
                        </h5>
                        <button 
                            type="button" 
                            class="btn-close btn-close-white" 
                            data-bs-dismiss="modal" 
                            aria-label="{{ __('agent.close') }}"
                            wire:click="clearSelection"
                        ></button>
                    </div>
                    <div class="modal-body">
                        <!-- Question -->
                        <div class="mb-4">
                            <h6 class="text-muted mb-2">
                                <i class="las la-question-circle me-1"></i>
                                {{ __('agent.question') }}
                            </h6>
                            <div class="card bg-light">
                                <div class="card-body">
                                    {{ $this->selectedQuestion->question_text }}
                                </div>
                            </div>
                        </div>

                        <!-- Answer -->
                        <div class="mb-4">
                            <h6 class="text-muted mb-2">
                                <i class="las la-check-circle me-1"></i>
                                {{ __('agent.answer') }}
                            </h6>
                            <div class="card bg-light">
                                <div class="card-body" style="white-space: pre-wrap;">
                                    {{ $this->selectedQuestion->answer_text }}
                                </div>
                            </div>
                        </div>

                        <!-- Metadata -->
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-body">
                                        <h6 class="card-subtitle mb-2 text-muted">
                                            <i class="las la-calendar me-1"></i>
                                            {{ __('agent.submitted_at') }}
                                        </h6>
                                        <p class="card-text">
                                            {{ $this->selectedQuestion->created_at->format('Y-m-d H:i:s') }}
                                            <br>
                                            <small class="text-muted">
                                                ({{ $this->selectedQuestion->created_at->diffForHumans() }})
                                            </small>
                                        </p>
                                    </div>
                                </div>
                            </div>

                            @if($this->selectedQuestion->domain)
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-body">
                                            <h6 class="card-subtitle mb-2 text-muted">
                                                <i class="las la-tag me-1"></i>
                                                {{ __('agent.domain') }}
                                            </h6>
                                            <p class="card-text">
                                                <span class="badge bg-info">
                                                    {{ __('agent.domains.' . $this->selectedQuestion->domain) }}
                                                </span>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if($this->selectedQuestion->result_count !== null)
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-body">
                                            <h6 class="card-subtitle mb-2 text-muted">
                                                <i class="las la-list me-1"></i>
                                                {{ __('agent.result_count') }}
                                            </h6>
                                            <p class="card-text">
                                                {{ $this->selectedQuestion->result_count }} {{ __('agent.results') }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if($this->selectedQuestion->processing_time_ms !== null)
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-body">
                                            <h6 class="card-subtitle mb-2 text-muted">
                                                <i class="las la-stopwatch me-1"></i>
                                                {{ __('agent.processing_time') }}
                                            </h6>
                                            <p class="card-text">
                                                {{ $this->selectedQuestion->processing_time_ms }} {{ __('agent.milliseconds') }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button 
                            type="button" 
                            class="btn btn-secondary" 
                            data-bs-dismiss="modal"
                            wire:click="clearSelection"
                        >
                            <i class="las la-times me-1"></i>
                            {{ __('agent.close') }}
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
