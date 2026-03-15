@extends('admin.dashboard')

@section('sidebar')
    @include('components.sidebar.tasks')
@endsection

@section('content')
    @include('components.breadcrumb', [
        'title' => __('crm::crm.tasks_and_activities'),
        'items' => [
            ['label' => __('crm::crm.dashboard'), 'url' => route('admin.dashboard')],
            ['label' => __('crm::crm.tasks_and_activities'), 'url' => route('tasks.index')],
            ['label' => __('crm::crm.create')],
        ],
    ])

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h2>{{ __('crm::crm.add_new_task') }}</h2>
                </div>
                <div class="card-body">
                    <form action="{{ route('tasks.store') }}" method="POST" enctype="multipart/form-data"
                        onsubmit="disableButton()">
                        @csrf

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <x-dynamic-search name="client_id" :label="__('crm::crm.client')" column="cname"
                                    model="App\Models\Client" :placeholder="__('crm::crm.search_for_client')" :required="false" :class="'form-select'" />
                            </div>

                            <!-- User Name -->
                            <div class="mb-3 col-lg-4">
                                <label for="user_id" class="form-label">{{ __('crm::crm.user') }}</label>
                                <select name="user_id" id="user_id" class="form-control">
                                    @foreach ($users as $id => $name)
                                        <option value="{{ $id }}" {{ old('user_id') == $id ? 'selected' : '' }}>
                                            {{ $name }}</option>
                                    @endforeach
                                </select>
                                @error('user_id')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror

                                <!-- Send to All Users Checkbox -->
                                <div class="form-check mt-2">
                                    <input class="form-check-input" type="checkbox" name="send_to_all_users"
                                        id="send_to_all_users" value="1"
                                        {{ old('send_to_all_users') ? 'checked' : '' }}>
                                    <label class="form-check-label fw-bold text-primary" for="send_to_all_users">
                                        <i class="las la-users"></i> {{ __('crm::crm.send_notification_to_all_users') }}
                                    </label>
                                    <small
                                        class="d-block text-muted">{{ __('crm::crm.send_notification_about_task_to_all_users') }}</small>
                                </div>
                            </div>

                            <div class="mb-3 col-lg-4">
                                <label class="form-label" for="task_type_id">{{ __('crm::crm.task_type') }}</label>
                                <select name="task_type_id" id="task_type_id" class="form-control">
                                    <option value="">{{ __('crm::crm.select_task_type') }}</option>
                                    @foreach ($taskTypes as $id => $title)
                                        <option value="{{ $id }}"
                                            {{ old('task_type_id') == $id ? 'selected' : '' }}>
                                            {{ $title }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('task_type_id')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <!-- Title -->
                            <div class="mb-3 col-lg-4">
                                <label for="title" class="form-label">{{ __('crm::crm.task_title') }}</label>
                                <input type="text" name="title" id="title" class="form-control"
                                    value="{{ old('title') }}">
                                @error('title')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <!-- Priority -->
                            <div class="mb-3 col-lg-2">
                                <label for="priority" class="form-label">{{ __('crm::crm.priority') }}</label>
                                <select name="priority" id="priority" class="form-control">
                                    @foreach (\Modules\CRM\Enums\TaskPriorityEnum::cases() as $priority)
                                        <option value="{{ $priority->value }}"
                                            {{ old('priority') == $priority->value ? 'selected' : '' }}>
                                            {{ $priority->label() }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('priority')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <!-- Status -->
                            <div class="mb-3 col-lg-2">
                                <label for="status" class="form-label">{{ __('crm::crm.task_status') }}</label>
                                <select name="status" id="status" class="form-control">
                                    @foreach (\Modules\CRM\Enums\TaskStatusEnum::cases() as $status)
                                        <option value="{{ $status->value }}"
                                            {{ old('status') === $status->value ? 'selected' : '' }}>
                                            {{ $status->label() }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('status')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <!-- Start Date -->
                            <div class="mb-3 col-lg-1">
                                <label for="start_date" class="form-label">{{ __('crm::crm.start_date') }}</label>
                                <input type="date" name="start_date" id="start_date" class="form-control"
                                    value="{{ old('start_date', \Carbon\Carbon::now()->format('Y-m-d')) }}">
                                @error('start_date')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="mb-3 col-lg-1">
                                <label for="due_date" class="form-label">{{ __('crm::crm.end_date') }}</label>
                                <input type="date" name="due_date" id="due_date" class="form-control"
                                    value="{{ old('due_date') }}">
                                @error('due_date')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <!-- Duration -->
                            <div class="mb-3 col-lg-2">
                                <label for="duration" class="form-label">{{ __('crm::crm.duration') }}</label>
                                <input type="number" name="duration" id="duration" class="form-control"
                                    min="0" step="0.5"
                                    value="{{ old('duration') }}">
                                @error('duration')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <!-- Description -->
                            <div class="mb-3 col-lg-12">
                                <label for="description" class="form-label">{{ __('crm::crm.description') }}</label>
                                <textarea name="description" id="description" class="form-control" rows="3">{{ old('description') }}</textarea>
                                @error('description')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <!-- Client Comment -->
                            <div class="mb-3 col-lg-6">
                                <label for="client_comment" class="form-label">{{ __('crm::crm.client_comment') }}</label>
                                <textarea name="client_comment" id="client_comment" class="form-control">{{ old('client_comment') }}</textarea>
                                @error('client_comment')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <!-- User Comment -->
                            <div class="mb-3 col-lg-6">
                                <label for="user_comment" class="form-label">{{ __('crm::crm.user_comment') }}</label>
                                <textarea name="user_comment" id="user_comment" class="form-control">{{ old('user_comment') }}</textarea>
                                @error('user_comment')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <!-- Attachments (Multiple) -->
                            <div class="mb-3 col-lg-3">
                                <label for="attachments" class="form-label">
                                    {{ __('crm::crm.attachments_images_or_files') }}
                                    <small class="text-muted">({{ __('crm::crm.multiple_files_allowed') }})</small>
                                </label>
                                <input type="file" name="attachments[]" id="attachments" class="form-control" multiple>
                                <small class="text-muted d-block mt-1">
                                    <i class="las la-info-circle"></i> {{ __('crm::crm.max_5mb_per_file_formats') }}
                                </small>
                                @error('attachments')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                                @error('attachments.*')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <x-branches::branch-select :branches="$branches" />
                        </div>

                        <!-- Save Buttons -->
                        <div class="d-flex justify-content-start mt-4">
                            <button type="submit" class="btn btn-main me-2" id="submitBtn">
                                <i class="las la-save"></i> {{ __('crm::crm.save') }}
                            </button>
                            <a href="{{ route('tasks.index') }}" class="btn btn-danger">
                                <i class="las la-times"></i> {{ __('crm::crm.cancel') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sendToAllCheckbox = document.getElementById('send_to_all_users');
            const userSelect = document.getElementById('user_id');

            if (sendToAllCheckbox && userSelect) {
                sendToAllCheckbox.addEventListener('change', function() {
                    if (this.checked) {
                        userSelect.disabled = true;
                        userSelect.style.opacity = '0.5';
                    } else {
                        userSelect.disabled = false;
                        userSelect.style.opacity = '1';
                    }
                });

                // Check initial state
                if (sendToAllCheckbox.checked) {
                    userSelect.disabled = true;
                    userSelect.style.opacity = '0.5';
                }
            }
        });
    </script>
@endpush
