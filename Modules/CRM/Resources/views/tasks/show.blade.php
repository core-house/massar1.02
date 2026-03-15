@extends('admin.dashboard')

@section('sidebar')
    @include('components.sidebar.tasks')
@endsection

@section('content')
    @include('components.breadcrumb', [
        'title' => __('crm::crm.task_details'),
        'items' => [
            ['label' => __('crm::crm.dashboard'), 'url' => route('admin.dashboard')],
            ['label' => __('crm::crm.tasks_and_activities'), 'url' => route('tasks.index')],
            ['label' => __('crm::crm.task_details')],
        ],
    ])

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="page-title mb-0">{{ __('crm::crm.task_details') }}</h4>
                        <div class="d-flex gap-2">
                            @can('edit Tasks')
                                <a href="{{ route('tasks.edit', $task) }}" class="btn btn-primary">
                                    <i class="las la-edit"></i> {{ __('crm::crm.edit') }}
                                </a>
                            @endcan
                            <a href="{{ route('tasks.index') }}" class="btn btn-secondary">
                                <i class="las la-arrow-right"></i> {{ __('crm::crm.back') }}
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Task Title -->
                        <div class="col-md-12 mb-4">
                            <h3 class="text-primary">{{ $task->title }}</h3>
                        </div>

                        <!-- Client -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">{{ __('crm::crm.client') }}:</label>
                            <div class="form-control-static">
                                {{ optional($task->client)->cname ?? __('crm::crm.na') }}
                            </div>
                        </div>

                        <!-- Assigned User -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">{{ __('crm::crm.assigned_to') }}:</label>
                            <div class="form-control-static">
                                {{ optional($task->user)->name ?? __('crm::crm.na') }}
                            </div>
                        </div>

                        <!-- Created By -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">{{ __('crm::crm.created_by') }}:</label>
                            <div class="form-control-static">
                                {{ optional($task->creator)->name ?? __('crm::crm.unknown') }}
                            </div>
                        </div>

                        <!-- Task Type -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">{{ __('crm::crm.task_type') }}:</label>
                            <div class="form-control-static">
                                {{ optional($task->taskType)->title ?? __('crm::crm.na') }}
                            </div>
                        </div>

                        <!-- Priority -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">{{ __('crm::crm.priority') }}:</label>
                            <div class="form-control-static">
                                @php
                                    $priority = is_string($task->priority)
                                        ? \Modules\CRM\Enums\TaskPriorityEnum::tryFrom($task->priority)
                                        : $task->priority;
                                @endphp
                                @if ($priority)
                                    <span class="badge bg-{{ $priority->color() }} fs-6">
                                        {{ $priority->label() }}
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Status -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">{{ __('crm::crm.status') }}:</label>
                            <div class="form-control-static">
                                @php
                                    $status = is_string($task->status)
                                        ? \Modules\CRM\Enums\TaskStatusEnum::tryFrom($task->status)
                                        : $task->status;
                                @endphp
                                @if ($status)
                                    <span class="badge bg-{{ $status->color() }} fs-6">
                                        {{ $status->label() }}
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Start Date -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">{{ __('crm::crm.start_date') }}:</label>
                            <div class="form-control-static">
                                {{ $task->start_date ? \Carbon\Carbon::parse($task->start_date)->format('Y-m-d') : __('crm::crm.na') }}
                            </div>
                        </div>

                        <!-- Due Date -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">{{ __('crm::crm.end_date') }}:</label>
                            <div class="form-control-static">
                                {{ $task->due_date ? \Carbon\Carbon::parse($task->due_date)->format('Y-m-d') : __('crm::crm.na') }}
                            </div>
                        </div>

                        <!-- Duration -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">{{ __('crm::crm.duration') }}:</label>
                            <div class="form-control-static">
                                @if($task->duration)
                                    <span class="badge bg-info fs-6">{{ $task->formatted_duration }} {{ __('crm::crm.hours') }}</span>
                                @else
                                    <span class="text-muted">{{ __('crm::crm.na') }}</span>
                                @endif
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="col-md-12 mb-3">
                            <label class="form-label fw-bold">{{ __('crm::crm.description') }}:</label>
                            <div class="form-control-static p-3 bg-light rounded">
                                {{ $task->description ?? __('crm::crm.no_description') }}
                            </div>
                        </div>

                        <!-- Client Comment -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">{{ __('crm::crm.client_comment') }}:</label>
                            <div class="form-control-static">
                                {{ $task->client_comment ?? __('crm::crm.na') }}
                            </div>
                        </div>

                        <!-- User Comment -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">{{ __('crm::crm.user_comment') }}:</label>
                            <div class="form-control-static">
                                {{ $task->user_comment ?? __('crm::crm.na') }}
                            </div>
                        </div>

                        <!-- Attachment -->
                        <div class="col-md-12 mb-3">
                            <label class="form-label fw-bold">{{ __('crm::crm.attachments') }}:</label>
                            <div class="form-control-static">
                                @if ($task->hasMedia('tasks'))
                                    @php
                                        $media = $task->getFirstMedia('tasks');
                                    @endphp
                                    <div class="mt-2">
                                        @if ($media && Str::contains($media->mime_type, 'image'))
                                            <img src="{{ $media->getUrl() }}" class="img-fluid rounded"
                                                style="max-width: 500px;" alt="{{ __('crm::crm.task_attachment') }}">
                                        @else
                                            <a href="{{ $media->getUrl() }}" class="btn btn-primary" download>
                                                <i class="las la-download me-2"></i>{{ __('crm::crm.download_file') }}
                                            </a>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-muted">{{ __('crm::crm.no_attachment') }}</span>
                                @endif
                            </div>
                        </div>

                        <!-- Created At -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">{{ __('crm::crm.created_at') }}:</label>
                            <div class="form-control-static">
                                {{ $task->created_at->format('Y-m-d H:i:s') }}
                            </div>
                        </div>

                        <!-- Updated At -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">{{ __('crm::crm.updated_at') }}:</label>
                            <div class="form-control-static">
                                {{ $task->updated_at->format('Y-m-d H:i:s') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
