<div class="row mb-4">
    <div class="col-12">
        <div class="card border-dark">
            <div class="card-header">
                <h6 class="card-title mb-0">
                    <i class="fas fa-users me-2"></i>
                    {{ __('Stakeholders') }}
                </h6>
                <small class="d-block mt-1">{{ __('Identify all parties involved in the project') }}</small>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- العميل (Client) -->
                    <div class="col-md-3 mb-3 d-flex flex-column">
                        <div class="card-body text-center">
                            <div class="mb-3">
                                <i class="fas fa-user-tie fa-2x text-primary"></i>
                            </div>
                            <label class="form-label fw-bold">{{ __('Client') }}</label>
                            <div class="d-flex gap-2 align-items-center">
                                <div class="flex-grow-1">
                                    <livewire:app::searchable-select :model="Modules\Inquiries\Models\Contact::class" label-field="name"
                                        wire-model="selectedContacts.client"
                                        placeholder="{{ __('Search for client...') }}" :selected-id="$selectedContacts['client']"
                                        :key="'client-select-' . ($selectedContacts['client'] ?? 'new')" />
                                </div>
                                <button type="button" class="btn btn-sm btn-primary" wire:click="openContactModal(1)"
                                    title="{{ __('Add New Client') }}">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                            @if ($selectedContacts['client'])
                                @php
                                    $contact = collect($contacts)->firstWhere('id', $selectedContacts['client']);
                                @endphp
                                @if ($contact)
                                    <div class="card mt-3 bg-light">
                                        <div class="card-body p-2 text-start">
                                            <small class="d-block">
                                                <strong>{{ __('Name') }}:</strong> {{ $contact['name'] }}
                                            </small>
                                            <small class="d-block">
                                                <strong>{{ __('Type') }}:</strong>
                                                {{ $contact['type'] === 'company' ? __('Company') : __('Person') }}
                                            </small>
                                            @if ($contact['phone_1'])
                                                <small class="d-block">
                                                    <strong>{{ __('Phone') }}:</strong> {{ $contact['phone_1'] }}
                                                </small>
                                            @endif
                                            @if ($contact['email'])
                                                <small class="d-block">
                                                    <strong>{{ __('Email') }}:</strong> {{ $contact['email'] }}
                                                </small>
                                            @endif
                                            @if ($contact['address_1'])
                                                <small class="d-block">
                                                    <strong>{{ __('Address') }}:</strong> {{ $contact['address_1'] }}
                                                </small>
                                            @endif
                                            @if (!empty($contact['parent_id']))
                                                @php
                                                    $parent = collect($contacts)->firstWhere(
                                                        'id',
                                                        $contact['parent_id'],
                                                    );
                                                @endphp
                                                @if ($parent)
                                                    <small class="d-block">
                                                        <strong>{{ __('Parent Company') }}:</strong>
                                                        {{ $parent['name'] }}
                                                    </small>
                                                @endif
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            @endif
                        </div>
                    </div>

                    <!-- المقاول الرئيسي (Main Contractor) -->
                    <div class="col-md-3 mb-3 d-flex flex-column">
                        <div class="card-body text-center">
                            <div class="mb-3">
                                <i class="fas fa-hard-hat fa-2x text-warning"></i>
                            </div>
                            <label class="form-label fw-bold">{{ __('Main Contractor') }}</label>
                            <div class="d-flex gap-2 align-items-center">
                                <div class="flex-grow-1">
                                    <livewire:app::searchable-select :model="Modules\Inquiries\Models\Contact::class" label-field="name"
                                        wire-model="selectedContacts.main_contractor"
                                        placeholder="{{ __('Search for contractor...') }}" :selected-id="$selectedContacts['main_contractor']"
                                        :key="'contractor-select-' .
                                            ($selectedContacts['main_contractor'] ?? 'new')" />
                                </div>
                                <button type="button" class="btn btn-sm btn-warning" wire:click="openContactModal(2)"
                                    title="{{ __('Add New Contractor') }}">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                            @if ($selectedContacts['main_contractor'])
                                @php
                                    $contact = collect($contacts)->firstWhere(
                                        'id',
                                        $selectedContacts['main_contractor'],
                                    );
                                @endphp
                                @if ($contact)
                                    <div class="card mt-3 bg-light">
                                        <div class="card-body p-2 text-start">
                                            <small class="d-block">
                                                <strong>{{ __('Name') }}:</strong> {{ $contact['name'] }}
                                            </small>
                                            <small class="d-block">
                                                <strong>{{ __('Type') }}:</strong>
                                                {{ $contact['type'] === 'company' ? __('Company') : __('Person') }}
                                            </small>
                                            @if ($contact['phone_1'])
                                                <small class="d-block">
                                                    <strong>{{ __('Phone') }}:</strong> {{ $contact['phone_1'] }}
                                                </small>
                                            @endif
                                            @if ($contact['email'])
                                                <small class="d-block">
                                                    <strong>{{ __('Email') }}:</strong> {{ $contact['email'] }}
                                                </small>
                                            @endif
                                            @if ($contact['address_1'])
                                                <small class="d-block">
                                                    <strong>{{ __('Address') }}:</strong> {{ $contact['address_1'] }}
                                                </small>
                                            @endif
                                            @if (!empty($contact['parent_id']))
                                                @php
                                                    $parent = collect($contacts)->firstWhere(
                                                        'id',
                                                        $contact['parent_id'],
                                                    );
                                                @endphp
                                                @if ($parent)
                                                    <small class="d-block">
                                                        <strong>{{ __('Parent Company') }}:</strong>
                                                        {{ $parent['name'] }}
                                                    </small>
                                                @endif
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            @endif
                        </div>
                    </div>

                    <!-- الاستشاري (Consultant) -->
                    <div class="col-md-3 mb-3 d-flex flex-column">
                        <div class="card-body text-center">
                            <div class="mb-3">
                                <i class="fas fa-user-graduate fa-2x text-info"></i>
                            </div>
                            <label class="form-label fw-bold">{{ __('Consultant') }}</label>
                            <div class="d-flex gap-2 align-items-center">
                                <div class="flex-grow-1">
                                    <livewire:app::searchable-select :model="Modules\Inquiries\Models\Contact::class" label-field="name"
                                        wire-model="selectedContacts.consultant"
                                        placeholder="{{ __('Search for consultant...') }}" :selected-id="$selectedContacts['consultant']"
                                        :key="'consultant-select-' . ($selectedContacts['consultant'] ?? 'new')" />
                                </div>
                                <button type="button" class="btn btn-sm btn-info" wire:click="openContactModal(3)"
                                    title="{{ __('Add New Consultant') }}">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                            @if ($selectedContacts['consultant'])
                                @php
                                    $contact = collect($contacts)->firstWhere('id', $selectedContacts['consultant']);
                                @endphp
                                @if ($contact)
                                    <div class="card mt-3 bg-light">
                                        <div class="card-body p-2 text-start">
                                            <small class="d-block">
                                                <strong>{{ __('Name') }}:</strong> {{ $contact['name'] }}
                                            </small>
                                            <small class="d-block">
                                                <strong>{{ __('Type') }}:</strong>
                                                {{ $contact['type'] === 'company' ? __('Company') : __('Person') }}
                                            </small>
                                            @if ($contact['phone_1'])
                                                <small class="d-block">
                                                    <strong>{{ __('Phone') }}:</strong> {{ $contact['phone_1'] }}
                                                </small>
                                            @endif
                                            @if ($contact['email'])
                                                <small class="d-block">
                                                    <strong>{{ __('Email') }}:</strong> {{ $contact['email'] }}
                                                </small>
                                            @endif
                                            @if ($contact['address_1'])
                                                <small class="d-block">
                                                    <strong>{{ __('Address') }}:</strong> {{ $contact['address_1'] }}
                                                </small>
                                            @endif
                                            @if (!empty($contact['parent_id']))
                                                @php
                                                    $parent = collect($contacts)->firstWhere(
                                                        'id',
                                                        $contact['parent_id'],
                                                    );
                                                @endphp
                                                @if ($parent)
                                                    <small class="d-block">
                                                        <strong>{{ __('Parent Company') }}:</strong>
                                                        {{ $parent['name'] }}
                                                    </small>
                                                @endif
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            @endif
                        </div>
                    </div>

                    <!-- المالك (Owner) -->
                    <div class="col-md-3 mb-3 d-flex flex-column">
                        <div class="card-body text-center">
                            <div class="mb-3">
                                <i class="fas fa-crown fa-2x text-success"></i>
                            </div>
                            <label class="form-label fw-bold">{{ __('Owner') }}</label>
                            <div class="d-flex gap-2 align-items-center">
                                <div class="flex-grow-1">
                                    <livewire:app::searchable-select :model="Modules\Inquiries\Models\Contact::class" label-field="name"
                                        wire-model="selectedContacts.owner"
                                        placeholder="{{ __('Search for owner...') }}" :selected-id="$selectedContacts['owner']"
                                        :key="'owner-select-' . ($selectedContacts['owner'] ?? 'new')" />
                                </div>
                                <button type="button" class="btn btn-sm btn-success" wire:click="openContactModal(4)"
                                    title="{{ __('Add New Owner') }}">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                            @if ($selectedContacts['owner'])
                                @php
                                    $contact = collect($contacts)->firstWhere('id', $selectedContacts['owner']);
                                @endphp
                                @if ($contact)
                                    <div class="card mt-3 bg-light">
                                        <div class="card-body p-2 text-start">
                                            <small class="d-block">
                                                <strong>{{ __('Name') }}:</strong> {{ $contact['name'] }}
                                            </small>
                                            <small class="d-block">
                                                <strong>{{ __('Type') }}:</strong>
                                                {{ $contact['type'] === 'company' ? __('Company') : __('Person') }}
                                            </small>
                                            @if ($contact['phone_1'])
                                                <small class="d-block">
                                                    <strong>{{ __('Phone') }}:</strong> {{ $contact['phone_1'] }}
                                                </small>
                                            @endif
                                            @if ($contact['email'])
                                                <small class="d-block">
                                                    <strong>{{ __('Email') }}:</strong> {{ $contact['email'] }}
                                                </small>
                                            @endif
                                            @if ($contact['address_1'])
                                                <small class="d-block">
                                                    <strong>{{ __('Address') }}:</strong> {{ $contact['address_1'] }}
                                                </small>
                                            @endif
                                            @if (!empty($contact['parent_id']))
                                                @php
                                                    $parent = collect($contacts)->firstWhere(
                                                        'id',
                                                        $contact['parent_id'],
                                                    );
                                                @endphp
                                                @if ($parent)
                                                    <small class="d-block">
                                                        <strong>{{ __('Parent Company') }}:</strong>
                                                        {{ $parent['name'] }}
                                                    </small>
                                                @endif
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@include('inquiries::components.addContactModal')
