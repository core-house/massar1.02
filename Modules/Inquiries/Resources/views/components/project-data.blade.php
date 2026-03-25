<!-- Project Data Section -->
<div class="row mb-4 ">
    <div class="col-12 ">
        <div class="card border-success ">
            <div class="card-header">
                <h2 class="card-title mb-0">
                    <i class="fas fa-project-diagram me-2"></i>
                    {{ __('Project Data') }}
                </h2>
                <small class="d-block mt-1">{{ __('Basic project information and important dates') }}</small>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 mb-3 d-flex flex-column">
                        <label class="form-label fw-bold">{{ __('Project') }}</label>
                        <livewire:app::searchable-select
                            :model="Modules\Progress\Models\Project::class"
                            label-field="name"
                            wire-model="projectId"
                            placeholder="{{ __('Search for project or add new...') }}"
                            :key="'project-select'"
                            :selected-id="$projectId" />
                    </div>

                    <div class="col-md-2 mb-3">
                        <label class="form-label fw-bold">{{ __('Tender Number') }}</label>
                        <input type="text" wire:model="tenderNo" class="form-control">
                        @error('tenderNo')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-bold">{{ __('Tender ID') }}</label>
                        <input type="text" wire:model="tenderId" class="form-control">
                        @error('tenderId')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-3 mb-3">
                        <label class="form-label fw-bold">{{ __('Attach File') }}</label>
                        <input type="file" wire:model="projectImage" id="projectImage"
                            class="form-control @error('projectImage') is-invalid @enderror">
                        @error('projectImage')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror

                        {{-- عرض الملف الموجود سابقًا --}}
                        @if (!empty($existingProjectImage))
                            <div class="mt-3">
                                <h6 class="fw-bold mb-2 text-info">
                                    {{ __('Previously Saved Files') }} (1):
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
                                                {{ __('KB') }})
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
                                    {{ __('Newly Uploaded Files') }} (1):
                                </h6>
                                <div class="list-group">
                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-file-alt text-success me-2"></i>
                                            <span
                                                class="text-success">{{ $projectImage->getClientOriginalName() }}</span>
                                            <small class="text-muted ms-2">
                                                ({{ number_format($projectImage->getSize() / 1024, 2) }}
                                                {{ __('KB') }})
                                            </small>
                                        </div>
                                        <button type="button" wire:click="removeNewProjectImage"
                                            class="btn btn-sm btn-danger" title="{{ __('Delete File') }}">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- Loading indicator --}}
                        <div wire:loading wire:target="projectImage" class="mt-2">
                            <div class="spinner-border spinner-border-sm text-primary" role="status">
                                <span class="visually-hidden">{{ __('Uploading...') }}</span>
                            </div>
                            <small class="text-primary ms-2">{{ __('Uploading files...') }}</small>
                        </div>
                    </div>

                    <div class="col-md-2 mb-3">
                        <label class="form-label fw-bold">{{ __('Inquiry Status For Client') }}</label>
                        <select wire:model="status" class="form-select">
                            <option value="">{{ __('Select status...') }}</option>
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
                        <label class="form-label fw-bold">{{ __('Inquiry Status For KON') }}</label>
                        <select wire:model="statusForKon" class="form-select">
                            <option value="">{{ __('Select...') }}</option>
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
                        <label class="form-label fw-bold">{{ __('KON Position') }}</label>
                        <select wire:model="konTitle" class="form-select">
                            <option value="">{{ __('Select title...') }}</option>
                            @foreach ($konTitleOptions as $title)
                                <option value="{{ $title->value }}">{{ $title->label() }}</option>
                            @endforeach
                        </select>
                        @error('konTitle')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-2 mb-3">
                        <label class="form-label fw-bold">{{ __('Inquiry Received Date') }}</label>
                        <input type="date" wire:model="inquiryDate" class="form-control">
                        @error('inquiryDate')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-2 mb-3">
                        <label class="form-label fw-bold">{{ __('Inquiry Delivery Date') }}</label>
                        <input type="date" wire:model="reqSubmittalDate" class="form-control">
                        @error('reqSubmittalDate')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-2 mb-3">
                        <label class="form-label fw-bold">{{ __('Project Start Date') }}</label>
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
