@extends('admin.dashboard')

@section('sidebar')
    @include('components.sidebar.quality')
@endsection

@section('content')
    @include('components.breadcrumb', [
        'title' => __('quality::quality.add new batch'),
        'breadcrumb_items' => [
            ['label' => __('quality::quality.quality'), 'url' => route('quality.dashboard')],
            ['label' => __('quality::quality.batch tracking'), 'url' => route('quality.batches.index')],
            ['label' => __('quality::quality.new')],
        ],
    ])

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h5>{{ __('quality::quality.batch details') }}</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('quality.batches.store') }}" method="POST">
                        @csrf

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="batch_number" class="form-label">{{ __('quality::quality.batch number') }} <span class="text-danger">*</span></label>
                                <input type="text" name="batch_number" id="batch_number"
                                    class="form-control @error('batch_number') is-invalid @enderror"
                                    value="{{ old('batch_number') }}" required>
                                @error('batch_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="item_id" class="form-label">{{ __('quality::quality.item') }} <span class="text-danger">*</span></label>
                                <select name="item_id" id="item_id"
                                    class="form-control @error('item_id') is-invalid @enderror" required>
                                    <option value="">-- {{ __('quality::quality.select item') }} --</option>
                                    @foreach($items as $item)
                                        <option value="{{ $item->id }}" {{ old('item_id') == $item->id ? 'selected' : '' }}>
                                            {{ $item->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('item_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="production_date" class="form-label">{{ __('quality::quality.production date') }} <span class="text-danger">*</span></label>
                                <input type="date" name="production_date" id="production_date"
                                    class="form-control @error('production_date') is-invalid @enderror"
                                    value="{{ old('production_date') }}" required>
                                @error('production_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="expiry_date" class="form-label">{{ __('quality::quality.expiry date') }}</label>
                                <input type="date" name="expiry_date" id="expiry_date"
                                    class="form-control @error('expiry_date') is-invalid @enderror"
                                    value="{{ old('expiry_date') }}">
                                @error('expiry_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="quantity" class="form-label">{{ __('quality::quality.quantity') }} <span class="text-danger">*</span></label>
                                <input type="number" step="0.001" name="quantity" id="quantity"
                                    class="form-control @error('quantity') is-invalid @enderror"
                                    value="{{ old('quantity') }}" min="0" required>
                                @error('quantity')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="quality_status" class="form-label">{{ __('quality::quality.quality status') }} <span class="text-danger">*</span></label>
                                <select name="quality_status" id="quality_status"
                                    class="form-control @error('quality_status') is-invalid @enderror" required>
                                    <option value="">-- {{ __('quality::quality.select status') }} --</option>
                                    <option value="passed" {{ old('quality_status') == 'passed' ? 'selected' : '' }}>{{ __('quality::quality.pass') }}</option>
                                    <option value="failed" {{ old('quality_status') == 'failed' ? 'selected' : '' }}>{{ __('quality::quality.fail') }}</option>
                                    <option value="conditional" {{ old('quality_status') == 'conditional' ? 'selected' : '' }}>{{ __('quality::quality.conditional') }}</option>
                                    <option value="quarantine" {{ old('quality_status') == 'quarantine' ? 'selected' : '' }}>{{ __('quality::quality.quarantine') }}</option>
                                </select>
                                @error('quality_status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="supplier_id" class="form-label">{{ __('quality::quality.supplier') }}</label>
                                <select name="supplier_id" id="supplier_id"
                                    class="form-control @error('supplier_id') is-invalid @enderror">
                                    <option value="">-- {{ __('quality::quality.select supplier') }} --</option>
                                    @foreach($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
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
                                        <option value="{{ $warehouse->id }}" {{ old('warehouse_id') == $warehouse->id ? 'selected' : '' }}>
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
                                    value="{{ old('location') }}"
                                    placeholder="{{ __('quality::quality.example: shelf a - level 2 - position 5') }}">
                                @error('location')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12 mb-3">
                                <label for="notes" class="form-label">{{ __('quality::quality.notes') }}</label>
                                <textarea name="notes" id="notes" rows="3"
                                    class="form-control @error('notes') is-invalid @enderror">{{ old('notes') }}</textarea>
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-3">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>{{ __('quality::quality.save batch') }}
                            </button>
                            <a href="{{ route('quality.batches.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i>{{ __("quality::quality.cancel") }}
                            </a>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save me-2"></i>{{ __("quality::quality.save batch") }}
                            </button>
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
        });
    </script>
@endsection
