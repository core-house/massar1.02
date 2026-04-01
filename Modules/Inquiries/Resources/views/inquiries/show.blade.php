@extends('admin.dashboard')

@section('sidebar')
    @include('components.sidebar.inquiries')
@endsection

@section('content')
    @include('components.breadcrumb', [
        'title' => __('inquiries::inquiries.inquiry_details'),
        'breadcrumb_items' => [
            ['label' => __('inquiries::inquiries.home'), 'url' => route('admin.dashboard')],
            ['label' => __('inquiries::inquiries.inquiry'), 'url' => route('inquiries.index')],
            ['label' => __('inquiries::inquiries.inquiry_details')],
        ],
    ])

    <div class="container-fluid">
        <!-- Header Actions -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0 text-gray-800">
                    <i class="fas fa-clipboard-list text-primary me-2"></i>
                    {{ __('inquiries::inquiries.inquiries') }} #{{ $inquiry->id }}
                </h1>
                <p class="text-muted mb-0 mt-1">{{ $inquiry->tender_id ?? __('inquiries::inquiries.no_tender_id') }}</p>
            </div>
            <div>
                @if ($inquiry->is_draft)
                    <span class="badge bg-warning text-dark me-2 px-3 py-2">
                        <i class="fas fa-pencil-alt me-1"></i>
                        {{ __('inquiries::inquiries.draft') }}
                    </span>
                @endif
                @can('edit Inquiries')
                    @if ($inquiry->assignedEngineers->contains(auth()->id()) || auth()->user()->can('force_edit_inquiries'))
                        <a href="{{ route('inquiries.edit', $inquiry->id) }}" class="btn btn-primary">
                            <i class="fas fa-edit me-1"></i>{{ __('inquiries::inquiries.edit') }}
                        </a>
                    @endif
                @endcan

                <a href="{{ route('inquiries.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i>
                    {{ __('inquiries::inquiries.back') }}
                </a>
            </div>
        </div>

        <div class="row">
            <!-- Column 1: Project Information -->
            <div class="col-xl-4 col-lg-6 mb-4">
                <!-- Basic Info -->
                <div class="card border-left-primary shadow mb-4">
                    <div class="card-header d-flex align-items-center">
                        <i class="fas fa-info-circle me-2"></i>
                        <h5 class="mb-0">{{ __('inquiries::inquiries.project_information') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <strong class="text-gray-700">{{ __('inquiries::inquiries.project') }}:</strong>
                            <p class="mb-0">{{ $inquiry->project?->name ?? __('inquiries::inquiries.not_specified') }}</p>
                        </div>
                        <div class="mb-3">
                            <strong class="text-gray-700">{{ __('inquiries::inquiries.inquiry_date') }}:</strong>
                            <p class="mb-0">{{ $inquiry->inquiry_date?->format('Y-m-d') ?? __('inquiries::inquiries.not_specified') }}</p>
                        </div>
                        <div class="mb-3">
                            <strong class="text-gray-700">{{ __('inquiries::inquiries.required_submission_date') }}:</strong>
                            <p class="mb-0">{{ $inquiry->req_submittal_date?->format('Y-m-d') ?? __('inquiries::inquiries.not_specified') }}
                            </p>
                        </div>
                        <div class="mb-3">
                            <strong class="text-gray-700">{{ __('inquiries::inquiries.project_start_date') }}:</strong>
                            <p class="mb-0">{{ $inquiry->project_start_date?->format('Y-m-d') ?? __('inquiries::inquiries.not_specified') }}
                            </p>
                        </div>
                        <div class="mb-3">
                            <strong class="text-gray-700">{{ __('inquiries::inquiries.status') }}:</strong>
                            <p class="mb-0">
                                @if ($inquiry->status)
                                    <span
                                        class="badge bg-{{ $inquiry->status->color() }}">{{ $inquiry->status->label() }}</span>
                                @else
                                    {{ __('inquiries::inquiries.not_specified') }}
                                @endif
                            </p>
                        </div>
                        <div class="mb-3">
                            <strong class="text-gray-700">{{ __('Status For KON') }}:</strong>
                            <p class="mb-0">
                                @if ($inquiry->status_for_kon)
                                    <span
                                        class="badge bg-{{ $inquiry->status_for_kon->color() }}">{{ $inquiry->status_for_kon->label() }}</span>
                                @else
                                    {{ __('inquiries::inquiries.not_specified') }}
                                @endif
                            </p>
                        </div>
                        <div class="mb-3">
                            <strong class="text-gray-700">{{ __('inquiries::inquiries.kon_title') }}:</strong>
                            <p class="mb-0">{{ $inquiry->kon_title?->label() ?? __('inquiries::inquiries.not_specified') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Location -->
                <div class="card border-left-info shadow mb-4">
                    <div class="card-header d-flex align-items-center">
                        <i class="fas fa-map-marker-alt me-2"></i>
                        <h5 class="mb-0">{{ __('inquiries::inquiries.location') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <strong class="text-gray-700">{{ __('inquiries::inquiries.distance_from_hq') }}:</strong>
                            <p class="mb-0">
                                {{-- @if ($inquiry->town_distance) --}}
                                <span class="badge bg-info text-white">{{ number_format($inquiry->town_distance, 2) }}
                                    {{ __('inquiries::inquiries.km') }}</span>
                                {{-- @endif --}}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Priority -->
                <div class="card border-left-warning shadow mb-4">
                    <div class="card-header d-flex align-items-center">
                        <i class="fas fa-star me-2"></i>
                        <h5 class="mb-0">{{ __('inquiries::inquiries.priority') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <strong class="text-gray-700">{{ __('inquiries::inquiries.client_priority') }}:</strong>
                            <p class="mb-0">
                                @if ($inquiry->client_priority)
                                    <span class="badge bg-warning text-dark">{{ $inquiry->client_priority }}</span>
                                @else
                                    {{ __('inquiries::inquiries.not_specified') }}
                                @endif
                            </p>
                        </div>
                        <div class="mb-3">
                            <strong class="text-gray-700">{{ __('inquiries::inquiries.kon_priority') }}:</strong>
                            <p class="mb-0">
                                @if ($inquiry->kon_priority)
                                    <span class="badge text-white">{{ $inquiry->kon_priority }}</span>
                                @else
                                    {{ __('inquiries::inquiries.not_specified') }}
                                @endif
                            </p>
                        </div>
                        <div class="mb-3">
                            <strong class="text-gray-700">{{ __('inquiries::inquiries.project_size') }}:</strong>
                            <p class="mb-0">{{ $inquiry->projectSize?->name ?? __('inquiries::inquiries.not_specified') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Estimation -->
                <div class="card border-left-secondary shadow mb-4">
                    <div class="card-header d-flex align-items-center">
                        <i class="fas fa-calculator me-2"></i>
                        <h5 class="mb-0">{{ __('inquiries::inquiries.estimation_details') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <strong class="text-gray-700">{{ __('inquiries::inquiries.tender_number') }}:</strong>
                            <p class="mb-0">{{ $inquiry->tender_number ?? __('inquiries::inquiries.not_specified') }}</p>
                        </div>
                        <div class="mb-3">
                            <strong class="text-gray-700">{{ __('inquiries::inquiries.estimation_start') }}:</strong>
                            <p class="mb-0">
                                {{ $inquiry->estimation_start_date?->format('Y-m-d h:i A') ?? __('inquiries::inquiries.not_specified') }}</p>
                        </div>
                        <div class="mb-3">
                            <strong class="text-gray-700">{{ __('inquiries::inquiries.estimation_end') }}:</strong>
                            <p class="mb-0">
                                {{ $inquiry->estimation_finished_date?->format('Y-m-d h:i A') ?? __('inquiries::inquiries.not_specified') }}</p>
                        </div>
                        <div class="mb-3">
                            <strong class="text-gray-700">{{ __('inquiries::inquiries.submission_date') }}:</strong>
                            <p class="mb-0">
                                {{ $inquiry->submitting_date?->format('Y-m-d h:i A') ?? __('inquiries::inquiries.not_specified') }}</p>
                        </div>
                        <div class="mb-3">
                            <strong class="text-gray-700">{{ __('inquiries::inquiries.total_value') }}:</strong>
                            <p class="mb-0">
                                @if ($inquiry->total_project_value)
                                    <strong class="text-success">{{ number_format($inquiry->total_project_value, 2) }}
                                        {{ __('inquiries::inquiries.sar') }}</strong>
                                @else
                                    {{ __('inquiries::inquiries.not_specified') }}
                                @endif
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Quotation State -->
                <div class="card border-left-danger shadow mb-4">
                    <div class="card-header d-flex align-items-center">
                        <i class="fas fa-file-invoice me-2"></i>
                        <h5 class="mb-0">{{ __('inquiries::inquiries.quotation_state') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <strong class="text-gray-700">{{ __('inquiries::inquiries.status') }}:</strong>
                            <p class="mb-0">
                                @if ($inquiry->pricingStatus)
                                    <span class="badge px-3 py-2"
                                        style="background-color: {{ $inquiry->pricingStatus->color }}">
                                        {{ __($inquiry->pricingStatus->name) }}
                                    </span>
                                    @if ($inquiry->pricing_reason)
                                        <div class="alert alert-info mt-2 small mb-0">
                                            <strong><i
                                                    class="fas fa-info-circle me-1"></i>{{ __('inquiries::inquiries.reason') }}:</strong><br>
                                            {{ $inquiry->pricing_reason }}
                                        </div>
                                    @endif
                                @else
                                    <span class="badge bg-secondary px-3 py-2">{{ __('inquiries::inquiries.not_specified') }}</span>
                                @endif

                            </p>
                        </div>
                        @if ($inquiry->rejection_reason)
                            <div class="alert alert-warning mb-0">
                                <strong>{{ __('inquiries::inquiries.rejection_reason') }}:</strong><br>
                                {{ $inquiry->rejection_reason }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- ======== Work Types & Inquiry Source ======== -->
            <div class="col-xl-4 col-lg-6 mb-4">

                {{-- 1. Work Types --}}
                <div class="card shadow-sm mb-4">
                    <div class="card-header d-flex align-items-center">
                        <i class="fas fa-sitemap me-2"></i>
                        <h5 class="mb-0">{{ __('inquiries::inquiries.selected_works') }}</h5>
                    </div>
                    <div class="card-body">

                        @if (!empty($allWorkTypes) && count($allWorkTypes) > 0)
                            @foreach ($allWorkTypes as $item)
                                <div class="border rounded p-3 mb-3 bg-light">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <h6 class="text-primary mb-0">
                                            <span class="badge bg-primary me-2">#{{ $item['order'] + 1 }}</span>
                                            {{ $item['work_type']->name }}
                                        </h6>
                                    </div>

                                    {{-- Hierarchy Path --}}
                                    @if (!empty($item['hierarchy_path']))
                                        <div class="d-flex flex-wrap gap-1 mb-2">
                                            @foreach ($item['hierarchy_path'] as $path)
                                                <span class="badge bg-info text-white small">{{ $path }}</span>
                                            @endforeach
                                        </div>
                                    @endif

                                    {{-- Description --}}
                                    @if (!empty($item['description']))
                                        <small class="text-muted d-block mb-1">
                                            <i class="fas fa-info-circle me-1"></i>{{ __('inquiries::inquiries.description') }}:
                                        </small>
                                        <p class="mb-0 small text-dark">{{ $item['description'] }}</p>
                                    @endif
                                </div>
                            @endforeach

                            {{-- Final General Description --}}
                            @if ($inquiry->final_work_type)
                                <div class="alert alert-info small p-2 mt-2 mb-0">
                                    <i class="fas fa-edit me-1"></i>
                                    <strong>{{ __('inquiries::inquiries.final_description') }}:</strong>
                                    {{ $inquiry->final_work_type }}
                                </div>
                            @endif

                            {{-- Total Submittal Score --}}
                            <div class="text-end mt-2">
                                <span class="badge bg-success fs-6 px-3 py-2">
                                    {{ __('inquiries::inquiries.total_submittal_score') }}: {{ $inquiry->total_submittal_score ?? 0 }}
                                </span>
                            </div>
                        @elseif($inquiry->workType)
                            {{-- Single main work type (old style) --}}
                            <div class="border rounded p-3 bg-light">
                                <h6 class="text-primary mb-0">
                                    {{ $inquiry->workType->name }}
                                </h6>

                                @if (!empty($workTypePath))
                                    <div class="d-flex flex-wrap gap-1 mt-2">
                                        @foreach ($workTypePath as $path)
                                            <span class="badge bg-info text-white small">{{ $path }}</span>
                                        @endforeach
                                    </div>
                                @endif

                                @if ($inquiry->final_work_type)
                                    <small class="text-muted d-block mt-2">
                                        <i class="fas fa-info-circle me-1"></i>{{ __('inquiries::inquiries.description') }}:
                                    </small>
                                    <p class="mb-0 small">{{ $inquiry->final_work_type }}</p>
                                @endif
                            </div>
                        @else
                            <p class="text-center text-muted py-3">
                                <i class="fas fa-exclamation-triangle fa-2x mb-2 d-block"></i>
                                {{ __('inquiries::inquiries.no_work_types_selected') }}
                            </p>
                        @endif
                    </div>
                </div>

                {{-- Inquiry Source Card --}}
                <div class="card shadow-sm mb-4">
                    <div class="card-header d-flex align-items-center">
                        <i class="fas fa-project-diagram me-2"></i>
                        <h5 class="mb-0">{{ __('inquiries::inquiries.inquiry_source') }}</h5>
                    </div>
                    <div class="card-body">

                        {{-- Hierarchy Path (مثل: Marketing → Email → Client X) --}}
                        @if (!empty($inquirySourcePath))
                            <div class="d-flex flex-wrap gap-1 mb-3">
                                @foreach ($inquirySourcePath as $item)
                                    <span class="badge bg-info text-white small px-2">
                                        {{ $item }}
                                    </span>
                                @endforeach
                            </div>
                        @else
                            <p class="text-muted small mb-2">
                                <i class="fas fa-info-circle me-1"></i>
                                {{ __('inquiries::inquiries.no_hierarchical_inquiry_sources') }}
                            </p>
                        @endif

                        {{-- Final Description --}}
                        @if ($inquiry->final_inquiry_source)
                            <div class="alert alert-light small p-3 mb-0 rounded">
                                <strong class="text-success">
                                    <i class="fas fa-quote-right me-1"></i>
                                    {{ __('inquiries::inquiries.final_description') }}:
                                </strong>
                                <p class="mb-0 mt-1 text-dark">{{ $inquiry->final_inquiry_source }}</p>
                            </div>
                        @else
                            <p class="text-muted text-center small mb-0">
                                <i class="fas fa-ban me-1"></i>
                                {{ __('inquiries::inquiries.no_final_description') }}
                            </p>
                        @endif

                    </div>
                </div>

                <div class="card shadow-sm mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-users-cog me-2"></i>
                            {{ __('inquiries::inquiries.assigned_engineers') }}
                        </h5>
                    </div>
                    <div class="card-body">
                        @if ($inquiry->assignedEngineers && $inquiry->assignedEngineers->count() > 0)
                            <div class="row g-3">
                                @foreach ($inquiry->assignedEngineers as $engineer)
                                    <div class="col-md-4 col-sm-6">
                                        <div class="card h-100 border-0 shadow-sm">
                                            <div class="card-body text-center">
                                                <div class="avatar-circle bg-primary text-white mx-auto mb-3">
                                                    <i class="fas fa-user"></i>
                                                </div>
                                                <h6 class="mb-1">{{ $engineer->name }}</h6>
                                                @if ($engineer->email)
                                                    <small class="text-muted d-block mb-2">
                                                        <i class="fas fa-envelope me-1"></i>
                                                        {{ $engineer->email }}
                                                    </small>
                                                @endif
                                                @if ($engineer->pivot->assigned_at)
                                                    <small class="text-muted d-block">
                                                        <i class="fas fa-calendar-check me-1"></i>
                                                        {{ __('inquiries::inquiries.assigned_at') }}: {{ $engineer->pivot->assigned_at }}
                                                    </small>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="alert alert-info mb-0">
                                <i class="fas fa-info-circle me-2"></i>
                                {{ __('inquiries::inquiries.no_assigned_engineer_to_inquiry') }}
                            </div>
                        @endif
                        @if ($inquiry->assigned_engineer_date)
                            <div class="alert alert-light small mt-3 mb-0 p-2">
                                <i class="fas fa-calendar-check me-1"></i>
                                <strong>{{ __('inquiries::inquiries.engineer_assignment_date') }}:</strong>
                                <strong>{{ \Carbon\Carbon::parse($inquiry->assigned_engineer_date)->format('Y-m-d h:i A') }}</strong>
                            </div>
                        @endif
                    </div>
                </div>

            </div>

            <!-- ======== نهاية القسم ======== -->

            <div class="col-xl-4 col-lg-12 mb-4">
                <!-- Contacts -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header d-flex align-items-center">
                        <i class="fas fa-users me-2"></i>
                        <h5 class="mb-0">{{ __('inquiries::inquiries.contacts') }}</h5>
                    </div>
                    <div class="card-body">
                        @foreach ([
            'client' => ['label' => __('inquiries::inquiries.client'), 'icon' => 'fa-user-tie', 'color' => 'primary'],
            'main_contractor' => ['label' => __('inquiries::inquiries.main_contractor'), 'icon' => 'fa-hard-hat', 'color' => 'success'],
            'consultant' => ['label' => __('inquiries::inquiries.consultant'), 'icon' => 'fa-user-check', 'color' => 'info'],
            'owner' => ['label' => __('inquiries::inquiries.owner'), 'icon' => 'fa-crown', 'color' => 'warning'],
            // 'engineer' => ['label' => __('inquiries::inquiries.engineer'), 'icon' => 'fa-user-cog', 'color' => 'danger'],
        ] as $roleKey => $roleData)
                            <div class="d-flex align-items-center p-3 border-bottom">
                                <div class="flex-shrink-0">
                                    <i class="fas {{ $roleData['icon'] }} fa-lg text-{{ $roleData['color'] }}"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <strong class="small text-gray-800">{{ $roleData['label'] }}</strong>
                                        <span class="badge bg-{{ $roleData['color'] }} text-white small">
                                            {{ $roleData['label'] }}
                                        </span>
                                    </div>

                                    @if (isset($contactsByRole[$roleKey]) && $contactsByRole[$roleKey])
                                        @php $contact = $contactsByRole[$roleKey]; @endphp
                                        <div class="small">
                                            <div class="fw-bold">{{ $contact->name }}</div>
                                            @if ($contact->email)
                                                <div class="text-muted">
                                                    <i class="fas fa-envelope me-1"></i> {{ $contact->email }}
                                                </div>
                                            @endif
                                            @if ($contact->phone_1)
                                                <div class="text-muted">
                                                    <i class="fas fa-phone me-1"></i> {{ $contact->phone_1 }}
                                                </div>
                                            @endif
                                            @if ($contact->type)
                                                <span
                                                    class="badge bg-light text-dark small mt-1">{{ ucfirst($contact->type) }}</span>
                                            @endif
                                        </div>
                                    @else
                                        <div class="text-muted small">
                                            <i class="fas fa-user-slash me-1"></i> {{ __('inquiries::inquiries.not_assigned') }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach


                    </div>
                </div>

                <!-- Quotation Types & Units -->
                @if (!empty($quotationData))
                    <div class="card shadow-sm mb-4">
                        <div class="card-header d-flex align-items-center">
                            <i class="fas fa-list-ul me-2"></i>
                            <h5 class="mb-0">{{ __('inquiries::inquiries.quotation_types_units') }}</h5>
                        </div>
                        <div class="card-body">
                            @foreach ($quotationData as $data)
                                <div class="mb-3">
                                    <h6 class="text-primary small fw-bold mb-2">
                                        <i class="fas fa-tag me-1"></i> {{ $data['type']->name }}
                                    </h6>
                                    <div class="d-flex flex-wrap gap-1">
                                        @foreach ($data['units'] as $unit)
                                            <span class="badge bg-white text-primary border small">
                                                {{ $unit->name }}
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Type Note -->
                @if ($inquiry->type_note)
                    <div class="card shadow-sm mb-4">
                        <div class="card-header d-flex align-items-center">
                            <i class="fas fa-sticky-note me-2"></i>
                            <h5 class="mb-0">{{ __('inquiries::inquiries.type_note') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-light small p-3 mb-0">{{ $inquiry->type_note }}</div>
                        </div>
                    </div>
                @endif

                <!-- Total Score -->
                <div class="card shadow-sm mb-4">
                    <div class="card-body text-center p-4 bg-success text-white">
                        <i class="fas fa-trophy fa-2x mb-3"></i>
                        <h3 class="mb-0 fw-bold">{{ $inquiry->total_check_list_score ?? 0 }}</h3>
                        <p class="mb-0 small">{{ __('inquiries::inquiries.total_score') }}</p>
                    </div>
                </div>

                <!-- Project Difficulty -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header text-white d-flex align-items-center">
                        <i class="fas fa-tachometer-alt me-2"></i>
                        <h5 class="mb-0">{{ __('inquiries::inquiries.project_difficulty') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex rounded overflow-hidden mb-3" style="height: 10px;">
                            <div class="flex-fill {{ $inquiry->project_difficulty >= 1 ? 'bg-success' : 'bg-light' }}">
                            </div>
                            <div class="flex-fill {{ $inquiry->project_difficulty >= 2 ? 'bg-warning' : 'bg-light' }}">
                            </div>
                            <div class="flex-fill {{ $inquiry->project_difficulty >= 3 ? 'bg-danger' : 'bg-light' }}">
                            </div>
                            <div class="flex-fill {{ $inquiry->project_difficulty >= 4 ? 'bg-secondary' : 'bg-light' }}">
                            </div>
                        </div>
                        <div class="text-center">
                            <span
                                class="badge bg-{{ $inquiry->project_difficulty == 1
                                    ? 'success'
                                    : ($inquiry->project_difficulty == 2
                                        ? 'warning'
                                        : ($inquiry->project_difficulty == 3
                                            ? 'danger'
                                            : 'secondary')) }} text-white">
                                @switch($inquiry->project_difficulty)
                                    @case(1)
                                        {{ __('inquiries::inquiries.easy') }}
                                    @break

                                    @case(2)
                                        {{ __('inquiries::inquiries.medium') }}
                                    @break

                                    @case(3)
                                        {{ __('inquiries::inquiries.hard') }}
                                    @break

                                    @default
                                        {{ __('inquiries::inquiries.very_hard') }}
                                @endswitch
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Full Width Sections -->
        <div class="row">
            <!-- Work Conditions -->
            @if ($inquiry->workConditions->isNotEmpty())
                <div class="col-12 mb-4">
                    <div class="card shadow">
                        <div class="card-header d-flex align-items-center">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <h5 class="mb-0">{{ __('Working Conditions') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="row row-cols-1 row-cols-md-3 g-3">
                                @foreach ($inquiry->workConditions as $condition)
                                    <div class="col">
                                        <div class="border rounded p-3 bg-white text-center h-100">
                                            <i class="fas fa-tools text-warning fa-2x mb-2"></i>
                                            <h6 class="mb-1">{{ $condition->name }}</h6>
                                            <span class="badge bg-warning text-dark">{{ __('inquiries::inquiries.score') }}:
                                                {{ $condition->score }}</span>
                                            @if (isset($condition->pivot->selected_option))
                                                <small class="text-muted d-block mt-1">
                                                    {{ __('Selected') }}: {{ $condition->pivot->selected_option }}
                                                </small>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div class="text-end mt-3">
                                <span class="badge bg-warning text-dark fs-6 px-4 py-2">
                                    {{ __('inquiries::inquiries.total_conditions_score') }}: {{ $inquiry->total_check_list_score ?? 0 }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Project Documents -->
            @if ($inquiry->projectDocuments->isNotEmpty())
                <div class="col-12 mb-4">
                    <div class="card shadow">
                        <div class="card-header d-flex align-items-center">
                            <i class="fas fa-file-alt me-2"></i>
                            <h5 class="mb-0">{{ __('inquiries::inquiries.project_documents') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="row row-cols-1 row-cols-md-4 g-3">
                                @foreach ($inquiry->projectDocuments as $document)
                                    <div class="col">
                                        <div class="border rounded p-3 bg-white text-center">
                                            <i class="fas fa-file-pdf text-primary fa-3x mb-2"></i>
                                            <h6 class="mb-1">{{ $document->name }}</h6>
                                            @if ($document->name === 'other' && $document->pivot->description)
                                                <small
                                                    class="text-muted d-block">{{ $document->pivot->description }}</small>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Uploaded Documents -->
            @if ($inquiry->getMedia('inquiry-documents')->isNotEmpty())
                <div class="col-12 mb-4">
                    <div class="card shadow">
                        <div class="card-header bg-info text-white d-flex align-items-center">
                            <i class="fas fa-cloud-upload-alt me-2"></i>
                            <h5 class="mb-0">{{ __('inquiries::inquiries.uploaded_documents') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="row row-cols-1 row-cols-md-4 g-3">
                                @foreach ($inquiry->getMedia('inquiry-documents') as $media)
                                    <div class="col">
                                        <div class="border rounded p-3 bg-white text-center">
                                            <i class="fas fa-file-download text-info fa-3x mb-2"></i>
                                            <h6 class="mb-1">{{ $media->file_name }}</h6>
                                            <small class="text-muted d-block">{{ number_format($media->size / 1024, 2) }}
                                                KB</small>
                                            <a href="{{ $media->getUrl() }}" target="_blank"
                                                class="btn btn-sm btn-primary mt-2">
                                                <i class="fas fa-eye me-1"></i> {{ __('inquiries::inquiries.view') }}
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Project Image -->
            @if ($inquiry->getFirstMedia('project-image'))
                <div class="col-12 mb-4">
                    <div class="card shadow">
                        <div class="card-header bg-secondary text-white d-flex align-items-center">
                            <i class="fas fa-image me-2"></i>
                            <h5 class="mb-0">{{ __('inquiries::inquiries.project_image') }}</h5>
                        </div>
                        <div class="card-body text-center">
                            <img src="{{ $inquiry->getFirstMedia('project-image')->getUrl() }}" alt="Project Image"
                                class="img-fluid rounded shadow" style="max-height: 500px; object-fit: contain;">
                        </div>
                    </div>
                </div>
            @endif

            <!-- Comments -->
            @if ($inquiry->comments->isNotEmpty())
                <div class="col-12 mb-4">
                    <div class="card shadow">
                        <div class="card-header bg-gradient-info text-white d-flex align-items-center">
                            <i class="fas fa-comments me-2"></i>
                            <h5 class="mb-0">{{ __('inquiries::inquiries.comments') }} ({{ $inquiry->comments->count() }})</h5>
                        </div>
                        <div class="card-body">
                            @foreach ($inquiry->comments as $comment)
                                <div class="border-start border-info border-4 ps-3 py-2 mb-3 bg-light rounded">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <strong class="text-primary">
                                            <i class="fas fa-user-circle me-1"></i>
                                            {{ $comment->user?->name ?? __('inquiries::inquiries.unknown_user') }}
                                        </strong>
                                        <small class="text-muted">
                                            <i class="fas fa-clock me-1"></i>
                                            {{ $comment->created_at->format('Y-m-d H:i') }}
                                        </small>
                                    </div>
                                    <p class="mb-0">{{ $comment->comment }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- Timeline Footer -->
            <div class="col-12">
                <div class="card shadow">
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-md-3 mb-3">
                                <i class="fas fa-calendar-plus fa-2x text-primary mb-2"></i>
                                <h6 class="text-muted">{{ __('inquiries::inquiries.created_at') }}</h6>
                                <strong>{{ $inquiry->created_at->format('Y-m-d H:i') }}</strong>
                            </div>
                            <div class="col-md-3 mb-3">
                                <i class="fas fa-calendar-check fa-2x text-success mb-2"></i>
                                <h6 class="text-muted">{{ __('inquiries::inquiries.updated_at') }}</h6>
                                <strong>{{ $inquiry->updated_at->format('Y-m-d H:i') }}</strong>
                            </div>
                            <div class="col-md-3 mb-3">
                                <i class="fas fa-user-plus fa-2x text-info mb-2"></i>
                                <h6 class="text-muted">{{ __('inquiries::inquiries.created_by') }}</h6>
                                <strong>{{ $inquiry->creator?->name ?? __('inquiries::inquiries.system') }}</strong>
                            </div>
                            <div class="col-md-3 mb-3">
                                <i class="fas fa-hashtag fa-2x text-warning mb-2"></i>
                                <h6 class="text-muted">{{ __('inquiries::inquiries.inquiry_id') }}</h6>
                                <strong>#{{ $inquiry->id }}</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const scoreElement = document.querySelector('.score-display h2');
            if (scoreElement) {
                const finalScore = parseInt(scoreElement.textContent);
                let current = 0;
                const increment = Math.max(1, Math.ceil(finalScore / 50));
                const timer = setInterval(() => {
                    current += increment;
                    if (current >= finalScore) {
                        current = finalScore;
                        clearInterval(timer);
                    }
                    scoreElement.textContent = current;
                }, 20);
            }
        });
    </script>
@endpush
