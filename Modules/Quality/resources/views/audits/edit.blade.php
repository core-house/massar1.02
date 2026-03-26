@extends('admin.dashboard')

@section('sidebar')
    @include('components.sidebar.quality')
@endsection

@section('content')
    @include('components.breadcrumb', [
        'title' => __('quality::quality.edit audit'),
        'breadcrumb_items' => [
            ['label' => __('quality::quality.quality'), 'url' => route('quality.dashboard')],
            ['label' => __('quality::quality.audit'), 'url' => route('quality.audits.index')],
            ['label' => __('quality::quality.edit')],
        ],
    ])

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h5>{{ __('quality::quality.audit details') }}</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('quality.audits.update', $audit) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="audit_title" class="form-label">{{ __('quality::quality.audit title') }} <span class="text-danger">*</span></label>
                                <input type="text" name="audit_title" id="audit_title"
                                    class="form-control @error('audit_title') is-invalid @enderror"
                                    value="{{ old('audit_title', $audit->audit_title) }}" required>
                                @error('audit_title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="audit_type" class="form-label">{{ __('quality::quality.audit type') }} <span class="text-danger">*</span></label>
                                <select name="audit_type" id="audit_type"
                                    class="form-control @error('audit_type') is-invalid @enderror" required>
                                    <option value="internal" {{ old('audit_type', $audit->audit_type) == 'internal' ? 'selected' : '' }}>{{ __('quality::quality.internal') }}</option>
                                    <option value="external" {{ old('audit_type', $audit->audit_type) == 'external' ? 'selected' : '' }}>{{ __('quality::quality.external') }}</option>
                                    <option value="supplier" {{ old('audit_type', $audit->audit_type) == 'supplier' ? 'selected' : '' }}>{{ __('quality::quality.supplier audit') }}</option>
                                    <option value="certification" {{ old('audit_type', $audit->audit_type) == 'certification' ? 'selected' : '' }}>{{ __('quality::quality.certification') }}</option>
                                    <option value="customer" {{ old('audit_type', $audit->audit_type) == 'customer' ? 'selected' : '' }}>{{ __('quality::quality.customer') }}</option>
                                </select>
                                @error('audit_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="status" class="form-label">{{ __('quality::quality.status') }} <span class="text-danger">*</span></label>
                                <select name="status" id="status"
                                    class="form-control @error('status') is-invalid @enderror" required>
                                    <option value="planned" {{ old('status', $audit->status) == 'planned' ? 'selected' : '' }}>{{ __('quality::quality.planned') }}</option>
                                    <option value="in_progress" {{ old('status', $audit->status) == 'in_progress' ? 'selected' : '' }}>{{ __('quality::quality.in progress') }}</option>
                                    <option value="completed" {{ old('status', $audit->status) == 'completed' ? 'selected' : '' }}>{{ __('quality::quality.completed') }}</option>
                                    <option value="cancelled" {{ old('status', $audit->status) == 'cancelled' ? 'selected' : '' }}>{{ __('quality::quality.cancelled') }}</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="planned_date" class="form-label">{{ __('quality::quality.planned date') }} <span class="text-danger">*</span></label>
                                <input type="date" name="planned_date" id="planned_date"
                                    class="form-control @error('planned_date') is-invalid @enderror"
                                    value="{{ old('planned_date', $audit->planned_date?->format('Y-m-d')) }}" required>
                                @error('planned_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="lead_auditor_id" class="form-label">{{ __('quality::quality.lead auditor') }} <span class="text-danger">*</span></label>
                                <select name="lead_auditor_id" id="lead_auditor_id"
                                    class="form-control @error('lead_auditor_id') is-invalid @enderror" required>
                                    <option value="">-- {{ __('quality::quality.select lead auditor') }} --</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ old('lead_auditor_id', $audit->lead_auditor_id) == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('lead_auditor_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3" id="external_fields" style="display: {{ in_array(old('audit_type', $audit->audit_type), ['external', 'certification']) ? 'block' : 'none' }}">
                                <label for="external_auditor" class="form-label">{{ __('quality::quality.external auditor') }}</label>
                                <input type="text" name="external_auditor" id="external_auditor"
                                    class="form-control @error('external_auditor') is-invalid @enderror"
                                    value="{{ old('external_auditor', $audit->external_auditor) }}">
                                @error('external_auditor')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3" id="external_org_field" style="display: {{ in_array(old('audit_type', $audit->audit_type), ['external', 'certification']) ? 'block' : 'none' }}">
                                <label for="external_organization" class="form-label">{{ __('quality::quality.external organization') }}</label>
                                <input type="text" name="external_organization" id="external_organization"
                                    class="form-control @error('external_organization') is-invalid @enderror"
                                    value="{{ old('external_organization', $audit->external_organization) }}">
                                @error('external_organization')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 mb-3">
                                <label for="audit_team" class="form-label">{{ __('quality::quality.audit team') }}</label>
                                <select name="audit_team[]" id="audit_team" class="form-control" multiple>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}"
                                            {{ in_array($user->id, old('audit_team', $audit->audit_team ?? [])) ? 'selected' : '' }}>
                                            {{ $user->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <small class="text-muted">{{ __('quality::quality.you can select multiple members (ctrl + click)') }}</small>
                            </div>

                            <div class="col-12 mb-3">
                                <label for="audit_objectives" class="form-label">{{ __('quality::quality.audit objectives') }}</label>
                                <textarea name="audit_objectives" id="audit_objectives" rows="3"
                                    class="form-control @error('audit_objectives') is-invalid @enderror">{{ old('audit_objectives', $audit->audit_objectives) }}</textarea>
                                @error('audit_objectives')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-3">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>{{ __('quality::quality.save changes') }}
                            </button>
                            <a href="{{ route('quality.audits.show', $audit) }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>{{ __('quality::quality.back') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const auditTypeSelect = document.getElementById('audit_type');
            const externalFields = document.getElementById('external_fields');
            const externalOrgField = document.getElementById('external_org_field');

            auditTypeSelect.addEventListener('change', function() {
                const show = this.value === 'external' || this.value === 'certification';
                externalFields.style.display = show ? 'block' : 'none';
                externalOrgField.style.display = show ? 'block' : 'none';
            });
        });
    </script>
@endsection
