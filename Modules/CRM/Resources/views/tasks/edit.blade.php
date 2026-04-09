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
                                <label class="form-label">{{ __('crm::crm.client') }}</label>
                                <div class="d-flex gap-2">
                                    <div style="flex: 1;">
                                        <select class="select2-dynamic form-control" id="client_id" name="client_id">
                                            <option value="">{{ __('crm::crm.search_for_client') }}</option>
                                            @foreach(\App\Models\Client::all() as $client)
                                                <option value="{{ $client->id }}" {{ old('client_id', $task->client_id) == $client->id ? 'selected' : '' }}>
                                                    {{ $client->cname }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-primary"
                                            onclick="new bootstrap.Modal(document.getElementById('addClientModal')).show()"
                                            title="{{ __('crm::crm.add_new_client') }}"
                                            style="height: 50px;">
                                        <i class="las la-plus"></i>
                                    </button>
                                </div>
                            </div>

                            {{-- User Name --}}
                            <div class="mb-3 col-lg-4">
                                <label for="user_id" class="form-label">{{ __('crm::crm.user') }}</label>
                                <select class="select2-dynamic form-control" id="user_id" name="user_id">
                                    <option value="">{{ __('crm::crm.search_for_user') }}</option>
                                    @foreach ($users as $id => $name)
                                        <option value="{{ $id }}" {{ old('user_id', $task->user_id) == $id ? 'selected' : '' }}>
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
                                    value="{{ old('due_date', \Carbon\Carbon::parse($task->due_date ?? now())->format('Y-m-d')) }}">
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

    <!-- Add Client Modal -->
    <div class="modal fade" id="addClientModal" tabindex="-1" aria-labelledby="addClientModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addClientModalLabel" style="color: white !important;">{{ __('crm::crm.add_new_client') }}</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="{{ __('common.close') }}"></button>
                </div>
                <div class="modal-body">
                    <form id="addClientForm">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="modal_cname" class="form-label">{{ __('crm::crm.client_name') }} <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="modal_cname" name="cname" required>
                                <small class="text-danger d-none" id="error_cname"></small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="modal_phone" class="form-label">{{ __('crm::crm.phone') }}</label>
                                <input type="text" class="form-control" id="modal_phone" name="phone">
                                <small class="text-danger d-none" id="error_phone"></small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="modal_email" class="form-label">{{ __('crm::crm.email') }}</label>
                                <input type="email" class="form-control" id="modal_email" name="email">
                                <small class="text-danger d-none" id="error_email"></small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="modal_address" class="form-label">{{ __('crm::crm.address') }}</label>
                                <input type="text" class="form-control" id="modal_address" name="address">
                                <small class="text-danger d-none" id="error_address"></small>
                            </div>
                        </div>
                        <input type="hidden" name="is_active" value="1">
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="las la-times"></i> {{ __('crm::crm.cancel') }}
                    </button>
                    <button type="button" class="btn btn-primary" id="saveClientBtn">
                        <i class="las la-save"></i> {{ __('crm::crm.save') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Add Client Modal Handler
            const saveClientBtn = document.getElementById('saveClientBtn');
            const addClientForm = document.getElementById('addClientForm');
            const addClientModal = document.getElementById('addClientModal');

            if (saveClientBtn && addClientForm) {
                saveClientBtn.addEventListener('click', function() {
                    // Clear previous errors
                    document.querySelectorAll('.text-danger').forEach(el => {
                        el.classList.add('d-none');
                        el.textContent = '';
                    });
                    document.querySelectorAll('.is-invalid').forEach(el => {سا}
                        el.classList.remove('is-invalid');
                    });

                    const formData = new FormData(addClientForm);
                    saveClientBtn.disabled = true;
                    saveClientBtn.innerHTML = '<i class="las la-spinner la-spin"></i> جاري الحفظ...';

                    fetch('{{ route("clients.store") }}', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => {
                        if (!response.ok) {
                            return response.json().then(err => {
                                throw err;
                            });
                        }
                        return response.json();
                    })
                    .then(data => {
                        // Close modal
                        bootstrap.Modal.getInstance(addClientModal).hide();

                        // Reset form
                        addClientForm.reset();

                        // Show success message
                        alert('✓ تم إضافة العميل بنجاح');

                        // Reload page to update client list
                        window.location.reload();
                    })
                    .catch(error => {
                        console.error('Error:', error);

                        // Show validation errors
                        if (error.errors) {
                            Object.keys(error.errors).forEach(key => {
                                const errorEl = document.getElementById('error_' + key);
                                const inputEl = document.getElementById('modal_' + key);

                                if (errorEl) {
                                    errorEl.textContent = error.errors[key][0];
                                    errorEl.classList.remove('d-none');
                                }

                                if (inputEl) {
                                    inputEl.classList.add('is-invalid');
                                }
                            });
                        } else {
                            alert('⚠ ' + (error.message || 'حدث خطأ أثناء الحفظ'));
                        }
                    })
                    .finally(() => {
                        saveClientBtn.disabled = false;
                        saveClientBtn.innerHTML = '<i class="las la-save"></i> {{ __("crm::crm.save") }}';
                    });
                });
            }
        });
    </script>
@endpush

<style>
    .select2-container--default .select2-selection--single {
        height: 50px !important;
        border: 1px solid #ced4da;
        border-radius: 0.25rem;
        padding: 6px 12px;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 36px;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 48px;
    }
</style>

<script>
$(document).ready(function() {
    // Initialize Select2
    $('#client_id, #user_id').select2({
        placeholder: function() {
            return $(this).find('option:first').text();
        },
        allowClear: true,
        width: '100%',
        language: {
            noResults: function() {
                return "لا توجد نتائج";
            },
            searching: function() {
                return "جاري البحث...";
            }
        }
    });
});
</script>
