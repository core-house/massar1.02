<!-- Estimation Information Section -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-primary">
            <div class="card-header">
                <h6 class="card-title mb-0">
                    <i class="fas fa-calculator me-2"></i>
                    {{ __('Estimation Information') }}
                </h6>
                <small class="d-block mt-1">{{ __('Estimation and Pricing Details') }}</small>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label class="form-label fw-bold">{{ __('Start Date') }}</label>
                        <input type="datetime-local" wire:model="estimationStartDate" class="form-control">
                        @error('startDate')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-3 mb-3">
                        <label class="form-label fw-bold">{{ __('End Date') }}</label>
                        <input type="datetime-local" wire:model="estimationFinishedDate" class="form-control">
                        @error('finishedDate')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-3 mb-3">
                        <label class="form-label fw-bold">{{ __('Submission Date') }}</label>
                        <input type="datetime-local" wire:model="submittingDate" class="form-control">
                        @error('submittingDate')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-3 mb-3">
                        <label class="form-label fw-bold">{{ __('Total Project Value') }}</label>
                        <input type="number" wire:model="totalProjectValue" class="form-control"
                            placeholder="{{ __('Enter the value...') }}">
                        @error('totalProjectValue')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-3">
                        <label for="document_files" class="form-label fw-bold">
                            <i class="fas fa-upload me-2"></i>
                            {{ __('Upload Documents (Multiple Files)') }}
                        </label>
                        <input type="file" wire:model="documentFiles" id="document_files" class="form-control"
                            accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" multiple>

                        @error('documentFiles.*')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror

                        {{-- عرض الملفات الموجودة سابقًا (في التعديل فقط، فارغ في الإنشاء) --}}
                        @if (!empty($existingDocuments ?? []))
                            <div class="mt-3">
                                <h6 class="fw-bold mb-2 text-info">
                                    {{ __('Previously Saved Files') }} ({{ count($existingDocuments ?? []) }}):
                                </h6>
                                <div class="list-group mb-3">
                                    @foreach ($existingDocuments ?? [] as $doc)
                                        <div class="list-group-item d-flex justify-content-between align-items-center">
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-file-alt text-info me-2"></i>
                                                <a href="{{ $doc['url'] }}" target="_blank"
                                                    class="text-decoration-none">
                                                    <span class="text-info">{{ $doc['file_name'] }}</span>
                                                </a>
                                                <small
                                                    class="text-muted ms-2">({{ number_format($doc['size'] / 1024, 2) }}
                                                    {{ __('KB') }})</small>
                                            </div>
                                            <button type="button"
                                                wire:click="removeExistingDocument({{ $doc['id'] }})"
                                                class="btn btn-sm btn-outline-danger" title="{{ __('Delete File') }}"
                                                onclick="return confirm('{{ __('Are you sure you want to delete this file?') }}')">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        {{-- عرض الملفات المرفوعة الجديدة --}}
                        @if (!empty($documentFiles))
                            <div class="mt-3">
                                <h6 class="fw-bold mb-2 text-success">
                                    {{ __('Newly Uploaded Files') }} ({{ count($documentFiles) }}):
                                </h6>
                                <div class="list-group">
                                    @foreach ($documentFiles as $index => $file)
                                        <div class="list-group-item d-flex justify-content-between align-items-center">
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-file-alt text-success me-2"></i>
                                                <span class="text-success">{{ $file->getClientOriginalName() }}</span>
                                                <small
                                                    class="text-muted ms-2">({{ number_format($file->getSize() / 1024, 2) }}
                                                    {{ __('KB') }})</small>
                                            </div>
                                            <button type="button" wire:click="removeDocumentFile({{ $index }})"
                                                class="btn btn-sm btn-danger" title="{{ __('Delete File') }}">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        {{-- Loading indicator --}}
                        <div wire:loading wire:target="documentFiles" class="mt-2">
                            <div class="spinner-border spinner-border-sm text-primary" role="status">
                                <span class="visually-hidden">{{ __('Uploading...') }}</span>
                            </div>
                            <small class="text-primary ms-2">{{ __('Uploading files...') }}</small>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
