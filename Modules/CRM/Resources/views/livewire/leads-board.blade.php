<div>
    @push('styles')
        <link href="{{ asset('assets/css/custom-css/leads.css') }}" rel="stylesheet" />
    @endpush

    @if (session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Filters Section -->
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-header bg-white border-0">
            <div class="d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold">
                    <i class="fas fa-filter me-2"></i>
                    {{ __('crm::crm.filters') }}
                </h6>
            </div>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <!-- Search -->
                <div class="col-md-1">
                    <label class="form-label">{{ __('crm::crm.search') }}</label>
                    <input type="text" wire:model.live.debounce.500ms="search" class="form-control"
                        placeholder="{{ __('crm::crm.search_for_client') }}">
                </div>

                <!-- Status Filter -->
                <div class="col-md-1">
                    <label class="form-label">{{ __('crm::crm.status') }}</label>
                    <select wire:model.live="filterStatus" class="form-select">
                        <option value="all">{{ __('crm::crm.all_statuses') }}</option>
                        @foreach ($statuses as $status)
                            <option value="{{ $status->id }}">{{ $status->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Source Filter -->
                <div class="col-md-1">
                    <label class="form-label">{{ __('crm::crm.lead_sources') }}</label>
                    <select wire:model.live="filterSource" class="form-select">
                        <option value="all">{{ __('crm::crm.all') }}</option>
                        @foreach ($sources as $source)
                            <option value="{{ $source->id }}">{{ $source->title }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Client Filter -->
                <div class="col-md-1">
                    <label class="form-label">{{ __('crm::crm.client') }}</label>
                    <select wire:model.live="filterClient" class="form-select">
                        <option value="all">{{ __('crm::crm.all_clients') }}</option>
                        @foreach ($clients as $client)
                            <option value="{{ $client['id'] }}">{{ $client['cname'] }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Assigned To Filter -->
                <div class="col-md-1">
                    <label class="form-label">{{ __('crm::crm.assigned_to') }}</label>
                    <select wire:model.live="filterAssignedTo" class="form-select">
                        <option value="all">{{ __('crm::crm.all_users') }}</option>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Date From -->
                <div class="col-md-1">
                    <label class="form-label">{{ __('crm::crm.date_from') }}</label>
                    <input type="date" wire:model.live="filterDateFrom" class="form-control">
                </div>

                <!-- Date To -->
                <div class="col-md-1">
                    <label class="form-label">{{ __('crm::crm.date_to') }}</label>
                    <input type="date" wire:model.live="filterDateTo" class="form-control">
                </div>

                <button wire:click="resetFilters" class="btn btn-secondary col-md-1 my-5">
                    <i class="fas fa-redo me-2"></i>
                    {{ __('crm::crm.reset') }}
                </button>

            </div>
        </div>
    </div>

    <div class="leads-board d-flex flex-row flex-nowrap" id="leads-board"
        style="max-height: 80vh; overflow-x: auto; overflow-y: hidden; white-space: nowrap; scrollbar-width: thin; position: relative;">

        @foreach ($statuses as $status)
            <div class="status-column" data-status-id="{{ $status->id }}" wire:key="status-{{ $status->id }}"
                style="width: 300px; flex: 0 0 auto; border-bottom-color: {{ $status->color }};
               max-height: 76vh; display: flex; flex-direction: column; margin-right: 15px;">

                <div class="status-header" style="border-color: {{ $status->color }}">
                    <div class="status-title" style="color: {{ $status->color }}">
                        {{ $status->name }}
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="leads-count">
                            {{ !empty($leads[$status->id]) ? number_format(collect($leads[$status->id])->sum('amount')) : '0.00' }}
                            {{ __('crm::crm.egp') }}
                        </span>

                        <!-- Report button -->
                        <button class="btn btn-sm btn-outline-info" wire:click="openStatusReport({{ $status->id }})"
                            title="{{ __('crm::crm.stage_report') }}">
                            <i class="fas fa-chart-bar"></i>
                        </button>

                        {{-- @can('Add Opportunities') --}}
                        <button class="btn btn-sm btn-outline-primary" wire:click="openAddModal({{ $status->id }})">
                            <i class="fas fa-plus"></i>
                        </button>
                        {{-- @endcan --}}
                    </div>
                </div>

                <div class="leads-container" data-status-id="{{ $status->id }}"
                    style="overflow-y: auto; flex: 1 1 0; min-height: 200px;">
                    @if (!empty($leads[$status->id]))
                        @foreach ($leads[$status->id] as $lead)
                            <div class="lead-card" draggable="true" data-lead-id="{{ $lead['id'] }}"
                                wire:key="lead-{{ $lead['id'] }}" style="border-right-color: {{ $status->color }}">
                                <div class="lead-title">{{ $lead['title'] }}</div>
                                <div class="lead-info">
                                    <i class="fas fa-user"></i> {{ $lead['client']['cname'] ?? __('crm::crm.undefined') }}
                                </div>
                                @if ($lead['amount'])
                                    <div class="lead-amount">
                                        <i class="fas fa-money-bill"></i> {{ number_format($lead['amount']) }}
                                        {{ __('crm::crm.egp') }}
                                    </div>
                                @endif
                                @if ($lead['assigned_to'])
                                    <div class="lead-info">
                                        <i class="fas fa-user-tie"></i> {{ $lead['assigned_to']['name'] }}
                                    </div>
                                @endif
                                <div class="lead-actions d-flex align-items-center gap-2">
                                    <button class="btn btn-info btn-sm d-flex align-items-center justify-content-center"
                                        style="width: 32px; height: 32px;" wire:click="showLead({{ $lead['id'] }})"
                                        title="{{ __('crm::crm.view_details') }}">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button
                                        class="btn btn-success btn-sm d-flex align-items-center justify-content-center"
                                        style="width: 32px; height: 32px;" wire:click="editLead({{ $lead['id'] }})"
                                        title="{{ __('crm::crm.edit') }}">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    {{-- @can('Delete Opportunities') --}}
                                    <button
                                        class="btn btn-danger btn-sm d-flex align-items-center justify-content-center"
                                        style="width: 32px; height: 32px;"
                                        wire:click="deleteLead({{ $lead['id'] }})" title="{{ __('crm::crm.delete') }}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    {{-- @endcan --}}
                                </div>

                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    {{-- نافذة إضافة فرصة جديدة --}}
    @if ($showAddModal)
        <div class="modal-overlay" wire:click.self="closeModal">
            <div class="modal-content">
                <h4 class="mb-4">{{ __('crm::crm.add_new_lead') }}</h4>

                <!-- عرض جميع الأخطاء في أعلى النموذج -->
                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <h6 class="alert-heading mb-2"><i
                                class="fas fa-exclamation-triangle me-2"></i>{{ __('crm::crm.please_fix_the_following_errors') }}
                        </h6>
                        <ul class="mb-0 ps-3">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"
                            aria-label="Close"></button>
                    </div>
                @endif

                <form wire:submit.prevent="addLead">
                    <div class="form-group">
                        <label class="form-label">{{ __('crm::crm.lead_title') }} *</label>
                        <input type="text" class="form-control @error('newLead.title') is-invalid @enderror"
                            wire:model="newLead.title" required>
                        @error('newLead.title')
                            <div class="invalid-feedback d-block">
                                <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">{{ __('crm::crm.client') }} *</label>

                        <div class="client-search-container position-relative">
                            <!-- Search Field -->
                            <div class="input-group">
                                <input type="text" class="form-control" wire:model.live="clientSearch"
                                    placeholder="{{ __('crm::crm.search_for_client_or_type_new_name') }}"
                                    autocomplete="off">

                                @if ($clientSearch && $newLead['client_id'])
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-outline-secondary"
                                            wire:click="clearClientSearch" title="{{ __('crm::crm.clear') }}">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                @endif
                            </div>

                            <!-- قائمة نتائج البحث -->
                            @if ($showClientDropdown && $clientSearch)
                                <div class="client-dropdown">
                                    @if (count($filteredClients) > 0)
                                        <!-- العEGPلاء الEGPوEGPودين -->
                                        @foreach ($filteredClients as $client)
                                            <div class="dropdown-item client-item"
                                                wire:click="selectClient({{ $client['id'] }}, '{{ $client['cname'] }}')">
                                                <i class="fas fa-user text-muted"></i>
                                                <span>{{ $client['cname'] }}</span>
                                            </div>
                                        @endforeach
                                    @endif

                                    <!-- زر إنشاء عميل جديد -->
                                    @if ($clientSearch && !collect($filteredClients)->contains('cname', $clientSearch))
                                        <div class="dropdown-item create-client-item"
                                            wire:click="createClientFromSearch">
                                            <i class="fas fa-plus text-success"></i>
                                            <span>{{ __('crm::crm.create_new_client') }}
                                                "<strong>{{ $clientSearch }}</strong>"</span>
                                        </div>
                                    @endif

                                    <!-- رسالة عدم وجود نتائج -->
                                    @if (count($filteredClients) === 0 && collect($filteredClients)->contains('cname', $clientSearch))
                                        <div class="dropdown-item no-results">
                                            <i class="fas fa-search text-muted"></i>
                                            <span>{{ __('crm::crm.no_results_found') }}</span>
                                        </div>
                                    @endif
                                </div>
                            @endif

                            <!-- مؤشر العميل المحدد -->
                            @if ($newLead['client_id'] && $selectedClientText)
                                <small class="text-success mt-1 d-block">
                                    <i class="fas fa-check-circle"></i>
                                    {{ __('crm::crm.selected') }} {{ $selectedClientText }}
                                </small>
                            @endif
                        </div>

                        @error('newLead.client_id')
                            <div class="invalid-feedback d-block">
                                <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                            </div>
                        @enderror
                        @error('clientSearch')
                            <div class="invalid-feedback d-block">
                                <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">{{ __('crm::crm.expected_value') }}</label>
                        <input type="number" step="0.01"
                            class="form-control @error('newLead.amount') is-invalid @enderror"
                            wire:model="newLead.amount" placeholder="0.00">
                        @error('newLead.amount')
                            <div class="invalid-feedback d-block">
                                <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">{{ __('crm::crm.source') }}</label>
                        <select class="form-control @error('newLead.source') is-invalid @enderror"
                            wire:model="newLead.source">
                            <option value="">{{ __('crm::crm.select_lead_source') }}</option>
                            @foreach ($sources as $source)
                                <option value="{{ $source->id }}">{{ $source->title }}</option>
                            @endforeach
                        </select>
                        @error('newLead.source')
                            <div class="invalid-feedback d-block">
                                <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">{{ __('crm::crm.follow_up_manager') }}</label>
                        <select class="form-control" wire:model="newLead.assigned_to">
                            <option value="">{{ __('crm::crm.select_manager') }}</option>
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">{{ __('crm::crm.lead_description') }}</label>
                        <textarea class="form-control" wire:model="newLead.description" rows="3"
                            placeholder="{{ __('crm::crm.additional_details_about_lead') }}"></textarea>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-main">
                            <i class="fas fa-save"></i> {{ __('crm::crm.save_lead') }}
                        </button>
                        <button type="button" class="btn btn-secondary" wire:click="closeModal">
                            <i class="fas fa-times"></i> {{ __('crm::crm.cancel') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- Edit Opportunity Window --}}
    @if ($showEditModal)
        <div class="modal-overlay" wire:click.self="closeModal">
            <div class="modal-content">
                <h4 class="mb-4">{{ __('crm::crm.edit_lead') }}</h4>

                <form wire:submit.prevent="updateLead">
                    <div class="form-group">
                        <label class="form-label">{{ __('crm::crm.lead_title') }} *</label>
                        <input type="text" class="form-control" wire:model="editingLead.title" required>
                        @error('editingLead.title')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">{{ __('crm::crm.client') }} *</label>
                        <select class="form-control" wire:model="editingLead.client_id">
                            <option value="">{{ __('crm::crm.select_client') }}</option>
                            @foreach ($clients as $client)
                                <option value="{{ is_array($client) ? $client['id'] : $client->id }}">
                                    {{ is_array($client) ? $client['cname'] : $client->cname }}</option>
                            @endforeach
                        </select>
                        @error('editingLead.client_id')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">{{ __('crm::crm.expected_value') }}</label>
                        <input type="number" step="0.01" class="form-control" wire:model="editingLead.amount"
                            placeholder="0.00">
                        @error('editingLead.amount')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">{{ __('crm::crm.source') }}</label>
                        <select class="form-control" wire:model="editingLead.source">
                            <option value="">{{ __('crm::crm.select_lead_source') }}</option>
                            @foreach ($sources as $source)
                                <option value="{{ $source->id }}">{{ $source->title }}</option>
                            @endforeach
                        </select>
                        @error('editingLead.source')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">{{ __('crm::crm.follow_up_manager') }}</label>
                        <select class="form-control" wire:model="editingLead.assigned_to">
                            <option value="">{{ __('crm::crm.select_manager') }}</option>
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">{{ __('crm::crm.lead_description') }}</label>
                        <textarea class="form-control" wire:model="editingLead.description" rows="3"
                            placeholder="{{ __('crm::crm.additional_details_about_lead') }}"></textarea>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-success">{{ __('crm::crm.update_lead') }}</button>
                        <button type="button" class="btn btn-secondary"
                            wire:click="closeModal">{{ __('crm::crm.cancel') }}</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- Show Lead Details Modal --}}
    @if ($showViewModal && $viewingLead)
        <div class="modal-overlay" wire:click.self="closeModal">
            <div class="modal-content" style="max-width: 700px;">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="mb-0">{{ __('crm::crm.lead_details') }}</h4>
                    <button type="button" class="btn-close" wire:click="closeModal"></button>
                </div>

                <div class="row g-3">
                    <!-- Lead Title -->
                    <div class="col-12">
                        <div class="card border-start border-primary border-4">
                            <div class="card-body">
                                <h5 class="card-title text-primary mb-0">{{ $viewingLead['title'] }}</h5>
                            </div>
                        </div>
                    </div>

                    <!-- Client Information -->
                    <div class="col-md-6">
                        <div class="card h-100">
                            <div class="card-body">
                                <h6 class="card-subtitle mb-2 text-muted">
                                    <i class="fas fa-user"></i> {{ __('crm::crm.client') }}
                                </h6>
                                <p class="card-text fw-bold">
                                    {{ $viewingLead['client']['cname'] ?? __('crm::crm.undefined') }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Expected Value -->
                    <div class="col-md-6">
                        <div class="card h-100">
                            <div class="card-body">
                                <h6 class="card-subtitle mb-2 text-muted">
                                    <i class="fas fa-money-bill-wave"></i> {{ __('crm::crm.expected_value') }}
                                </h6>
                                <p class="card-text fw-bold text-success">
                                    {{ $viewingLead['amount'] ? number_format($viewingLead['amount']) . ' ' . __('crm::crm.egp') : __('crm::crm.not_specified') }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Status -->
                    <div class="col-md-6">
                        <div class="card h-100">
                            <div class="card-body">
                                <h6 class="card-subtitle mb-2 text-muted">
                                    <i class="fas fa-flag"></i> {{ __('crm::crm.status') }}
                                </h6>
                                <p class="card-text">
                                    <span class="badge"
                                        style="background-color: {{ $viewingLead['status']['color'] ?? '#6c757d' }}; color: white;">
                                        {{ $viewingLead['status']['name'] ?? __('crm::crm.undefined') }}
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Source -->
                    <div class="col-md-6">
                        <div class="card h-100">
                            <div class="card-body">
                                <h6 class="card-subtitle mb-2 text-muted">
                                    <i class="fas fa-source"></i> {{ __('crm::crm.source') }}
                                </h6>
                                <p class="card-text">
                                    {{ $viewingLead['source_title'] ?? __('crm::crm.not_specified') }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Assigned To -->
                    <div class="col-md-6">
                        <div class="card h-100">
                            <div class="card-body">
                                <h6 class="card-subtitle mb-2 text-muted">
                                    <i class="fas fa-user-tie"></i> {{ __('crm::crm.follow_up_manager') }}
                                </h6>
                                <p class="card-text">
                                    {{ $viewingLead['assigned_to']['name'] ?? __('crm::crm.unassigned') }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Description -->
                    @if (!empty($viewingLead['description']))
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <h6 class="card-subtitle mb-2 text-muted">
                                        <i class="fas fa-align-left"></i> {{ __('crm::crm.lead_description') }}
                                    </h6>
                                    <p class="card-text">{{ $viewingLead['description'] }}</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Dates -->
                    <div class="col-md-6">
                        <div class="card h-100">
                            <div class="card-body">
                                <h6 class="card-subtitle mb-2 text-muted">
                                    <i class="fas fa-calendar-plus"></i> {{ __('crm::crm.creation_date') }}
                                </h6>
                                <p class="card-text">
                                    {{ $viewingLead['created_at'] ? \Carbon\Carbon::parse($viewingLead['created_at'])->format('Y-m-d H:i') : '-' }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card h-100">
                            <div class="card-body">
                                <h6 class="card-subtitle mb-2 text-muted">
                                    <i class="fas fa-calendar-check"></i> {{ __('crm::crm.last_update') }}
                                </h6>
                                <p class="card-text">
                                    {{ $viewingLead['updated_at'] ? \Carbon\Carbon::parse($viewingLead['updated_at'])->format('Y-m-d H:i') : '-' }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-4 d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-success" wire:click="editLead({{ $viewingLead['id'] }})">
                        <i class="fas fa-edit"></i> {{ __('crm::crm.edit') }}
                    </button>
                    <button type="button" class="btn btn-secondary" wire:click="closeModal">
                        <i class="fas fa-times"></i> {{ __('crm::crm.close') }}
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- نافذة تقرير الEGPرحلة --}}
    @if ($showReportModal && $selectedStatusForReport)
        <div class="modal-overlay" wire:click.self="closeModal">
            <div class="modal-content" style="max-width: 800px; max-height: 90vh; overflow-y: auto;">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="mb-0">{{ __('crm::crm.stage_report_colon') }} {{ $selectedStatusForReport->name }}</h4>
                    <button type="button" class="btn-close" wire:click="closeModal"></button>
                </div>

                {{-- Quick Statistics --}}
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card text-center border-primary">
                            <div class="card-body">
                                <h5 class="card-title text-primary">{{ $reportData['total_leads'] }}</h5>
                                <p class="card-text">{{ __('crm::crm.total_leads') }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-center border-success">
                            <div class="card-body">
                                <h5 class="card-title text-success">
                                    {{ number_format($reportData['total_amount']) }} {{ __('crm::crm.egp') }}</h5>
                                <p class="card-text">{{ __('crm::crm.total_value') }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-center border-info">
                            <div class="card-body">
                                <h5 class="card-title text-info">{{ number_format($reportData['avg_amount']) }}
                                    {{ __('crm::crm.egp') }}</h5>
                                <p class="card-text">{{ __('crm::crm.average_value') }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-center border-warning">
                            <div class="card-body">
                                <h5 class="card-title text-warning">{{ count($reportData['leads_by_source']) }}
                                </h5>
                                <p class="card-text">{{ __('crm::crm.number_of_sources') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- تحليل حسب المصدر --}}
                @if (!empty($reportData['leads_by_source']))
                    <div class="mb-4">
                        <h5>{{ __('crm::crm.distribution_by_source') }}</h5>
                        <div class="table-responsive">
                            <table class="table table-sm table-striped">
                                <thead>
                                    <tr>
                                        <th>{{ __('crm::crm.source') }}</th>
                                        <th>{{ __('crm::crm.number_of_leads') }}</th>
                                        <th>{{ __('crm::crm.percentage') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($reportData['leads_by_source'] as $source => $count)
                                        <tr>
                                            <td>{{ $source ?: __('crm::crm.undefined') }}</td>
                                            <td>{{ $count }}</td>
                                            <td>{{ $reportData['total_leads'] > 0 ? round(($count / $reportData['total_leads']) * 100, 1) : 0 }}%
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif

                {{-- تحليل حسب المسؤول --}}
                @if (!empty($reportData['leads_by_user']))
                    <div class="mb-4">
                        <h5>{{ __('crm::crm.distribution_by_manager') }}</h5>
                        <div class="table-responsive">
                            <table class="table table-sm table-striped">
                                <thead>
                                    <tr>
                                        <th>{{ __('crm::crm.manager') }}</th>
                                        <th>{{ __('crm::crm.number_of_leads') }}</th>
                                        <th>{{ __('crm::crm.percentage') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($reportData['leads_by_user'] as $user => $count)
                                        <tr>
                                            <td>{{ $user ?: __('crm::crm.unassigned') }}</td>
                                            <td>{{ $count }}</td>
                                            <td>{{ $reportData['total_leads'] > 0 ? round(($count / $reportData['total_leads']) * 100, 1) : 0 }}%
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif

                {{-- Opportunity Details --}}
                <div class="mb-4">
                    <h5>{{ __('crm::crm.leads_details') }}</h5>
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>{{ __('crm::crm.title') }}</th>
                                    <th>{{ __('crm::crm.client') }}</th>
                                    <th>{{ __('crm::crm.value') }}</th>
                                    <th>{{ __('crm::crm.source') }}</th>
                                    <th>{{ __('crm::crm.manager') }}</th>
                                    <th>{{ __('crm::crm.creation_date') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($reportData['leads_details'] as $lead)
                                    <tr>
                                        <td>{{ $lead['title'] }}</td>
                                        <td>{{ $lead['client_name'] }}</td>
                                        <td>{{ $lead['amount'] ? number_format($lead['amount']) . ' ' . __('crm::crm.egp') : '-' }}
                                        </td>
                                        <td>{{ $lead['source'] }}</td>
                                        <td>{{ $lead['assigned_to'] }}</td>
                                        <td>{{ $lead['created_at'] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="text-end">
                    <button type="button" class="btn btn-secondary"
                        wire:click="closeModal">{{ __('crm::crm.close') }}</button>
                    <button type="button" class="btn btn-outline"
                        onclick="window.print()">{{ __('crm::crm.print_report') }}</button>
                </div>
            </div>
        </div>
    @endif

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const board = document.getElementById('leads-board');
                // const scrollLeftIndicator = document.getElementById('scroll-left');
                const scrollRightIndicator = document.getElementById('scroll-right');

                let draggedElement = null;
                let scrollInterval = null;
                let isDragging = false;

                // إعداد أزرار التحكEGP في الاسكرول اليدوي
                const scrollLeftBtn = document.createElement('button');
                scrollLeftBtn.className = 'scroll-btn left';
                scrollLeftBtn.innerHTML = '◀';
                scrollLeftBtn.onclick = () => board.scrollBy({
                    left: -300,
                    behavior: 'smooth'
                });
                const scrollRightBtn = document.createElement('button');
                scrollRightBtn.className = 'scroll-btn right';
                scrollRightBtn.innerHTML = '▶';
                scrollRightBtn.onclick = () => board.scrollBy({
                    left: 300,
                    behavior: 'smooth'
                });

                board.parentNode.style.position = 'relative';
                board.parentNode.appendChild(scrollLeftBtn);
                board.parentNode.appendChild(scrollRightBtn);

                setupDragAndDrop();

                function setupDragAndDrop() {
                    document.querySelectorAll('.lead-card').forEach(card => {
                        card.addEventListener('dragstart', handleDragStart);
                        card.addEventListener('dragend', handleDragEnd);
                        card.addEventListener('touchstart', handleTouchStart, {
                            passive: false
                        });
                        card.addEventListener('touchmove', handleTouchMove, {
                            passive: false
                        });
                        card.addEventListener('touchend', handleTouchEnd, {
                            passive: false
                        });
                    });

                    document.querySelectorAll('.status-column').forEach(column => {
                        column.addEventListener('dragover', throttle(handleDragOver, 10));
                        column.addEventListener('drop', handleDrop);
                        column.addEventListener('dragenter', handleDragEnter);
                        column.addEventListener('dragleave', handleDragLeave);
                        column.addEventListener('touchmove', throttle(handleTouchMove, 10), {
                            passive: false
                        });
                    });
                }

                function handleDragStart(e) {
                    draggedElement = this;
                    isDragging = true;
                    this.classList.add('dragging');
                    const clone = this.cloneNode(true);
                    clone.classList.add('drag-placeholder');
                    this.parentNode.insertBefore(clone, this.nextSibling);
                    e.dataTransfer.effectAllowed = 'move';
                    e.dataTransfer.setData('text/html', this.outerHTML);
                    startScrollMonitoring();
                }

                function handleDragEnd(e) {
                    this.classList.remove('dragging');
                    const placeholder = document.querySelector('.drag-placeholder');
                    if (placeholder) placeholder.remove();
                    document.querySelectorAll('.status-column').forEach(col => {
                        col.classList.remove('dragover', 'drag-active');
                    });
                    draggedElement = null;
                    isDragging = false;
                    stopScrollMonitoring();
                }

                function handleDragOver(e) {
                    e.preventDefault();
                    e.dataTransfer.dropEffect = 'move';
                    updateScrollBasedOnMousePosition(e);
                    return false;
                }

                function handleDragEnter(e) {
                    e.preventDefault();
                    this.classList.add('dragover');
                    document.querySelectorAll('.status-column').forEach(col => {
                        if (col !== this) col.classList.add('drag-active');
                    });
                }

                function handleDragLeave(e) {
                    const rect = this.getBoundingClientRect();
                    const x = e.clientX;
                    const y = e.clientY;
                    if (x < rect.left || x > rect.right || y < rect.top || y > rect.bottom) {
                        this.classList.remove('dragover');
                    }
                }

                function handleDrop(e) {
                    e.stopPropagation();
                    e.preventDefault();
                    this.classList.remove('dragover');
                    document.querySelectorAll('.status-column').forEach(col => {
                        col.classList.remove('drag-active');
                    });
                    if (draggedElement) {
                        const leadId = draggedElement.getAttribute('data-lead-id');
                        const newStatusId = this.getAttribute('data-status-id');
                        this.style.transform = 'scale(1.05)';
                        setTimeout(() => this.style.transform = '', 200);
                        @this.updateLeadStatus(leadId, newStatusId);
                    }
                    return false;
                }

                function handleTouchStart(e) {
                    e.preventDefault();
                    draggedElement = this;
                    isDragging = true;
                    this.classList.add('dragging');
                    const clone = this.cloneNode(true);
                    clone.classList.add('drag-placeholder');
                    this.parentNode.insertBefore(clone, this.nextSibling);
                    startScrollMonitoring();
                }

                function handleTouchMove(e) {
                    e.preventDefault();
                    const touch = e.touches[0];
                    updateScrollBasedOnTouchPosition(touch);
                    const target = document.elementFromPoint(touch.clientX, touch.clientY);
                    if (target && target.closest('.status-column')) {
                        target.closest('.status-column').classList.add('dragover');
                    }
                }

                function handleTouchEnd(e) {
                    e.preventDefault();
                    const touch = e.changedTouches[0];
                    const target = document.elementFromPoint(touch.clientX, touch.clientY);
                    if (target && target.closest('.status-column')) {
                        const leadId = draggedElement.getAttribute('data-lead-id');
                        const newStatusId = target.closest('.status-column').getAttribute('data-status-id');
                        target.closest('.status-column').style.transform = 'scale(1.05)';
                        setTimeout(() => target.closest('.status-column').style.transform = '', 200);
                        @this.updateLeadStatus(leadId, newStatusId);
                    }
                    draggedElement.classList.remove('dragging');
                    const placeholder = document.querySelector('.drag-placeholder');
                    if (placeholder) placeholder.remove();
                    document.querySelectorAll('.status-column').forEach(col => {
                        col.classList.remove('dragover', 'drag-active');
                    });
                    draggedElement = null;
                    isDragging = false;
                    stopScrollMonitoring();
                }

                function updateScrollBasedOnMousePosition(e) {
                    const boardRect = board.getBoundingClientRect();
                    const mouseX = e.clientX;
                    const scrollZone = 80;
                    const leftZone = boardRect.left + scrollZone;
                    const rightZone = boardRect.right - scrollZone;

                    const maxScrollSpeed = 20;
                    let scrollSpeed = 0;

                    if (mouseX < leftZone) {
                        scrollSpeed = -maxScrollSpeed * ((leftZone - mouseX) / scrollZone);
                        showScrollIndicator('left');
                        startAutoScroll('left', scrollSpeed);
                    } else if (mouseX > rightZone) {
                        scrollSpeed = maxScrollSpeed * ((mouseX - rightZone) / scrollZone);
                        showScrollIndicator('right');
                        startAutoScroll('right', scrollSpeed);
                    } else {
                        hideScrollIndicators();
                        stopAutoScroll();
                    }
                }

                function updateScrollBasedOnTouchPosition(touch) {
                    const boardRect = board.getBoundingClientRect();
                    const touchX = touch.clientX;
                    const scrollZone = 80;
                    const leftZone = boardRect.left + scrollZone;
                    const rightZone = boardRect.right - scrollZone;

                    const maxScrollSpeed = 20;
                    let scrollSpeed = 0;

                    if (touchX < leftZone) {
                        scrollSpeed = -maxScrollSpeed * ((leftZone - touchX) / scrollZone);
                        showScrollIndicator('left');
                        startAutoScroll('left', scrollSpeed);
                    } else if (touchX > rightZone) {
                        scrollSpeed = maxScrollSpeed * ((touchX - rightZone) / scrollZone);
                        showScrollIndicator('right');
                        startAutoScroll('right', scrollSpeed);
                    } else {
                        hideScrollIndicators();
                        stopAutoScroll();
                    }
                }

                function startAutoScroll(direction, speed) {
                    stopAutoScroll();
                    scrollInterval = setInterval(() => {
                        board.scrollLeft += direction === 'left' ? speed : speed;
                    }, 16);
                }

                function stopAutoScroll() {
                    if (scrollInterval) {
                        clearInterval(scrollInterval);
                        scrollInterval = null;
                    }
                }

                function showScrollIndicator(direction) {
                    hideScrollIndicators();
                    const indicator = direction === 'left' ? scrollLeftIndicator : scrollRightIndicator;
                    indicator.style.display = 'flex';
                }

                function hideScrollIndicators() {
                    scrollLeftIndicator.style.display = 'none';
                    scrollRightIndicator.style.display = 'none';
                }

                function startScrollMonitoring() {
                    document.addEventListener('dragover', updateScrollBasedOnMousePosition);
                }

                function stopScrollMonitoring() {
                    document.removeEventListener('dragover', updateScrollBasedOnMousePosition);
                    stopAutoScroll();
                    hideScrollIndicators();
                }

                function throttle(func, limit) {
                    let inThrottle;
                    return function() {
                        const args = arguments;
                        const context = this;
                        if (!inThrottle) {
                            func.apply(context, args);
                            inThrottle = true;
                            setTimeout(() => inThrottle = false, limit);
                        }
                    }
                }

                const observer = new MutationObserver((mutations) => {
                    mutations.forEach(mutation => {
                        if (mutation.addedNodes.length) {
                            requestAnimationFrame(() => setupDragAndDrop());
                        }
                    });
                });
                observer.observe(board, {
                    childList: true,
                    subtree: true
                });

                Livewire.on('lead-moved', () => {
                    requestAnimationFrame(() => setupDragAndDrop());
                });

                Livewire.on('lead-added', () => {
                    requestAnimationFrame(() => setupDragAndDrop());
                });

                document.addEventListener('livewire:updated', () => {
                    requestAnimationFrame(() => setupDragAndDrop());
                });
            });

            document.addEventListener('click', function(e) {
                const clientSearchContainer = document.querySelector('.client-search-container');
                const clientDropdown = document.querySelector('.client-dropdown');

                if (clientSearchContainer && clientDropdown && !clientSearchContainer.contains(e.target)) {
                    @this.call('hideClientDropdown');
                }
            });

            document.addEventListener('keydown', function(e) {
                const dropdown = document.querySelector('.client-dropdown');
                if (!dropdown) return;

                const items = dropdown.querySelectorAll('.dropdown-item:not(.no-results)');
                let currentIndex = Array.from(items).findIndex(item => item.classList.contains('active'));

                switch (e.key) {
                    case 'ArrowDown':
                        e.preventDefault();
                        currentIndex = Math.min(currentIndex + 1, items.length - 1);
                        updateActiveItem(items, currentIndex);
                        break;

                    case 'ArrowUp':
                        e.preventDefault();
                        currentIndex = Math.max(currentIndex - 1, 0);
                        updateActiveItem(items, currentIndex);
                        break;

                    case 'Enter':
                        e.preventDefault();
                        if (currentIndex >= 0 && items[currentIndex]) {
                            items[currentIndex].click();
                        }
                        break;

                    case 'Escape':
                        @this.call('hideClientDropdown');
                        break;
                }
            });

            function updateActiveItem(items, activeIndex) {
                items.forEach((item, index) => {
                    item.classList.remove('active');
                    if (index === activeIndex) {
                        item.classList.add('active');
                    }
                });
            }
        </script>
    @endpush
</div>
