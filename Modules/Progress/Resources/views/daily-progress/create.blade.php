@extends('admin.dashboard')

@section('sidebar')
    @include('components.sidebar-wrapper', ['sections' => ['daily_progress', 'projects', 'accounts']])
@endsection

@section('title', __('general.daily_progress_title'))

@section('content')
<div class="container">
    <div class="main-card card shadow-lg border-0">
        <div class="card-header text-white d-flex justify-content-between align-items-center"
             style="background: linear-gradient(120deg, #2c7be5 0%, #1a56ce 100%); border-radius: 0.75rem 0.75rem 0 0;">
            <h5 class="mb-0"><i class="fas fa-tasks me-2"></i> {{ __('general.daily_progress_title') }}</h5>
            @can('dailyprogress-list')
            <a href="{{ route('daily.progress.index') }}" class="btn btn-light btn-sm">
                <i class="fas fa-arrow-left me-1"></i> {{ __('general.back_to_list') }}
            </a>
            @endcan

        </div>

        <div class="card-body bg-light">
            <form action="{{ route('daily.progress.store') }}" method="POST" class="needs-validation" novalidate>
                @csrf

                <div class="row mb-4">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">{{ __('general.project') }}</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="fas fa-project-diagram"></i></span>
                            <select name="project_id" id="project_id" class="form-select shadow-sm" required>
                                <option value="">{{ __('general.select_project') }}</option>
                                @foreach ($projects as $project)
                                    <option value="{{ $project->id }}">{{ $project->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">{{ __('general.date') }}</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="fas fa-calendar-day"></i></span>
                            <input type="date" name="progress_date" class="form-control shadow-sm" required>
                        </div>
                    </div>
                </div>

                <!-- جدول البنود -->
                <div class="table-responsive mb-4" id="items_table_wrapper" style="display:none;">
                    <table class="table table-hover table-striped align-middle border shadow-sm">
                        <thead class="table-primary">
                            <tr>
                                <th>{{ __('general.work_item') }}</th>
                                <th width="200">{{ __('general.enter_quantity') }}</th>
                            </tr>
                        </thead>
                        <tbody id="items_table_body">
                            <!-- الصفوف تتولد هنا -->
                        </tbody>
                    </table>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold">{{ __('general.notes') }}</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="fas fa-sticky-note"></i></span>
                        <textarea name="notes" class="form-control shadow-sm" rows="3"></textarea>
                    </div>
                </div>

                <div class="d-flex justify-content-between mt-4">
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="fas fa-save me-2"></i> {{ __('general.save_progress') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        // عند اختيار المشروع
        $('#project_id').change(function() {
            var projectId = $(this).val();
            if (projectId) {
                $.get('/api/project-items/' + projectId, function(data) {
                    $('#items_table_body').empty();
                    if (data.length > 0) {
                        $('#items_table_wrapper').show();
                        $.each(data, function(index, item) {
                            var row = `
                                <tr>
                                    <td class="fw-bold text-dark">${item.work_item.name}</td>
                                    <td>
                                        <input type="number"
                                            name="quantities[${item.id}]"
                                            class="form-control quantity-input shadow-sm"
                                            step="0.01" min="0" required>
                                    </td>
                                </tr>
                            `;
                            $('#items_table_body').append(row);
                        });

                        // التنقل بالـ Enter
                        $('.quantity-input').on('keydown', function(e) {
                            if (e.key === 'Enter') {
                                e.preventDefault();
                                var inputs = $('.quantity-input');
                                var idx = inputs.index(this);
                                if (idx + 1 < inputs.length) {
                                    inputs[idx + 1].focus();
                                } else {
                                    inputs[idx].blur(); // آخر واحد
                                }
                            }
                        });
                    } else {
                        $('#items_table_wrapper').hide();
                    }
                });
            } else {
                $('#items_table_wrapper').hide();
            }
        });
    });
</script>
@endpush
@endsection
