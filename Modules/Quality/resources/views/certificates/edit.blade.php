@extends('admin.dashboard')

@section('sidebar')
    @include('components.sidebar.quality')
@endsection

@section('content')
    @include('components.breadcrumb', [
        'title' => __('quality::quality.edit certificate'),
        'breadcrumb_items' => [
            ['label' => __('quality::quality.quality'), 'url' => route('quality.dashboard')],
            ['label' => __('quality::quality.certificates'), 'url' => route('quality.certificates.index')],
            ['label' => __('quality::quality.edit')],
        ],
    ])

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h5>{{ __('quality::quality.certificate details') }}</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('quality.certificates.update', $certificate) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('quality::quality.certificate number') }}</label>
                                <input type="text" class="form-control" value="{{ $certificate->certificate_number }}" readonly>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="certificate_name" class="form-label">{{ __('quality::quality.certificate name') }} <span class="text-danger">*</span></label>
                                <input type="text" name="certificate_name" id="certificate_name"
                                    class="form-control @error('certificate_name') is-invalid @enderror"
                                    value="{{ old('certificate_name', $certificate->certificate_name) }}" required>
                                @error('certificate_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="certificate_type" class="form-label">{{ __('quality::quality.certificate type') }} <span class="text-danger">*</span></label>
                                <select name="certificate_type" id="certificate_type"
                                    class="form-control @error('certificate_type') is-invalid @enderror" required>
                                    <option value="ISO_9001" {{ old('certificate_type', $certificate->certificate_type) == 'ISO_9001' ? 'selected' : '' }}>ISO 9001</option>
                                    <option value="ISO_22000" {{ old('certificate_type', $certificate->certificate_type) == 'ISO_22000' ? 'selected' : '' }}>ISO 22000</option>
                                    <option value="HACCP" {{ old('certificate_type', $certificate->certificate_type) == 'HACCP' ? 'selected' : '' }}>HACCP</option>
                                    <option value="GMP" {{ old('certificate_type', $certificate->certificate_type) == 'GMP' ? 'selected' : '' }}>GMP</option>
                                    <option value="HALAL" {{ old('certificate_type', $certificate->certificate_type) == 'HALAL' ? 'selected' : '' }}>HALAL</option>
                                    <option value="FDA" {{ old('certificate_type', $certificate->certificate_type) == 'FDA' ? 'selected' : '' }}>FDA</option>
                                    <option value="CE" {{ old('certificate_type', $certificate->certificate_type) == 'CE' ? 'selected' : '' }}>CE</option>
                                    <option value="custom" {{ old('certificate_type', $certificate->certificate_type) == 'custom' ? 'selected' : '' }}>{{ __('quality::quality.other') }}</option>
                                </select>
                                @error('certificate_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3" id="custom_type_field" style="display: {{ old('certificate_type', $certificate->certificate_type) == 'custom' ? 'block' : 'none' }}">
                                <label for="custom_type" class="form-label">{{ __('quality::quality.certificate name') }} <span class="text-danger">*</span></label>
                                <input type="text" name="custom_type" id="custom_type"
                                    class="form-control @error('custom_type') is-invalid @enderror"
                                    value="{{ old('custom_type', $certificate->custom_type) }}">
                                @error('custom_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="issuing_authority" class="form-label">{{ __('quality::quality.issuing authority') }} <span class="text-danger">*</span></label>
                                <input type="text" name="issuing_authority" id="issuing_authority"
                                    class="form-control @error('issuing_authority') is-invalid @enderror"
                                    value="{{ old('issuing_authority', $certificate->issuing_authority) }}" required>
                                @error('issuing_authority')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('quality::quality.issue date') }}</label>
                                <input type="text" class="form-control"
                                    value="{{ $certificate->issue_date?->format('Y-m-d') ?? '---' }}" readonly>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="expiry_date" class="form-label">{{ __('quality::quality.valid until') }} <span class="text-danger">*</span></label>
                                <input type="date" name="expiry_date" id="expiry_date"
                                    class="form-control @error('expiry_date') is-invalid @enderror"
                                    value="{{ old('expiry_date', $certificate->expiry_date?->format('Y-m-d')) }}" required>
                                @error('expiry_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="status" class="form-label">{{ __('quality::quality.status') }} <span class="text-danger">*</span></label>
                                <select name="status" id="status"
                                    class="form-control @error('status') is-invalid @enderror" required>
                                    <option value="active" {{ old('status', $certificate->status) == 'active' ? 'selected' : '' }}>{{ __('quality::quality.active') }}</option>
                                    <option value="expired" {{ old('status', $certificate->status) == 'expired' ? 'selected' : '' }}>{{ __('quality::quality.expired certificate') }}</option>
                                    <option value="renewal_pending" {{ old('status', $certificate->status) == 'renewal_pending' ? 'selected' : '' }}>{{ __('quality::quality.renewal pending') }}</option>
                                    <option value="suspended" {{ old('status', $certificate->status) == 'suspended' ? 'selected' : '' }}>{{ __('quality::quality.suspended') }}</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="notification_days" class="form-label">{{ __('quality::quality.notification before expiry (days)') }} <span class="text-danger">*</span></label>
                                <input type="number" name="notification_days" id="notification_days"
                                    class="form-control @error('notification_days') is-invalid @enderror"
                                    value="{{ old('notification_days', $certificate->notification_days) }}" min="1" step="1" required>
                                @error('notification_days')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12 mb-3">
                                <label class="form-label">{{ __('quality::quality.scope') }}</label>
                                <textarea class="form-control" rows="3" readonly>{{ $certificate->scope }}</textarea>
                            </div>

                            <div class="col-md-12 mb-3">
                                <label for="notes" class="form-label">{{ __('quality::quality.notes') }}</label>
                                <textarea name="notes" id="notes" rows="3"
                                    class="form-control @error('notes') is-invalid @enderror">{{ old('notes', $certificate->notes) }}</textarea>
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-3">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>{{ __('quality::quality.save changes') }}
                            </button>
                            <a href="{{ route('quality.certificates.show', $certificate) }}" class="btn btn-secondary">
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
            const typeSelect = document.getElementById('certificate_type');
            const customField = document.getElementById('custom_type_field');

            typeSelect.addEventListener('change', function() {
                customField.style.display = this.value === 'custom' ? 'block' : 'none';
            });
        });
    </script>
@endsection
