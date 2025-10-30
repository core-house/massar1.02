<div class="card h-100 border-primary">
    <div class="card-header bg-light">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h6 class="mb-0">
                    @php
                        $icons = [
                            'client' => 'fa-user-tie text-primary',
                            'main_contractor' => 'fa-hard-hat text-warning',
                            'consultant' => 'fa-user-graduate text-info',
                            'owner' => 'fa-crown text-success',
                            'engineer' => 'fa-user-cog text-secondary',
                        ];
                        $icon = $icons[$roleSlug] ?? 'fa-user';
                    @endphp
                    <i class="fas {{ $icon }} me-2"></i>
                    {{ $roleName }}
                </h6>
                <small class="text-muted">{{ __('Select contacts') }}</small>
            </div>
            <div class="btn-group btn-group-sm">
                <button type="button" wire:click.prevent="openSelectModal" class="btn btn-outline-primary btn-sm">
                    <i class="fas fa-search"></i>
                </button>
                <button type="button" wire:click.prevent="openAddModal" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i>
                </button>
            </div>
        </div>
    </div>

    <div class="card-body p-2" style="min-height: 120px;">
        @if (count($selectedContacts) > 0)
            <div class="selected-contacts-compact">
                @foreach ($selectedContactsData as $contact)
                    <div
                        class="contact-badge mb-1 p-2 border rounded {{ $contact->id == $primaryContactId ? 'border-primary bg-primary bg-opacity-10' : 'bg-light' }}">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="flex-grow-1" style="font-size: 0.85rem;">
                                <div class="fw-bold">
                                    <span
                                        class="badge {{ $contact->type == 'person' ? 'bg-info' : 'bg-success' }} badge-sm">
                                        {{ $contact->type == 'person' ? 'P' : 'O' }}
                                    </span>
                                    {{ Str::limit($contact->name, 20) }}
                                    @if ($contact->id == $primaryContactId)
                                        <span class="badge bg-primary badge-sm">★</span>
                                    @endif
                                </div>
                                @if ($contact->phone)
                                    <small class="text-muted d-block">
                                        <i class="fas fa-phone fa-xs"></i> {{ $contact->phone }}
                                    </small>
                                @endif
                            </div>

                            <div class="btn-group-vertical btn-group-sm ms-2">
                                @if ($contact->id != $primaryContactId)
                                    <button type="button" wire:click="setPrimary({{ $contact->id }})"
                                        class="btn btn-outline-primary btn-sm p-1" title="{{ __('Set as Primary') }}"
                                        style="font-size: 0.7rem;">
                                        <i class="fas fa-star"></i>
                                    </button>
                                @endif
                                <button type="button" wire:click="removeContact({{ $contact->id }})"
                                    class="btn btn-outline-danger btn-sm p-1" title="{{ __('Remove') }}"
                                    style="font-size: 0.7rem;">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center text-muted py-3">
                <i class="fas fa-user-slash fa-2x mb-2 opacity-50"></i>
                <p class="mb-0 small">{{ __('No contacts selected') }}</p>
            </div>
        @endif
    </div>
</div>

