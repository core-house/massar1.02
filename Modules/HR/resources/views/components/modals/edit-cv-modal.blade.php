@props(['editingCv' => null])

<!-- Edit CV Modal -->
<div class="modal fade" id="editModal" tabindex="-1" wire:ignore.self>
    <div class="modal-dialog modal-fullscreen" style="width: 100vw; max-width: 100vw; height: 100vh; margin: 0;">
        <div class="modal-content border-0 shadow-lg" style="height: 100vh; border-radius: 0; border: none;">
            <div class="modal-header bg-warning text-white border-0">
                <div class="d-flex align-items-center">
                    <i class="mdi mdi-pencil-outline me-2 fs-4"></i>
                    <h5 class="modal-title mb-0">Edit CV</h5>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form wire:submit.prevent="update">
                <div class="modal-body bg-light" style="overflow-y: auto; max-height: calc(100vh - 150px);">
                    <div class="row g-4">
                        <!-- Personal Information -->
                        <div class="col-md-12">
                            <div class="card border-0 shadow-sm">
                                <div class="card-header bg-white border-0">
                                    <h6 class="mb-0 text-primary">
                                        <i class="mdi mdi-account-outline me-2"></i>Personal Information
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Full Name *</label>
                                        <input wire:model="name" type="text" class="form-control @error('name') is-invalid @enderror" required>
                                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email</label>
                                        <input wire:model="email" type="email" class="form-control @error('email') is-invalid @enderror">
                                        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="phone" class="form-label">Phone *</label>
                                        <input wire:model="phone" type="text" class="form-control @error('phone') is-invalid @enderror" required>
                                        @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="birth_date" class="form-label">Birth Date *</label>
                                                <input wire:model="birth_date" type="date" class="form-control @error('birth_date') is-invalid @enderror" required>
                                                @error('birth_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="gender" class="form-label">Gender *</label>
                                                <select wire:model="gender" class="form-select @error('gender') is-invalid @enderror" required>
                                                    <option value="">Select Gender</option>
                                                    <option value="male">Male</option>
                                                    <option value="female">Female</option>
                                                    <option value="other">Other</option>
                                                </select>
                                                @error('gender') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="marital_status" class="form-label">Marital Status *</label>
                                                <select wire:model="marital_status" class="form-select @error('marital_status') is-invalid @enderror" required>
                                                    <option value="">Select Status</option>
                                                    <option value="single">Single</option>
                                                    <option value="married">Married</option>
                                                    <option value="divorced">Divorced</option>
                                                    <option value="widowed">Widowed</option>
                                                </select>
                                                @error('marital_status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="nationality" class="form-label">Nationality *</label>
                                                <input wire:model="nationality" type="text" class="form-control @error('nationality') is-invalid @enderror" required>
                                                @error('nationality') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="religion" class="form-label">Religion *</label>
                                        <input wire:model="religion" type="text" class="form-control @error('religion') is-invalid @enderror" required>
                                        @error('religion') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Address Information -->
                        <div class="col-md-12">
                            <div class="card border-0 shadow-sm">
                                <div class="card-header bg-white border-0">
                                    <h6 class="mb-0 text-primary">
                                        <i class="mdi mdi-map-marker-outline me-2"></i>Address Information
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="country" class="form-label">Country</label>
                                                <input wire:model="country" type="text" class="form-control @error('country') is-invalid @enderror">
                                                @error('country') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="state" class="form-label">State/Province</label>
                                                <input wire:model="state" type="text" class="form-control @error('state') is-invalid @enderror">
                                                @error('state') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="city" class="form-label">City</label>
                                                <input wire:model="city" type="text" class="form-control @error('city') is-invalid @enderror">
                                                @error('city') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="address" class="form-label">Full Address</label>
                                        <textarea wire:model="address" class="form-control @error('address') is-invalid @enderror" rows="3"></textarea>
                                        @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="cv_file" class="form-label">CV File</label>
                                        <input wire:model.live="cv_file" type="file" class="form-control @error('cv_file') is-invalid @enderror" accept=".pdf,.doc,.docx">
                                        <small class="form-text text-muted">Accepted formats: PDF, DOC, DOCX (Max: 10MB)</small>
                                        
                                        <!-- File upload progress -->
                                        <div wire:loading wire:target="cv_file" class="mt-2">
                                            <div class="progress" style="height: 6px;">
                                                <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 100%"></div>
                                            </div>
                                            <small class="text-muted">Uploading file...</small>
                                        </div>
                                        
                                        <!-- Current file display -->
                                        @if(isset($editingCv) && $editingCv && $editingCv->getFirstMedia('HR_Cvs'))
                                            <div class="mt-2">
                                                <small class="text-muted">Current file:</small>
                                                <div class="d-flex align-items-center mt-1">
                                                    <i class="mdi mdi-file-document me-2"></i>
                                                    <span class="text-primary">{{ $editingCv->getFirstMedia('HR_Cvs')->file_name }}</span>
                                                    <button type="button" wire:click="downloadCv({{ $editingCv->id }})" class="btn btn-sm btn-outline-primary ms-2">
                                                        <i class="mdi mdi-download"></i> Download
                                                    </button>
                                                </div>
                                            </div>
                                        @endif
                                        
                                        <!-- New file selected indicator -->
                                        @if(isset($cv_file) && $cv_file)
                                            <div class="mt-2">
                                                <div class="d-flex align-items-center">
                                                    <i class="mdi mdi-file-document text-success me-2"></i>
                                                    <span class="text-success">{{ $cv_file->getClientOriginalName() }}</span>
                                                    <small class="text-muted ms-2">({{ number_format($cv_file->getSize() / 1024, 2) }} KB)</small>
                                                    <small class="text-warning ms-2">(Will replace current file)</small>
                                                </div>
                                            </div>
                                        @endif
                                        
                                        @error('cv_file') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Professional Information -->
                        <div class="col-12">
                            <div class="card border-0 shadow-sm">
                                <div class="card-header bg-white border-0">
                                    <h6 class="mb-0 text-primary">
                                        <i class="mdi mdi-briefcase-outline me-2"></i>Professional Information
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="summary" class="form-label">Professional Summary</label>
                                        <textarea wire:model.defer="summary" class="form-control @error('summary') is-invalid @enderror" rows="3" placeholder="Brief professional summary..."></textarea>
                                        @error('summary') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="skills" class="form-label">Skills</label>
                                        <textarea wire:model.defer="skills" class="form-control @error('skills') is-invalid @enderror" rows="3" placeholder="Technical and soft skills..."></textarea>
                                        @error('skills') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="experience" class="form-label">Work Experience</label>
                                        <textarea wire:model.defer="experience" class="form-control @error('experience') is-invalid @enderror" rows="4" placeholder="Previous work experience..."></textarea>
                                        @error('experience') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="education" class="form-label">Education</label>
                                        <textarea wire:model.defer="education" class="form-control @error('education') is-invalid @enderror" rows="4" placeholder="Educational background..."></textarea>
                                        @error('education') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="projects" class="form-label">Projects</label>
                                        <textarea wire:model.defer="projects" class="form-control @error('projects') is-invalid @enderror" rows="3" placeholder="Notable projects..."></textarea>
                                        @error('projects') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="certifications" class="form-label">Certifications</label>
                                        <textarea wire:model.defer="certifications" class="form-control @error('certifications') is-invalid @enderror" rows="3" placeholder="Professional certifications..."></textarea>
                                        @error('certifications') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="languages" class="form-label">Languages</label>
                                                <input wire:model.defer="languages" type="text" class="form-control @error('languages') is-invalid @enderror" placeholder="e.g., English, Arabic">
                                                @error('languages') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="portfolio" class="form-label">Portfolio URL</label>
                                                <input wire:model.defer="portfolio" type="url" class="form-control @error('portfolio') is-invalid @enderror" placeholder="https://...">
                                                @error('portfolio') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="interests" class="form-label">Interests</label>
                                        <textarea wire:model.defer="interests" class="form-control @error('interests') is-invalid @enderror" rows="2" placeholder="Personal interests and hobbies..."></textarea>
                                        @error('interests') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="references" class="form-label">References</label>
                                        <textarea wire:model.defer="references" class="form-control @error('references') is-invalid @enderror" rows="3" placeholder="Professional references..."></textarea>
                                        @error('references') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="cover_letter" class="form-label">Cover Letter</label>
                                        <textarea wire:model.defer="cover_letter" class="form-control @error('cover_letter') is-invalid @enderror" rows="4" placeholder="Cover letter content..."></textarea>
                                        @error('cover_letter') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-white border-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                        <i class="mdi mdi-close me-1"></i>Cancel
                    </button>
                    <button type="submit" class="btn btn-warning">
                        <i class="mdi mdi-content-save me-1"></i>
                        <span wire:loading.remove wire:target="update">Update CV</span>
                        <span wire:loading wire:target="update">Updating...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>