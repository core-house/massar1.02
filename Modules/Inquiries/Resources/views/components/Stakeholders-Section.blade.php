<div class="row mb-4">
    <div class="col-12">
        <div class="card border-dark">
            <div class="card-header">
                <h6 class="card-title mb-0">
                    <i class="fas fa-users me-2"></i>
                    {{ __('inquiries::inquiries.stakeholders') }}
                </h6>
                <small class="d-block mt-1">{{ __('inquiries::inquiries.identify_parties') }}</small>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- العميل (Client) -->
                    <div class="col-md-3 mb-3 d-flex flex-column">
                        <div class="card-body text-center">
                            <div class="mb-3">
                                <i class="fas fa-user-tie fa-2x text-primary"></i>
                            </div>
                            <label class="form-label fw-bold">{{ __('inquiries::inquiries.client') }}</label>
                            <div class="d-flex gap-2 align-items-center">
                                <div class="flex-grow-1">
                                    <livewire:app::searchable-select :model="Modules\Inquiries\Models\Contact::class" label-field="name"
                                        wire-model="selectedContacts.client"
                                        placeholder="{{ __('inquiries::inquiries.search_for_client') }}" :selected-id="$selectedContacts['client']"
                                        :key="'client-select-' . ($selectedContacts['client'] ?? 'new')" />
                                </div>
                                <button type="button" class="btn btn-sm btn-primary" wire:click="openContactModal(1)"
                                    title="{{ __('inquiries::inquiries.add_new_client') }}">
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
                                                <strong>{{ __('inquiries::inquiries.name') }}:</strong> {{ $contact['name'] }}
                                            </small>
                                            <small class="d-block">
                                                <strong>{{ __('inquiries::inquiries.contact_type') }}:</strong>
                                                {{ $contact['type'] === 'company' ? __('inquiries::inquiries.company') : __('inquiries::inquiries.person') }}
                                            </small>
                                            @if ($contact['phone_1'])
                                                <small class="d-block">
                                                    <strong>{{ __('inquiries::inquiries.phone') }}:</strong> {{ $contact['phone_1'] }}
                                                </small>
                                            @endif
                                            @if ($contact['email'])
                                                <small class="d-block">
                                                    <strong>{{ __('inquiries::inquiries.email') }}:</strong> {{ $contact['email'] }}
                                                </small>
                                            @endif
                                            @if ($contact['address_1'])
                                                <small class="d-block">
                                                    <strong>{{ __('inquiries::inquiries.address') }}:</strong> {{ $contact['address_1'] }}
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
                                                        <strong>{{ __('inquiries::inquiries.parent_company') }}:</strong>
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
                            <label class="form-label fw-bold">{{ __('inquiries::inquiries.main_contractor') }}</label>
                            <div class="d-flex gap-2 align-items-center">
                                <div class="flex-grow-1">
                                    <livewire:app::searchable-select :model="Modules\Inquiries\Models\Contact::class" label-field="name"
                                        wire-model="selectedContacts.main_contractor"
                                        placeholder="{{ __('inquiries::inquiries.search_for_contractor') }}" :selected-id="$selectedContacts['main_contractor']"
                                        :key="'contractor-select-' .
                                            ($selectedContacts['main_contractor'] ?? 'new')" />
                                </div>
                                <button type="button" class="btn btn-sm btn-warning" wire:click="openContactModal(2)"
                                    title="{{ __('inquiries::inquiries.add_new_contractor') }}">
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
                                                <strong>{{ __('inquiries::inquiries.name') }}:</strong> {{ $contact['name'] }}
                                            </small>
                                            <small class="d-block">
                                                <strong>{{ __('inquiries::inquiries.contact_type') }}:</strong>
                                                {{ $contact['type'] === 'company' ? __('inquiries::inquiries.company') : __('inquiries::inquiries.person') }}
                                            </small>
                                            @if ($contact['phone_1'])
                                                <small class="d-block">
                                                    <strong>{{ __('inquiries::inquiries.phone') }}:</strong> {{ $contact['phone_1'] }}
                                                </small>
                                            @endif
                                            @if ($contact['email'])
                                                <small class="d-block">
                                                    <strong>{{ __('inquiries::inquiries.email') }}:</strong> {{ $contact['email'] }}
                                                </small>
                                            @endif
                                            @if ($contact['address_1'])
                                                <small class="d-block">
                                                    <strong>{{ __('inquiries::inquiries.address') }}:</strong> {{ $contact['address_1'] }}
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
                                                        <strong>{{ __('inquiries::inquiries.parent_company') }}:</strong>
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
                            <label class="form-label fw-bold">{{ __('inquiries::inquiries.consultant') }}</label>
                            <div class="d-flex gap-2 align-items-center">
                                <div class="flex-grow-1">
                                    <livewire:app::searchable-select :model="Modules\Inquiries\Models\Contact::class" label-field="name"
                                        wire-model="selectedContacts.consultant"
                                        placeholder="{{ __('inquiries::inquiries.search_for_consultant') }}" :selected-id="$selectedContacts['consultant']"
                                        :key="'consultant-select-' . ($selectedContacts['consultant'] ?? 'new')" />
                                </div>
                                <button type="button" class="btn btn-sm btn-info" wire:click="openContactModal(3)"
                                    title="{{ __('inquiries::inquiries.add_new_consultant') }}">
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
                                                <strong>{{ __('inquiries::inquiries.name') }}:</strong> {{ $contact['name'] }}
                                            </small>
                                            <small class="d-block">
                                                <strong>{{ __('inquiries::inquiries.contact_type') }}:</strong>
                                                {{ $contact['type'] === 'company' ? __('inquiries::inquiries.company') : __('inquiries::inquiries.person') }}
                                            </small>
                                            @if ($contact['phone_1'])
                                                <small class="d-block">
                                                    <strong>{{ __('inquiries::inquiries.phone') }}:</strong> {{ $contact['phone_1'] }}
                                                </small>
                                            @endif
                                            @if ($contact['email'])
                                                <small class="d-block">
                                                    <strong>{{ __('inquiries::inquiries.email') }}:</strong> {{ $contact['email'] }}
                                                </small>
                                            @endif
                                            @if ($contact['address_1'])
                                                <small class="d-block">
                                                    <strong>{{ __('inquiries::inquiries.address') }}:</strong> {{ $contact['address_1'] }}
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
                                                        <strong>{{ __('inquiries::inquiries.parent_company') }}:</strong>
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
                            <label class="form-label fw-bold">{{ __('inquiries::inquiries.owner') }}</label>
                            <div class="d-flex gap-2 align-items-center">
                                <div class="flex-grow-1">
                                    <livewire:app::searchable-select :model="Modules\Inquiries\Models\Contact::class" label-field="name"
                                        wire-model="selectedContacts.owner"
                                        placeholder="{{ __('inquiries::inquiries.search_for_owner') }}" :selected-id="$selectedContacts['owner']"
                                        :key="'owner-select-' . ($selectedContacts['owner'] ?? 'new')" />
                                </div>
                                <button type="button" class="btn btn-sm btn-success" wire:click="openContactModal(4)"
                                    title="{{ __('inquiries::inquiries.add_new_owner') }}">
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
                                                <strong>{{ __('inquiries::inquiries.name') }}:</strong> {{ $contact['name'] }}
                                            </small>
                                            <small class="d-block">
                                                <strong>{{ __('inquiries::inquiries.contact_type') }}:</strong>
                                                {{ $contact['type'] === 'company' ? __('inquiries::inquiries.company') : __('inquiries::inquiries.person') }}
                                            </small>
                                            @if ($contact['phone_1'])
                                                <small class="d-block">
                                                    <strong>{{ __('inquiries::inquiries.phone') }}:</strong> {{ $contact['phone_1'] }}
                                                </small>
                                            @endif
                                            @if ($contact['email'])
                                                <small class="d-block">
                                                    <strong>{{ __('inquiries::inquiries.email') }}:</strong> {{ $contact['email'] }}
                                                </small>
                                            @endif
                                            @if ($contact['address_1'])
                                                <small class="d-block">
                                                    <strong>{{ __('inquiries::inquiries.address') }}:</strong> {{ $contact['address_1'] }}
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
                                                        <strong>{{ __('inquiries::inquiries.parent_company') }}:</strong>
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
