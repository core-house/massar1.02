<?php

declare(strict_types=1);

use Livewire\Volt\Component;
use Modules\Agent\Models\AgentQuestion;
use Modules\Agent\Services\IntentClassifier;
use Modules\Agent\Services\ResponseFormatter;
use Modules\Agent\Services\Domains\HRQueryService;
use Modules\Agent\Services\Domains\InvoiceQueryService;
use Modules\Agent\Services\Domains\InventoryQueryService;
use Modules\Agent\Services\Domains\CRMQueryService;
use Modules\Agent\Services\DomainQueryService;
use Modules\Agent\Exceptions\InvalidQuestionException;
use Modules\Agent\Exceptions\UnsupportedDomainException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

new class extends Component {
    public string $questionText = '';
    public ?string $answerText = null;
    public bool $isProcessing = false;
    public ?string $errorMessage = null;
    public ?int $currentQuestionId = null;

    /**
     * Submit question and process it with Transaction Isolation
     */
    public function submitQuestion(): void
    {
        // Validate input
        $this->validate([
            'questionText' => ['required', 'string', 'max:1000', 'min:5'],
        ], [
            'questionText.required' => __('agent.validation.question_required'),
            'questionText.min' => __('agent.validation.question_min'),
            'questionText.max' => __('agent.validation.question_max'),
        ]);

        $this->isProcessing = true;
        $this->errorMessage = null;
        $this->answerText = null;

        try {
            // Phase 1: Create pending record (SHORT transaction)
            $question = DB::transaction(function () {
                return AgentQuestion::create([
                    'user_id' => auth()->id(),
                    'question_text' => $this->questionText,
                    'status' => 'pending',
                ]);
            });

            $this->currentQuestionId = $question->id;

            // Phase 2: Process question (NO transaction - long operation)
            $classification = app(IntentClassifier::class)->classify($this->questionText);

            if (!$classification->isValid()) {
                throw new InvalidQuestionException(
                    $classification->isMultiIntent 
                        ? __('agent.errors.multi_intent_question')
                        : __('agent.errors.unmappable_question')
                );
            }

            $domainService = $this->getDomainService($classification->domain);
            $plan = $domainService->createQueryPlan($classification, $this->questionText);
            $result = $domainService->execute($plan, auth()->user());

            // Format response
            $this->answerText = app(ResponseFormatter::class)->format($result, $plan, $this->questionText);

            // Phase 3: Update answered record (SHORT transaction)
            DB::transaction(function () use ($question, $result, $classification) {
                $question->update([
                    'answer_text' => $this->answerText,
                    'domain' => $classification->domain,
                    'result_count' => $result->count,
                    'processing_time_ms' => $result->executionTime,
                    'status' => 'answered',
                ]);
            });

        } catch (InvalidQuestionException $e) {
            // Phase 3b: Update failed record (SHORT transaction)
            if ($this->currentQuestionId) {
                DB::transaction(function () use ($e) {
                    AgentQuestion::find($this->currentQuestionId)->update([
                        'status' => 'failed',
                        'error_message' => $e->getMessage(),
                    ]);
                });
            }

            $this->errorMessage = $e->getMessage();

        } catch (\Exception $e) {
            // Phase 3c: Update failed record (SHORT transaction)
            if ($this->currentQuestionId) {
                DB::transaction(function () use ($e) {
                    AgentQuestion::find($this->currentQuestionId)->update([
                        'status' => 'failed',
                        'error_message' => __('agent.errors.processing_error'),
                    ]);
                });
            }

            $this->errorMessage = __('agent.errors.processing_error');

            Log::error('Agent question processing failed', [
                'question_id' => $this->currentQuestionId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

        } finally {
            $this->isProcessing = false;
        }
    }

    /**
     * Get domain service based on domain name
     */
    private function getDomainService(string $domain): DomainQueryService
    {
        return match($domain) {
            'hr' => app(HRQueryService::class),
            'invoices' => app(InvoiceQueryService::class),
            'inventory' => app(InventoryQueryService::class),
            'crm' => app(CRMQueryService::class),
            default => throw new UnsupportedDomainException(__('agent.errors.unsupported_domain')),
        };
    }

    /**
     * Reset form to initial state
     */
    public function resetForm(): void
    {
        $this->reset(['questionText', 'answerText', 'errorMessage', 'currentQuestionId']);
    }
}; ?>

<div>
    <div class="container-fluid py-4">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">
                            <i class="las la-question-circle me-2"></i>
                            {{ __('agent.title') }}
                        </h4>
                    </div>
                    <div class="card-body">
                        <!-- Question Form -->
                        <form wire:submit="submitQuestion">
                            <div class="mb-3" x-data="{ count: $wire.entangle('questionText').live.length }">
                                <label for="questionText" class="form-label">
                                    {{ __('agent.ask_question') }}
                                </label>
                                <textarea 
                                    wire:model="questionText"
                                    id="questionText"
                                    class="form-control @error('questionText') is-invalid @enderror"
                                    rows="4"
                                    placeholder="{{ __('agent.question_placeholder') }}"
                                    maxlength="1000"
                                    :disabled="$wire.isProcessing"
                                    x-on:input="count = $event.target.value.length"
                                ></textarea>
                                
                                <!-- Character Counter -->
                                <div class="form-text text-end" x-text="`${count} {{ __('agent.character_count_of') }} 1000`"></div>
                                
                                @error('questionText')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-flex gap-2">
                                <button 
                                    type="submit" 
                                    class="btn btn-primary"
                                    :disabled="$wire.isProcessing"
                                    wire:loading.attr="disabled"
                                >
                                    <span wire:loading.remove>
                                        <i class="las la-paper-plane me-1"></i>
                                        {{ __('agent.submit') }}
                                    </span>
                                    <span wire:loading>
                                        <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                                        {{ __('agent.processing') }}
                                    </span>
                                </button>

                                <button 
                                    type="button" 
                                    class="btn btn-secondary"
                                    wire:click="resetForm"
                                    :disabled="$wire.isProcessing"
                                >
                                    <i class="las la-redo me-1"></i>
                                    {{ __('agent.reset') }}
                                </button>

                                <a href="{{ route('agent.history') }}" class="btn btn-outline-primary">
                                    <i class="las la-history me-1"></i>
                                    {{ __('agent.history') }}
                                </a>
                            </div>
                        </form>

                        <!-- Error Message -->
                        @if($errorMessage)
                            <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
                                <i class="las la-exclamation-triangle me-2"></i>
                                {{ $errorMessage }}
                                <button type="button" class="btn-close" wire:click="$set('errorMessage', null)"></button>
                            </div>
                        @endif

                        <!-- Answer Display -->
                        @if($answerText)
                            <div class="mt-4">
                                <div class="card bg-light">
                                    <div class="card-header bg-success text-white">
                                        <h5 class="mb-0">
                                            <i class="las la-check-circle me-2"></i>
                                            {{ __('agent.answer') }}
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="answer-content" style="white-space: pre-wrap;">{{ $answerText }}</div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Loading Indicator -->
                        <div wire:loading wire:target="submitQuestion" class="mt-3">
                            <div class="alert alert-info d-flex align-items-center" role="alert">
                                <div class="spinner-border spinner-border-sm me-3" role="status">
                                    <span class="visually-hidden">{{ __('agent.loading') }}</span>
                                </div>
                                <div>{{ __('agent.processing_message') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
