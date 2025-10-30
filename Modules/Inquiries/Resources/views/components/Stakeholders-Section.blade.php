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

                    @php
                        $roles = [
                            'client' => ['title' => __('Client'), 'icon' => 'fa-user-tie text-primary'],
                            'main_contractor' => [
                                'title' => __('Main Contractor'),
                                'icon' => 'fa-hard-hat text-warning',
                            ],
                            'consultant' => ['title' => __('Consultant'), 'icon' => 'fa-user-graduate text-info'],
                            'owner' => ['title' => __('Owner'), 'icon' => 'fa-crown text-success'],
                            'engineer' => ['title' => __('Engineer'), 'icon' => 'fa-user-cog text-secondary'],
                        ];
                    @endphp

                    @foreach ($roles as $slug => $data)
                        <div class="col-md-{{ count($roles) == 5 ? '2' : '3' }} mb-3">
                            <label class="form-label fw-bold">
                                <i class="fas {{ $data['icon'] }} me-1"></i> {{ $data['title'] }}
                            </label>

                            <!-- Contact Selector -->
                            <livewire:inquiries::contact-selector :inquiry="$inquiry" :roleSlug="$slug"
                                :key="$slug . '-selector'" />

                            <!-- عرض الـ Primary Contact (إن وجد) -->
                            @if (!empty($contactSelectors[$slug]['primary']))
                                @php
                                    $contactId = $contactSelectors[$slug]['primary'];
                                    $contact = \Modules\Inquiries\Models\Contact::find($contactId);
                                @endphp
                                @if ($contact)
                                    <div class="card mt-2 bg-light">
                                        <div class="card-body p-2 text-start small">
                                            <strong>{{ Str::limit($contact->name, 25) }}</strong>
                                            @if ($contact->phone)
                                                <br><small><i class="fas fa-phone"></i> {{ $contact->phone }}</small>
                                            @endif
                                            @if ($contact->email)
                                                <br><small><i class="fas fa-envelope"></i> {{ $contact->email }}</small>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            @endif
                        </div>
                    @endforeach

                </div>
            </div>
        </div>
    </div>
</div>

