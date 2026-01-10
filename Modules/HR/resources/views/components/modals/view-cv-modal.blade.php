@props(['viewCv' => null])

<!-- View CV Modal -->
<div class="modal fade" id="viewModal" tabindex="-1" wire:ignore.self>
    <div class="modal-dialog modal-fullscreen" style="width: 100vw; max-width: 100vw; height: 100vh; margin: 0;">
        <div class="modal-content border-0 shadow-lg" style="height: 100vh; border-radius: 0; border: none;">
            <div class="modal-header bg-info text-white border-0">
                <div class="d-flex align-items-center">
                    <i class="mdi mdi-eye-outline me-2 fs-4"></i>
                    <h5 class="modal-title mb-0">CV Details</h5>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body bg-light" style="overflow-y: auto; max-height: calc(100vh - 150px);">
                @if($viewCv)
                    <div class="row g-4">
                        <div class="col-md-8">
                            <div class="card border-0 shadow-sm">
                                <div class="card-header bg-white border-0">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-lg me-3">
                                            <div class="avatar-title bg-primary rounded-circle">
                                                {{ strtoupper(substr($viewCv->name, 0, 2)) }}
                                            </div>
                                        </div>
                                        <div>
                                            <h4 class="mb-1">{{ $viewCv->name }}</h4>
                                            <p class="text-muted mb-0">
                                                <i class="mdi mdi-email-outline me-1"></i>{{ $viewCv->email }} | 
                                                <i class="mdi mdi-phone-outline me-1"></i>{{ $viewCv->phone }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <strong>Nationality:</strong> {{ $viewCv->nationality }}<br>
                                            <strong>Gender:</strong> {{ ucfirst($viewCv->gender) }}<br>
                                            <strong>Marital Status:</strong> {{ ucfirst($viewCv->marital_status) }}<br>
                                            <strong>Religion:</strong> {{ $viewCv->religion }}
                                        </div>
                                        <div class="col-md-6">
                                            <strong>Birth Date:</strong> {{ $viewCv->birth_date }}<br>
                                            <strong>Location:</strong> {{ $viewCv->city }}, {{ $viewCv->country }}<br>
                                            <strong>Address:</strong> {{ $viewCv->address ?: 'N/A' }}
                                        </div>
                                    </div>

                                    @if($viewCv->summary)
                                        <div class="mb-3">
                                            <h6>Professional Summary</h6>
                                            <p>{{ $viewCv->summary }}</p>
                                        </div>
                                    @endif

                                    @if($viewCv->skills)
                                        <div class="mb-3">
                                            <h6>Skills</h6>
                                            <p>{{ $viewCv->skills }}</p>
                                        </div>
                                    @endif

                                    @if($viewCv->experience)
                                        <div class="mb-3">
                                            <h6>Work Experience</h6>
                                            <p>{{ $viewCv->experience }}</p>
                                        </div>
                                    @endif

                                    @if($viewCv->education)
                                        <div class="mb-3">
                                            <h6>Education</h6>
                                            <p>{{ $viewCv->education }}</p>
                                        </div>
                                    @endif

                                    @if($viewCv->projects)
                                        <div class="mb-3">
                                            <h6>Projects</h6>
                                            <p>{{ $viewCv->projects }}</p>
                                        </div>
                                    @endif

                                    @if($viewCv->certifications)
                                        <div class="mb-3">
                                            <h6>Certifications</h6>
                                            <p>{{ $viewCv->certifications }}</p>
                                        </div>
                                    @endif

                                    <div class="row">
                                        @if($viewCv->languages)
                                            <div class="col-md-6">
                                                <h6>Languages</h6>
                                                <p>{{ $viewCv->languages }}</p>
                                            </div>
                                        @endif
                                        @if($viewCv->interests)
                                            <div class="col-md-6">
                                                <h6>Interests</h6>
                                                <p>{{ $viewCv->interests }}</p>
                                            </div>
                                        @endif
                                    </div>

                                    @if($viewCv->references)
                                        <div class="mb-3">
                                            <h6>References</h6>
                                            <p>{{ $viewCv->references }}</p>
                                        </div>
                                    @endif

                                    @if($viewCv->cover_letter)
                                        <div class="mb-3">
                                            <h6>Cover Letter</h6>
                                            <p>{{ $viewCv->cover_letter }}</p>
                                        </div>
                                    @endif

                                    @if($viewCv->portfolio)
                                        <div class="mb-3">
                                            <h6>Portfolio</h6>
                                            <a href="{{ $viewCv->portfolio }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                <i class="mdi mdi-link"></i> View Portfolio
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-body">
                                    <h6>CV File</h6>
                                    @if($viewCv->getFirstMedia('HR_Cvs'))
                                        <button wire:click="downloadCv({{ $viewCv->id }})" class="btn btn-primary w-100 mb-2">
                                            <i class="mdi mdi-download"></i> Download CV
                                        </button>
                                    @else
                                        <p class="text-muted">No CV file uploaded</p>
                                    @endif
                                    
                                    <hr>
                                    <h6>Actions</h6>
                                    <button wire:click="edit({{ $viewCv->id }})" class="btn btn-warning w-100 mb-2" data-bs-dismiss="modal">
                                        <i class="mdi mdi-pencil"></i> Edit CV
                                    </button>
                                    <button wire:click="delete({{ $viewCv->id }})" class="btn btn-danger w-100 mb-2" data-bs-dismiss="modal">
                                        <i class="mdi mdi-delete"></i> Delete CV
                                    </button>
                                    <button type="button" class="btn btn-secondary w-100" data-bs-dismiss="modal">
                                        <i class="mdi mdi-close"></i> Close
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="mdi mdi-file-document-outline text-muted" style="font-size: 4rem;"></i>
                        <h5 class="mt-3 mb-2">No CV Selected</h5>
                        <p class="text-muted">Please select a CV to view its details.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>