@extends('admin.dashboard')

@section('sidebar')
    @include('components.sidebar.crm')
@endsection

@section('content')
    @include('components.breadcrumb', [
        'title' => __('crm::crm.tickets'),
        'items' => [
            ['label' => __('crm::crm.dashboard'), 'url' => route('admin.dashboard')],
            ['label' => __('crm::crm.tickets'), 'url' => route('tickets.index')],
            ['label' => __('crm::crm.create')],
        ],
    ])

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h2>{{ __('crm::crm.add_new_ticket') }}</h2>
                </div>
                <div class="card-body">
                    <form action="{{ route('tickets.store') }}" method="POST" onsubmit="disableButton()">
                        @csrf

                        <div class="row">
                            <!-- Client -->
                            <div class="col-md-4 mb-3">
                                <x-dynamic-search name="client_id" :label="__('crm::crm.client')" column="cname"
                                    model="App\Models\Client" :placeholder="__('crm::crm.search_for_client')" :required="true" :class="'form-select'" />
                            </div>

                            <!-- Ticket Type -->
                            <div class="mb-3 col-lg-4">
                                <label for="ticket_type" class="form-label">{{ __('crm::crm.ticket_type') }}</label>
                                <select name="ticket_type" id="ticket_type" class="form-control">
                                    <option value="">{{ __('crm::crm.select_type') }}</option>
                                    <option value="product_quality"
                                        {{ old('ticket_type') == 'product_quality' ? 'selected' : '' }}>
                                        {{ __('crm::crm.product_quality_complaint') }}</option>
                                    <option value="delivery_delay"
                                        {{ old('ticket_type') == 'delivery_delay' ? 'selected' : '' }}>
                                        {{ __('crm::crm.delivery_shipping_delay') }}</option>
                                    <option value="quantity_issue"
                                        {{ old('ticket_type') == 'quantity_issue' ? 'selected' : '' }}>
                                        {{ __('crm::crm.quantity_shortage_excess') }}</option>
                                    <option value="invoice_error"
                                        {{ old('ticket_type') == 'invoice_error' ? 'selected' : '' }}>
                                        {{ __('crm::crm.invoice_pricing_error') }}</option>
                                    <option value="technical_inquiry"
                                        {{ old('ticket_type') == 'technical_inquiry' ? 'selected' : '' }}>
                                        {{ __('crm::crm.technical_product_inquiry') }}</option>
                                    <option value="visit_training"
                                        {{ old('ticket_type') == 'visit_training' ? 'selected' : '' }}>
                                        {{ __('crm::crm.visit_training_request') }}</option>
                                </select>
                                @error('ticket_type')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <!-- Assigned To -->
                            <div class="mb-3 col-lg-4">
                                <label for="assigned_to" class="form-label">{{ __('crm::crm.assigned_to') }}</label>
                                <select name="assigned_to" id="assigned_to" class="form-control">
                                    <option value="">{{ __('crm::crm.select_user') }}</option>
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}"
                                            {{ old('assigned_to') == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('assigned_to')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <!-- Subject -->
                            <div class="mb-3 col-lg-12">
                                <label for="subject" class="form-label">{{ __('crm::crm.subject') }}</label>
                                <input type="text" name="subject" id="subject" class="form-control"
                                    value="{{ old('subject') }}" required>
                                @error('subject')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <!-- Description -->
                            <div class="mb-3 col-lg-9">
                                <label for="description" class="form-label">{{ __('crm::crm.details') }}</label>
                                <textarea name="description" id="description" class="form-control" rows="5" required>{{ old('description') }}</textarea>
                                @error('description')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <x-branches::branch-select :branches="$branches" />

                            <!-- Ticket Reference -->
                            <div class="mb-3 col-lg-4">
                                <label for="ticket_reference" class="form-label">{{ __('crm::crm.ticket_reference') }}</label>
                                <input type="text" name="ticket_reference" id="ticket_reference" class="form-control"
                                    value="{{ old('ticket_reference') }}">
                                @error('ticket_reference')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <!-- Opened Date -->
                            <div class="mb-3 col-lg-4">
                                <label for="opened_date" class="form-label">{{ __('crm::crm.opened_date') }}</label>
                                <input type="date" name="opened_date" id="opened_date" class="form-control"
                                    value="{{ old('opened_date', date('Y-m-d')) }}">
                                @error('opened_date')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <!-- Response Deadline -->
                            <div class="mb-3 col-lg-4">
                                <label for="response_deadline" class="form-label">{{ __('crm::crm.response_deadline') }}</label>
                                <input type="date" name="response_deadline" id="response_deadline" class="form-control"
                                    value="{{ old('response_deadline') }}">
                                @error('response_deadline')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <!-- Priority -->
                            <div class="mb-3 col-lg-6">
                                <label for="priority" class="form-label">{{ __('crm::crm.priority') }}</label>
                                <select name="priority" id="priority" class="form-control" required>
                                    <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>
                                        {{ __('crm::crm.low') }}</option>
                                    <option value="medium" {{ old('priority', 'medium') == 'medium' ? 'selected' : '' }}>
                                        {{ __('crm::crm.medium') }}</option>
                                    <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>
                                        {{ __('crm::crm.high') }}</option>
                                </select>
                                @error('priority')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>

                        <!-- Save Buttons -->
                        <div class="d-flex justify-content-start mt-4">
                            <button type="submit" class="btn btn-main me-2" id="submitBtn">
                                <i class="las la-save"></i> {{ __('crm::crm.save') }}
                            </button>
                            <a href="{{ route('tickets.index') }}" class="btn btn-danger">
                                <i class="las la-times"></i> {{ __('crm::crm.cancel') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
