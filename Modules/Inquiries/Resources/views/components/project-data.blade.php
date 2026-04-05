<!-- Project Data Section -->
<div class="row mb-4 ">
    <div class="col-12 ">
        <div class="card border-success ">
            <div class="card-header">
                <h2 class="card-title mb-0">
                    <i class="fas fa-project-diagram me-2"></i>
                    {{ __('inquiries::inquiries.project_data') }}
                </h2>
                <small class="d-block mt-1">{{ __('inquiries::inquiries.basic_project') }}</small>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 mb-3 d-flex flex-column">
                        <label class="form-label fw-bold">{{ __('inquiries::inquiries.project') }}</label>
                        <livewire:app::searchable-select
                            :model="Modules\Progress\Models\Project::class"
                            label-field="name"
                            wire-model="projectId"
                            placeholder="{{ __('inquiries::inquiries.search_for_project_or_add_new') }}"
                            :key="'project-select'"
                            :selected-id="$projectId" />
                    </div>

                    <div class="col-md-2 mb-3">
                        <label class="form-label fw-bold">{{ __('inquiries::inquiries.tender_number') }}</label>
                        <input type="text" wire:model="tenderNo" class="form-control">
                        @error('tenderNo')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-bold">{{ __('inquiries::inquiries.tender_id') }}</label>
                        <input type="text" wire:model="tenderId" class="form-control">
                        @error('tenderId')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-3 mb-3">
                        <label class="form-label fw-bold">{{ __('inquiries::inquiries.add_files') }}</label>
                        <input type="file" wire:model="projectImage" id="projectImage"
                            class="form-control @error('projectImage') is-invalid @enderror">
                        @error('projectImage')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror

                        {{-- عرض الملف الموجود سابقًا --}}
                        @if (!empty($existingProjectImage))
                            <div class="mt-3">
                                <h6 class="fw-bold mb-2 text-info">
                                    {{ __('inquiries::inquiries.previously_saved_files') }} (1):
                                </h6>
                                <div class="list-group mb-3">
                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-file-alt text-info me-2"></i>
                                            <a href="{{ '/media/' . $existingProjectImage->id }}" target="_blank"
                                                class="text-decoration-none">
                                                <span class="text-info">{{ $existingProjectImage->file_name }}</span>
                                            </a>
                                            <small class="text-muted ms-2">
                                                ({{ number_format($existingProjectImage->size / 1024, 2) }}
                                                {{ __('inquiries::inquiries.kb') }})
                                            </small>
                                        </div>
                                        <button type="button" wire:click="removeExistingProjectImage"
                                            class="btn btn-sm btn-outline-danger" title="{{ __('Delete File') }}"
                                            data-confirm="{{ __('Are you sure you want to delete this file?') }}"
                                            onclick="return confirm(this.getAttribute('data-confirm'))">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- عرض الملف الجديد المرفوع --}}
                        @if (!empty($projectImage))
                            <div class="mt-3">
                                <h6 class="fw-bold mb-2 text-success">
                                    {{ __('inquiries::inquiries.newly_uploaded_files') }} (1):
                                </h6>
                                <div class="list-group">
                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-file-alt text-success me-2"></i>
                                            <span
                                                class="text-success">{{ $projectImage->getClientOriginalName() }}</span>
                                            <small class="text-muted ms-2">
                                                ({{ number_format($projectImage->getSize() / 1024, 2) }}
                                                {{ __('inquiries::inquiries.kb') }})
                                            </small>
                                        </div>
                                        <button type="button" wire:click="removeNewProjectImage"
                                            class="btn btn-sm btn-danger" title="{{ __('inquiries::inquiries.delete_file') }}">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- Loading indicator --}}
                        <div wire:loading wire:target="projectImage" class="mt-2">
                            <div class="spinner-border spinner-border-sm text-primary" role="status">
                                <span class="visually-hidden">{{ __('inquiries::inquiries.uploading') }}</span>
                            </div>
                            <small class="text-primary ms-2">{{ __('inquiries::inquiries.uploading_files') }}</small>
                        </div>
                    </div>

                    <div class="col-md-2 mb-3">
                        <label class="form-label fw-bold">{{ __('inquiries::inquiries.inquiry_status_for_client') }}</label>
                        <select wire:model="status" class="form-select">
                            <option value="">{{ __('inquiries::inquiries.select_status') }}</option>
                            @foreach ($statusOptions as $status)
                                <option value="{{ $status->value }}">
                                    {{ $status->label() }}</option>
                            @endforeach
                        </select>
                        @error('status')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-2 mb-3">
                        <label class="form-label fw-bold">{{ __('inquiries::inquiries.inquiry_status_for_kon') }}</label>
                        <select wire:model="statusForKon" class="form-select">
                            <option value="">{{ __('inquiries::inquiries.select') }}</option>
                            @foreach ($statusForKonOptions as $status)
                                <option value="{{ $status->value }}">
                                    {{ $status->label() }}</option>
                            @endforeach
                        </select>
                        @error('statusForKon')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-2 mb-3">
                        <label class="form-label fw-bold">{{ __('inquiries::inquiries.kon_position') }}</label>
                        <select wire:model="konTitle" class="form-select">
                            <option value="">{{ __('inquiries::inquiries.select_title...') }}</option>
                            @foreach ($konTitleOptions as $title)
                                <option value="{{ $title->value }}">{{ $title->label() }}</option>
                            @endforeach
                        </select>
                        @error('konTitle')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-2 mb-3">
                        <label class="form-label fw-bold">{{ __('inquiries::inquiries.inquiry_received_date') }}</label>
                        <input type="date" wire:model="inquiryDate" class="form-control">
                        @error('inquiryDate')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-2 mb-3">
                        <label class="form-label fw-bold">{{ __('inquiries::inquiries.required_delivery_date') }}</label>
                        <input type="date" wire:model="reqSubmittalDate" class="form-control">
                        @error('reqSubmittalDate')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-2 mb-3">
                        <label class="form-label fw-bold">{{ __('inquiries::inquiries.project_start_date') }}</label>
                        <input type="date" wire:model="projectStartDate" class="form-control">
                        @error('projectStartDate')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
