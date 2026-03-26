@extends('admin.dashboard')

@section('sidebar')
    @include('components.sidebar.quality')
@endsection

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h2><i class="fas fa-edit me-2"></i>{{ __("quality::quality.edit") }} الفحص: {{ $inspection->inspection_number }}</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('quality.dashboard') }}">{{ __("quality::quality.quality") }}</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('quality.inspections.index') }}">{{ __("quality::quality.inspections") }}</a></li>
                    <li class="breadcrumb-item active">{{ __("quality::quality.edit") }}</li>
                </ol>
            </nav>
        </div>
    </div>

    <form action="{{ url('/quality/inspections/' . $inspection->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <input type="hidden" name="inspection_id" value="{{ $inspection->id }}">
        <div class="row">
            <div class="col-lg-8">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">{{ __("quality::quality.inspection details") }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label required">{{ __("quality::quality.item") }}</label>
                                <select name="item_id" class="form-select @error('item_id') is-invalid @enderror" required>
                                    <option value="">{{ __("quality::quality.select item") }}</option>
                                    @foreach($items as $item)
                                        <option value="{{ $item->id }}" {{ old('item_id', $inspection->item_id) == $item->id ? 'selected' : '' }}>{{ $item->name }}</option>
                                    @endforeach
                                </select>
                                @error('item_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label required">{{ __("quality::quality.inspection type") }}</label>
                                <select name="inspection_type" class="form-select @error('inspection_type') is-invalid @enderror" required>
                                    <option value="">{{ __("quality::quality.select type") }}</option>
                                    <option value="receiving" {{ old('inspection_type', $inspection->inspection_type) == 'receiving' ? 'selected' : '' }}>{{ __("quality::quality.receiving inspection") }}</option>
                                    <option value="in_process" {{ old('inspection_type', $inspection->inspection_type) == 'in_process' ? 'selected' : '' }}>{{ __("quality::quality.in-process inspection") }}</option>
                                    <option value="final" {{ old('inspection_type', $inspection->inspection_type) == 'final' ? 'selected' : '' }}>{{ __("quality::quality.final inspection") }}</option>
                                    <option value="random" {{ old('inspection_type', $inspection->inspection_type) == 'random' ? 'selected' : '' }}>{{ __("quality::quality.random inspection") }}</option>
                                    <option value="customer_complaint" {{ old('inspection_type', $inspection->inspection_type) == 'customer_complaint' ? 'selected' : '' }}>{{ __("quality::quality.customer complaint inspection") }}</option>
                                </select>
                                @error('inspection_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __("quality::quality.supplier") }}</label>
                                <select name="supplier_id" class="form-select">
                                    <option value="">{{ __("quality::quality.select supplier (optional)") }}</option>
                                    @foreach($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}" {{ old('supplier_id', $inspection->supplier_id) == $supplier->id ? 'selected' : '' }}>{{ $supplier->aname }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label required">{{ __("quality::quality.inspection date") }}</label>
                                <input type="date" name="inspection_date" 
                                       class="form-control @error('inspection_date') is-invalid @enderror" 
                                       value="{{ old('inspection_date', $inspection->inspection_date?->format('Y-m-d')) }}" required>
                                @error('inspection_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label required">{{ __("quality::quality.inspected quantity") }}</label>
                                <input type="number" step="0.001" name="quantity_inspected" 
                                       class="form-control @error('quantity_inspected') is-invalid @enderror" 
                                       value="{{ old('quantity_inspected', $inspection->quantity_inspected) }}" required>
                                @error('quantity_inspected')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label required">{{ __("quality::quality.passed quantity") }}</label>
                                <input type="number" step="0.001" name="pass_quantity" 
                                       class="form-control @error('pass_quantity') is-invalid @enderror" 
                                       value="{{ old('pass_quantity', $inspection->pass_quantity) }}" required>
                                @error('pass_quantity')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label required">{{ __("quality::quality.failed quantity") }}</label>
                                <input type="number" step="0.001" name="fail_quantity" 
                                       class="form-control @error('fail_quantity') is-invalid @enderror" 
                                       value="{{ old('fail_quantity', $inspection->fail_quantity) }}" required>
                                @error('fail_quantity')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-12 mb-3">
                                <label class="form-label">{{ __("quality::quality.defects found") }}</label>
                                <textarea name="defects_found" rows="3" class="form-control" 
                                          placeholder="{{ __("quality::quality.list defects found...") }}">{{ old('defects_found', $inspection->defects_found) }}</textarea>
                            </div>

                            <div class="col-12 mb-3">
                                <label class="form-label">{{ __("quality::quality.inspector notes") }}</label>
                                <textarea name="inspector_notes" rows="3" class="form-control" 
                                          placeholder="{{ __("quality::quality.additional notes...") }}">{{ old('inspector_notes', $inspection->inspector_notes) }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">{{ __("quality::quality.result") }} {{ __("quality::quality.and action") }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label required">{{ __("quality::quality.inspection result") }}</label>
                            <select name="result" class="form-select @error('result') is-invalid @enderror" required>
                                <option value="pass" {{ old('result', $inspection->result) == 'pass' ? 'selected' : '' }}>{{ __("quality::quality.pass") }}</option>
                                <option value="fail" {{ old('result', $inspection->result) == 'fail' ? 'selected' : '' }}>{{ __("quality::quality.fail") }}</option>
                                <option value="conditional" {{ old('result', $inspection->result) == 'conditional' ? 'selected' : '' }}>{{ __("quality::quality.conditional") }}</option>
                            </select>
                            @error('result')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label required">{{ __("quality::quality.action taken") }}</label>
                            <select name="action_taken" class="form-select @error('action_taken') is-invalid @enderror" required>
                                <option value="accepted" {{ old('action_taken', $inspection->action_taken) == 'accepted' ? 'selected' : '' }}>{{ __("quality::quality.accepted") }}</option>
                                <option value="rejected" {{ old('action_taken', $inspection->action_taken) == 'rejected' ? 'selected' : '' }}>{{ __("quality::quality.rejected") }}</option>
                                <option value="rework" {{ old('action_taken', $inspection->action_taken) == 'rework' ? 'selected' : '' }}>{{ __("quality::quality.rework") }}</option>
                                <option value="conditional_accept" {{ old('action_taken', $inspection->action_taken) == 'conditional_accept' ? 'selected' : '' }}>{{ __("quality::quality.conditional accept") }}</option>
                                <option value="pending_review" {{ old('action_taken', $inspection->action_taken) == 'pending_review' ? 'selected' : '' }}>{{ __("quality::quality.pending review") }}</option>
                            </select>
                            @error('action_taken')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">{{ __("quality::quality.batch number") }}</label>
                            <input type="text" name="batch_number" class="form-control" 
                                   value="{{ old('batch_number', $inspection->batch_number) }}" placeholder="{{ __("quality::quality.optional") }}">
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
                        <i class="fas fa-save me-2"></i>{{ __("quality::quality.save inspection") }}
                    </button>
                    <a href="{{ route('quality.inspections.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times me-2"></i>{{ __("quality::quality.cancel") }}
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    console.log('Form action:', form.action);
    console.log('Form method:', form.method);
    console.log('Method input value:', document.querySelector('input[name="_method"]').value);
    console.log('Inspection ID:', '{{ $inspection->id }}');
});
</script>
@endsection