@extends('admin.dashboard')

@section('sidebar')
    @include('components.sidebar.quality')
@endsection

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h2><i class="fas fa-edit me-2"></i>{{ __("quality::quality.edit report") }}: {{ $ncr->ncr_number }}</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('quality.dashboard') }}">{{ __("quality::quality.quality") }}</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('quality.ncr.index') }}">{{ __("quality::quality.ncr") }}</a></li>
                    <li class="breadcrumb-item active">{{ __("quality::quality.edit") }}</li>
                </ol>
            </nav>
        </div>
    </div>
 
    <form action="{{ url('/quality/ncr/' . $ncr->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="row">
            <div class="col-lg-8">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">{{ __("quality::quality.non-conformance information") }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label required">{{ __("quality::quality.item") }}</label>
                                <select name="item_id" class="form-select @error('item_id') is-invalid @enderror" required>
                                    <option value="">{{ __("quality::quality.select item") }}</option>
                                    @foreach($items as $item)
                                        <option value="{{ $item->id }}" {{ old('item_id', $ncr->item_id) == $item->id ? 'selected' : '' }}>{{ $item->name }}</option>
                                    @endforeach
                                </select>
                                @error('item_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __("quality::quality.batch number") }}</label>
                                <input type="text" name="batch_number" class="form-control" 
                                       value="{{ old('batch_number', $ncr->batch_number) }}" placeholder="{{ __("quality::quality.optional") }}">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label required">{{ __("quality::quality.detection date") }}</label>
                                <input type="date" name="detected_date" 
                                       class="form-control @error('detected_date') is-invalid @enderror" 
                                       value="{{ old('detected_date', $ncr->detected_date?->format('Y-m-d')) }}" required>
                                @error('detected_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label required">{{ __("quality::quality.affected quantity") }}</label>
                                <input type="number" step="0.001" name="affected_quantity" 
                                       class="form-control @error('affected_quantity') is-invalid @enderror" 
                                       value="{{ old('affected_quantity', $ncr->affected_quantity) }}" required>
                                @error('affected_quantity')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label required">{{ __("quality::quality.source") }}</label>
                                <select name="source" class="form-select @error('source') is-invalid @enderror" required>
                                    <option value="">{{ __("quality::quality.select source") }}</option>
                                    <option value="receiving_inspection" {{ old('source', $ncr->source) == 'receiving_inspection' ? 'selected' : '' }}>{{ __("quality::quality.receiving inspection") }}</option>
                                    <option value="in_process" {{ old('source', $ncr->source) == 'in_process' ? 'selected' : '' }}>{{ __("quality::quality.in-process inspection") }}</option>
                                    <option value="final_inspection" {{ old('source', $ncr->source) == 'final_inspection' ? 'selected' : '' }}>{{ __("quality::quality.final inspection") }}</option>
                                    <option value="customer_complaint" {{ old('source', $ncr->source) == 'customer_complaint' ? 'selected' : '' }}>{{ __("quality::quality.customer complaint inspection") }}</option>
                                    <option value="internal_audit" {{ old('source', $ncr->source) == 'internal_audit' ? 'selected' : '' }}>{{ __("quality::quality.internal") }}</option>
                                    <option value="supplier_notification" {{ old('source', $ncr->source) == 'supplier_notification' ? 'selected' : '' }}>{{ __("quality::quality.supplier notification") }}</option>
                                </select>
                                @error('source')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-12 mb-3">
                                <label class="form-label required">{{ __("quality::quality.problem description") }}</label>
                                <textarea name="problem_description" rows="4" 
                                          class="form-control @error('problem_description') is-invalid @enderror" 
                                          placeholder="{{ __("quality::quality.explain the problem in detail...") }}" required>{{ old('problem_description', $ncr->problem_description) }}</textarea>
                                @error('problem_description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-12 mb-3">
                                <label class="form-label">{{ __("quality::quality.immediate action") }}</label>
                                <textarea name="immediate_action" rows="3" class="form-control" 
                                          placeholder="{{ __("quality::quality.what immediate action was taken?") }}">{{ old('immediate_action', $ncr->immediate_action) }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">{{ __("quality::quality.classification and priority") }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label required">{{ __("quality::quality.severity") }}</label>
                            <select name="severity" class="form-select @error('severity') is-invalid @enderror" required>
                                <option value="minor" {{ old('severity', $ncr->severity) == 'minor' ? 'selected' : '' }}>{{ __("quality::quality.minor") }}</option>
                                <option value="major" {{ old('severity', $ncr->severity) == 'major' ? 'selected' : '' }}>{{ __("quality::quality.major") }}</option>
                                <option value="critical" {{ old('severity', $ncr->severity) == 'critical' ? 'selected' : '' }}>{{ __("quality::quality.critical") }}</option>
                            </select>
                            @error('severity')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">{{ __("quality::quality.estimated cost") }}</label>
                            <input type="number" required step="0.01" name="estimated_cost" 
                                   class="form-control" value="{{ old('estimated_cost', $ncr->estimated_cost) }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">{{ __("quality::quality.disposition") }}</label>
                            <select name="disposition" class="form-select">
                                <option value="">{{ __("quality::quality.to be determined") }}</option>
                                <option value="rework" {{ old('disposition', $ncr->disposition) == 'rework' ? 'selected' : '' }}>{{ __("quality::quality.rework") }}</option>
                                <option value="scrap" {{ old('disposition', $ncr->disposition) == 'scrap' ? 'selected' : '' }}>{{ __("quality::quality.scrap") }}</option>
                                <option value="return_to_supplier" {{ old('disposition', $ncr->disposition) == 'return_to_supplier' ? 'selected' : '' }}>{{ __("quality::quality.return to supplier") }}</option>
                                <option value="use_as_is" {{ old('disposition', $ncr->disposition) == 'use_as_is' ? 'selected' : '' }}>{{ __("quality::quality.use as is") }}</option>
                                <option value="repair" {{ old('disposition', $ncr->disposition) == 'repair' ? 'selected' : '' }}>{{ __("quality::quality.repair") }}</option>
                                <option value="downgrade" {{ old('disposition', $ncr->disposition) == 'downgrade' ? 'selected' : '' }}>{{ __("quality::quality.downgrade") }}</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">{{ __("quality::quality.assigned to") }}</label>
                            <select name="assigned_to" class="form-select">
                                <option value="">{{ __("quality::quality.select responsible") }}</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ old('assigned_to', $ncr->assigned_to) == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">{{ __("quality::quality.target closure date") }}</label>
                            <input type="date" name="target_closure_date" 
                                   class="form-control" value="{{ old('target_closure_date', $ncr->target_closure_date?->format('Y-m-d')) }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">{{ __("quality::quality.attachments") }}</label>
                            <input type="file" name="attachments[]" class="form-control" multiple>
                            <small class="text-muted">{{ __("quality::quality.photos, reports, certificates") }}</small>
                        </div>
                    </div>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-warning btn-lg">
                        <i class="fas fa-save me-2"></i>{{ __("quality::quality.update report") }}
                    </button>
                    <a href="{{ route('quality.ncr.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times me-2"></i>{{ __("quality::quality.cancel") }}
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection