@extends('admin.dashboard')

@section('sidebar')
    @include('components.sidebar.tasks')
@endsection

@section('content')
    @include('components.breadcrumb', [
        'title' => __('crm::crm.edit_tasks_activities'),
        'breadcrumb_items' => [
            ['label' => __('crm::crm.dashboard'), 'url' => route('admin.dashboard')],
            ['label' => __('crm::crm.tasks_and_activities'), 'url' => route('tasks.index')],
            ['label' => __('crm::crm.edit')],
        ],
    ])

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h2>{{ __('crm::crm.edit_task') }}</h2>
                </div>
                <div class="card-body">
                    <form action="{{ route('tasks.update', $task->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            {{-- Client Name --}}
                            <div class="col-md-4 mb-3">
                                <x-dynamic-search name="client_id" :label="__('crm::crm.client')" column="cname"
                                    model="App\Models\Client" :placeholder="__('crm::crm.search_for_client')" :required="false" :class="'form-select'"
                                    :selected="$task->client_id" />
                            </div>

                            {{-- User Name --}}
                            <div class="mb-3 col-lg-4">
                                <label for="user_id" class="form-label">{{ __('crm::crm.user') }}</label>
                                <select name="user_id" id="user_id" class="form-control">
                                    @foreach ($users as $id => $name)
                                        <option value="{{ $id }}"
                                            {{ old('user_id', $task->user_id) == $id ? 'selected' : '' }}>
                                            {{ $name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('user_id')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            {{-- Task Type --}}
                            <div class="mb-3 col-lg-4">
                                <label class="form-label" for="task_type_id">{{ __('crm::crm.task_type') }}</label>
                                <select name="task_type_id" id="task_type_id" class="form-control">
                                    <option value="">{{ __('crm::crm.select_task_type') }}</option>
                                    @foreach ($taskTypes as $id => $title)
                                        <option value="{{ $id }}"
                                            {{ old('task_type_id', $task->task_type_id ?? '') == $id ? 'selected' : '' }}>
                                            {{ $title }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('task_type_id')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            {{-- Title --}}
                            <div class="mb-3 col-lg-4">
                                <label for="title" class="form-label">{{ __('crm::crm.task_title') }}</label>
                                <input type="text" name="title" id="title" class="form-control"
                                    value="{{ old('title', $task->title) }}">
                                @error('title')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            {{-- Priority --}}
                            <div class="mb-3 col-lg-2">
                                <label for="priority" class="form-label">{{ __('crm::crm.priority') }}</label>
                                <select name="priority" id="priority" class="form-control">
                                    @foreach (\Modules\CRM\Enums\TaskPriorityEnum::cases() as $priority)
                                        <option value="{{ $priority->value }}"
                                            {{ old('priority', $task->priority->value) == $priority->value ? 'selected' : '' }}>
                                            {{ $priority->label() }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('priority')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            {{-- Status --}}
                            <div class="mb-3 col-lg-2">
                                <label for="status" class="form-label">{{ __('crm::crm.task_status') }}</label>
                                <select name="status" id="status" class="form-control">
                                    @foreach (\Modules\CRM\Enums\TaskStatusEnum::cases() as $status)
                                        <option value="{{ $status->value }}"
                                            {{ old('status', $task->status->value) == $status->value ? 'selected' : '' }}>
                                            {{ $status->label() }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('status')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            {{-- Start Date --}}
                            <div class="mb-3 col-lg-1">
                                <label for="start_date" class="form-label">{{ __('crm::crm.start_date') }}</label>
                                <input type="date" name="start_date" id="start_date" class="form-control"
                                    value="{{ old('start_date', \Carbon\Carbon::parse($task->start_date)->format('Y-m-d')) }}">
                                @error('start_date')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            {{-- Due Date --}}
                            <div class="mb-3 col-lg-1">
                                <label for="due_date" class="form-label">{{ __('crm::crm.end_date') }}</label>
                                <input type="date" name="due_date" id="due_date" class="form-control"
                                    value="{{ old('due_date', \Carbon\Carbon::parse($task->due_date)->format('Y-m-d')) }}">
                                @error('due_date')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            {{-- Duration --}}
                            <div class="mb-3 col-lg-2">
                                <label for="duration" class="form-label">{{ __('crm::crm.duration') }}</label>
                                <input type="number" name="duration" id="duration" class="form-control"
                                    min="0" step="0.5"
                                    value="{{ old('duration', $task->duration) }}">
                                @error('duration')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            {{-- Description --}}
                            <div class="mb-3 col-lg-12">
                                <label for="description" class="form-label">{{ __('crm::crm.description') }}</label>
                                <textarea name="description" id="description" class="form-control" rows="3">{{ old('description', $task->description) }}</textarea>
                                @error('description')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            {{-- Client Comment --}}
                            <div class="mb-3 col-lg-6">
                                <label for="client_comment" class="form-label">{{ __('crm::crm.client_comment') }}</label>
                                <textarea name="client_comment" id="client_comment" class="form-control">{{ old('client_comment', $task->client_comment) }}</textarea>
                                @error('client_comment')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            {{-- User Comment --}}
                            <div class="mb-3 col-lg-6">
                                <label for="user_comment" class="form-label">{{ __('crm::crm.user_comment') }}</label>
                                <textarea name="user_comment" id="user_comment" class="form-control">{{ old('user_comment', $task->user_comment) }}</textarea>
                                @error('user_comment')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            {{-- Existing Attachments --}}
                            @if($task->hasMedia('tasks'))
                            <div class="mb-3 col-lg-12">
                                <label class="form-label">{{ __('crm::crm.current_attachments') }}</label>
                                <div class="row g-2">
                                    @foreach($task->getMedia('tasks') as $media)
                                        <div class="col-md-2">
                                            <div class="card">
                                                <div class="card-body p-2 text-center">
                                                    @if(Str::contains($media->mime_type, 'image'))
                                                        <img src="{{ $media->getUrl() }}" class="img-fluid rounded mb-2" style="max-height: 80px;">
                                                    @else
                                                        <i class="las la-file-alt" style="font-size: 3rem; color: #6c757d;"></i>
                                                    @endif
                                                    <p class="small mb-1 text-truncate">{{ $media->file_name }}</p>
                                                    <a href="{{ $media->getUrl() }}" class="btn btn-sm btn-outline-primary" target="_blank">
                                                        <i class="las la-eye"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            @endif

                            {{-- New Attachments --}}
                            <div class="mb-3 col-lg-12">
                                <label for="attachments" class="form-label">
                                    {{ __('crm::crm.add_new_attachments') }}
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
                        </div>

                        {{-- Save Button --}}
                        <div class="d-flex justify-content-start mt-4">
                            <button type="submit" class="btn btn-success me-2">
                                <i class="las la-save"></i> {{ __('crm::crm.update') }}
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
