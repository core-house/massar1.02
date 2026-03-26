@extends('admin.dashboard')

@section('sidebar')
    @include('components.sidebar.crm')
@endsection

@section('content')
    @include('components.breadcrumb', [
        'title' => __('crm::crm.edit_ticket'),
        'breadcrumb_items' => [
            ['label' => __('crm::crm.dashboard'), 'url' => route('admin.dashboard')],
            ['label' => __('crm::crm.tickets'), 'url' => route('tickets.index')],
            ['label' => $ticket->subject, 'url' => route('tickets.show', $ticket->id)],
            ['label' => __('crm::crm.edit')],
        ],
    ])

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h2>{{ __('crm::crm.edit_ticket') }}: {{ $ticket->subject }}</h2>
                </div>
                <div class="card-body">
                    <form action="{{ route('tickets.update', $ticket->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <!-- Client -->
                            <div class="col-md-6 mb-3">
                                <x-dynamic-search name="client_id" :label="__('crm::crm.client')" column="cname" model="App\Models\Client"
                                    :placeholder="__('crm::crm.search_for_client')" :required="true" :class="'form-select'" :selected-id="$ticket->client_id"
                                    :selected-name="$ticket->client->cname ?? ''" />
                            </div>

                            <!-- Assigned To -->
                            <div class="mb-3 col-lg-6">
                                <label for="assigned_to" class="form-label">{{ __('crm::crm.assigned_to') }}</label>
                                <select name="assigned_to" id="assigned_to" class="form-control">
                                    <option value="">{{ __('crm::crm.select_user') }}</option>
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}"
                                            {{ old('assigned_to', $ticket->assigned_to) == $user->id ? 'selected' : '' }}>
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
                                    value="{{ old('subject', $ticket->subject) }}" required>
                                @error('subject')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <!-- Description -->
                            <div class="mb-3 col-lg-9">
                                <label for="description" class="form-label">{{ __('crm::crm.details') }}</label>
                                <textarea name="description" id="description" class="form-control" rows="5" required>{{ old('description', $ticket->description) }}</textarea>
                                @error('description')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <x-branches::branch-select :branches="$branches" :selected="$ticket->branch_id" />

                            <!-- Priority -->
                            <div class="mb-3 col-lg-6">
                                <label for="priority" class="form-label">{{ __('crm::crm.priority') }}</label>
                                <select name="priority" id="priority" class="form-control" required>
                                    <option value="low"
                                        {{ old('priority', $ticket->priority) == 'low' ? 'selected' : '' }}>
                                        {{ __('crm::crm.low') }}</option>
                                    <option value="medium"
                                        {{ old('priority', $ticket->priority) == 'medium' ? 'selected' : '' }}>
                                        {{ __('crm::crm.medium') }}</option>
                                    <option value="high"
                                        {{ old('priority', $ticket->priority) == 'high' ? 'selected' : '' }}>
                                        {{ __('crm::crm.high') }}</option>
                                </select>
                                @error('priority')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <!-- Status -->
                            <div class="mb-3 col-lg-6">
                                <label for="status" class="form-label">{{ __('crm::crm.status') }}</label>
                                <select name="status" id="status" class="form-control" required>
                                    <option value="open"
                                        {{ old('status', $ticket->status) == 'open' ? 'selected' : '' }}>
                                        {{ __('crm::crm.open') }}</option>
                                    <option value="in_progress"
                                        {{ old('status', $ticket->status) == 'in_progress' ? 'selected' : '' }}>
                                        {{ __('crm::crm.in_progress') }}</option>
                                    <option value="resolved"
                                        {{ old('status', $ticket->status) == 'resolved' ? 'selected' : '' }}>
                                        {{ __('crm::crm.resolved') }}</option>
                                    <option value="closed"
                                        {{ old('status', $ticket->status) == 'closed' ? 'selected' : '' }}>
                                        {{ __('crm::crm.closed') }}</option>
                                </select>
                                @error('status')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>

                        <!-- Save Buttons -->
                        <div class="d-flex justify-content-start mt-4">
                            <button type="submit" class="btn btn-main me-2">
                                <i class="las la-save"></i> {{ __('crm::crm.update') }}
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