<!-- Select Existing Contact Modal -->
@if ($showSelectModal)
    <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('Select Contact') }} - {{ $roleName }}</h5>
                    <button type="button" class="btn-close" wire:click="$set('showSelectModal', false)"></button>
                </div>
                <div class="modal-body">
                    <!-- Search & Filter -->
                    <div class="row mb-3">
                        <div class="col-md-8">
                            <input type="text" wire:model.live.debounce.300ms="searchTerm" class="form-control"
                                placeholder="{{ __('Search by name, email, or phone...') }}">
                        </div>
                        <div class="col-md-4">
                            <select wire:model.live="contactType" class="form-select">
                                <option value="all">{{ __('All Types') }}</option>
                                <option value="person">{{ __('Persons') }}</option>
                                <option value="organization">{{ __('Organizations') }}</option>
                            </select>
                        </div>
                    </div>

                    <!-- Contacts List -->
                    <div class="contacts-list" style="max-height: 400px; overflow-y: auto;">
                        @forelse($availableContacts as $contact)
                            <div
                                class="contact-item p-2 border-bottom {{ in_array($contact->id, $selectedContacts) ? 'bg-light' : '' }}">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="flex-grow-1">
                                        <div class="fw-bold">
                                            {{ $contact->name }}
                                            <span
                                                class="badge {{ $contact->type == 'person' ? 'bg-info' : 'bg-success' }} ms-2">
                                                {{ ucfirst($contact->type) }}
                                            </span>
                                        </div>
                                        <small class="text-muted">
                                            {{ $contact->email ?? $contact->phone }}
                                            @if ($contact->isPerson() && $contact->organizations->isNotEmpty())
                                                | <i class="fas fa-building"></i>
                                                @foreach ($contact->organizations as $org)
                                                    <span class="badge bg-secondary">{{ $org->name }}</span>
                                                @endforeach
                                            @endif
                                        </small>
                                    </div>

                                    @if (in_array($contact->id, $selectedContacts))
                                        <button type="button" class="btn btn-sm btn-success" disabled>
                                            <i class="fas fa-check"></i> Selected
                                        </button>
                                    @else
                                        <button type="button" wire:click="addContact({{ $contact->id }})"
                                            class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-plus"></i> Add
                                        </button>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <p class="text-center text-muted py-4">{{ __('No contacts found') }}</p>
                        @endforelse
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="$set('showSelectModal', false)">
                        {{ __('Close') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
@endif

<!-- Add New Contact Modal -->
@if ($showAddModal)
    <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('Add New Contact') }} - {{ $roleName }}</h5>
                    <button type="button" class="btn-close" wire:click="$set('showAddModal', false)"></button>
                </div>
                <div class="modal-body">
                    <form wire:submit.prevent="saveNewContact">
                        <!-- Type -->
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label class="form-label">{{ __('Contact Type') }} *</label>
                                <div class="btn-group w-100" role="group">
                                    <input type="radio" class="btn-check" wire:model.live="newContact.type"
                                        value="person" id="type_person_{{ $roleSlug }}" autocomplete="off">
                                    <label class="btn btn-outline-primary" for="type_person_{{ $roleSlug }}">
                                        <i class="fas fa-user me-2"></i>{{ __('Person') }}
                                    </label>

                                    <input type="radio" class="btn-check" wire:model.live="newContact.type"
                                        value="organization" id="type_organization_{{ $roleSlug }}"
                                        autocomplete="off">
                                    <label class="btn btn-outline-success"
                                        for="type_organization_{{ $roleSlug }}">
                                        <i class="fas fa-building me-2"></i>{{ __('Organization') }}
                                    </label>
                                </div>
                                @error('newContact.type')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <!-- Name -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">
                                    @if ($newContact['type'] == 'person')
                                        {{ __('Full Name') }} *
                                    @else
                                        {{ __('Organization Name') }} *
                                    @endif
                                </label>
                                <input type="text" wire:model="newContact.name" class="form-control" required>
                                @error('newContact.name')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">{{ __('Email') }}</label>
                                <input type="email" wire:model="newContact.email" class="form-control">
                                @error('newContact.email')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <!-- Phone -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">{{ __('Phone') }} *</label>
                                <input type="tel" wire:model="newContact.phone" class="form-control" required>
                                @error('newContact.phone')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>

                            @if ($newContact['type'] == 'person')
                                <div class="col-md-6">
                                    <label class="form-label">{{ __('Job Title') }}</label>
                                    <input type="text" wire:model="newContact.job_title" class="form-control">
                                </div>
                            @endif
                        </div>

                        <!-- Organization (for person) -->
                        @if ($newContact['type'] == 'person')
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <label class="form-label">{{ __('Organization') }}</label>
                                    <select wire:model="newContact.organization_id" class="form-select">
                                        <option value="">{{ __('Select Organization') }}</option>
                                        @foreach (\Modules\Inquiries\Models\Contact::where('type', 'organization')->orderBy('name')->get() as $org)
                                            <option value="{{ $org->id }}">{{ $org->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        @endif
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="$set('showAddModal', false)">
                        {{ __('Cancel') }}
                    </button>
                    <button type="button" wire:click="saveNewContact" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> {{ __('Save Contact') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
@endif
<script>
    document.addEventListener('livewire:load', () => {
        Livewire.on('closeModal', () => {
            document.querySelectorAll('.modal.show').forEach(modal => {
                modal.classList.remove('show');
                modal.style.display = 'none';
                document.body.classList.remove('modal-open');
            });
            document.querySelectorAll('.modal-backdrop').forEach(backdrop => {
                backdrop.remove();
            });
        });
    });
</script>
