@extends('admin.dashboard')

@section('sidebar')
    @include('components.sidebar.quality')
@endsection

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h2><i class="fas fa-plus-circle me-2"></i>{{ __('quality::quality.new quality inspection') }}</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('quality.dashboard') }}">{{ __('quality::quality.quality') }}</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('quality.inspections.index') }}">{{ __('quality::quality.inspections') }}</a></li>
                    <li class="breadcrumb-item active">{{ __('quality::quality.new') }}</li>
                </ol>
            </nav>
        </div>
    </div>

    <form action="{{ route('quality.inspections.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row">
            <div class="col-lg-8">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">{{ __('quality::quality.inspection details') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label required">{{ __("quality::quality.item") }}</label>
                                <select name="item_id" class="form-select @error('item_id') is-invalid @enderror" required>
                                    <option value="">{{ __('quality::quality.select item') }}</option>
                                    @foreach($items as $item)
                                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                                    @endforeach
                                </select>
                                @error('item_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label required">{{ __('quality::quality.inspection type') }}</label>
                                <select name="inspection_type" class="form-select @error('inspection_type') is-invalid @enderror" required>
                                    <option value="">{{ __('quality::quality.select type') }}</option>
                                    <option value="receiving">{{ __('quality::quality.receiving inspection') }}</option>
                                    <option value="in_process">{{ __('quality::quality.in-process inspection') }}</option>
                                    <option value="final">{{ __('quality::quality.final inspection') }}</option>
                                    <option value="random">{{ __('quality::quality.random inspection') }}</option>
                                    <option value="customer_complaint">{{ __('quality::quality.customer complaint inspection') }}</option>
                                </select>
                                @error('inspection_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('quality::quality.supplier') }}</label>
                                <select name="supplier_id" class="form-select">
                                    <option value="">{{ __('quality::quality.select supplier (optional)') }}</option>
                                    @foreach($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}">{{ $supplier->aname }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label required">{{ __('quality::quality.inspection date') }}</label>
                                <input type="date" name="inspection_date" 
                                       class="form-control @error('inspection_date') is-invalid @enderror" 
                                       value="{{ date('Y-m-d') }}" required>
                                @error('inspection_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label required">{{ __('quality::quality.inspected quantity') }}</label>
                                <input type="number" step="0.001" name="quantity_inspected" 
                                       class="form-control @error('quantity_inspected') is-invalid @enderror" required>
                                @error('quantity_inspected')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label required">{{ __('quality::quality.passed quantity') }}</label>
                                <input type="number" step="0.001" name="pass_quantity" 
                                       class="form-control @error('pass_quantity') is-invalid @enderror" required>
                                @error('pass_quantity')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label required">{{ __('quality::quality.failed quantity') }}</label>
                                <input type="number" step="0.001" name="fail_quantity" 
                                       class="form-control @error('fail_quantity') is-invalid @enderror" required>
                                @error('fail_quantity')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-12 mb-3">
                                <label class="form-label">{{ __('quality::quality.defects found') }}</label>
                                <textarea name="defects_found" rows="3" class="form-control" 
                                          placeholder="{{ __('quality::quality.list defects found...') }}"></textarea>
                            </div>

                            <div class="col-12 mb-3">
                                <label class="form-label">{{ __('quality::quality.inspector notes') }}</label>
                                <textarea name="inspector_notes" rows="3" class="form-control" 
                                          placeholder="{{ __('quality::quality.additional notes...') }}"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">{{ __('quality::quality.result') }} {{ __('quality::quality.and action') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label required">{{ __('quality::quality.inspection result') }}</label>
                            <select name="result" class="form-select @error('result') is-invalid @enderror" required>
                                <option value="pass">{{ __('quality::quality.pass') }}</option>
                                <option value="fail">{{ __('quality::quality.fail') }}</option>
                                <option value="conditional">{{ __('quality::quality.conditional') }}</option>
                            </select>
                            @error('result')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label required">{{ __('quality::quality.action taken') }}</label>
                            <select name="action_taken" class="form-select @error('action_taken') is-invalid @enderror" required>
                                <option value="accepted">{{ __('quality::quality.accepted') }}</option>
                                <option value="rejected">{{ __('quality::quality.rejected') }}</option>
                                <option value="rework">{{ __('quality::quality.rework') }}</option>
                                <option value="conditional_accept">{{ __('quality::quality.conditional accept') }}</option>
                                <option value="pending_review">{{ __('quality::quality.pending review') }}</option>
                            </select>
                            @error('action_taken')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">{{ __('quality::quality.batch number') }}</label>
                            <input type="text" name="batch_number" class="form-control" 
                                   placeholder="{{ __('quality::quality.optional') }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">{{ __('quality::quality.attachments') }}</label>
                            <input type="file" name="attachments[]" class="form-control" multiple>
                            <small class="text-muted">{{ __('quality::quality.photos, reports, certificates') }}</small>
                        </div>
                    </div>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-success btn-lg">
                        <i class="fas fa-save me-2"></i>{{ __('quality::quality.save inspection') }}
                    </button>
                    <a href="{{ route('quality.inspections.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times me-2"></i>{{ __("quality::quality.cancel") }}
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

