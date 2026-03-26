<div>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <form wire:submit.prevent="save('final')">
                    <div class="card-body">

                        @if ($isDraft)
                            <div class="alert alert-warning d-flex align-items-center mb-4" role="alert">
                                <i class="fas fa-edit me-3 fa-2x"></i>
                                <div>
                                    <h4 class="alert-heading mb-1">{{ __('Draft Mode') }}</h4>
                                    <p class="mb-0">{{ __('This inquiry is currently saved as a draft.') }}</p>
                                </div>
                            </div>
                        @endif



                        <!-- Project Data Section -->
                        @include('inquiries::components.project-data')
                        <!-- Quotation State Section -->
                        @include('inquiries::components.quotation-state')

                        @include('inquiries::components.assigned-enginner')

                        @include('inquiries::components.cities-select')

                        <!-- Work Types Section & Inquiry Sources -->
                        @include('inquiries::components.work-types&inquiry-source')
                    </div>
                    <!-- Stakeholders Section -->
                    @include('inquiries::components.Stakeholders-Section')
                    {{-- بعد حقل المهندس الحالي (Contact Engineer) --}}



                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card border-dark">
                                <div class="card-header ">
                                    <h5>{{ __('Required Quotation Information') }}</h5>
                                </div>
                                <div class="card-body">
                                    {{-- Quotation Types & Units: Side by Side --}}
                                    <div class="row">
                                        @foreach ($quotationTypes as $type)
                                            <div class="col-md-2 mb-3">
                                                <div class="card h-100">
                                                    <div class="card-header">
                                                        <h6 class="mb-0 text-primary">{{ $type->name }}</h6>
                                                    </div>
                                                    <div class="card-body p-2">
                                                        @forelse ($type->units as $unit)
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="checkbox"
                                                                    wire:model="selectedQuotationUnits.{{ $type->id }}.{{ $unit->id }}"
                                                                    id="quotation_unit_{{ $type->id }}_{{ $unit->id }}">
                                                                <label class="form-check-label small"
                                                                    for="quotation_unit_{{ $type->id }}_{{ $unit->id }}">
                                                                    {{ $unit->name }}
                                                                </label>
                                                            </div>
                                                        @empty
                                                            <p class="text-muted small text-center mb-0">
                                                                {{ __('No units available for this type') }}</p>
                                                        @endforelse
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    <div class="row mt-3">
                                        <div class="col-12">
                                            <label for="type_note"
                                                class="form-label">{{ __('Type Notes (Optional)') }}</label>
                                            <textarea class="form-control" id="type_note" rows="3" wire:model="type_note"
                                                placeholder="{{ __('Enter any additional notes here...') }}"></textarea>
                                            @error('type_note')
                                                <div class="text-danger small mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    @error('selectedQuotationUnits')
                                        <div class="alert alert-danger mt-3">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <!-- Project Documents Section -->
                        <div class="col-6">
                            <div class="card border-primary">
                                <div class="card-header">
                                    <h6 class="card-title mb-0">
                                        <i class="fas fa-file-alt me-2"></i>
                                        {{ __('Project Documents') }}
                                    </h6>
                                    <small class="d-block mt-1">{{ __('Select available documents') }}</small>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        @foreach ($projectDocuments as $index => $document)
                                            <div class="col-md-3 mb-3">
                                                <div class="form-check">
                                                    <input type="checkbox"
                                                        wire:model="projectDocuments.{{ $index }}.checked"
                                                        id="document_{{ $index }}" class="form-check-input">
                                                    <label for="document_{{ $index }}" class="form-check-label">
                                                        {{ $document['name'] }}
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Required Submittal Checklist & Working Conditions Section -->
                        @include('inquiries::components.submittal-work-condition')
                    </div>

                    <!-- Estimation Information Section -->
                    @include('inquiries::components.estimation-information')

                    <!-- Temporary Comments Section -->
                    @include('inquiries::components.inquiry-comments')

                    <!-- Form Actions -->
                    <div class="row p-3 bg-white border-top shadow-lg" style="position: fixed; bottom: 0; left: 0; right: 0; z-index: 1020; margin: 0;">
                        <div class="col-12 px-4">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <a href="{{ route('inquiries.index') }}" class="btn btn-secondary btn-lg">
                                        <i class="fas fa-times me-2"></i>
                                        {{ __('Cancel') }}
                                    </a>
                                </div>

                                <div class="btn-group" role="group">
                                    <button type="button" wire:click="save('draft')"
                                        class="btn btn-warning btn-lg">
                                        <i class="fas fa-file-alt me-2"></i>
                                        {{ __('Save as Draft') }}
                                    </button>

                                    <button type="button" wire:click="save" class="btn btn-success btn-lg">
                                        <i class="fas fa-check-circle me-2"></i>
                                        @if ($isDraft)
                                            {{ __('Publish Inquiry') }}
                                        @else
                                            {{ __('Save Inquiry') }}
                                        @endif
                                    </button>
                                </div>
                            </div>

                            @if ($isDraft)
                                <div class="mt-2 text-end">
                                    <small class="text-muted">
                                        <i class="fas fa-info-circle me-1"></i>
                                        {{ __('Click "Save as Draft" to save changes. Click "Publish Inquiry" to finalize.') }}
                                    </small>
                                </div>
                            @endif
                        </div>
                    </div>
                     {{-- Spacer to prevent footer from covering content --}}
                    <div style="height: 100px;"></div>

                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        document.addEventListener('livewire:initialized', function() {
            // Show notification when draft is saved
            Livewire.on('draftSaved', (data) => {
                // يمكن إضافة notification هنا
                console.log('Draft saved successfully!', data);
            });

            // Work Types Hierarchical Selection
            const stepsWrapper = document.getElementById('steps_wrapper');
            const workTypesRow = document.getElementById('work_types_row');

            function createWorkTypeStepItem(stepNum, parentId) {
                Livewire.dispatch('getWorkTypeChildren', {
                    stepNum: stepNum - 1,
                    parentId: parentId
                });
            }

            function removeWorkTypeStepsAfter(stepNum) {
                const stepsToRemove = stepsWrapper.querySelectorAll('[data-step]');
                stepsToRemove.forEach(step => {
                    const stepNumber = parseInt(step.getAttribute('data-step'));
                    if (stepNumber > stepNum) {
                        step.remove();
                    }
                });
            }

            // Listen for workTypeChildrenLoaded
            Livewire.on('workTypeChildrenLoaded', ({
                stepNum,
                children
            }) => {
                if (children.length === 0) {
                    return;
                }

                const nextStepNum = stepNum + 1;
                const existingStep = document.querySelector(`[data-step="${nextStepNum}"]`);

                if (!existingStep) {
                    const stepItem = document.createElement('div');
                    stepItem.className = 'col-md-3';
                    stepItem.setAttribute('data-step', nextStepNum);
                    stepItem.innerHTML = `
                        <label class="form-label fw-bold">
                            <span class="badge bg-primary me-2">${nextStepNum}</span>
                            Classification ${nextStepNum}
                        </label>
                        <select wire:model.live="currentWorkTypeSteps.step_${nextStepNum}" id="step_${nextStepNum}" class="form-select">
                            <option value="">Select step ${nextStepNum}...</option>
                                </select>
                             `;

                    workTypesRow.appendChild(stepItem);

                    const select = document.getElementById(`step_${nextStepNum}`);
                    select.addEventListener('change', function() {
                        const selectedId = this.value;
                        if (selectedId) {
                            removeWorkTypeStepsAfter(nextStepNum);
                            createWorkTypeStepItem(nextStepNum + 1, selectedId);
                        } else {
                            removeWorkTypeStepsAfter(nextStepNum);
                        }
                    });
                }

                const select = document.getElementById(`step_${nextStepNum}`);
                if (select) {
                    select.innerHTML = `<option value="">Select step ${nextStepNum}...</option>`;
                    children.forEach(item => {
                        select.add(new Option(item.name, item.id));
                    });
                }
            });

            // Inquiry Sources Hierarchical Selection
            const inquiryStepsWrapper = document.getElementById('inquiry_sources_steps_wrapper');
            const inquirySourcesRow = document.getElementById('inquiry_sources_row');

            function createInquirySourceStepItem(stepNum, parentId) {
                Livewire.dispatch('getInquirySourceChildren', {
                    stepNum: stepNum - 1,
                    parentId: parentId
                });
            }

            function removeInquirySourceStepsAfter(stepNum) {
                const stepsToRemove = inquiryStepsWrapper.querySelectorAll('[data-step]');
                stepsToRemove.forEach(step => {
                    const stepNumber = parseInt(step.getAttribute('data-step'));
                    if (stepNumber > stepNum) {
                        step.remove();
                    }
                });
            }
            Livewire.on('workTypeAdded', () => {
                const step1Select = document.getElementById('step_1');
                step1Select.value = '';
                removeWorkTypeStepsAfter(1);
                Livewire.dispatch('updateCurrentWorkPath', {
                    path: []
                });
            });

            Livewire.on('inquirySourceChildrenLoaded', ({
                stepNum,
                children
            }) => {
                if (children.length === 0) {
                    return;
                }

                const nextStepNum = stepNum + 1;
                const existingStep = document.querySelector(
                    `#inquiry_sources_row [data-step="${nextStepNum}"]`);

                if (!existingStep) {
                    const stepItem = document.createElement('div');
                    stepItem.className = 'col-md-3';
                    stepItem.setAttribute('data-step', nextStepNum);
                    stepItem.innerHTML = `
                        <label class="form-label fw-bold">
                            <span class="badge bg-warning text-dark me-2">${nextStepNum}</span>
                            المصدر ${nextStepNum}
                        </label>
                        <select wire:model.live="inquirySourceSteps.inquiry_source_step_${nextStepNum}" id="inquiry_source_step_${nextStepNum}" class="form-select">
                            <option value="">Select step ${nextStepNum}...</option>
                        </select>
                    `;

                    inquirySourcesRow.appendChild(stepItem);

                    const select = document.getElementById(`inquiry_source_step_${nextStepNum}`);
                    select.addEventListener('change', function() {
                        const selectedId = this.value;
                        if (selectedId) {
                            removeInquirySourceStepsAfter(nextStepNum);
                            createInquirySourceStepItem(nextStepNum + 1, selectedId);
                        } else {
                            removeInquirySourceStepsAfter(nextStepNum);
                        }
                    });
                }

                const select = document.getElementById(`inquiry_source_step_${nextStepNum}`);
                if (select) {
                    select.innerHTML = `<option value="">Select step ${nextStepNum}...</option>`;
                    children.forEach(item => {
                        select.add(new Option(item.name, item.id));
                    });
                }
            });

            // Handle step_1 change
            document.getElementById('step_1').addEventListener('change', function() {
                const selectedId = this.value;
                removeWorkTypeStepsAfter(1);
                if (selectedId) {
                    createWorkTypeStepItem(2, selectedId);
                }
            });

            // Handle inquiry_source_step_1 change
            document.getElementById('inquiry_source_step_1').addEventListener('change', function() {
                const selectedId = this.value;
                removeInquirySourceStepsAfter(1);
                if (selectedId) {
                    createInquirySourceStepItem(2, selectedId);
                }
            });
        });
    </script>
@endpush
</div>
