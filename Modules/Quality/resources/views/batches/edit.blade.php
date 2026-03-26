@extends('admin.dashboard')

@section('sidebar')
    @include('components.sidebar.quality')
@endsection

@section('content')
    @include('components.breadcrumb', [
        'title' => __('quality::quality.edit batch'),
        'breadcrumb_items' => [
            ['label' => __('quality::quality.quality'), 'url' => route('quality.dashboard')],
            ['label' => __('quality::quality.batch tracking'), 'url' => route('quality.batches.index')],
            ['label' => __('quality::quality.edit')],
        ],
    ])

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h5>{{ __('quality::quality.batch details') }}</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('quality.batches.update', $batch) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('quality::quality.batch number') }}</label>
                                <input type="text" class="form-control" value="{{ $batch->batch_number }}" readonly>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('quality::quality.item') }}</label>
                                <input type="text" class="form-control" value="{{ $batch->item->name ?? '---' }}" readonly>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="production_date" class="form-label">{{ __('quality::quality.production date') }} <span class="text-danger">*</span></label>
                                <input type="date" name="production_date" id="production_date"
                                    class="form-control @error('production_date') is-invalid @enderror"
                                    value="{{ old('production_date', $batch->production_date?->format('Y-m-d')) }}" required>
                                @error('production_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="expiry_date" class="form-label">{{ __('quality::quality.expiry date') }}</label>
                                <input type="date" name="expiry_date" id="expiry_date"
                                    class="form-control @error('expiry_date') is-invalid @enderror"
                                    value="{{ old('expiry_date', $batch->expiry_date?->format('Y-m-d')) }}">
                                @error('expiry_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="remaining_quantity" class="form-label">{{ __('quality::quality.remaining quantity') }} <span class="text-danger">*</span></label>
                                <input type="number" step="0.001" name="remaining_quantity" id="remaining_quantity"
                                    class="form-control @error('remaining_quantity') is-invalid @enderror"
                                    value="{{ old('remaining_quantity', $batch->remaining_quantity) }}" min="0" required>
                                @error('remaining_quantity')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="quality_status" class="form-label">{{ __('quality::quality.quality status') }} <span class="text-danger">*</span></label>
                                <select name="quality_status" id="quality_status"
                                    class="form-control @error('quality_status') is-invalid @enderror" required>
                                    <option value="">-- {{ __('quality::quality.select status') }} --</option>
                                    <option value="passed" {{ old('quality_status', $batch->quality_status) == 'passed' ? 'selected' : '' }}>{{ __('quality::quality.pass') }}</option>
                                    <option value="failed" {{ old('quality_status', $batch->quality_status) == 'failed' ? 'selected' : '' }}>{{ __('quality::quality.fail') }}</option>
                                    <option value="conditional" {{ old('quality_status', $batch->quality_status) == 'conditional' ? 'selected' : '' }}>{{ __('quality::quality.conditional') }}</option>
                                    <option value="quarantine" {{ old('quality_status', $batch->quality_status) == 'quarantine' ? 'selected' : '' }}>{{ __('quality::quality.quarantine') }}</option>
                                </select>
                                @error('quality_status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="status" class="form-label">{{ __('quality::quality.status') }} <span class="text-danger">*</span></label>
                                <select name="status" id="status"
                                    class="form-control @error('status') is-invalid @enderror" required>
                                    <option value="active" {{ old('status', $batch->status) == 'active' ? 'selected' : '' }}>{{ __('quality::quality.active') }}</option>
                                    <option value="consumed" {{ old('status', $batch->status) == 'consumed' ? 'selected' : '' }}>{{ __('quality::quality.consumed') }}</option>
                                    <option value="expired" {{ old('status', $batch->status) == 'expired' ? 'selected' : '' }}>{{ __('quality::quality.expired certificate') }}</option>
                                    <option value="rejected" {{ old('status', $batch->status) == 'rejected' ? 'selected' : '' }}>{{ __('quality::quality.rejected') }}</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="supplier_id" class="form-label">{{ __('quality::quality.supplier') }}</label>
                                <select name="supplier_id" id="supplier_id"
                                    class="form-control @error('supplier_id') is-invalid @enderror">
                                    <option value="">-- {{ __('quality::quality.select supplier') }} --</option>
                                    @foreach($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}" {{ old('supplier_id', $batch->supplier_id) == $supplier->id ? 'selected' : '' }}>
                                            {{ $supplier->aname }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('supplier_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="warehouse_id" class="form-label">{{ __('quality::quality.warehouse') }}</label>
                                <select name="warehouse_id" id="warehouse_id"
                                    class="form-control @error('warehouse_id') is-invalid @enderror">
                                    <option value="">-- {{ __('quality::quality.select warehouse') }} --</option>
                                    @foreach($warehouses as $warehouse)
                                        <option value="{{ $warehouse->id }}" {{ old('warehouse_id', $batch->warehouse_id) == $warehouse->id ? 'selected' : '' }}>
                                            {{ $warehouse->aname }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('warehouse_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12 mb-3">
                                <label for="location" class="form-label">{{ __('quality::quality.storage location') }}</label>
                                <input type="text" name="location" id="location"
                                    class="form-control @error('location') is-invalid @enderror"
                                    value="{{ old('location', $batch->location) }}"
                                    placeholder="{{ __('quality::quality.example: shelf a - level 2 - position 5') }}">
                                @error('location')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12 mb-3">
                                <label for="notes" class="form-label">{{ __('quality::quality.notes') }}</label>
                                <textarea name="notes" id="notes" rows="3"
                                    class="form-control @error('notes') is-invalid @enderror">{{ old('notes', $batch->notes) }}</textarea>
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-3">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>{{ __('quality::quality.save changes') }}
                            </button>
                            <a href="{{ route('quality.batches.show', $batch) }}" class="btn btn-secondary">
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
            const productionDate = document.getElementById('production_date');
            const expiryDate = document.getElementById('expiry_date');

            productionDate.addEventListener('change', function() {
                expiryDate.min = this.value;
                if (expiryDate.value && expiryDate.value < this.value) {
                    expiryDate.value = '';
                }
            });

            if (productionDate.value) {
                expiryDate.min = productionDate.value;
            }
        });
    </script>
@endsection
